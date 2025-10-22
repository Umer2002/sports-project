<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayoutPlansTableSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            ['player_count' => 50, 'payout_amount' => 500],
            ['player_count' => 60, 'payout_amount' => 586],
            ['player_count' => 70, 'payout_amount' => 672],
            ['player_count' => 80, 'payout_amount' => 757],
            ['player_count' => 90, 'payout_amount' => 843],
            ['player_count' => 100, 'payout_amount' => 929],
            ['player_count' => 110, 'payout_amount' => 1015],
            ['player_count' => 120, 'payout_amount' => 1101],
            ['player_count' => 130, 'payout_amount' => 1187],
            ['player_count' => 140, 'payout_amount' => 1272],
            ['player_count' => 150, 'payout_amount' => 1358],
            ['player_count' => 160, 'payout_amount' => 1444],
            ['player_count' => 170, 'payout_amount' => 1530],
            ['player_count' => 180, 'payout_amount' => 1616],
            ['player_count' => 190, 'payout_amount' => 1702],
        ];

        DB::table('payout_plans')->insert($plans);
    }
}
