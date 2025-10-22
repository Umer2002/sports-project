<?php

namespace App\Http\Controllers;

use App\Models\PlayerAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlayerAvailabilityController extends Controller
{
    public function index()
    {
        $player = Auth::user()->player;
        $availabilities = $player?->availabilities()->get() ?? [];
        return response()->json($availabilities);
    }

    public function store(Request $request)
    {
        $player = Auth::user()->player;
        $request->validate([
            'day' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        return $player->availabilities()->create($request->only('day','start_time','end_time'));
    }

    public function update(Request $request, PlayerAvailability $availability)
    {
        $player = Auth::user()->player;
        abort_if($availability->player_id !== $player->id, 403);

        $request->validate([
            'day' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $availability->update($request->only('day','start_time','end_time'));

        return response()->json(['success' => true]);
    }

    public function destroy(PlayerAvailability $availability)
    {
        $player = Auth::user()->player;
        abort_if($availability->player_id !== $player->id, 403);
        $availability->delete();
        return response()->json(['success' => true]);
    }
}
