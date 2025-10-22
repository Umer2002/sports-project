<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;
use App\Models\Game;
use App\Models\Referee;
use App\Models\User;
use App\Models\Club;
use Illuminate\Support\Str;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        $requiredClubs = 4;
        $currentClubs = Club::count();
        if ($currentClubs < $requiredClubs) {
            Club::factory()->count($requiredClubs - $currentClubs)->create();
        }
        $clubs = Club::all();

        $requiredReferees = 3;
        $currentReferees = Referee::count();
        if ($currentReferees < $requiredReferees) {
            $clubsForReferees = $clubs->isNotEmpty() ? $clubs : Club::factory()->count(2)->create();

            foreach (range(1, $requiredReferees - $currentReferees) as $i) {
                $name = $faker->name();
                $email = $faker->unique()->safeEmail();

                $user = User::factory()->create([
                    'name' => $name,
                    'email' => $email,
                ]);

                $club = $clubsForReferees->random();

                Referee::create([
                    'user_id' => $user->id,
                    'club_id' => $club->id,
                    'full_name' => $name,
                    'email' => $email,
                    'phone' => $faker->phoneNumber(),
                    'preferred_contact_method' => $faker->randomElement(['email', 'phone']),
                    'city' => $faker->city(),
                    'region' => $faker->state(),
                    'country' => $faker->country(),
                    'license_type' => $faker->randomElement(['Pro', 'Elite', 'Regional']),
                    'certification_level' => $faker->numberBetween(1, 4),
                    'certifying_body' => $faker->company(),
                    'license_expiry_date' => now()->addMonths($faker->numberBetween(6, 18))->toDateString(),
                    'background_check_passed' => true,
                    'liability_insurance' => $faker->boolean(),
                    'sports_officiated' => [$faker->randomElement(['soccer', 'basketball', 'baseball', 'rugby'])],
                    'account_status' => 'active',
                ]);
            }

            $faker->unique(true);
        }

        $referees = Referee::all();
        if ($clubs->count() < 2 || $referees->isEmpty()) {
            return;
        }

        $requiredMatches = 5;
        $currentMatches = Game::count();
        if ($currentMatches < $requiredMatches) {
            foreach (range(1, $requiredMatches - $currentMatches) as $i) {
                $homeClub = $clubs->random();
                $awayCandidates = $clubs->where('id', '!=', $homeClub->id);
                if ($awayCandidates->isEmpty()) {
                    $awayClub = Club::factory()->create();
                    $clubs->push($awayClub);
                } else {
                    $awayClub = $awayCandidates->random();
                }

                Game::create([
                    'home_club_id' => $homeClub->id,
                    'away_club_id' => $awayClub->id,
                    'match_date' => now()->addDays($i + rand(1, 5))->toDateString(),
                    'match_time' => now()->setHour(rand(13, 20))->setMinute(rand(0, 59))->format('H:i:s'),
                    'venue' => 'Venue ' . Str::upper(Str::random(4)),
                    'age_group' => 'U' . $faker->randomElement([10, 12, 14, 16, 18]),
                    'required_referee_level' => $faker->numberBetween(0, 3),
                ]);
            }
        }

        $matches = Game::all();

        foreach ($matches as $match) {
            foreach ($referees as $referee) {
                Application::updateOrCreate(
                    [
                        'match_id' => $match->id,
                        'referee_id' => $referee->id,
                    ],
                    [
                        'status' => 'pending',
                        'applied_at' => now()->subDays(rand(0, 7))->setTime(rand(8, 20), rand(0, 59)),
                        'priority_score' => rand(10, 100),
                    ]
                );
            }
        }
    }
}
