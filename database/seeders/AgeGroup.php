<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AgeGroup extends Seeder
{
    public function run(): void
    {
        $sportIds = DB::table('sports')->pluck('id');
        if ($sportIds->isEmpty()) {
            return;
        }

        $now = Carbon::now();
        $baseGroups = [
            ['code' => 'U5', 'label' => 'Under 5', 'min_age_years' => 3, 'max_age_years' => 4, 'is_open_ended' => 0, 'sort_order' => 10],
            ['code' => 'U7', 'label' => 'Under 7', 'min_age_years' => 5, 'max_age_years' => 6, 'is_open_ended' => 0, 'sort_order' => 20],
            ['code' => 'U9', 'label' => 'Under 9', 'min_age_years' => 7, 'max_age_years' => 8, 'is_open_ended' => 0, 'sort_order' => 30],
            ['code' => 'U11', 'label' => 'Under 11', 'min_age_years' => 9, 'max_age_years' => 10, 'is_open_ended' => 0, 'sort_order' => 40],
            ['code' => 'U13', 'label' => 'Under 13', 'min_age_years' => 11, 'max_age_years' => 12, 'is_open_ended' => 0, 'sort_order' => 50],
            ['code' => 'U15', 'label' => 'Under 15', 'min_age_years' => 13, 'max_age_years' => 14, 'is_open_ended' => 0, 'sort_order' => 60],
            ['code' => 'U17', 'label' => 'Under 17', 'min_age_years' => 15, 'max_age_years' => 16, 'is_open_ended' => 0, 'sort_order' => 70],
            ['code' => 'U19', 'label' => 'Under 19', 'min_age_years' => 17, 'max_age_years' => 18, 'is_open_ended' => 0, 'sort_order' => 80],
            ['code' => 'OPEN', 'label' => 'Open Division', 'min_age_years' => null, 'max_age_years' => null, 'is_open_ended' => 1, 'sort_order' => 90],
            ['code' => 'MASTERS', 'label' => 'Masters', 'min_age_years' => 35, 'max_age_years' => null, 'is_open_ended' => 1, 'sort_order' => 100],
        ];

        $payload = [];
        foreach ($sportIds as $sportId) {
            foreach ($baseGroups as $group) {
                $payload[] = [
                    'sport_id' => $sportId,
                    'code' => $group['code'],
                    'label' => $group['label'],
                    'min_age_years' => $group['min_age_years'],
                    'max_age_years' => $group['max_age_years'],
                    'is_open_ended' => $group['is_open_ended'],
                    'context' => null,
                    'notes' => null,
                    'sort_order' => $group['sort_order'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('age_groups')->upsert(
            $payload,
            ['sport_id', 'code'],
            ['label', 'min_age_years', 'max_age_years', 'is_open_ended', 'context', 'notes', 'sort_order', 'updated_at']
        );
    }
}
