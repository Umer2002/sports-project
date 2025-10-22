<!-- Assign Task / Reminders -->
<div class="task-card mt-xxl-4" style="margin-top: 20px !important;">
    <!-- Header -->
    <div class="task-header">
        <h5>Assign Task / Reminders</h5>
        <i class="fas fa-ellipsis-h"></i>
    </div>

    <!-- Table -->
    <div class="task-table">
        <div class="task-row task-head">
            <div>User</div>
            <div>Task</div>
            <div>Status</div>
            <div>Manager</div>
            <div>Progress</div>
        </div>

        @forelse($tasks as $task)
            <div class="task-row">
                <div>
                    @if ($task->user && $task->user->profile_photo_path)
                        <img src="{{ Storage::url($task->user->profile_photo_path) }}"
                            alt="{{ $task->user->name }}" />
                    @elseif($task->user)
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($task->user->name) }}&background=0d6efd&color=ffffff&size=32"
                            alt="{{ $task->user->name }}" />
                    @else
                        <img src="{{ asset('assets/club-dashboard-main/assets/emp1.png') }}"
                            alt="Unassigned" />
                    @endif
                </div>
                <div>{{ $task->title }}</div>
                <div>
                    @php
                        $statusClass = match ($task->status) {
                            'completed' => 'done',
                            'in_progress' => 'progress',
                            'pending' => 'todo',
                            'on_hold' => 'hold',
                            'waiting_approval' => 'wait',
                            default => 'todo',
                        };
                        $statusLabel = match ($task->status) {
                            'completed' => 'Done',
                            'in_progress' => 'In Progress',
                            'pending' => 'To Do',
                            'on_hold' => 'On Hold',
                            'waiting_approval' => 'Wait Approval',
                            default => 'To Do',
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}"
                        style="display: inline;">{{ $statusLabel }}</span>
                </div>
                <div>{{ $task->user->name ?? 'Unassigned' }}</div>
                <div class="progress-bar">
                    @php
                        $progress = match ($task->status) {
                            'completed' => 100,
                            'in_progress' => 60,
                            'pending' => 0,
                            'on_hold' => 50,
                            'waiting_approval' => 70,
                            default => 0,
                        };
                        $color = match ($task->status) {
                            'completed' => '#22c55e',
                            'in_progress' => '#6366f1',
                            'pending' => '#8b5cf6',
                            'on_hold' => '#f97316',
                            'waiting_approval' => '#3b82f6',
                            default => '#8b5cf6',
                        };
                    @endphp
                    <span style="width: {{ $progress }}%; background: {{ $color }}"></span>
                </div>
            </div>
        @empty
            <div class="task-row">
                <div colspan="5" class="text-center text-muted py-3">
                    No tasks assigned yet
                </div>
            </div>
        @endforelse
    </div>
</div>