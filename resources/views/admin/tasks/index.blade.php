@extends('layouts.admin')
@section('title', 'Tasks')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>Tasks</h4>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Task</a>
    </div>

    @include('partials.alerts')

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                    <tr>
                        <td>{{ $task->title }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($task->status) }}</span></td>
                        <td>{{ $task->user->name ?? 'Unassigned' }}</td>
                        <td>{{ $task->created_at->diffForHumans() }}</td>
                        <td>
                            <a href="{{ route('admin.tasks.edit', $task->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this task?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                            <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#assignModal{{ $task->id }}">Assign</button>
                        </td>
                    </tr>

                    @include('admin.tasks.modal-assign')
                    @empty
                    <tr><td colspan="5">No tasks found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
