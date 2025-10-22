<?php

namespace App\Models;

use App\Models\Referee;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache; // ⬅️ you use Cache in isOnline()

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','player_id','club_id','coach_id',
        'ambassador_token','referral_code','referred_by',
    ];

    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ===== Roles relation (your existing pivot) =====
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')
                    ->withTimestamps();
    }

    /** Assign a role by name or Role model */
    public function assignRole(Role|string $role): static
    {
        $roleModel = $role instanceof Role
            ? $role
            : Role::firstOrCreate(['name' => $role]); // creates if missing

        // Avoid duplicates
        $this->roles()->syncWithoutDetaching([$roleModel->id]);

        // Optional: refresh the relation in memory
        $this->load('roles');

        return $this;
    }

    /** Remove a role by name or Role model */
    public function removeRole(Role|string $role): static
    {
        $roleId = $role instanceof Role
            ? $role->id
            : optional(Role::where('name', $role)->first())->id;

        if ($roleId) {
            $this->roles()->detach($roleId);
            $this->load('roles');
        }
        return $this;
    }

    /** Replace all roles with the provided set (names or Role models) */
    public function syncRoles(array $roles): static
    {
        $ids = collect($roles)->map(function ($r) {
            return $r instanceof Role ? $r->id : Role::firstOrCreate(['name' => $r])->id;
        })->all();

        $this->roles()->sync($ids);
        $this->load('roles');
        return $this;
    }

    /** Check if user has a role by name (query-based, no need to preload) */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /** Optional helpers */
    public function hasAnyRole(array $names): bool
    {
        return $this->roles()->whereIn('name', $names)->exists();
    }

    public function roleNames(): array
    {
        return $this->roles()->pluck('name')->all();
    }

    // ===== Your other relations/methods (unchanged) =====
    public function club()   { return $this->belongsTo(Club::class); }
    public function player() { return $this->hasOne(Player::class); }
    public function coach()  { return $this->hasOne(Coach::class); }
    public function referee(){ return $this->hasOne(Referee::class); }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'followed_id', 'follower_id');
    }
    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'followed_id');
    }

    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }

    public function cartItems() { return $this->hasMany(CartItem::class); }
    public function orders()    { return $this->hasMany(Order::class); }

    public function getCartTotalAttribute()     { return $this->cartItems->sum('total_price'); }
    public function getCartItemCountAttribute() { return $this->cartItems->sum('quantity'); }

    public function ambassadorReferrals()
    {
        return $this->hasMany(\App\Models\AmbassadorReferral::class, 'ambassador_id');
    }

    public function playersReferredCount(): int
    {
        return $this->ambassadorReferrals()->where('type','player')->count();
    }

    public function ambassadorCommissionAmount(): int
    {
        $n = $this->playersReferredCount();
        if ($n < 100) return 0;
        return match (true) {
            $n >= 8000 => 3000,
            $n >= 7500 => 2950,
            $n >= 7000 => 2850,
            $n >= 6500 => 2750,
            $n >= 6000 => 2500,
            $n >= 5000 => 2250,
            $n >= 4000 => 2000,
            $n >= 3000 => 1750,
            $n >= 2000 => 1500,
            $n >= 1500 => 1250,
            $n >= 1000 => 1000,
            $n >= 750  => 750,
            $n >= 500  => 500,
            $n >= 250  => 250,
            default    => 100,
        };
    }

    public function sentInvites()      { return $this->hasMany(Invite::class, 'sender_id'); }
    public function receivedInvites()  { return $this->hasMany(Invite::class, 'receiver_id'); }
    public function acceptedInvites()  { return $this->sentInvites()->where('is_accepted', true); }
    public function pendingInvites()   { return $this->sentInvites()->where('is_accepted', false); }

    public function referrals()        { return $this->hasMany(User::class, 'referred_by'); }
    public function referrer()         { return $this->belongsTo(User::class, 'referred_by'); }

    public function getReferralCodeAttribute()
    {
        if (empty($this->attributes['referral_code'])) {
            $this->attributes['referral_code'] = 'PLAY' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
            $this->save();
        }
        return $this->attributes['referral_code'];
    }

    public function getTotalReferralEarnings()       { return $this->acceptedInvites()->count() * 10; }
    public function getPendingReferralEarnings()     { return $this->acceptedInvites()->where('accepted_at','>', now()->subDays(90))->count() * 10; }
    public function getAvailableReferralEarnings()   { return $this->acceptedInvites()->where('accepted_at','<=', now()->subDays(90))->where('payout_processed', false)->count() * 10; }
}
