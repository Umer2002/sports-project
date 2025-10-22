<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatParticipant;
use App\Models\Message;
use App\Models\Team;
use App\Models\Player;
use App\Models\Coach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeamChatController extends Controller
{
    /**
     * Ensure the authenticated user is a member (player) or coach of the team.
     */
    protected function authorizeTeamMembership(Team $team): void
    {
        $userId = Auth::id();

        $user = Auth::user();
        if ($user && $user->hasRole('club')) {
            $clubId = optional($user->club)->id ?? $user->club_id;
            if ($clubId && (int) $team->club_id === (int) $clubId) {
                return;
            }
        }

        $isPlayer = Player::where('user_id', $userId)
            ->whereHas('teams', fn($q) => $q->where('teams.id', $team->id))
            ->exists();

        $isCoach = Coach::where('user_id', $userId)
            ->whereHas('teams', fn($q) => $q->where('teams.id', $team->id))
            ->exists();

        abort_unless($isPlayer || $isCoach, 403, 'You are not a member of this team');
    }

    public function show(Team $team)
    {
        $this->authorizeTeamMembership($team);
        $user = Auth::user();

        // Create or fetch chat for team
        $chat = Chat::firstOrCreate(
            ['team_id' => $team->id, 'type' => 'team'],
            ['title' => $team->name]
        );

        // Ensure current user is participant
        ChatParticipant::firstOrCreate([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
        ]);

        $messages = $chat->messages()->with('user')->orderBy('created_at')->get();

        return view('teams.chat', compact('team', 'chat', 'messages', 'user'));
    }
    // create live chat
    public function livechat(Team $team)
    {
        $this->authorizeTeamMembership($team);
        $user = Auth::user();

        // Create or fetch chat for team
        $chat = Chat::firstOrCreate(
            ['team_id' => $team->id, 'type' => 'team'],
            ['title' => $team->name]
        );

        // Ensure current user is participant
        ChatParticipant::firstOrCreate([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
        ]);

        $messages = $chat->messages()->with('user')->orderBy('created_at')->get();

        return view('teams.livechat', compact('team', 'chat', 'messages', 'user'));
    }


    public function send(Request $request, Team $team)
    {
        $this->authorizeTeamMembership($team);
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = Auth::user();

        $chat = Chat::firstOrCreate(
            ['team_id' => $team->id, 'type' => 'team'],
            ['title' => $team->name]
        );

        ChatParticipant::firstOrCreate([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
        ]);

        $chat->messages()->create([
            'sender_id' => $user->id,
            'receiver_id' => null,
            'content' => $request->message,
        ]);

        return redirect()->route('player.teams.chat', $team);
    }

    /**
     * JSON: fetch messages for a team chat (for React client)
     */
    public function messages(Team $team)
    {
        $this->authorizeTeamMembership($team);

        $chat = Chat::firstOrCreate(
            ['team_id' => $team->id, 'type' => 'team'],
            ['title' => $team->name]
        );

        // Ensure current user is participant
        ChatParticipant::firstOrCreate([
            'chat_id' => $chat->id,
            'user_id' => Auth::id(),
        ]);

        $messages = $chat->messages()->with('user')->orderBy('created_at')->get();
        return response()->json([
            'chat_id' => $chat->id,
            'messages' => $messages,
        ]);
    }

    /**
     * JSON: send a message to the team chat (for React client)
     */
    public function sendJson(Request $request, Team $team)
    {
        $this->authorizeTeamMembership($team);

        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();

        $chat = Chat::firstOrCreate(
            ['team_id' => $team->id, 'type' => 'team'],
            ['title' => $team->name]
        );

        ChatParticipant::firstOrCreate([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
        ]);

        $message = $chat->messages()->create([
            'sender_id' => $user->id,
            'receiver_id' => null,
            'content' => $data['message'],
        ]);

        $message->load('user');
        // Broadcast if broadcasting is configured; safe to attempt
        if (class_exists(\App\Events\MessageSent::class)) {
            broadcast(new \App\Events\MessageSent($message))->toOthers();
        }

        return response()->json(['message' => $message]);
    }

    /**
     * Upload an attachment to the team chat and create a message.
     */
    public function sendAttachment(Request $request, Team $team)
    {
        $this->authorizeTeamMembership($team);

        $data = $request->validate([
            'attachment' => 'required|file|max:51200',
            'message' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();

        $chat = Chat::firstOrCreate(
            ['team_id' => $team->id, 'type' => 'team'],
            ['title' => $team->name]
        );

        ChatParticipant::firstOrCreate([
            'chat_id' => $chat->id,
            'user_id' => $user->id,
        ]);

        $file = $request->file('attachment');
        $path = $file->store('chat_attachments', 'public');
        $mime = $file->getMimeType();

        $message = $chat->messages()->create([
            'sender_id' => $user->id,
            'receiver_id' => null,
            // Ensure non-null content value
            'content' => (string) ($data['message'] ?? ''),
            'attachment_path' => $path,
            'attachment_type' => $mime,
        ]);

        $message->load('user');
        if (class_exists(\App\Events\MessageSent::class)) {
            broadcast(new \App\Events\MessageSent($message))->toOthers();
        }

        return response()->json(['message' => $message]);
    }
}
