<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AwardController extends Controller
{
    /**
     * Display a listing of awards
     */
    public function index()
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        $rewards = Reward::all();
        
        return view('coach.awards.index', compact('rewards'));
    }

    /**
     * Show the form for assigning awards to players
     */
    public function create()
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Get all players from coach's teams
        $teamIds = $coach->teams()->pluck('teams.id');
        $players = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->with(['user', 'teams'])->get();

        $rewards = Reward::all();

        return view('coach.awards.assign', compact('players', 'rewards'));
    }

    /**
     * Store the assigned awards
     */
    public function store(Request $request)
    {
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

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        $reward = Reward::findOrFail($request->award_id);
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

        // Check if coach has access to these players
        $teamIds = $coach->teams()->pluck('teams.id');
        $validPlayerIds = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->whereIn('id', $playerIds)->pluck('id');

        if ($validPlayerIds->count() !== count($playerIds)) {
            return back()->withErrors(['player_ids' => 'Some selected players are not accessible.']);
        }

        try {
            DB::beginTransaction();

            foreach ($playerIds as $playerId) {
                // Check if player already has this award
                $existingAward = DB::table('player_award')
                    ->where('player_id', $playerId)
                    ->where('award_id', $reward->id)
                    ->exists();

                if (!$existingAward) {
                    DB::table('player_award')->insert([
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
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('coach.awards.index')
                ->with('success', 'Awards assigned successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to assign awards. Please try again.']);
        }
    }

    /**
     * Get players for a specific team (AJAX)
     */
    public function getPlayers(Request $request)
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return response()->json(['error' => 'Coach not found'], 404);
        }

        $teamId = $request->get('team_id');
        if (!$teamId) {
            return response()->json(['players' => []]);
        }

        // Verify coach has access to this team
        $teamIds = $coach->teams()->pluck('teams.id');
        if (!$teamIds->contains($teamId)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $players = Player::whereHas('teams', function($query) use ($teamId) {
            $query->where('teams.id', $teamId);
        })->with(['user'])->get(['id', 'user_id', 'name']);

        return response()->json(['players' => $players]);
    }

    /**
     * Get award details for AJAX request
     */
    public function getAwardDetails(Reward $award)
    {
        try {
            return response()->json([
                'success' => true,
                'award' => [
                    'id' => $award->id,
                    'name' => $award->name,
                    'description' => $award->achievement,
                    'requirements' => $award->type,
                    'rewards' => $award->achievement,
                    'color' => '#007bff', // Default color since rewards table doesn't have color
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
     * Show award assignment log
     */
    public function log()
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        $assignments = DB::table('player_award')
            ->join('players', 'player_award.player_id', '=', 'players.id')
            ->join('awards', 'player_award.award_id', '=', 'awards.id')
            ->join('users', 'player_award.assigned_by', '=', 'users.id')
            ->where('player_award.assigned_by', auth()->id())
            ->select([
                'player_award.*',
                'players.name as player_name',
                'awards.name as award_name',
                'awards.color as award_color',
                'users.name as assigned_by_name'
            ])
            ->orderBy('player_award.awarded_at', 'desc')
            ->paginate(20);

        return view('coach.awards.log', compact('assignments'));
    }
}