<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\{Coach, Task, Event, Player, Team};
use App\Services\EventService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private function composeDashboardData(\App\Models\Coach $coach)
    {
        // Get tasks assigned to players in coach's teams
        $teamIds = $coach->teams()->pluck('teams.id');
        $playerUserIds = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->pluck('user_id');

        $tasks = Task::whereIn('assigned_to', $playerUserIds)
            ->orWhere('assigned_to', auth()->id())
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Get teams assigned to this coach
        $teams = $coach->teams()->with(['sport', 'club'])->get();
        $teamCount = $teams->count();

        // Get all players from coach's teams
        $playerIds = $teams->flatMap(function ($team) {
            return $team->players()->pluck('players.id');
        })->unique();
        
        $activePlayers = Player::whereIn('id', $playerIds)->count();
        $inactivePlayers = 0; // Can be calculated based on last activity

        // Training sessions and events
        $events = collect();
        foreach ($teams as $team) {
            $teamEvents = EventService::getClubEvents($team->club_id ?? 0);
            $events = $events->merge($teamEvents);
        }

        // Match statistics - get club IDs from teams
        $clubIds = $teams->pluck('club_id')->filter()->unique();
        
        $scheduledMatches = \App\Models\GameMatch::where(function($query) use ($clubIds) {
            $query->whereIn('home_club_id', $clubIds)
                  ->orWhereIn('away_club_id', $clubIds);
        })->where('match_date', '>', now())->count();

        $completedMatches = \App\Models\GameMatch::where(function($query) use ($clubIds) {
            $query->whereIn('home_club_id', $clubIds)
                  ->orWhereIn('away_club_id', $clubIds);
        })->where('match_date', '<', now())->count();

        // Injury reports for players in coach's teams
        $injuryReports = \App\Models\InjuryReport::whereIn('player_id', $playerIds)->count();

        // Player transfers
        $playerTransfers = \App\Models\PlayerTransfer::whereIn('player_id', $playerIds)->count();

        // Tournament data
        $tournamentCount = \App\Models\Tournament::count();
        $now = now();
        $activeTournaments = \App\Models\Tournament::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->count();
        $upcomingTournaments = \App\Models\Tournament::where('start_date', '>', $now)->count();

        // Active tournament details
        $activeTournament = \App\Models\Tournament::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderBy('start_date', 'desc')
            ->first();

        $activeTournamentName = $activeTournament ? $activeTournament->name : 'Spring Cup 2025';
        $tournamentFormat = $activeTournament ? 'Round Robin' : 'Round Robin';
        $tournamentDates = $activeTournament ? $activeTournament->start_date->format('M d') . ' - ' . $activeTournament->end_date->format('M d') : 'May 10 - May 14';
        $tournamentLocation = $activeTournament ? $activeTournament->location : 'Ottawa Sports Dome';
        $tournamentTeams = $activeTournament ? $activeTournament->teams()->count() . ' Teams. 2 Divisions' : '12 Teams. 2 Divisions';
        $tournamentStatus = $activeTournament ? 'Active' : 'Registration Open';

        // Trend calculations
        $lastMonthInjuries = \App\Models\InjuryReport::whereIn('player_id', $playerIds)
            ->where('created_at', '>=', now()->subMonth()->subMonth())
            ->where('created_at', '<', now()->subMonth())
            ->count();
        $injuryTrend = $lastMonthInjuries > 0 ? (($injuryReports - $lastMonthInjuries) / $lastMonthInjuries) * 100 : 0;

        $lastMonthTransfers = \App\Models\PlayerTransfer::whereIn('player_id', $playerIds)
            ->where('created_at', '>=', now()->subMonth()->subMonth())
            ->where('created_at', '<', now()->subMonth())
            ->count();
        $transferTrend = $lastMonthTransfers > 0 ? (($playerTransfers - $lastMonthTransfers) / $lastMonthTransfers) * 100 : 0;

        $activePlayerTrendDirection = $teamTrendDirection = $injuryTrendDirection = 'up';
        $activePlayerTrendPercent = $teamTrendPercent = $injuryTrendPercent = '0';

        // Weather data
        $weatherService = new \App\Services\WeatherService();
        $weather = $weatherService->getCurrentWeather('Ottawa', 'CA');

        // Social links
        $socialLinks = $coach->socail_links ?? [];

        // Chat roster - Players
        $playersForChat = Player::whereIn('id', $playerIds)
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
                $avatarPath = $player->photo ?: optional($player->user)->profile_photo_path;

                return $this->makeChatContact($name, 'Player', $meta, $avatarPath, $isOnline, optional($player->user)->id);
            })
            ->values();

        // Chat roster - Team members (other coaches if any)
        $coachesForChat = collect();
        foreach ($teams as $team) {
            $teamCoaches = $team->coaches()->where('coaches.id', '!=', $coach->id)->get();
            $coachesForChat = $coachesForChat->merge($teamCoaches);
        }
        
        $coachesForChat = $coachesForChat->map(function (Coach $otherCoach) {
            $name = trim(trim($otherCoach->first_name . ' ' . $otherCoach->last_name));
            if ($name === '') {
                $name = optional($otherCoach->user)->name ?? $otherCoach->email ?? 'Coach #' . $otherCoach->id;
            }

            $teamNames = $otherCoach->teams->pluck('name')->filter();
            $teamsLabel = $teamNames->take(2)->implode(', ');
            if ($teamNames->count() > 2) {
                $teamsLabel .= ' +' . ($teamNames->count() - 2) . ' more';
            }

            $metaParts = $teamsLabel ? [$teamsLabel] : [];
            if ($otherCoach->sport && $otherCoach->sport->name) {
                $metaParts[] = $otherCoach->sport->name;
            }
            $meta = empty($metaParts) ? null : implode(' • ', $metaParts);

            $isOnline = (bool) optional($otherCoach->user)->isOnline();
            $avatarPath = $otherCoach->photo ?: optional($otherCoach->user)->profile_photo_path;

            return $this->makeChatContact($name, 'Coach', $meta, $avatarPath, $isOnline, optional($otherCoach->user)->id);
        })->values();

        $chatCoaches = $coachesForChat;
        $chatPlayers = $playersForChat;

        // Awards assigned by this coach to players in their teams
        $awardsAssigned = \App\Models\PlayerReward::whereHas('player', function($query) use ($playerUserIds) {
            $query->whereIn('user_id', $playerUserIds);
        })->where('issued_by', auth()->id())->count();

        // Get rewards and players for award assignment modal
        $rewards = \App\Models\Reward::all();
        $modalPlayers = \App\Models\Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->with(['user', 'teams'])->get();
        
        // Alias for task assignment modal compatibility
        $players = $modalPlayers;

        return compact(
            'coach', 'teams', 'teamCount', 'activePlayers', 'inactivePlayers',
            'injuryReports', 'playerTransfers', 'injuryTrend', 'transferTrend',
            'activePlayerTrendDirection', 'activePlayerTrendPercent', 'teamTrendDirection', 'teamTrendPercent',
            'injuryTrendDirection', 'injuryTrendPercent', 'events', 'tasks',
            'weather', 'socialLinks', 'tournamentCount', 'activeTournaments', 'upcomingTournaments',
            'scheduledMatches', 'completedMatches', 'activeTournamentName', 'tournamentFormat',
            'tournamentDates', 'tournamentLocation', 'tournamentTeams', 'tournamentStatus',
            'chatCoaches', 'chatPlayers', 'awardsAssigned', 'rewards', 'modalPlayers', 'players'
        );
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

    public function index()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user has coach role
        if (!auth()->user()->hasRole('coach')) {
            abort(403, 'Access denied. Coach role required.');
        }

        // Get coach profile
        $user = auth()->user();
        $coach = $user->coach;
        
        if (!$coach) {
            // If coach profile doesn't exist, redirect to setup/profile creation
            return redirect()->route('coach.setup')->with('error', 'Please complete your coach profile.');
        }

        $data = $this->composeDashboardData($coach);
        return view('coach.dashboard', $data);
    }

    public function setup()
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $user = auth()->user();
        $coach = $user->coach;
        
        if ($coach) {
            return redirect()->route('coach-dashboard');
        }

        $sports = \App\Models\Sport::pluck('name', 'id');
        return view('coach.setup', compact('sports'));
    }

    public function storeSetup(\Illuminate\Http\Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'sport_id' => 'required|exists:sports,id',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
        ]);

        $user = auth()->user();
        
        // Create coach record for this user
        $coach = Coach::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $user->email,
                'sport_id' => $data['sport_id'],
                'phone' => $data['phone'] ?? null,
                'bio' => $data['bio'] ?? null,
            ]
        );

        // Update user's coach_id
        if (!$user->coach_id && $coach && $coach->id) {
            $user->update(['coach_id' => $coach->id]);
        }

        return redirect()->route('coach-dashboard')->with('success', 'Coach profile setup completed.');
    }
}

