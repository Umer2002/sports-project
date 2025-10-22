<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Sport;
use App\Models\Player;
use App\Models\PlayerTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function index()
    {
        $player = $this->authenticatedPlayer();
        $transfers = PlayerTransfer::where('player_id', $player->id)
            ->with(['fromClub', 'toClub', 'fromSport', 'toSport'])
            ->latest()
            ->get();

        $clubs = Club::all();
        $sports = Sport::all();

        return view('players.transfers.index', compact('transfers', 'clubs', 'sports', 'player'));
    }

    public function create()
    {
        $player = $this->authenticatedPlayer();
        
        // Check if there's already a pending transfer
        $existingTransfer = PlayerTransfer::where('player_id', $player->id)
            ->where('status', 'pending')
            ->first();
            
        if ($existingTransfer) {
            return redirect()->route('player.transfers.index')
                ->with('error', 'You already have a pending transfer request. Please wait for it to be processed before submitting a new one.');
        }
        
        $clubs = Club::all();
        $sports = Sport::all();

        return view('players.transfers.create', compact('clubs', 'sports', 'player'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'to_club_id' => 'required|exists:clubs,id',
            'to_sport_id' => 'required|exists:sports,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $player = $this->authenticatedPlayer();

        // Check if there's already a pending transfer
        $existingTransfer = PlayerTransfer::where('player_id', $player->id)
            ->where('status', 'pending')
            ->first();

        if ($existingTransfer) {
            return back()->with('error', 'You already have a pending transfer request. Please wait for it to be processed before submitting a new one.');
        }

        // Additional validation: Check if player is trying to transfer to the same club
        if ($player->club_id == $request->to_club_id) {
            return back()->with('error', 'You cannot request a transfer to the same club you are currently in.');
        }

        // Additional validation: Check if player is trying to transfer to the same sport
        if ($player->sport_id == $request->to_sport_id) {
            return back()->with('error', 'You cannot request a transfer to the same sport you are currently playing.');
        }

        PlayerTransfer::create([
            'player_id' => $player->id,
            'from_club_id' => $player->club_id,
            'to_club_id' => $request->to_club_id,
            'from_sport_id' => $player->sport_id,
            'to_sport_id' => $request->to_sport_id,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        return redirect()->route('player.transfers.index')
            ->with('success', 'Transfer request submitted successfully. Waiting for club approval.');
    }

    public function show(PlayerTransfer $transfer)
    {
        // Ensure the player can only view their own transfers
        $player = $this->authenticatedPlayer();

        if ($transfer->player_id !== $player->id) {
            abort(403);
        }

        return view('players.transfers.show', compact('transfer', 'player'));
    }

    public function cancel(PlayerTransfer $transfer)
    {
        // Ensure the player can only cancel their own pending transfers
        $player = $this->authenticatedPlayer();

        if ($transfer->player_id !== $player->id || $transfer->status !== 'pending') {
            abort(403);
        }

        $transfer->update(['status' => 'cancelled']);

        return redirect()->route('player.transfers.index')
            ->with('success', 'Transfer request cancelled successfully.');
    }

    public function destroy(PlayerTransfer $transfer)
    {
        // Ensure the player can only delete their own transfers
        $player = $this->authenticatedPlayer();

        if ($transfer->player_id !== $player->id) {
            abort(403);
        }

        // Only allow deletion of pending or cancelled transfers
        if (!in_array($transfer->status, ['pending', 'cancelled'])) {
            return redirect()->route('player.transfers.index')
                ->with('error', 'Cannot delete approved or rejected transfers.');
        }

        $transfer->delete();

        return redirect()->route('player.transfers.index')
            ->with('success', 'Transfer request deleted successfully.');
    }

    protected function authenticatedPlayer(): Player
    {
        $player = Auth::user()->player;

        if (! $player) {
            abort(403, 'Player profile not found.');
        }

        return $player;
    }
}
