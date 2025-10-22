<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TournamentRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\ClubInvite;
use Auth;
class TournamentRegistrationController extends Controller
{
    public function setup(Request $request, TournamentRegistration $registration): View
    {
        $club = auth()->user()->club;
        $this->authorizeRegistration($registration, $club);

        $registration->load(['tournament', 'invite']);
        $tournament = $registration->tournament;

        return view('club.tournament_registrations.setup', [
            'registration' => $registration,
            'tournament' => $tournament,
        ]);
    }

    public function storeSetup(Request $request, TournamentRegistration $registration): RedirectResponse
    {
        $club = auth()->user()->club;
        $this->authorizeRegistration($registration, $club);

        $registration->loadMissing('tournament');
        $tournament = $registration->tournament;

        $rules = [
            'team_quantity' => 'nullable|integer|min:1|max:50',
            'confirm_payment' => 'accepted',
        ];

        if ($registration->joining_type === 'per_team') {
            $rules['team_quantity'] = 'required|integer|min:1|max:50';
        }

        $validated = $request->validate($rules, [
            'confirm_payment.accepted' => 'Please confirm the tournament fee to continue.',
        ]);

        $teamQuantity = $registration->joining_type === 'per_team'
            ? (int) $validated['team_quantity']
            : 1;

        $amountDue = round($teamQuantity * (float) $registration->joining_fee, 2);

        $registration->fill([
            'team_quantity' => $teamQuantity,
            'amount_due' => $amountDue,
            'amount_paid' => $amountDue,
            'paid_at' => now(),
            'status' => TournamentRegistration::STATUS_PAID,
        ])->save();

        return redirect()
            ->route('club.tournament-registrations.show', $registration)
            ->with('success', 'Tournament registration saved. Now create your teams and add them to the tournament.');
    }

    public function show(TournamentRegistration $registration): View
    {
        $club = auth()->user()->club;
        $this->authorizeRegistration($registration, $club);

        $registration->load(['tournament.teams', 'tournament.hostClub']);

        $teams = Team::where('club_id', $registration->club_id)
            ->orderBy('name')
            ->get();

        $attachedTeamIds = $registration->tournament->teams
            ->where('club_id', $registration->club_id)
            ->pluck('id')
            ->all();

        return view('club.tournament_registrations.show', [
            'registration' => $registration,
            'tournament' => $registration->tournament,
            'teams' => $teams,
            'attachedTeamIds' => $attachedTeamIds,
        ]);
    }

    public function attachTeams(Request $request, TournamentRegistration $registration): RedirectResponse
    {
        $club = auth()->user()->club;
        $this->authorizeRegistration($registration, $club);

        if ($registration->status !== TournamentRegistration::STATUS_PAID && $registration->status !== TournamentRegistration::STATUS_TEAMS_CREATED) {
            return redirect()->route('club.tournament-registrations.show', $registration)
                ->with('error', 'Please complete the registration payment before adding teams.');
        }

        $teamIds = collect($request->input('team_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($teamIds->isEmpty()) {
            return redirect()->route('club.tournament-registrations.show', $registration)
                ->with('error', 'Select at least one team to add.');
        }

        $validTeamIds = Team::where('club_id', $registration->club_id)
            ->whereIn('id', $teamIds)
            ->pluck('id');

        if ($validTeamIds->count() !== $teamIds->count()) {
            return redirect()->route('club.tournament-registrations.show', $registration)
                ->with('error', 'One or more selected teams do not belong to your club.');
        }

        $existingTeamIds = $registration->tournament->teams()
            ->where('teams.club_id', $registration->club_id)
            ->pluck('teams.id');

        $newTeamIds = $teamIds->diff($existingTeamIds);
        $totalTeams = $existingTeamIds->count() + $newTeamIds->count();

        if ($registration->joining_type === 'per_team' && $totalTeams > $registration->team_quantity) {
            return redirect()->route('club.tournament-registrations.show', $registration)
                ->with('error', 'You have selected more teams than purchased. Reduce your selection or update the team quantity with the tournament host.');
        }

        if ($newTeamIds->isNotEmpty()) {
            $registration->tournament->teams()->syncWithoutDetaching($newTeamIds->all());
        }

        if ($registration->joining_type === 'per_team' && $totalTeams >= $registration->team_quantity) {
            $registration->update(['status' => TournamentRegistration::STATUS_COMPLETED]);
        } elseif ($registration->joining_type === 'per_club' && $totalTeams > 0) {
            $registration->update(['status' => TournamentRegistration::STATUS_COMPLETED]);
        } else {
            $registration->update(['status' => TournamentRegistration::STATUS_TEAMS_CREATED]);
        }

        return redirect()->route('club.tournament-registrations.show', $registration)
            ->with('success', 'Teams added to the tournament.');
    }

    private function authorizeRegistration(TournamentRegistration $registration, $club): void
    {
        if (! $club || $registration->club_id !== $club->id) {
            abort(403, 'You do not have access to this tournament registration.');
        }
    }

     // create a function that will list tournaments which invites are accepted
    public function listTournaments()
{
    $clubId = Auth::user()->club->id;

    $invites = ClubInvite::query()
        ->where('status', ClubInvite::STATUS_REGISTERED)
        ->where('registered_club_id', $clubId)
        ->with([
            'tournament' => function ($q) use ($clubId) {
                $q->with([
                    'venue:id,name', // if you have a Venue relation
                    // Filter registrations to THIS club and get the latest one
                    'registrations' => fn ($r) => $r->where('club_id', $clubId)->latest(),
                ])->select([
                    'id','name','location','venue_id','start_date','end_date','joining_fee','joining_type'
                ]);
            },
        ])
        ->get();

    return view('club.tournaments.list', compact('invites'));
}
}
