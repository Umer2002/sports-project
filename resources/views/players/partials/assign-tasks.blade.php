<div class="card bg-dark text-white">
    <div class="card-header">
        <strong>Assign Task</strong>
    </div>
    <div class="card-body p-0">
        <table class="table table-dark table-hover m-0">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Manager</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr>
                    <td>
                        <img src="{{ asset($task->user->avatar ?? 'images/avatar-default.png') }}" class="rounded-circle me-2" width="30">
                        {{ $task->user->first_name }}
                    </td>
                    <td>{{ $task->title }}</td>
                    <td><span class="badge bg-{{ getStatusColor($task->status) }}">{{ $task->status }}</span></td>
                    <td>{{ $task->manager->name }}</td>
                    <td>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width: {{ $task->progress }}%; background-color: {{ getStatusColor($task->status, true) }}"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
