<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Player;
use App\Models\PlayerReward;
use App\Models\PlayerStat as P;
use App\Models\Position;
use App\Models\Reward;
use App\Models\Role;
use App\Models\Sport;
use App\Models\Stat;
use App\Models\Team;
use App\Models\User;
use App\Models\Ad;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlayerController extends Controller
{
    public function index()
    {
        $players = Player::with('teams', 'club')->orderBy('id', 'desc')->get();
        return view('admin.players.index', compact('players'));
    }

    public function create()
    {
        $teams = Team::all();
        $sports = Sport::all();
        $clubs = Club::all();
        $positions = Position::all();
        $player = null;
        $stats = Stat::all();
        $rewards = Reward::all();
        $ads = Ad::where('active', true)->get();

        return view('admin.players.create', compact('teams', 'sports', 'clubs', 'positions', 'player','stats','rewards', 'ads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:players,email',
            'position' => 'required|string|max:255',
            'team_id' => 'required|exists:teams,id',
            'sport_id' => 'required|exists:sports,id',
            'social_links' => 'nullable|array',
            'stats' => 'nullable|array',
            'password' => 'required|string|min:6',
        ]);

        DB::beginTransaction();

        try {
            // Create user first
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => 0,
            ]);

            // Create player and associate user_id
            $data_player = [
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'team_id' => $request->team_id,
                'sport_id' => $request->sport_id,
                'paypal_link' => $request->paypal_link,
                'birthday' => $request->birthday,
                'age' => $request->age,
                'club_id' => $request->club_id,
                'nationality' => $request->nationality,
                'height' => $request->height,
                'weight' => $request->weight,
                'debut' => $request->debut,
                'jersey_no' => $request->jersey_no,
                'bio' => $request->bio,
                'social_links' => $request->social_links,
                'phone' => $request->phone,
                // 'city' => $request->city,
                // 'state' => $request->state,
                'nationality' => $request->nationality,
                'position_id' => $request->position_id,
            ];

            $player = Player::create($data_player);

            // Assign 'player' role
            $role = Role::where('name', 'player')->first();
            if ($role) {
                $user->roles()->attach($role);
            }

            // Add stats if provided
            // if (isset($request->stats)) {
            //     foreach ($request->stats as $key => $stat) {
            //         P::create([
            //             'player_id' => $player->id,
            //             'stat_id' => $key,
            //             'value' => $stat,
            //         ]);
            //     }
            // }
            P::where('player_id', $player->id)->delete();

            if (isset($request->stat_1) && isset($request->stat_value_1)) {
                P::create([
                    'player_id' => $player->id,
                    'stat_id'   => $request->stat_1,
                    'value'     => $request->stat_value_1,
                ]);
            }
            if (isset($request->stat_2) && isset($request->stat_value_2)) {
                P::create([
                    'player_id' => $player->id,
                    'stat_id'   => $request->stat_2,
                    'value'     => $request->stat_value_2,
                ]);
            }
            if (isset($request->stat_3) && isset($request->stat_value_3)) {
                P::create([
                    'player_id' => $player->id,
                    'stat_id'   => $request->stat_3,
                    'value'     => $request->stat_value_3,
                ]);
            }

            if ($request->has('ads')) {
                $player->ads()->sync($request->input('ads'));
            }

            DB::commit();
            return redirect()->route('admin.players.index')->with('success', 'Player and user created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create player. ' . $e->getMessage());
        }
    }

    public function edit(Player $player)
    {
        $teams = Team::all();
        $sports = Sport::all();
        $clubs = Club::all();
        $positions = Position::all();
        $user = $player->user;
        
        // Load all necessary relationships
        $player->load(['stats.stat', 'rewards', 'ads', 'sport', 'position', 'club', 'team']);
        
        $rewards = Reward::all();
        // Load stats based on player's sport, or all stats if no sport is selected
        $stats = $player->sports_id ? Stat::where('sports_id', $player->sports_id)->get() : Stat::all();
        $ads = Ad::where('active', true)->get();
        
        // Prepare social links for the view
        $socials = is_array($player->social_links) ? $player->social_links : json_decode($player->social_links, true) ?? [];

        // Debug: Log the player data being loaded
        \Log::info('Player Edit Data:', [
            'player_id' => $player->id,
            'position_id' => $player->position_id,
            'club_id' => $player->club_id,
            'team_id' => $player->team_id,
            'sport_id' => $player->sport_id,
            'stats_count' => $player->stats->count(),
            'rewards_count' => $player->rewards->count(),
        ]);

        return view('admin.players.edit', compact('player', 'teams', 'sports', 'clubs', 'positions', 'user','stats','rewards', 'ads', 'socials'));
    }

    public function update(Request $request, Player $player)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:players,email,' . $player->id . '|unique:users,email,' . $player->user_id,
            'position_id' => 'required|exists:positions,id',
            'team_id' => 'required|exists:teams,id',
            'sport_id' => 'required|exists:sports,id',
            'club_id' => 'nullable|exists:clubs,id',
            'social_links' => 'nullable|array',
            'password' => 'nullable|string|min:6',
        ]);

        $player->update([
            'name' => $request->name,
            'gender' => $request->gender,
            'email' => $request->email,
            'team_id' => $request->team_id,
            'sport_id' => $request->sport_id,
            'paypal_link' => $request->paypal_link,
            'birthday' => $request->birthday,
            'age' => $request->age,
            'club_id' => $request->club_id,
            'nationality' => $request->nationality,
            'height' => $request->height,
            'weight' => $request->weight,
            'debut' => $request->debut,
            'jersey_no' => $request->jersey_no,
            'bio' => $request->bio,
            'social_links' => $request->social_links,
            'position_id' => $request->position_id,
            'phone' => $request->phone,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'address' => $request->address,
            'college' => $request->college,
            'university' => $request->university,
            'referee_affiliation' => $request->referee_affiliation,
        ]);

        // Update associated user
        if ($player->user) {
            $player->user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->filled('password') ? Hash::make($request->password) : $player->user->password,
            ]);
        }

        // Update stats
        P::where('player_id', $player->id)->delete();

        // Handle stats with unique stat_ids only
        $processedStats = [];
        for ($i = 1; $i <= 3; $i++) {
            $statId = $request->input("stat_{$i}");
            $statValue = $request->input("stat_value_{$i}");
            
            if ($statId && $statValue && !in_array($statId, $processedStats)) {
                P::create([
                    'player_id' => $player->id,
                    'stat_id'   => $statId,
                    'value'     => $statValue,
                ]);
                $processedStats[] = $statId;
            }
        }

        PlayerReward::where('user_id', $player->user_id)->delete();
        if (isset($request->rewards)) {
            foreach ($request->rewards as $Id) {
                PlayerReward::create([
                    'user_id' => $player->user_id,
                    'reward_id'   => $Id,
                    'issued_by'     => Auth()->user()->id,
                ]);
            }
        }

        if ($request->has('ads')) {
            $player->ads()->sync($request->input('ads'));
        } else {
            $player->ads()->detach();
        }

        return redirect()->route('admin.players.index')->with('success', 'Player and user updated successfully!');
    }

    public function destroy(Player $player)
    {
        if ($player->user) {
            $player->user->delete();
        }

        $player->delete();

        return redirect()->route('admin.players.index')->with('success', 'Player and user deleted successfully!');
    }

    public function getStatsBySport($sport_id)
    {
        try {
            $stats = Stat::where('sport_id', $sport_id)->get();
            return response()->json(['stats' => $stats]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
