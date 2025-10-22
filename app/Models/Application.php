<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'referee_id',
        'status',
        'applied_at',
        'priority_score'
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'priority_score' => 'integer'
    ];

    public function match()
    {
        return $this->belongsTo(GameMatch::class, 'match_id');
    }

    public function referee()
    {
        return $this->belongsTo(Referee::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByPriority($query)
    {
        return $query->orderBy('priority_score', 'desc')->orderBy('applied_at', 'asc');
    }
}
