<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $table = 'players';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_id',
        'gender',
        'club_id',
        'team_id',
        'sport_id',
        'photo',
        'position_id',
        'birth_date',
        'height',
        'weight',
        'debut',
        'jersey_no',
        'college',
        'university',
        'referee_affiliation',
        'social_links',
        // 'is_registered',
        'birthday',
        'age',
        'nationality',
        'address',
        'city',
        'state',
        'zip_code',
        'bio',
        'paypal_link',
        'guardian_first_name',
        'guardian_last_name',
        'guardian_email',
        'is_lifetime_free',
        'lifetime_free_granted_at',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_registered' => 'boolean',
        'birthday' => 'date',
        'debut' => 'date',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_lifetime_free' => 'boolean',
        'lifetime_free_granted_at' => 'datetime',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)
            ->withPivot(['sport_id', 'position_id'])
            ->withTimestamps();
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function stats()
    {
        return $this->hasMany(PlayerStat::class);
    }

    public function playerStats()
    {
        return $this->hasOne(PlayerStat::class);
    }

    public function availabilities()
    {
        return $this->hasMany(PlayerAvailability::class);
    }

    public function transfers()
    {
        return $this->hasMany(PlayerTransfer::class);
    }

    public function sportHistories()
    {
        return $this->hasMany(PlayerSportHistory::class);
    }

    public function getCurrentSportHistory()
    {
        return $this->sportHistories()
            ->where('sport_id', $this->sport_id)
            ->whereNull('ended_at')
            ->first();
    }

    public function getSportHistory($sportId)
    {
        return $this->sportHistories()
            ->where('sport_id', $sportId)
            ->latest('started_at')
            ->first();
    }

    public function ads()
    {
        return $this->belongsToMany(Ad::class, 'ad_player', 'player_id', 'ad_id');
    }

    public function rewards()
    {
        return $this->hasMany(PlayerReward::class, 'user_id', 'user_id');
    }

    public function awards()
    {
        return $this->belongsToMany(Award::class, 'player_award')
                    ->withPivot([
                        'assigned_by',
                        'awarded_at',
                        'visibility',
                        'coach_note',
                        'notify_player',
                        'post_to_feed',
                        'add_to_profile'
                    ])
                    ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class)->where('type', 'player');
    }

    public function getHasPaidAttribute(): bool
    {
        if ($this->is_lifetime_free) {
            return true;
        }

        return $this->payments()->exists();
    }

    public function getStatValue($statName)
    {
        // Load all related stats (make sure 'stats' relationship is eager-loaded)
        $stat = $this->stats()
            ->whereHas('stat', function ($q) use ($statName) {
                $q->where('name', $statName);
            })
            ->first();

        return $stat?->value;
    }

}
