<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Mail\InviteEmail;
use App\Models\Club;
use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InviteController extends Controller
{
    public function create()
    {
        return view('players.invite');
    }

    public function createClub()
    {
        return view('players.invite-club');
    }

    public function store(Request $request)
    {
        $formData = $this->decodeFormData($request->input('form_data'));
        $contacts = $this->collectContacts($formData, $request->input('contact_list'));

        if ($contacts->isEmpty()) {
            return back()->withInput()->withErrors([
                'contacts' => 'Please provide at least one valid email address to invite.',
            ]);
        }

        [$sent, $failed] = $this->dispatchInvites($contacts, 'player', $formData);

        $message = $sent->isNotEmpty()
            ? sprintf('Invitations sent to %d contact(s).', $sent->count())
            : 'No invitations were sent.';

        return back()->with([
            'success' => $message,
            'sent_invites' => $sent,
            'failed_invites' => $failed,
            'link' => $this->defaultShareLink(),
        ])->withInput();
    }

    public function storeClub(Request $request)
    {
        $formData = $this->decodeFormData($request->input('form_data'));
        $contacts = $this->collectContacts($formData, $formData['contact_list'] ?? null);

        if ($contacts->isEmpty()) {
            return back()->withInput()->withErrors([
                'contacts' => 'Please provide at least one club contact email.',
            ]);
        }

        $formData['club_name'] = $formData['club_name'] ?? ($formData['club'] ?? null);
        $formData['league'] = $formData['league'] ?? null;
        $formData['location'] = $formData['location'] ?? null;
        $formData['website'] = $formData['website'] ?? null;
        $formData['source'] = 'player_club_invite';

        [$sent, $failed] = $this->dispatchInvites($contacts, 'club', $formData);

        $message = $sent->isNotEmpty()
            ? sprintf('Club invitations sent to %d contact(s).', $sent->count())
            : 'No club invitations were sent.';

        return back()->with([
            'success' => $message,
            'sent_invites' => $sent,
            'failed_invites' => $failed,
            'link' => $this->defaultShareLink('club'),
        ])->withInput();
    }

    public function overview()
    {
        $user = Auth::user();

        $playerInvites = Invite::query()
            ->where('sender_id', $user->id)
            ->whereIn('type', ['player', 'player_free'])
            ->get();

        $playerAccepted = $playerInvites->where('is_accepted', true)->count();
        $playerEarnings = $playerAccepted * 10;

        $clubInvites = Invite::query()
            ->where('sender_id', $user->id)
            ->whereIn('type', ['club', 'club_invite'])
            ->get();

        $clubStats = $this->buildClubReferralStats($clubInvites);
        $clubEarnings = $clubStats->sum('payout_earned');
        $totalEarnings = $playerEarnings + $clubEarnings;

        $chequeNumber = str_pad((string) $user->id, 6, '0', STR_PAD_LEFT);
        $chequeDate = now()->format('m/d/Y');
        $amountFormatted = '$' . number_format($totalEarnings, 2);
        $amountWords = $this->amountToWords($totalEarnings);

        return view('players.invite-overview', [
            'user' => $user,
            'playerInvites' => $playerInvites,
            'playerAccepted' => $playerAccepted,
            'playerEarnings' => $playerEarnings,
            'clubStats' => $clubStats,
            'clubEarnings' => $clubEarnings,
            'totalEarnings' => $totalEarnings,
            'chequeNumber' => $chequeNumber,
            'chequeDate' => $chequeDate,
            'amountFormatted' => $amountFormatted,
            'amountWords' => $amountWords,
        ]);
    }

    protected function decodeFormData(?string $json): array
    {
        if (! $json) {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function collectContacts(array $formData, ?string $fallbackList = null): Collection
    {
        $contacts = collect($formData['contacts'] ?? [])
            ->filter()
            ->map(function ($value) {
                return is_string($value) ? trim($value) : null;
            })
            ->filter();

        if (! empty($formData['contactList']) && is_string($formData['contactList'])) {
            $contacts = $contacts->merge($this->splitContactList($formData['contactList']));
        }

        if (! empty($formData['contact_list'])) {
            $contacts = $contacts->merge($this->splitContactList($formData['contact_list']));
        }

        if ($fallbackList) {
            $contacts = $contacts->merge($this->splitContactList($fallbackList));
        }

        return $contacts
            ->filter(fn ($value) => filter_var($value, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();
    }

    protected function splitContactList(string $raw): array
    {
        return collect(preg_split('/[\s,;]+/', $raw))
            ->filter(fn ($value) => ! empty($value))
            ->map(fn ($value) => trim($value))
            ->all();
    }

    protected function dispatchInvites(Collection $emails, string $type, array $metadata = []): array
    {
        $sender = Auth::user();
        $sent = collect();
        $failed = collect();

        foreach ($emails as $email) {
            try {
                $invite = Invite::updateOrCreate([
                    'sender_id' => $sender->id,
                    'receiver_email' => $email,
                    'type' => $type,
                ], [
                    'receiver_id' => null,
                    'reference_id' => $sender->id,
                ]);

                $storedContacts = collect($metadata['contacts'] ?? [])
                    ->merge($this->splitContactList($metadata['contact_list'] ?? ''))
                    ->merge($this->splitContactList($metadata['contactList'] ?? ''))
                    ->merge([$email])
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $meta = array_merge($invite->metadata ?? [], $metadata, [
                    'contacts' => $storedContacts,
                    'source' => $metadata['source'] ?? ($type === 'club' ? 'club_referral' : 'player_referral'),
                ]);

                $invite->forceFill([
                    'token' => Str::uuid(),
                    'metadata' => $meta,
                    'email_attempts' => ($invite->email_attempts ?? 0) + 1,
                    'email_last_attempt_at' => now(),
                ])->save();

                $inviteLink = $invite->getInviteLink();

                Mail::to($email)->send(new InviteEmail($inviteLink, $sender, [
                    'subject' => $type === 'club'
                        ? 'Play2Earn Sports club invitation'
                        : 'Join me on Play2Earn Sports!',
                    'referral_code' => $sender->referral_code ?? null,
                    'custom_message' => $metadata['personal_message'] ?? null,
                ]));

                $invite->forceFill([
                    'email_sent_at' => now(),
                ])->save();

                $sent->push($email);
            } catch (\Throwable $exception) {
                report($exception);
                $failed->push([
                    'email' => $email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return [$sent, $failed];
    }

    protected function defaultShareLink(string $type = 'player'): string
    {
        return url('/invite/' . Auth::id());
    }

    protected function buildClubReferralStats(Collection $invites): Collection
    {
        if ($invites->isEmpty()) {
            return collect();
        }

        $clubNames = $invites
            ->map(fn ($invite) => trim($invite->metadata['club_name'] ?? ''))
            ->filter()
            ->unique();

        $clubs = Club::query()
            ->whereIn('name', $clubNames->all())
            ->withCount('players')
            ->get()
            ->keyBy(fn ($club) => strtolower($club->name));

        return $invites
            ->groupBy(fn ($invite) => strtolower(trim($invite->metadata['club_name'] ?? Str::uuid())))
            ->map(function ($group, $key) use ($clubs) {
                /** @var Invite $first */
                $first = $group->first();
                $meta = $first->metadata ?? [];
                $clubName = $meta['club_name'] ?? 'Unknown Club';
                $clubModel = $clubs->get($key);
                $playersRegistered = $clubModel?->players_count ?? 0;
                $payoutEarned = intdiv($playersRegistered, 200) * 1000;

                $contactCount = $group
                    ->flatMap(fn ($invite) => $invite->metadata['contacts'] ?? [])
                    ->filter()
                    ->unique()
                    ->count();

                return [
                    'club_name' => $clubName,
                    'league' => $meta['league'] ?? null,
                    'location' => $meta['location'] ?? null,
                    'website' => $meta['website'] ?? null,
                    'contacts_sent' => $contactCount,
                    'players_registered' => $playersRegistered,
                    'payout_earned' => $payoutEarned,
                    'status' => $playersRegistered >= 200 ? 'Qualified' : 'In progress',
                ];
            })
            ->sortByDesc('players_registered')
            ->values();
    }

    protected function amountToWords(float $amount): string
    {
        $amount = round($amount, 2);
        $integer = (int) floor($amount);
        $cents = (int) round(($amount - $integer) * 100);

        if ($integer === 0 && $cents === 0) {
            return 'Zero dollars';
        }

        if (class_exists(\NumberFormatter::class)) {
            $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
            $words = ucfirst($formatter->format($integer));
        } else {
            $words = number_format($integer);
        }

        $words .= ' dollars';

        if ($cents > 0) {
            $words .= ' and ' . str_pad((string) $cents, 2, '0', STR_PAD_LEFT) . '/100';
        }

        return $words;
    }
}
