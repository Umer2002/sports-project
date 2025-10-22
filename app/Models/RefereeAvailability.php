<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefereeAvailability extends Model
{
    protected $fillable = [
        'referee_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_available',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_available' => 'boolean',
    ];

    public function referee()
    {
        return $this->belongsTo(Referee::class);
    }
}
