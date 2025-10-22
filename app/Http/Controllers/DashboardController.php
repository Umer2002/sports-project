<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AgeGroup;
use App\Models\Blog;
use App\Models\Chat;
use App\Models\City;
use App\Models\Division;
use App\Models\Event;
use App\Models\Gender;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PickupGame;
use App\Models\Player;
use App\Models\Reward;
use App\Models\Sport;
use App\Models\State;
use App\Models\Task;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Video;
use App\Services\WeatherService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function index()
    {
        if (! Auth::check()) {
            abort(403, 'Unauthorized');
        }
        $user = Auth::user();

        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        // Get weather data
        $weather = $this->weatherService->getCurrentWeather();

        $orders = [
            'processing' => Order::where('status', 'processing')->count(),
            'on_hold'    => Order::where('status', 'on_hold')->count(),
            'delivered'  => Order::where('status', 'delivered')->count(),
        ];

        $events = Event::where('event_date', '>=', now())->get();
        $player = Player::with(['stats', 'ads', 'sport', 'club', 'user', 'position', 'teams'])
            ->where('user_id', $user->id)
            ->first();

        $hasPlayerProfile = (bool) $player;

        if (! $player) {
            $player = Player::make([
                'name'    => $user->name,
                'user_id' => $user->id,
            ]);

            $player->setRelation('user', $user);
            $player->setRelation('sport', null);
            $player->setRelation('club', null);
            $player->setRelation('position', null);
            $player->setRelation('teams', collect());
            $player->setRelation('ads', collect());
            $player->setRelation('stats', collect());
        }

        $paymentIncomplete = false;
        if ($user->hasRole('player') && $hasPlayerProfile) {
            $paymentIncomplete = ! Payment::where('player_id', $player->id)->exists();
        }

        // Get active ads for the player
        $ads = $hasPlayerProfile
            ? $player->ads()->where('active', true)->get()
            : collect();

        $chats = Chat::with(['participants', 'messages.user'])->whereIn('id', function ($query) use ($user) {
            $query->select('chat_id')
                ->from('chat_participants')
                ->where('user_id', $user->id);
        })->get();

        $teammates = collect();

        if ($hasPlayerProfile) {
            $teamIds = $player->teams->pluck('id');

            $teammates = User::whereHas('player.teams', function ($q) use ($teamIds) {
                $q->whereIn('teams.id', $teamIds);
            })
                ->where('id', '!=', $user->id)
                ->get();
        }

        $posts = Blog::where('user_id', $user->id)->take(3)->get();
        $blogs = Blog::with('user')->latest()->get();

        $pickupGames = collect();
        if ($player && $player->sport_id) {
            $pickupGames = PickupGame::with(['sport', 'participants'])
                ->where('sport_id', $player->sport_id)
                ->where('game_datetime', '>=', now())
                ->get();
        }

        // Upcoming tournaments (basic)
// Upcoming tournaments (basic)  ← replace your existing line
        $tournaments = \App\Models\Tournament::query()
            ->with(['venue', 'city', 'state', 'sport','hotels'])
            ->whereDate('end_date', '>=', now())
            ->orderBy('start_date')
            ->limit(50) // optional
            ->get();
        // dd($tournaments);
        // Recent videos and video ads (from local DB, synced with PlayTube)
        $recentVideos = Video::where('is_ad', false)
            ->latest()
            ->limit(6)
            ->get()
            ->map(function (Video $video) {
                return $this->buildVideoPreview($video);
            })
            ->values();
        $featuredVideo = $recentVideos->first();
        $videoAds      = Video::where('is_ad', true)->latest()->limit(3)->get();

        // Only show tasks assigned to the logged-in user
        $tasks = Task::with('user')
            ->where('assigned_to', $user->id)
            ->latest()
            ->get();

        $statsWithValues = $this->resolvePlayerStats($player, $hasPlayerProfile);

        $playerRewards = DB::table('rewards')
            ->join('player_rewards', 'player_rewards.reward_id', '=', 'rewards.id')
            ->select('rewards.name', 'rewards.image')
            ->where('player_rewards.user_id', $user->id)
            ->get();

        $totalRevenue           = '$' . rand(40, 60) . 'k';
        $revenueIncreasePercent = rand(50, 90);

        $injuryReports           = rand(100, 200);
        $injuryReportsChange     = rand(60, 100);
        $reportedInjuries        = $injuryReports;
        $injuriesIncreasePercent = rand(10, 40);

        $playerTransfers          = rand(60, 100);
        $transfersIncreasePercent = rand(10, 30);

        $newRegistrations             = rand(40, 70);
        $registrationsIncreasePercent = rand(30, 70);

        // --------------
        $injuryReports = DB::table('injury_reports')->count();

        $recentReports = DB::table('injury_reports')
            ->where('injury_datetime', '>=', Carbon::now()->subDays(7))
            ->count();

        $previousWeekReports = DB::table('injury_reports')
            ->whereBetween('injury_datetime', [
                Carbon::now()->subDays(14),
                Carbon::now()->subDays(7),
            ])->count();

        $injuryReportsChange = $recentReports;

        $injuriesIncreasePercent = $previousWeekReports > 0
            ? round((($recentReports - $previousWeekReports) / $previousWeekReports) * 100, 2)
            : 0; // return 0 instead of null when previous week is 0

        // ---------------------------
        $playerTransfers = DB::table('player_transfers')->count();

        $recentTransfers = DB::table('player_transfers')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        $previousTransfers = DB::table('player_transfers')
            ->whereBetween('created_at', [
                Carbon::now()->subDays(14),
                Carbon::now()->subDays(7),
            ])->count();

        $transfersIncreasePercent = $previousTransfers > 0
            ? round((($recentTransfers - $previousTransfers) / $previousTransfers) * 100, 2)
            : 0;

        // -------------------------
        $totalRegistrations = DB::table('players')->count();

        $newRegistrations = DB::table('players')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        $previousRegistrations = DB::table('players')
            ->whereBetween('created_at', [
                Carbon::now()->subDays(14),
                Carbon::now()->subDays(7),
            ])->count();

        $registrationsIncreasePercent = $previousRegistrations > 0
            ? round((($newRegistrations - $previousRegistrations) / $previousRegistrations) * 100, 2)
            : 0;

        $allRewards = Reward::all();

        // Check if user wants old dashboard design (default to new)
        $useOldDashboard = request()->get('old') === '1' || request()->routeIs('player.dashboard.old');
        $useNewDashboard = request()->routeIs('player.dashboard.new') || request()->routeIs('player.dashboard') || ! $useOldDashboard;

        // Always use new dashboard for consistency
        $viewName = 'players.dashboard-new';

        return view($viewName, compact(
            'chats',
            'orders',
            'events',
            'posts',
            'user',
            'teammates',
            'player',
            'pickupGames',
            'tournaments',
            'tasks',
            'statsWithValues',
            'playerRewards',
            'weather',
            'ads',
            'recentVideos',
            'featuredVideo',
            'videoAds',
            'blogs',

            'totalRevenue',
            'revenueIncreasePercent',
            'injuriesIncreasePercent',
            'reportedInjuries',
            'playerTransfers',
            'transfersIncreasePercent',
            'newRegistrations',
            'registrationsIncreasePercent',
            'allRewards',
            'paymentIncomplete',
            'hasPlayerProfile',
        ));
    }

    public function tournamentDirectory(Request $request)
    {
        // 1) Resolve effective sport (query > user.player > user.club)
        $sportIdFromQuery = $request->integer('sport_id');
        $user             = Auth::user();

        $derivedSportId = null;
        if (method_exists($user, 'player') && ($user->relationLoaded('player') ? $user->player : $user->player()->exists())) {
            $derivedSportId = optional($user->player)->sport_id;
        } elseif (method_exists($user, 'club') && ($user->relationLoaded('club') ? $user->club : $user->club()->exists())) {
            $derivedSportId = optional($user->club)->sport_id;
        }
        $sportId = $sportIdFromQuery ?: $derivedSportId;

        // 2) Other filters
        $stateId  = $request->integer('state_id');
        $cityId   = $request->integer('city_id');
        $division = $request->integer('division_id');
        $gender   = $request->integer('gender_id');
        $ageGroup = $request->integer('age_group_id');
        $maxFee   = $request->filled('max_fee') ? (float) $request->input('max_fee') : null;
        $status   = $request->string('status')->toString(); // open|waitlist|closed
        $dateFrom = $request->date('date_from');
        $dateTo   = $request->date('date_to');
        $today    = now()->startOfDay();

        // 3) Option datasets – all scoped by $sportId
        $sports = Sport::orderBy('name')->get(['id', 'name']);

        $divisions = Division::when($sportId, fn($q) => $q->where('sport_id', $sportId))
            ->orderBy('name')->get(['id', 'name', 'sport_id']);

        $genders = Gender::when($sportId, fn($q) => $q->where('sport_id', $sportId))
            ->ordered()->get(['id', 'label as name', 'sport_id']);

        $ageGroups = AgeGroup::when($sportId, fn($q) => $q->where('sport_id', $sportId))
            ->ordered()->get(['id', 'label as name', 'sport_id']);

        // 3a) Hard guard: if an incoming selection doesn’t belong to the sport, clear it
        $validDivisionIds = $divisions->pluck('id')->all();
        $validGenderIds   = $genders->pluck('id')->all();
        $validAgeIds      = $ageGroups->pluck('id')->all();

        if ($division && ! in_array($division, $validDivisionIds, true)) {
            $division = null;
        }

        if ($gender && ! in_array($gender, $validGenderIds, true)) {
            $gender = null;
        }

        if ($ageGroup && ! in_array($ageGroup, $validAgeIds, true)) {
            $ageGroup = null;
        }

        // States/Cities limited to tournaments of this sport (keeps lists relevant)
        $stateIdsForSport = Tournament::when($sportId, function ($q) use ($sportId) {
            if (Schema::hasColumn('tournaments', 'sport_id')) {
                $q->where('sport_id', $sportId);
            } else {
                $q->whereHas('hostClub', fn($x) => $x->where('sport_id', $sportId));
            }

        })
            ->whereNotNull('state_id')->distinct()->pluck('state_id');

        $states = State::whereIn('id', $stateIdsForSport)->orderBy('name')->get(['id', 'name']);

        $cityIdsForSport = Tournament::when($sportId, function ($q) use ($sportId) {
            if (Schema::hasColumn('tournaments', 'sport_id')) {
                $q->where('sport_id', $sportId);
            } else {
                $q->whereHas('hostClub', fn($x) => $x->where('sport_id', $sportId));
            }

        })
            ->when($stateId, fn($q) => $q->where('state_id', $stateId))
            ->whereNotNull('city_id')->distinct()->pluck('city_id');

        $cities = City::whereIn('id', $cityIdsForSport)->orderBy('name')->get(['id', 'name', 'state_id']);

        if ($cityId && ! $cities->pluck('id')->contains($cityId)) {
            $cityId = null;
        }

        // 4) Tournaments query (sport + selected filters)
        $q = Tournament::query()
            ->when(Schema::hasColumn('tournaments', 'sport_id'),
                fn($qq) => $qq->with(['sport']),
                fn($qq) => $qq->with(['hostClub.sport'])
            )
            ->with(['state', 'city', 'division', 'ageGroup', 'gender'])
            ->withCount('teams')
            ->when($sportId, function ($qq) use ($sportId) {
                if (Schema::hasColumn('tournaments', 'sport_id')) {
                    $qq->where('sport_id', $sportId);
                } else {
                    $qq->whereHas('hostClub', fn($x) => $x->where('sport_id', $sportId));
                }

            })
            ->when($stateId, fn($qq) => $qq->where('state_id', $stateId))
            ->when($cityId, fn($qq) => $qq->where('city_id', $cityId))
            ->when($division, fn($qq) => $qq->where('division_id', $division))
            ->when($gender, fn($qq) => $qq->where('gender_id', $gender))
            ->when($ageGroup, fn($qq) => $qq->where('age_group_id', $ageGroup))
            ->when($maxFee !== null, fn($qq) => $qq->where('joining_fee', '<=', $maxFee))
            ->when($dateFrom, fn($qq) => $qq->whereDate('end_date', '>=', $dateFrom))
            ->when($dateTo, fn($qq) => $qq->whereDate('start_date', '<=', $dateTo))
            ->when($status === 'open', fn($qq) => $qq->whereDate('registration_cutoff_date', '>=', $today)
                    ->where(fn($x) => $x->whereNull('joining_type')->orWhere('joining_type', 'open')))
            ->when($status === 'waitlist', fn($qq) => $qq->where('joining_type', 'waitlist'))
            ->when($status === 'closed', fn($qq) => $qq->whereDate('registration_cutoff_date', '<', $today))
            ->orderBy('start_date', 'asc');

        $tournaments = $q->paginate(20)->withQueryString();

        return view('players.directory', compact(
            'tournaments',
            'sports', 'states', 'cities', 'divisions', 'genders', 'ageGroups',
            'sportId', 'stateId', 'cityId', 'division', 'gender', 'ageGroup',
            'maxFee', 'status', 'dateFrom', 'dateTo'
        ));
    }


    private function resolvePlayerStats($player, bool $hasPlayerProfile)
    {
        if (! $hasPlayerProfile) {
            return collect();
        }

        $legacyColumnsExist = Schema::hasColumn('player_stats', 'stat_id') && Schema::hasColumn('player_stats', 'value');
        $structuredColumnsExist = Schema::hasColumn('player_stats', 'stat1') && Schema::hasColumn('player_stats', 'stat1_vlaue');

        if ($legacyColumnsExist) {
            return DB::table('player_stats')
                ->join('stats', 'player_stats.stat_id', '=', 'stats.id')
                ->select('stats.id as stat_id', 'stats.name', 'player_stats.value')
                ->where('player_stats.player_id', $player->id)
                ->get();
        }

        if (! $structuredColumnsExist) {
            return collect();
        }

        $structuredStats = DB::table('player_stats')
            ->where('player_id', $player->id)
            ->select('stat1', 'stat2', 'stat3', 'stat4', 'stat1_vlaue', 'stat2_vlaue', 'stat3_vlaue', 'stat4_vlaue')
            ->first();

        if (! $structuredStats) {
            return collect();
        }

        return collect(range(1, 4))
            ->map(function ($index) use ($structuredStats) {
                $name = $structuredStats->{"stat{$index}"} ?? null;
                $value = $structuredStats->{"stat{$index}_vlaue"} ?? null;

                if (is_null($name) && is_null($value)) {
                    return null;
                }

                return [
                    'stat_id' => $index,
                    'name' => $name ?: "Stat {$index}",
                    'value' => $value,
                ];
            })
            ->filter()
            ->values();
    }

    private function buildVideoPreview(Video $video): array
    {
        return [
            'id' => $video->id,
            'title' => $video->title ?: 'Highlight',
            'description' => Str::limit($video->description ?? '', 130),
            'thumbnail' => $this->resolveVideoThumbnail($video),
            'show_url' => route('player.videos.show', $video),
            'duration' => $this->formatVideoDuration($video->duration ?? null),
            'uploaded_human' => optional($video->created_at)->diffForHumans() ?? '',
            'author' => optional($video->user)->name ?? 'Play2Earn',
        ];
    }

    private function resolveVideoThumbnail(Video $video): string
    {
        $placeholder = asset('assets/player-dashboard/images/video-thumbnail.png');

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
                // ignore disk resolution errors
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
        } elseif ($video->playback_url && Str::contains($video->playback_url, 'youtu.be/')) {
            $path = trim(parse_url($video->playback_url, PHP_URL_PATH) ?? '', '/');
            if ($path !== '') {
                return 'https://img.youtube.com/vi/' . $path . '/hqdefault.jpg';
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
