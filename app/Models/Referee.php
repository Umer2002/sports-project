<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referee extends Model
{
    protected $fillable = [
        'full_name', 'email', 'phone', 'preferred_contact_method', 'profile_picture', 'user_id',
        'government_id', 'languages_spoken', 'city', 'region', 'country',
        'license_type', 'certification_level', 'certifying_body', 'license_expiry_date',
        'background_check_passed', 'liability_insurance', 'liability_document',
        'sports_officiated', 'account_status', 'internal_notes', 'club_id',
    ];

    protected $casts = [
        'sports_officiated' => 'array',
        'license_expiry_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function matches()
    {
        return $this->hasMany(Game::class);
    }

    public function availability()
    {
        return $this->hasMany(RefereeAvailability::class);
    }

    public function assignedGames()
    {
        return $this->hasMany(Game::class, 'referee_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get the expertise levels this referee has
     */
    public function expertises()
    {
        return $this->belongsToMany(Expertise::class, 'referee_expertises');
    }

    /**
     * Get games that match this referee's expertise levels
     */
    public function availableGames()
    {
        return Game::whereIn('expertise_id', $this->expertises()->pluck('expertises.id'));
    }

    /**
     * Get pickup games that match this referee's expertise levels
     */
    public function availablePickupGames()
    {
        return PickupGame::whereIn('expertise_id', $this->expertises()->pluck('expertises.id'));
    }

}
