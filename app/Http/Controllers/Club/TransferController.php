<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\PlayerTransfer;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function index()
    {
        $club = Auth::user()->club;

        $pendingTransfers = PlayerTransfer::where('to_club_id', $club->id)
            ->where('status', 'pending')
            ->with(['player', 'fromClub', 'fromSport', 'toSport'])
            ->latest()
            ->get();

        $recentTransfers = PlayerTransfer::where('to_club_id', $club->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->with(['player', 'fromClub', 'fromSport', 'toSport'])
            ->latest()
            ->take(10)
            ->get();

        return view('club.transfers.index', compact('pendingTransfers', 'recentTransfers'));
    }

    public function show(PlayerTransfer $transfer)
    {
        $club = Auth::user()->club;

        // Ensure the club can only view transfers to their club
        if ($transfer->to_club_id !== $club->id) {
            abort(403);
        }

        $teams = Team::where('club_id', $club->id)->get();

        return view('club.transfers.show', compact('transfer', 'teams'));
    }

    public function approve(Request $request, PlayerTransfer $transfer)
    {
        $request->validate([
            'team_id' => 'nullable|exists:teams,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $club = Auth::user()->club;

        // Ensure the club can only approve transfers to their club
        if ($transfer->to_club_id !== $club->id) {
            abort(403);
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

        return redirect()->route('club.transfers.index')
            ->with('success', 'Transfer approved successfully.');
    }

    public function reject(Request $request, PlayerTransfer $transfer)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $club = Auth::user()->club;

        // Ensure the club can only reject transfers to their club
        if ($transfer->to_club_id !== $club->id) {
            abort(403);
        }

        $transfer->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'notes' => $request->notes
        ]);

        return redirect()->route('club.transfers.index')
            ->with('success', 'Transfer rejected.');
    }
    public function cancel(Request $request, PlayerTransfer $transfer)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        $club = Auth::user()->club;

        // Ensure the club can only reject transfers to their club
        if ($transfer->to_club_id !== $club->id) {
            abort(403);
        }

        $transfer->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'notes' => $request->notes
        ]);

        return redirect()->route('club.transfers.index')
            ->with('success', 'Transfer rejected.');
    }
}
