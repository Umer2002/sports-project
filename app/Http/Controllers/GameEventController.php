<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameEvent;
use Illuminate\Http\Request;

class GameEventController extends Controller
{
    public function feed(Game $game)
    {
        return view('games.feed', ['game' => $game]);
    }

    public function list(Game $game, Request $request)
    {
        $lastId = $request->query('last_id');
        $query = GameEvent::where('game_id', $game->id)->orderBy('id');
        if ($lastId) {
            $query->where('id', '>', $lastId);
        }
        return response()->json($query->get());
    }

    public function store(Game $game, Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'minute' => 'nullable|integer',
        ]);

        $event = $game->events()->create($data);
        return response()->json($event);
    }
}
