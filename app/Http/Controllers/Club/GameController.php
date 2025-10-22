<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Invite;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class GameController extends Controller
{
    public function index()
    {
        $club = Auth::user()->club;

        if (!$club) {
            return redirect()->route('club.dashboard')->with('error', 'No club associated with your account.');
        }

        $games = Game::where('home_club_id', $club->id)->latest()->get();
        return view('club.games.index', compact('games'));
    }

    public function create()
    {
        $clubs = Club::pluck('name', 'id');
        return view('club.games.create', compact('clubs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'away_club_id' => 'required|exists:clubs,id',
            'match_date' => 'required|date',
            'match_time' => 'required',
            'venue' => 'required',
        ]);

        $club = Auth::user()->club;

        if (!$club) {
            return redirect()->route('club.dashboard')->with('error', 'No club associated with your account.');
        }

        Game::create([
            'home_club_id' => $club->id,
            'away_club_id' => $request->away_club_id,
            'match_date' => $request->match_date,
            'match_time' => $request->match_time,
            'venue' => $request->venue,
        ]);

        return redirect()->route('club.games.index')->with('success', 'Game created successfully.');
    }

    public function invite(Request $request, Game $game)
    {
        $request->validate(['email' => 'required|email']);

        $invite = Invite::create([
            'sender_id' => Auth::id(),
            'receiver_email' => $request->email,
            'receiver_id' => null,
            'type' => 'game',
            'reference_id' => $game->id,
        ]);

        if (!$invite->receiver_id) {
            Mail::to($request->email)->send(new \App\Mail\InviteToJoin($invite));
        }

        return back()->with('success', 'Invitation sent.');
    }
}
