<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMatch extends Model
{
    protected $table = "matches";
    protected $fillable = [
        'tournament_id',
        'home_club_id',
        'away_club_id',
        'match_date',
        'match_time',
        'venue',
        'score',
        'referee_id',
        'required_referee_level',
    ];

    protected $casts = [
        'score' => 'array',
        'match_date' => 'date',
    ];

    /**
     * Get the tournament that owns the match
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Get the home club
     */
    public function homeClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'home_club_id');
    }

    /**
     * Get the away club
     */
    public function awayClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'away_club_id');
    }

    /**
     * Get the referee for this match
     */
    public function referee(): BelongsTo
    {
        return $this->belongsTo(Referee::class, 'referee_id');
    }

    /**
     * Get available referees for this match based on expertise
     */
    public function availableReferees()
    {
        // For now, return all referees. In a real implementation, this would filter by expertise level
        return Referee::query();
    }

    /**
     * Get applications for this match
     */
    public function applications()
    {
        return $this->hasMany(Application::class, 'match_id');
    }

    /**
     * Get referees who have applied for this match
     */
    public function appliedReferees()
    {
        return $this->hasManyThrough(
            Referee::class,
            Application::class,
            'match_id', // Foreign key on applications table
            'id', // Foreign key on referees table
            'id', // Local key on matches table
            'referee_id' // Local key on applications table
        )->where('applications.status', 'pending');
    }

    /**
     * Get the venue for this match
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'venue');
    }
}
