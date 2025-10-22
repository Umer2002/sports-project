<?php

namespace App\Services\Tournament;

use App\Models\AgeGroup;
use App\Models\Club;
use App\Models\Event;
use App\Models\GameMatch;
use App\Models\Gender;
use App\Models\SportClassificationOption;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\Venue;
use App\Models\VenueAvailability;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class TournamentCreationService
{
    private array $defaultTimeSlots = [
        ['10:00:00', '12:00:00'],
        ['14:00:00', '16:00:00'],
        ['18:00:00', '20:00:00'],
    ];

    /**
     * Create a tournament with shared business rules across roles.
     */
    public function create(array $data, Club $hostClub, array $options = []): Tournament
    {
        $options = array_merge([
            'restrictTeamsToHostClub' => false,
            'clubLabel' => 'the host club',
            'timeSlots' => $this->defaultTimeSlots,
            'createFallbackVenue' => false,
        ], $options);

        $data['host_club_id'] = $hostClub->id;

        $classificationOptionIds = collect($data['classification_option_ids'] ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $genderId = $data['gender_id'] ?? null;
        $ageGroupId = $data['age_group_id'] ?? null;

        $this->assertEligibility($hostClub, $genderId, $ageGroupId, $classificationOptionIds, $options);

        $teams = $this->getTeamsForTournament(
            $data['team_ids'] ?? [],
            $hostClub,
            $options,
            $genderId,
            $ageGroupId,
            $classificationOptionIds
        );

        $this->ensureValidFormat($data['tournament_format_id'] ?? 0, $data['team_ids'] ?? []);

        return DB::transaction(function () use ($data, $classificationOptionIds, $teams, $hostClub, $options) {
            $tournament = Tournament::create(
                Arr::except($data, ['team_ids', 'classification_option_ids'])
            );

            $teamIds = $data['team_ids'] ?? [];
            $tournament->teams()->sync($teamIds);
            $tournament->classificationOptions()->sync($classificationOptionIds->all());

            $fixtures = $this->generateFixtures($data['tournament_format_id'], $teamIds);

            $this->scheduleMatches($tournament, $fixtures, $teams, $hostClub, $options);

            return $tournament;
        });
    }

    /**
     * Ensure the tournament format requirements are satisfied.
     */
    public function ensureValidFormat(int $formatId, array $teamIds): void
    {
        $teamCount = count($teamIds);

        if ($formatId === 1 || $formatId === 2) {
            if ($teamCount < 2) {
                throw ValidationException::withMessages([
                    'team_ids' => 'This format requires at least 2 teams.',
                ]);
            }
            return;
        }

        if ($formatId === 3) {
            if ($teamCount < 4) {
                throw ValidationException::withMessages([
                    'team_ids' => 'Group Stage requires at least 4 teams.',
                ]);
            }
            return;
        }

        throw ValidationException::withMessages([
            'tournament_format_id' => 'Invalid tournament format.',
        ]);
    }

    private function assertEligibility(
        Club $hostClub,
        ?int $genderId,
        ?int $ageGroupId,
        Collection $classificationOptionIds,
        array $options
    ): void {
        $clubSportPhrase = ($options['clubLabel'] ?? 'the club') . ' sport';

        if ($genderId && ! Gender::where('sport_id', $hostClub->sport_id)->whereKey($genderId)->exists()) {
            throw ValidationException::withMessages([
                'gender_id' => 'Selected gender is not available for ' . $clubSportPhrase . '.',
            ]);
        }

        if ($ageGroupId && ! AgeGroup::where('sport_id', $hostClub->sport_id)->whereKey($ageGroupId)->exists()) {
            throw ValidationException::withMessages([
                'age_group_id' => 'Selected age group is not available for ' . $clubSportPhrase . '.',
            ]);
        }

        if ($classificationOptionIds->isNotEmpty()) {
            $invalidOptionIds = SportClassificationOption::whereIn('id', $classificationOptionIds)
                ->whereDoesntHave('group', fn ($query) => $query->where('sport_id', $hostClub->sport_id))
                ->pluck('id');

            if ($invalidOptionIds->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'classification_option_ids' => 'One or more sport options are not available for ' . $clubSportPhrase . '.',
                ]);
            }
        }
    }

    private function getTeamsForTournament(
        array $teamIds,
        Club $hostClub,
        array $options,
        ?int $genderId,
        ?int $ageGroupId,
        Collection $classificationOptionIds
    ): Collection {
        $teamIds = collect($teamIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($teamIds)) {
            throw ValidationException::withMessages([
                'team_ids' => 'Please select valid teams for this tournament.',
            ]);
        }

        $teams = Team::whereIn('id', $teamIds)->get();

        $missing = array_diff($teamIds, $teams->pluck('id')->all());
        if (! empty($missing)) {
            throw ValidationException::withMessages([
                'team_ids' => 'Please select valid teams for this tournament.',
            ]);
        }

        $clubLabel = $options['clubLabel'] ?? 'the club';
        $clubSportPhrase = $clubLabel . ' sport';

        if (! empty($options['restrictTeamsToHostClub'])) {
            $invalidTeam = $teams->first(fn ($team) => $team->club_id !== $hostClub->id);
            if ($invalidTeam) {
                throw ValidationException::withMessages([
                    'team_ids' => 'You can only select teams that are registered under ' . $clubLabel . '.',
                ]);
            }
        }

        $sportMismatch = $teams->first(fn ($team) => $team->sport_id !== $hostClub->sport_id);
        if ($sportMismatch) {
            throw ValidationException::withMessages([
                'team_ids' => 'All teams must match ' . $clubSportPhrase . '.',
            ]);
        }

        if ($genderId) {
            $genderMismatch = $teams->first(fn ($team) => (int) $team->gender_id !== (int) $genderId);
            if ($genderMismatch) {
                throw ValidationException::withMessages([
                    'team_ids' => 'Selected teams must match the chosen gender category.',
                ]);
            }
        }

        if ($ageGroupId) {
            $ageMismatch = $teams->first(fn ($team) => (int) $team->age_group_id !== (int) $ageGroupId);
            if ($ageMismatch) {
                throw ValidationException::withMessages([
                    'team_ids' => 'Selected teams must match the chosen age group.',
                ]);
            }
        }

        if ($classificationOptionIds->isNotEmpty()) {
            $teams->loadMissing('classificationOptions:id');
            $classificationMismatch = $teams->first(function ($team) use ($classificationOptionIds) {
                $teamOptionIds = $team->classificationOptions
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id);

                return $classificationOptionIds->diff($teamOptionIds)->isNotEmpty();
            });

            if ($classificationMismatch) {
                throw ValidationException::withMessages([
                    'team_ids' => 'Selected teams must satisfy the chosen sport option filters.',
                ]);
            }
        }

        return $teams;
    }

    private function generateFixtures(int $formatId, array $teamIds): array
    {
        return match ($formatId) {
            1 => $this->generateRoundRobin($teamIds),
            2 => $this->generateKnockout($teamIds),
            3 => $this->generateGroupStage($teamIds, 2),
            default => throw ValidationException::withMessages([
                'tournament_format_id' => 'Invalid tournament format.',
            ]),
        };
    }

    private function scheduleMatches(
        Tournament $tournament,
        array $fixtures,
        Collection $teams,
        Club $hostClub,
        array $options
    ): void {
        $dates = CarbonPeriod::create($tournament->start_date, $tournament->end_date)->toArray();

        if (empty($dates)) {
            throw new RuntimeException('No dates available to schedule matches inside the tournament range.');
        }

        $timeSlots = $options['timeSlots'] ?? $this->defaultTimeSlots;
        if (empty($timeSlots)) {
            throw new RuntimeException('No time slots configured for tournament scheduling.');
        }

        $venues = $this->resolveVenues($tournament, $hostClub, $options);
        if ($venues->isEmpty()) {
            throw new RuntimeException('No venues available to schedule matches.');
        }

        $teamMap = $teams->keyBy('id');
        $datesCount = count($dates);
        $dateIdx = 0;

        foreach ($fixtures as $fixture) {
            if (count($fixture) === 3) {
                [$_group, $homeId, $awayId] = $fixture;
            } else {
                [$homeId, $awayId] = $fixture;
            }

            if ($homeId === $awayId) {
                throw new RuntimeException('Home and away teams cannot be the same.');
            }

            $homeTeam = $teamMap->get($homeId);
            $awayTeam = $teamMap->get($awayId);

            if (! $homeTeam || ! $awayTeam) {
                throw new RuntimeException('One or both teams not found when scheduling matches.');
            }

            $scheduled = false;
            $cycleOffset = 0;

            while (! $scheduled && $cycleOffset < $datesCount) {
                $matchDate = $dates[($dateIdx + $cycleOffset) % $datesCount]->toDateString();

                foreach ($venues as $venue) {
                    foreach ($timeSlots as [$slotStart, $slotEnd]) {
                        if ($this->hasVenueConflict($venue->id, $matchDate, $slotStart, $slotEnd)) {
                            continue;
                        }

                        VenueAvailability::create([
                            'venue_id' => $venue->id,
                            'available_date' => $matchDate,
                            'start_time' => $slotStart,
                            'end_time' => $slotEnd,
                        ]);

                        Event::create([
                            'group_id' => $tournament->id,
                            'title' => $homeTeam->name . ' vs ' . $awayTeam->name,
                            'description' => "Match of {$tournament->name}",
                            'event_date' => $matchDate,
                            'event_time' => $slotStart,
                            'location' => $venue->id,
                            'start' => Carbon::parse("$matchDate $slotStart"),
                            'end' => Carbon::parse("$matchDate $slotEnd"),
                            'type' => 'tournament_match',
                            'privacy' => 'public',
                        ]);

                        GameMatch::create([
                            'tournament_id' => $tournament->id,
                            'home_club_id' => $homeTeam->club_id,
                            'away_club_id' => $awayTeam->club_id,
                            'match_date' => $matchDate,
                            'match_time' => $slotStart,
                            'venue' => $venue->id,
                            'score' => null,
                            'referee_id' => null,
                        ]);

                        $scheduled = true;
                        break 2;
                    }
                }

                if (! $scheduled) {
                    $cycleOffset++;
                }
            }

            if (! $scheduled) {
                throw new RuntimeException('Unable to schedule all matches inside the given dates. Please ensure you have sufficient venues and time slots available.');
            }

            $dateIdx = ($dateIdx + 1) % $datesCount;
        }
    }

    private function resolveVenues(Tournament $tournament, Club $hostClub, array $options): Collection
    {
        $tournament->loadMissing('venue');

        $venues = collect();

        if ($tournament->venue) {
            $venues = collect([$tournament->venue]);
        }

        if ($venues->isEmpty() && $tournament->city_id) {
            $venues = Venue::where('city_id', $tournament->city_id)
                ->orderBy('name')
                ->get();
        }

        if ($venues->isEmpty()) {
            $venues = Venue::orderBy('name')->get();
        }

        if ($venues->isEmpty() && ! empty($options['createFallbackVenue'])) {
            $venues = collect([
                Venue::create([
                    'name' => $hostClub->name . ' Field',
                    'location' => $tournament->location,
                    'capacity' => 100,
                    'description' => 'Default venue for ' . $hostClub->name,
                ]),
            ]);
        }

        return $venues->values();
    }

    private function hasVenueConflict(int $venueId, string $date, string $slotStart, string $slotEnd): bool
    {
        return VenueAvailability::where('venue_id', $venueId)
            ->where('available_date', $date)
            ->where(function ($q) use ($slotStart, $slotEnd) {
                $q->whereBetween('start_time', [$slotStart, $slotEnd])
                    ->orWhereBetween('end_time', [$slotStart, $slotEnd])
                    ->orWhere(function ($q) use ($slotStart, $slotEnd) {
                        $q->where('start_time', '<=', $slotStart)
                            ->where('end_time', '>=', $slotEnd);
                    });
            })
            ->exists();
    }

    private function generateRoundRobin(array $teamIds): array
    {
        if (count($teamIds) % 2 !== 0) {
            $teamIds[] = null;
        }

        $n = count($teamIds);
        $rounds = $n - 1;
        $half = $n / 2;
        $fixtures = [];

        for ($round = 0; $round < $rounds; $round++) {
            for ($i = 0; $i < $half; $i++) {
                $home = $teamIds[$i];
                $away = $teamIds[$n - 1 - $i];

                if ($home !== null && $away !== null) {
                    $fixtures[] = [$home, $away];
                }
            }

            $teamIds = array_merge(
                [$teamIds[0]],
                [$teamIds[$n - 1]],
                array_slice($teamIds, 1, $n - 2)
            );
        }

        return $fixtures;
    }

    private function generateKnockout(array $teamIds): array
    {
        $teams = $teamIds;
        shuffle($teams);
        $fixtures = [];

        while (count($teams) > 1) {
            $nextRound = [];
            $count = count($teams);

            if ($count % 2 !== 0) {
                $nextRound[] = array_pop($teams);
                $count--;
            }

            for ($i = 0; $i < $count / 2; $i++) {
                $home = $teams[$i];
                $away = $teams[$count - 1 - $i];
                $fixtures[] = [$home, $away];
                $nextRound[] = $home;
            }

            $teams = $nextRound;
        }

        return $fixtures;
    }

    private function generateGroupStage(array $teamIds, int $groupsCount = 2): array
    {
        $teams = $teamIds;
        shuffle($teams);

        $groups = array_chunk($teams, (int) ceil(count($teams) / $groupsCount));
        $fixtures = [];

        foreach ($groups as $groupIndex => $groupTeams) {
            $groupFixtures = $this->generateRoundRobin($groupTeams);

            foreach ($groupFixtures as $fixture) {
                $fixtures[] = [$groupIndex + 1, $fixture[0], $fixture[1]];
            }
        }

        return $fixtures;
    }
}
