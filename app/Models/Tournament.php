<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = [
        'name',
        'description',
        'host_club_id',
        'country_id',
        'state_id',
        'city_id',
        'venue_id',
        'start_date',
        'end_date',
        'sport_id',
        'location',
        'tournament_format_id',
        'division_id',
        'gender_id',
        'age_group_id',
        'registration_cutoff_date',
        'joining_fee',
        'joining_type',
    ];

    protected $casts = [
        'start_date'               => 'date',
        'end_date'                 => 'date',
        'registration_cutoff_date' => 'date',
        'joining_fee'              => 'decimal:2',
    ];

    public function hostClub()
    {
        return $this->belongsTo(Club::class, 'host_club_id');
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

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'tournament_team')->withTimestamps();
    }

    public function coaches()
    {
        return $this->belongsToMany(Coach::class, 'coach_tournament')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class)->withDefault();
    }
    public function ageGroup()
    {
        return $this->belongsTo(AgeGroup::class);
    }

    public function scheduledGames()
    {
        return $this->hasMany(Game::class);
    }

    public function games()
    {
        return $this->scheduledGames();
    }

    public function matches()
    {
        return $this->hasMany(GameMatch::class, 'tournament_id');
    }

    public function invites()
    {
        return $this->hasMany(ClubInvite::class);
    }

    public function registrations()
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    // Tournament.php
    public function hotels()
    {
        // tournaments.venue_id -> venues.id -> hotels.venue_id
        return $this->hasManyThrough(
            Hotel::class, // final model
            Venue::class, // intermediate
            'id',         // Venue's local key referenced by tournaments.venue_id
            'venue_id',   // Hotel's FK pointing to venues.id
            'venue_id',   // Tournament's FK pointing to venues.id
            'id'          // Venue's PK
        );
    }

    public function classificationOptions()
    {
        return $this->belongsToMany(SportClassificationOption::class, 'tournament_classifications', 'tournament_id', 'option_id')
            ->withTimestamps();
    }

    public function classifications()
    {
        return $this->classificationOptions();
    }
}
