<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\ChatParticipant;
use App\Models\Club;
use App\Models\Message;
use App\Models\Player;
use App\Models\Role;
use App\Models\Team;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ChatDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Ensure two test users exist (easy to log in)
        $userA = User::firstOrCreate(
            ['email' => 'player.chat1@demo.local'],
            ['name' => 'Player Chat One', 'password' => Hash::make('password')]
        );
        $userB = User::firstOrCreate(
            ['email' => 'player.chat2@demo.local'],
            ['name' => 'Player Chat Two', 'password' => Hash::make('password')]
        );

        // Attach 'player' role if it exists (not strictly required for /player routes but helpful)
        $playerRoleId = optional(Role::where('name', 'player')->first())->id;
        if ($playerRoleId) {
            $userA->roles()->syncWithoutDetaching([$playerRoleId]);
            $userB->roles()->syncWithoutDetaching([$playerRoleId]);
        }

        // 2) Create minimal Club/Team/Player relations so TeamChat authorization passes
        // Create a club admin user for the club (clubs.user_id is required)
        $clubAdmin = User::firstOrCreate(
            ['email' => 'club.chat.admin@demo.local'],
            ['name' => 'Chat Demo Club Admin', 'password' => Hash::make('password')]
        );
        $clubRoleId = optional(Role::where('name', 'club')->first())->id;
        if ($clubRoleId) {
            $clubAdmin->roles()->syncWithoutDetaching([$clubRoleId]);
        }

        $sportId = Sport::query()->value('id'); // nullable if none

        $club = Club::firstOrCreate(
            ['email' => 'club.chat.demo@demo.local'],
            [
                'name' => 'Chat Demo Club',
                'user_id' => $clubAdmin->id,
                'social_links' => [],
                'is_registered' => 1,
                'sport_id' => $sportId,
            ]
        );

        // Create players mapped to these users
        $playerA = Player::firstOrCreate(
            ['email' => $userA->email],
            ['name' => $userA->name, 'user_id' => $userA->id, 'club_id' => $club->id]
        );
        $playerB = Player::firstOrCreate(
            ['email' => $userB->email],
            ['name' => $userB->name, 'user_id' => $userB->id, 'club_id' => $club->id]
        );

        // Ensure a team exists and both players belong to it
        $team = Team::firstOrCreate(
            ['club_id' => $club->id, 'name' => 'Chat Demo Team'],
            ['description' => 'Team used for testing team chat.']
        );
        $team->players()->syncWithoutDetaching([$playerA->id, $playerB->id]);

        // 3) Seed an Individual chat between the two users
        $dmChat = Chat::firstOrCreate([
            'type' => 'individual',
            'title' => 'DM: '.$userA->name.' & '.$userB->name,
        ]);
        ChatParticipant::firstOrCreate(['chat_id' => $dmChat->id, 'user_id' => $userA->id]);
        ChatParticipant::firstOrCreate(['chat_id' => $dmChat->id, 'user_id' => $userB->id]);

        // Add a few example messages
        if (!Message::where('chat_id', $dmChat->id)->exists()) {
            Message::create([
                'chat_id' => $dmChat->id,
                'sender_id' => $userA->id,
                'receiver_id' => $userB->id,
                'content' => 'Hey! This is a seeded DM message.',
            ]);
            Message::create([
                'chat_id' => $dmChat->id,
                'sender_id' => $userB->id,
                'receiver_id' => $userA->id,
                'content' => 'Nice! I can see the chat working.',
            ]);
            Message::create([
                'chat_id' => $dmChat->id,
                'sender_id' => $userA->id,
                'receiver_id' => $userB->id,
                'content' => 'Let’s test broadcasting and the UI flow.',
            ]);
        }

        // 4) Seed a Team chat if chats.team_id column exists
        if (Schema::hasColumn('chats', 'team_id')) {
            $teamChat = Chat::firstOrCreate(
                ['team_id' => $team->id, 'type' => 'team'],
                ['title' => $team->name]
            );
        } else {
            // Fallback: create a team-typed chat without team_id (routes using TeamChatController expect team_id)
            $teamChat = Chat::firstOrCreate(
                ['type' => 'team', 'title' => $team->name.' (no team_id column)']
            );
        }

        ChatParticipant::firstOrCreate(['chat_id' => $teamChat->id, 'user_id' => $userA->id]);
        ChatParticipant::firstOrCreate(['chat_id' => $teamChat->id, 'user_id' => $userB->id]);

        if (!Message::where('chat_id', $teamChat->id)->exists()) {
            Message::create([
                'chat_id' => $teamChat->id,
                'sender_id' => $userA->id,
                'receiver_id' => null,
                'content' => 'Welcome to the team chat! (seeded)',
            ]);
            Message::create([
                'chat_id' => $teamChat->id,
                'sender_id' => $userB->id,
                'receiver_id' => null,
                'content' => 'Great, messages load correctly in the feed.',
            ]);
        }

        $this->command?->info('Chat demo data seeded. Users: player.chat1@demo.local / player.chat2@demo.local (password: password)');
        $this->command?->info('Team: "'.$team->name.'" under club "'.$club->name.'"');
    }
}
