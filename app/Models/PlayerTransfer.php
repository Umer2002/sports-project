<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerTransfer extends Model
{
    protected $fillable = [
        'player_id', 'from_club_id', 'to_club_id',
        'from_sport_id', 'to_sport_id', 'status', 'approved_at', 'rejected_at', 'notes'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function fromClub()
    {
        return $this->belongsTo(Club::class, 'from_club_id');
    }

    public function toClub()
    {
        return $this->belongsTo(Club::class, 'to_club_id');
    }

    public function fromSport()
    {
        return $this->belongsTo(Sport::class, 'from_sport_id');
    }

    public function toSport()
    {
        return $this->belongsTo(Sport::class, 'to_sport_id');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
