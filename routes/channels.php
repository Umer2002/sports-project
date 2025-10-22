<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // Authorize if the authenticated user participates in the chat
    return \App\Models\Chat::where('id', $chatId)
        ->whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->exists() ? ['id' => $user->id, 'name' => $user->name] : false;
});

