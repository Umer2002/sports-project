<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Sport;
use App\Models\Blog;
use App\Models\Event;
use App\Models\Chat;
use App\Models\Order;
use App\Models\Task;
use App\Models\PickupGame;
use App\Models\Reward;
use App\Models\PlayerReward;
use App\Models\PlayerStat;
use App\Models\Stat;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;

class PublicProfileController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function show($playerId)
    {
        $player = Player::with(['stats', 'ads', 'sport', 'club', 'position', 'user'])
            ->findOrFail($playerId);

        // Get weather data
        $weather = $this->weatherService->getCurrentWeather();

        // Get player stats
        $statsWithValues = DB::table('player_stats')
            ->select('id as stat_id', 'stat1', 'stat2', 'stat3', 'stat4')
            ->where('player_id', $player->id)
            ->get();

        // Get player rewards
        $playerRewards = DB::table('rewards')
            ->join('player_rewards', 'player_rewards.reward_id', '=', 'rewards.id')
            ->select('rewards.name', 'rewards.image')
            ->where('player_rewards.user_id', $player->user_id)
            ->get();

        // Get player's posts
        $posts = Blog::where('user_id', $player->user_id)->take(3)->get();

        // Get pickup games for the player's sport
        $pickupGames = collect();
        if ($player->sport_id) {
            $pickupGames = PickupGame::with(['sport', 'participants'])
                ->where('sport_id', $player->sport_id)
                ->where('game_datetime', '>=', now())
                ->get();
        }

        // Get all rewards for display
        $allRewards = Reward::all();

        // Determine the theme based on sport
        $theme = $this->getThemeBySport($player->sport);

        // Check if the theme file exists, if not fall back to swimming theme (which is complete)
        $themePath = resource_path("views/public/profiles/{$theme}.blade.php");
        if (!file_exists($themePath)) {
            $theme = 'swimming'; // Use swimming theme as fallback since it's complete
        }

        // Get team members if player has a team
        $teamMembers = collect();
        if ($player->team) {
            $teamMembers = Player::with(['user', 'position'])
                ->where('team_id', $player->team_id)
                ->where('id', '!=', $player->id)
                ->get();
        }

        // Get club history
        $clubHistory = $player->sportHistories()
            ->with(['club', 'team'])
            ->orderBy('started_at', 'desc')
            ->get();

        // Get achievements/awards
        $achievements = $player->sportHistories()
            ->whereNotNull('achievements')
            ->get()
            ->pluck('achievements')
            ->flatten(1);

        return view("public.profiles.{$theme}", compact(
            'player',
            'weather',
            'statsWithValues',
            'playerRewards',
            'posts',
            'pickupGames',
            'allRewards',
            'teamMembers',
            'clubHistory',
            'achievements'
        ));
    }

    public function test()
    {
        // Get a sample player for testing
        $player = Player::with(['sport', 'club', 'position', 'user'])->first();

        if (!$player) {
            return response()->json(['error' => 'No players found in database']);
        }

        $theme = $this->getThemeBySport($player->sport);

        return response()->json([
            'player_id' => $player->id,
            'player_name' => $player->name,
            'sport' => $player->sport ? $player->sport->name : 'No sport',
            'theme' => $theme,
            'profile_url' => route('player.profile', $player->id)
        ]);
    }

    private function getThemeBySport($sport)
    {
        if (!$sport) {
            return 'default';
        }

        // Map sport names to theme directories
        $sportThemeMap = [
            'Swimming' => 'swimming',
            'BASEBALL' => 'Baseball',
            'BASKETBALL' => 'BasketBall',
            'Boxing' => 'Boxer',
            'Mixed Martial Arts' => 'MMA',
            'FOOTBALL' => 'american',
            'Soccer' => 'american', // Map Soccer to american theme
            'Field hockey' => 'field-hocky',
            'Gymnastics' => 'Gymnastic',
            'Lacrosse' => 'lacrosse',
            'Track and Field' => 'track-and-field',
            'Volleyball' => 'Volleyball',
        ];

        $sportName = $sport->name;

        // Check for exact match first
        if (isset($sportThemeMap[$sportName])) {
            return $sportThemeMap[$sportName];
        }

        // Check for case-insensitive match
        foreach ($sportThemeMap as $dbSport => $theme) {
            if (strtolower($dbSport) === strtolower($sportName)) {
                return $theme;
            }
        }

        // If no match found, return default
        return 'default';
    }
}
