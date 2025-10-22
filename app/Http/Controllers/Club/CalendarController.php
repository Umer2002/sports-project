<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Game;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        $events = Event::where('user_id', Auth::id())->orderBy('event_date')->get();

        $club = Auth::user()->club;

        $games = collect();
        if ($club) {
            $games = Game::where('home_club_id', $club->id)->orderBy('match_date')->get();
        }

        return view('club.calendar.index', compact('events', 'games'));
    }
}
