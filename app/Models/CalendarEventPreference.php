<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CalendarEventPreference extends Model
{
    protected $fillable = [
        'user_id',
        'preferenceable_type',
        'preferenceable_id',
        'attending_status',
        'carpool_status',
        'seats_available',
        'attachments',
        'calendar_added_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'calendar_added_at' => 'datetime',
    ];

    public function preferenceable(): MorphTo
    {
        return $this->morphTo();
    }
}

