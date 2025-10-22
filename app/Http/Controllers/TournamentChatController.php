<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatParticipant;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TournamentChatController extends Controller
{
    /**
     * Join (or create) a tournament chat room and return the chat details.
     */
    public function join(Request $request, Tournament $tournament): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($tournament->end_date && $tournament->end_date->isPast()) {
            return response()->json(['error' => 'Tournament chat room has closed.'], 422);
        }

        if (!$user->hasAnyRole(['club', 'coach', 'player', 'admin'])) {
            return response()->json(['error' => 'Access denied for this chat room.'], 403);
        }

        $chat = Chat::firstOrCreate(
            [
                'type' => 'tournament',
                'tournament_id' => $tournament->id,
            ],
            [
                'title' => $tournament->name ?? 'Tournament Chat',
            ]
        );

        // Keep the chat title in sync if the tournament name has been updated.
        if ($chat->wasRecentlyCreated === false && $tournament->name && $chat->title !== $tournament->name) {
            $chat->update(['title' => $tournament->name]);
        }

        ChatParticipant::firstOrCreate([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'chat_id' => $chat->id,
            'redirect_url' => route('player.chat', ['chat_id' => $chat->id]),
        ]);
    }
}
