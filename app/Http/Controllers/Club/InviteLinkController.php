<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;

class InviteLinkController extends Controller
{
    public function accept(string $token)
    {
        $club = Club::where('invite_token', $token)->firstOrFail();
        $club->incrementInvites();

        return response()->json([
            'message' => 'Thank you for accepting the invitation.',
            'club' => $club->name,
            'total_invites' => $club->invites_count,
            'reward' => $club->calculateInviteReward(),
        ]);
    }
}
