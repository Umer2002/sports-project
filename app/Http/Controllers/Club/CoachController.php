<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Country;

class CoachController extends Controller
{
    public function index()
    {
        // Get coaches that belong to the current club through users
        $club = auth()->user()->club;
        $coaches = Coach::whereHas('user', function($query) use ($club) {
            $query->where('club_id', $club->id);
        })->with('sport')->paginate(10);

        return view('club.coaches.index', compact('coaches'));
    }

    public function create()
    {
        $countries = Country::all();
        // dd($counteries);
        $sports = Sport::pluck('name', 'id');
        // dd($sports);
        return view('club.coaches.create', compact('sports', 'countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|email|unique:coaches,email',
            'phone' => 'required|string|max:191',
            'gender' => 'required|string|in:male,female,other',
            'city_id' => 'required|string|max:191',
            'bio' => 'required|string',
            'country_id' => 'required|string|max:191',
            'age' => 'nullable|integer|min:18|max:100',
            'sport_id' => 'required|exists:sports,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Create user account for the coach
        $user = User::create([
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(12)), // Random password
            'club_id' => auth()->user()->club->id, // Assign to current club
        ]);

        // Assign coach role
        $user->assignRole('coach');

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('coaches/photos', 'public');
        }

        // Create coach profile
        $data['user_id'] = $user->id;
        $coach = Coach::create($data);

        return redirect()->route('club.coaches.index')->with('success', 'Coach created successfully!');
    }

    public function show(Coach $coach)
    {
        // Ensure coach belongs to current club
        $this->authorizeClubCoach($coach);
        return view('club.coaches.show', compact('coach'));
    }

    public function edit(Coach $coach)
    {
        // Ensure coach belongs to current club
        $this->authorizeClubCoach($coach);
                $countries = Country::all();

        $sports = Sport::pluck('name', 'id');
        return view('club.coaches.create', compact('coach', 'sports', 'countries'));
    }

    public function update(Request $request, Coach $coach)
    {
        // Ensure coach belongs to current club
        $this->authorizeClubCoach($coach);

        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|email|unique:coaches,email,' . $coach->id,
            'phone' => 'required|string|max:191',
            'gender' => 'required|string|in:male,female,other',
            'city_id' => 'required|string|max:191',
            'bio' => 'required|string',
            'country_id' => 'required|string|max:191',
            'age' => 'nullable|integer|min:18|max:100',
            'sport_id' => 'required|exists:sports,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('coaches/photos', 'public');
        }

        $coach->update($data);

        // Update user name if changed
        if ($coach->user) {
            $coach->user->update([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
            ]);
        }

        return redirect()->route('club.coaches.index')->with('success', 'Coach updated successfully!');
    }

    public function destroy(Coach $coach)
    {
        // Ensure coach belongs to current club
        $this->authorizeClubCoach($coach);

        // Delete user account
        if ($coach->user) {
            $coach->user->delete();
        }

        // Delete coach profile
        $coach->delete();

        return redirect()->route('club.coaches.index')->with('success', 'Coach deleted successfully!');
    }

    private function authorizeClubCoach(Coach $coach)
    {
        $club = auth()->user()->club;
        if (!$coach->user || $coach->user->club_id !== $club->id) {
            abort(403, 'Unauthorized access to this coach.');
        }
    }
}
