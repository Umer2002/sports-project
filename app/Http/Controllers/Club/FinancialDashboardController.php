<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Invite;
use App\Models\PayoutPlan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FinancialDashboardController extends Controller
{
    private const PLATFORM_FEE_PERCENTAGE = 5.0;

    public function index(Request $request)
    {
        $club = $this->resolveClub($request);

        $payoutPlans = PayoutPlan::orderBy('player_count')->get();

        $registeredPlayerCount = $club->players()->count();
        $acceptedInviteCount = Invite::query()
            ->where('type', 'club_invite')
            ->where('reference_id', $club->id)
            ->whereNotNull('accepted_at')
            ->count();

        $playerCount = max($registeredPlayerCount, $acceptedInviteCount);
        $defaultCalculation = $this->calculatePayoutForPlayers($playerCount, $payoutPlans);

        $latestClubPayout = $club->payments()
            ->where('type', 'club_payout')
            ->latest('created_at')
            ->first();

        $latestClubPayoutAmount = $latestClubPayout?->amount ? $this->convertFromCents((float) $latestClubPayout->amount) : 0.0;

        $totalClubPayouts = $this->convertFromCents(
            (float) $club->payments()
            ->where('type', 'club_payout')
            ->sum('amount')
        );

        $playerPaymentsTotal = $this->convertFromCents(
            (float) $club->payments()
            ->where('type', 'player')
            ->sum('amount')
        );

        $currencyOptions = $club->payments()
            ->whereNotNull('currency')
            ->distinct()
            ->pluck('currency')
            ->filter()
            ->values();

        if ($currencyOptions->isEmpty()) {
            $currencyOptions = collect(['USD', 'CAD', 'EUR']);
        }

        $payoutCountdown = $this->buildPayoutCountdown($club);
        $sponsorshipCountdown = $this->buildSponsorshipCountdown($club);

        $donationTotal = $club->total_donations ?? 0;
        $donationCount = $club->donations_count ?? 0;

        $recentDonations = $club->donations()
            ->where('status', 'completed')
            ->latest()
            ->limit(5)
            ->get();

        $monthlyRevenue = $this->buildMonthlyRevenue($club);

        return view('club.financial-dashboard', [
            'club' => $club,
            'playerCount' => $playerCount,
            'registeredPlayerCount' => $registeredPlayerCount,
            'acceptedInviteCount' => $acceptedInviteCount,
            'payoutPlans' => $payoutPlans,
            'defaultCalculation' => $defaultCalculation,
            'currencyOptions' => $currencyOptions,
            'platformFeePercent' => self::PLATFORM_FEE_PERCENTAGE,
            'latestClubPayout' => $latestClubPayout,
            'latestClubPayoutAmount' => $latestClubPayoutAmount,
            'totalClubPayouts' => $totalClubPayouts,
            'playerPaymentsTotal' => $playerPaymentsTotal,
            'payoutCountdown' => $payoutCountdown,
            'sponsorshipCountdown' => $sponsorshipCountdown,
            'donationTotal' => $donationTotal,
            'donationCount' => $donationCount,
            'recentDonations' => $recentDonations,
            'monthlyRevenue' => $monthlyRevenue,
        ]);
    }

    public function calculate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'player_count' => ['required', 'integer', 'min:0'],
        ]);

        $club = $this->resolveClub($request);
        $payoutPlans = PayoutPlan::orderBy('player_count')->get();
        $calculation = $this->calculatePayoutForPlayers((int) $data['player_count'], $payoutPlans);

        return response()->json([
            'data' => array_merge($calculation, [
                'currency' => $request->input('currency', 'USD'),
            ]),
        ]);
    }

    public function export(Request $request)
    {
        $data = $request->validate([
            'player_count' => ['required', 'integer', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
        ]);

        $club = $this->resolveClub($request);
        $payoutPlans = PayoutPlan::orderBy('player_count')->get();
        $calculation = $this->calculatePayoutForPlayers((int) $data['player_count'], $payoutPlans);

        $rows = [
            ['Club', $club->name],
            ['Players Entered', $calculation['player_count']],
            ['Plan Base Players', $calculation['plan_player_count']],
            ['Per Player Rate', $this->formatCurrency($calculation['per_player_rate'])],
            ['Gross Payout', $this->formatCurrency($calculation['gross_payout'])],
            ['Platform Fee (' . self::PLATFORM_FEE_PERCENTAGE . '%)', $this->formatCurrency($calculation['platform_fee_amount'])],
            ['Net Payout', $this->formatCurrency($calculation['net_payout'])],
            ['Currency', strtoupper($data['currency'])],
            ['Payout Status', $club->getPayoutStatusDescription()],
            ['Generated At', now()->toDateTimeString()],
        ];

        $callback = static function () use ($rows): void {
            $handle = fopen('php://output', 'wb');
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        $filename = 'club-financial-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function resolveClub(Request $request): Club
    {
        $user = $request->user();

        abort_unless($user && $user->hasRole('club'), 403, 'Club access required.');

        $club = $user->club ?: Club::where('user_id', $user->id)->first();

        abort_if(! $club, 404, 'Club profile not found.');

        return $club;
    }

    /**
     * @param  Collection<int, PayoutPlan>  $payoutPlans
     */
    private function calculatePayoutForPlayers(int $playerCount, Collection $payoutPlans): array
    {
        $selectedPlan = $payoutPlans
            ->sortBy('player_count')
            ->firstWhere('player_count', '>=', $playerCount);

        if (! $selectedPlan) {
            $selectedPlan = $payoutPlans->sortByDesc('player_count')->first();
        }

        if (! $selectedPlan) {
            return [
                'player_count' => $playerCount,
                'plan_player_count' => 0,
                'per_player_rate' => 0,
                'gross_payout' => 0,
                'platform_fee_percent' => self::PLATFORM_FEE_PERCENTAGE,
                'platform_fee_amount' => 0,
                'net_payout' => 0,
                'is_projected' => true,
            ];
        }

        $rate = $selectedPlan->player_count > 0
            ? round($selectedPlan->payout_amount / $selectedPlan->player_count, 2)
            : 0;

        $gross = round($rate * $playerCount, 2);
        $platformFeeAmount = round($gross * (self::PLATFORM_FEE_PERCENTAGE / 100), 2);
        $net = round($gross - $platformFeeAmount, 2);

        return [
            'player_count' => $playerCount,
            'plan_player_count' => $selectedPlan->player_count,
            'per_player_rate' => $rate,
            'gross_payout' => $gross,
            'platform_fee_percent' => self::PLATFORM_FEE_PERCENTAGE,
            'platform_fee_amount' => $platformFeeAmount,
            'net_payout' => $net,
            'is_projected' => $selectedPlan->player_count !== $playerCount,
        ];
    }

    private function buildPayoutCountdown(Club $club): array
    {
        $registrationDate = $club->getRegistrationDate() ?? now();
        $onboardingEnd = $registrationDate->copy()->addDays(14);
        $payoutEnd = $onboardingEnd->copy()->addDays(90);

        if ($club->payout_status === 'paid' && $club->payout_paid_at) {
            $label = 'Last Payout Paid';
            $target = $club->payout_paid_at;
        } elseif ($club->isInOnboardingPeriod()) {
            $label = 'Onboarding Ends';
            $target = $onboardingEnd;
        } elseif ($club->isInPayoutPeriod()) {
            $label = 'Payout Period Ends';
            $target = $payoutEnd;
        } elseif ($club->payout_status === 'calculated') {
            $label = 'Finalize Payout';
            $target = $club->payout_calculated_at ?? $payoutEnd;
        } else {
            $label = 'Next Cycle Starts';
            $target = $payoutEnd;
        }

        return $this->formatCountdown($target, $label);
    }

    private function buildSponsorshipCountdown(Club $club): array
    {
        $latestDonation = $club->donations()->where('status', 'completed')->latest('created_at')->first();
        $base = $latestDonation?->created_at ?? $club->getRegistrationDate() ?? now();
        $target = $base->copy()->addDays(14);

        return $this->formatCountdown($target, 'Sponsorship Cycle Ends', [
            'latestDonationAmount' => $latestDonation?->amount,
        ]);
    }

    private function formatCountdown(Carbon $target, string $label, array $extra = []): array
    {
        $now = now();
        if ($target->lessThanOrEqualTo($now)) {
            return array_merge([
                'label' => $label,
                'target_iso' => $target->toIso8601String(),
                'weeks' => 0,
                'days' => 0,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
                'is_complete' => true,
            ], $extra);
        }

        $diff = $now->diff($target);
        $daysTotal = (int) $diff->days;
        $weeks = intdiv($daysTotal, 7);
        $days = $daysTotal % 7;

        return array_merge([
            'label' => $label,
            'target_iso' => $target->toIso8601String(),
            'weeks' => $weeks,
            'days' => $days,
            'hours' => (int) $diff->h,
            'minutes' => (int) $diff->i,
            'seconds' => (int) $diff->s,
            'is_complete' => false,
        ], $extra);
    }

    private function buildMonthlyRevenue(Club $club): array
    {
        $start = now()->subMonthsNoOverflow(5)->startOfMonth();

        $playerPayments = $club->payments()
            ->where('type', 'player')
            ->where('created_at', '>=', $start)
            ->get()
            ->groupBy(function ($payment) {
                return Carbon::parse($payment->created_at)->format('Y-m');
            })
            ->map(fn ($group) => $group->sum('amount'))
            ->all();

        $clubPayouts = $club->payments()
            ->where('type', 'club_payout')
            ->where('created_at', '>=', $start)
            ->get()
            ->groupBy(function ($payment) {
                return Carbon::parse($payment->created_at)->format('Y-m');
            })
            ->map(fn ($group) => $group->sum('amount'))
            ->all();

        $labels = collect(range(0, 5))
            ->map(fn ($offset) => now()->subMonthsNoOverflow(5 - $offset)->format('Y-m'))
            ->values();

        return [
            'labels' => $labels->all(),
            'playerPayments' => $labels
                ->map(fn ($label) => $this->convertFromCents((float) ($playerPayments[$label] ?? 0)))
                ->all(),
            'clubPayouts' => $labels
                ->map(fn ($label) => $this->convertFromCents((float) ($clubPayouts[$label] ?? 0)))
                ->all(),
        ];
    }

    private function convertFromCents(float $value): float
    {
        return round($value / 100, 2);
    }

    private function formatCurrency(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}
