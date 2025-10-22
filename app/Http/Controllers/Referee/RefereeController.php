<?php

namespace App\Http\Controllers\Referee;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\GameMatch;
use App\Models\Referee as RefereeModel;
use App\Models\RefereeAvailability;
use App\Models\Tournament;
use App\Models\Video;
use App\Services\WeatherService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RefereeController extends Controller
{
    public function dashboard()
    {
        $referee = Auth::user()->referee;
        $now = Carbon::now();

        $assignedMatchesQuery = GameMatch::with(['homeClub', 'awayClub', 'tournament'])
            ->where('referee_id', $referee->id)
            ->orderBy('match_date')
            ->orderBy('match_time');

        $assignedMatches = (clone $assignedMatchesQuery)
            ->whereDate('match_date', '>=', $now->toDateString())
            ->get();

        $recentAssignments = (clone $assignedMatchesQuery)
            ->whereDate('match_date', '<', $now->toDateString())
            ->orderBy('match_date', 'desc')
            ->limit(10)
            ->get();

        $applicationBaseQuery = Application::where('referee_id', $referee->id);

        $pendingApplications = (clone $applicationBaseQuery)
            ->where('status', 'pending')
            ->with(['match.homeClub', 'match.awayClub', 'match.tournament'])
            ->latest('applied_at')
            ->get();

        $rejectedApplications = (clone $applicationBaseQuery)
            ->where('status', 'rejected')
            ->with(['match.homeClub', 'match.awayClub', 'match.tournament'])
            ->latest('applied_at')
            ->get();

        $acceptedApplicationsCount = (clone $applicationBaseQuery)->where('status', 'accepted')->count();
        $completedApplicationsCount = (clone $applicationBaseQuery)->where('status', 'completed')->count();
        $cancelledApplicationsCount = (clone $applicationBaseQuery)->where('status', 'cancelled')->count();
        $totalApplications = (clone $applicationBaseQuery)->count();

        $todayApplications = (clone $applicationBaseQuery)
            ->whereDate('applied_at', $now->toDateString())
            ->count();

        $pendingApplicationsCount = $pendingApplications->count();
        $rejectedApplicationsCount = $rejectedApplications->count();

        $totalAssignedDirect = GameMatch::where('referee_id', $referee->id)->count();
        $additionalAcceptedAssignments = (clone $applicationBaseQuery)->accepted()
            ->whereDoesntHave('match', function ($query) use ($referee) {
                $query->where('referee_id', $referee->id);
            })
            ->count();

        $stats = [
            'total_assigned' => $totalAssignedDirect + $additionalAcceptedAssignments,
            'total_completed' => $completedApplicationsCount,
            'total_cancelled' => $cancelledApplicationsCount,
            'priority_score' => $this->calculatePriorityScore($referee),
        ];

        $currentPeriodStart = $now->copy()->subDays(28)->startOfDay();
        $previousPeriodStart = $currentPeriodStart->copy()->subDays(28);
        $previousPeriodEnd = $currentPeriodStart->copy()->subSecond();

        $assignmentsCurrent = GameMatch::where('referee_id', $referee->id)
            ->whereBetween('match_date', [$currentPeriodStart->toDateString(), $now->toDateString()])
            ->count();
        $assignmentsPrevious = GameMatch::where('referee_id', $referee->id)
            ->whereBetween('match_date', [$previousPeriodStart->toDateString(), $previousPeriodEnd->toDateString()])
            ->count();

        $applicationsCurrent = (clone $applicationBaseQuery)
            ->whereBetween('applied_at', [$currentPeriodStart, $now])
            ->count();
        $applicationsPrevious = (clone $applicationBaseQuery)
            ->whereBetween('applied_at', [$previousPeriodStart, $previousPeriodEnd])
            ->count();

        $completionsCurrent = (clone $applicationBaseQuery)
            ->where('status', 'completed')
            ->whereBetween('applied_at', [$currentPeriodStart, $now])
            ->count();
        $completionsPrevious = (clone $applicationBaseQuery)
            ->where('status', 'completed')
            ->whereBetween('applied_at', [$previousPeriodStart, $previousPeriodEnd])
            ->count();

        $acceptedCurrent = (clone $applicationBaseQuery)
            ->where('status', 'accepted')
            ->whereBetween('applied_at', [$currentPeriodStart, $now])
            ->count();
        $acceptedPrevious = (clone $applicationBaseQuery)
            ->where('status', 'accepted')
            ->whereBetween('applied_at', [$previousPeriodStart, $previousPeriodEnd])
            ->count();

        $pendingCurrent = (clone $applicationBaseQuery)
            ->where('status', 'pending')
            ->whereBetween('applied_at', [$currentPeriodStart, $now])
            ->count();
        $pendingPrevious = (clone $applicationBaseQuery)
            ->where('status', 'pending')
            ->whereBetween('applied_at', [$previousPeriodStart, $previousPeriodEnd])
            ->count();

        $acceptanceRate = $totalApplications > 0 ? round(($acceptedApplicationsCount / $totalApplications) * 100) : null;
        $completionRate = $totalApplications > 0 ? round(($completedApplicationsCount / $totalApplications) * 100) : null;

        $availabilityData = $referee->availability()->where('is_available', true)->get();
        $availabilitySummary = [
            'slots' => $availabilityData->count(),
            'days' => $availabilityData->pluck('day_of_week')->unique()->count(),
            'next_available' => $availabilityData->map(function ($slot) use ($now) {
                try {
                    $nextDate = Carbon::parse("next {$slot->day_of_week}");
                    $timeValue = $slot->start_time instanceof Carbon
                        ? $slot->start_time->format('H:i:s')
                        : (string) $slot->start_time;
                    return $nextDate->setTimeFromTimeString($timeValue);
                } catch (\Exception $e) {
                    return null;
                }
            })->filter()->sort()->first(),
        ];

        $calendarEvents = collect();
        foreach ($assignedMatches as $match) {
            $calendarEvents->push($this->makeCalendarEventPayload($match, 'assigned'));
        }
        foreach ($pendingApplications as $application) {
            if (!$application->match) {
                continue;
            }
            $calendarEvents->push($this->makeCalendarEventPayload($application->match, 'pending'));
        }
        $calendarEvents = $calendarEvents->filter()->values();

        $weatherService = new WeatherService();
        $weatherCity = $referee->city ?: 'Ottawa';
        $weatherCountry = $referee->country ?: 'CA';
        $weather = $weatherService->getCurrentWeather($weatherCity, $weatherCountry);

        $locationLabelParts = collect([$referee->city, $referee->region, $referee->country])->filter();
        $locationLabel = $locationLabelParts->isNotEmpty() ? $locationLabelParts->implode(', ') : 'Ottawa, Canada';

        $tournaments = Tournament::with(['sport', 'city', 'state', 'division', 'gender', 'ageGroup'])
            ->withCount('teams')
            ->orderBy('start_date')
            ->limit(6)
            ->get();

        $tournamentResults = $tournaments->map(function (Tournament $tournament) use ($now) {
            $status = 'Upcoming';
            if ($tournament->start_date && $tournament->end_date) {
                if ($tournament->start_date->isPast() && $tournament->end_date->isFuture()) {
                    $status = 'Active';
                } elseif ($tournament->end_date->isPast()) {
                    $status = 'Completed';
                }
            }

            return [
                'name' => $tournament->name,
                'city' => optional($tournament->city)->name,
                'state' => optional($tournament->state)->name,
                'dates' => ($tournament->start_date && $tournament->end_date)
                    ? $tournament->start_date->format('M j') . ' - ' . $tournament->end_date->format('M j')
                    : null,
                'division' => optional($tournament->division)->name ?? optional($tournament->ageGroup)->name,
                'teams' => $tournament->teams_count,
                'fee' => $tournament->joining_fee,
                'status' => $status,
                'sport' => optional($tournament->sport)->name,
                'gender' => optional($tournament->gender)->name,
                'registration_deadline' => optional($tournament->registration_cutoff_date)->format('M j'),
            ];
        });

        $tournamentFilters = [
            'states' => $tournaments->pluck('state.name')->filter()->unique()->values(),
            'cities' => $tournaments->pluck('city.name')->filter()->unique()->values(),
            'sports' => $tournaments->pluck('sport.name')->filter()->unique()->values(),
            'divisions' => $tournaments->pluck('division.name')->filter()->unique()->values(),
            'genders' => $tournaments->pluck('gender.name')->filter()->unique()->values(),
            'statuses' => collect(['Active', 'Upcoming', 'Completed'])->values(),
        ];

        $totalTournaments = Tournament::count();
        $activeTournaments = Tournament::whereDate('start_date', '<=', $now)->whereDate('end_date', '>=', $now)->count();
        $upcomingTournaments = Tournament::whereDate('start_date', '>', $now)->count();
        $nextTournament = Tournament::whereDate('start_date', '>=', $now)->orderBy('start_date')->first();

        $tournamentHighlights = [
            'total' => $totalTournaments,
            'active' => $activeTournaments,
            'upcoming' => $upcomingTournaments,
            'next' => $nextTournament ? [
                'name' => $nextTournament->name,
                'start' => $nextTournament->start_date?->format('M j'),
                'sport' => optional($nextTournament->sport)->name,
            ] : null,
        ];

        $summaryCards = [
            [
                'label' => 'Assigned Matches',
                'value' => $stats['total_assigned'],
                'trend' => $this->formatTrend($assignmentsCurrent, $assignmentsPrevious),
                'description' => 'Matches in the last 28 days',
                'icon' => 'fa-clipboard-check',
                'color' => 'green',
            ],
            [
                'label' => 'Applications Sent',
                'value' => $totalApplications,
                'trend' => $this->formatTrend($applicationsCurrent, $applicationsPrevious),
                'description' => 'All-time applications',
                'icon' => 'fa-paper-plane',
                'color' => 'orange',
            ],
            [
                'label' => 'Matches Completed',
                'value' => $completedApplicationsCount,
                'trend' => $this->formatTrend($completionsCurrent, $completionsPrevious),
                'description' => 'Completed assignments',
                'icon' => 'fa-flag-checkered',
                'color' => 'blue',
            ],
            [
                'label' => 'Acceptance Rate',
                'value' => $acceptanceRate !== null ? $acceptanceRate . '%' : '—',
                'trend' => $this->formatTrend($acceptedCurrent, $acceptedPrevious),
                'description' => 'Accepted this month',
                'icon' => 'fa-gauge-high',
                'color' => 'purple',
            ],
        ];

        $insightCards = [
            [
                'label' => 'Pending Decisions',
                'value' => $pendingApplicationsCount,
                'trend' => $this->formatTrend($pendingCurrent, $pendingPrevious),
                'description' => 'Awaiting organizer response',
                'icon' => 'fa-hourglass-half',
            ],
            [
                'label' => 'Daily Applications',
                'value' => "{$todayApplications}/2",
                'description' => 'Daily limit applied',
                'icon' => 'fa-calendar-day',
            ],
            [
                'label' => 'Priority Score',
                'value' => $stats['priority_score'],
                'description' => 'Higher is better',
                'icon' => 'fa-ranking-star',
            ],
            [
                'label' => 'Availability Slots',
                'value' => $availabilitySummary['slots'],
                'description' => $availabilitySummary['days'] . ' days open',
                'icon' => 'fa-clock',
            ],
            // [
            //     'label' => 'Completion Rate',
            //     'value' => $completionRate !== null ? $completionRate . '%' : '—',
            //     'description' => 'Completed vs total',
            //     'icon' => 'fa-check-circle',
            // ],
        ];

        $quickActions = [
            [
                'label' => 'Find Match',
                'icon' => 'fa-search',
                'url' => route('referee.matches.available'),
                'variant' => 'primary',
            ],
            [
                'label' => 'Update Availability',
                'icon' => 'fa-calendar-plus',
                'url' => route('referee.availability.form'),
                'variant' => 'emerald',
            ],
            
           
            [
                'label' => 'Blog Post',
                'icon' => 'fa-blog',
                'url' => route('referee.matches.available'),
                'variant' => 'violet',
                'disabled' => true,
            ],
           
            [
                'label' => 'Directory',
                'icon' => 'fa-address-book',
                'url' => route('referee.matches.available'),
                'variant' => 'rose',
            ],
        ];

        $userColumns = Schema::hasTable('users') ? Schema::getColumnListing('users') : [];
        $userSelect = collect(['id', 'name', 'first_name', 'last_name', 'last_activity'])
            ->filter(fn($column) => in_array($column, $userColumns))
            ->all();
        if (empty($userSelect)) {
            $userSelect = in_array('name', $userColumns) ? ['id', 'name'] : ['id'];
        }

        $relevantTournamentIds = $assignedMatches->pluck('tournament_id')
            ->merge($pendingApplications->pluck('match.tournament_id'))
            ->merge($recentAssignments->pluck('tournament_id'))
            ->filter()
            ->unique()
            ->values();

        $relatedRefereeIds = collect();

        if ($relevantTournamentIds->isNotEmpty()) {
            $relatedRefereeIds = GameMatch::whereIn('tournament_id', $relevantTournamentIds)
                ->whereNotNull('referee_id')
                ->where('referee_id', '!=', $referee->id)
                ->pluck('referee_id');
        }

        $relatedRefereeIds = $relatedRefereeIds->merge(
            Application::whereIn('match_id', $assignedMatches->pluck('id'))
                ->where('referee_id', '!=', $referee->id)
                ->pluck('referee_id')
        )->unique()->filter();

        $peerRefereesQuery = RefereeModel::with(['user' => function ($query) use ($userSelect) {
            $query->select($userSelect);
        }])->where('id', '!=', $referee->id);

        if ($relatedRefereeIds->isNotEmpty()) {
            $peerReferees = (clone $peerRefereesQuery)
                ->whereIn('id', $relatedRefereeIds->take(24))
                ->orderBy('updated_at', 'desc')
                ->get();
        } else {
            $peerReferees = collect();
        }

        if ($peerReferees->isEmpty()) {
            $peerReferees = (clone $peerRefereesQuery)
                ->orderBy('updated_at', 'desc')
                ->limit(12)
                ->get();
        }

        if ($peerReferees->isEmpty()) {
            $peerReferees = (clone $peerRefereesQuery)
                ->orderBy('full_name')
                ->limit(12)
                ->get();
        }

        $chatContacts = $peerReferees->map(function (RefereeModel $peer) use ($now) {
            $user = $peer->user;
            $lastActivityRaw = $user?->last_activity;
            try {
                $lastActivity = $lastActivityRaw ? Carbon::createFromTimestamp((int) $lastActivityRaw) : null;
            } catch (\Exception $e) {
                $lastActivity = null;
            }

            $location = collect([$peer->city, $peer->region])
                ->filter()
                ->implode(', ');

            return [
                'name' => $peer->full_name ?: ($user?->name ?? 'Referee'),
                'role' => $peer->certification_level ? 'Level ' . $peer->certification_level : 'Certified Referee',
                'meta' => $location ?: ($peer->country ?: null),
                'is_online' => $user ? $user->isOnline() : false,
                'last_activity' => $lastActivity
                    ? $lastActivity->diffForHumans($now, Carbon::DIFF_RELATIVE_TO_NOW, true, 1)
                    : null,
            ];
        })->filter(fn($contact) => trim($contact['name']) !== '')->take(12)->values();

        if ($chatContacts->isEmpty()) {
            $chatContacts = collect([
                [
                    'name' => 'Build your referee network',
                    'role' => null,
                    'meta' => 'Invite referees to connect',
                    'is_online' => false,
                    'last_activity' => null,
                ],
            ]);
        }

        $tournamentChats = $assignedMatches->map(function (GameMatch $match) use ($now) {
            try {
                $date = $match->match_date instanceof Carbon
                    ? $match->match_date
                    : ($match->match_date ? Carbon::parse($match->match_date) : $now->copy());
            } catch (\Exception $e) {
                $date = $now->copy();
            }
            return [
                'tournament_id' => $match->tournament_id,
                'title' => optional($match->tournament)->name ?? 'Independent Match',
                'subtitle' => trim(($match->homeClub->name ?? 'TBD') . ' vs ' . ($match->awayClub->name ?? 'TBD')),
                'status' => $date->isPast() ? 'Completed' : 'Assigned',
                'last_activity' => $date,
            ];
        });

        $pendingTournamentChats = $pendingApplications->map(function (Application $application) use ($now) {
            if (!$application->match) {
                return null;
            }

            $match = $application->match;
            try {
                $date = $match->match_date instanceof Carbon
                    ? $match->match_date
                    : ($match->match_date ? Carbon::parse($match->match_date) : $now->copy());
            } catch (\Exception $e) {
                $date = $now->copy();
            }

            return [
                'tournament_id' => $match->tournament_id,
                'title' => optional($match->tournament)->name ?? 'Open Assignment',
                'subtitle' => trim(($match->homeClub->name ?? 'TBD') . ' vs ' . ($match->awayClub->name ?? 'TBD')),
                'status' => 'Pending',
                'last_activity' => $application->applied_at instanceof Carbon
                    ? $application->applied_at
                    : ($application->applied_at ? Carbon::parse($application->applied_at) : $date),
            ];
        })->filter();

        $tournamentChats = $tournamentChats
            ->merge($pendingTournamentChats)
            ->filter(fn($chat) => $chat['title'])
            ->unique('tournament_id')
            ->sortByDesc(fn($chat) => $chat['last_activity'] ?? $now)
            ->take(6)
            ->values();

        $taskReminders = collect();

        $statusStyling = [
            'Assigned' => ['badge' => 'wait', 'progress' => 70, 'color' => '#3b82f6'],
            'Upcoming' => ['badge' => 'todo', 'progress' => 60, 'color' => '#6366f1'],
            'Pending' => ['badge' => 'wait', 'progress' => 40, 'color' => '#3b82f6'],
            'Completed' => ['badge' => 'done', 'progress' => 100, 'color' => '#22c55e'],
            'Rejected' => ['badge' => 'hold', 'progress' => 10, 'color' => '#f97316'],
            'Cancelled' => ['badge' => 'hold', 'progress' => 5, 'color' => '#f97316'],
        ];

        foreach ($assignedMatches as $match) {
            $taskReminders->push([
                'title' => optional($match->tournament)->name ?? 'Independent Match',
                'task' => trim(($match->homeClub->name ?? 'TBD') . ' vs ' . ($match->awayClub->name ?? 'TBD')),
                'status' => 'Upcoming',
                'manager' => $match->homeClub->name ?? 'Organizer',
            ]);
        }

        foreach ($pendingApplications as $application) {
            if (!$application->match) {
                continue;
            }

            $taskReminders->push([
                'title' => optional($application->match->tournament)->name ?? 'Open Assignment',
                'task' => 'Awaiting confirmation',
                'status' => 'Pending',
                'manager' => $application->match->homeClub->name ?? 'Organizer',
            ]);
        }

        foreach ($recentAssignments as $match) {
            $taskReminders->push([
                'title' => optional($match->tournament)->name ?? 'Independent Match',
                'task' => trim(($match->homeClub->name ?? 'TBD') . ' vs ' . ($match->awayClub->name ?? 'TBD')),
                'status' => 'Completed',
                'manager' => $match->homeClub->name ?? 'Organizer',
            ]);
        }

        foreach ($rejectedApplications as $application) {
            if (!$application->match) {
                continue;
            }

            $taskReminders->push([
                'title' => optional($application->match->tournament)->name ?? 'Open Assignment',
                'task' => 'Application rejected',
                'status' => 'Rejected',
                'manager' => $application->match->homeClub->name ?? 'Organizer',
            ]);
        }

        $taskReminders = $taskReminders
            ->map(function ($task) use ($statusStyling) {
                $styles = $statusStyling[$task['status']] ?? $statusStyling['Pending'];
                return array_merge($task, $styles);
            })
            ->unique(fn($task) => $task['title'] . $task['task'] . $task['status'])
            ->take(6)
            ->values();

        $recentVideos = Video::with('user:id,name')
            ->where('is_ad', false)
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Video $video) => $this->buildVideoPreview($video))
            ->values();

        $videosExploreUrl = route('player.videos.explore');

        $claimedAssignment = $assignedMatches->first() ?? $recentAssignments->first();

        $applicationStats = [
            'total' => $totalApplications,
            'pending' => $pendingApplicationsCount,
            'accepted' => $acceptedApplicationsCount,
            'rejected' => $rejectedApplicationsCount,
            'completed' => $completedApplicationsCount,
        ];

        return view('referee.dashboard', compact(
            'assignedMatches',
            'pendingApplications',
            'rejectedApplications',
            'todayApplications',
            'stats',
            'summaryCards',
            'insightCards',
            'quickActions',
            'calendarEvents',
            'weather',
            'locationLabel',
            'tournamentHighlights',
            'tournamentFilters',
            'tournamentResults',
            'recentAssignments',
            'chatContacts',
            'tournamentChats',
            'taskReminders',
            'recentVideos',
            'videosExploreUrl',
            'availabilitySummary',
            'applicationStats',
            'claimedAssignment'
        ));
    }

    public function availableMatches()
    {
        $referee = Auth::user()->referee;

        // Get all upcoming matches that don't have a referee assigned
        $availableMatches = GameMatch::whereNull('referee_id')
            ->where('match_date', '>=', now()->toDateString())
            ->with(['homeClub', 'awayClub', 'tournament'])
            ->get(); 
            // Filter matches based on referee eligibility
            $eligibleMatches = $availableMatches->filter(function ($match) use ($referee) {
                return $this->isRefereeEligible($referee, $match);
            });
            

        // Remove matches that the referee has already applied for
        $appliedMatchIds = Application::where('referee_id', $referee->id)
            ->pluck('match_id')
            ->toArray();

          

        $eligibleMatches = $eligibleMatches->reject(function ($match) use ($appliedMatchIds) {
            return in_array($match->id, $appliedMatchIds);
        });

        
        // Check daily limit
        $todayApplications = Application::where('referee_id', $referee->id)
            ->whereDate('applied_at', today())
            ->count();

        $canApply = $todayApplications < 5;

        return view('referee.available-matches', compact('eligibleMatches', 'canApply', 'todayApplications'));
    }

    public function viewMatch(GameMatch $match)
    {
        return view('referee.view-match', compact('match'));
    }

    public function apply(GameMatch $match)
    {
        $referee = Auth::user()->referee;

        // Check if referee is eligible for this match
        if (!$this->isRefereeEligible($referee, $match)) {
            return back()->with('error', 'You are not eligible for this match.');
        }

        // Check if match is still available
        if ($match->referee_id) {
            return back()->with('error', 'This match has already been assigned to another referee.');
        }

        // Check daily application limit (max 2 matches per day)
        if ($this->hasReachedDailyLimit($referee)) {
            return back()->with('error', 'You have reached the daily limit of 2 match applications.');
        }

        // Check if referee already applied for this match
        $alreadyApplied = Application::where('match_id', $match->id)
            ->where('referee_id', $referee->id)
            ->exists();

        if ($alreadyApplied) {
            return back()->with('info', 'You already applied for this match.');
        }

        // Calculate priority score
        $priorityScore = $this->calculatePriorityScore($referee);

        // Create application
        Application::create([
            'match_id' => $match->id,
            'referee_id' => $referee->id,
            'status' => 'pending',
            'applied_at' => now(),
            'priority_score' => $priorityScore,
        ]);

        // Check if this referee should be automatically assigned (first to apply)
        $this->processMatchAssignment($match);

        return redirect()->route('referee.dashboard')->with('success', 'Application submitted successfully.');
    }

    /**
     * Check if referee is eligible for the match
     */
    private function isRefereeEligible($referee, $match)
    {
        // Skip level requirements for now
        // if ($match->required_referee_level && $referee->level && $referee->level < $match->required_referee_level) {
        //     return false;
        // }

        // If referee has no availability data, allow all matches (assume available)
        if ($referee->availability()->count() == 0) {
            return true;
        }
        // Check if referee is available on match date/time
        try {
            // Create a proper datetime by combining date and time
            $matchDate = \Carbon\Carbon::parse($match->match_date)->format('Y-m-d');
            $matchTime = \Carbon\Carbon::parse($match->match_time)->format('H:i:s');
            $matchDateTime = \Carbon\Carbon::parse($matchDate . ' ' . $matchTime);
        } catch (\Exception $e) {
            // If parsing fails, skip this match
            return false;
        }
        
        $dayOfWeek = $matchDateTime->format('l'); // Monday, Tuesday, etc.
        
        $availability = $referee->availability()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();
        if (!$availability) {
            return false;
        }

        // Check if match time falls within availability window
        $matchTime = $matchDateTime->format('H:i:s');
        if ($matchTime < $availability->start_time || $matchTime > $availability->end_time) {
            return false;
        }

        return true;
    }

    /**
     * Check if referee has reached daily application limit
     */
    private function hasReachedDailyLimit($referee)
    {
        $todayApplications = Application::where('referee_id', $referee->id)
            ->whereDate('applied_at', today())
            ->count();

        return $todayApplications >= 2;
    }

    /**
     * Calculate priority score for referee
     */
    private function calculatePriorityScore($referee)
    {
        $score = 0;

        // Base score from referee level
        $score += $referee->level * 10;

        // Bonus for good performance (no recent cancellations)
        $recentCancellations = Application::where('referee_id', $referee->id)
            ->where('status', 'cancelled')
            ->where('applied_at', '>=', now()->subDays(30))
            ->count();

        $score -= $recentCancellations * 20;

        // Bonus for experience (more completed matches)
        $completedMatches = Application::where('referee_id', $referee->id)
            ->where('status', 'completed')
            ->count();

        $score += $completedMatches * 5;

        return max(0, $score); // Ensure score is not negative
    }

    private function formatTrend(float|int $current, float|int $previous): array
    {
        $current = (float) $current;
        $previous = (float) $previous;
        $difference = $current - $previous;
        $direction = $difference >= 0 ? 'up' : 'down';

        if ($previous == 0.0) {
            $percent = $current > 0 ? 100.0 : 0.0;
        } else {
            $percent = ($difference / abs($previous)) * 100;
        }

        return [
            'direction' => $direction,
            'percent' => round(abs($percent), 1),
            'difference' => round($difference, 1),
        ];
    }

    private function makeCalendarEventPayload(?GameMatch $match, string $status): ?array
    {
        if (!$match) {
            return null;
        }

        try {
            $date = $match->match_date instanceof Carbon
                ? $match->match_date->format('Y-m-d')
                : Carbon::parse($match->match_date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }

        $start = $date;
        if (!empty($match->match_time)) {
            try {
                $time = Carbon::parse($match->match_time)->format('H:i:s');
                $start = "{$date}T{$time}";
            } catch (\Exception $e) {
                // keep date-only start
            }
        }

        $palette = [
            'assigned' => ['#34d399', '#34d399', '#052e16'],
            'pending' => ['#facc15', '#facc15', '#1f2937'],
            'completed' => ['#38bdf8', '#38bdf8', '#0f172a'],
        ];
        $colors = $palette[$status] ?? ['#94a3b8', '#94a3b8', '#0f172a'];

        $title = sprintf(
            '%s vs %s (%s)',
            $match->homeClub->name ?? 'TBD',
            $match->awayClub->name ?? 'TBD',
            ucfirst($status)
        );

        return [
            'id' => "{$status}-{$match->id}",
            'title' => $title,
            'start' => $start,
            'backgroundColor' => $colors[0],
            'borderColor' => $colors[1],
            'textColor' => $colors[2],
            'url' => route('referee.matches.view', $match),
            'extendedProps' => [
                'venue' => $match->venue ?? 'TBD',
                'tournament' => optional($match->tournament)->name ?? 'N/A',
                'status' => $status,
            ],
        ];
    }

    /**
     * Process match assignment based on applications
     */
    private function processMatchAssignment($match)
    {
        // Get all pending applications for this match, ordered by priority
        $applications = Application::where('match_id', $match->id)
            ->where('status', 'pending')
            ->byPriority()
            ->get();

        if ($applications->isEmpty()) {
            return;
        }

        // Assign to the highest priority referee
        $topApplication = $applications->first();
        
        // Update match with referee assignment
        $match->update(['referee_id' => $topApplication->referee_id]);
        
        // Update application status
        $topApplication->update(['status' => 'accepted']);
        
        // Reject all other applications
        Application::where('match_id', $match->id)
            ->where('status', 'pending')
            ->where('id', '!=', $topApplication->id)
            ->update(['status' => 'rejected']);
    }

    /**
     * Cancel match application
     */
    public function cancelApplication(GameMatch $match)
    {
        \Log::info('Cancel application called for match ID: ' . $match->id);
        
        $referee = Auth::user()->referee;
        \Log::info('Referee ID: ' . $referee->id . ', Match referee_id: ' . $match->referee_id);

        // Check if this is a directly assigned match
        if ($match->referee_id == $referee->id) {
            \Log::info('Direct assignment found, proceeding with cancellation');
            
            // Check if cancellation is allowed (e.g., not too close to match time)
            try {
                $matchDate = \Carbon\Carbon::parse($match->match_date)->format('Y-m-d');
                $matchTime = \Carbon\Carbon::parse($match->match_time)->format('H:i:s');
                $matchDateTime = \Carbon\Carbon::parse($matchDate . ' ' . $matchTime);
            } catch (\Exception $e) {
                \Log::error('Date parsing error: ' . $e->getMessage());
                return back()->with('error', 'Invalid match date/time format.');
            }
            
            if ($matchDateTime->diffInHours(now()) < 24) {
                \Log::info('Cancellation denied - too close to match time');
                return back()->with('error', 'Cannot cancel match within 24 hours of start time.');
            }

            // Remove referee from match
            $match->update(['referee_id' => null]);
            \Log::info('Match referee_id set to null');

            // Process next available referee
            $this->processMatchAssignment($match);

            return redirect()->route('referee.dashboard')->with('success', 'Match assignment cancelled successfully.');
        }

        \Log::info('No direct assignment found, checking applications');
        
        // Otherwise, look for accepted application
        $application = Application::where('match_id', $match->id)
            ->where('referee_id', $referee->id)
            ->where('status', 'accepted')
            ->first();

        if (!$application) {
            \Log::warning('No accepted application found');
            return back()->with('error', 'No accepted application found for this match.');
        }

        \Log::info('Accepted application found, proceeding with cancellation');
        
        // Check if cancellation is allowed (e.g., not too close to match time)
        try {
            $matchDate = \Carbon\Carbon::parse($match->match_date)->format('Y-m-d');
            $matchTime = \Carbon\Carbon::parse($match->match_time)->format('H:i:s');
            $matchDateTime = \Carbon\Carbon::parse($matchDate . ' ' . $matchTime);
        } catch (\Exception $e) {
            \Log::error('Date parsing error: ' . $e->getMessage());
            return back()->with('error', 'Invalid match date/time format.');
        }
        
        if ($matchDateTime->diffInHours(now()) < 24) {
            \Log::info('Cancellation denied - too close to match time');
            return back()->with('error', 'Cannot cancel match within 24 hours of start time.');
        }

        // Update application status
        $application->update(['status' => 'cancelled']);
        \Log::info('Application status updated to cancelled');

        // Remove referee from match
        $match->update(['referee_id' => null]);
        \Log::info('Match referee_id set to null');

        // Process next available referee
        $this->processMatchAssignment($match);

        return redirect()->route('referee.dashboard')->with('success', 'Match application cancelled successfully.');
    }

    // === Availability ===

    public function availabilityForm()
    {
        $referee = Auth::user()->referee;
        
        // Get existing availability data
        $availability = [];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        foreach ($days as $day) {
            $availability[$day] = [
                'available' => false,
                'from' => '',
                'to' => ''
            ];
        }
        
        // Load existing availability from database
        $existingAvailability = $referee->availability;
        foreach ($existingAvailability as $avail) {
            $dayName = $avail->day_of_week;
            if (isset($availability[$dayName])) {
                $availability[$dayName] = [
                    'available' => $avail->is_available,
                    'from' => $avail->start_time->format('H:i'),
                    'to' => $avail->end_time->format('H:i')
                ];
            }
        }
        
        return view('referee.availability', compact('referee', 'availability'));
    }

    public function availabilityJson()
    {
        $referee = Auth::user()->referee;

        $events = $referee->availability->where('is_available', true)->map(function ($item) {
            // Convert day_of_week to next occurrence date
            $nextDay = date('Y-m-d', strtotime("next {$item->day_of_week}"));
            
            return [
                'id' => $item->id,
                'title' => 'Available',
                'start' => $nextDay . 'T' . $item->start_time->format('H:i:s'),
                'end' => $nextDay . 'T' . $item->end_time->format('H:i:s'),
                'color' => '#28a745',
            ];
        });

        return response()->json($events);
    }

    public function storeAvailability(Request $request)
    {
        $referee = Auth::user()->referee;

        // Clear existing availability
        $referee->availability()->delete();

        // Process weekly availability
        if ($request->has('availability')) {
            foreach ($request->availability as $day => $data) {
                $isAvailable = isset($data['available']) && $data['available'];
                $hasTimeRange = !empty($data['from']) && !empty($data['to']);
                
                // Check if this day already exists
                $existingAvailability = $referee->availability()->where('day_of_week', $day)->first();
                
                if ($existingAvailability) {
                    // Update existing record
                    $existingAvailability->update([
                        'is_available' => $isAvailable,
                        'start_time' => $data['from'] ?? '00:00:00',
                        'end_time' => $data['to'] ?? '23:59:59',
                    ]);
                } else {
                    // Create new record
                    RefereeAvailability::create([
                        'referee_id' => $referee->id,
                        'day_of_week' => $day,
                        'is_available' => $isAvailable,
                        'start_time' => $data['from'] ?? '00:00:00',
                        'end_time' => $data['to'] ?? '23:59:59',
                    ]);
                }
            }
        }

        return redirect()->route('referee.availability.form')->with('success', 'Availability updated successfully.');
    }

    public function deleteAvailability($id)
    {
        $referee = Auth::user()->referee;
        $availability = $referee->availability()->findOrFail($id);
        $availability->delete();

        return response()->json(['success' => true]);
    }

    private function buildVideoPreview(Video $video): array
    {
        return [
            'id' => $video->id,
            'title' => $video->title ?: 'Spotlight Video',
            'description' => Str::limit($video->description ?? '', 160),
            'thumbnail' => $this->resolveVideoThumbnail($video),
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

            $normalizedThumb = ltrim($rawThumbnail, '/');
            $normalizedThumb = preg_replace('#^storage/app/public/#', '', $normalizedThumb);

            $candidates = array_unique(array_filter([
                $normalizedThumb,
                Str::startsWith($normalizedThumb, 'public/storage/') ? Str::after($normalizedThumb, 'public/storage/') : null,
                Str::startsWith($normalizedThumb, 'public/') ? Str::after($normalizedThumb, 'public/') : null,
                Str::startsWith($normalizedThumb, 'storage/') ? Str::after($normalizedThumb, 'storage/') : null,
            ]));

            try {
                $publicDisk = Storage::disk('public');
                foreach ($candidates as $candidate) {
                    if ($publicDisk->exists($candidate)) {
                        return $publicDisk->url($candidate);
                    }
                }
            } catch (\Throwable $e) {
                // ignore
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
            $queryString = parse_url($video->playback_url, PHP_URL_QUERY);
            parse_str($queryString ?? '', $queryParts);
            if (!empty($queryParts['v'])) {
                return 'https://img.youtube.com/vi/' . $queryParts['v'] . '/hqdefault.jpg';
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
}
