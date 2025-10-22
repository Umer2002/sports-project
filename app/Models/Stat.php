<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    protected $table = 'stats';
    
    protected $fillable = [
        'name',
        'sport_id',
    ];

    /**
     * Get the sport that this stat belongs to.
     */
    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }
}
