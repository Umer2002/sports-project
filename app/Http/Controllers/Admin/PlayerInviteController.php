<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Str;

class PlayerInviteController extends Controller
{
    public function index(Request $request): View
    {
        $invites = Invite::query()
            ->where('type', 'player_free')
            ->latest()
            ->paginate(25);

        return view('admin.player-invites.free', [
            'invites' => $invites,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['nullable', 'email'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $metadata = [
            'lifetime_free' => true,
            'created_by_admin_id' => Auth::id(),
            'created_via' => 'admin_portal',
        ];

        if (! empty($data['notes'])) {
            $metadata['notes'] = $data['notes'];
        }

        $invite = Invite::create([
            'sender_id' => Auth::id(),
            'receiver_email' => $data['email'] ?? '',
            'receiver_id' => null,
            'token' => Str::uuid(),
            'type' => 'player_free',
            'reference_id' => Auth::id(),
            'metadata' => $metadata,
            'is_accepted' => false,
        ]);

        return redirect()
            ->route('admin.player-invites.free.index')
            ->with('success', 'Lifetime free invite generated.')
            ->with('generated_invite_token', $invite->token);
    }
}
