<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Large seeders (cities, classifications, etc.) need extra headroom.
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        // User::factory(10)->create();
        $this->call([
            RolesSeeder::class,
            SportsTableSeeder::class,
            DashboardSeeder::class,

            PositionsTableSeeder::class,
            AgeGroup::class,
            StatsTableSeeder::class,
            ClubSlugBackfillSeeder::class,
            CitiesTableChunkOneSeeder::class,
            CitiesTableChunkTwoSeeder::class,
            CitiesTableChunkThreeSeeder::class,
            CitiesTableChunkFourSeeder::class,
            CitiesTableChunkFiveSeeder::class,
            PayoutPlansTableSeeder::class,
            CountriesTableSeeder::class,
            StateSeeder::class,
            GenderTableSeeder::class,
            TeamCreationTestSeeder::class,
            ExpertiseSeeder::class,
            BaseballClassificationsSeeder::class,
            BasketballClassificationsSeeder::class,
            BoxingClassificationsSeeder::class,
            FieldHockeyClassificationsSeeder::class,
            GolfClassificationsSeeder::class,
            GymnasticsClassificationsSeeder::class,
            HockeyClassificationsSeeder::class,
            MMAClassificationsSeeder::class,
            RugbyClassificationsSeeder::class,
            SoccerClassificationsSeeder::class,
            TrackAndFieldClassificationsSeeder::class,
            SwimmingClassificationsSeeder::class,
            TennisClassificationsSeeder::class,
            VolleyballClassificationsSeeder::class,
            WrestlingClassificationsSeeder::class,
            DivisionSeeder::class,
            RewardsSeeder::class,
            BlogSeeder::class,
            ReferralDemoSeeder::class,

            ChatDemoSeeder::class,
            ApplicationSeeder::class,
            GenerateReferralCodesSeeder::class,
        ]);
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
