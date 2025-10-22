<?php

namespace App\Http\Controllers;

use App\Models\ClubInvite;
use Illuminate\Http\RedirectResponse;

class TournamentInviteAcceptanceController extends Controller
{
    public function __invoke(string $token): RedirectResponse
    {
        $invite = ClubInvite::where('token', $token)->with('tournament')->firstOrFail();

        if ($invite->status === ClubInvite::STATUS_REGISTERED) {
            return redirect()->route('login')
                ->with('info', 'This tournament invitation has already been completed. Please sign in to manage your teams.');
        }

        if (! $invite->accepted_at) {
            $invite->accepted_at = now();
            if (! $invite->inviter_club_id && $invite->tournament) {
                $invite->inviter_club_id = $invite->tournament->host_club_id;
            }
            $invite->save();
        }

        session(['pending_tournament_invite_token' => $invite->token]);

        return redirect()->route('register.club', ['invite_token' => $invite->token])
            ->with('success', 'Great! Let’s create your club account to join the tournament.');
    }


}
