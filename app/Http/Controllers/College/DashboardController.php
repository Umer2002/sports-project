<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\{User, Sport, Club, Coach, Role};
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sports = Sport::all();
        // Managed (hidden) clubs for this college
        $managedClubs = Club::where('user_id', $user->id)->where('is_registered', 0)->get();
        $managedClubIds = $managedClubs->pluck('id');
        // Only coaches assigned to this college's managed clubs
        $coaches = \App\Models\Coach::with(['sport','user'])
            ->whereHas('user', function($q) use ($managedClubIds) {
                $q->whereIn('club_id', $managedClubIds);
            })
            ->latest()->limit(10)->get();
        return view('college.dashboard', compact('user','sports','coaches','managedClubs'));
    }

    public function createManagedClub(Request $request)
    {
        $data = $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'name' => 'nullable|string|max:191',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:191',
            'address' => 'nullable|string',
            'paypal_link' => 'nullable|url',
            'joining_url' => 'nullable|url',
            'bio' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ]);
        $user = Auth::user();
        $sport = Sport::findOrFail($data['sport_id']);
        $defaultName = trim(($user->name ?: 'College') . ' - ' . $sport->name);
        $name = $data['name'] ?: $defaultName;

        $payload = [
            'user_id' => $user->id,
            'sport_id' => $sport->id,
            'name' => $name,
            'email' => $data['email'] ?? $user->email,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'paypal_link' => $data['paypal_link'] ?? null,
            'joining_url' => $data['joining_url'] ?? null,
            'bio' => $data['bio'] ?? null,
            'social_links' => '',
            'is_registered' => 0,
        ];
        if ($request->hasFile('logo')) {
            $payload['logo'] = $request->file('logo')->store('club_logos', 'public');
        }

        $club = Club::create($payload);

        return back()->with('success', "Managed club created: {$club->name} (ID {$club->id})");
    }

    public function storeCoach(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|email|unique:coaches,email|unique:users,email',
            'phone' => 'required|string|max:191',
            'gender' => 'required|string|max:191',
            'city' => 'required|string|max:191',
            'country_id' => 'required|string|max:191',
            'age' => 'required|integer|min:18',
            'sport_id' => 'required|exists:sports,id',
            'bio' => 'nullable|string',
            'social_links' => 'nullable|array',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'club_id' => 'required|exists:clubs,id',
            'password' => 'nullable|string|min:6',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('coach_photos', 'public');
        }

        // map to model's expected key 'socail_links' (note the existing typo)
        $data['socail_links'] = $request->input('social_links', []);

        // Create user account for coach first
        $user = User::create([
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'] ?? \Illuminate\Support\Str::random(10)),
            'club_id' => $data['club_id'],
            'is_admin' => 0,
        ]);

        if ($role = Role::where('name', 'coach')->first()) {
            $user->roles()->attach($role->id);
        }

        // Create coach with user_id
        $coachPayload = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'gender' => $data['gender'],
            'city' => $data['city'],
            'country_id' => $data['country_id'],
            'age' => $data['age'],
            'sport_id' => $data['sport_id'],
            'bio' => $data['bio'] ?? null,
            'socail_links' => $data['socail_links'],
            'photo' => $data['photo'] ?? null,
            'user_id' => $user->id,
        ];
        Coach::create($coachPayload);

        return back()->with('success', 'Coach created and assigned to club.');
    }
}
