<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerSportHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id', 'sport_id', 'club_id', 'team_id',
        'stats_data', 'achievements', 'started_at', 'ended_at'
    ];

    protected $casts = [
        'stats_data' => 'array',
        'achievements' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function isActive()
    {
        return is_null($this->ended_at);
    }

    public function getStatValue($statName)
    {
        return $this->stats_data[$statName] ?? 0;
    }

    public function getAchievement($achievementName)
    {
        return $this->achievements[$achievementName] ?? null;
    }
}
