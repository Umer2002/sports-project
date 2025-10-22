<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Get tasks assigned to players in coach's teams
        $teamIds = $coach->teams()->pluck('teams.id');
        $playerUserIds = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->pluck('user_id');

        $tasks = Task::whereIn('assigned_to', $playerUserIds)
            ->orWhere('assigned_to', auth()->id())
            ->with('user')
            ->latest()
            ->paginate(20);

        // Get players and teams for modal
        $players = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->with(['user', 'teams'])->get();

        $teams = $coach->teams()->with(['sport', 'club'])->get();

        return view('coach.tasks.index', compact('tasks', 'players', 'teams'));
    }

    public function create()
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Get players from coach's teams
        $teamIds = $coach->teams()->pluck('teams.id');
        $players = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->with('user')->get();

        return view('coach.tasks.create', compact('players'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'status' => 'nullable|in:pending,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high,critical',
            'due_date' => 'nullable|date',
            'subtasks' => 'nullable|array',
            'subtasks.*' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
            'related_team' => 'nullable|exists:teams,id',
            'notify_email' => 'nullable|boolean',
            'notify_chat' => 'nullable|boolean',
        ]);

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Verify the assigned user is a player in coach's teams
        $teamIds = $coach->teams()->pluck('teams.id');
        $playerUserIds = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->pluck('user_id');

        if (!$playerUserIds->contains($request->assigned_to)) {
            return back()->withErrors(['assigned_to' => 'You can only assign tasks to players in your teams.']);
        }

        // Handle file uploads
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('tasks/attachments', $filename, 'public');
                $attachmentPaths[] = [
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        // Filter out empty subtasks
        $subtasks = $request->subtasks ? array_filter($request->subtasks, function($subtask) {
            return !empty(trim($subtask));
        }) : [];

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'status' => $request->status ?? 'pending',
            'priority' => $request->priority ?? 'medium',
            'due_date' => $request->due_date,
            'created_by' => auth()->id(),
            'subtasks' => array_values($subtasks), // Re-index array
            'attachments' => $attachmentPaths,
            'related_team_id' => $request->related_team,
            'notify_email' => $request->has('notify_email') ? true : false,
            'notify_chat' => $request->has('notify_chat') ? true : false,
        ]);

        return redirect()->route('coach.tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Verify coach can edit this task
        $teamIds = $coach->teams()->pluck('teams.id');
        $playerUserIds = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->pluck('user_id');

        if (!$playerUserIds->contains($task->assigned_to)) {
            abort(403, 'You can only edit tasks assigned to players in your teams.');
        }

        // Get players from coach's teams
        $players = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->with('user')->get();

        return view('coach.tasks.edit', compact('task', 'players'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'status' => 'nullable|in:pending,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Verify coach can edit this task
        $teamIds = $coach->teams()->pluck('teams.id');
        $playerUserIds = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->pluck('user_id');

        if (!$playerUserIds->contains($task->assigned_to)) {
            abort(403, 'You can only edit tasks assigned to players in your teams.');
        }

        // Verify the new assigned user is also in coach's teams
        if (!$playerUserIds->contains($request->assigned_to)) {
            return back()->withErrors(['assigned_to' => 'You can only assign tasks to players in your teams.']);
        }

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'status' => $request->status ?? $task->status,
            'priority' => $request->priority ?? $task->priority,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('coach.tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Verify coach can delete this task
        $teamIds = $coach->teams()->pluck('teams.id');
        $playerUserIds = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->pluck('user_id');

        if (!$playerUserIds->contains($task->assigned_to)) {
            abort(403, 'You can only delete tasks assigned to players in your teams.');
        }

        $task->delete();

        return redirect()->route('coach.tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Verify coach can update this task
        $teamIds = $coach->teams()->pluck('teams.id');
        $playerUserIds = Player::whereHas('teams', function($query) use ($teamIds) {
            $query->whereIn('teams.id', $teamIds);
        })->pluck('user_id');

        if (!$playerUserIds->contains($task->assigned_to)) {
            abort(403, 'You can only update tasks assigned to players in your teams.');
        }

        $task->update(['status' => $request->status]);

        return back()->with('success', 'Task status updated successfully.');
    }
}
