<?php

namespace App\Http\Controllers\Club\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function payments()
    {
        $club = Auth::user()->club;
        abort_unless($club, 403);
        return view('club.store.settings.payments', compact('club'));
    }

    public function updatePayments(Request $request)
    {
        $club = Auth::user()->club;
        abort_unless($club, 403);

        $data = $request->validate([
            'stripe_public_key' => 'nullable|string|max:255',
            'stripe_secret_key' => 'nullable|string|max:255',
            'stripe_account_id' => 'nullable|string|max:255',
        ]);

        $club->update($data);

        return back()->with('success', 'Stripe settings updated');
    }
}

