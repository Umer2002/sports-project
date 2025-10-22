<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Team;

class TeamController extends Controller
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

        $teams = $coach->teams()->with(['sport', 'players'])->get();

        return view('coach.teams.index', compact('teams'));
    }

    public function show(Team $team)
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Check if coach is assigned to this team
        if (!$coach->teams()->where('teams.id', $team->id)->exists()) {
            abort(403, 'You are not assigned to this team.');
        }

        $team->load(['players', 'sport', 'club', 'coaches']);

        return view('coach.teams.show', compact('team'));
    }
}

