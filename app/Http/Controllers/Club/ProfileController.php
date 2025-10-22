<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    /**
     * Show the club profile edit form
     */
    public function edit()
    {
        $user = Auth::user();
        $club = $user->club ?: Club::where('user_id', $user->id)->first();
        
        if (!$club) {
            return redirect()->route('club.setup')->with('error', 'Club not found. Please complete setup first.');
        }

        $sports = \App\Models\Sport::pluck('name', 'id');
        
        return view('club.profile.edit', compact('club', 'sports'));
    }

    /**
     * Update the club profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $club = $user->club ?: Club::where('user_id', $user->id)->first();
        
        if (!$club) {
            return redirect()->route('club.setup')->with('error', 'Club not found.');
        }

        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'bio' => 'nullable|string|max:1000',
            'sport_id' => 'required|exists:sports,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'paypal_link' => 'nullable|url|max:500',
            'social_links' => 'nullable|array',
            'social_links.facebook' => 'nullable|url|max:500',
            'social_links.instagram' => 'nullable|url|max:500',
            'social_links.twitter' => 'nullable|url|max:500',
            'social_links.youtube' => 'nullable|url|max:500',
            'social_links.linkedin' => 'nullable|url|max:500',
            'social_links.tiktok' => 'nullable|url|max:500',
            'social_links.pinterest' => 'nullable|url|max:500',
            'social_links.snapchat' => 'nullable|url|max:500',
            'social_links.reddit' => 'nullable|url|max:500',
        ]);

        $data = $request->only([
            'name', 'email', 'phone', 'address', 'bio', 'sport_id', 'paypal_link'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($club->logo && Storage::exists($club->logo)) {
                Storage::delete($club->logo);
            }
            
            $data['logo'] = $request->file('logo')->store('club-logos', 'public');
        }

        // Handle social links
        $socialLinks = [];
        if ($request->has('social_links')) {
            foreach ($request->input('social_links') as $platform => $url) {
                if (!empty($url)) {
                    $socialLinks[$platform] = $url;
                }
            }
        }
        $data['social_links'] = $socialLinks;

        $club->update($data);

        return redirect()->route('club.profile.edit')->with('success', 'Club profile updated successfully!');
    }

    /**
     * Show the club profile view
     */
    public function show()
    {
        $user = Auth::user();
        $club = $user->club ?: Club::where('user_id', $user->id)->first();
        
        if (!$club) {
            return redirect()->route('club.setup')->with('error', 'Club not found.');
        }

        return view('club.profile.show', compact('club'));
    }
}
