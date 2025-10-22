<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AwardController extends Controller
{
    /**
     * Display a listing of awards assigned by the club.
     */
    public function index()
    {
        $club = auth()->user()->club;
        
        if (!$club) {
            return redirect()->route('club.dashboard')->with('error', 'Club not found.');
        }

        // Get awards assigned by this club
        $awardsAssigned = DB::table('player_award')
            ->join('players', 'player_award.player_id', '=', 'players.id')
            ->join('users', 'players.user_id', '=', 'users.id')
            ->join('rewards', 'player_award.award_id', '=', 'rewards.id')
            ->join('users as assigned_by_user', 'player_award.assigned_by', '=', 'assigned_by_user.id')
            ->where('player_award.assigned_by', auth()->id())
            ->select(
                'player_award.*',
                'users.name',
                'rewards.name as award_name',
                'rewards.type as award_type',
                'assigned_by_user.name as assigned_by_name'
            )
            ->orderBy('player_award.created_at', 'desc')
            ->paginate(20);

        return view('club.awards.index', compact('awardsAssigned'));
    }

    /**
     * Show the form for creating a new award assignment.
     */
    public function create()
    {
        $club = auth()->user()->club;
        
        if (!$club) {
            return redirect()->route('club.dashboard')->with('error', 'Club not found.');
        }

        // Get available rewards
        $rewards = Reward::all();
        
        // Get club players
        $clubPlayers = $club->players()->with(['user', 'teams', 'position'])->get();

        return view('club.awards.create', compact('rewards', 'clubPlayers'));
    }

    /**
     * Store a newly assigned award.
     */
    public function store(Request $request)
    {
        \Log::info('=== CLUB AWARD STORE METHOD CALLED ===');
        $club = auth()->user()->club;
        
        // Debug: Log the request data
        \Log::info('Club Award Store Request:', $request->all());
        
        // Check if player_ids are present
        if (!$request->has('player_ids') || empty($request->player_ids)) {
            \Log::info('No players selected in award assignment');
            return back()->withErrors(['player_ids' => 'Please select at least one player to assign the award to.']);
        }
        
        \Log::info('Player IDs found, proceeding with validation');

        $request->validate([
            'award_id' => 'required|exists:rewards,id',
            'player_ids' => 'required|array|min:1',
            'player_ids.*' => 'exists:players,id',
            'visibility' => 'required|in:public,team,private',
            'coach_note' => 'nullable|string|max:1000',
            'notify_player' => 'boolean',
            'post_to_feed' => 'boolean',
            'add_to_profile' => 'boolean',
            'award_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'use_presaved_image' => 'boolean',
        ]);
        
        \Log::info('Validation passed, proceeding with processing');

        if (!$club) {
            \Log::info('Club not found for user');
            return redirect()->route('club.dashboard')->with('error', 'Club not found.');
        }
        
        \Log::info('Club found, proceeding with award processing');

        \Log::info('Looking for reward with ID:', ['award_id' => $request->award_id]);
        $reward = Reward::findOrFail($request->award_id);
        \Log::info('Reward found:', ['reward_id' => $reward->id, 'reward_name' => $reward->name]);
        $playerIds = $request->player_ids;
        $visibility = $request->visibility;
        $coachNote = $request->coach_note;
        $notifyPlayer = $request->boolean('notify_player', true);
        $postToFeed = $request->boolean('post_to_feed', false);
        $addToProfile = $request->boolean('add_to_profile', true);
        $usePresavedImage = $request->boolean('use_presaved_image', false);
        
        // Handle image logic
        $awardImagePath = null;
        if ($usePresavedImage && $reward->image) {
            // Use presaved reward image
            $awardImagePath = $reward->image;
        } elseif ($request->hasFile('award_image')) {
            // Upload custom image
            $awardImagePath = $request->file('award_image')->store('award-assignments', 'public');
        }

        // Check if club has access to these players
        \Log::info('Checking player access for club');
        
        // First check if players belong to the club directly
        $validPlayerIds = $club->players()->whereIn('id', $playerIds)->pluck('id');
        \Log::info('Club players found:', ['valid_player_ids' => $validPlayerIds->toArray(), 'requested_player_ids' => $playerIds]);

        if ($validPlayerIds->count() !== count($playerIds)) {
            \Log::info('Player access validation failed - players do not belong to club');
            return back()->withErrors(['player_ids' => 'Some selected players are not accessible.']);
        }
        
        \Log::info('Player access validation passed - players belong to club');

        try {
            DB::beginTransaction();

            \Log::info('Processing awards for players:', $playerIds);

            foreach ($playerIds as $playerId) {
                // Check if player already has this award
                $existingAward = DB::table('player_award')
                    ->where('player_id', $playerId)
                    ->where('award_id', $reward->id)
                    ->exists();

                \Log::info("Player {$playerId} existing award check:", ['exists' => $existingAward]);

                if (!$existingAward) {
                    $insertData = [
                        'player_id' => $playerId,
                        'award_id' => $reward->id,
                        'assigned_by' => auth()->id(),
                        'awarded_at' => now(),
                        'visibility' => $visibility,
                        'coach_note' => $coachNote,
                        'notify_player' => $notifyPlayer,
                        'post_to_feed' => $postToFeed,
                        'add_to_profile' => $addToProfile,
                        'award_image' => $awardImagePath,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    
                    \Log::info("Inserting award data for player {$playerId}:", $insertData);
                    
                    DB::table('player_award')->insert($insertData);
                    \Log::info("Successfully inserted award for player {$playerId}");
                } else {
                    \Log::info("Player {$playerId} already has this award, skipping");
                }
            }

            DB::commit();
            \Log::info('Award assignment completed successfully');

            return redirect()->route('club.dashboard')
                ->with('success', 'Awards assigned successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Award assignment failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return back()->withErrors(['error' => 'Failed to assign awards. Please try again.']);
        }
    }

    /**
     * Display the specified award assignment.
     */
    public function show($id)
    {
        $club = auth()->user()->club;
        
        if (!$club) {
            return redirect()->route('club.dashboard')->with('error', 'Club not found.');
        }

        $award = DB::table('player_award')
            ->join('players', 'player_award.player_id', '=', 'players.id')
            ->join('users', 'players.user_id', '=', 'users.id')
            ->join('rewards', 'player_award.award_id', '=', 'rewards.id')
            ->join('users as assigned_by_user', 'player_award.assigned_by', '=', 'assigned_by_user.id')
            ->where('player_award.id', $id)
            ->where('player_award.assigned_by', auth()->id())
            ->select(
                'player_award.*',
                'users.name',
                'rewards.name as award_name',
                'rewards.type as award_type',
                'assigned_by_user.name as assigned_by_first_name',
            )
            ->first();

        if (!$award) {
            return redirect()->route('club.awards.index')->with('error', 'Award assignment not found.');
        }

        return view('club.awards.show', compact('award'));
    }

    /**
     * Remove the specified award assignment.
     */
    public function destroy($id)
    {
        $club = auth()->user()->club;
        
        if (!$club) {
            return redirect()->route('club.dashboard')->with('error', 'Club not found.');
        }

        $award = DB::table('player_award')
            ->where('id', $id)
            ->where('assigned_by', auth()->id())
            ->first();

        if (!$award) {
            return redirect()->route('club.awards.index')->with('error', 'Award assignment not found.');
        }

        // Delete award image if exists
        if ($award->award_image && Storage::disk('public')->exists($award->award_image)) {
            Storage::disk('public')->delete($award->award_image);
        }

        DB::table('player_award')->where('id', $id)->delete();

        return redirect()->route('club.awards.index')->with('success', 'Award assignment removed successfully.');
    }

    /**
     * Get award details for AJAX requests.
     */
    public function getAwardDetails($id)
    {
        try {
            $award = Reward::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'award' => [
                    'id' => $award->id,
                    'name' => $award->name,
                    'description' => $award->achievement,
                    'requirements' => 'Complete profile • Join first club', // Default requirements
                    'rewards' => '+250 XP • Frame • Feed highlight', // Default rewards
                    'type' => $award->type,
                    'image' => $award->image ? asset('images/' . $award->image) : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch award details'
            ], 500);
        }
    }

    /**
     * Get club players for AJAX requests.
     */
    public function getPlayers()
    {
        $club = auth()->user()->club;
        
        if (!$club) {
            return response()->json(['players' => []]);
        }

        $players = $club->players()
            ->with(['user', 'teams', 'position'])
            ->get()
            ->map(function($player) {
                return [
                    'id' => $player->id,
                    'name' => $player->name,
                    'team' => $player->teams->first()->name ?? 'Unassigned',
                    'position' => $player->position->name ?? null,
                ];
            });

        return response()->json(['players' => $players]);
    }

    /**
     * Show the award log for the club
     */
    public function log()
    {
        $club = auth()->user()->club;
        
        if (!$club) {
            return redirect()->route('club.dashboard')->with('error', 'Club not found.');
        }

        // Get all award assignments for this club's players
        $awardAssignments = DB::table('player_award')
            ->join('players', 'player_award.player_id', '=', 'players.id')
            ->join('rewards', 'player_award.award_id', '=', 'rewards.id')
            ->join('users as assigned_by_user', 'player_award.assigned_by', '=', 'assigned_by_user.id')
            ->join('team_players', 'players.id', '=', 'team_players.player_id')
            ->join('teams', 'team_players.team_id', '=', 'teams.id')
            ->where('teams.club_id', $club->id)
            ->select([
                'player_award.*',
                'players.name as player_name',
                'rewards.name as award_name',
                'rewards.type as award_type',
                'rewards.image as award_image',
                'assigned_by_user.name as assigned_by_name',
                'teams.name as team_name'
            ])
            ->orderBy('player_award.awarded_at', 'desc')
            ->paginate(20);

        return view('club.awards.log', compact('awardAssignments'));
    }
}
