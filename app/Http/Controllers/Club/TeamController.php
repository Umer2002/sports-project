<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Concerns\ResolvesUserClub;
use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Player;
use App\Models\Position;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    use ResolvesUserClub;

    public function index()
    {
        $club = $this->resolveClubForUser();

        if (! $club) {
            if (auth()->user()->hasRole('club')) {
                return redirect()->route('club.setup')->with('error', 'Club not found. Please complete setup first.');
            }

            abort(403, 'Club association required to manage teams.');
        }

        $teams = Team::where('club_id', $club->id)->with('players')->paginate(10);

        return view('club.teams.index', compact('teams'));
    }

    public function show(Team $team)
    {
        // Ensure team belongs to current club
        $club = $this->authorizeClubTeam($team);
        
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
              
        // Get available players (not already in this team)
        $availablePlayers = Player::where('club_id', $club->id)
            ->whereDoesntHave('teams', function ($query) use ($team) {
                $query->where('team_id', $team->id);
            })
            ->orderBy('name')
            ->get();
        
        // Get positions for this team's sport
        $positions = Position::where('sports_id', $team->sport_id)->get();
        
        return view('club.teams.show', compact('team', 'teamPlayers', 'availablePlayers', 'positions', 'playersWithPositions'));
    }

    public function edit(Team $team)
    {
        // Ensure team belongs to current club
        $this->authorizeClubTeam($team);
        return view('admin.teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        // Ensure team belongs to current club
        $this->authorizeClubTeam($team);

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'division_id' => 'required|exists:divisions,id',
        ]);

        $division = Division::findOrFail($data['division_id']);
        if ($division->sport_id !== $team->sport_id) {
            return back()->withErrors(['division_id' => 'Selected division does not match your club sport.'])->withInput();
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('teams/logos', 'public');
        }

        $team->update($data);

        return redirect()->route('club.teams.index')->with('success', 'Team updated successfully!');
    }

    public function destroy(Team $team)
    {
        // Ensure team belongs to current club
        $this->authorizeClubTeam($team);

        $team->delete();

        return redirect()->route('club.teams.index')->with('success', 'Team deleted successfully!');
    }

    public function addPlayer(Request $request, Team $team)
    {
        // Ensure team belongs to current club
        $club = $this->authorizeClubTeam($team);

        $request->validate([
            'player_id' => 'required|exists:players,id',
            'position_id' => 'nullable|exists:positions,id'
        ]);

        $player = Player::where('club_id', $club->id)->find($request->player_id);

        if (! $player) {
            return response()->json([
                'success' => false,
                'message' => 'You can only add players registered with your club.'
            ], 422);
        }

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
        // Ensure team belongs to current club
        $club = $this->authorizeClubTeam($team);

        $request->validate([
            'player_id' => 'required|exists:players,id'
        ]);

        $player = Player::where('club_id', $club->id)->find($request->player_id);

        if (! $player) {
            return response()->json([
                'success' => false,
                'message' => 'You can only remove players registered with your club.'
            ], 422);
        }

        // Remove player from team
        $team->players()->detach($request->player_id);

        return response()->json([
            'success' => true,
            'message' => 'Player removed from team successfully'
        ]);
    }

    public function updatePlayerPosition(Request $request, Team $team)
    {
        // Ensure team belongs to current club
        $club = $this->authorizeClubTeam($team);

        $request->validate([
            'player_id' => 'required|exists:players,id',
            'position_id' => 'nullable|exists:positions,id'
        ]);

        $player = Player::where('club_id', $club->id)->find($request->player_id);

        if (! $player) {
            return response()->json([
                'success' => false,
                'message' => 'You can only modify players registered with your club.'
            ], 422);
        }

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

    private function authorizeClubTeam(Team $team)
    {
        $club = $this->resolveClubForUser();

        if (! $club) {
            abort(403, 'Club association required to manage teams.');
        }

        if ($team->club_id !== $club->id) {
            abort(403, 'Unauthorized access to this team.');
        }

        return $club;
    }

    public function getPlayers(Team $team)
    {
        // Ensure team belongs to current club
        $club = $this->authorizeClubTeam($team);

        try {
            $players = $team->players()->with('position')->get()->map(function ($player) {
                return [
                    'id' => $player->id,
                    'name' => $player->name,
                    'email' => $player->email,
                    'position' => $player->position ? $player->position->position_name : null,
                    'avatar' => $player->avatar ?? null
                ];
            });

            return response()->json([
                'success' => true,
                'players' => $players
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading team players: ' . $e->getMessage()
            ], 500);
        }
    }
}
