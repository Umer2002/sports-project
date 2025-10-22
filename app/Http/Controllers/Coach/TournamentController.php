<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\GameMatch;
use App\Models\Team;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Get tournaments where coach's teams are participating
        $teamIds = $coach->teams()->pluck('teams.id');
        $tournaments = Tournament::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->with(['teams', 'matches'])
        ->orderBy('start_date', 'desc')
        ->paginate(20);

        return view('coach.tournaments.index', compact('tournaments'));
    }

    public function show(Tournament $tournament)
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Check if coach has teams in this tournament
        $teamIds = $coach->teams()->pluck('teams.id');
        $hasAccess = $tournament->teams()->whereIn('teams.id', $teamIds)->exists();
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this tournament');
        }

        // Get matches for this tournament where coach's teams are playing
        $matches = GameMatch::where('tournament_id', $tournament->id)
            ->where(function($query) use ($teamIds) {
                $query->whereIn('home_club_id', $teamIds)
                      ->orWhereIn('away_club_id', $teamIds);
            })
            ->with(['homeClub', 'awayClub', 'tournament'])
            ->orderBy('match_date', 'asc')
            ->get();

        return view('coach.tournaments.show', compact('tournament', 'matches'));
    }

    public function matchStats(GameMatch $match)
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Check if coach has teams in this match
        $teamIds = $coach->teams()->pluck('teams.id');
        $hasAccess = in_array($match->home_club_id, $teamIds->toArray()) || 
                     in_array($match->away_club_id, $teamIds->toArray());
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this match');
        }

        // Get players for both teams
        $homeTeamPlayers = collect();
        $awayTeamPlayers = collect();
        
        if ($match->homeClub) {
            $homeTeamPlayers = $match->homeClub->players ?? collect();
        }
        if ($match->awayClub) {
            $awayTeamPlayers = $match->awayClub->players ?? collect();
        }

        return view('coach.tournaments.match-stats', compact('match', 'homeTeamPlayers', 'awayTeamPlayers'));
    }

    public function updateMatchStats(Request $request, GameMatch $match)
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Check if coach has teams in this match
        $teamIds = $coach->teams()->pluck('teams.id');
        $hasAccess = in_array($match->home_club_id, $teamIds->toArray()) || 
                     in_array($match->away_club_id, $teamIds->toArray());
        
        if (!$hasAccess) {
            abort(403, 'You do not have access to this match');
        }

        $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
            'match_status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Update match with scores and status
        $match->update([
            'score' => [
                'home' => $request->home_score,
                'away' => $request->away_score,
            ],
            'status' => $request->match_status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('coach.tournaments.show', $match->tournament_id)
            ->with('success', 'Match stats updated successfully!');
    }
}
