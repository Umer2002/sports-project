<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Task;
use App\Services\WeatherService;
use Illuminate\Support\Str;

class PublicClubProfileController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function show(Club $club)
    {
        $club->loadMissing([
            'sport',
            'teams.players.stats',
            'teams.players.position',
            'teams.players.user',
            'players.stats',
            'players.position',
            'players.user',
        ]);

        // Get weather data for the club's location
        $weather = $this->weatherService->getCurrentWeather($club->city ?? 'Ottawa');

        // Get club statistics
        $clubTeams = $club->teams;
        $totalTeams = $clubTeams->count();
        $totalPlayers = $clubTeams->sum(fn($team) => $team->players->count());
        $totalCoaches = Coach::whereHas('teams', function ($query) use ($club) {
            $query->where('club_id', $club->id);
        })->count();
        $totalPosts = Blog::whereHas('user', function ($query) use ($club) {
            $query->where('club_id', $club->id);
        })->count();

        // Get recent posts from blogs table
        $posts = Blog::whereHas('user', function ($query) use ($club) {
            $query->where('club_id', $club->id);
        })->with('user')->latest()->limit(5)->get();

        // Get teams directly from teams table
        $teams = $clubTeams->loadMissing('players');

        // Get coaches through teams
        $coaches = Coach::whereHas('teams', function ($query) use ($club) {
            $query->where('club_id', $club->id);
        })->get();

        // Get players through teams
        $players = $clubTeams->flatMap(fn($team) => $team->players)
            ->merge($club->players)
            ->unique('id');

        // Get recent tasks assigned to club users
        $tasks = Task::whereHas('user', function ($query) use ($club) {
            $query->where('club_id', $club->id);
        })->latest()->limit(10)->get();

        // Determine the theme based on sport
        $theme = $this->getThemeBySport($club->sport);
        // if (! view()->exists("public.clubs.{$theme}")) {
        if (!is_dir(public_path("assets/clubs/{$theme}"))) {
            $theme = 'default';
        }


        // return view("public.clubs.{$theme}", compact(
        return view("public.clubs.football", compact(
            'club',
            'weather',
            'totalPlayers',
            'totalTeams',
            'totalCoaches',
            'totalPosts',
            'posts',
            'teams',
            'coaches',
            'players',
            'tasks',
            'theme'
        ));
    }

    public function test()
    {
        // Get a sample club for testing
        $club = Club::with(['sport', 'teams'])->first();

        if (!$club) {
            return response()->json(['error' => 'No clubs found in database'], 404);
        }

        return redirect()->route('public.club.profile', $club->slug);
    }

    public function showRegistration($clubId)
    {
        $club = Club::with(['sport'])->findOrFail($clubId);
        $user = auth()->user();

        return view('public.clubs.registration', compact('club', 'user'));
    }

    private function getThemeBySport($sport)
    {
        if (! $sport) {
            return 'default';
        }

        // Normalize the sport name so minor data entry differences do not break theming.
        $normalized = Str::of($sport->name)->lower()->trim();
        $slug = Str::slug($normalized);

        // $sportThemeMap = [
        //     'football' => 'football',
        //     'american-football' => 'football',
        //     'soccer' => 'football',
        //     'futbol' => 'football',
        //     'gymnastics' => 'gymnastic',
        //     'gymnastic' => 'gymnastic',
        //     'lacrosse' => 'lacrose',
        //     'lacrose' => 'lacrose',
        //     'tennis' => 'tenis',
        //     'basketball' => 'basketball',
        //     'wrestling' => 'wrestling',
        //     'field-hockey' => 'lacrose',
        //     'hockey' => 'lacrose',
        // ];

        $sportThemeMap = [
            'basketball' => 'basketball',
            'american-football' => 'football',
            'soccer' => 'football',
            'football' => 'football',
            'hockey' => 'hockey',
            'field-hockey' => 'field-hockey',
            'rugby' => 'rugby',
            'boxing' => 'boxing',
            'gymnastics' => 'gymnastic',
            'volleyball' => 'volleyball',
            'swimming' => 'swimming',
            'lacrosse' => 'lacrose',
            'wrestling' => 'wrestling',
            'mixed-martial-arts' => 'mixed-martial-arts',
            'track-and-field' => 'track-and-field',
            'golf' => 'golf',
            'tennis' => 'tenis',
        ];

        // Exact slug match.
        if (isset($sportThemeMap[$slug])) {
            return $sportThemeMap[$slug];
        }
        // Partial match to catch names like "Junior Football Club" or "U18 Soccer".
        foreach ($sportThemeMap as $match => $theme) {
            if (Str::contains($slug, $match)) {
                return $theme;
            }
        }

        return 'default';
    }
}
