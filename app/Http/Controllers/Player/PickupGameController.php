<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\PickupGame;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PickupGameController extends Controller
{
    public function index(Request $request)
    {
        $player = Auth::user()->player;
        
        // Get search parameters
        $search = $request->get('search');
        $sport_id = $request->get('sport_id');
        $location = $request->get('location');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        
        $myGames = PickupGame::where('host_id', Auth::id())
            ->with(['sport', 'participants'])
            ->latest()
            ->get();

        $joinedGames = PickupGame::whereHas('participants', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->with(['sport', 'host', 'participants'])
        ->latest()
        ->get();

        // Get available games (future games that user is not hosting and not joined)
        $gamesQuery = PickupGame::with(['sport', 'host', 'participants'])
            ->where('game_datetime', '>=', now())
            ->where('host_id', '!=', Auth::id())
            ->whereDoesntHave('participants', function($query) {
                $query->where('user_id', Auth::id());
            });

        // Apply search filters
        if ($search) {
            $gamesQuery->where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($sport_id) {
            $gamesQuery->where('sport_id', $sport_id);
        }

        if ($location) {
            $gamesQuery->where('location', 'like', "%{$location}%");
        }

        if ($date_from) {
            $gamesQuery->whereDate('game_datetime', '>=', $date_from);
        }

        if ($date_to) {
            $gamesQuery->whereDate('game_datetime', '<=', $date_to);
        }

        $games = $gamesQuery->orderBy('game_datetime', 'asc')->paginate(12);
        
        // Get sports for filter dropdown
        $sports = \App\Models\Sport::all();

        return view('players.pickup_games.index', compact('games', 'myGames', 'joinedGames', 'player', 'sports', 'search', 'sport_id', 'location', 'date_from', 'date_to'));
    }

    public function create()
    {
        $player = Auth::user()->player;
        $sports = Sport::all();
        return view('players.pickup_games.create', compact('sports', 'player'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'game_datetime' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'max_players' => 'required|integer|min:2|max:50',
            'skill_level' => 'required|in:beginner,intermediate,advanced,all_levels',
            'equipment_needed' => 'nullable|string',
            'privacy' => 'required|in:public,private',
            'join_fee' => 'nullable|numeric|min:0'
        ]);

        $game = PickupGame::create([
            'sport_id' => $request->sport_id,
            'title' => $request->title,
            'description' => $request->description,
            'game_datetime' => $request->game_datetime,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'max_players' => $request->max_players,
            'skill_level' => $request->skill_level,
            'equipment_needed' => $request->equipment_needed,
            'privacy' => $request->privacy,
            'join_fee' => $request->join_fee,
            'host_id' => Auth::id(),
            'share_link' => route('player.pickup-games.show', ['pickup_game' => Str::random(10)])
        ]);

        return redirect()->route('player.pickup-games.show', ['pickup_game' => $game])
            ->with('success', 'Fun game created successfully! Share the link with your friends.');
    }

    public function show(PickupGame $pickup_game)
    {
        $player = Auth::user()->player;
        $pickup_game->load(['sport', 'host', 'participants']);
        // dd($pickup_game);

        return view('players.pickup_games.show', compact('pickup_game', 'player'));
    }

        public function join(PickupGame $pickup_game)
    {
        if (!$pickup_game->canJoin(Auth::id())) {
            return back()->with('error', 'Cannot join this game. It might be full or you are already a participant.');
        }

        $pickup_game->participants()->attach(Auth::id(), [
            'attendance_confirmed' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'You have joined the game!');
    }

    public function leave(PickupGame $pickup_game)
    {
        $pickup_game->participants()->detach(Auth::id());

        return back()->with('success', 'You have left the game.');
    }

    public function share(PickupGame $pickup_game)
    {
        if (!$pickup_game->isHostedBy(Auth::id())) {
            abort(403);
        }

        $shareLink = $pickup_game->generateShareLink();

        return back()->with('success', 'Share link generated: ' . $shareLink);
    }

    public function edit(PickupGame $game)
    {
        if (!$game->isHostedBy(Auth::id())) {
            abort(403);
        }

        $player = Auth::user()->player;
        $sports = Sport::all();

        return view('players.pickup_games.edit', compact('game', 'sports', 'player'));
    }

    public function update(Request $request, PickupGame $game)
    {
        if (!$game->isHostedBy(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'game_datetime' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'max_players' => 'required|integer|min:2|max:50',
            'skill_level' => 'required|in:beginner,intermediate,advanced,all_levels',
            'equipment_needed' => 'nullable|string',
            'privacy' => 'required|in:public,private',
            'join_fee' => 'nullable|numeric|min:0'
        ]);

        $game->update($request->all());

        return redirect()->route('player.pickup-games.show', ['pickup_game' => $game])
            ->with('success', 'Game updated successfully!');
    }

    public function destroy(PickupGame $game)
    {
        if (!$game->isHostedBy(Auth::id())) {
            abort(403);
        }

        $game->delete();

        return redirect()->route('player.pickup_games.index')
            ->with('success', 'Game cancelled successfully.');
    }
}
