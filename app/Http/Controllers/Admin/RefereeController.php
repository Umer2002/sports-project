<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referee;
use App\Models\RefereeAvailability;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RefereeController extends Controller
{
    public function index()
    {
        $referees = Referee::latest()->paginate(10);
        return view('admin.referees.index', compact('referees'));
    }

    public function create()
    {
        return view('admin.referees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'preferred_contact_method' => 'nullable|string|max:255',
            'government_id' => 'nullable|string|max:255',
            'languages_spoken' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'license_type' => 'nullable|string|max:255',
            'certifying_body' => 'nullable|string|max:255',
            'license_expiry_date' => 'nullable|date',
            'background_check_passed' => 'nullable|boolean',
            'liability_insurance' => 'nullable|boolean',
            'account_status' => 'nullable|in:active,inactive,suspended',
            'internal_notes' => 'nullable|string',
            'sports_officiated' => 'nullable|array',
            'club_id' => 'nullable|exists:clubs,id',
            'profile_picture' => 'nullable|image|max:2048',
            'liability_document' => 'nullable|file|max:2048',
        ]);

        // Create associated User first
        $user = User::create([
            'name' => $data['full_name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(10)),
            'is_admin' => 0,
        ]);

        // Handle uploads
        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('referees', 'public');
        }

        if ($request->hasFile('liability_document')) {
            $data['liability_document'] = $request->file('liability_document')->store('referees/docs', 'public');
        }

        $data['user_id'] = $user->id;
        $data['sports_officiated'] = $request->filled('sports_officiated') ? json_encode($request->sports_officiated) : null;
        $data['background_check_passed'] = $request->boolean('background_check_passed');
        $data['liability_insurance'] = $request->boolean('liability_insurance');
        $data['account_status'] = $request->input('account_status', 'active');

        Referee::create($data);

        return redirect()->route('admin.referees.index')->with('success', 'Referee and user created successfully.');
    }

    public function edit(Referee $referee)
    {
        return view('admin.referees.edit', compact('referee'));
    }

    public function update(Request $request, Referee $referee)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $referee->user_id,
            'phone' => 'nullable|string|max:20',
            'preferred_contact_method' => 'nullable|string|max:255',
            'government_id' => 'nullable|string|max:255',
            'languages_spoken' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'license_type' => 'nullable|string|max:255',
            'certifying_body' => 'nullable|string|max:255',
            'license_expiry_date' => 'nullable|date',
            'background_check_passed' => 'nullable|boolean',
            'liability_insurance' => 'nullable|boolean',
            'account_status' => 'nullable|in:active,inactive,suspended',
            'internal_notes' => 'nullable|string',
            'sports_officiated' => 'nullable|array',
            'club_id' => 'nullable|exists:clubs,id',
            'profile_picture' => 'nullable|image|max:2048',
            'liability_document' => 'nullable|file|max:2048',
        ]);

        // Handle uploads
        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('referees', 'public');
        }

        if ($request->hasFile('liability_document')) {
            $data['liability_document'] = $request->file('liability_document')->store('referees/docs', 'public');
        }

        $data['sports_officiated'] = $request->filled('sports_officiated') ? json_encode($request->sports_officiated) : null;
        $data['background_check_passed'] = $request->boolean('background_check_passed');
        $data['liability_insurance'] = $request->boolean('liability_insurance');

        $referee->update($data);

        // Update associated User
        if ($referee->user) {
            $referee->user->update([
                'name' => $data['full_name'],
                'email' => $data['email'],
            ]);
        }

        return redirect()->route('admin.referees.index')->with('success', 'Referee and user updated successfully.');
    }

    public function destroy(Referee $referee)
    {
        if ($referee->user) {
            $referee->user->delete();
        }

        $referee->delete();

        return redirect()->route('admin.referees.index')->with('success', 'Referee and user deleted.');
    }

    // Availability
    public function availabilityForm(Referee $referee)
    {
        return view('admin.referees.availability', compact('referee'));
    }

    public function storeAvailability(Request $request, Referee $referee)
    {
        $request->validate([
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time'
        ]);

        $referee->availability()->create($request->only(['day', 'start_time', 'end_time']));

        return back()->with('success', 'Availability added.');
    }

    public function deleteAvailability(RefereeAvailability $availability)
    {
        $availability->delete();

        return back()->with('success', 'Availability removed.');
    }
}
