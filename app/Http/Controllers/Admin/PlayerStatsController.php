<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Models\Stat;
use App\Models\Team;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayerStatsController extends Controller
{
    /**
     * Display a listing of sport stat configurations.
     */
    public function index()
    {
        $playerStats = PlayerGameStat::with(['sport'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $sports = Sport::all();

        return view('admin.player-stats.index', compact('playerStats', 'sports'));
    }

    /**
     * Show the form for creating new sport stat configuration.
     */
    public function create()
    {
        $sports = Sport::all();
        $stats = Stat::all();
        
        return view('admin.player-stats.create', compact('sports', 'stats'));
    }

    /**
     * Store sport stat configuration.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'stat1_id' => 'required|exists:stats,id',
            'stat2_id' => 'required|exists:stats,id',
            'stat3_id' => 'required|exists:stats,id',
            'stat4_id' => 'required|exists:stats,id',
        ]);

        try {
            DB::beginTransaction();

            // Get stat names from IDs
            $stat1 = Stat::findOrFail($request->stat1_id);
            $stat2 = Stat::findOrFail($request->stat2_id);
            $stat3 = Stat::findOrFail($request->stat3_id);
            $stat4 = Stat::findOrFail($request->stat4_id);

            // Update or create sport stat configuration
            PlayerGameStat::updateOrCreate(
                ['sport_id' => $request->sport_id],
                [
                    'stat1' => $stat1->name,
                    'stat2' => $stat2->name,
                    'stat3' => $stat3->name,
                    'stat4' => $stat4->name,
                ]
            );

            DB::commit();

            return redirect()->route('admin.player-stats.index')
                ->with('success', 'Sport stats configuration saved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving sport stats configuration: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to save sport stats configuration. Please try again.']);
        }
    }

    /**
     * Display the specified sport stat configuration.
     */
    public function show(PlayerGameStat $playerStat)
    {
        $playerStat->load(['sport']);
        
        return view('admin.player-stats.show', compact('playerStat'));
    }

    /**
     * Show the form for editing the specified sport stat configuration.
     */
    public function edit(PlayerGameStat $playerStat)
    {
        $playerStat->load(['sport']);
        $sports = Sport::all();
        $stats = Stat::all();
        
        return view('admin.player-stats.edit', compact('playerStat', 'sports', 'stats'));
    }

    /**
     * Update the specified sport stat configuration.
     */
    public function update(Request $request, PlayerGameStat $playerStat)
    {
        $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'stat1_id' => 'required|exists:stats,id',
            'stat2_id' => 'required|exists:stats,id',
            'stat3_id' => 'required|exists:stats,id',
            'stat4_id' => 'required|exists:stats,id',
        ]);

        try {
            // Get stat names from IDs
            $stat1 = Stat::findOrFail($request->stat1_id);
            $stat2 = Stat::findOrFail($request->stat2_id);
            $stat3 = Stat::findOrFail($request->stat3_id);
            $stat4 = Stat::findOrFail($request->stat4_id);

            $playerStat->update([
                'sport_id' => $request->sport_id,
                'stat1' => $stat1->name,
                'stat2' => $stat2->name,
                'stat3' => $stat3->name,
                'stat4' => $stat4->name,
            ]);

            return redirect()->route('admin.player-stats.index')
                ->with('success', 'Sport stat configuration updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating sport stat configuration: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to update sport stat configuration. Please try again.']);
        }
    }

    /**
     * Remove the specified sport stat configuration.
     */
    public function destroy(PlayerGameStat $playerStat)
    {
        try {
            $playerStat->delete();

            return redirect()->route('admin.player-stats.index')
                ->with('success', 'Sport stat configuration deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Error deleting sport stat configuration: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to delete sport stat configuration. Please try again.']);
        }
    }

    /**
     * Get players for a specific team.
     */
    public function getTeamPlayers(Team $team)
    {
        $team->load(['players', 'club.sport']);
        $players = $team->players;
        $sport = $team->club->sport;
        
        // Get stats for this sport
        $stats = Stat::where('sport_id', $sport->id)->get();

        return response()->json([
            'players' => $players,
            'stats' => $stats,
            'sport' => $sport
        ]);
    }

    /**
     * Get stats for a specific sport.
     */
    public function getSportStats(Request $request)
    {
        $sportId = $request->sport_id;
        
        if (!$sportId) {
            return response()->json(['stats' => []]);
        }

        $stats = Stat::where('sport_id', $sportId)->get();

        return response()->json(['stats' => $stats]);
    }

    /**
     * Bulk delete player stats.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'stat_ids' => 'required|array|min:1',
            'stat_ids.*' => 'exists:player_game_stats,id',
        ]);

        try {
            PlayerGameStat::whereIn('id', $request->stat_ids)->delete();

            return redirect()->route('admin.player-stats.index')
                ->with('success', 'Selected player stats deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Error bulk deleting player stats: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to delete selected player stats. Please try again.']);
        }
    }
}
