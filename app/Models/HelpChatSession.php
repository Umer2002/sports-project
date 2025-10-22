<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HelpChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'player_id',
        'role',
        'role_label',
        'intent',
        'intent_label',
        'intent_group',
        'stage',
        'status',
        'last_interaction_at',
    ];

    protected $dates = [
        'last_interaction_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(HelpChatMessage::class, 'session_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(HelpChatTicket::class, 'session_id');
    }
}
