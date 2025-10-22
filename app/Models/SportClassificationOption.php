<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SportClassificationOption extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'group_id',
        'code',
        'label',
        'sort_order',
        'numeric_rank',
        'meta',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'meta' => 'array',
        'sort_order' => 'integer',
        'numeric_rank' => 'integer',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('label');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(SportClassificationGroup::class, 'group_id');
    }
}
