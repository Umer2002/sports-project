<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteEmail;

class InviteLinkController extends Controller
{
    public function accept(string $token)
    {
        $referralCode = null;
        $inviterName = null;
        $inviteModel = null;
        $inviteType = 'player';

        // Check if it's a club invite token
        if ($club = Club::where('invite_token', $token)->first()) {
            $club->incrementInvites();
            $referralCode = $club->referral_code ?? 'CLUB' . str_pad($club->id, 4, '0', STR_PAD_LEFT);
            $inviterName = $club->name;
            $inviteType = 'club';
        } else {
            // Check if it's a player invite token
            $inviteModel = Invite::where('token', $token)->first();

            if (! $inviteModel) {
                // If no invite found, check if it's a user ID (for direct player invites)
                if (is_numeric($token)) {
                    $user = \App\Models\User::find($token);
                    if ($user) {
                        $referralCode = $user->referral_code ?? 'PLAY' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
                        $inviterName = $user->name;
                    }
                }

                if (! $referralCode) {
                    abort(404, 'Invalid invite link');
                }
            } else {
                $referralCode = $inviteModel->sender?->referral_code ?? 'PLAY' . str_pad($inviteModel->sender->id, 4, '0', STR_PAD_LEFT);
                $inviterName = $inviteModel->sender?->name ?? 'Play2Earn';
                $inviteType = $inviteModel->type ?? 'player';

                $inviteModel->update([
                    'receiver_email' => $inviteModel->receiver_email ?: $inviteModel->sender?->email,
                    'metadata' => array_merge($inviteModel->metadata ?? [], [
                        'visited_via_link_at' => now()->toIso8601String(),
                    ]),
                ]);

                if (in_array($inviteModel->type, ['player', 'player_free', 'club_invite'], true)) {
                    session([
                        'pending_player_invite_token' => $inviteModel->token,
                        'pending_invite_type' => $inviteModel->type,
                    ]);
                }
            }
        }

        // Create invite record for tracking
        $inviter = null;
        if (is_numeric($token)) {
            $inviter = \App\Models\User::find($token);
        } else {
            $inviteForTracking = $inviteModel ?: Invite::where('token', $token)->first();
            if ($inviteForTracking) {
                $inviter = $inviteForTracking->sender;
            }
        }

        if ($inviter) {
            // Store invite info in session for registration tracking
            session([
                'invite_tracking' => [
                    'inviter_id' => $inviter->id,
                    'referral_code' => $referralCode,
                    'inviter_name' => $inviterName,
                    'invite_type' => $inviteType,
                    'invite_id' => $inviteModel?->id,
                ]
            ]);
        }

        // Redirect to registration page with referral code
        return redirect()->route('register', [
            'ref' => $referralCode,
            'inviter' => $inviterName
        ])->with('success', 'You\'ve been invited by ' . $inviterName . '! Use referral code: ' . $referralCode);
    }

    public function sendInviteEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = Auth::user();

        // Find or create invite token for this user
        $invite = \App\Models\Invite::where('receiver_email', $request->email)->first();
        if (!$invite) {
            // Determine the type and reference_id based on user role or explicit inputs
            $type = $request->input('type')
                ?: ($user->hasRole('club') || $user->hasRole('volunteer') ? 'club' : 'player');

            // Prefer explicit reference_id if provided (e.g., volunteer inviting for a Club)
            $referenceId = $request->input('reference_id');

            if (!$referenceId) {
                if ($user->hasRole('club')) {
                    $referenceId = $user->club->id ?? null;
                } elseif ($user->hasRole('player')) {
                    $referenceId = $user->player->id ?? null;
                }
            }

            // Enforce reference_id for club invites
            if ($type === 'club' && empty($referenceId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'reference_id is required for club invites.'
                ], 422);
            }

            // Create new invite record if none exists
            $invite = \App\Models\Invite::create([
                'sender_id' => $user->id,
                'receiver_email' => $request->email,
                'token' => \Illuminate\Support\Str::uuid(),
                'type' => $type,
                'reference_id' => $referenceId,
                'is_accepted' => false,
            ]);
            // If user is a club, increment the club's invites_count
            if ($user->hasRole('club') && $user->club) {
                $user->club->increment('invites_count');
            }

            $inviteLink = url('/invite/' . $invite->token);

            // Send the email
            Mail::to($request->email)->send(new InviteEmail($inviteLink, $user, [
                'subject' => 'You are invited to Play2Earn Sports!',
                'referral_code' => $user->referral_code ?? null,
            ]));
        } else {
            // If force flag provided, re-send the existing invite email
            if ($request->boolean('force')) {
                $inviteLink = url('/invite/' . $invite->token);
                Mail::to($request->email)->send(new InviteEmail($inviteLink, $user, [
                    'subject' => 'Reminder: Your Play2Earn Sports invitation',
                    'referral_code' => $user->referral_code ?? null,
                ]));
                return response()->json(['success' => true,'msg'=>"Invite re-sent"]);
            }
            return response()->json(['success' => true,'msg'=>"Invite Already Sent"]);
        }





        return response()->json(['success' => true,'msg'=>"Invite Successfully sent"]);
    }

    public function trackSuccessfulSignup($userId, $inviterId = null)
    {
        // If inviter_id is provided, create invite record
        if ($inviterId) {
            $user = \App\Models\User::find($userId);
            $inviter = \App\Models\User::find($inviterId);
            
            if ($user && $inviter) {
                // Create invite record for successful signup
                Invite::create([
                    'sender_id' => $inviterId,
                    'receiver_id' => $userId,
                    'receiver_email' => $user->email,
                    'token' => \Illuminate\Support\Str::uuid(),
                    'type' => 'player',
                    'reference_id' => $userId,
                    'is_accepted' => true,
                    'accepted_at' => now(),
                    'payout_processed' => false,
                ]);

                // Update user's referred_by field
                $user->update(['referred_by' => $inviterId]);
            }
        }
    }
}
