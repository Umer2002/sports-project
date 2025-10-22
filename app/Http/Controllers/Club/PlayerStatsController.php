<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Player;
use App\Models\PlayerGameStat;
use App\Models\PlayerStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayerStatsController extends Controller
{
    /**
     * Get team players with their sport's configured stats
     */
    public function getTeamPlayersWithStats(Team $team)
    {
        try {
            // Get team players
            $players = $team->players()->with(['position', 'sport'])->get();
            
            // Get sport's configured stats
            $sportStats = PlayerGameStat::where('sport_id', $team->sport_id)->first();
            
            if (!$sportStats) {
                return response()->json([
                    'success' => false,
                    'message' => 'No stats configured for this sport yet. Please configure stats first.',
                    'players' => $players,
                    'configured_stats' => null
                ]);
            }
            
            // Get existing player stats for these players
            $existingStats = PlayerStat::whereIn('player_id', $players->pluck('id'))
                ->get()
                ->keyBy('player_id');
            
            // Add existing stats to players
            $players->each(function ($player) use ($existingStats, $sportStats) {
                $playerStats = $existingStats->get($player->id);
                $player->existing_stats = $playerStats ? [
                    'stat1' => $playerStats->stat1_vlaue,
                    'stat2' => $playerStats->stat2_vlaue,
                    'stat3' => $playerStats->stat3_vlaue,
                    'stat4' => $playerStats->stat4_vlaue,
                ] : null;
            });
            
            return response()->json([
                'success' => true,
                'players' => $players,
                'configured_stats' => [
                    'stat1' => $sportStats->stat1,
                    'stat2' => $sportStats->stat2,
                    'stat3' => $sportStats->stat3,
                    'stat4' => $sportStats->stat4,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting team players with stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading team data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Save player stats
     */
    public function savePlayerStats(Request $request)
    {
        try {
        $request->validate([
            'player_stats' => 'required|array',
            'player_stats.*.player_id' => 'required|exists:players,id',
            'player_stats.*.stat1' => 'nullable|numeric|min:0|max:100',
            'player_stats.*.stat2' => 'nullable|numeric|min:0|max:100',
            'player_stats.*.stat3' => 'nullable|numeric|min:0|max:100',
            'player_stats.*.stat4' => 'nullable|numeric|min:0|max:100',
        ]);
        
        // Get the club's sport stats configuration
        $club = auth()->user()->club;
        if (!$club) {
            return response()->json([
                'success' => false,
                'message' => 'Club not found'
            ], 400);
        }
        
        $sportStats = PlayerGameStat::where('sport_id', $club->sport_id)->first();
        if (!$sportStats) {
            \Log::info('No sport stats found for club sport ID: ' . $club->sport_id);
            return response()->json([
                'success' => false,
                'message' => 'No stats configured for this sport (ID: ' . $club->sport_id . ')'
            ], 400);
        }
        
        \Log::info('Found sport stats for club sport ID: ' . $club->sport_id, [
            'stat1' => $sportStats->stat1,
            'stat2' => $sportStats->stat2,
            'stat3' => $sportStats->stat3,
            'stat4' => $sportStats->stat4
        ]);
            
            DB::beginTransaction();
            
            $savedCount = 0;
            
            foreach ($request->player_stats as $playerStatData) {
                if (empty(array_filter([
                    $playerStatData['stat1'] ?? null,
                    $playerStatData['stat2'] ?? null,
                    $playerStatData['stat3'] ?? null,
                    $playerStatData['stat4'] ?? null,
                ]))) {
                    continue; // Skip if no stats provided
                }
                
                PlayerStat::updateOrCreate(
                    ['player_id' => $playerStatData['player_id']],
                    [
                        // Stat names from sport configuration
                        'stat1' => $sportStats->stat1,
                        'stat2' => $sportStats->stat2,
                        'stat3' => $sportStats->stat3,
                        'stat4' => $sportStats->stat4,
                        // Stat values from form
                        'stat1_vlaue' => $playerStatData['stat1'] ?? null,
                        'stat2_vlaue' => $playerStatData['stat2'] ?? null,
                        'stat3_vlaue' => $playerStatData['stat3'] ?? null,
                        'stat4_vlaue' => $playerStatData['stat4'] ?? null,
                    ]
                );
                
                $savedCount++;
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully saved stats for {$savedCount} players",
                'saved_count' => $savedCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving player stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving player stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
