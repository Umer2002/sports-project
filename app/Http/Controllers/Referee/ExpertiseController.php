<?php

namespace App\Http\Controllers\Referee;

use App\Http\Controllers\Controller;
use App\Models\Expertise;
use App\Models\Referee;
use App\Models\Game;
use App\Models\PickupGame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpertiseController extends Controller
{
    /**
     * Display referee's expertise management page
     */
    public function index()
    {
        $referee = Auth::user()->referee;
        $allExpertises = Expertise::all();
        $refereeExpertises = $referee->expertises;
        
        return view('referee.expertise.index', compact('referee', 'allExpertises', 'refereeExpertises'));
    }

    /**
     * Update referee's expertise levels
     */
    public function update(Request $request)
    {
        $referee = Auth::user()->referee;
        
        $request->validate([
            'expertise_ids' => 'array',
            'expertise_ids.*' => 'exists:expertises,id'
        ]);

        // Sync the expertise levels (removes old ones, adds new ones)
        $referee->expertises()->sync($request->expertise_ids ?? []);

        return redirect()->route('referee.expertise.index')
            ->with('success', 'Expertise levels updated successfully!');
    }

    /**
     * Display available games for this referee based on expertise
     */
    public function availableGames()
    {
        $referee = Auth::user()->referee;
        $refereeExpertiseIds = $referee->expertises->pluck('id');
        
        // Get games that match referee's expertise levels
        $availableGames = Game::whereIn('expertise_id', $refereeExpertiseIds)
            ->orWhereNull('expertise_id') // Games with no expertise requirement
            ->with(['tournament', 'homeClub', 'awayClub', 'expertise'])
            ->upcoming()
            ->get();
            
        // Get pickup games that match referee's expertise levels
        $availablePickupGames = PickupGame::whereIn('expertise_id', $refereeExpertiseIds)
            ->orWhereNull('expertise_id') // Games with no expertise requirement
            ->with(['sport', 'host', 'expertise'])
            ->where('game_datetime', '>', now())
            ->get();

        return view('referee.expertise.available-games', compact(
            'referee', 
            'availableGames', 
            'availablePickupGames'
        ));
    }

    /**
     * Display all games (for admin/overview)
     */
    public function allGames()
    {
        $referee = Auth::user()->referee;
        $refereeExpertiseIds = $referee->expertises->pluck('id');
        
        // Get all games with expertise info
        $allGames = Game::with(['tournament', 'homeClub', 'awayClub', 'expertise'])
            ->upcoming()
            ->get();
            
        $allPickupGames = PickupGame::with(['sport', 'host', 'expertise'])
            ->where('game_datetime', '>', now())
            ->get();

        return view('referee.expertise.all-games', compact(
            'referee', 
            'allGames', 
            'allPickupGames',
            'refereeExpertiseIds'
        ));
    }
}