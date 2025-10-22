<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InjuryReport extends Model
{
    protected $fillable = [
        'player_id', 'injury_datetime', 'team_name', 'location',
        'injury_type', 'injury_type_other', 'incident_description',
        'images', 'first_aid', 'first_aid_description', 'emergency_called',
        'hospital_referred', 'assisted_by', 'assisted_by_other',
        'expected_recovery', 'medical_note', 'return_to_play_required',
    ];

    protected $casts = [
        'images' => 'array',
        'injury_datetime' => 'datetime',
        'expected_recovery' => 'date',
        'first_aid' => 'boolean',
        'emergency_called' => 'boolean',
        'hospital_referred' => 'boolean',
        'return_to_play_required' => 'boolean'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}

