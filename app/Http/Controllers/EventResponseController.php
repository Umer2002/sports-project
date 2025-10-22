<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventResponseController extends Controller
{
    public function respond(Request $request, Event $event)
    {
        $player = Auth::user()->player;
        $request->validate([
            'status' => 'required|in:yes,no,maybe'
        ]);

        EventResponse::updateOrCreate(
            ['event_id' => $event->id, 'player_id' => $player->id],
            ['status' => $request->status]
        );

        return response()->json(['success' => true]);
    }
}
