<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueAvailability extends Model
{
    protected $fillable = [
        'venue_id', 'available_date', 'start_time', 'end_time'
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
