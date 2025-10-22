<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupGame;
use App\Models\Sport;
use Illuminate\Http\Request;

class PickupGameController extends Controller
{
    public function index()
    {
        $games = PickupGame::with('sport', 'host')->latest()->paginate(10);
        return view('admin.pickup_games.index', compact('games'));
    }

    public function create()
    {
        $sports = Sport::all();
        return view('admin.pickup_games.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'game_datetime' => 'required|date',
            'location' => 'required',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'max_players' => 'required|integer|min:1',
            'privacy' => 'required|in:public,private',
            'join_fee' => 'nullable|numeric',
        ]);

        $validated['host_id'] = auth()->id();
        PickupGame::create($validated);

        return redirect()->route('admin.pickup_games.index')->with('success', 'Pickup game created successfully.');
    }

    public function join($id)
    {
        $game = PickupGame::findOrFail($id);
        $user = Auth::user();

        if ($game->participants()->count() >= $game->max_players) {
            return back()->withErrors(['msg' => 'Game is full.']);
        }

        $game->participants()->syncWithoutDetaching([$user->id]);

        return back()->with('success', 'You have joined the game.');
    }

    public function leave($id)
    {
        $game = PickupGame::findOrFail($id);
        $user = Auth::user();

        $game->participants()->detach($user->id);

        return back()->with('success', 'You have left the game.');
    }

}
