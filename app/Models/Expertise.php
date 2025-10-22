<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expertise extends Model
{
    use HasFactory;

    protected $fillable = [
        'expertise_level',
        'description',
    ];

    /**
     * Get the referees that have this expertise
     */
    public function referees()
    {
        return $this->belongsToMany(Referee::class, 'referee_expertises');
    }

    /**
     * Get the games that require this expertise level
     */
    public function games()
    {
        return $this->hasMany(Game::class);
    }

    /**
     * Get the pickup games that require this expertise level
     */
    public function pickupGames()
    {
        return $this->hasMany(PickupGame::class);
    }
}
