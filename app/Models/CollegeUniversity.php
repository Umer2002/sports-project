<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollegeUniversity extends Model
{
    protected $fillable = [
        'user_id',
        'sport_id',
        'college_name',
        'logo',
        'social_links',
        'paypal_link',
        'address',
        'phone',
        'email',
        'joining_url',
        'bio',
        'is_registered',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_registered' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }
}
