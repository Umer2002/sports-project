<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'club_id',
        'title',
        'description',
        'event_date',
        'event_time',
        'location',
        'banner',
        'group_id',
        'privacy',
        'going_users_id',
        'interested_users_id',
        'type',
        'event_type',
        'status',
        'event_data',
        'start',
        'end',
        'background_color',
    ];

    protected $casts = [
        'going_users_id' => 'array',
        'interested_users_id' => 'array',
        'event_data' => 'array',
    ];

    // Relationship to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to team (if event is team-specific)
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // Relationship to club (if event is club-specific)
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    // Inverse polymorphic relationship for invites
    public function invites()
    {
        return $this->morphMany(Invite::class, 'inviteable', 'type', 'reference_id');
    }

    // Optional: helper to check if user is invited
    public function isUserInvited($userId)
    {
        return $this->invites()->where('invitee_id', $userId)->exists();
    }

    // Helper to get event color based on type
    public function getEventColor()
    {
        $colorMap = [
            'training' => '#1e88e5',
            'match' => '#e53935',
            'meeting' => '#43a047',
            'tournament' => '#ff9800',
            'other' => '#6d6d6d'
        ];
        
        return $colorMap[$this->type] ?? '#1e88e5';
    }

    // Helper to get formatted event date and time
    public function getFormattedDateTime()
    {
        // If we have event_date and event_time, use them
        if ($this->event_date && $this->event_time) {
            return $this->event_date . 'T' . $this->event_time;
        }
        
        // Otherwise, use the start field if available
        if ($this->start) {
            return date('Y-m-d\TH:i:s', strtotime($this->start));
        }
        
        // Fallback to current date/time
        return now()->format('Y-m-d\TH:i:s');
    }
}
