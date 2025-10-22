<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Player;

class PlayerController extends Controller
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

        // Get all players from teams the coach is assigned to
        $teamIds = $coach->teams()->pluck('teams.id');
        $players = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->with(['team', 'sport', 'position'])->paginate(20);

        return view('coach.players.index', compact('players'));
    }

    public function show(Player $player)
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Check if player belongs to one of coach's teams
        $teamIds = $coach->teams()->pluck('teams.id');
        $hasAccess = $player->teams()->whereIn('teams.id', $teamIds)->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this player.');
        }

        $player->load(['team', 'teams', 'sport', 'position', 'stats']);

        return view('coach.players.show', compact('player'));
    }
}

