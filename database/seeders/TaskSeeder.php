<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Models\Player;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and players to assign tasks to
        $users = User::whereHas('roles', function($query) {
            $query->where('name', 'player');
        })->limit(5)->get();

        if ($users->count() == 0) {
            $this->command->info('No players found. Skipping task seeding.');
            return;
        }

        $tasks = [
            [
                'title' => 'Complete fitness assessment',
                'description' => 'Complete the quarterly fitness assessment and submit results to coaching staff.',
                'status' => 'pending',
                'priority' => 'high',
                'assigned_to' => $users->random()->id,
            ],
            [
                'title' => 'Review game footage',
                'description' => 'Watch and analyze the last game footage, focusing on defensive positioning.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'assigned_to' => $users->random()->id,
            ],
            [
                'title' => 'Attend team meeting',
                'description' => 'Attend the weekly team meeting on Friday at 6 PM.',
                'status' => 'completed',
                'priority' => 'high',
                'assigned_to' => $users->random()->id,
            ],
            [
                'title' => 'Update player profile',
                'description' => 'Update your player profile with recent achievements and stats.',
                'status' => 'pending',
                'priority' => 'low',
                'assigned_to' => $users->random()->id,
            ],
            [
                'title' => 'Practice penalty kicks',
                'description' => 'Spend 30 minutes practicing penalty kicks before the next training session.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'assigned_to' => $users->random()->id,
            ],
            [
                'title' => 'Submit medical clearance',
                'description' => 'Submit updated medical clearance form to the team doctor.',
                'status' => 'pending',
                'priority' => 'high',
                'assigned_to' => $users->random()->id,
            ],
            [
                'title' => 'Team building activity',
                'description' => 'Participate in the team building activity scheduled for this weekend.',
                'status' => 'completed',
                'priority' => 'medium',
                'assigned_to' => $users->random()->id,
            ],
            [
                'title' => 'Nutrition consultation',
                'description' => 'Schedule and attend a nutrition consultation with the team nutritionist.',
                'status' => 'pending',
                'priority' => 'medium',
                'assigned_to' => $users->random()->id,
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'status' => $taskData['status'],
                'priority' => $taskData['priority'],
                'assigned_to' => $taskData['assigned_to'],
                'created_by' => User::whereHas('roles', function($query) {
                    $query->where('name', 'coach');
                })->first()?->id ?? 1,
                'due_date' => now()->addDays(rand(1, 14)),
            ]);
        }

        $this->command->info('Created ' . count($tasks) . ' sample tasks.');
    }
}