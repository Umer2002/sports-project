<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClubInvite extends Model
{
    protected $fillable = [
        'tournament_id',
        'invitee_club_name',
        'email',
        'status',
        'token',
        'inviter_club_id',
        'registered_club_id',
        'accepted_at',
        'registered_at',
        'reward_amount',
        'reward_status',
        'reward_payout_scheduled_at',
        'reward_paid_at',
        'notes',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'registered_at' => 'datetime',
        'reward_amount' => 'decimal:2',
        'reward_payout_scheduled_at' => 'datetime',
        'reward_paid_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_REGISTERED = 'registered';
    public const STATUS_DECLINED = 'declined';

    public const REWARD_STATUS_PENDING = 'pending';
    public const REWARD_STATUS_SCHEDULED = 'scheduled';
    public const REWARD_STATUS_PAID = 'paid';

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function inviter()
    {
        return $this->belongsTo(Club::class, 'inviter_club_id');
    }

    public function registeredClub()
    {
        return $this->belongsTo(Club::class, 'registered_club_id');
    }

    public function registration()
    {
        return $this->hasOne(TournamentRegistration::class);
    }

    public function ensureToken(): string
    {
        if (! $this->token) {
            $this->token = (string) Str::uuid();
            $this->save();
        }

        return $this->token;
    }

    public function scheduleReward(float $amount, ?\DateTimeInterface $baseDate = null): void
    {
        $baseDate = $baseDate ? Carbon::parse($baseDate) : now();

        $this->forceFill([
            'reward_amount' => $amount,
            'reward_status' => self::REWARD_STATUS_SCHEDULED,
            'reward_payout_scheduled_at' => $baseDate->copy()->addDays(90),
        ])->save();
    }

    public function markRewardPaid(): void
    {
        $this->forceFill([
            'reward_status' => self::REWARD_STATUS_PAID,
            'reward_paid_at' => now(),
        ])->save();
    }
}
