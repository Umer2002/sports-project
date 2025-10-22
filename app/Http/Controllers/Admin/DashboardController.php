<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Sport;
use App\Models\Task;
use App\Models\User;
use App\Models\Video;
use App\Services\DashboardStatsService;
use App\Services\EventService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Str;

class DashboardController extends Controller
{
    public function index()
    {
        $stats           = DashboardStatsService::getDashboardStats() ?? [];
        $chartData       = DashboardStatsService::getMonthlyChartData() ?? [];
        $latestEntries   = DashboardStatsService::getLatestEntries() ?? [];
        $tournamentStats = DashboardStatsService::getTournamentStats() ?? [];
        $reminders       = DashboardStatsService::getReminders() ?? [];
        $events          = EventService::getAllEvents() ?? collect();
        $tasks           = Task::with('user')->latest()->get();
        $now             = now();

        // ✅ Recent Videos Section
        $recentVideos = Video::where('is_ad', false)
            ->latest()
            ->limit(6)
            ->get()
            ->map(function (Video $video) {
                return $this->buildVideoPreview($video);
            })
            ->values();

        $featuredVideo = $recentVideos->first() ?: null;
        $videoAds      = Video::where('is_ad', true)->latest()->limit(3)->get();

        // ✅ Onboarding Countdown (Dynamic Sports Counts)
        $sportsCounts = \DB::table('sports')
            ->select('sports.name', \DB::raw('COUNT(players.id) as total_players'))
            ->leftJoin('players', 'players.sport_id', '=', 'sports.id')
            ->whereIn('sports.name', ['Soccer', 'Basketball', 'American Football', 'Baseball', 'Hockey'])
            ->groupBy('sports.name')
            ->pluck('total_players', 'sports.name');

        $onboardingData = [
            'Soccer'     => $sportsCounts['Soccer'] ?? 0,
            'Basketball' => $sportsCounts['Basketball'] ?? 0,
            'Football'   => $sportsCounts['Football'] ?? 0,
            'Baseball'   => $sportsCounts['Baseball'] ?? 0,
            'Hockey'     => $sportsCounts['Hockey'] ?? 0,
        ];

        // ✅ Tournament Data (for Tournament Search Section)
        $tournamentDirectoryEntries = \App\Models\Tournament::query()
            ->with(['state:id,name', 'city:id,name', 'division:id,name', 'sport:id,name'])
            ->withCount('teams')
            ->orderByRaw('CASE WHEN start_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('start_date')
            ->get()
            ->map(function ($tournament) {
                $registrationCutoff = $tournament->registration_cutoff_date;
                $startDate          = $tournament->start_date;
                $endDate            = $tournament->end_date;

                $statusType     = 'closed';
                $statusClass    = 'status-closed';
                $statusLabel    = 'Closed';
                $actionLabel    = 'Details';
                $actionDisabled = true;

                if ($tournament->joining_type === 'waitlist') {
                    $statusType     = 'waitlist';
                    $statusClass    = 'status-waitlist';
                    $statusLabel    = 'Waitlist';
                    $actionLabel    = 'Join Waitlist';
                    $actionDisabled = false;
                } elseif (($registrationCutoff && $registrationCutoff->isFuture()) || (! $registrationCutoff && $startDate && $startDate->isFuture())) {
                    $statusType     = 'open';
                    $statusClass    = 'status-open';
                    $statusLabel    = 'Open';
                    $actionLabel    = 'Register';
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
                    'id'              => $tournament->id,
                    'name'            => $tournament->name ?? 'Unnamed Tournament',
                    'city'            => optional($tournament->city)->name ?? '—',
                    'state'           => optional($tournament->state)->name ?? '—',
                    'dates'           => $dateLabel,
                    'division'        => optional($tournament->division)->name ?? '—',
                    'teams'           => $tournament->teams_count ?? 0,
                    'fee'             => $tournament->joining_fee,
                    'sport'           => optional($tournament->sport)->name ?? '—',
                    'gender'          => optional($tournament->gender)->name ?? '—',
                    'status_type'     => $statusType,
                    'status_class'    => $statusClass,
                    'status_label'    => $statusLabel,
                    'action_label'    => $actionLabel,
                    'action_disabled' => $actionDisabled,
                    'month_key'       => $monthLabel ? $startDate->format('Y-m') : null,
                    'month_label'     => $monthLabel ?? 'TBD',
                ];
            })
            ->values();

        $tournamentFilterOptions = [
            'states'    => $tournamentDirectoryEntries->pluck('state')->filter(fn($v) => $v && $v !== '—')->unique()->sort()->values()->all(),
            'cities'    => $tournamentDirectoryEntries->pluck('city')->filter(fn($v) => $v && $v !== '—')->unique()->sort()->values()->all(),
            'sports'    => $tournamentDirectoryEntries->pluck('sport')->filter(fn($v) => $v && $v !== '—')->unique()->sort()->values()->all(),
            'divisions' => $tournamentDirectoryEntries->pluck('division')->filter(fn($v) => $v && $v !== '—')->unique()->sort()->values()->all(),
            'statuses'  => $tournamentDirectoryEntries
                ->pluck('status_label', 'status_type')
                ->filter(fn($label, $type) => $type && $label)
                ->unique()
                ->map(fn($label, $type) => ['value' => $type, 'label' => $label])
                ->values()
                ->all(),
            'months'    => $tournamentDirectoryEntries
                ->map(fn($entry) => [
                    'value' => $entry['month_key'],
                    'label' => $entry['month_label'],
                ])
                ->filter(fn($entry) => $entry['value'])
                ->unique('value')
                ->sortBy('value')
                ->values()
                ->all(),
        ];

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
                $end   = $tournament->end_date;

                $status = 'Upcoming';
                if ($start && $start->isPast() && (! $end || $end->isFuture())) {
                    $status = 'Live Now';
                } elseif ($end && $end->isPast()) {
                    $status = 'Closed';
                }

                $initials = Str::of($tournament->name ?? 'Tournament')
                    ->replaceMatches('/[^A-Za-z0-9 ]+/u', ' ')
                    ->trim()
                    ->explode(' ')
                    ->filter()
                    ->map(fn($part) => Str::upper(Str::substr($part, 0, 1)))
                    ->take(2)
                    ->implode('') ?: 'TC';

                return [
                    'id'          => $tournament->id,
                    'name'        => $tournament->name ?? 'Tournament',
                    'location'    => $tournament->location ?? 'TBD',
                    'start_label' => $start ? $start->format('M d') : null,
                    'end_label'   => $end ? $end->format('M d') : null,
                    'status'      => $status,
                    'is_closed'   => $status === 'Closed',
                    'initials'    => $initials,
                ];
            });

        // Active tournament details - get the most recent active tournament
        $activeTournament = \App\Models\Tournament::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderBy('start_date', 'desc')
            ->first();

        $activeTournamentName = $activeTournament ? $activeTournament->name : 'Spring Cup 2025';
        $tournamentFormat     = $activeTournament ? 'Round Robin' : 'Round Robin'; // Default format
        $tournamentDates      = $activeTournament ? $activeTournament->start_date->format('M d') . ' - ' . $activeTournament->end_date->format('M d') : 'May 10 - May 14';
        $tournamentLocation   = $activeTournament ? $activeTournament->location : 'Ottawa Sports Dome';
        $tournamentTeams      = $activeTournament ? $activeTournament->teams()->count() . ' Teams. 2 Divisions' : '12 Teams. 2 Divisions';
        $tournamentStatus     = $activeTournament ? 'Active' : 'Registration Open';

        $clubSportStats = \App\Models\PlayerGameStat::where('sport_id', auth()->user()->sport_id)->first();
        $paypalLink = auth()->user()->club->paypal_link;
        $inactivePlayers = 0;
        $teams = \App\Models\Team::with('club')->get();

        // ✅ Merge all data
        $viewData = [
            'featuredVideo'              => $featuredVideo,
            'recentVideos'               => $recentVideos,
            'videoAds'                   => $videoAds,
            'reminders'                  => $reminders,
            'events'                     => $events,
            'onboardingData'             => $onboardingData,
            'tournamentDirectoryEntries' => $tournamentDirectoryEntries,
            'tournamentFilterOptions'    => $tournamentFilterOptions,
            'tournamentChatRooms'        => $tournamentChatRooms,
            'activeTournamentName'       => $activeTournamentName,
            'tournamentFormat'           => $tournamentFormat,
            'tournamentDates'            => $tournamentDates,
            'tournamentLocation'         => $tournamentLocation,
            'tournamentTeams'            => $tournamentTeams,
            'tournamentStatus'           => $tournamentStatus,
            'clubSportStats'             => $clubSportStats,
            'paypalLink'                 => $paypalLink,
            'inactivePlayers'            => $inactivePlayers,
            'teams'                      => $teams, // ✅ Add this line
        ];

        $viewData = array_merge($viewData, $stats, $chartData, $latestEntries, $tournamentStats);

        return view('admin.dashboard', $viewData);
    }

    private function buildVideoPreview(Video $video): array
    {
        return [
            'id'             => $video->id,
            'title'          => $video->title ?: 'Highlight',
            'description'    => Str::limit($video->description ?? '', 130),
            'thumbnail'      => $this->resolveVideoThumbnail($video),
            'show_url'       => route('player.videos.show', $video),
            'duration'       => $this->formatVideoDuration($video->duration ?? null),
            'uploaded_human' => optional($video->created_at)->diffForHumans() ?? '',
            'author'         => optional($video->user)->name ?? 'Play2Earn',
        ];
    }

    private function resolveVideoThumbnail(Video $video): string
    {
        $placeholder = asset('assets/club-dashboard-main/assets/videotthump.png');

        $rawThumbnail = $video->thumbnail ?? $video->thumbnail_url ?? $video->poster ?? $video->cover_image ?? $video->image ?? $video->mobile_app_image ?? null;

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
            if (! empty($queryParts['v'])) {
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
            $seconds   = (int) $duration;
            $minutes   = intdiv($seconds, 60);
            $remaining = $seconds % 60;

            return sprintf('%02d:%02d', $minutes, $remaining);
        }

        return (string) $duration;
    }
}
