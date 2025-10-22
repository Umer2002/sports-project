<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpertiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $expertiseLevels = [
            [
                'expertise_level' => 'Beginner',
                'description' => 'Entry-level referee with basic knowledge of rules and game management',
            ],
            [
                'expertise_level' => 'Intermediate',
                'description' => 'Experienced referee with good understanding of advanced rules and game flow',
            ],
            [
                'expertise_level' => 'Advanced',
                'description' => 'Highly skilled referee capable of handling complex game situations',
            ],
            [
                'expertise_level' => 'Professional',
                'description' => 'Expert referee qualified for professional and high-level competitions',
            ],
        ];

        foreach ($expertiseLevels as $level) {
            \App\Models\Expertise::create($level);
        }
    }
}
