@extends('layouts.coach-dashboard')

@section('title', 'Task Management')
@section('page_title', 'Tasks')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Task Management</h2>
                <button type="button" class="btn btn-primary composer-primary" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                    <i class="fas fa-plus"></i> Create New Task
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Task</th>
                                        <th>Assigned To</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Due Date</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $task->title }}</strong>
                                                    @if($task->description)
                                                        <br><small class="text-muted">{{ Str::limit($task->description, 100) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($task->user)
                                                    <div class="d-flex align-items-center">
                                                        @if($task->user->profile_photo_path)
                                                            <img src="{{ Storage::url($task->user->profile_photo_path) }}" 
                                                                 alt="{{ $task->user->name }}" 
                                                                 class="rounded-circle me-2" 
                                                                 style="width: 32px; height: 32px; object-fit: cover;">
                                                        @else
                                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                                                 style="width: 32px; height: 32px; font-size: 12px;">
                                                                {{ Str::substr($task->user->name, 0, 2) }}
                                                            </div>
                                                        @endif
                                                        <span>{{ $task->user->name }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($task->status) {
                                                        'completed' => 'success',
                                                        'in_progress' => 'info',
                                                        'pending' => 'warning',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }} py-2">{{ ucfirst($task->status) }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $priorityClass = match($task->priority ?? 'medium') {
                                                        'high' => 'danger',
                                                        'medium' => 'warning',
                                                        'low' => 'success',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $priorityClass }} py-2">{{ ucfirst($task->priority ?? 'Medium') }}</span>
                                            </td>
                                            <td>
                                                @if($task->due_date && $task->due_date instanceof \Carbon\Carbon)
                                                    <span class="{{ $task->due_date < now() ? 'text-danger' : '' }}">
                                                        {{ $task->due_date->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No due date</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $task->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('coach.tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <!-- Status Update Dropdown -->
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-primary view-task-btn" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#viewTaskModal{{ $task->id }}"
                                                                title="View Task"
                                                                style="padding: 0.375rem 0.75rem;">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" 
                                                                data-bs-toggle="dropdown"
                                                                style="padding: 0.375rem 0.75rem;">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                            <li>
                                                                <a href="{{ route('coach.tasks.edit', $task) }}" class="dropdown-item">
                                                                    <i class="fas fa-edit me-2"></i>Edit Task
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('coach.tasks.update-status', $task) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="pending">
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-clock me-2"></i>Mark as Pending
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('coach.tasks.update-status', $task) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="in_progress">
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-spinner me-2"></i>Mark as In Progress
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form action="{{ route('coach.tasks.update-status', $task) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="completed">
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-check me-2"></i>Mark as Completed
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('coach.tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="fas fa-trash me-2"></i>Delete Task
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- View Task Modal -->
                                        <div class="modal fade" id="viewTaskModal{{ $task->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-tasks me-2"></i>{{ $task->title }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <div class="mb-3">
                                                                    <h6 class="text-muted mb-2">Description</h6>
                                                                    <p>{{ $task->description ?: 'No description provided.' }}</p>
                                                                </div>

                                                                @if($task->subtasks && count($task->subtasks) > 0)
                                                                    <div class="mb-3">
                                                                        <h6 class="text-muted mb-2">Subtasks</h6>
                                                                        <ul class="list-unstyled">
                                                                            @foreach($task->subtasks as $subtask)
                                                                                <li class="mb-2">
                                                                                    <i class="fas fa-circle text-muted me-2" style="font-size: 8px;"></i>
                                                                                    {{ $subtask }}
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endif

                                                                @if($task->attachments && count($task->attachments) > 0)
                                                                    <div class="mb-3">
                                                                        <h6 class="text-muted mb-2">Attachments</h6>
                                                                        <div class="list-group">
                                                                            @foreach($task->attachments as $attachment)
                                                                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="list-group-item list-group-item-action">
                                                                                    <i class="fas fa-file me-2"></i>
                                                                                    {{ $attachment['filename'] }}
                                                                                    <small class="text-muted">({{ number_format($attachment['size'] / 1024, 2) }} KB)</small>
                                                                                </a>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="mb-3">
                                                                    <h6 class="text-muted mb-2">Assigned To</h6>
                                                                    @if($task->user)
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                                                                                 style="width: 32px; height: 32px; background: linear-gradient(45deg, #007bff, #0056b3); color: white; font-size: 12px; font-weight: bold;">
                                                                                {{ Str::substr($task->user->name, 0, 2) }}
                                                                            </div>
                                                                            <span>{{ $task->user->name }}</span>
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted">Unassigned</span>
                                                                    @endif
                                                                </div>

                                                                <div class="mb-3">
                                                                    <h6 class="text-muted mb-2">Due Date</h6>
                                                                    @if($task->due_date && $task->due_date instanceof \Carbon\Carbon)
                                                                        <p class="{{ $task->due_date < now() ? 'text-danger' : '' }}">
                                                                            {{ $task->due_date->format('M d, Y h:i A') }}
                                                                        </p>
                                                                    @else
                                                                        <p class="text-muted">No due date set</p>
                                                                    @endif
                                                                </div>

                                                                <div class="mb-3">
                                                                    <h6 class="text-muted mb-2">Priority</h6>
                                                                    @php
                                                                        $priorityClass = match($task->priority) {
                                                                            'low' => 'success',
                                                                            'medium' => 'warning',
                                                                            'high' => 'danger',
                                                                            'critical' => 'dark',
                                                                            default => 'secondary'
                                                                        };
                                                                    @endphp
                                                                    <span class="badge bg-{{ $priorityClass }}">{{ ucfirst($task->priority ?? 'Medium') }}</span>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <h6 class="text-muted mb-2">Status</h6>
                                                                    @php
                                                                        $statusClass = match($task->status) {
                                                                            'completed' => 'success',
                                                                            'in_progress' => 'info',
                                                                            'pending' => 'warning',
                                                                            default => 'secondary'
                                                                        };
                                                                    @endphp
                                                                    <span class="badge bg-{{ $statusClass }}">{{ ucfirst($task->status) }}</span>
                                                                </div>

                                                                @if($task->team)
                                                                    <div class="mb-3">
                                                                        <h6 class="text-muted mb-2">Related Team</h6>
                                                                        <p>{{ $task->team->name }}</p>
                                                                    </div>
                                                                @endif

                                                                <div class="mb-3">
                                                                    <h6 class="text-muted mb-2">Notifications</h6>
                                                                    @if($task->notify_email)
                                                                        <span class="badge bg-info me-1"><i class="fas fa-envelope me-1"></i>Email</span>
                                                                    @endif
                                                                    @if($task->notify_chat)
                                                                        <span class="badge bg-primary"><i class="fas fa-comments me-1"></i>Chat</span>
                                                                    @endif
                                                                    @if(!$task->notify_email && !$task->notify_chat)
                                                                        <span class="text-muted">None</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <a href="{{ route('coach.tasks.edit', $task) }}" class="btn btn-primary">
                                                            <i class="fas fa-edit me-1"></i>Edit Task
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $tasks->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No tasks found</h5>
                            <p class="text-muted">Create your first task to get started.</p>
                            <a href="{{ route('coach.tasks.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create New Task
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Task Action Buttons Styling */
.btn-group {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 0.25rem;
}

.view-task-btn {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}

.btn-group .dropdown-toggle {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
    border-left: 1px solid rgba(255,255,255,0.2) !important;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}

.btn-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
}

.dropdown-menu {
    border: 1px solid rgba(0,0,0,0.1);
    margin-top: 0.25rem;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    padding-left: 1.25rem;
}

.dropdown-item i {
    width: 20px;
    text-align: center;
}

.dropdown-divider {
    margin: 0.25rem 0;
}

/* Badge Styling */
.badge {
    font-weight: 500;
    letter-spacing: 0.5px;
    padding: 0.5rem 0.75rem !important;
}

/* Table Actions Column */
.table td:last-child {
    white-space: nowrap;
}
</style>

@include('coach.partials.task-assignment-modal')
@endsection
