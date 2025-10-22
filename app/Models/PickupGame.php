<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupGame extends Model
{
    protected $fillable = [
        'sport_id', 'game_datetime', 'location', 'latitude', 'longitude', 'max_players', 'privacy', 'join_fee', 'host_id',
        'title', 'description', 'skill_level', 'equipment_needed', 'share_link', 'expertise_id', 'referee_id'
    ];

    protected $casts = [
        'game_datetime' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'join_fee' => 'decimal:8,2',
    ];

    public function sport() {
        return $this->belongsTo(Sport::class);
    }

    public function host() {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function participants() {
        return $this->belongsToMany(User::class, 'pickup_game_participants');
    }

    public function isFull()
    {
        return $this->participants()->count() >= $this->max_players;
    }

    public function getAvailableSpots()
    {
        return $this->max_players - $this->participants()->count();
    }

    public function isHostedBy($userId)
    {
        return $this->host_id === $userId;
    }

    public function canJoin($userId)
    {
        return !$this->isFull() && !$this->participants()->where('user_id', $userId)->exists();
    }

    public function generateShareLink()
    {
        $this->share_link = route('player.pickup-games.show', $this);
        $this->save();
        return $this->share_link;
    }

    /**
     * Get the expertise level required for this pickup game
     */
    public function expertise()
    {
        return $this->belongsTo(Expertise::class);
    }

    /**
     * Get the assigned referee for this pickup game
     */
    public function referee()
    {
        return $this->belongsTo(Referee::class);
    }

    /**
     * Get referees that can officiate this pickup game based on expertise
     */
    public function availableReferees()
    {
        if (!$this->expertise_id) {
            return Referee::all(); // If no expertise required, all referees available
        }
        
        return Referee::whereHas('expertises', function($query) {
            $query->where('expertises.id', $this->expertise_id);
        });
    }
}


