<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GenderTableSeeder extends Seeder
{
    public function run(): void
    {
        $sportIds = DB::table('sports')->pluck('id');

        if ($sportIds->isEmpty()) {
            return;
        }

        $now = Carbon::now();

        $genders = [
            ['code' => 'BOYS', 'label' => 'Boys', 'sort_order' => 10],
            ['code' => 'GIRLS', 'label' => 'Girls', 'sort_order' => 20],
            ['code' => 'MEN', 'label' => 'Men', 'sort_order' => 30],
            ['code' => 'WOMEN', 'label' => 'Women', 'sort_order' => 40],
            ['code' => 'COED', 'label' => 'Co-ed', 'sort_order' => 50],
            ['code' => 'OPEN', 'label' => 'Open / Unspecified', 'sort_order' => 60],
        ];

        $payload = [];
        foreach ($sportIds as $sportId) {
            foreach ($genders as $gender) {
                $payload[] = [
                    'sport_id' => $sportId,
                    'code' => $gender['code'],
                    'label' => $gender['label'],
                    'sort_order' => $gender['sort_order'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('genders')->upsert(
            $payload,
            ['sport_id', 'code'],
            ['label', 'sort_order', 'updated_at']
        );
    }
}
