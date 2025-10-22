<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgeGroup extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'sport_id',
        'code',
        'label',
        'min_age_years',
        'max_age_years',
        'is_open_ended',
        'context',
        'notes',
        'sort_order',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_open_ended' => 'boolean',
        'min_age_years' => 'integer',
        'max_age_years' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Scope records ordered for dropdown usage.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('label');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }
}
