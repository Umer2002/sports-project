<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Club extends Model
{
    use HasFactory;
    // By default, Laravel uses a pluralized table name ("clubs") for the "Club" model,
    // so there's no need to specify $table unless you deviate from the convention.

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'user_id',
        'social_links',
        'paypal_link',
        'address',
        'phone',
        'email',
        'joining_url',
        'invite_token',
        'invites_count',
        'bio',
        'is_registered',
        'registration_date',
        'initial_player_count',
        'final_player_count',
        'estimated_payout',
        'final_payout',
        'payout_calculated_at',
        'payout_paid_at',
        'payout_status',
        'sport_id',
        'country_id',
        'state_id',
        'city_id',

    ];

    protected $casts = [
        'social_links' => 'array',
        'is_registered' => 'boolean',
        'registration_date' => 'datetime',
        'payout_calculated_at' => 'datetime',
        'payout_paid_at' => 'datetime',
    ];

    // Here you can define relationships. For example:
    protected static function booted(): void
    {
        static::creating(function (Club $club) {
            if (!$club->slug) {
                $club->slug = static::generateUniqueSlug($club->name);
            }
        });

        static::updating(function (Club $club) {
            if (($club->isDirty('name') && !$club->isDirty('slug')) || !$club->slug) {
                $club->slug = static::generateUniqueSlug($club->name, $club->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);
        if ($baseSlug === '') {
            $baseSlug = 'club';
        }

        $slug = $baseSlug;
        $suffix = 1;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generateInviteToken(): string
    {
        if (!$this->invite_token) {
            $this->invite_token = Str::uuid();
            $this->save();
        }

        return $this->invite_token;
    }

    public function getInviteLink(): string
    {
        $token = $this->generateInviteToken();
        return url('/invite/' . $token);
    }

    public function incrementInvites(): void
    {
        $this->increment('invites_count');
    }

    public function calculateInviteReward(): int
    {
        $count = $this->invites_count;

        if ($count >= 100) {
            return intdiv($count, 100) * 1000;
        }

        if ($count >= 99) {
            return 1000;
        }

        if ($count >= 70) {
            return 700;
        }

        if ($count >= 50) {
            return 500;
        }

        return 0;
    }

    public function players()
    {
        return $this->hasMany(\App\Models\Player::class, 'club_id');
    }

    public function coaches()
    {
        return $this->hasManyThrough(\App\Models\Coach::class, \App\Models\User::class, 'club_id', 'user_id');
    }

    public function sport()
    {
        return $this->belongsTo(\App\Models\Sport::class, 'sport_id');
    }



    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    public function tournamentRegistrations()
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    public function donations()
    {
        return $this->hasMany(\App\Models\Donation::class);
    }

    public function getTotalDonationsAttribute()
    {
        return $this->donations()->where('status', 'completed')->sum('amount') / 100;
    }

    public function getDonationsCountAttribute()
    {
        return $this->donations()->where('status', 'completed')->count();
    }

    /**
     * Get the registration date (defaults to created_at if not set)
     */
    public function getRegistrationDate()
    {
        return $this->registration_date ?? $this->created_at;
    }

    /**
     * Check if the club is in the 2-week onboarding period
     */
    public function isInOnboardingPeriod()
    {
        $registrationDate = $this->getRegistrationDate();
        $twoWeeksLater = $registrationDate->copy()->addDays(14);
        return now()->lt($twoWeeksLater);
    }

    /**
     * Check if the club is in the 90-day payout period
     */
    public function isInPayoutPeriod()
    {
        $registrationDate = $this->getRegistrationDate();
        $twoWeeksLater = $registrationDate->copy()->addDays(14);
        $payoutStart = $twoWeeksLater;
        $payoutEnd = $payoutStart->copy()->addDays(90);

        return now()->gte($payoutStart) && now()->lt($payoutEnd);
    }

    /**
     * Check if the club is eligible for final payout calculation
     */
    public function isEligibleForFinalPayout()
    {
        $registrationDate = $this->getRegistrationDate();
        $twoWeeksLater = $registrationDate->copy()->addDays(14);
        $finalCalculationDate = $twoWeeksLater->copy()->addDays(89);

        return now()->gte($finalCalculationDate);
    }

    /**
     * Get the current player count
     */
    public function getCurrentPlayerCount()
    {
        return $this->players()->count();
    }

    /**
     * Calculate estimated payout based on current player count
     */
    public function calculateEstimatedPayout()
    {
        $playerCount = $this->getCurrentPlayerCount();
        $payoutPlan = \App\Models\PayoutPlan::where('player_count', '<=', $playerCount)
            ->orderBy('player_count', 'desc')
            ->first();

        return $payoutPlan ? (float) $payoutPlan->payout_amount : 0.0;
    }

    /**
     * Calculate final payout based on final player count
     */
    public function calculateFinalPayout()
    {
        $playerCount = $this->final_player_count ?? $this->getCurrentPlayerCount();
        $payoutPlan = \App\Models\PayoutPlan::where('player_count', '<=', $playerCount)
            ->orderBy('player_count', 'desc')
            ->first();

        return $payoutPlan ? (float) $payoutPlan->payout_amount : 0.0;
    }

    /**
     * Get time remaining in onboarding period
     */
    public function getOnboardingTimeRemaining()
    {
        if (!$this->isInOnboardingPeriod()) {
            return null;
        }

        $registrationDate = $this->getRegistrationDate();
        $twoWeeksLater = $registrationDate->addDays(14);
        $remaining = now()->diff($twoWeeksLater);

        return [
            'weeks' => $remaining->days > 7 ? floor($remaining->days / 7) : 0,
            'days' => $remaining->days % 7,
            'hours' => $remaining->h,
            'minutes' => $remaining->i,
        ];
    }

    /**
     * Get time remaining in payout period
     */
    public function getPayoutTimeRemaining()
    {
        if (!$this->isInPayoutPeriod()) {
            return null;
        }

        $registrationDate = $this->getRegistrationDate();
        $twoWeeksLater = $registrationDate->addDays(14);
        $payoutEnd = $twoWeeksLater->addDays(90);
        $remaining = now()->diff($payoutEnd);

        return [
            'weeks' => $remaining->days > 7 ? floor($remaining->days / 7) : 0,
            'days' => $remaining->days % 7,
            'hours' => $remaining->h,
            'minutes' => $remaining->i,
        ];
    }

    /**
     * Process the initial player count calculation (after 2 weeks)
     */
    public function processInitialPlayerCount()
    {
        if ($this->isInOnboardingPeriod()) {
            return false; // Still in onboarding period
        }

        if ($this->initial_player_count !== null) {
            return false; // Already processed
        }

        $this->initial_player_count = $this->getCurrentPlayerCount();
        $this->estimated_payout = $this->calculateEstimatedPayout();
        $this->save();

        return true;
    }

    /**
     * Process the final payout calculation (after 89 days)
     */
    public function processFinalPayout()
    {
        if (!$this->isEligibleForFinalPayout()) {
            return false; // Not yet eligible
        }

        if ($this->payout_status !== 'pending') {
            return false; // Already processed
        }

        $this->final_player_count = $this->getCurrentPlayerCount();
        $this->final_payout = $this->calculateFinalPayout();
        $this->payout_calculated_at = now();
        $this->payout_status = 'calculated';
        $this->save();

        return true;
    }

    /**
     * Mark payout as paid
     */
    public function markPayoutAsPaid()
    {
        if ($this->payout_status !== 'calculated') {
            return false;
        }

        $this->payout_paid_at = now();
        $this->payout_status = 'paid';
        $this->save();

        return true;
    }

    /**
     * Get payout status description
     */
    public function getPayoutStatusDescription()
    {
        if ($this->payout_status === 'paid') {
            return 'Paid';
        }

        if ($this->payout_status === 'calculated') {
            return 'Calculated - Ready for Payment';
        }

        if ($this->isInOnboardingPeriod()) {
            return 'Onboarding Period';
        }

        if ($this->isInPayoutPeriod()) {
            return 'Payout Period';
        }

        return 'Pending';
    }
}
