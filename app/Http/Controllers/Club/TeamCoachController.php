<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\{Team, Coach};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamCoachController extends Controller
{
    private function authorizeClubTeam(Team $team)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('club') || !$user->club || $team->club_id !== $user->club->id) {
            abort(403, 'Unauthorized');
        }
    }

    public function attach(Request $request, Team $team)
    {
        $this->authorizeClubTeam($team);
        $data = $request->validate(['coach_id' => 'required|exists:coaches,id']);
        $coach = Coach::findOrFail($data['coach_id']);

        // Optional: ensure coach belongs to the same club via user.club_id
        if ($coach->user && $coach->user->club_id !== Auth::user()->club->id) {
            return response()->json(['message' => 'Coach does not belong to your club'], 422);
        }

        $team->coaches()->syncWithoutDetaching([$coach->id]);
        return response()->json(['message' => 'Coach assigned to team', 'team_id' => $team->id, 'coach_id' => $coach->id]);
    }

    public function detach(Team $team, Coach $coach)
    {
        $this->authorizeClubTeam($team);
        $team->coaches()->detach($coach->id);
        return response()->json(['message' => 'Coach removed from team']);
    }
}

