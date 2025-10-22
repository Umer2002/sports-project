<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Sport;
use App\Models\State;
use App\Models\Tournament;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TournamentBrowseController extends Controller
{
    private const STATUS_LABELS = [
        'upcoming' => 'Upcoming',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'scheduled' => 'Scheduled',
    ];

    /**
     * Display the public tournament search experience.
     */
    public function index(Request $request): View
    {
        $sports = Sport::orderBy('name')->get();

        $search = trim((string) $request->query('q', ''));
        $sportId = $request->query('sport');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $countryFilter = $request->query('country');
        $stateFilter = $request->query('state');
        $cityFilter = $request->query('city');

        $countryId = $countryFilter !== null && $countryFilter !== '' ? $countryFilter : null;
        $stateId = $stateFilter !== null && $stateFilter !== '' ? $stateFilter : null;
        $cityId = $cityFilter !== null && $cityFilter !== '' ? $cityFilter : null;

        $tournaments = Tournament::query()
            ->with([
                'hostClub.sport',
                'teams.club',
                'games.homeClub',
                'games.awayClub',
                'country',
                'state',
                'city',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $like = "%{$search}%";

                $query->where(function ($inner) use ($like) {
                    $inner->where('name', 'like', $like)
                        ->orWhere('location', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhereHas('hostClub', function ($clubQuery) use ($like) {
                            $clubQuery->where('name', 'like', $like)
                                ->orWhere('address', 'like', $like);
                        })
                        ->orWhereHas('teams', function ($teamQuery) use ($like) {
                            $teamQuery->where(function ($teamScope) use ($like) {
                                $teamScope->where('name', 'like', $like)
                                    ->orWhereHas('club', function ($teamClubQuery) use ($like) {
                                        $teamClubQuery->where('name', 'like', $like)
                                            ->orWhere('address', 'like', $like);
                                    });
                            });
                        });
                });
            })
            ->when($sportId, function ($query) use ($sportId) {
                $query->where(function ($scope) use ($sportId) {
                    $scope->whereHas('hostClub', function ($clubQuery) use ($sportId) {
                        $clubQuery->where('sport_id', $sportId);
                    })->orWhereHas('teams', function ($teamQuery) use ($sportId) {
                        $teamQuery->where('sport_id', $sportId);
                    });
                });
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('start_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('end_date', '<=', $endDate);
            })
            ->when($countryId, function ($query) use ($countryId) {
                $query->where('country_id', $countryId);
            })
            ->when($stateId, function ($query) use ($stateId) {
                $query->where('state_id', $stateId);
            })
            ->when($cityId, function ($query) use ($cityId) {
                $query->where('city_id', $cityId);
            })
            ->orderByRaw('COALESCE(start_date, created_at) ASC')
            ->get();

        $resolvedCountryId = $countryId;
        $resolvedStateId = $stateId;
        $resolvedCityId = $cityId;

        if ($resolvedStateId && ! $resolvedCountryId) {
            $stateModel = State::find($resolvedStateId);
            if ($stateModel) {
                $resolvedCountryId = (string) $stateModel->country_id;
            }
        }

        if ($resolvedCityId && ! $resolvedStateId) {
            $cityModel = City::find($resolvedCityId);
            if ($cityModel) {
                $resolvedStateId = (string) $cityModel->state_id;
                $resolvedCountryId = $resolvedCountryId ?: (string) $cityModel->country_id;
            }
        }

        $countries = Country::orderBy('name')->get();
        $states = collect();
        $cities = collect();

        if ($resolvedCountryId) {
            $states = State::where('country_id', $resolvedCountryId)->orderBy('name')->get();
        }

        if ($resolvedStateId) {
            $cities = City::where('state_id', $resolvedStateId)->orderBy('name')->get();
        }

        $payloadCollection = $tournaments->map(fn (Tournament $tournament) => $this->transformTournament($tournament));

        $tournamentPayload = $payloadCollection->values();
        $tournamentLookup = $payloadCollection->keyBy('id');

        $filters = [
            'q' => $search,
            'sport' => $sportId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'country' => $resolvedCountryId,
            'state' => $resolvedStateId,
            'city' => $resolvedCityId,
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'tournaments' => $tournamentPayload->values()->all(),
                    'filters' => $filters,
                    'meta' => [
                        'count' => $tournaments->count(),
                    ],
                ],
            ]);
        }

        return view('tournaments.search', [
            'tournaments' => $tournaments,
            'tournamentPayload' => $tournamentPayload,
            'tournamentLookup' => $tournamentLookup,
            'sports' => $sports,
            'countries' => $countries,
            'states' => $states,
            'cities' => $cities,
            'filters' => $filters,
        ]);
    }

    /**
     * Prepare tournament details for the front-end experience.
     */
    private function transformTournament(Tournament $tournament): array
    {
        $matches = $tournament->games
            ->sortBy(fn ($game) => sprintf('%s %s', $game->match_date ?? '', $game->match_time ?? ''))
            ->values();

        $teamClubs = $tournament->teams
            ->pluck('club')
            ->filter();

        $scoreboard = $this->buildScoreboard($matches, $teamClubs);

        $start = $this->toCarbon($tournament->start_date);
        $end = $this->toCarbon($tournament->end_date);
        $status = $this->determineStatus($start, $end);

        $upcomingMatch = $matches->first(function ($match) {
            $matchDate = $this->toCarbon($match->match_date);
            if (!$matchDate) {
                return false;
            }

            if ($match->score && isset($match->score['home'], $match->score['away'])) {
                return false;
            }

            return $matchDate->isFuture();
        });

        return [
            'id' => $tournament->id,
            'name' => $tournament->name,
            'status' => $status,
            'status_label' => self::STATUS_LABELS[$status] ?? Str::title(str_replace('_', ' ', $status)),
            'description' => (string) $tournament->description,
            'summary' => $this->buildSummary($tournament),
            'location' => $tournament->location ?: 'Venue to be announced',
            'sport' => $tournament->hostClub->sport->name ?? $tournament->teams->first()?->sport?->name,
            'host_club' => $tournament->hostClub?->name,
            'host_logo' => $tournament->hostClub?->logo,
            'start_date' => $start?->format('M j, Y'),
            'end_date' => $end?->format('M j, Y'),
            'date_range' => $this->formatDateRange($start, $end),
            'matches' => $matches->map(fn ($match) => $this->transformMatch($match))->all(),
            'scoreboard' => $scoreboard,
            'teams' => $tournament->teams
                ->map(fn ($team) => [
                    'id' => $team->id,
                    'name' => $team->name,
                    'club' => $team->club?->name,
                    'logo' => $team->club?->logo ?? $team->logo,
                ])->values()->all(),
            'metrics' => [
                ['label' => 'Fixtures', 'value' => $matches->count()],
                ['label' => 'Completed', 'value' => $matches->filter(fn ($match) => $this->matchHasResult($match))->count()],
                ['label' => 'Teams', 'value' => max($tournament->teams->count(), $teamClubs->count())],
            ],
            'upcoming_match' => $upcomingMatch ? $this->transformMatch($upcomingMatch) : null,
        ];
    }

    /**
     * Build a condensed summary for quick scanning.
     */
    private function buildSummary(Tournament $tournament): string
    {
        if ($tournament->description) {
            return Str::limit(strip_tags($tournament->description), 160);
        }

        $host = $tournament->hostClub?->name;
        $parts = [];

        if ($host) {
            $parts[] = "Hosted by {$host}";
        }

        if ($tournament->location) {
            $parts[] = $tournament->location;
        }

        return $parts ? implode(' • ', $parts) : 'Tournament details coming soon.';
    }

    /**
     * Convert a raw match into a payload for the client-side UI.
     */
    private function transformMatch($match): array
    {
        $date = $this->toCarbon($match->match_date);
        $time = $this->normaliseTime($match->match_time);
        $hasResult = $this->matchHasResult($match);

        $status = 'scheduled';
        if ($hasResult) {
            $status = 'completed';
        } elseif ($date && $date->isFuture()) {
            $status = 'upcoming';
        } elseif ($date && $date->isPast()) {
            $status = 'awaiting_score';
        }

        return [
            'id' => $match->id,
            'date' => $date?->format('D, M j') ?? 'TBD',
            'time' => $time ?? 'TBD',
            'venue' => $match->venue ?: 'Venue TBA',
            'status' => $status,
            'status_label' => match ($status) {
                'completed' => 'Completed',
                'upcoming' => 'Upcoming',
                'awaiting_score' => 'Awaiting score',
                default => 'Scheduled',
            },
            'score' => $hasResult ? [
                'home' => (int) ($match->score['home'] ?? 0),
                'away' => (int) ($match->score['away'] ?? 0),
            ] : null,
            'home' => [
                'id' => $match->homeClub?->id,
                'name' => $match->homeClub?->name ?? 'TBD',
                'logo' => $match->homeClub?->logo,
            ],
            'away' => [
                'id' => $match->awayClub?->id,
                'name' => $match->awayClub?->name ?? 'TBD',
                'logo' => $match->awayClub?->logo,
            ],
        ];
    }

    /**
     * Build a scoreboard (league table) for the tournament.
     */
    private function buildScoreboard(Collection $matches, Collection $extraClubs): array
    {
        $table = [];

        $seedClub = function ($club) use (&$table): void {
            if (!$club) {
                return;
            }

            if (!isset($table[$club->id])) {
                $table[$club->id] = [
                    'club_id' => $club->id,
                    'club_name' => $club->name,
                    'logo' => $club->logo,
                    'played' => 0,
                    'wins' => 0,
                    'draws' => 0,
                    'losses' => 0,
                    'goals_for' => 0,
                    'goals_against' => 0,
                    'goal_diff' => 0,
                    'points' => 0,
                ];
            }
        };

        foreach ($matches as $match) {
            $seedClub($match->homeClub);
            $seedClub($match->awayClub);

            if (!$this->matchHasResult($match)) {
                continue;
            }

            $homeClub = $match->homeClub;
            $awayClub = $match->awayClub;

            if (!$homeClub || !$awayClub) {
                continue;
            }

            $homeGoals = (int) ($match->score['home'] ?? 0);
            $awayGoals = (int) ($match->score['away'] ?? 0);

            $table[$homeClub->id]['played'] += 1;
            $table[$awayClub->id]['played'] += 1;

            $table[$homeClub->id]['goals_for'] += $homeGoals;
            $table[$homeClub->id]['goals_against'] += $awayGoals;
            $table[$awayClub->id]['goals_for'] += $awayGoals;
            $table[$awayClub->id]['goals_against'] += $homeGoals;

            if ($homeGoals > $awayGoals) {
                $table[$homeClub->id]['wins'] += 1;
                $table[$homeClub->id]['points'] += 3;
                $table[$awayClub->id]['losses'] += 1;
            } elseif ($homeGoals < $awayGoals) {
                $table[$awayClub->id]['wins'] += 1;
                $table[$awayClub->id]['points'] += 3;
                $table[$homeClub->id]['losses'] += 1;
            } else {
                $table[$homeClub->id]['draws'] += 1;
                $table[$awayClub->id]['draws'] += 1;
                $table[$homeClub->id]['points'] += 1;
                $table[$awayClub->id]['points'] += 1;
            }
        }

        foreach ($extraClubs as $club) {
            $seedClub($club);
        }

        foreach ($table as &$row) {
            $row['goal_diff'] = $row['goals_for'] - $row['goals_against'];
        }

        unset($row);

        $rows = array_values($table);
        usort($rows, function ($a, $b) {
            $comparison = $b['points'] <=> $a['points'];
            if ($comparison !== 0) {
                return $comparison;
            }

            $comparison = $b['goal_diff'] <=> $a['goal_diff'];
            if ($comparison !== 0) {
                return $comparison;
            }

            $comparison = $b['goals_for'] <=> $a['goals_for'];
            if ($comparison !== 0) {
                return $comparison;
            }

            return strcasecmp($a['club_name'], $b['club_name']);
        });

        return $rows;
    }

    private function determineStatus(?Carbon $start, ?Carbon $end): string
    {
        $now = Carbon::now();

        if ($start && $now->lt($start)) {
            return 'upcoming';
        }

        if ($start && $end && $now->between($start->copy()->startOfDay(), $end->copy()->endOfDay())) {
            return 'in_progress';
        }

        if ($end && $now->gt($end)) {
            return 'completed';
        }

        if ($start && !$end && $now->gte($start)) {
            return 'in_progress';
        }

        return 'scheduled';
    }

    private function formatDateRange(?Carbon $start, ?Carbon $end): string
    {
        if ($start && $end) {
            return sprintf('%s – %s', $start->format('M j, Y'), $end->format('M j, Y'));
        }

        if ($start) {
            return 'Starts ' . $start->format('M j, Y');
        }

        return 'Dates to be confirmed';
    }

    private function toCarbon($value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function normaliseTime(?string $time): ?string
    {
        if (!$time) {
            return null;
        }

        try {
            return Carbon::createFromFormat('H:i:s', $time)->format('g:i A');
        } catch (\Throwable $e) {
            try {
                return Carbon::parse($time)->format('g:i A');
            } catch (\Throwable $throwable) {
                return null;
            }
        }
    }

    private function matchHasResult($match): bool
    {
        if (!is_array($match->score)) {
            return false;
        }

        return isset($match->score['home'], $match->score['away']);
    }
}
