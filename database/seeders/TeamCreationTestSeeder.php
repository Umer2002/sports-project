<?php

namespace Database\Seeders;

use App\Models\AgeGroup as AgeGroupModel;
use App\Models\Club;
use App\Models\Gender;
use App\Models\Player;
use App\Models\Sport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamCreationTestSeeder extends Seeder
{
    public function run(): void
    {
        $sport = Sport::firstOrCreate(
            ['name' => 'Soccer'],
            ['description' => 'Seeded soccer sport for team wizard testing.']
        );

        $ageGroups = AgeGroupModel::where('sport_id', $sport->id)->ordered()->get();
        if ($ageGroups->isEmpty()) {
            $this->command?->warn('TeamCreationTestSeeder: No age groups found for '.$sport->name.'.');
            return;
        }

        $genderRecords = Gender::where('sport_id', $sport->id)
            ->get()
            ->keyBy(fn (Gender $gender) => strtolower($gender->code));

        if ($genderRecords->isEmpty()) {
            $this->command?->warn('TeamCreationTestSeeder: No genders found for '.$sport->name.'.');
            return;
        }

        $clubUser = User::firstOrCreate(
            ['email' => 'team-creation-club-admin@example.test'],
            [
                'name' => 'Team Creation Club Admin',
                'password' => 'password',
            ]
        );

        $club = Club::updateOrCreate(
            ['email' => 'team-creation-club@example.test'],
            [
                'name' => 'Team Creation Test Club',
                'sport_id' => $sport->id,
                'user_id' => $clubUser->id,
                'social_links' => [],
                'phone' => '555-0100',
                'address' => '123 Practice Field Dr, Sample City',
                'bio' => 'Seed club with deep roster for team creation wizard testing.',
                'paypal_link' => null,
                'joining_url' => null,
                'invite_token' => (string) Str::uuid(),
                'invites_count' => 0,
                'is_registered' => true,
                'registration_date' => Carbon::now()->subDays(10),
            ]
        );

        $sequence = 1;
        foreach ($ageGroups as $ageGroup) {
            $genderCodes = $this->genderCodesForGroup($ageGroup, $genderRecords->keys()->all());

            foreach ($genderCodes as $code) {
                $gender = $genderRecords->get($code);

                if (! $gender) {
                    continue;
                }

                $this->seedPlayersForCombination($club, $sport, $ageGroup, $gender, $sequence);
            }
        }
    }

    protected function seedPlayersForCombination(Club $club, Sport $sport, AgeGroupModel $ageGroup, Gender $gender, int &$sequence): void
    {
        $playersPerCombination = 6;

        for ($i = 1; $i <= $playersPerCombination; $i++, $sequence++) {
            $age = $this->resolveAgeForGroup($ageGroup);
            $birthday = Carbon::now()
                ->subYears($age)
                ->subMonths(random_int(0, 11))
                ->subDays(random_int(0, 27));

            $email = sprintf('teamtest+%s-%s-%02d@example.test', strtolower($gender->code), strtolower($ageGroup->code), $i);

            $playerUser = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => sprintf('%s %s Player %02d', $gender->label, $ageGroup->code, $i),
                    'password' => 'password',
                ]
            );

            Player::updateOrCreate(
                ['email' => $email],
                [
                    'name' => sprintf('%s %s Player %02d', $gender->label, $ageGroup->code, $i),
                    'user_id' => $playerUser->id,
                    'gender' => $gender->label,
                    'age' => $age,
                    'birthday' => $birthday,
                    'club_id' => $club->id,
                    'sport_id' => $sport->id,
                    'phone' => sprintf('555-1%03d', $sequence),
                    'bio' => sprintf(
                        'Seeder generated %s player for %s testing.',
                        strtolower($gender->label),
                        $ageGroup->label
                    ),
                ]
            );
        }
    }

    protected function resolveAgeForGroup(AgeGroupModel $ageGroup): int
    {
        $min = $ageGroup->min_age_years;
        $max = $ageGroup->max_age_years;

        if (! is_null($min) && ! is_null($max)) {
            return random_int($min, $max);
        }

        if (! is_null($min)) {
            return random_int($min, $min + 8);
        }

        if (! is_null($max)) {
            return random_int(max(3, $max - 3), $max);
        }

        return random_int(18, 32);
    }

    protected function genderCodesForGroup(AgeGroupModel $ageGroup, array $availableCodes): array
    {
        $has = fn (string $request): bool => in_array($request, $availableCodes, true);
        $code = strtolower($ageGroup->code ?? '');

        if ($code === 'open' || $ageGroup->is_open_ended) {
            return array_values(array_filter(['men', 'women', 'coed', 'open'], $has));
        }

        if ($code === 'masters' || ($ageGroup->min_age_years && $ageGroup->min_age_years >= 18)) {
            return array_values(array_filter(['men', 'women', 'coed'], $has));
        }

        if ($ageGroup->max_age_years !== null && $ageGroup->max_age_years <= 12) {
            return array_values(array_filter(['boys', 'girls', 'coed'], $has));
        }

        return array_values(array_filter(['boys', 'girls', 'men', 'women', 'coed'], $has));
    }
}
