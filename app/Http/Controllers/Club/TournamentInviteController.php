<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\ClubInvite;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TournamentInviteController extends Controller
{
    public function store(Request $request, Tournament $tournament): RedirectResponse
    {
        $club = auth()->user()->club;

        if (! $club || $tournament->host_club_id !== $club->id) {
            abort(403, 'You may only invite clubs to tournaments you host.');
        }

        $data = $request->validate([
            'invitee_club_name' => 'required|string|max:191',
            'email' => 'nullable|email|max:191',
            'notes' => 'nullable|string|max:1000',
        ]);

        $invite = new ClubInvite([
            'tournament_id' => $tournament->id,
            'invitee_club_name' => $data['invitee_club_name'],
            'email' => $data['email'] ?? null,
            'status' => ClubInvite::STATUS_PENDING,
            'inviter_club_id' => $club->id,
            'notes' => $data['notes'] ?? null,
        ]);

        $invite->token = Str::uuid();
        $invite->save();

        $inviteLink = route('tournaments.invites.accept', $invite->token);

        return redirect()->route('club.tournaments.show', $tournament)
            ->with('success', 'Invitation created. Share the invite link with the club you want to join.')
            ->with('invite_link', $inviteLink);
    }
}
