<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Sport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ClubBrowseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $sportId = $request->query('sport');

        $clubs = Club::query()
            ->select(['id', 'name', 'slug', 'logo', 'sport_id', 'bio', 'address'])
            ->with([
                'sport:id,name',
                'players' => function ($query) {
                    $query->select('players.id', 'players.club_id', 'players.name', 'players.photo')
                        ->orderBy('players.name');
                },
            ])
            ->withCount(['players', 'teams'])
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->when($search !== '', function ($query) use ($search) {
                $like = "%{$search}%";
                $query->where(function ($inner) use ($like) {
                    $inner->where('name', 'like', $like)
                        ->orWhere('bio', 'like', $like)
                        ->orWhere('address', 'like', $like);
                });
            })
            ->when($sportId, function ($query) use ($sportId) {
                $query->where('sport_id', $sportId);
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $sports = Sport::orderBy('name')->get(['id', 'name']);

        return view('clubs.search', [
            'clubs' => $clubs,
            'sports' => $sports,
            'filters' => [
                'q' => $search,
                'sport' => $sportId,
            ],
        ]);
    }
}
