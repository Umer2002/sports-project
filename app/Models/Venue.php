<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'location',
        'capacity',
        'type',
        'club_id',
        'country_id',
        'state_id',
        'city_id',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function availabilities()
    {
        return $this->hasMany(VenueAvailability::class);
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }
}
