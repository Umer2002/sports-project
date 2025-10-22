<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerGameStat extends Model
{
    protected $table = 'player_game_stats';
    
    protected $fillable = [
        'sport_id',
        'stat1',
        'stat2',
        'stat3',
        'stat4',
    ];

    /**
     * Get the sport associated with this stat configuration.
     */
    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get all 4 stats as an array.
     */
    public function getStatsAttribute()
    {
        return [
            'stat1' => $this->stat1,
            'stat2' => $this->stat2,
            'stat3' => $this->stat3,
            'stat4' => $this->stat4,
        ];
    }

    /**
     * Set all 4 stats from an array.
     */
    public function setStatsAttribute($stats)
    {
        $this->stat1 = $stats['stat1'] ?? null;
        $this->stat2 = $stats['stat2'] ?? null;
        $this->stat3 = $stats['stat3'] ?? null;
        $this->stat4 = $stats['stat4'] ?? null;
    }
}