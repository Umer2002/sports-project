<?php

namespace App\Http\Controllers\Concerns;

use App\Models\AgeGroup;
use App\Models\Gender;
use App\Models\Player;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Collection;

trait HandlesTeamEligibility
{
    /**
     * Filter a collection of players down to those matching a team's age-group and gender settings.
     */
    protected function filterPlayersForTeam(Collection $players, Team $team): Collection
    {
        $ageGroup = $team->ageGroup;
        $gender = $team->genderCategory;

        return $players->filter(function (Player $player) use ($ageGroup, $gender) {
            if ($gender && ! $this->playerMatchesGender($player, $gender)) {
                return false;
            }

            if ($ageGroup && ! $this->playerMatchesAgeGroup($player, $ageGroup)) {
                return false;
            }

            return true;
        })->values();
    }

    /**
     * Determine whether the player fits the requested age group.
     */
    protected function playerMatchesAgeGroup(Player $player, AgeGroup $ageGroup): bool
    {
        $age = $this->resolvePlayerAge($player);

        if ($age === null) {
            return false;
        }

        if (! is_null($ageGroup->min_age_years) && $age < $ageGroup->min_age_years) {
            return false;
        }

        if (! is_null($ageGroup->max_age_years) && $age > $ageGroup->max_age_years) {
            return false;
        }

        return true;
    }

    /**
     * Resolve the most reliable age value for a player.
     */
    protected function resolvePlayerAge(Player $player): ?int
    {
        if (! is_null($player->age)) {
            return (int) $player->age;
        }

        if ($player->birthday) {
            return Carbon::parse($player->birthday)->age;
        }

        if (isset($player->birth_date) && $player->birth_date) {
            return Carbon::parse($player->birth_date)->age;
        }

        return null;
    }

    /**
     * Determine whether the player's gender matches the requested gender (if any).
     */
    protected function playerMatchesGender(Player $player, Gender $gender): bool
    {
        $teamGender = $this->normalizeGenderValue($gender->code) ?? $this->normalizeGenderValue($gender->label);

        if (! $teamGender) {
            return true;
        }

        if (in_array($teamGender, ['open', 'coed'], true)) {
            return true;
        }

        $playerGender = $this->normalizeGenderValue($player->gender);

        return $playerGender !== null && $playerGender === $teamGender;
    }

    /**
     * Normalize gender text into comparable tokens.
     */
    protected function normalizeGenderValue(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $normalized = strtolower(trim($value));

        $map = [
            'male' => 'men',
            'm' => 'men',
            'man' => 'men',
            'men' => 'men',
            'female' => 'women',
            'f' => 'women',
            'woman' => 'women',
            'women' => 'women',
            'girl' => 'girls',
            'girls' => 'girls',
            'boy' => 'boys',
            'boys' => 'boys',
            'coed' => 'coed',
            'co-ed' => 'coed',
            'mixed' => 'coed',
            'open' => 'open',
        ];

        return $map[$normalized] ?? $normalized;
    }
}
