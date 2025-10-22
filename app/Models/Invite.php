<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\Club;
use App\Models\User;

class Invite extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'receiver_email',
        'token',
        'type',
        'reference_id',
        'is_accepted',
        'accepted_at',
        'payout_processed',
        'payout_processed_at',
        'email_sent_at',
        'email_last_attempt_at',
        'email_attempts',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'accepted_at' => 'datetime',
        'payout_processed_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'email_last_attempt_at' => 'datetime',
    ];

    public function generateToken(): string
    {
        if (!$this->token) {
            $this->token = Str::uuid();
            $this->save();
        }

        return $this->token;
    }

    public function getInviteLink(): string
    {
        $token = $this->generateToken();
        return url('/invite/' . $token);
    }

    public function getClubPlayerRegistrationLink(): ?string
    {
        if (! in_array($this->type, ['club_invite', 'club'], true) || ! $this->reference_id) {
            return null;
        }

        $club = Club::find($this->reference_id);
        if (! $club) {
            return null;
        }

        $token = $this->generateToken();

        return route('register.player', [
            'club' => $club->id,
            'sport' => optional($club->sport)->id,
            'invite_token' => $token,
        ]);
    }

    public static function markAcceptedForUser(User $user): int
    {
        $email = $user->email;
        $now = now();
        $playerId = optional($user->player)->id;
        $playerModel = $user->player;
        $accepted = 0;

        static::query()
            ->whereIn('type', ['club_invite', 'club', 'player', 'player_free', 'event'])
            ->where(function ($q) use ($user, $email) {
                $q->where('receiver_email', $email)
                  ->orWhere('receiver_id', $user->id);
            })
            ->where(function ($q) {
                $q->whereNull('is_accepted')->orWhere('is_accepted', false);
            })
            ->with('sender')
            ->chunkById(100, function ($invites) use ($user, $email, $now, $playerId, &$accepted, &$playerModel) {
                foreach ($invites as $invite) {
                    if ($invite->type === 'event') {
                        $sender = $invite->sender;
                        if (! $sender) {
                            continue;
                        }
                        if (is_null($sender->club_id) && is_null($sender->player_id)) {
                            continue;
                        }
                    }

                    $metadata = $invite->metadata ?? [];
                    if (! is_array($metadata)) {
                        $metadata = [];
                    }

                    $metadata['accepted_user_id'] = $user->id;
                    if ($playerId) {
                        $metadata['accepted_player_id'] = $playerId;
                    }
                    if ($invite->type === 'player_free') {
                        $metadata['lifetime_free_applied_at'] = $now->toIso8601String();
                    }

                    $invite->forceFill([
                        'receiver_id' => $user->id,
                        'receiver_email' => $email,
                        'is_accepted' => true,
                        'accepted_at' => $now,
                        'metadata' => $metadata,
                    ])->save();

                    if ($invite->type === 'player_free' && $playerModel && ! $playerModel->is_lifetime_free) {
                        $playerModel->forceFill([
                            'is_lifetime_free' => true,
                            'lifetime_free_granted_at' => $playerModel->lifetime_free_granted_at ?? $now,
                        ])->save();
                    }

                    $accepted++;
                }
            });

        return $accepted;
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
