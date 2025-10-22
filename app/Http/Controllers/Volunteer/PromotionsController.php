<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PromotionsController extends Controller
{
    public function index()
    {
        $this->authorizeVolunteer();
        $sportId = optional(Auth::user())->sport_id;
        $promotions = Promotion::where('user_id', Auth::id())
            ->when($sportId, fn($q) => $q->where('sport_id', $sportId))
            ->latest()
            ->paginate(12);
        return view('volunteer.promotions.index', compact('promotions'));
    }

    public function create()
    {
        $this->authorizeVolunteer();
        return view('volunteer.promotions.create');
    }

    public function store(Request $request)
    {
        $this->authorizeVolunteer();
        $data = $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            // max in kilobytes (256 MB)
            'video' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/mpeg|max:262144',
            'youtube_url' => 'nullable|url',
            'share_text' => 'nullable|string|max:280',
        ]);

        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('promotions', 'public');
        }

        $promotion = Promotion::create([
            'user_id' => Auth::id(),
            'sport_id' => optional(Auth::user())->sport_id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'video_path' => $videoPath,
            'youtube_url' => $data['youtube_url'] ?? null,
            'share_text' => $data['share_text'] ?? null,
        ]);

        return redirect()->route('volunteer.promotions.index')->with('success', 'Promotion created');
    }

    private function authorizeVolunteer(): void
    {
        abort_unless(Auth::check() && Auth::user()->hasRole('volunteer'), 403);
    }
}
