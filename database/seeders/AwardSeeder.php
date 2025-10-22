<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Award;

class AwardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $awards = [
            [
                'name' => 'New Recruit',
                'description' => 'Welcome to the team! This badge celebrates the first step in your journey.',
                'requirements' => '• Complete profile • Join first club',
                'rewards' => '• +250 XP • Frame • Feed highlight',
                'color' => '#007bff',
                'image' => 'https://via.placeholder.com/200x200/007bff/ffffff?text=New+Recruit',
                'is_active' => true,
            ],
            [
                'name' => 'Team Player',
                'description' => 'Recognized for exceptional teamwork and collaboration.',
                'requirements' => '• Participate in 5+ team events • Positive team feedback',
                'rewards' => '• +500 XP • Team badge • Recognition post',
                'color' => '#28a745',
                'image' => 'https://via.placeholder.com/200x200/28a745/ffffff?text=Team+Player',
                'is_active' => true,
            ],
            [
                'name' => 'Rising Star',
                'description' => 'Outstanding performance and dedication to improvement.',
                'requirements' => '• Consistent attendance • Skill improvement • Coach recommendation',
                'rewards' => '• +750 XP • Star badge • Featured profile',
                'color' => '#ffc107',
                'image' => 'https://via.placeholder.com/200x200/ffc107/000000?text=Rising+Star',
                'is_active' => true,
            ],
            [
                'name' => 'Leadership Excellence',
                'description' => 'Demonstrated leadership qualities and mentoring abilities.',
                'requirements' => '• Mentor new players • Lead team activities • Positive influence',
                'rewards' => '• +1000 XP • Leadership badge • Special recognition',
                'color' => '#dc3545',
                'image' => 'https://via.placeholder.com/200x200/dc3545/ffffff?text=Leadership',
                'is_active' => true,
            ],
            [
                'name' => 'Championship Winner',
                'description' => 'Victory in major tournaments and competitions.',
                'requirements' => '• Win tournament • Outstanding performance • Team contribution',
                'rewards' => '• +1500 XP • Championship badge • Trophy display',
                'color' => '#6f42c1',
                'image' => 'https://via.placeholder.com/200x200/6f42c1/ffffff?text=Champion',
                'is_active' => true,
            ],
            [
                'name' => 'Sportsmanship Award',
                'description' => 'Exemplary sportsmanship and fair play.',
                'requirements' => '• Fair play record • Respect for opponents • Positive attitude',
                'rewards' => '• +300 XP • Sportsmanship badge • Community recognition',
                'color' => '#17a2b8',
                'image' => 'https://via.placeholder.com/200x200/17a2b8/ffffff?text=Sportsmanship',
                'is_active' => true,
            ],
            [
                'name' => 'Training Dedication',
                'description' => 'Consistent attendance and effort in training sessions.',
                'requirements' => '• 90%+ attendance • Active participation • Skill development',
                'rewards' => '• +400 XP • Dedication badge • Training recognition',
                'color' => '#fd7e14',
                'image' => 'https://via.placeholder.com/200x200/fd7e14/ffffff?text=Dedication',
                'is_active' => true,
            ],
            [
                'name' => 'Community Builder',
                'description' => 'Contributed to building a positive team community.',
                'requirements' => '• Community engagement • Help other players • Positive influence',
                'rewards' => '• +600 XP • Community badge • Social recognition',
                'color' => '#20c997',
                'image' => 'https://via.placeholder.com/200x200/20c997/ffffff?text=Community',
                'is_active' => true,
            ],
        ];

        foreach ($awards as $award) {
            Award::create($award);
        }
    }
}