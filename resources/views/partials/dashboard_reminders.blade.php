{{-- <div class="container-fluid mt-5">
    <div class="card bg-dark text-white">
        <div class="card-header border-0 bg-transparent">
            <h5 class="mb-0">Assigned Tasks</h5>
        </div>
        <div class="card-body">


            <ul class="list-group list-group-flush">
                @forelse($tasks as $task)
                    <li
                        class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold">{{ $task->title }}</span><br>
                            <small class="text-muted">Assigned to:
                                {{ optional($task->user)->name ?? 'Unassigned' }}</small>
                        </div>
                        <div>
                            @php
                                $color = match ($task->status) {
                                    'completed' => 'success',
                                    'in_progress' => 'info',
                                    'pending' => 'warning',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $color }} text-uppercase">{{ $task->status }}</span>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item bg-dark text-muted">No tasks found.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div> --}}

<style>
    #task_tab {
        font-size: 60%;
    }

    #task_tab tr td {
        font-size: 100%;
        padding: 3px;
    }

    .dashboard-task-infos .progress {
        height: 5px;
        top: 2px;
    }
</style>
@if(isset($tasks) && count($tasks) > 0)
<div class="container-fluid">
    <div class="col-xs-12">
        <div class="card">
            <div class="header">
                <h2>Assigned Task(s)</h2>
            </div>
            <div class="tableBody p-0">
                <div class="table-responsive">
                    <table class="table table-hover dashboard-task-infos" id="task_tab">
                        <thead>
                            <tr>
                                <th colspan="2">User</th>
                                <th>Task</th>
                                <th>Status</th>
                                <th>Manager</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($tasks) && count($tasks) > 0)
                                @foreach($tasks as $task)
                                    <tr>
                                        <td class="table-img">
                                            <img src="{{ url('assets/images/user/user1.jpg') }}" alt="">
                                        </td>
                                        <td>{{ optional($task->user)->name ?? 'Unassigned' }}</td>
                                        <td>{{ $task->title }}</td>
                                        <td>
                                            @php
                                                $color = match ($task->status) {
                                                    'completed' => 'success',
                                                    'in_progress' => 'info',
                                                    'pending' => 'warning',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span
                                                class="label bg-{{ $color }} shadow-style">{{ $task->status }}</span>
                                        </td>
                                        <td>{{ $task->title }}</td>
                                        <td>
                                            <div class="progress shadow-style">
                                                <div class="progress-bar bg-green width-per-17" role="progressbar"
                                                    aria-valuenow="17" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No tasks found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@php
    $user = Auth::user();
@endphp
@if($user && $user->roles->first()?->name === 'player')
    @include('partials.dashboard_referral')
@endif

<style>
    /* ── Extra styling for the invite card ──────────────────────────────*/
    ._invite-card {
        background: #353C5A !important;
        font-size: 80%
        margin: 10px;
    }

    .step-box {
        width: 24px;
        height: 24px;
        background: #70b3ff;
        color: #0d1b2a;
        font-weight: 700;
        font-size: 0.75rem;
        border-radius: .15rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-pink {
        background: #ff23c8;
        color: #ffffff;
    }

    .btn-pink:hover {
        background: #e11cb4;
        color: #ffffff;
    }
    .vsmall{
        font-size: 50% !important;
    }
    .display-6{
        line-height: 1;
        margin-left: 30px;
    }
    .cblue{
        color:#70b3ff;
    }
</style>

