<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class InviteController extends Controller
{
    public function create()
    {
        return view('club.invite');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'  => 'required|in:club,player',
            'form_data' => 'nullable|string',
        ]);

        // Parse form data if provided
        $formData = [];
        if ($request->form_data) {
            $formData = json_decode($request->form_data, true) ?? [];
        }

        $invite = Invite::create([
            'sender_id' => Auth::id(),
            'receiver_email' => '',
            'receiver_id' => null,
            'type' => $data['type'],
            'reference_id' => Auth::id(),
            'token' => Str::uuid(),
            'metadata' => json_encode($formData), // Store additional form data
        ]);

        $link = URL::to('/invite/' . $invite->token);

        return back()->with('success', 'Invite link generated.')->with('link', $link);
    }
}
