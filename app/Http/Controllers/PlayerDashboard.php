<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Models\Order;
use App\Models\Event;
use App\Models\Player;
use App\Models\Chat;
use App\Models\Users;
use App\Models\Blog;
use App\Models\PickupGame;
use App\Models\Ad;
use App\Models\Reward;
use App\Services\EventService;

class PlayerDashboard extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            throw new AccessDeniedHttpException();
        }

        $orders = [
            'processing' => Order::where('status', 'processing')->count(),
            'on_hold' => Order::where('status', 'on_hold')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
        ];

        $data = Session::all();
        $user = $data['user'];
        $userId = $user['id'];
        $playerId = $user['player_id'];

        $players = Player::with('stats', 'ads')->find($playerId);

        $chats = Chat::with(['participants', 'messages.user'])
            ->whereIn('id', function ($query) use ($userId) {
                $query->select('chat_id')
                    ->from('chat_participants')
                    ->where('user_id', $userId);
            })
            ->get();

        $club_users = Users::where('club_id', $user['club_id'])
            ->where('id', '!=', $userId)
            ->get()
            ->filter(function ($clubUser) use ($userId) {
                $chatExists = Chat::where('type', 'private')
                    ->whereHas('participants', function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })
                    ->whereHas('participants', function ($q) use ($clubUser) {
                        $q->where('user_id', $clubUser->id);
                    })
                    ->exists();

                return !$chatExists;
            });

        $blogs = Blog::where('user_id', $userId)->take(3)->get();
        $allRewards = Reward::all();
         

        // Invite to pickup games logic
        $pickupGames = PickupGame::with('sport')
            ->where(function ($q) use ($userId) {
                $q->whereHas('participants', function ($sub) use ($userId) {
                    $sub->where('user_id', $userId);
                })
                ->orWhereDoesntHave('participants');
            })
            ->orderBy('game_datetime', 'asc')
            ->get();

        // Get relevant events for the player using EventService
        $events = EventService::getPlayerEvents($user['player_id'], $user['club_id']);

        // Fetch only ads associated with this player and active
        $adsData = $players ? $players->ads()->where('active', true)->get() : collect();
        return view('players.dashboard', compact(
            'chats', 'orders', 'events', 'blogs', 'user', 'club_users', 'players', 'pickupGames', 'adsData', 'allRewards'
        ));
    }
}
