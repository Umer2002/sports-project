<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\User;
use App\Models\Sport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClubController extends Controller
{
    public function index()
    {
        $clubs = Club::latest()->paginate(10);
        $sports = Sport::all();
        return view('admin.clubs.index', compact('clubs', 'sports'));
    }

    public function create()
    {
        $sports = Sport::all();
        return view('admin.clubs.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'social_links' => 'array',
            'email' => 'required|email|unique:users,email',
            'paypal_link' => 'nullable|url',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:191',
            'joining_url' => 'nullable|url',
            'bio' => 'nullable|string',
            'sport_id' => 'required|exists:sports,id',
            'is_registered' => 'boolean',
        ]);

        // Create User First
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make('password'), // Default or generated password
            'is_admin' => 0,
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('club_logos', 'public');
        }

        $data['user_id'] = $user->id;
        $data['social_links'] = $request->input('social_links', []);
        $data['registration_date'] = now(); // Set registration date
        // dd($data);
        Club::create($data);

        return redirect()->route('admin.clubs.index')->with('success', 'Club and user created successfully!');
    }

    public function edit(Club $club)
    {
        $sports = Sport::all();
        return view('admin.clubs.edit', compact('club', 'sports'));
    }

    public function update(Request $request, Club $club)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'email' => 'required|email|unique:users,email,' . $club->user_id,
            'social_links' => 'array',
            'paypal_link' => 'nullable|url',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:191',
            'joining_url' => 'nullable|url',
            'bio' => 'nullable|string',
            'sport_id' => 'required|exists:sports,id',
            'is_registered' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('club_logos', 'public');
        }

        $data['social_links'] = $request->input('social_links', []);

        // Update club info
        $club->update($data);

        // Update associated user
        $club->user()->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        return redirect()->route('admin.clubs.index')->with('success', 'Club and user updated!');
    }

    public function destroy(Club $club)
    {
        // Delete related user
        $club->user()->delete();

        // Delete the club
        $club->delete();

        return redirect()->route('admin.clubs.index')->with('success', 'Club and user deleted.');
    }

    public function show(Club $club)
    {
        $club->load(['players', 'sport']);
        
        // Process payout workflow automatically
        $club->processInitialPlayerCount();
        $club->processFinalPayout();
        
        $players = $club->players;
        $payoutPlans = \App\Models\PayoutPlan::orderBy('player_count')->get();
        $playerCount = $players->count();
        
        // Get current payout information
        $currentPayout = $club->calculateEstimatedPayout();
        $finalPayout = $club->final_payout;
        $estimatedPayout = $club->estimated_payout;
        
        // Get time remaining information
        $onboardingTimeRemaining = $club->getOnboardingTimeRemaining();
        $payoutTimeRemaining = $club->getPayoutTimeRemaining();
        
        // Get registration information
        $registrationDate = $club->getRegistrationDate();
        $daysSinceRegistration = $registrationDate ? $registrationDate->diffInDays(now()) : 0;
        
        // Get payout status
        $payoutStatus = $club->getPayoutStatusDescription();
        
        // Get payments history
        $payments = $club->payments()->orderBy('created_at', 'desc')->get();
        
        return view('admin.clubs.show', compact(
            'club', 
            'players', 
            'payoutPlans', 
            'payments', 
            'currentPayout',
            'finalPayout',
            'estimatedPayout',
            'onboardingTimeRemaining',
            'payoutTimeRemaining',
            'registrationDate',
            'daysSinceRegistration',
            'payoutStatus'
        ));
    }

    /**
     * Process payout for a club
     */
    public function processPayout(Club $club)
    {
        if ($club->payout_status !== 'calculated') {
            return redirect()->back()->with('error', 'Club is not eligible for payout processing.');
        }

        // Mark as paid
        $club->markPayoutAsPaid();

        // Create payment record
        \App\Models\Payment::create([
            'club_id' => $club->id,
            'amount' => $club->final_payout,
            'type' => 'club_payout',
            'status' => 'completed',
            'processed_by' => auth()->id(),
            'notes' => 'Final payout processed',
        ]);

        return redirect()->back()->with('success', 'Payout processed successfully!');
    }
}
