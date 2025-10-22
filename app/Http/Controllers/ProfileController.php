<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Player;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update player profile information.
     */
    public function updatePlayerProfile(Request $request)
    {
        $user = Auth::user();
        $player = Player::where('user_id', $user->id)->first();
        
        if (!$player) {
            return response()->json(['success' => false, 'message' => 'Player profile not found'], 404);
        }

        $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'height' => 'nullable|string|max:20',
            'weight' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:100',
            'jersey_no' => 'nullable|integer|min:1|max:999',
            'bio' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Update user information
            $user->update([
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'email' => $request->email,
            ]);

            // Handle profile picture upload
            $photoPath = $player->photo;
            if ($request->hasFile('profile_picture')) {
                // Delete old photo if exists
                if ($player->photo && Storage::disk('public')->exists('players/' . $player->photo)) {
                    Storage::disk('public')->delete('players/' . $player->photo);
                }
                
                // Store new photo
                $file = $request->file('profile_picture');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('players', $filename, 'public');
                $photoPath = $filename;

                // Optional background removal if configured
                $bgApi = env('BG_REMOVE_URL');
                $bgToken = env('BG_REMOVE_TOKEN');
                $bgProvider = env('BG_REMOVE_PROVIDER'); // 'removebg' or null for generic
                if ($bgApi) {
                    try {
                        $binary = Storage::disk('public')->get('players/' . $filename);
                        if ($bgProvider === 'removebg') {
                            // remove.bg API expects 'X-Api-Key' and 'image_file'
                            $response = Http::withHeaders([
                                    'X-Api-Key' => $bgToken,
                                ])
                                ->attach('image_file', $binary, $filename)
                                ->post($bgApi, [ 'size' => 'auto', 'crop' => 'true', 'format' => 'png' ]);
                        } else {
                            // Generic endpoint: multipart field named 'image', optional Bearer token
                            $response = Http::withHeaders(array_filter([
                                        'Authorization' => $bgToken ? ('Bearer ' . $bgToken) : null,
                                    ]))
                                    ->attach('image', $binary, $filename)
                                    ->post($bgApi, [ 'format' => 'png' ]);
                        }

                        if ($response->successful()) {
                            $contentType = $response->header('Content-Type');
                            $body = $response->body();
                            if ($contentType && str_contains($contentType, 'image')) {
                                $outName = pathinfo($filename, PATHINFO_FILENAME) . '_nobg.png';
                                Storage::disk('public')->put('players/' . $outName, $body);
                                // Replace saved photo with background-removed version
                                $photoPath = $outName;
                                // Optionally remove the original
                                try { Storage::disk('public')->delete('players/' . $filename); } catch (\Throwable $e) {}
                            }
                        }
                    } catch (\Throwable $e) {
                        // Background removal failed; keep original photo
                        \Log::warning('Profile background removal failed', ['error' => $e->getMessage()]);
                    }
                }
            }

            // Update player information
            $player->update([
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'email' => $request->email,
                'phone' => $request->phone,
                'birthday' => $request->birthday,
                'height' => $request->height,
                'weight' => $request->weight,
                'nationality' => $request->nationality,
                'jersey_no' => $request->jersey_no,
                'bio' => $request->bio,
                'photo' => $photoPath,
            ]);

            // Calculate age if birthday is provided
            if ($request->birthday) {
                $age = \Carbon\Carbon::parse($request->birthday)->age;
                $player->update(['age' => $age]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'player' => [
                    'name' => $player->name,
                    'position' => $player->position->position_name ?? '',
                    'nationality' => $player->nationality,
                    'height' => $player->height,
                    'weight' => $player->weight,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the player profile page.
     */
    public function profile(Request $request): View
    {
        $user = $request->user();
        $player = Player::with(['sport', 'club', 'position'])->where('user_id', $user->id)->first();
        
        return view('players.profile', [
            'user' => $user,
            'player' => $player,
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
