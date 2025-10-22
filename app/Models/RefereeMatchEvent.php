<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefereeMatchEvent extends Model
{
    protected $fillable = [
        'referee_id',
        'title',
        'opponent',
        'match_type',
        'home_away',
        'event_date',
        'kickoff_time',
        'arrival_time',
        'end_time',
        'repeat_frequency',
        'repeat_interval',
        'repeat_occurrences',
        'repeat_days',
        'repeat_ends_at',
        'venue_name',
        'venue_address',
        'notification_lead_times',
        'color',
        'roster',
        'attachment_path',
        'is_draft',
        'extra',
    ];

    protected $casts = [
        'event_date' => 'date',
        'kickoff_time' => 'datetime:H:i',
        'arrival_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'repeat_days' => 'array',
        'repeat_ends_at' => 'date',
        'roster' => 'array',
        'extra' => 'array',
        'is_draft' => 'boolean',
    ];

    public function referee(): BelongsTo
    {
        return $this->belongsTo(Referee::class);
    }

    public function getNotificationLeadTimesAttribute(?string $value): array
    {
        if (! $value) {
            return [];
        }

        return array_filter(array_map('trim', explode(',', $value)));
    }

    public function setNotificationLeadTimesAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['notification_lead_times'] = implode(',', array_filter($value));
            return;
        }

        $this->attributes['notification_lead_times'] = $value;
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('event_date', '>=', now()->toDateString())->orderBy('event_date');
    }
}

