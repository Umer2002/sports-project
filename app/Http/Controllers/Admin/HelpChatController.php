<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpChatSession;
use Illuminate\Contracts\View\View;

class HelpChatController extends Controller
{
    public function index(): View
    {
        $sessions = HelpChatSession::with([
            'user:id,name,email',
            'player:id,name',
            'tickets' => fn ($query) => $query->latest(),
        ])
            ->withCount('messages')
            ->latest('last_interaction_at')
            ->latest('created_at')
            ->paginate(20);

        return view('admin.help-chats.index', compact('sessions'));
    }
}
