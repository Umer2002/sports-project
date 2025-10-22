<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'club_id',
        'player_id',
        'stripe_session_id',
        'amount',
        'currency',
        'donor_name',
        'donor_email',
        'message',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount / 100, 2);
    }

    public function getAmountInDollarsAttribute()
    {
        return $this->amount / 100;
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
