<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatParticipant;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Session;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // dd($user);
        $chats = Chat::whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->with('participants')
            ->get();
        $videos = \App\Models\Video::latest()->take(15)->get();
        $events = \App\Models\Event::all();
        return view('dashboard', compact('chats', 'user', 'videos', 'events'));
    }

    public function getMessages($chatId)
    {
        $messages = Message::with('user')
            ->where('chat_id', $chatId)
            ->orderBy('created_at', 'asc')
            ->get();
        // dd($messages);
        return response()->json($messages);
    }
    public function chat(Request $request)
    {
        $user = Auth::user(); // Get logged-in user

        // Fetch all chats where the user is a participant
        $chats = Chat::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['participants', 'messages' => function ($q) {
            $q->latest()->limit(1); // Load only latest message
        }])->get();

        // Determine if a chat_id is passed (for direct open)
        $activeChatId = $request->query('chat_id');
        $messages = collect(); // empty by default

        if ($activeChatId) {
            $messages = Message::with('user')
                ->where('chat_id', $activeChatId)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        $videos = \App\Models\Video::latest()->take(15)->get();
        $events = \App\Models\Event::latest()->take(10)->get();

        return view('players.chat.chat', compact('user', 'chats', 'activeChatId', 'messages', 'videos', 'events'));
    }

    public function send(Request $request)
    {
        $request->validate(['chat_id' => 'required|exists:chats,id', 'message' => 'required|string|max:2000']);

        $userId = Auth::id();
        $chat = Chat::with('tournament')->findOrFail($request->chat_id);

        if (!$chat->participants()->where('user_id', $userId)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($chat->type === 'tournament' && $chat->tournament && $chat->tournament->end_date && $chat->tournament->end_date->isPast()) {
            return response()->json(['error' => 'Tournament chat has closed.'], 403);
        }

        $message = Message::create([
            'chat_id' => $request->chat_id,
            'sender_id' => $userId,
            'content' => $request->message,
        ]);

        $message->load('user');
        broadcast(new \App\Events\MessageSent($message))->toOthers();
        Log::info('Broadcast dispatched for message ID ' . $message->id);

        return response()->json(['message' => $message]);
    }

    public function sendAttachment(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'attachment' => 'required|file|max:51200', // up to ~50MB
            'message' => 'nullable|string|max:2000',
        ]);

        $userId = Auth::id();
        $chat = Chat::with('tournament')->findOrFail($request->chat_id);

        if (!$chat->participants()->where('user_id', $userId)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($chat->type === 'tournament' && $chat->tournament && $chat->tournament->end_date && $chat->tournament->end_date->isPast()) {
            return response()->json(['error' => 'Tournament chat has closed.'], 403);
        }

        $file = $request->file('attachment');
        $path = $file->store('chat_attachments', 'public');
        $mime = $file->getMimeType();

        $message = Message::create([
            'chat_id' => $request->chat_id,
            'sender_id' => $userId,
            // DB requires non-null content; default to empty string
            'content' => (string) ($request->input('message') ?? ''),
            'attachment_path' => $path,
            'attachment_type' => $mime,
        ]);

        $message->load('user');
        if (class_exists(\App\Events\MessageSent::class)) {
            broadcast(new \App\Events\MessageSent($message))->toOthers();
        }

        return response()->json(['message' => $message]);
    }

    public function messages(Chat $chat)
    {
        return $chat->messages()->with('user')->get();
    }

    public function initiate($userId)
    {
        $currentId = Auth::id();

        if (!$userId || (int) $userId === (int) $currentId) {
            return response()->json(['error' => 'Invalid user selected for chat'], 422);
        }

        $recipient = User::find($userId);
        if (!$recipient) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $chat = Chat::where('type', 'individual')
            ->whereHas('participants', fn($q) => $q->where('user_id', $currentId))
            ->whereHas('participants', fn($q) => $q->where('user_id', $recipient->id))
            ->first();

        if (!$chat) {
            $chat = DB::transaction(function () use ($currentId, $recipient) {
                $chat = Chat::create(['type' => 'individual']);
                ChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $currentId]);
                ChatParticipant::create(['chat_id' => $chat->id, 'user_id' => $recipient->id]);
                return $chat;
            });
        }

        return response()->json(['chat_id' => $chat->id]);
    }

    public function sendMessage(Request $request)
    {
        // Legacy endpoint (kept for compatibility). Prefer send().
        $request->validate(['chat_id' => 'required', 'message' => 'required']);
        // get user from session
        $user = Session::get('user');

        // get user id
        $userId = $user['id'];
        $message = Message::create([
            'chat_id' => $request->chat_id,
            'sender_id' => $userId,
            'content' => $request->message,
        ]);

        $message->load('user');
        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json(['message' => $message]);
        // return response()->json($message);
    }

}
