<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ensure ambassador token exists
        if (!$user->ambassador_token) {
            $user->update(['ambassador_token' => bin2hex(random_bytes(8))]);
            $user->refresh();
        }
        $playerCount = $user->playersReferredCount();
        $commission = $user->ambassadorCommissionAmount();
        $playerInvite = route('register', ['ref' => $user->ambassador_token, 'ref_type' => 'player']);
        $clubInvite = route('register', ['ref' => $user->ambassador_token, 'ref_type' => 'club']);
        return view('volunteer.dashboard', compact('user','playerCount','commission','playerInvite','clubInvite'));
    }
}
