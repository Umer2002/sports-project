<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = ['tournament_id', 'venue_id', 'name', 'address', 'google_place_id'];

    public function tournament() {
        return $this->belongsTo(Tournament::class);
    }

    public function venue() {
        return $this->belongsTo(Venue::class);
    }
}
