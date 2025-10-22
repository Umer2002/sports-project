<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentRegistration extends Model
{
    protected $fillable = [
        'tournament_id',
        'club_id',
        'club_invite_id',
        'status',
        'joining_type',
        'joining_fee',
        'team_quantity',
        'amount_due',
        'amount_paid',
        'paid_at',
        'payment_reference',
        'metadata',
    ];

    protected $casts = [
        'joining_fee' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'team_quantity' => 'integer',
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ];

    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAID = 'paid';
    public const STATUS_TEAMS_CREATED = 'teams_created';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function invite(): BelongsTo
    {
        return $this->belongsTo(ClubInvite::class, 'club_invite_id');
    }

    public function markAsPaid(?string $reference = null, ?float $amount = null): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'amount_paid' => $amount ?? $this->amount_due,
            'paid_at' => now(),
            'payment_reference' => $reference,
        ]);
    }

    public function requiresTeamQuantity(): bool
    {
        return $this->joining_type === 'per_team';
    }

    public function calculateAmountDue(?int $teamCount = null): float
    {
        $quantity = $this->joining_type === 'per_team'
            ? ($teamCount ?? $this->team_quantity)
            : 1;

        return round($quantity * (float) $this->joining_fee, 2);
    }
}
