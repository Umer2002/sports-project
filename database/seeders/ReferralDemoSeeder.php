<?php
namespace Database\Seeders;

use App\Models\Club;
use App\Models\Player;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ReferralDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now  = Carbon::now();
        $d120 = $now->copy()->subDays(120); // ready
        $d110 = $now->copy()->subDays(110); // ready
        $d100 = $now->copy()->subDays(100); // ready
        $d95  = $now->copy()->subDays(95);  // ready
        $d50  = $now->copy()->subDays(50);  // upcoming
        $d45  = $now->copy()->subDays(45);  // upcoming
        $d30  = $now->copy()->subDays(30);  // upcoming
        $d200 = $now->copy()->subDays(200); // paid long ago

        // ---------- helpers ----------
        $firstOrUser = function (string $email, string $name, ?string $role = null): User {
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => Hash::make('password')]
            );
            if ($role && class_exists(Role::class) && method_exists($user, 'roles')) {
                if ($r = Role::where('name', $role)->first()) {
                    $user->roles()->syncWithoutDetaching([$r->id]);
                }
            }
            return $user;
        };

        // ✅ Ensure social_links (json) and other non-null fields are set
        $firstOrClub = function (array $data): Club {
            return Club::firstOrCreate(
                ['name' => $data['name']],
                [
                    'user_id'           => $data['user_id'],
                    'email'             => $data['email'] ?? ($data['name'] . '@club.local'),
                    'paypal_link'       => $data['paypal_link'] ?? null,
                    'social_links'      => $data['social_links'] ?? [], // <-- important
                    'is_registered'     => 1,
                    'registration_date' => $data['registration_date'] ?? Carbon::now()->subDays(140),
                    'invites_count'     => $data['invites_count'] ?? 0,
                    'payout_status'     => $data['payout_status'] ?? 'pending',
                    // slug auto via model
                ]
            );
        };

        $firstOrPlayer = function (array $data): Player {
            return Player::firstOrCreate(
                ['user_id' => $data['user_id']],
                [
                    'name'         => $data['name'],
                    'email'        => $data['email'] ?? ($data['name'] . '@player.local'),
                    'club_id'      => $data['club_id'] ?? null,
                    'paypal_link'  => $data['paypal_link'] ?? null,
                    'social_links' => $data['social_links'] ?? [], // <-- important
                ]
            );
        };
        // Map user_id => email for convenience
        $userEmailById = \App\Models\User::pluck('email', 'id')->all();

        $inviteRow = function (int $senderUserId, int $receiverUserId,  ? \Carbon\Carbon $acceptedAt, bool $paid = false) use ($now, $userEmailById) {
            $row = [
                'sender_id'   => $senderUserId,
                'receiver_id' => $receiverUserId,
                'created_at'  => $acceptedAt ?: $now,
                'updated_at'  => $acceptedAt ?: $now,
            ];

            // Optional/conditional columns — only set if they exist in your schema
            if (Schema::hasColumn('invites', 'accepted_at')) {
                $row['accepted_at'] = $acceptedAt;
            }
            if (Schema::hasColumn('invites', 'payout_processed')) {
                $row['payout_processed'] = $paid;
            }
            if (Schema::hasColumn('invites', 'payout_processed_at')) {
                $row['payout_processed_at'] = $paid ? $now : null;
            }
            if (Schema::hasColumn('invites', 'payout_batch_id')) {
                $row['payout_batch_id'] = $paid ? ('batch_' . \Illuminate\Support\Str::uuid()->toString()) : null;
            }

            // 🔑 Fix: set receiver_email (and sender_email) if your table has them
            if (Schema::hasColumn('invites', 'receiver_email')) {
                $row['receiver_email'] = $userEmailById[$receiverUserId] ?? 'unknown-receiver@example.com';
            }
            if (Schema::hasColumn('invites', 'sender_email')) {
                $row['sender_email'] = $userEmailById[$senderUserId] ?? 'unknown-sender@example.com';
            }

            return $row;
        };

        // ---------- reuse ChatDemoSeeder entities if present ----------
        $uChat1     = User::where('email', 'player.chat1@demo.local')->first() ?? $firstOrUser('player.chat1@demo.local', 'Player Chat One', 'player');
        $uChat2     = User::where('email', 'player.chat2@demo.local')->first() ?? $firstOrUser('player.chat2@demo.local', 'Player Chat Two', 'player');
        $uClubAdmin = User::where('email', 'club.chat.admin@demo.local')->first() ?? $firstOrUser('club.chat.admin@demo.local', 'Chat Demo Club Admin', 'club');

        $clubDemo = Club::where('name', 'Chat Demo Club')->first();
        if (! $clubDemo) {
            $clubDemo = $firstOrClub([
                'name'         => 'Chat Demo Club',
                'user_id'      => $uClubAdmin->id,
                'email'        => 'club.chat.demo@demo.local',
                'paypal_link'  => 'chat.demo.club.payments@example.com',
                'social_links' => [], // ensure json present
            ]);
        } elseif ($clubDemo->social_links === null || $clubDemo->social_links === '') {
            $clubDemo->update(['social_links' => []]);
        }
        if (! $clubDemo->paypal_link) {
            $clubDemo->update(['paypal_link' => 'chat.demo.club.payments@example.com']);
        }

        $playerChat1 = Player::where('user_id', $uChat1->id)->first() ??
            $firstOrPlayer([
            'user_id'      => $uChat1->id,
            'name'         => 'Player Chat One',
            'email'        => 'player.chat1@demo.local',
            'club_id'      => $clubDemo->id,
            'paypal_link'  => 'player.chat1.payouts@example.com',
            'social_links' => [],
        ]);
        if ($playerChat1->social_links === null) {
            $playerChat1->update(['social_links' => []]);
        }

        if (! $playerChat1->paypal_link) {
            $playerChat1->update(['paypal_link' => 'player.chat1.payouts@example.com']);
        }

        $playerChat2 = Player::where('user_id', $uChat2->id)->first() ??
            $firstOrPlayer([
            'user_id'      => $uChat2->id,
            'name'         => 'Player Chat Two',
            'email'        => 'player.chat2@demo.local',
            'club_id'      => $clubDemo->id,
            'paypal_link'  => 'player.chat2.payouts@example.com',
            'social_links' => [],
        ]);
        if ($playerChat2->social_links === null) {
            $playerChat2->update(['social_links' => []]);
        }

        if (! $playerChat2->paypal_link) {
            $playerChat2->update(['paypal_link' => 'player.chat2.payouts@example.com']);
        }

        // ---------- extra inviters ----------
        $uClubAlphaOwner = $firstOrUser('club.alpha.owner@demo.local', 'Alpha FC Owner', 'club');
        $clubAlpha       = $firstOrClub([
            'name'         => 'Alpha FC',
            'user_id'      => $uClubAlphaOwner->id,
            'email'        => 'alpha.fc@demo.local',
            'paypal_link'  => 'alpha.fc.payments@example.com',
            'social_links' => [],
        ]);

        $uClubBetaOwner = $firstOrUser('club.beta.owner@demo.local', 'Beta FC Owner', 'club');
        $clubBeta       = $firstOrClub([
            'name'         => 'Beta FC',
            'user_id'      => $uClubBetaOwner->id,
            'email'        => 'beta.fc@demo.local',
            'paypal_link'  => 'beta.fc.payments@example.com',
            'social_links' => [],
        ]);

        $uPlayerEve = $firstOrUser('eve.player@demo.local', 'Eve Player', 'player');
        $playerEve  = $firstOrPlayer([
            'user_id'      => $uPlayerEve->id,
            'name'         => 'Eve Player',
            'email'        => 'eve.player@demo.local',
            'club_id'      => $clubDemo->id,
            'paypal_link'  => 'eve.player.payouts@example.com',
            'social_links' => [],
        ]);

        // ---------- receivers (need both User + Player/Club) ----------
        $uRecvP1 = $firstOrUser('recv.p1@demo.local', 'Receiver Player 1', 'player');
        $recvP1  = $firstOrPlayer(['user_id' => $uRecvP1->id, 'name' => 'Receiver Player 1', 'email' => 'recv.p1@demo.local', 'club_id' => $clubDemo->id, 'social_links' => []]);

        $uRecvP2 = $firstOrUser('recv.p2@demo.local', 'Receiver Player 2', 'player');
        $recvP2  = $firstOrPlayer(['user_id' => $uRecvP2->id, 'name' => 'Receiver Player 2', 'email' => 'recv.p2@demo.local', 'club_id' => $clubDemo->id, 'social_links' => []]);

        $uRecvP3 = $firstOrUser('recv.p3@demo.local', 'Receiver Player 3', 'player');
        $recvP3  = $firstOrPlayer(['user_id' => $uRecvP3->id, 'name' => 'Receiver Player 3', 'email' => 'recv.p3@demo.local', 'club_id' => $clubDemo->id, 'social_links' => []]);

        $uRecvC1Owner = $firstOrUser('recv.c1.owner@demo.local', 'Receiver Club 1 Owner', 'club');
        $recvC1       = $firstOrClub(['name' => 'Receiver Club 1', 'user_id' => $uRecvC1Owner->id, 'email' => 'recv.c1@demo.local', 'paypal_link' => 'recv.club1.payments@example.com', 'social_links' => []]);

        $uRecvC2Owner = $firstOrUser('recv.c2.owner@demo.local', 'Receiver Club 2 Owner', 'club');
        $recvC2       = $firstOrClub(['name' => 'Receiver Club 2', 'user_id' => $uRecvC2Owner->id, 'email' => 'recv.c2@demo.local', 'paypal_link' => 'recv.club2.payments@example.com', 'social_links' => []]);

        $uRecvC3Owner = $firstOrUser('recv.c3.owner@demo.local', 'Receiver Club 3 Owner', 'club');
        $recvC3       = $firstOrClub(['name' => 'Receiver Club 3', 'user_id' => $uRecvC3Owner->id, 'email' => 'recv.c3@demo.local', 'paypal_link' => 'recv.club3.payments@example.com', 'social_links' => []]);

        // ---------- invites ----------
        $rows = [];

                                                                                // Players — READY
        $rows[] = $inviteRow($uChat1->id, $uRecvP1->id, $d110, false);          // player
        $rows[] = $inviteRow($uChat1->id, $uRecvC1Owner->id, $d95, false);      // club
        $rows[] = $inviteRow($uPlayerEve->id, $uRecvC2Owner->id, $d120, false); // club

                                                                           // Players — UPCOMING
        $rows[] = $inviteRow($uChat2->id, $uRecvC3Owner->id, $d50, false); // club
        $rows[] = $inviteRow($uChat2->id, $uRecvP2->id, $d30, false);      // player

                                                                                  // Clubs — READY
        $rows[] = $inviteRow($clubDemo->user_id, $uRecvP3->id, $d100, false);     // player
        $rows[] = $inviteRow($clubDemo->user_id, $uRecvC2Owner->id, $d95, false); // club
        $rows[] = $inviteRow($clubAlpha->user_id, $uRecvP2->id, $d110, false);    // player

                                                                             // Clubs — UPCOMING
        $rows[] = $inviteRow($clubBeta->user_id, $uRecvP1->id, $d45, false); // player

        // Paid examples
        $rows[] = $inviteRow($clubDemo->user_id, $uRecvP1->id, $d200, true);
        $rows[] = $inviteRow($uChat1->id, $uRecvC3Owner->id, $d200, true);

        DB::table('invites')->insert($rows);

        $this->command?->info('Referral demo seeded with social_links defaults set for Clubs/Players.');
    }
}
