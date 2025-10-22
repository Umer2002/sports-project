<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Division;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Position;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with('club')->latest()->paginate(10);
        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        $clubs = Club::pluck('name', 'id');
        $sports = \App\Models\Sport::pluck('name', 'id');
        $teams = Team::pluck('name', 'id');

        return view('admin.teams.create', compact('clubs', 'sports', 'teams'));
    }



    public function store(Request $request)
    {
        $data = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'sport_id' => 'nullable|exists:sports,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'division_id' => 'required|exists:divisions,id',
        ]);

        // Force sport_id from selected club to prevent tampering
        $club = Club::findOrFail($data['club_id']);
        $data['sport_id'] = $club->sport_id;

        $division = Division::findOrFail($data['division_id']);
        if ($division->sport_id !== $data['sport_id']) {
            return back()->withErrors(['division_id' => 'Selected division does not match the club sport.'])->withInput();
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('team_logos', 'public');
        }

        Team::create($data);

        return redirect()->route('admin.teams.index')->with('success', 'Team created successfully!');
    }

    public function edit(Team $team)
    {
        $clubs = Club::pluck('name', 'id');
        $sports = \App\Models\Sport::pluck('name', 'id');
        
        // Debug: Log the variables
        \Log::info('Admin TeamController edit method called', [
            'team_id' => $team->id,
            'clubs_count' => $clubs->count(),
            'sports_count' => $sports->count()
        ]);
        
        return view('admin.teams.edit', compact('team', 'clubs', 'sports'));
    }

    public function update(Request $request, Team $team)
    {
        $data = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'sport_id' => 'nullable|exists:sports,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'division_id' => 'required|exists:divisions,id',
        ]);

        // Force sport_id from selected club to prevent tampering
        $club = Club::findOrFail($data['club_id']);
        $data['sport_id'] = $club->sport_id;

        $division = Division::findOrFail($data['division_id']);
        if ($division->sport_id !== $data['sport_id']) {
            return back()->withErrors(['division_id' => 'Selected division does not match the club sport.'])->withInput();
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('team_logos', 'public');
        }

        $team->update($data);

        return redirect()->route('admin.teams.index')->with('success', 'Team updated successfully!');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('admin.teams.index')->with('success', 'Team deleted successfully!');
    }

    public function show(Team $team)
    {
        $team->load(['club', 'sport', 'players.position']);
        
        // Query 1: Eloquent relationship (existing)
        $teamPlayers = $team->players()->with('position')->get();
        
        // Query 2: Direct database query
        $playersWithPositions = DB::table('player_team')
            ->join('players', 'player_team.player_id', '=', 'players.id')
            ->leftJoin('positions', 'player_team.position_id', '=', 'positions.id')
            ->where('player_team.team_id', $team->id)
            ->select(
                'players.id as player_id',
                'players.name as player_name',
                'players.email as player_email',
                'positions.id as position_id',
                'positions.position_name',
                'player_team.created_at as joined_at',
                'player_team.updated_at'
            )
            ->get();
        // // Query 3: Players grouped by position
        // $playersByPosition = DB::table('player_team')
        //     ->join('players', 'player_team.player_id', '=', 'players.id')
        //     ->leftJoin('positions', 'player_team.position_id', '=', 'positions.id')
        //     ->where('player_team.team_id', $team->id)
        //     ->select(
        //         'positions.position_name',
        //         'positions.id as position_id',
        //         DB::raw('COUNT(players.id) as player_count'),
        //         DB::raw('GROUP_CONCAT(players.name) as player_names')
        //     )
        //     ->groupBy('positions.id', 'positions.position_name')
        //     ->get();
        
        // // Query 4: Players without positions
        // $playersWithoutPosition = DB::table('player_team')
        //     ->join('players', 'player_team.player_id', '=', 'players.id')
        //     ->where('player_team.team_id', $team->id)
        //     ->whereNull('player_team.position_id')
        //     ->select(
        //         'players.id as player_id',
        //         'players.name as player_name',
        //         'players.email as player_email',
        //         'player_team.created_at as joined_at'
        //     )
        //     ->get();
              
        // Get available players (not already in this team)
        $availablePlayers = Player::whereDoesntHave('teams', function($query) use ($team) {
            $query->where('team_id', $team->id);
        })->get();
        
        // Get positions for this team's sport
        $positions = Position::where('sports_id', $team->sport_id)->get();
        
        return view('admin.teams.show', compact('team', 'teamPlayers', 'availablePlayers', 'positions', 'playersWithPositions'));
    }

    public function addPlayer(Request $request, Team $team)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id',
            'position_id' => 'nullable|exists:positions,id'
        ]);

        // Check if player is already in the team
        if ($team->players()->where('player_id', $request->player_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Player is already in this team'
            ]);
        }

        // Add player to team
        $team->players()->attach($request->player_id, [
            'position_id' => $request->position_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Player added to team successfully'
        ]);
    }

    public function removePlayer(Request $request, Team $team)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id'
        ]);

        // Remove player from team
        $team->players()->detach($request->player_id);

        return response()->json([
            'success' => true,
            'message' => 'Player removed from team successfully'
        ]);
    }

    public function updatePlayerPosition(Request $request, Team $team)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id',
            'position_id' => 'nullable|exists:positions,id'
        ]);

        // Update player position
        $team->players()->updateExistingPivot($request->player_id, [
            'position_id' => $request->position_id,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Player position updated successfully'
        ]);
    }
    /**
     * Get a specific player's position in a team
     */
    public function getPlayerPosition(Request $request, Team $team)
    {
        $request->validate([
            'player_id' => 'required|exists:players,id'
        ]);

        $playerPosition = $team->players()
            ->where('player_id', $request->player_id)
            ->with('position')
            ->first();

        if (!$playerPosition) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found in this team'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'player' => $playerPosition,
                'position' => $playerPosition->position,
                'joined_at' => $playerPosition->pivot->created_at
            ]
        ]);
    }

    /**
     * Get all players with their positions for a specific team
     */
    public function getTeamPlayersWithPositions(Team $team)
    {
        $playersWithPositions = $team->players()
            ->with('position')
            ->get()
            ->map(function ($player) {
                return [
                    'player_id' => $player->id,
                    'player_name' => $player->name,
                    'position_id' => $player->pivot->position_id,
                    'position_name' => $player->position->position_name ?? 'No Position',
                    'joined_at' => $player->pivot->created_at,
                    'email' => $player->email
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $playersWithPositions
        ]);
    }

    public function addCoach(Request $request, Team $team)
    {
        $request->validate([
            'coach_id' => 'required|exists:coaches,id'
        ]);

        // Check if coach is already assigned to this team
        if ($team->coaches()->where('coach_id', $request->coach_id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Coach is already assigned to this team.']);
        }

        $team->coaches()->attach($request->coach_id);

        return response()->json(['success' => true, 'message' => 'Coach added to team successfully!']);
    }

    public function removeCoach(Request $request, Team $team)
    {
        $request->validate([
            'coach_id' => 'required|exists:coaches,id'
        ]);

        $team->coaches()->detach($request->coach_id);

        return response()->json(['success' => true, 'message' => 'Coach removed from team successfully!']);
    }
}
