<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerStat extends Model
{
    protected $table = 'player_stats';
    
    protected $fillable = [
        'player_id',
        'stat1',
        'stat2',
        'stat3',
        'stat4',
        'stat1_vlaue',
        'stat2_vlaue',
        'stat3_vlaue',
        'stat4_vlaue',
    ];

    /**
     * Get the player associated with this stat record.
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the stats as an array
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
}
