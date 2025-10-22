<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Award extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'requirements',
        'rewards',
        'color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the players who have been awarded this award
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'player_award')
                    ->withPivot([
                        'assigned_by',
                        'awarded_at',
                        'visibility',
                        'coach_note',
                        'notify_player',
                        'post_to_feed',
                        'add_to_profile'
                    ])
                    ->withTimestamps();
    }

    /**
     * Get the coaches who have assigned this award
     */
    public function assignedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'player_award', 'award_id', 'assigned_by')
                    ->withPivot([
                        'player_id',
                        'awarded_at',
                        'visibility',
                        'coach_note',
                        'notify_player',
                        'post_to_feed',
                        'add_to_profile'
                    ])
                    ->withTimestamps();
    }
}
