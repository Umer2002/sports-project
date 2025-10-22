<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('user')->latest()->get();
        return view('admin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        $users = User::all();
        return view('admin.tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Task::create($request->all());
        return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $users = User::all();
        return view('admin.tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($request->all());
        return redirect()->route('admin.tasks.index')->with('success', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back()->with('success', 'Task deleted.');
    }

    public function assign(Request $request, Task $task)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $task->assigned_to = $request->assigned_to;
        $task->save();

        return back()->with('success', 'Task assigned.');
    }
}
