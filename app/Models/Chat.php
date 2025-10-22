<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'title',
        'type',
        'team_id',
        'tournament_id',
    ];

    /**
     * Get the messages that belong to this chat.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Participants of the chat.
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'chat_participants', 'chat_id', 'user_id');
    }

    /**
     * Team this chat belongs to (for team chats).
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Tournament this chat belongs to (for tournament chats).
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}
