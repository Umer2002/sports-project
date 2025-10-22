<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    public function create()
    {
        return view('players.challenge');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'challenge_type' => 'required|in:video,game',
            'video_url' => 'nullable|url',
            'game_info' => 'nullable|string',
        ]);

        // TODO: persist challenge data
        return back()->with('success', 'Challenge sent.');
    }
}
