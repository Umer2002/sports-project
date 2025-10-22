<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\{Coach, Task, Event, Player, Club, Chat, Reward, Video, GameMatch, Tournament};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use App\Traits\CalendarEventHelpers;

class DashboardController extends Controller
{
    use CalendarEventHelpers;

    public function setup()
    {
        if (!auth()->check() || !auth()->user()->hasRole('club')) {
            abort(403);
        }
        // Support both linkage styles: via users.club_id or clubs.user_id
        $user = auth()->user();
        $club = $user->club ?: Club::where('user_id', $user->id)->first();
        if ($club) {
            return redirect()->route('club-dashboard');
        }
        $sports = \App\Models\Sport::pluck('name', 'id');
        return view('club.setup', compact('sports'));
    }

    public function storeSetup(\Illuminate\Http\Request $request)
    {
        // dd($request->all());
        if (!auth()->check() || !auth()->user()->hasRole('club')) {
            abort(403);
        }
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'sport_id' => 'required|exists:sports,id',
        ]);
        $user = auth()->user();
        // Create or update the club record for this user
        $club = Club::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $data['name'],
                'email' => $user->email,
                'sport_id' => $data['sport_id'],
                'is_registered' => 1,
                'social_links' => [],
            ]
        );

        // Ensure the user's foreign key aligns with the user->club() relation
        if (!$user->club_id && $club && $club->id) {
            $user->update(['club_id' => $club->id]);
        }
        return redirect()->route('club-dashboard')->with('success', 'Club setup completed.');
    }
    private function composeDashboardData(\App\Models\Club $club)
    {
        $coaches = Coach::latest()->limit(6)->get();
        $tasks   = Task::with('user')->latest()->get();
        $club->processInitialPlayerCount();
        $club->processFinalPayout();

        $totalRevenue = $club->payments()->where('type', 'player')->sum('amount');
        $injuryReports = $club->players()->pluck('id');
        $injuryReports = \App\Models\InjuryReport::whereIn('player_id', $injuryReports)->count();
        $playerTransfers = \App\Models\PlayerTransfer::where('from_club_id', $club->id)
            ->orWhere('to_club_id', $club->id)->count();
        $newRegistrations = $club->players()->where('created_at', '>=', now()->subMonth())->count();

        $estimatedPayout = $club->calculateEstimatedPayout();

        $coachCount = $club->coaches()->count();
        $teamCount = $club->teams()->count();
        $activePlayers = $club->players()->count();
        $inactivePlayers = 0; // For now, set to 0. You can add logic to determine inactive players later

        $lastMonthRevenue = $club->payments()->where('type', 'player')
            ->where('created_at', '>=', now()->subMonth()->subMonth())
            ->where('created_at', '<', now()->subMonth())
            ->sum('amount');
        $revenueTrend = $lastMonthRevenue > 0 ? (($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

        $lastMonthInjuries = \App\Models\InjuryReport::whereIn('player_id', $club->players()->pluck('id'))
            ->where('created_at', '>=', now()->subMonth()->subMonth())
            ->where('created_at', '<', now()->subMonth())
            ->count();
        $injuryTrend = $lastMonthInjuries > 0 ? (($injuryReports - $lastMonthInjuries) / $lastMonthInjuries) * 100 : 0;

        $lastMonthTransfers = \App\Models\PlayerTransfer::where(function($query) use ($club) {
            $query->where('from_club_id', $club->id)->orWhere('to_club_id', $club->id);
        })
        ->where('created_at', '>=', now()->subMonth()->subMonth())
        ->where('created_at', '<', now()->subMonth())
        ->count();
        $transferTrend = $lastMonthTransfers > 0 ? (($playerTransfers - $lastMonthTransfers) / $lastMonthTransfers) * 100 : 0;

        $lastMonthRegistrations = $club->players()
            ->where('created_at', '>=', now()->subMonth()->subMonth())
            ->where('created_at', '<', now()->subMonth())
            ->count();
        $registrationTrend = $lastMonthRegistrations > 0 ? (($newRegistrations - $lastMonthRegistrations) / $lastMonthRegistrations) * 100 : 0;

        $onboardingTimeRemaining = $club->getOnboardingTimeRemaining();
        $payoutTimeRemaining = $club->getPayoutTimeRemaining();
        $payoutStatus = $club->getPayoutStatusDescription();
        $registrationDate = $club->getRegistrationDate();
        $daysSinceRegistration = $registrationDate ? $registrationDate->diffInDays(now()) : 0;
        $activePlayerTrendDirection = $teamTrendDirection = $coachTrendDirection = $payoutTrendDirection = 'up';
        $activePlayerTrendPercent = $teamTrendPercent=  $coachTrendPercent = $payoutTrendPercent = '0';

        $events = $this->buildCalendarEvents($club);

        // Calculate onboarding data
        $onboardingEndDate = $club->created_at->addDays(14);
        $isOnboardingActive = now()->lt($onboardingEndDate);

        // Calculate payout data using actual club methods
        $isPayoutActive = $club->isInPayoutPeriod();
        $payoutAmount = $club->calculateEstimatedPayout();
        $payoutStatusDescription = $club->getPayoutStatusDescription();

        // Calculate actual payout end date based on club registration
        $registrationDate = $club->getRegistrationDate();
        $payoutEndDate = $registrationDate ? $registrationDate->addDays(14)->addDays(90) : now()->addDays(14);

        // Get actual payout time remaining
        $actualPayoutTimeRemaining = $club->getPayoutTimeRemaining();

        // Always show payout countdown for registered clubs
        $showPayoutCountdown = $club->is_registered;

        // Adjust messaging based on current period
        if ($club->isInOnboardingPeriod()) {
            // During onboarding, show when payout period will start
            $payoutStatusDescription = 'Payout Period Starts After Onboarding';
            $payoutEndDate = $registrationDate ? $registrationDate->addDays(14) : now()->addDays(14);
        } elseif (!$isPayoutActive && !$club->isInOnboardingPeriod()) {
            // Onboarding complete but not in payout period yet
            $payoutStatusDescription = 'Onboarding Complete - Payout Period Starting Soon';
            $payoutEndDate = $registrationDate ? $registrationDate->addDays(14) : now()->addDays(14);
        }

        // Add weather data
        $weatherService = new \App\Services\WeatherService();
        $weather = $weatherService->getCurrentWeather('Ottawa', 'CA'); // You can make this dynamic based on club location

        // Add social links data
        $socialLinks = $club->social_links ?? [];
        $paypalLink = $club->paypal_link;

        // Add tournament data for new sections
        $tournamentCount = \App\Models\Tournament::count();

        // Determine tournament status based on dates
        $now = now();
        $activeTournaments = \App\Models\Tournament::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->count();
        $upcomingTournaments = \App\Models\Tournament::where('start_date', '>', $now)->count();

        $tournamentDirectoryEntries = \App\Models\Tournament::query()
            ->with(['state:id,name', 'city:id,name', 'division:id,name', 'sport:id,name'])
            ->withCount('teams')
            ->when($club->sport_id, function ($query) use ($club) {
                if (Schema::hasColumn('tournaments', 'sport_id')) {
                    $query->where('sport_id', $club->sport_id);
                } else {
                    $query->whereHas('hostClub', fn ($q) => $q->where('sport_id', $club->sport_id));
                }
            })
            ->orderByRaw('CASE WHEN start_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('start_date')
            ->get()
            ->map(function ($tournament) {
                $registrationCutoff = $tournament->registration_cutoff_date;
                $startDate = $tournament->start_date;
                $endDate = $tournament->end_date;

                $statusType = 'closed';
                $statusClass = 'status-closed';
                $statusLabel = 'Closed';
                $actionLabel = 'Details';
                $actionDisabled = true;

                if ($tournament->joining_type === 'waitlist') {
                    $statusType = 'waitlist';
                    $statusClass = 'status-waitlist';
                    $statusLabel = 'Waitlist';
                    $actionLabel = 'Join Waitlist';
                    $actionDisabled = false;
                } elseif (($registrationCutoff && $registrationCutoff->isFuture()) || (! $registrationCutoff && $startDate && $startDate->isFuture())) {
                    $statusType = 'open';
                    $statusClass = 'status-open';
                    $statusLabel = 'Open';
                    $actionLabel = 'Register';
                    $actionDisabled = false;
                }

                $dateLabel = 'TBD';
                if ($startDate && $endDate) {
                    $dateLabel = $startDate->format('M j') . ' - ' . $endDate->format('M j');
                } elseif ($startDate) {
                    $dateLabel = $startDate->format('M j, Y');
                }

                $monthLabel = $startDate ? $startDate->format('F Y') : null;

                return [
                    'id' => $tournament->id,
                    'name' => $tournament->name ?? 'Unnamed Tournament',
                    'city' => optional($tournament->city)->name ?? '—',
                    'state' => optional($tournament->state)->name ?? '—',
                    'dates' => $dateLabel,
                    'division' => optional($tournament->division)->name ?? '—',
                    'teams' => $tournament->teams_count ?? 0,
                    'fee' => $tournament->joining_fee,
                    'sport' => optional($tournament->sport)->name ?? '—',
                    'gender' => optional($tournament->gender)->name ?? '—',
                    'status_type' => $statusType,
                    'status_class' => $statusClass,
                    'status_label' => $statusLabel,
                    'action_label' => $actionLabel,
                    'action_disabled' => $actionDisabled,
                    'month_key' => $monthLabel ? $startDate->format('Y-m') : null,
                    'month_label' => $monthLabel ?? 'TBD',
                ];
            })
            ->values();

        $tournamentFilterOptions = [
            'states' => $tournamentDirectoryEntries->pluck('state')->filter(fn ($value) => $value && $value !== '—')->unique()->sort()->values()->all(),
            'cities' => $tournamentDirectoryEntries->pluck('city')->filter(fn ($value) => $value && $value !== '—')->unique()->sort()->values()->all(),
            'sports' => $tournamentDirectoryEntries->pluck('sport')->filter(fn ($value) => $value && $value !== '—')->unique()->sort()->values()->all(),
            'divisions' => $tournamentDirectoryEntries->pluck('division')->filter(fn ($value) => $value && $value !== '—')->unique()->sort()->values()->all(),
            'statuses' => $tournamentDirectoryEntries
                ->pluck('status_label', 'status_type')
                ->filter(fn ($label, $type) => $type && $label)
                ->unique()
                ->map(fn ($label, $type) => ['value' => $type, 'label' => $label])
                ->values()
                ->all(),
            'months' => $tournamentDirectoryEntries
                ->map(fn ($entry) => [
                    'value' => $entry['month_key'],
                    'label' => $entry['month_label'],
                ])
                ->filter(fn ($entry) => $entry['value'])
                ->unique('value')
                ->sortBy('value')
                ->values()
                ->all(),
        ];

        $registeredTeams = \App\Models\TournamentTeam::count();
        $approvedTeams = $registeredTeams; // All registered teams are considered approved
        $pendingTeams = 0; // No pending status in current schema

        // Use GameMatch model which uses matches table
        $scheduledMatches = \App\Models\GameMatch::where('match_date', '>', $now)->count();
        $completedMatches = \App\Models\GameMatch::where('match_date', '<', $now)->count();
        $finalizedWinners = \App\Models\Tournament::where('end_date', '<', $now)->count();

        // Active tournament details - get the most recent active tournament
        $activeTournament = \App\Models\Tournament::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderBy('start_date', 'desc')
            ->first();

        $activeTournamentName = $activeTournament ? $activeTournament->name : 'Spring Cup 2025';
        $tournamentFormat = $activeTournament ? 'Round Robin' : 'Round Robin'; // Default format
        $tournamentDates = $activeTournament ? $activeTournament->start_date->format('M d') . ' - ' . $activeTournament->end_date->format('M d') : 'May 10 - May 14';
        $tournamentLocation = $activeTournament ? $activeTournament->location : 'Ottawa Sports Dome';
        $tournamentTeams = $activeTournament ? $activeTournament->teams()->count() . ' Teams. 2 Divisions' : '12 Teams. 2 Divisions';
        $tournamentStatus = $activeTournament ? 'Active' : 'Registration Open';

        $tournamentChatRooms = \App\Models\Tournament::query()
            ->select(['id', 'name', 'start_date', 'end_date', 'location'])
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            })
            ->orderBy('start_date')
            ->take(12)
            ->get()
            ->map(function ($tournament) use ($now) {
                $start = $tournament->start_date;
                $end = $tournament->end_date;

                $status = 'Upcoming';
                if ($start && $start->isPast() && (!$end || $end->isFuture())) {
                    $status = 'Live Now';
                } elseif ($end && $end->isPast()) {
                    $status = 'Closed';
                }

                $initials = Str::of($tournament->name ?? 'Tournament')
                    ->replaceMatches('/[^A-Za-z0-9 ]+/u', ' ')
                    ->trim()
                    ->explode(' ')
                    ->filter()
                    ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
                    ->take(2)
                    ->implode('') ?: 'TC';

                return [
                    'id' => $tournament->id,
                    'name' => $tournament->name ?? 'Tournament',
                    'location' => $tournament->location ?? 'TBD',
                    'start_label' => $start ? $start->format('M d') : null,
                    'end_label' => $end ? $end->format('M d') : null,
                    'status' => $status,
                    'is_closed' => $status === 'Closed',
                    'initials' => $initials,
                ];
            });

        $playersForChat = $club->players()
            ->with(['user', 'team', 'teams'])
            ->orderBy('name')
            ->get()
            ->map(function (Player $player) {
                $name = trim($player->name ?? '');
                if ($name === '') {
                    $name = optional($player->user)->name ?? 'Player #' . $player->id;
                }

                $primaryTeam = $player->team ?: $player->teams->first();
                $meta = $primaryTeam ? $primaryTeam->name : null;
                $isOnline = (bool) optional($player->user)->isOnline();
                $avatarPath = $player->photo;

                return $this->makeChatContact($name, 'Player', $meta, $avatarPath, $isOnline, optional($player->user)->id);
            })
            ->values();

        $coachesForChat = $club->coaches()
            ->with(['user', 'teams', 'sport'])
            ->orderBy('name')
            ->get()
            ->map(function (Coach $coach) {
                $name = $coach->name ?? '';
                if ($name === '') {
                    $name = optional($coach->user)->name ?? $coach->email ?? 'Coach #' . $coach->id;
                }

                $teamNames = $coach->teams->pluck('name')->filter();
                $teamsLabel = $teamNames->take(2)->implode(', ');
                if ($teamNames->count() > 2) {
                    $teamsLabel .= ' +' . ($teamNames->count() - 2) . ' more';
                }

                $metaParts = $teamsLabel ? [$teamsLabel] : [];
                if ($coach->sport && $coach->sport->name) {
                    $metaParts[] = $coach->sport->name;
                }
                $meta = empty($metaParts) ? null : implode(' • ', $metaParts);

                $isOnline = (bool) optional($coach->user)->isOnline();
                $avatarPath = $coach->photo;

                return $this->makeChatContact($name, 'Coach', $meta, $avatarPath, $isOnline, optional($coach->user)->id);
            })
            ->values();

        $clubOwnerContact = null;
        if ($club->user) {
            $ownerMetaParts = [];
            if ($club->name) {
                $ownerMetaParts[] = $club->name;
            }
            if (optional($club->sport)->name) {
                $ownerMetaParts[] = $club->sport->name . ' Club';
            }
            $ownerMeta = empty($ownerMetaParts) ? null : implode(' • ', $ownerMetaParts);

            $clubOwnerContact = $this->makeChatContact(
                $club->user->name ?? 'Club Owner',
                'Club Owner',
                $ownerMeta,
                $club->logo,
                (bool) $club->user->isOnline(),
                $club->user->id
            );
        }

        $chatOwners = $clubOwnerContact ? collect([$clubOwnerContact]) : collect();
        $chatCoaches = $coachesForChat;
        $chatPlayers = $playersForChat;

        $currentUser = auth()->user();
        $recentChats = $currentUser ? $this->buildRecentChatThreads($currentUser) : collect();

        $availableAwards = Reward::query()
            ->get(['id', 'name','type', 'achievement', 'image']);

        // Get club players for attribute modal
        $clubPlayers = $club->players()->with(['position', 'sport', 'playerStats'])->get();

        // Get club teams for attribute modal
        $clubTeams = $club->teams()->select('id', 'name')->get();

        // Get club's sport stats configuration
        $clubSportStats = \App\Models\PlayerGameStat::where('sport_id', $club->sport_id)->first();

        $recentVideos = Video::with(['user:id,name'])
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (Video $video) => $this->buildVideoPreview($video));

        return compact(
            'totalRevenue','injuryReports','playerTransfers','newRegistrations','estimatedPayout','coachCount','teamCount','activePlayers','inactivePlayers',
            'revenueTrend','injuryTrend','transferTrend','registrationTrend','onboardingTimeRemaining','payoutTimeRemaining','payoutStatus',
            'registrationDate','daysSinceRegistration','payoutTrendDirection','payoutTrendPercent','coachTrendDirection','coachTrendPercent',
            'teamTrendDirection','teamTrendPercent','activePlayerTrendDirection','activePlayerTrendPercent','events','club','tasks',
            'onboardingEndDate','isOnboardingActive','payoutEndDate','isPayoutActive','payoutAmount','payoutStatusDescription','actualPayoutTimeRemaining','showPayoutCountdown',
            'weather','socialLinks','paypalLink','tournamentCount','activeTournaments','upcomingTournaments','registeredTeams','approvedTeams','pendingTeams',
            'scheduledMatches','completedMatches','finalizedWinners','activeTournamentName','tournamentFormat','tournamentDates','tournamentLocation','tournamentTeams','tournamentStatus',
            'chatOwners','chatCoaches','chatPlayers','recentChats','availableAwards','clubPlayers','clubTeams','clubSportStats','tournamentChatRooms','tournamentDirectoryEntries','tournamentFilterOptions',
            'recentVideos'
        );
    }




    private function buildCalendarEvents(Club $club): array
    {
        $palette = [
            'event' => '#38bdf8',
            'match_host' => '#f97316',
            'match_participant' => '#fb923c',
            'tournament_host' => '#8b5cf6',
            'tournament_participant' => '#6366f1',
        ];

        $club->loadMissing([
            'players:id,name,club_id',
            'teams:id,club_id',
            'teams.players:id,name',
        ]);

        $club->loadMissing([
            'coaches' => function ($query) {
                $query->select('coaches.id', 'coaches.first_name', 'coaches.last_name', 'coaches.email', 'coaches.user_id');
            },
            'coaches.user:id,name',
        ]);

        $clubPlayerSummary = $this->summarizeNames($club->players);
        $clubCoachSummary = $this->summarizeNames($club->coaches);

        $entries = collect();

        $clubEvents = Event::with([
                'user:id,name',
                'team.players:id,name',
                'team.coaches:id,first_name,last_name,email,user_id',
                'team.coaches.user:id,name',
                'team.club:id,name',
            ])
            ->where(function ($query) use ($club) {
                $query->where('club_id', $club->id)
                    ->orWhereHas('user', function ($userQuery) use ($club) {
                        $userQuery->where('club_id', $club->id);
                    })
                    ->orWhereHas('team', function ($teamQuery) use ($club) {
                        $teamQuery->where('club_id', $club->id);
                    })
                    ->orWhereHas('invites', function ($inviteQuery) use ($club) {
                        $inviteQuery->where('type', 'club')
                            ->where('reference_id', $club->id);
                    })
                    ->orWhereHas('user', function ($userQuery) use ($club) {
                        $userQuery->whereHas('player', function ($playerQuery) use ($club) {
                            $playerQuery->where('club_id', $club->id);
                        });
                    });
            })
            ->orderBy('start')
            ->get();

        foreach ($clubEvents as $event) {
            $teamPlayers = $event->team ? $this->summarizeNames($event->team->players) : $clubPlayerSummary;
            $teamCoaches = $event->team ? $this->summarizeNames($event->team->coaches) : $clubCoachSummary;

            $entries->push([
                'title' => ($event->type ? ucfirst($event->type) . ': ' : 'Event: ') . ($event->title ?? 'Club Event'),
                'start' => $event->getFormattedDateTime(),
                'end' => $event->end ? (function ($value) {
                    try {
                        return Carbon::parse($value)->format('Y-m-d\TH:i:s');
                    } catch (\Throwable $e) {
                        return null;
                    }
                })($event->end) : null,
                'color' => $palette['event'],
                'className' => ['calendar-event'],
                'extendedProps' => [
                    'resource_type' => 'event',
                    'resource_id' => $event->id,
                    'team_id' => optional($event->team)->id,
                    'team_name' => optional($event->team)->name,
                    'category' => 'event',
                    'type' => $event->type ?? 'event',
                    'location' => $event->location ?? 'TBD',
                    'description' => Str::limit($event->description ?? '', 140),
                    'coaches' => $teamCoaches['list'],
                    'coaches_overflow' => $teamCoaches['overflow'],
                    'coaches_count' => $teamCoaches['total'],
                    'players' => $teamPlayers['list'],
                    'players_overflow' => $teamPlayers['overflow'],
                    'player_count' => $teamPlayers['total'],
                    'clubs' => [$club->name],
                    'club_count' => 1,
                    'role' => 'host',
                ],
            ]);
        }

        $tournaments = Tournament::with([
                'coaches:id,first_name,last_name,email,user_id',
                'coaches.user:id,name',
                'hostClub:id,name',
                'teams:id,club_id',
                'teams.club:id,name',
                'teams.players:id,name',
                'venue:id,name',
            ])
            ->where(function ($query) use ($club) {
                $query->where('host_club_id', $club->id)
                    ->orWhereHas('teams', function ($teamQuery) use ($club) {
                        $teamQuery->where('club_id', $club->id);
                    });
            })
            ->get();

        foreach ($tournaments as $tournament) {
            $isHost = $tournament->host_club_id === $club->id;
            $playerSummary = $this->summarizeNames(
                $tournament->teams
                    ->filter(fn ($team) => $team->club_id === $club->id)
                    ->flatMap(fn ($team) => $team->players)
            );
            $coachSummary = $this->summarizeNames($tournament->coaches);
            $clubSummary = $this->summarizeNames(collect([
                optional($tournament->hostClub)->name,
            ])->merge($tournament->teams->map(fn ($team) => optional($team->club)->name))->filter(), 12);

            $entries->push([
                'title' => ($isHost ? 'Tournament Hosted: ' : 'Tournament: ') . ($tournament->name ?? 'Tournament'),
                'start' => optional($tournament->start_date)->format('Y-m-d') ?? optional($tournament->registration_cutoff_date)->format('Y-m-d'),
                'end' => $tournament->end_date ? $tournament->end_date->copy()->addDay()->format('Y-m-d') : null,
                'allDay' => true,
                'color' => $isHost ? $palette['tournament_host'] : $palette['tournament_participant'],
                'className' => ['calendar-tournament', $isHost ? 'calendar-tournament-host' : 'calendar-tournament-participant'],
                'extendedProps' => [
                    'resource_type' => 'tournament',
                    'resource_id' => $tournament->id,
                    'category' => 'tournament',
                    'role' => $isHost ? 'host' : 'participant',
                    'location' => $tournament->location ?? optional($tournament->venue)->name ?? 'TBD',
                    'description' => Str::limit($tournament->description ?? '', 160),
                    'dates' => $this->formatDateRange($tournament->start_date, $tournament->end_date),
                    'coaches' => $coachSummary['list'],
                    'coaches_overflow' => $coachSummary['overflow'],
                    'coaches_count' => $coachSummary['total'],
                    'players' => $playerSummary['list'],
                    'players_overflow' => $playerSummary['overflow'],
                    'player_count' => $playerSummary['total'],
                    'clubs' => $clubSummary['list'],
                    'club_count' => $clubSummary['total'],
                    'clubs_overflow' => $clubSummary['overflow'],
                ],
            ]);
        }

        $matches = GameMatch::with([
                'tournament.coaches:id,first_name,last_name,email,user_id',
                'tournament.coaches.user:id,name',
                'tournament.hostClub:id,name',
                'tournament.teams:id,club_id',
                'tournament.teams.club:id,name',
                'tournament.teams.players:id,name',
                'homeClub:id,name',
                'homeClub.players:id,name,club_id',
                'awayClub:id,name',
                'awayClub.players:id,name,club_id',
                'venue:id,name',
            ])
            ->where(function ($query) use ($club) {
                $query->where('home_club_id', $club->id)
                    ->orWhere('away_club_id', $club->id)
                    ->orWhereHas('tournament.teams', function ($teamQuery) use ($club) {
                        $teamQuery->where('club_id', $club->id);
                    });
            })
            ->get();

        foreach ($matches as $match) {
            $start = $this->combineDateAndTime($match->match_date, $match->match_time);
            if (!$start) {
                continue;
            }

            $isHost = $match->home_club_id === $club->id
                || optional($match->tournament)->host_club_id === $club->id;

            $playerCollection = collect();
            if ($match->homeClub && $match->homeClub->players) {
                $playerCollection = $playerCollection->merge($match->homeClub->players);
            }
            if ($match->awayClub && $match->awayClub->players) {
                $playerCollection = $playerCollection->merge($match->awayClub->players);
            }
            if ($match->tournament) {
                foreach ($match->tournament->teams as $team) {
                    if ($team->club_id === $club->id) {
                        $playerCollection = $playerCollection->merge($team->players);
                    }
                }
            }

            $playerSummary = $this->summarizeNames($playerCollection);
            $coachSummary = $match->tournament
                ? $this->summarizeNames($match->tournament->coaches)
                : $clubCoachSummary;

            $clubSummary = $this->summarizeNames(collect([
                optional($match->homeClub)->name,
                optional($match->awayClub)->name,
                optional(optional($match->tournament)->hostClub)->name,
            ])->filter(), 12);

            $entries->push([
                'title' => 'Match: ' . trim(($match->homeClub->name ?? 'Home') . ' vs ' . ($match->awayClub->name ?? 'Away')),
                'start' => $start,
                'color' => $isHost ? $palette['match_host'] : $palette['match_participant'],
                'className' => ['calendar-match', $isHost ? 'calendar-match-host' : 'calendar-match-participant'],
                'extendedProps' => [
                    'resource_type' => 'match',
                    'resource_id' => $match->id,
                    'category' => 'match',
                    'role' => $isHost ? 'host' : 'participant',
                    'location' => optional($match->venue)->name ?? $match->venue ?? 'TBD',
                    'tournament' => optional($match->tournament)->name,
                    'coaches' => $coachSummary['list'],
                    'coaches_overflow' => $coachSummary['overflow'],
                    'coaches_count' => $coachSummary['total'],
                    'players' => $playerSummary['list'],
                    'players_overflow' => $playerSummary['overflow'],
                    'player_count' => $playerSummary['total'],
                    'clubs' => $clubSummary['list'],
                    'club_count' => $clubSummary['total'],
                    'clubs_overflow' => $clubSummary['overflow'],
                ],
            ]);
        }

        return $entries
            ->filter(fn ($event) => !empty($event['start']))
            ->values()
            ->all();
    }

    private function makeChatContact(string $name, string $role, ?string $meta, ?string $avatarPath, bool $isOnline, ?int $userId = null): array
    {
        $tagline = $meta ? $role . ' • ' . $meta : $role;

        return [
            'user_id' => $userId,
            'name' => $name,
            'role' => $role,
            'meta' => $meta,
            'tagline' => $tagline,
            'avatar' => $this->resolveAvatar($avatarPath, $name),
            'status' => $isOnline ? 'online' : 'offline',
            'status_label' => $isOnline ? 'Online' : 'Offline',
            'initials' => $this->buildInitials($name),
        ];
    }

    /**
     * Build a collection of recent chat threads for the authenticated user.
     */
    private function buildRecentChatThreads(User $currentUser)
    {
        return Chat::query()
            ->whereHas('participants', fn($query) => $query->where('user_id', $currentUser->id))
            ->with([
                'participants:id,name',
                'messages' => fn($query) => $query->latest()->limit(1),
            ])
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get()
            ->map(fn(Chat $chat) => $this->makeChatThread($chat, $currentUser))
            ->filter()
            ->values();
    }

    /**
     * Convert a chat thread into the common contact payload used by the dashboard.
     */
    private function makeChatThread(Chat $chat, User $currentUser): ?array
    {
        $latestMessage = $chat->messages->first();
        $latestPreview = null;

        if ($latestMessage) {
            $latestPreview = trim((string) $latestMessage->content);
            if ($latestPreview === '' && !empty($latestMessage->attachment_path)) {
                $latestPreview = 'Sent an attachment';
            }
        }

        $preview = $latestPreview ? Str::limit($latestPreview, 70) : null;

        if ($chat->type === 'individual') {
            $otherUser = $chat->participants->firstWhere('id', '!=', $currentUser->id);
            if (!$otherUser instanceof User) {
                return null;
            }

            $name = trim($otherUser->name ?? '');
            if ($name === '') {
                $name = 'Conversation';
            }

            $contact = $this->makeChatContact(
                $name,
                'Direct Message',
                $preview ?? 'Start chatting',
                null,
                method_exists($otherUser, 'isOnline') ? (bool) $otherUser->isOnline() : false,
                $otherUser->id
            );

            // Override the tagline so we show the latest preview instead of role metadata.
            $contact['tagline'] = $preview ?? 'Direct message';
            $contact['chat_id'] = $chat->id;

            return $contact;
        }

        return null;
    }

    private function resolveAvatar(?string $path, string $fallbackName): string
    {
        if (!$path) {
            return $this->buildAvatarFallback($fallbackName);
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $normalized = ltrim($path, '/');
        if (Str::startsWith($normalized, 'storage/')) {
            $normalized = Str::after($normalized, 'storage/');
        }

        try {
            return Storage::disk('public')->url($normalized);
        } catch (\Throwable $e) {
            // Fallback to generated avatar if disk is not configured
        }

        return $this->buildAvatarFallback($fallbackName);
    }

    private function buildInitials(string $name): string
    {
        $parts = collect(preg_split('/\s+/', trim($name) ?: ''));
        $initials = $parts
            ->filter()
            ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
            ->take(2)
            ->implode('');

        if ($initials === '') {
            $sanitized = preg_replace('/[^A-Za-z0-9]/', '', $name);
            $initials = Str::upper(Str::substr($sanitized ?: 'User', 0, 2));
        }

        return $initials;
    }

    private function buildAvatarFallback(string $name): string
    {
        $displayName = trim($name) ?: 'User';

        return 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=0d6efd&color=ffffff&size=64&bold=true';
    }

    private function buildVideoPreview(Video $video): array
    {
        $thumbnail = $this->resolveVideoThumbnail($video);

        return [
            'id' => $video->id,
            'title' => $video->title ?: 'Club Highlight',
            'description' => Str::limit($video->description ?? '', 130),
            'thumbnail' => $thumbnail,
            'show_url' => route('player.videos.show', $video),
            'duration' => $this->formatVideoDuration($video->duration ?? null),
            'uploaded_human' => optional($video->created_at)->diffForHumans() ?? '',
            'author' => optional($video->user)->name ?? 'Play2Earn',
        ];
    }

    private function resolveVideoThumbnail(Video $video): string
    {
        $placeholder = asset('assets/club-dashboard-main/assets/videotthump.png');

        $rawThumbnail = $video->thumbnail
            ?? $video->thumbnail_url
            ?? $video->poster
            ?? $video->cover_image
            ?? $video->image
            ?? $video->mobile_app_image
            ?? null;

        if (is_string($rawThumbnail) && trim($rawThumbnail) !== '') {
            if (Str::startsWith($rawThumbnail, ['http://', 'https://'])) {
                return $rawThumbnail;
            }

            $normalized = ltrim($rawThumbnail, '/');
            $normalized = preg_replace('#^storage/app/public/#', '', $normalized);

            $candidates = array_unique(array_filter([
                $normalized,
                Str::startsWith($normalized, 'public/storage/') ? Str::after($normalized, 'public/storage/') : null,
                Str::startsWith($normalized, 'public/') ? Str::after($normalized, 'public/') : null,
                Str::startsWith($normalized, 'storage/') ? Str::after($normalized, 'storage/') : null,
            ]));

            try {
                $publicDisk = Storage::disk('public');
                foreach ($candidates as $candidate) {
                    if ($publicDisk->exists($candidate)) {
                        return $publicDisk->url($candidate);
                    }
                }
            } catch (\Throwable $e) {
                // fall through to filesystem checks
            }

            foreach ($candidates as $candidate) {
                if (file_exists(public_path($candidate))) {
                    return asset($candidate);
                }
                if (file_exists(public_path('storage/' . $candidate))) {
                    return asset('storage/' . $candidate);
                }
            }
        }

        if ($video->playback_url && Str::contains($video->playback_url, 'youtube.com')) {
            $query = parse_url($video->playback_url, PHP_URL_QUERY);
            parse_str($query ?? '', $parts);
            if (!empty($parts['v'])) {
                return 'https://img.youtube.com/vi/' . $parts['v'] . '/hqdefault.jpg';
            }
        }

        return $placeholder;
    }

    private function formatVideoDuration($duration): string
    {
        if ($duration === null || $duration === '') {
            return '--:--';
        }

        if (is_numeric($duration)) {
            $seconds = (int) $duration;
            $minutes = intdiv($seconds, 60);
            $remaining = $seconds % 60;
            return sprintf('%02d:%02d', $minutes, $remaining);
        }

        return (string) $duration;
    }

    public function index()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user has club role
        if (!auth()->user()->hasRole('club')) {
            abort(403, 'Access denied. Club role required.');
        }

        // Resolve club via users.club_id or clubs.user_id to avoid setup loop
        $user = auth()->user();
        $club = $user->club ?: Club::where('user_id', $user->id)->first();
        if (!$club || !$club->sport_id) {
            return redirect()->route('club.setup');
        }
        $data = $this->composeDashboardData($club);
        return view('club.dashboard', $data);
    }

    // Allow college users to open a managed club's dashboard view
    public function showForCollege(Club $club)
    {
        if (!auth()->check() || !auth()->user()->hasRole('college')) {
            abort(403);
        }
        // Only allow if college owns this managed club
        if ($club->user_id !== auth()->id()) {
            abort(403);
        }
        $data = $this->composeDashboardData($club);
        return view('club.dashboard', $data);
    }
}
