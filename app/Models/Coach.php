<?php

// ✅ Coach Model - app/Models/Coach.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'gender','user_id',
        'socail_links', 'city', 'bio', 'country_id', 'photo', 'age', 'sport_id',
    ];

    protected $casts = [
        'socail_links' => 'array',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_coach');
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'coach_tournament')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

// ✅ Coach Controller - app/Http/Controllers/Admin/CoachController.php
