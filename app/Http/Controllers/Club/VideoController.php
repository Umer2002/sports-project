<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $myVideos = Video::withCount(['likes', 'comments'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(12)
            ->get();

        return view('club.videos.index', [
            'myVideos' => $myVideos,
            'allowLive' => true,
        ]);
    }
}

