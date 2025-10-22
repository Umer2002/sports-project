<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SportClassificationGroup extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'sport_id',
        'code',
        'name',
        'description',
        'sort_order',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'meta' => 'array',
        'sort_order' => 'integer',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(SportClassificationOption::class, 'group_id');
    }
}
