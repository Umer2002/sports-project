<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Reward, PlayerReward, User};

class RewardsSeeder extends Seeder
{
    public function run(): void
    {
        $awards = [
            [
                'name' => 'New Recruit',
                'type' => 'badge',
                'image' => 'assets/player-dashboard/images/NewRecruit.png',
                'achievement' => 'Welcome to the team! Completing your profile and joining your first club shows your commitment and enthusiasm to be part of something amazing.'
            ],
            [
                'name' => 'Loyal Player',
                'type' => 'badge',
                'image' => 'assets/player-dashboard/images/NewRecruit3.png',
                'achievement' => 'This award is a tribute to your dedication and loyalty. Staying active for months proves your passion and love for the game.'
            ],
            [
                'name' => 'Team Spirit',
                'type' => 'badge',
                'image' => 'assets/player-dashboard/images/NewRecruit4.png',
                'achievement' => 'Your commitment to practices and games shows you are a true team player. This award celebrates your reliability and energy.'
            ],
            [
                'name' => 'Rising Star',
                'type' => 'badge',
                'image' => 'assets/player-dashboard/images/NewRecruit5.png',
                'achievement' => 'A testament to hard work and steady improvement. You are becoming a standout player—keep reaching for the top!'
            ],
            [
                'name' => 'Goal Getter',
                'type' => 'badge',
                'image' => 'assets/player-dashboard/images/NewRecruit6.png',
                'achievement' => 'Scoring goals changes games. This badge celebrates your ability to step up and deliver when it matters.'
            ],
            [
                'name' => 'Assist Queen',
                'type' => 'badge',
                'image' => 'assets/player-dashboard/images/NewRecruit2.png',
                'achievement' => 'You help others shine by creating chances. Your vision and generosity power your team’s success.'
            ],
            [
                'name' => 'Assist King',
                'type' => 'badge',
                'image' => 'assets/player-dashboard/images/NewRecruit7.png',
                'achievement' => 'Leader in assists—your playmaking sets the stage for victory and lifts the whole squad.'
            ],
            [
                'name' => 'MVP of the Month',
                'type' => 'badge',
                'image' => 'assets/player-dashboard/images/NewRecruit8.png',
                'achievement' => 'Voted most valuable by teammates and fans. Your effort, skill, and leadership stand out.'
            ],
        ];

        foreach ($awards as $data) {
            Reward::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        // Optionally attach a few example rewards to the first non-admin user
        $user = User::where('is_admin', 0)->first();
        if ($user) {
            $rewardIds = Reward::whereIn('name', ['New Recruit', 'Team Spirit', 'Rising Star'])->pluck('id');
            foreach ($rewardIds as $rid) {
                PlayerReward::firstOrCreate([
                    'user_id' => $user->id,
                    'reward_id' => $rid,
                ], [
                    'issued_by' => null,
                    'status' => 'active',
                ]);
            }
        }
    }
}

