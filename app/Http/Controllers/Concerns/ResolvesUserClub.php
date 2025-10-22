<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait ResolvesUserClub
{
    protected function resolveClubForUser(?User $user = null): ?Club
    {
        $user = $user ?? Auth::user();

        if (! $user) {
            return null;
        }

        $user->loadMissing(['club', 'coach.teams.club']);

        if ($user->club) {
            return $user->club;
        }

        $coach = $user->coach;
        if (! $coach) {
            return null;
        }

        $team = $coach->teams->first();
        if ($team && $team->relationLoaded('club')) {
            return $team->club;
        }

        return $coach->teams()->with('club')->first()?->club;
    }

    protected function requireClubForUser(?User $user = null): Club
    {
        $club = $this->resolveClubForUser($user);

        if (! $club) {
            abort(403, 'Club association required to manage teams.');
        }

        return $club;
    }
}

