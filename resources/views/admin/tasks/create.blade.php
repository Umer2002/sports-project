@extends('layouts.admin')
@section('title', isset($task) ? 'Edit Task' : 'Create Task')

@section('content')
<div class="container-fluid py-4">
    <div class="card">
        <div class="card-header">{{ isset($task) ? 'Edit Task' : 'Create New Task' }}</div>
        <div class="card-body">
            <form action="{{ isset($task) ? route('admin.tasks.update', $task->id) : route('admin.tasks.store') }}" method="POST">
                @csrf
                @if(isset($task)) @method('PUT') @endif

                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" value="{{ $task->title ?? old('title') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control">{{ $task->description ?? old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label>Assign to User</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">-- Unassigned --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ (isset($task) && $task->assigned_to == $user->id) ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn-success">{{ isset($task) ? 'Update' : 'Create' }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
