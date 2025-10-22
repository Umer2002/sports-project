<?php
namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;

class PayoutController extends Controller
{
    const DAYS_TO_PAYOUT = 90;

    public function index(Request $request)
    {
        $cutoff = Carbon::now()->subDays(self::DAYS_TO_PAYOUT);

        // Badges
        $totals = DB::table('invites as i')
            ->leftJoin('users as ru', 'ru.id', '=', 'i.receiver_id')
            ->leftJoin('players as rp', 'rp.user_id', '=', 'ru.id')
            ->leftJoin('clubs as rc', 'rc.user_id', '=', 'ru.id')
            ->whereNotNull('i.accepted_at')
            ->selectRaw('
                COUNT(*) as total_invites,
                SUM(CASE WHEN rc.id IS NOT NULL THEN 1000 ELSE 0 END) as total_club_payout,
                SUM(CASE WHEN rp.id IS NOT NULL THEN 10 ELSE 0 END) as total_player_payout
            ')
            ->first();

        // Players — Ready (inviter is a Player)
        $playersReady = DB::table('invites as i')
            ->join('players as ip', 'ip.user_id', '=', 'i.sender_id')
            ->leftJoin('users as ru', 'ru.id', '=', 'i.receiver_id')
            ->leftJoin('players as rp', 'rp.user_id', '=', 'ru.id')
            ->leftJoin('clubs as rc', 'rc.user_id', '=', 'ru.id')
            ->whereNotNull('i.accepted_at')
            ->where('i.accepted_at', '<=', $cutoff)
            ->where('i.payout_processed', false)
            ->groupBy('ip.id', 'ip.name', 'ip.paypal_link')
            ->selectRaw('
                ip.id,
                ip.name,
                ip.paypal_link,
                SUM(CASE WHEN rc.id IS NOT NULL THEN 1 ELSE 0 END) AS club_referrals,
                SUM(CASE WHEN rp.id IS NOT NULL THEN 1 ELSE 0 END) AS player_referrals,
                SUM(CASE WHEN rc.id IS NOT NULL THEN 1000 ELSE 0 END)
                  + SUM(CASE WHEN rp.id IS NOT NULL THEN 10 ELSE 0 END) AS total_amount
            ')
            ->havingRaw('(club_referrals + player_referrals) > 0')
            ->orderByDesc('total_amount')
            ->get();

        // Players — Upcoming (<90 days)
        $playersUpcoming = DB::table('invites as i')
            ->join('players as ip', 'ip.user_id', '=', 'i.sender_id')
            ->leftJoin('users as ru', 'ru.id', '=', 'i.receiver_id')
            ->leftJoin('players as rp', 'rp.user_id', '=', 'ru.id')
            ->leftJoin('clubs as rc', 'rc.user_id', '=', 'ru.id')
            ->whereNotNull('i.accepted_at')
            ->where('i.accepted_at', '>', $cutoff)
            ->where('i.payout_processed', false)
            ->groupBy('ip.id', 'ip.name', 'ip.paypal_link')
            ->selectRaw('
                ip.id,
                ip.name,
                ip.paypal_link,
                SUM(CASE WHEN rc.id IS NOT NULL THEN 1 ELSE 0 END) AS club_referrals,
                SUM(CASE WHEN rp.id IS NOT NULL THEN 1 ELSE 0 END) AS player_referrals,
                MIN(GREATEST(0, ? - DATEDIFF(NOW(), i.accepted_at))) AS min_days_left
            ', [self::DAYS_TO_PAYOUT])
            ->havingRaw('(club_referrals + player_referrals) > 0')
            ->orderBy('min_days_left')
            ->get();

        // Clubs — Ready (inviter is a Club)
        $clubsReady = DB::table('invites as i')
            ->join('clubs as ic', 'ic.user_id', '=', 'i.sender_id')
            ->leftJoin('users as ru', 'ru.id', '=', 'i.receiver_id')
            ->leftJoin('players as rp', 'rp.user_id', '=', 'ru.id')
            ->leftJoin('clubs as rc', 'rc.user_id', '=', 'ru.id')
            ->whereNotNull('i.accepted_at')
            ->where('i.accepted_at', '<=', $cutoff)
            ->where('i.payout_processed', false)
            ->groupBy('ic.id', 'ic.name', 'ic.paypal_link')
            ->selectRaw('
                ic.id,
                ic.name,
                ic.paypal_link,
                SUM(CASE WHEN rc.id IS NOT NULL THEN 1 ELSE 0 END) AS club_referrals,
                SUM(CASE WHEN rp.id IS NOT NULL THEN 1 ELSE 0 END) AS player_referrals,
                SUM(CASE WHEN rc.id IS NOT NULL THEN 1000 ELSE 0 END)
                  + SUM(CASE WHEN rp.id IS NOT NULL THEN 10 ELSE 0 END) AS total_amount
            ')
            ->havingRaw('(club_referrals + player_referrals) > 0')
            ->orderByDesc('total_amount')
            ->get();

        // Clubs — Upcoming
        $clubsUpcoming = DB::table('invites as i')
            ->join('clubs as ic', 'ic.user_id', '=', 'i.sender_id')
            ->leftJoin('users as ru', 'ru.id', '=', 'i.receiver_id')
            ->leftJoin('players as rp', 'rp.user_id', '=', 'ru.id')
            ->leftJoin('clubs as rc', 'rc.user_id', '=', 'ru.id')
            ->whereNotNull('i.accepted_at')
            ->where('i.accepted_at', '>', $cutoff)
            ->where('i.payout_processed', false)
            ->groupBy('ic.id', 'ic.name', 'ic.paypal_link')
            ->selectRaw('
                ic.id,
                ic.name,
                ic.paypal_link,
                SUM(CASE WHEN rc.id IS NOT NULL THEN 1 ELSE 0 END) AS club_referrals,
                SUM(CASE WHEN rp.id IS NOT NULL THEN 1 ELSE 0 END) AS player_referrals,
                MIN(GREATEST(0, ? - DATEDIFF(NOW(), i.accepted_at))) AS min_days_left
            ', [self::DAYS_TO_PAYOUT])
            ->havingRaw('(club_referrals + player_referrals) > 0')
            ->orderBy('min_days_left')
            ->get();

        // Payments made
        $paid = DB::table('invites as i')
            ->leftJoin('users as su', 'su.id', '=', 'i.sender_id')
            ->leftJoin('players as ip', 'ip.user_id', '=', 'su.id')
            ->leftJoin('clubs as ic', 'ic.user_id', '=', 'su.id')
            ->leftJoin('users as ru', 'ru.id', '=', 'i.receiver_id')
            ->leftJoin('players as rp', 'rp.user_id', '=', 'ru.id')
            ->leftJoin('clubs as rc', 'rc.user_id', '=', 'ru.id')
            ->whereNotNull('i.accepted_at')
            ->where('i.payout_processed', true)
            ->groupBy('i.sender_id', 'ip.id', 'ip.name', 'ic.id', 'ic.name')
            ->selectRaw('
                CASE WHEN ip.id IS NOT NULL THEN "player" ELSE "club" END AS inviter_type,
                COALESCE(ip.name, ic.name) AS inviter_name,
                SUM(CASE WHEN rc.id IS NOT NULL THEN 1 ELSE 0 END) AS club_referrals_paid,
                SUM(CASE WHEN rp.id IS NOT NULL THEN 1 ELSE 0 END) AS player_referrals_paid,
                SUM(CASE WHEN rc.id IS NOT NULL THEN 1000 ELSE 0 END)
                  + SUM(CASE WHEN rp.id IS NOT NULL THEN 10 ELSE 0 END) AS total_paid,
                MAX(i.payout_processed_at) AS last_paid_at
            ')
            ->orderByDesc('last_paid_at')
            ->get();

        return view('admin.payments.index', [
            'totals'          => $totals,
            'playersReady'    => $playersReady,
            'playersUpcoming' => $playersUpcoming,
            'clubsReady'      => $clubsReady,
            'clubsUpcoming'   => $clubsUpcoming,
            'paid'            => $paid,
        ]);
    }

    // === Pay endpoints (AJAX) ===
    // NOTE: Implement your PayPal Payout call here (service not shown to keep this focused).
    // For demo, we'll mark the eligible invites as paid; you can plug in your PayPal service.

    public function payPlayerInviter(Player $player)
    {
        $cutoff = Carbon::now()->subDays(self::DAYS_TO_PAYOUT);

        // Eligible invites: invited by this player (sender_id = player's user_id), >= 90 days, unpaid
        $eligible = DB::table('invites as i')
            ->where('i.sender_id', $player->user_id)
            ->whereNotNull('i.accepted_at')
            ->where('i.accepted_at', '<=', $cutoff)
            ->where('i.payout_processed', false)
            ->pluck('i.id');

        if ($eligible->isEmpty()) {
            return response()->json(['ok' => false, 'message' => 'No eligible invites to pay.'], 422);
        }

        // Compute amount (club×1000 + player×10)
        $amounts = DB::table('invites as i')
            ->leftJoin('users as ru', 'ru.id', '=', 'i.receiver_id')
            ->leftJoin('players as rp', 'rp.user_id', '=', 'ru.id')
            ->leftJoin('clubs as rc', 'rc.user_id', '=', 'ru.id')
            ->whereIn('i.id', $eligible)
            ->selectRaw('
                SUM(CASE WHEN rc.id IS NOT NULL THEN 1000 ELSE 0 END) as club_amt,
                SUM(CASE WHEN rp.id IS NOT NULL THEN 10 ELSE 0 END)    as player_amt
            ')
            ->first();

        $total = (float) ($amounts->club_amt + $amounts->player_amt);

        if ($total <= 0) {
            return response()->json(['ok' => false, 'message' => 'Amount is zero.'], 422);
        }
        if (empty($player->paypal_link)) {
            return response()->json(['ok' => false, 'message' => 'Player has no PayPal destination set.'], 422);
        }

        // TODO: call PayPal Payouts API with $player->paypal_link and $total (USD)
        // If success, mark invites as paid:
        DB::table('invites')->whereIn('id', $eligible)->update([
            'payout_processed'    => true,
            'payout_processed_at' => Carbon::now(),
        ]);

        return response()->json(['ok' => true, 'message' => 'Payout sent.']);
    }

    public function payClubInviter(Club $club)
    {
        $cutoff = Carbon::now()->subDays(self::DAYS_TO_PAYOUT);

        $eligible = DB::table('invites as i')
            ->where('i.sender_id', $club->user_id)
            ->whereNotNull('i.accepted_at')
            ->where('i.accepted_at', '<=', $cutoff)
            ->where('i.payout_processed', false)
            ->pluck('i.id');

        if ($eligible->isEmpty()) {
            return response()->json(['ok' => false, 'message' => 'No eligible invites to pay.'], 422);
        }

        $amounts = DB::table('invites as i')
            ->leftJoin('users as ru', 'ru.id', '=', 'i.receiver_id')
            ->leftJoin('players as rp', 'rp.user_id', '=', 'ru.id')
            ->leftJoin('clubs as rc', 'rc.user_id', '=', 'ru.id')
            ->whereIn('i.id', $eligible)
            ->selectRaw('
                SUM(CASE WHEN rc.id IS NOT NULL THEN 1000 ELSE 0 END) as club_amt,
                SUM(CASE WHEN rp.id IS NOT NULL THEN 10 ELSE 0 END)    as player_amt
            ')
            ->first();

        $total = (float) ($amounts->club_amt + $amounts->player_amt);

        if ($total <= 0) {
            return response()->json(['ok' => false, 'message' => 'Amount is zero.'], 422);
        }
        if (empty($club->paypal_link)) {
            return response()->json(['ok' => false, 'message' => 'Club has no PayPal destination set.'], 422);
        }

        // TODO: call PayPal Payouts API with $club->paypal_link and $total (USD)
        DB::table('invites')->whereIn('id', $eligible)->update([
            'payout_processed'    => true,
            'payout_processed_at' => Carbon::now(),
        ]);

        return response()->json(['ok' => true, 'message' => 'Payout sent.']);
    }
}
