<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403, 'Access denied. Coach role required.');
        }

        $coach = auth()->user()->coach;
        
        if (!$coach) {
            return redirect()->route('coach.setup')->with('error', 'Please complete your coach profile.');
        }

        $sports = Sport::pluck('name', 'id');
        
        return view('coach.profile.edit', compact('coach', 'sports'));
    }

    public function update(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403, 'Access denied. Coach role required.');
        }

        $coach = auth()->user()->coach;
        
        if (!$coach) {
            return redirect()->route('coach.setup')->with('error', 'Please complete your coach profile.');
        }

        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'sport_id' => 'required|exists:sports,id',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($coach->photo) {
                Storage::disk('public')->delete($coach->photo);
            }
            
            $data['photo'] = $request->file('photo')->store('coaches/photos', 'public');
        }

        $coach->update($data);

        return redirect()->route('coach.profile.edit')->with('success', 'Profile updated successfully!');
    }
}

