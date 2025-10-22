@extends('layouts.coach-dashboard')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Coach Dashboard')
@section('page_title', 'Dashboard')

@section('header_styles')
    <style>
        .light .assign-task-modal .form-control,
        .light .assign-task-modal .form-select {
            background: #f1f1f1;
            border: 1px solid #f1f1f1;
            color: #363c40 !important;
        }
    </style>
@endsection

@section('footer_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            if (calendarEl) {
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: "dayGridMonth",
                    events: {!! json_encode($events ?? []) !!},
                    eventClick: function(info) {
                        info.jsEvent.preventDefault();
                        if (info.event.url) {
                            window.open(info.event.url, '_blank');
                        }
                    }
                });

                calendar.render();
            }

            // Chart.js configurations
            const ctx1 = document.getElementById('chart1');
            if (ctx1) {
                new Chart(ctx1, {
                    type: 'doughnut',
                    data: {
                        labels: ['Active Players', 'Inactive Players'],
                        datasets: [{
                            data: [{{ $activePlayers ?? 0 }}, {{ $inactivePlayers ?? 0 }}],
                            backgroundColor: ['#28a745', '#6c757d'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: 'white',
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection

@section('content')
    @php
        $coachSocialLinksData = [];
        if (isset($socialLinks) && is_array($socialLinks)) {
            foreach ([
                'facebook' => 'facebook-f',
                'instagram' => 'instagram',
                'twitter' => 'twitter',
            ] as $platform => $icon) {
                $link = $socialLinks[$platform] ?? null;
                if ($link) {
                    $coachSocialLinksData[] = [
                        'type' => 'icon',
                        'icon' => $icon,
                        'url' => $link,
                        'class' => $platform,
                        'label' => ucfirst($platform),
                    ];
                }
            }
        }

        $coachEntityMedia = [
            'fallback_image' => asset('assets/club-dashboard-main/assets/football.png'),
            'alt' => optional($coach->sport ?? null)->name ?? 'Sport',
        ];

        if (isset($coach) && $coach->sport && $coach->sport->icon_path) {
            $coachEntityMedia['image'] = asset('storage/' . $coach->sport->icon_path);
            $coachEntityMedia['attributes'] = 'style="height:70px; border: 1px solid; border-radius:50px"';
        }

        $coachStatCards = [
            [
                'color' => 'green',
                'image' => asset('assets/club-dashboard-main/assets/bars.png'),
                'badge' => 'My Teams',
                'value' => $teamCount ?? 0,
                'trend_icon' => asset('assets/club-dashboard-main/assets/ic_trending_up.png'),
                'footer' => 'Teams Under Your Supervision',
            ],
            [
                'color' => 'orange',
                'image' => asset('assets/club-dashboard-main/assets/graph.png'),
                'badge' => 'Total Players',
                'value' => $activePlayers ?? 0,
                'trend_icon' => asset('assets/club-dashboard-main/assets/ic_trending_up.png'),
                'footer' => 'Players in Your Teams',
            ],
            [
                'color' => 'blue',
                'image' => asset('assets/club-dashboard-main/assets/bars.png'),
                'badge' => 'Scheduled Matches',
                'value' => $scheduledMatches ?? 0,
                'trend_icon' => asset('assets/club-dashboard-main/assets/ic_trending_up.png'),
                'footer' => 'Upcoming Matches',
            ],
            [
                'color' => 'purple',
                'image' => asset('assets/club-dashboard-main/assets/pie.png'),
                'badge' => 'Completed Matches',
                'value' => $completedMatches ?? 0,
                'trend_icon' => asset('assets/club-dashboard-main/assets/ic_trending_up.png'),
                'footer' => 'Matches Completed',
            ],
        ];
    @endphp

    @include('partials.dashboard_weather_widget', [
        'entity' => $coachEntityMedia,
        'weather' => $weather ?? null,
        'locationFallback' => '--',
        'socialLinks' => $coachSocialLinksData,
        'fallbackTemperature' => '-°',
    ])

    @include('partials.dashboard_stat_card_grid', [
        'cards' => $coachStatCards,
        'rowClass' => 'mb-4',
        'colClasses' => 'col-sm-6 col-md-6 col-lg-3',
    ])


    <!-- Metric Widget -->
    <div class="metric-widget gray-card p-4 pb-0 mb-4">
        <div class="section-header mb-3 d-flex justify-content-between">
            <h2 class="fs-5 mb-0">Performance Metrics</h2>
            <div><i class="bi bi-three-dots"></i></div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6 col-xxl-3">
                <div class="metric-card p-3 mb-4 d-flex gap-2">
                    <div class="time-breakdown-chart">
                        <div class="percentage-chart percentage-chart-meeting">
                            <svg viewBox="0 0 36 36">
                                <defs>
                                    <linearGradient id="circleGradient1" x1="0%" y1="0%" x2="100%"
                                        y2="100%">
                                        <stop offset="0%" style="stop-color: #e2a944; stop-opacity: 1" />
                                        <stop offset="90%" style="stop-color: #ea7d4d; stop-opacity: 1" />
                                    </linearGradient>
                                </defs>
                                <path class="percentage-chart-bg"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="percentage-chart-stroke" stroke="url(#circleGradient1)"
                                    stroke-dasharray="{{ min(($injuryReports ?? 0) * 10, 100) }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="counter" style="--counter-end: {{ min(($injuryReports ?? 0) * 10, 100) }}"></div>
                        </div>
                    </div>
                    <div class="metric-text">
                        <h3 class="metric-title fs-14px mb-0">Reported Injuries</h3>
                        <div class="progress-xy my-2" style="height: 5px">
                            <div class="progress-bar orange-grad"
                                style="width: {{ min(($injuryReports ?? 0) * 10, 100) }}%"></div>
                        </div>
                        <div class="metric-subtitle fs-12px">
                            {{ $injuryReports ?? 0 }} Injuries<br />Reported
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-6 col-xxl-3">
                <div class="metric-card p-3 mb-4 d-flex gap-2">
                    <div class="time-breakdown-chart">
                        <div class="percentage-chart percentage-chart-meeting">
                            <svg viewBox="0 0 36 36">
                                <defs>
                                    <linearGradient id="circleGradient2" x1="0%" y1="0%" x2="100%"
                                        y2="100%">
                                        <stop offset="0%" style="stop-color: #78bdde; stop-opacity: 1" />
                                        <stop offset="90%" style="stop-color: #0075ff; stop-opacity: 1" />
                                    </linearGradient>
                                </defs>
                                <path class="percentage-chart-bg"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="percentage-chart-stroke" stroke="url(#circleGradient2)"
                                    stroke-dasharray="{{ min(($playerTransfers ?? 0) * 5, 100) }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="counter" style="--counter-end: {{ min(($playerTransfers ?? 0) * 5, 100) }}">
                            </div>
                        </div>
                    </div>
                    <div class="metric-text">
                        <h3 class="metric-title fs-14px mb-0">Player Transfers</h3>
                        <div class="progress-xy my-2" style="height: 5px">
                            <div class="progress-bar blue-grad"
                                style="width: {{ min(($playerTransfers ?? 0) * 5, 100) }}%"></div>
                        </div>
                        <div class="metric-subtitle fs-12px">
                            {{ $playerTransfers ?? 0 }} Transfers<br />This Month
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 5: Awards -->
            <div class="col-sm-6 col-md-6 col-lg-3">
                <div class="metric-card p-3 mb-4 d-flex gap-2">
                    <div class="time-breakdown-chart">
                        <div class="percentage-chart percentage-chart-meeting">
                            <svg viewBox="0 0 36 36">
                                <defs>
                                    <linearGradient id="circleGradient3" x1="0%" y1="0%" x2="100%"
                                        y2="100%">
                                        <stop offset="0%" style="stop-color: #20c997; stop-opacity: 1" />
                                        <stop offset="90%" style="stop-color: #17a2b8; stop-opacity: 1" />
                                    </linearGradient>
                                </defs>
                                <path class="percentage-chart-bg"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="percentage-chart-stroke" stroke="url(#circleGradient3)"
                                    stroke-dasharray="{{ min(($awardsAssigned ?? 0) * 20, 100) }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="counter" style="--counter-end: {{ min(($awardsAssigned ?? 0) * 20, 100) }}">
                            </div>
                        </div>
                    </div>
                    <div class="metric-text">
                        <h3 class="metric-title fs-14px mb-0">Awards Assigned</h3>
                        <div class="progress-xy my-2" style="height: 5px">
                            <div class="progress-bar teal-grad"
                                style="width: {{ min(($awardsAssigned ?? 0) * 20, 100) }}%"></div>
                        </div>
                        <div class="metric-subtitle fs-12px">
                            {{ $awardsAssigned ?? 0 }} Awards<br />Given to Players
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 6: Tournaments -->
            <div class="col-sm-6 col-md-6 col-lg-3">
                <div class="metric-card p-3 mb-4 d-flex gap-2">
                    <div class="time-breakdown-chart">
                        <div class="percentage-chart percentage-chart-meeting">
                            <svg viewBox="0 0 36 36">
                                <defs>
                                    <linearGradient id="circleGradient4" x1="0%" y1="0%" x2="100%"
                                        y2="100%">
                                        <stop offset="0%" style="stop-color: #6f42c1; stop-opacity: 1" />
                                        <stop offset="90%" style="stop-color: #e83e8c; stop-opacity: 1" />
                                    </linearGradient>
                                </defs>
                                <path class="percentage-chart-bg"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="percentage-chart-stroke" stroke="url(#circleGradient4)"
                                    stroke-dasharray="{{ min(($activeTournaments ?? 0) * 25, 100) }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="counter" style="--counter-end: {{ min(($activeTournaments ?? 0) * 25, 100) }}">
                            </div>
                        </div>
                    </div>
                    <div class="metric-text">
                        <h3 class="metric-title fs-14px mb-0">Active Tournaments</h3>
                        <div class="progress-xy my-2" style="height: 5px">
                            <div class="progress-bar purple-grad"
                                style="width: {{ min(($activeTournaments ?? 0) * 25, 100) }}%"></div>
                        </div>
                        <div class="metric-subtitle fs-12px">
                            {{ $activeTournaments ?? 0 }} Tournaments
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="two-column">
        <div class="left-column">
            <!-- Action Button section -->
            <div class="action-widget p-4 mb-0">
                <div class="section-header">
                    <h2 class="section-title">Quick Actions</h2>
                    <button class="section-menu">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
                <div class="d-flex flex-wrap gap-3 w-100">
                    <a href="{{ route('coach.players.index') }}" class="action-btn green">
                        <i class="fas fa-user-friends"></i> View Players
                    </a>
                    <button type="button" class="action-btn cyan" data-bs-toggle="modal" data-bs-target="#assignAward">
                        <i class="fas fa-award"></i> Assign Award
                    </button>
                    <a href="{{ route('coach.tournaments.index') }}" class="action-btn teal">
                        <i class="fas fa-sitemap"></i> Tournament Engine
                    </a>
                    <a href="{{ route('coach.players.index') }}" class="action-btn blue">
                        <i class="fas fa-users"></i> Players
                    </a>
                    <a href="{{ route('coach.teams.index') }}" class="action-btn orange">
                        <i class="fas fa-users"></i> Team Management
                    </a>
                    <a href="{{ route('coach.events.index') }}" class="action-btn purple">
                        <i class="fas fa-calendar-alt"></i> Events
                    </a>
                    <a href="{{ route('coach.blog.index') }}" class="action-btn red">
                        <i class="fas fa-blog"></i> Blog Post
                    </a>
                    <button type="button" class="action-btn yellow" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                        <i class="fas fa-tasks"></i> Assign Task
                    </button>
                </div>
            </div>

            <!-- Calendar section -->
            <div class="my-4 calendar-container">
                <div id="calendar"></div>
            </div>

            <div class="tournament-card p-4 mb-4">
                <div class="row justify-content-between w-100 mb-2">
                    <!-- Left -->
                    <div class="col-md-auto">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('assets/club-dashboard-main/assets/tr.png') }}" alt="stadium"
                                class="tournament-img">
                            <h3 class="tournament-title" style="color: #fff;">Tournament Directory</h3>
                        </div>
                    </div>

                    <!-- Right -->
                    <div class="col-md-auto text-end align-self-center">
                        <div class="d-flex gap-3 justify-content-end align-items-center">
                            <div class="subtitle fw-semibold" style="color: #fff;">Tournament Engine</div>
                            <button class="start-btn"
                                onclick="window.location.href='{{ route('coach.tournaments.index') }}'">Start</button>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="card-desc" style="color: #fff;">
                    Search and register for tournaments by location, date, sport,
                    division, age, and more.
                </div>
            </div>
        </div>

        <div class="right-column">
            <!-- Chat Panel -->
            <div class="chat-container">
                <div class="chat-header">
                    <h3>Chats</h3>
                    <button class="menu-btn">⋯</button>
                </div>
                <div class="chat-search">
                    <input type="text" placeholder="Search" />
                </div>
                <div class="chat-list">
                    @php
                        $chatGroups = [
                            ['title' => 'Coaches', 'items' => collect($chatCoaches ?? [])],
                            ['title' => 'Team Members', 'items' => collect($chatPlayers ?? [])],
                        ];
                        $hasContacts = false;
                    @endphp

                    @foreach ($chatGroups as $group)
                        @if ($group['items']->count())
                            @php $hasContacts = true; @endphp
                            <div class="chat-group">
                                <div class="chat-group-title">
                                    <span>{{ $group['title'] }}</span>
                                    <span class="chat-group-count">{{ $group['items']->count() }}</span>
                                </div>
                                @foreach ($group['items'] as $contact)
                                    <div class="chat-item {{ empty($contact['user_id']) ? 'disabled' : '' }}"
                                        data-contact-name="{{ \Illuminate\Support\Str::slug($contact['name']) }}"
                                        data-contact-role="{{ \Illuminate\Support\Str::slug($contact['role']) }}"
                                        data-contact-label="{{ e($contact['name']) }}"
                                        data-user-id="{{ $contact['user_id'] ?? '' }}"
                                        data-contact-tagline="{{ e($contact['tagline'] ?? '') }}"
                                        data-contact-status="{{ e($contact['status_label'] ?? '') }}"
                                        data-contact-initials="{{ e($contact['initials'] ?? '') }}"
                                        title="{{ empty($contact['user_id']) ? 'Chat unavailable for this contact' : '' }}">
                                        <div class="chat-avatar">
                                            <img src="{{ $contact['avatar'] }}" alt="{{ $contact['name'] }}"
                                                loading="lazy" />
                                        </div>
                                        <div class="chat-info">
                                            <span class="name">{{ $contact['name'] }}</span>
                                            @if (!empty($contact['tagline']))
                                                <span class="meta">{{ $contact['tagline'] }}</span>
                                            @endif
                                            <span class="status {{ $contact['status'] }}">●
                                                {{ $contact['status_label'] }}</span>
                                        </div>
                                        <span class="arrow">›</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach

                    <div class="chat-empty" @if ($hasContacts) style="display: none;" @endif>
                        <p class="mb-1">No contacts yet.</p>
                        <p class="small mb-0">Start by adding team members.</p>
                    </div>
                </div>
            </div>

            <!-- Tournament Engine Card -->
            <div class="d-flex align-items-center justify-content-between gradient-card p-2 rounded mb-1">
                <!-- Left side -->
                <div class="d-flex align-items-center text-white">
                    <i class="fas fa-trophy me-2"></i>
                    <span class="fw-semibold fst-italic">Tournament Engine</span>
                </div>

                <!-- Right side -->
                <button class="btn btn-success btn-sm fw-semibold px-3 start-btn"
                    onclick="window.location.href='{{ route('coach.tournaments.index') }}'">
                    Start
                </button>
            </div>
            <!-- Assign Task / Reminders -->
            <div class="task-card">
                <!-- Header -->
                <div class="task-header">
                    <h5>Assign Task / Reminders</h5>

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
                                        default => 'todo',
                                    };
                                    $statusLabel = match ($task->status) {
                                        'completed' => 'Done',
                                        'in_progress' => 'In Progress',
                                        'pending' => 'To Do',
                                        default => 'To Do',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}" style="display: inline;">{{ $statusLabel }}</span>
                            </div>
                            <div>{{ $task->user->name ?? 'Unassigned' }}</div>
                            <div class="progress-bar">
                                @php
                                    $progress = match ($task->status) {
                                        'completed' => 100,
                                        'in_progress' => 60,
                                        'pending' => 0,
                                        default => 0,
                                    };
                                    $color = match ($task->status) {
                                        'completed' => '#22c55e',
                                        'in_progress' => '#6366f1',
                                        'pending' => '#8b5cf6',
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
        </div>
    </div>

    <style>
        /* Remove vertical gaps between right column divs */
        .right-column>div {
            margin-bottom: 0 !important;
        }

        .right-column {
            justify-content: flex-start;
        }

        .right-column>div:not(:last-child) {
            margin-bottom: 1rem !important;
        }

        /* Ensure chat container has no bottom margin */
        .chat-container {
            margin-bottom: 0 !important;
        }

        /* Ensure tournament engine card has minimal spacing */
        .gradient-card {
            margin-bottom: 0 !important;
        }

        /* Ensure task card has no top margin */
        .task-card {
            margin-top: 0 !important;
        }

        /* Action buttons styling */
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            color: white !important;
            border: none;
            cursor: pointer;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: white !important;
        }

        .action-btn i {
            color: white !important;
        }

        .action-btn.green {
            background: linear-gradient(135deg, #4CAF50, #45a049);
        }

        .action-btn.cyan {
            background: linear-gradient(135deg, #00BCD4, #0097A7);
        }

        .action-btn.teal {
            background: linear-gradient(135deg, #009688, #00796B);
        }

        .action-btn.blue {
            background: linear-gradient(135deg, #2196F3, #1976D2);
        }

        .action-btn.orange {
            background: linear-gradient(135deg, #FF9800, #F57C00);
        }

        .action-btn.purple {
            background: linear-gradient(135deg, #9C27B0, #7B1FA2);
        }

        .action-btn.red {
            background: linear-gradient(135deg, #F44336, #D32F2F);
        }

        /* Progress bar gradients */
        .progress-bar.teal-grad {
            background: linear-gradient(90deg, #20c997 0%, #17a2b8 100%);
        }

        .progress-bar.purple-grad {
            background: linear-gradient(90deg, #6f42c1 0%, #e83e8c 100%);
        }

        /* Modal player selection styles */
        .player-item {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f0f0f0;
        }

        .player-item:last-child {
            border-bottom: none;
        }

        .player-item:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
        }

        .player-item.selected {
            background-color: #e3f2fd;
            border-left: 3px solid #007bff;
        }

        .player-item.selected .form-check-input {
            background-color: #007bff;
            border-color: #007bff;
        }

        .player-list {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }

        .player-list::-webkit-scrollbar {
            width: 6px;
        }

        .player-list::-webkit-scrollbar-track {
            background: #a3a0a0;
            border-radius: 3px;
        }

        .player-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .player-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .chip-badge {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
        }

        .visibility-btn {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .visibility-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Start button text color */
        .start-btn {
            color: white !important;
        }
    </style>

    <!-- Assign Award Modal -->
    <div class="modal fade" id="assignAward" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-body assign-award-modal rounded-4">
                    <button class="btn-close-ghost" aria-label="Close" data-bs-dismiss="modal">&times;</button>
                    <div class="modal-title-block p-4">
                        <div class="brand-dot rounded-4"></div>
                        <div>
                            <div class="popup-title-color">Assign Award</div>
                            <div class="popup-sub-color">COACH / CLUB • SELECT AWARD AND PLAYERS</div>
                        </div>
                    </div>

                    <form action="{{ route('coach.awards.store') }}" method="POST" enctype="multipart/form-data"
                        id="awardAssignmentForm">
                        @csrf
                        <div class="d-flex gap-4 flex-column flex-md-row px-4">
                            <div class="flex-1 gap-2 d-flex flex-column">
                                <div class="assign-award-card flex-1 rounded-4 p-3">
                                    <label class="form-label">Award</label>
                                    <select class="form-select" name="award_id" id="modalAwardSelect" required>
                                        <option value="">Select a reward</option>
                                        @foreach ($rewards as $reward)
                                            <option value="{{ $reward->id }}">{{ $reward->name }}
                                                ({{ ucfirst($reward->type) }})</option>
                                        @endforeach
                                    </select>

                                    <div class="left-split mt-3">
                                        <div>
                                            <label for="modalFileInput" class="upload-box" id="modalUploadBox">
                                                <div id="modalImagePreview" style="display: none;">
                                                    <img id="modalPreviewImg" src="" alt="Award Preview"
                                                        style="max-width: 100%; max-height: 100px; border-radius: 8px;">
                                                    {{-- <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="modalRemoveImage">Remove Image</button> --}}
                                                </div>
                                                <div id="modalUploadPlaceholder">
                                                    <p class="mb-0 small-font">Award image</p>
                                                </div>
                                            </label>
                                            <input type="file" id="modalFileInput" name="award_image" hidden
                                                accept="image/*">
                                        </div>

                                        <div>
                                            <label class="form-label small-font">DESCRIPTION</label>
                                            <textarea class="form-select" name="description" id="modalDescription" rows="6"
                                                placeholder="Custom description for this award assignment..."></textarea>
                                            <div class="mt-2">
                                                <button type="button" class="p-1 form-select"
                                                    id="modalAutoFromCatalog">Auto from catalog</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-3">
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label small-font">Requirements</label>
                                            <textarea class="form-select" name="requirements" id="modalRequirements" rows="2"
                                                placeholder="Award requirements..."></textarea>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label small-font">Rewards</label>
                                            <textarea class="form-select" name="rewards" id="modalRewards" rows="2" placeholder="Award rewards..."></textarea>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label class="form-label small-font">Other Awards</label>
                                        <div class="d-flex align-items-center">
                                            <span class="swatch primary"></span>
                                            <span class="swatch mid"></span>
                                            <span class="swatch light"></span>
                                            <span class="swatch light"></span>
                                            <span class="swatch light"></span>
                                            <span class="swatch light"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-1 d-flex gap-3 d-flex d-md-none">
                                    <button type="button" class="popup-btn py-1 px-4"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="popup-btn popup-btn-active py-1 px-4">Assign
                                        Award</button>
                                </div>
                            </div>
                            <div class="flex-1 gap-2 d-flex flex-column">
                                <div class="assign-award-card flex-1 rounded-4 p-3">
                                    <label class="form-label">Assign to</label>
                                    <div class="assign-row">
                                        <input class="form-select" id="modalPlayerSearch" placeholder="Search players" />
                                        <button type="button" class="btn-grad small-font" id="modalSelectAll">Select
                                            All</button>
                                    </div>

                                    <!-- Available Players List -->
                                    <div class="mt-2 mb-3">
                                        <div class="player-list border rounded"
                                            style="max-height: 200px; overflow-y: auto;">
                                            @foreach ($modalPlayers as $player)
                                                <div class="form-check player-item p-2 border-bottom"
                                                    data-player-name="{{ strtolower($player->name) }}"
                                                    data-player-team="{{ strtolower($player->teams->first()->name ?? '') }}"
                                                    data-player-id="{{ $player->id }}">
                                                    <input class="form-check-input" type="checkbox" name="player_ids[]"
                                                        value="{{ $player->id }}"
                                                        id="modal_player_{{ $player->id }}">
                                                    <label class="form-check-label d-flex align-items-center w-100"
                                                        for="modal_player_{{ $player->id }}" style="cursor: pointer;">
                                                        <div class="player-avatar me-3"
                                                            style="width: 32px; height: 32px; background: linear-gradient(45deg, #007bff, #0056b3); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                                                            {{ strtoupper(substr($player->name, 0, 2)) }}
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-medium">{{ $player->name }}</div>
                                                            <small class="text-muted">
                                                                <i
                                                                    class="fas fa-users me-1"></i>{{ $player->teams->first()->name ?? 'No Team' }}
                                                            </small>
                                                        </div>
                                                        <div class="player-status">
                                                            <span class="badge bg-success">Active</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Selected Players Display -->
                                    <div class="mt-2 d-flex flex-wrap gap-2 w-100" id="modalSelectedPlayers">
                                        <!-- Selected players will appear here -->
                                    </div>

                                    <div class="two-col mt-3 d-flex flex-column">
                                        <div>
                                            <label class="form-label small-font">Date Awarded</label>
                                            <input class="form-select" name="awarded_at"
                                                value="{{ now()->format('Y-m-d\TH:i') }}" type="datetime-local" />
                                        </div>
                                        <div class="">
                                            <label class="form-label small-font">Visibility</label>
                                            <div class="form-select d-flex justify-content-between gap-2">
                                                <input type="radio" name="visibility" value="public"
                                                    id="modal_visibility_public" class="d-none" checked>
                                                <label for="modal_visibility_public"
                                                    class="popup-btn popup-btn-active flex-1 visibility-btn"
                                                    data-value="public">Public</label>
                                                <input type="radio" name="visibility" value="team"
                                                    id="modal_visibility_team" class="d-none">
                                                <label for="modal_visibility_team" class="popup-btn flex-1 visibility-btn"
                                                    data-value="team">Team only</label>
                                                <input type="radio" name="visibility" value="private"
                                                    id="modal_visibility_private" class="d-none">
                                                <label for="modal_visibility_private"
                                                    class="popup-btn flex-1 visibility-btn"
                                                    data-value="private">Private</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label class="form-label small-font">Coach Note (optional)</label>
                                        <textarea class="form-select" name="coach_note" rows="5"
                                            placeholder="Write a short message to appear with the award..."></textarea>
                                    </div>

                                    <div class="action-trio mt-3 d-flex gap-2">
                                        <input type="checkbox" name="notify_player" id="modal_notify_player"
                                            value="1" checked class="d-none">
                                        <label for="modal_notify_player"
                                            class="popup-btn popup-btn-active action-checkbox flex-1"
                                            data-checkbox="modal_notify_player">Notify player</label>

                                        <input type="checkbox" name="post_to_feed" id="modal_post_to_feed"
                                            value="1" class="d-none">
                                        <label for="modal_post_to_feed" class="popup-btn action-checkbox flex-1"
                                            data-checkbox="modal_post_to_feed">Post to team feed</label>

                                        <input type="checkbox" name="add_to_profile" id="modal_add_to_profile"
                                            value="1" checked class="d-none">
                                        <label for="modal_add_to_profile"
                                            class="popup-btn popup-btn-active action-checkbox flex-1"
                                            data-checkbox="modal_add_to_profile">Add to profile</label>
                                    </div>
                                </div>
                                <div class="flex-1 d-flex gap-3 d-flex d-md-none">
                                    <button type="button" class="popup-btn py-2 px-4" id="modalSaveDraft">Save
                                        Draft</button>
                                    <button type="button" class="popup-btn popup-btn-active py-1 px-4"
                                        onclick="window.open('{{ route('coach.awards.log') }}', '_blank')">View
                                        Log</button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-4 flex-column flex-md-row mt-4 p-2">
                            <div class="flex-1 d-flex gap-3 d-none d-md-flex">
                                <button type="button" class="popup-btn py-1 px-4"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="popup-btn popup-btn-active py-1 px-4">Assign Award</button>
                            </div>
                            <div class="flex-1 d-flex gap-3 d-none d-md-flex">
                                <button type="button" class="popup-btn py-2 px-4" id="modalSaveDraftDesktop">Save
                                    Draft</button>
                                <button type="button" class="popup-btn popup-btn-active py-1 px-4"
                                    onclick="window.open('{{ route('coach.awards.log') }}', '_blank')">View Log</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal elements
            const modalAwardSelect = document.getElementById('modalAwardSelect');
            const modalPlayerSearch = document.getElementById('modalPlayerSearch');
            const modalSelectAll = document.getElementById('modalSelectAll');
            const modalSelectedPlayers = document.getElementById('modalSelectedPlayers');
            const modalAutoFromCatalog = document.getElementById('modalAutoFromCatalog');
            const modalDescription = document.getElementById('modalDescription');
            const modalRequirements = document.getElementById('modalRequirements');
            const modalRewards = document.getElementById('modalRewards');
            const modalSaveDraft = document.getElementById('modalSaveDraft');
            const modalSaveDraftDesktop = document.getElementById('modalSaveDraftDesktop');
            const awardAssignmentForm = document.getElementById('awardAssignmentForm');

            // Player data from server
            const players = @json($modalPlayers);
            const selectedPlayers = new Map();

            // Award selection change
            modalAwardSelect.addEventListener('change', function() {
                const awardId = this.value;
                if (awardId) {
                    fetchAwardDetails(awardId);
                }
            });

            // Fetch award details
            function fetchAwardDetails(awardId) {
                fetch(`/coach/awards/${awardId}/details`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const award = data.award;
                            modalDescription.value = award.description || '';
                            modalRequirements.value = award.requirements || '';
                            modalRewards.value = award.rewards || '';

                            // Load award image if available
                            if (award.image && award.image.trim() !== '') {
                                loadAwardImage(award.image, award.name);
                            } else {
                                clearAwardImage();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching award details:', error);
                    });
            }

            // Load award image
            function loadAwardImage(imageUrl, awardName) {
                const previewImg = document.getElementById('modalPreviewImg');
                const imagePreview = document.getElementById('modalImagePreview');
                const uploadPlaceholder = document.getElementById('modalUploadPlaceholder');

                previewImg.src = imageUrl;
                previewImg.alt = awardName + ' Award';
                imagePreview.style.display = 'block';
                uploadPlaceholder.style.display = 'none';
            }

            // Clear award image
            function clearAwardImage() {
                const imagePreview = document.getElementById('modalImagePreview');
                const uploadPlaceholder = document.getElementById('modalUploadPlaceholder');
                const fileInput = document.getElementById('modalFileInput');

                imagePreview.style.display = 'none';
                uploadPlaceholder.style.display = 'block';
                fileInput.value = '';
            }

            // Auto from catalog
            modalAutoFromCatalog.addEventListener('click', function() {
                const awardId = modalAwardSelect.value;
                if (awardId) {
                    fetchAwardDetails(awardId);
                    this.innerHTML = '<i class="fas fa-check me-1"></i>Auto-populated!';
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-success');

                    setTimeout(() => {
                        this.innerHTML = 'Auto from catalog';
                        this.classList.remove('btn-success');
                        this.classList.add('btn-outline-secondary');
                    }, 2000);
                } else {
                    alert('Please select an award first.');
                }
            });

            // Player search
            modalPlayerSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const playerItems = document.querySelectorAll('.player-item');

                playerItems.forEach(item => {
                    const playerName = item.dataset.playerName;
                    const playerTeam = item.dataset.playerTeam;
                    const matchesName = playerName.includes(searchTerm);
                    const matchesTeam = playerTeam.includes(searchTerm);

                    if (matchesName || matchesTeam || searchTerm === '') {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });

                updateSelectAllButton();
            });

            // Select all players
            modalSelectAll.addEventListener('click', function() {
                const visibleItems = Array.from(document.querySelectorAll('.player-item')).filter(item =>
                    item.style.display !== 'none');
                const allChecked = visibleItems.every(item => item.querySelector('input[type="checkbox"]')
                    .checked);

                visibleItems.forEach(item => {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    checkbox.checked = !allChecked;
                    updatePlayerSelection(checkbox);
                });

                updateSelectAllButton();
            });

            // Individual player selection
            document.querySelectorAll('.player-item input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updatePlayerSelection(this);
                    updateSelectAllButton();
                });
            });

            function updatePlayerSelection(checkbox) {
                const playerItem = checkbox.closest('.player-item');
                const playerId = checkbox.value;
                const playerName = playerItem.querySelector('.fw-medium').textContent;
                const playerInitials = playerName.split(' ').map(n => n[0]).join('').toUpperCase();

                if (checkbox.checked) {
                    selectedPlayers.set(playerId, {
                        id: playerId,
                        name: playerName,
                        initials: playerInitials
                    });
                    playerItem.classList.add('selected');
                } else {
                    selectedPlayers.delete(playerId);
                    playerItem.classList.remove('selected');
                }

                updateSelectedPlayersDisplay();
            }

            function updateSelectedPlayersDisplay() {
                modalSelectedPlayers.innerHTML = '';

                selectedPlayers.forEach((player, playerId) => {
                    const playerChip = document.createElement('div');
                    playerChip.className =
                    'form-select w-auto d-flex gap-1 align-items-center h-42 m-w-200';
                    playerChip.innerHTML = `
                <div class="chip-badge">${player.initials}</div>
                <div>${player.name}</div>
                <button type="button" class="btn-close btn-close-white" style="font-size: 10px;" onclick="removePlayerFromModal('${playerId}')"></button>
            `;
                    modalSelectedPlayers.appendChild(playerChip);
                });
            }

            // Remove player from selection
            window.removePlayerFromModal = function(playerId) {
                const checkbox = document.getElementById(`modal_player_${playerId}`);
                if (checkbox) {
                    checkbox.checked = false;
                    updatePlayerSelection(checkbox);
                }
            };

            // Update select all button
            function updateSelectAllButton() {
                const visibleItems = Array.from(document.querySelectorAll('.player-item')).filter(item => item.style
                    .display !== 'none');
                const checkedItems = visibleItems.filter(item => item.querySelector('input[type="checkbox"]')
                    .checked);

                if (checkedItems.length === 0) {
                    modalSelectAll.textContent = 'Select All';
                    modalSelectAll.classList.remove('btn-warning');
                    modalSelectAll.classList.add('btn-outline-secondary');
                } else if (checkedItems.length === visibleItems.length) {
                    modalSelectAll.textContent = 'Deselect All';
                    modalSelectAll.classList.remove('btn-outline-secondary');
                    modalSelectAll.classList.add('btn-warning');
                } else {
                    modalSelectAll.textContent = `Select All (${checkedItems.length}/${visibleItems.length})`;
                    modalSelectAll.classList.remove('btn-warning');
                    modalSelectAll.classList.add('btn-outline-secondary');
                }
            }

            // Visibility buttons
            document.querySelectorAll('.visibility-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const value = this.dataset.value;

                    // Update radio button
                    document.getElementById(`modal_visibility_${value}`).checked = true;

                    // Update button styles - remove active class from all, add to clicked one
                    document.querySelectorAll('.visibility-btn').forEach(b => {
                        b.classList.remove('popup-btn-active');
                    });

                    this.classList.add('popup-btn-active');
                });
            });

            // Action checkbox buttons
            document.querySelectorAll('.action-checkbox').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const checkboxId = this.dataset.checkbox;
                    const checkbox = document.getElementById(checkboxId);

                    // Toggle checkbox
                    checkbox.checked = !checkbox.checked;

                    // Toggle button style
                    if (checkbox.checked) {
                        this.classList.add('popup-btn-active');
                    } else {
                        this.classList.remove('popup-btn-active');
                    }
                });
            });

            // Custom image upload
            document.getElementById('modalFileInput').addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const previewImg = document.getElementById('modalPreviewImg');
                        const imagePreview = document.getElementById('modalImagePreview');
                        const uploadPlaceholder = document.getElementById('modalUploadPlaceholder');

                        previewImg.src = e.target.result;
                        previewImg.alt = 'Custom Award Image';
                        imagePreview.style.display = 'block';
                        uploadPlaceholder.style.display = 'none';
                    };

                    reader.readAsDataURL(file);
                }
            });

            // Remove image button (only if element exists)
            const modalRemoveImageBtn = document.getElementById('modalRemoveImage');
            if (modalRemoveImageBtn) {
                modalRemoveImageBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    clearAwardImage();
                });
            }

            // Save draft functionality
            function saveDraft() {
                const formData = new FormData(awardAssignmentForm);
                // In a real app, you'd save the form data as a draft
                alert('Draft saved! (This is a placeholder)');
            }

            modalSaveDraft.addEventListener('click', saveDraft);
            modalSaveDraftDesktop.addEventListener('click', saveDraft);

            // Form validation
            awardAssignmentForm.addEventListener('submit', function(e) {
                if (selectedPlayers.size === 0) {
                    e.preventDefault();
                    alert('Please select at least one player to assign the award to.');
                    return false;
                }

                if (!modalAwardSelect.value) {
                    e.preventDefault();
                    alert('Please select an award to assign.');
                    return false;
                }
            });

            // Initialize
            updateSelectAllButton();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatItems = document.querySelectorAll('.chat-container .chat-item[data-user-id]');

            chatItems.forEach((item) => {
                const userId = item.getAttribute('data-user-id');
                if (!userId) {
                    item.classList.add('disabled');
                    return;
                }

                item.addEventListener('click', () => {
                    item.classList.add('chat-loading');
                    fetch(`/player/chat/initiate/${userId}`)
                        .then((response) => response.json())
                        .then((data) => {
                            if (!data?.chat_id) {
                                throw new Error(data?.error || 'Unable to open chat.');
                            }
                            window.location.href = `/player/chat?chat_id=${data.chat_id}`;
                        })
                        .catch((error) => {
                            console.warn(error);
                            window.alert(error?.message || 'Unable to open chat. Please try again.');
                        })
                        .finally(() => {
                            item.classList.remove('chat-loading');
                        });
                });
            });
        });
    </script>

    @include('coach.partials.task-assignment-modal')
@endsection
