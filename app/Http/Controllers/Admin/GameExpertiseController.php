<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\PickupGame;
use App\Models\Expertise;
use App\Models\Referee;
use Illuminate\Http\Request;

class GameExpertiseController extends Controller
{
    /**
     * Display games with expertise management
     */
    public function index()
    {
        $games = Game::with(['tournament', 'homeClub', 'awayClub', 'expertise'])
            ->upcoming()
            ->get();
            
        $pickupGames = PickupGame::with(['sport', 'host', 'expertise'])
            ->where('game_datetime', '>', now())
            ->get();
            
        $expertises = Expertise::all();
        
        return view('admin.game-expertise.index', compact('games', 'pickupGames', 'expertises'));
    }

    /**
     * Update game expertise requirement
     */
    public function updateGameExpertise(Request $request, Game $game)
    {
        $request->validate([
            'expertise_id' => 'nullable|exists:expertises,id'
        ]);

        $game->expertise_id = $request->expertise_id;
        $game->save();

        return response()->json([
            'success' => true,
            'message' => 'Game expertise requirement updated successfully!'
        ]);
    }

    /**
     * Update pickup game expertise requirement
     */
    public function updatePickupGameExpertise(Request $request, PickupGame $pickupGame)
    {
        $request->validate([
            'expertise_id' => 'nullable|exists:expertises,id'
        ]);

        $pickupGame->expertise_id = $request->expertise_id;
        $pickupGame->save();

        return response()->json([
            'success' => true,
            'message' => 'Pickup game expertise requirement updated successfully!'
        ]);
    }

    /**
     * Get available referees for a game
     */
    public function getAvailableReferees(Game $game)
    {
        $availableReferees = $game->availableReferees()->with('user')->get();
        
        return response()->json([
            'success' => true,
            'referees' => $availableReferees->map(function($referee) {
                return [
                    'id' => $referee->id,
                    'name' => $referee->full_name,
                    'email' => $referee->user ? $referee->user->email : 'N/A',
                    'expertise_levels' => $referee->expertises->pluck('level')->filter()->values()->toArray(),
                    'status' => 'available'
                ];
            })
        ]);
    }

    /**
     * Get available referees for a pickup game
     */
    public function getAvailableRefereesForPickupGame(PickupGame $pickupGame)
    {
        $availableReferees = $pickupGame->availableReferees()->with('user')->get();
        
        return response()->json([
            'success' => true,
            'referees' => $availableReferees->map(function($referee) {
                return [
                    'id' => $referee->id,
                    'name' => $referee->full_name,
                    'email' => $referee->user ? $referee->user->email : 'N/A',
                    'expertise_levels' => $referee->expertises->pluck('level')->filter()->values()->toArray(),
                    'status' => 'available'
                ];
            })
        ]);
    }

    /**
     * Assign referee to a game
     */
    public function assignRefereeToGame(Request $request, Game $game)
    {
        $request->validate([
            'referee_id' => 'required|exists:referees,id'
        ]);

        // Check if referee is qualified for this game
        $referee = Referee::find($request->referee_id);
        if (!$game->availableReferees()->where('referees.id', $referee->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This referee is not qualified for this game based on expertise requirements.'
            ], 400);
        }

        $game->update(['referee_id' => $request->referee_id]);

        return response()->json([
            'success' => true,
            'message' => 'Referee assigned successfully!'
        ]);
    }

    /**
     * Assign referee to a pickup game
     */
    public function assignRefereeToPickupGame(Request $request, PickupGame $pickupGame)
    {
        $request->validate([
            'referee_id' => 'required|exists:referees,id'
        ]);

        // Check if referee is qualified for this pickup game
        $referee = Referee::find($request->referee_id);
        if (!$pickupGame->availableReferees()->where('referees.id', $referee->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This referee is not qualified for this pickup game based on expertise requirements.'
            ], 400);
        }

        $pickupGame->update(['referee_id' => $request->referee_id]);

        return response()->json([
            'success' => true,
            'message' => 'Referee assigned successfully!'
        ]);
    }

    /**
     * Remove referee assignment from a game
     */
    public function removeRefereeFromGame(Game $game)
    {
        $game->update(['referee_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Referee assignment removed successfully!'
        ]);
    }

    /**
     * Remove referee assignment from a pickup game
     */
    public function removeRefereeFromPickupGame(PickupGame $pickupGame)
    {
        $pickupGame->update(['referee_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Referee assignment removed successfully!'
        ]);
    }
}