<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'player_id',
        'user_id',
        'club_id',
        'stripe_session_id',
        'amount',
        'currency',
        'type',
        'processed_by',
        'notes',
    ];

    public function player()
    {
        return $this->belongsTo(\App\Models\Player::class);
    }
    public function club()
    {
        return $this->belongsTo(\App\Models\Club::class);
    }
    public function processedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'processed_by');
    }

    public function donation()
    {
        return $this->hasOne(\App\Models\Donation::class, 'stripe_session_id', 'stripe_session_id');
    }
}
