<?php

namespace App\Http\Controllers;

use App\Models\PlayerTransfer;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerTransferController extends Controller
{
    public function request(Request $request)
    {
        $player = Auth::user()->player;

        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $request->validate([
            'to_club_id' => 'required|exists:clubs,id',
            'to_sport_id' => 'required|exists:sports,id',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if there's already a pending transfer for this player
        $existingTransfer = PlayerTransfer::where('player_id', $player->id)
            ->where('status', 'pending')
            ->first();

        if ($existingTransfer) {
            return response()->json(['error' => 'You already have a pending transfer request'], 400);
        }

        $transfer = PlayerTransfer::create([
            'player_id' => $player->id,
            'from_club_id' => $player->club_id,
            'to_club_id' => $request->to_club_id,
            'from_sport_id' => $player->sport_id,
            'to_sport_id' => $request->to_sport_id,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        return response()->json([
            'message' => 'Transfer request submitted successfully',
            'transfer' => $transfer->load(['toClub', 'toSport', 'fromClub', 'fromSport'])
        ], 201);
    }

    public function approve(Request $request, PlayerTransfer $transfer)
    {
        $request->validate([
            'team_id' => 'nullable|exists:teams,id',
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if the current user is authorized to approve this transfer
        $user = Auth::user();
        if (!$user->hasRole('club') || $user->club->id !== $transfer->to_club_id) {
            return response()->json(['error' => 'Unauthorized to approve this transfer'], 403);
        }

        $transfer->update([
            'status' => 'approved',
            'approved_at' => now(),
            'notes' => $request->notes
        ]);

        $player = $transfer->player;

        // Preserve player data for the new sport
        $player->club_id = $transfer->to_club_id;
        $player->sport_id = $transfer->to_sport_id;
        $player->save();

        if ($request->team_id) {
            $player->team_id = $request->team_id;
            $player->save();
        }

        return response()->json([
            'message' => 'Transfer approved successfully',
            'transfer' => $transfer->load(['player', 'toClub', 'toSport'])
        ]);
    }

    public function reject(Request $request, PlayerTransfer $transfer)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        // Check if the current user is authorized to reject this transfer
        $user = Auth::user();
        if (!$user->hasRole('club') || $user->club->id !== $transfer->to_club_id) {
            return response()->json(['error' => 'Unauthorized to reject this transfer'], 403);
        }

        $transfer->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'notes' => $request->notes
        ]);

        return response()->json([
            'message' => 'Transfer rejected',
            'transfer' => $transfer->load(['player', 'toClub', 'toSport'])
        ]);
    }

    public function index()
    {
        $user = Auth::user();
        $transfers = [];

        if ($user->hasRole('player')) {
            $transfers = PlayerTransfer::where('player_id', $user->player->id)
                ->with(['fromClub', 'toClub', 'fromSport', 'toSport'])
                ->latest()
                ->get();
        } elseif ($user->hasRole('club')) {
            $transfers = PlayerTransfer::where('to_club_id', $user->club->id)
                ->where('status', 'pending')
                ->with(['player', 'fromClub', 'fromSport', 'toSport'])
                ->latest()
                ->get();
        }

        return response()->json($transfers);
    }
}
