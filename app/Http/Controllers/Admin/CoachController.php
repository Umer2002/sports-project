<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CoachController extends Controller
{
    public function index(Request $request)
    {
        $query = Coach::with(['sport', 'teams.club']);

        $selectedClubId = $request->input('club_id');
        $selectedSportId = $request->input('sport_id');
        $club = null;

        if ($selectedClubId) {
            $club = Club::with('sport')->findOrFail($selectedClubId);

            $query->whereHas('teams', function ($teamQuery) use ($selectedClubId) {
                $teamQuery->where('club_id', $selectedClubId);
            });

            if ($club->sport_id) {
                $query->where('sport_id', $club->sport_id);
                $selectedSportId = $club->sport_id;
            }
        } elseif ($selectedSportId) {
            $query->where('sport_id', $selectedSportId);
        }

        $coaches = $query->paginate(10)->appends($request->only(['club_id', 'sport_id']));

        $sports = Sport::orderBy('name')->pluck('name', 'id');
        $clubs = Club::orderBy('name')->pluck('name', 'id');

        return view('admin.coaches.index', compact(
            'coaches',
            'sports',
            'clubs',
            'selectedClubId',
            'selectedSportId',
            'club'
        ));
    }

    public function create()
    {
        $sports = Sport::pluck('name', 'id');
        return view('admin.coaches.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|email|unique:coaches,email|unique:users,email',
            'phone' => 'required|string|max:191',
            'gender' => 'required|string|max:191',
            'social_links' => 'nullable|array',
            'city' => 'required|string|max:191',
            'bio' => 'nullable|string',
            'country_id' => 'required|string|max:191',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'age' => 'required|integer|min:18',
            'sport_id' => 'required|exists:sports,id',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('coach_photos', 'public');
        }

        $data['socail_links'] = $request->input('socail_links', []);

        // Create user account for coach first
        $user = User::create([
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(10)), // Temporary password
            'is_admin' => 0,
        ]);

        // Create coach with user_id
        $data['user_id'] = $user->id;
        Coach::create($data);

        return redirect()->route('admin.coaches.index')->with('success', 'Coach and user created successfully!');
    }

    public function update(Request $request, Coach $coach)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|email|unique:coaches,email,' . $coach->id . '|unique:users,email,' . $coach->user_id,
            'phone' => 'required|string|max:191',
            'gender' => 'required|string|max:191',
            'socail_links' => 'array',
            'city' => 'required|string|max:191',
            'bio' => 'nullable|string',
            'country_id' => 'required|string|max:191',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'age' => 'required|integer|min:18',
            'sport_id' => 'required|exists:sports,id',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('coach_photos', 'public');
        }

        $data['socail_links'] = $request->input('socail_links', []);

        $coach->update($data);

        // Update associated user
        if ($coach->user) {
            $coach->user->update([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
            ]);
        }

        return redirect()->route('admin.coaches.index')->with('success', 'Coach and user updated successfully!');
    }

    public function edit(Coach $coach)
    {
        $sports = Sport::pluck('name', 'id');
        return view('admin.coaches.edit', compact('coach', 'sports'));
    }

    public function destroy(Coach $coach)
    {
        if ($coach->user) {
            $coach->user->delete(); // Delete associated user
        }

        $coach->delete(); // Delete coach
        return redirect()->route('admin.coaches.index')->with('success', 'Coach and user deleted successfully.');
    }
}
