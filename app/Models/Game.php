<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\GameEvent;

class Game extends Model
{
    protected $table = 'matches';

    protected $fillable = ['tournament_id', 'home_club_id', 'away_club_id', 'age_group', 'required_referee_level', 'match_date', 'match_time', 'venue', 'score', 'referee_id', 'expertise_id'];
    protected $casts = ['score' => 'array'];

    public function tournament()
    {return $this->belongsTo(Tournament::class);}
    public function homeClub()
    {return $this->belongsTo(Club::class, 'home_club_id');}
    public function awayClub()
    {return $this->belongsTo(Club::class, 'away_club_id');}
    public function referee()
    {return $this->belongsTo(Referee::class, 'referee_id');}
    public function rsvps()
    {return $this->hasMany(Rsvp::class, 'match_id');}

    public function scopeUpcoming($query)
    {
        return $query->whereRaw("STR_TO_DATE(CONCAT(match_date, ' ', match_time), '%Y-%m-%d %H:%i:%s') >= ?", [Carbon::now()]);
    }

    public function events()
    {
        return $this->hasMany(GameEvent::class, 'game_id');
    }

    /**
     * Get the expertise level required for this game
     */
    public function expertise()
    {
        return $this->belongsTo(Expertise::class);
    }

    /**
     * Get referees that can officiate this game based on expertise
     */
    public function availableReferees()
    {
        if (!$this->expertise_id) {
            return Referee::query(); // If no expertise required, all referees available
        }
        
        return Referee::whereHas('expertises', function($query) {
            $query->where('expertises.id', $this->expertise_id);
        });
    }

}
