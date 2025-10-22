@extends('layouts.referee-dashboard')

@section('title', 'Referee Dashboard')
@section('page_title', 'Dashboard')

@section('header_styles')
    @parent
  
@endsection

@section('content')
    @php
        $trendIcons = [
            'up' => asset('assets/club-dashboard-main/assets/ic_trending_up.svg'),
            'down' => asset('assets/club-dashboard-main/assets/ic_trending_down.svg'),
        ];

        $statImages = [
            'green' => asset('assets/club-dashboard-main/assets/bars.png'),
            'orange' => asset('assets/club-dashboard-main/assets/graph.png'),
            'blue' => asset('assets/club-dashboard-main/assets/pie.png'),
            'purple' => asset('assets/club-dashboard-main/assets/bars.png'),
        ];

        $actionVariantMap = [
            'primary' => 'green',
            'emerald' => 'teal',
            'cyan' => 'cyan',
            'red' => 'red',
            'amber' => 'orange',
            'violet' => 'purple',
            'fuchsia' => 'purple',
            'rose' => 'red',
        ];

        $statusBadgeMap = [
            'Active' => 'status-open',
            'Upcoming' => 'status-register',
            'Completed' => 'status-waitlist',
        ];

        $progressThemes = [
            ['circle' => 'green', 'bar' => '#22c55e'],
            ['circle' => 'orange', 'bar' => '#f97316'],
            ['circle' => 'blue', 'bar' => '#3b82f6'],
            ['circle' => 'purple', 'bar' => '#8b5cf6'],
        ];

        $videoSlidesCollection = collect($videoSlides ?? []);
        $defaultVideoThumbnail = asset('assets/player-dashboard/images/video-thumbnail.png');
        $initialSlide = $videoSlidesCollection->first();
        $initialCover = data_get($initialSlide, 'thumbnail', $defaultVideoThumbnail);
        $videoSlidesCount = $videoSlidesCollection->count();

        $refereeSocialLinksData = [
            [
                'type' => 'image',
                'src' => asset('assets/club-dashboard-main/assets/paypal.svg'),
                'alt' => 'PayPal',
                'width' => 28,
                'height' => 28,
            ],
        ];

        foreach (
            [
                'facebook-f',
                'instagram',
                'tiktok',
                'pinterest',
                'linkedin-in',
                'snapchat-ghost',
                'twitter',
                'reddit',
                'youtube',
            ]
            as $icon
        ) {
            $refereeSocialLinksData[] = [
                'type' => 'icon',
                'icon' => $icon,
                'class' => $icon,
            ];
        }

        $refereeEntityMedia = [
            'image' => asset('assets/club-dashboard-main/assets/football.png'),
            'alt' => 'Sport ball',
        ];

        $defaultStatImage = $statImages['green'] ?? null;
        $refereeStatCards = [];

        foreach ($summaryCards as $card) {
            $trend = $card['trend'] ?? null;
            $direction = $trend['direction'] ?? 'up';
            $percentText = null;

            if ($trend && array_key_exists('percent', $trend) && $trend['percent'] !== null) {
                $percentValue = rtrim(rtrim(number_format($trend['percent'], 1), '0'), '.');
                $prefix = $direction === 'down' ? '-' : '+';
                $percentText = $prefix . $percentValue . '%';
            }

            $description = $card['description'] ?? '';
            $footerText = trim(($percentText ? $percentText . ' • ' : '') . $description);

            $color = $card['color'] ?? 'green';
            $value = $card['value'] ?? '0';
            if (is_numeric($value)) {
                $value = number_format($value);
            }

            $refereeStatCards[] = [
                'color' => $color,
                'class' => 'd-flex flex-column align-items-center mb-4 text-center p-3',
                'image' => $statImages[$color] ?? $defaultStatImage,
                'image_wrapper_class' => 'w-75',
                'image_class' => 'stat-visual',
                'badge' => $card['label'] ?? '',
                'badge_class' => 'mt-2 mb-2 px-2 py-1 w-75 text-truncate fs-12px',
                'value' => $value,
                'format' => 'raw',
                'value_class' => 'fs-24 fw-bold d-flex gap-2 align-items-center',
                'trend_icon' => $trend ? $trendIcons[$direction] ?? $trendIcons['up'] : null,
                'trend_direction' => $direction,
                'trend_class' => 'fs-12px mb-2 text-nowrap',
                'footer' => $footerText,
            ];
        }
    @endphp

    <div class="referee-club-wrapper">
        @include('partials.dashboard_weather_widget', [
            'entity' => $refereeEntityMedia,
            'weather' => $weather ?? null,
            'locationFallback' => $locationLabel,
            'socialLinks' => $refereeSocialLinksData,
            'fallbackTemperature' => '—°',
            'wrapperClass' => 'weather-widget',
            'socialWrapperClass' => 'social-icons justify-content-center',
        ])

        @include('partials.dashboard_stat_card_grid', [
            'cards' => $refereeStatCards,
            'rowClass' => '',
            'colClasses' => 'col-sm-6 col-md-6 col-lg-3',
        ])
        <div class="metric-widget gray-card p-4 pb-0 mb-4">
            <div class="section-header mb-3 d-flex justify-content-between align-items-center">
                <h2 class="section-title mb-0">Performance Overview</h2>
                <span class="subtitle fw-semibold">{{ $availabilitySummary['days'] }} active days</span>
            </div>
            <div class="metrics-row">
                @foreach ($insightCards as $index => $card)
                    @php
                        $theme = $progressThemes[$index % count($progressThemes)];
                        $trend = $card['trend'] ?? null;
                        $percentValue = $trend['percent'] ?? null;
                        if ($percentValue === null && is_numeric($card['value'])) {
                            $percentValue = (float) $card['value'];
                        }
                        $percentValue = $percentValue !== null ? (float) $percentValue : 50.0;
                        $percentValue = max(0, min(100, $percentValue));
                        $percentDisplay = number_format($percentValue, 0);
                    @endphp
                    <div class="metric-card">
                        <div class="progress-circle {{ $theme['circle'] }}">
                            {{ $percentDisplay }}%
                        </div>
                        <div class="metric-text">
                            <h3 class="metric-title">{{ $card['label'] }}</h3>
                            <div class="progress-xy my-2" style="height: 5px;">
                                <div class="progress-bar"
                                    style="width: {{ $percentValue }}%; background: {{ $theme['bar'] }};"></div>
                            </div>
                            <div class="metric-subtitle">
                                <strong>{{ $card['value'] }}</strong>
                                {{ $card['description'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
                @if (!empty($availabilitySummary['next_available']))
                    <div class="metric-card">
                        <div class="progress-circle purple">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="metric-text">
                            <h3 class="metric-title">Next Available Slot</h3>
                            <div class="metric-subtitle">
                                <strong>{{ $availabilitySummary['next_available']->timezone(config('app.timezone'))->format('D g:i A') }}</strong>
                                Plan your next assignment window.
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>


        <div class="two-column">
            <div class="left-column">
                <div class="action-widget p-4 mb-0">
                    <div class="section-header">
                        <h2 class="section-title">Quick Actions</h2>
                        <button class="section-menu"><i class="fas fa-ellipsis-h"></i></button>
                    </div>
                    <div class="d-flex flex-wrap gap-3 w-100">
                        @foreach ($quickActions as $action)
                            @php
                                $variant = $actionVariantMap[$action['variant']] ?? 'blue';
                                $isDisabled = $action['disabled'] ?? false;
                            @endphp
                            <a href="{{ $isDisabled ? '#' : $action['url'] }}"
                                class="action-btn {{ $variant }} {{ $isDisabled ? 'disabled' : '' }}">
                                <i class="fa-solid {{ $action['icon'] }}"></i> {{ $action['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>



                <div class="gray-card p-4">
                    <div class="section-header mb-3 d-flex justify-content-between align-items-center">
                        <h2 class="section-title mb-0">Match Calendar</h2>
                        <span class="subtitle fw-semibold">
                            Assigned & Pending
                        </span>
                    </div>
                    <div class="calendar-container">
                        <div id="calendar"></div>
                    </div>
                </div>


            </div>

            <div class="right-column">


                <div class="chat-container">
                    <div class="chat-header">
                        <h3>Referee Network</h3>
                        <button class="menu-btn">⋯</button>
                    </div>
                    <div class="chat-search">
                        <input type="text" placeholder="Search referees">
                    </div>
                    <div class="chat-list">
                        @forelse($chatContacts as $contact)
                            @php
                                $initials = collect(explode(' ', $contact['name']))
                                    ->filter()
                                    ->map(fn($segment) => strtoupper(substr($segment, 0, 1)))
                                    ->take(2)
                                    ->implode('');
                            @endphp
                            <div class="chat-item" data-contact-name="{{ $contact['name'] }}"
                                data-contact-role="{{ $contact['role'] ?? '' }}">
                                <div class="chat-avatar">
                                    {{ $initials ?: 'RC' }}
                                </div>
                                <div class="chat-info">
                                    <span class="name">{{ $contact['name'] }}</span>
                                    @if (!empty($contact['role']))
                                        <span class="meta">{{ $contact['role'] }}</span>
                                    @endif
                                    @if (!empty($contact['meta']))
                                        <span class="meta">{{ $contact['meta'] }}</span>
                                    @endif
                                    <span class="status {{ $contact['is_online'] ? 'online' : 'offline' }}">
                                        <i class="fa-solid fa-circle"></i>
                                        {{ $contact['is_online'] ? 'Online' : 'Offline' }}
                                    </span>
                                    <span class="status">
                                        {{ $contact['last_activity'] ? ucfirst($contact['last_activity']) : 'No recent activity' }}
                                    </span>
                                </div>
                                <div class="arrow">›</div>
                            </div>
                        @empty
                            <div class="chat-empty">
                                <p class="mb-1">No referees to display</p>
                                <p class="small">Claim or apply to matches to grow your network.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if (!empty($tournamentHighlights['next']))
                    @php
                        $nextHighlight = $tournamentHighlights['next'];
                    @endphp
                    <div class="gradient-card p-3 rounded mt-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center text-white gap-3">
                                <img src="{{ asset('assets/club-dashboard-main/assets/achivment.svg') }}" alt="Tournament"
                                    width="36" height="36">
                                <div>
                                    <span class="fw-semibold fst-italic">{{ $nextHighlight['name'] }}</span>
                                    <div class="small text-white-50">
                                        Starts {{ $nextHighlight['start'] ?? 'soon' }}
                                        @if (!empty($nextHighlight['sport']))
                                            • {{ $nextHighlight['sport'] }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('club.tournaments.index') }}" class="start-btn">View</a>
                        </div>
                    </div>
                @endif

                <div class="chat-card p-3 rounded mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-white mb-0">Tournament Chat All Clubs</h5>
                        <i class="fas fa-ellipsis-h text-white"></i>
                    </div>
                    <div class="mb-2">
                        <div class="input-group d-flex align-items-center px-2 py-1">
                            <input type="text" class="form-control border-0 text-white p-0 bg-transparent"
                                placeholder="Search tournaments">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <p class="note-text small mb-3">
                        Note: Chat rooms connect referees assigned or applying to the same tournament.
                    </p>
                    <div class="chat-list">
                        @forelse($tournamentChats as $room)
                            @php
                                $isActive = ($room['status'] ?? '') === 'Assigned';
                                $statusClass = match ($room['status']) {
                                    'Assigned' => 'text-success',
                                    'Completed' => 'text-muted',
                                    'Pending' => 'text-warning',
                                    default => 'text-info',
                                };
                            @endphp
                            <div class="chat-item d-flex align-items-center py-2">
                                <div class="chat-avatar me-3">
                                    {{ strtoupper(substr($room['title'], 0, 2)) }}
                                </div>
                                <div class="flex-grow-1 user-name">
                                    <div class="text-white">{{ $room['title'] }}</div>
                                    <small class="d-block text-white-50">{{ $room['subtitle'] }}</small>
                                    <small class="{{ $statusClass }}">
                                        <i class="fa-solid fa-circle me-1"></i>{{ $room['status'] }}
                                    </small>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        @empty
                            <div class="chat-empty text-center py-3">
                                <p class="mb-1">No tournament conversations yet.</p>
                                <p class="small mb-0">Apply or claim a match to unlock chat rooms.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if ($taskReminders->isNotEmpty())
                    <div class="task-card mt-4">
                        <div class="task-header">
                            <h5>Assign Task / Reminders</h5>
                            <i class="fas fa-ellipsis-h"></i>
                        </div>
                        <div class="task-table">
                            <div class="task-row task-head">
                                <div>User</div>
                                <div>Task</div>
                                <div>Status</div>
                                <div>Manager</div>
                                <div>Progress</div>
                            </div>
                            @foreach ($taskReminders as $task)
                                <div class="task-row">
                                    <div>
                                        <img src="{{ asset('assets/club-dashboard-main/assets/user.png') }}"
                                            alt="User">
                                    </div>
                                    <div>
                                        <span class="d-block text-white">{{ $task['title'] }}</span>
                                        <small class="text-white-50">{{ $task['task'] }}</small>
                                    </div>
                                    <div>
                                        <span class="badge {{ $task['badge'] }}">{{ $task['status'] }}</span>
                                    </div>
                                    <div>{{ $task['manager'] }}</div>
                                    <div class="progress-bar">
                                        <span
                                            style="width: {{ $task['progress'] }}%; background: {{ $task['color'] }}"></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="tournament-card p-4 mb-4">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-2">
                <div class="col-md-auto">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('assets/club-dashboard-main/assets/tr.png') }}" alt="stadium"
                            class="tournament-img" />
                        <h3 class="tournament-title">Tournament Directory</h3>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center align-items-center">
                    <div class="subtitle fw-semibold text-white mb-0">
                        Tournament Engine
                    </div>
                    <a href="{{ route('club.tournaments.index') }}" class="start-btn">Start</a>
                </div>
            </div>
            <div class="card-desc">
                Search and register for tournaments by location, date, sport, division, age, and more.
            </div>
        </div>


        <div class="upcoming-games-card mb-4">
            <div class="section-header mb-2 d-flex justify-content-between align-items-center">
                <h2 class="section-title mb-0">Upcoming Games</h2>
                <span class="subtitle fw-semibold">{{ $assignedMatches->count() }} scheduled</span>
            </div>
            <div class="games-filter-chips">
                <span class="games-chip">First-come, first-serve</span>
                <span class="games-chip">Claims <strong>{{ $applicationStats['pending'] ?? 0 }}</strong></span>
                <span class="games-chip">Open shifts <strong>{{ $applicationStats['accepted'] ?? 0 }}</strong></span>
                <span class="games-chip">Ratings ★★★★★</span>
                <span class="games-chip">Game claimed. This slot is held.</span>
            </div>
            <div class="table-responsive games-table">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Match</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignedMatches as $match)
                            @php
                                $matchDate =
                                    $match->match_date instanceof \Carbon\Carbon
                                        ? $match->match_date
                                        : \Carbon\Carbon::parse($match->match_date);
                                try {
                                    $matchTime = $match->match_time
                                        ? \Carbon\Carbon::parse($match->match_time)->format('g:i A')
                                        : 'TBD';
                                } catch (\Exception $e) {
                                    $matchTime = $match->match_time ?: 'TBD';
                                }
                                $teams = trim(($match->homeClub->name ?? 'TBD') . ' vs ' . ($match->awayClub->name ?? 'TBD'));
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $matchDate->format('D, M j') }}</strong>
                                    <div class="text-muted small">{{ $matchTime }}</div>
                                </td>
                                <td>
                                    <strong>{{ $teams }}</strong>
                                    <div class="text-muted small">
                                        {{ optional($match->tournament)->name ?? 'Independent Match' }}
                                    </div>
                                </td>
                                <td>{{ $match->venue ?? 'TBD' }}</td>
                                <td>
                                    <span class="game-status-pill available">
                                        <i class="fa-solid fa-check-circle"></i> Assigned
                                    </span>
                                </td>
                                <td>
                                    <div class="game-action-links">
                                        <a href="{{ route('referee.matches.view', $match) }}" class="chat-src-btn">View</a>
                                        <form method="POST" action="{{ route('referee.matches.cancel', $match) }}"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="chat-clr-btn"
                                                onclick="return confirm('Cancel this match assignment?');">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    No assigned matches yet. Visit the tournament engine to apply.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="games-footnotes">
                <div>• Optional fairness rules apply: daily claim limit, early preview for high-ref needs, waitlist, certification requirements, and cancellation codes.</div>
                <div>• Claimed games disappear from other referees’ dashboards in real time.</div>
            </div>
        </div>

        <div class="row g-4 align-items-stretch mb-4">
            <div class="col-lg-5">
                <div class="gray-card p-4 h-100">
                    <div class="section-header mb-3 d-flex justify-content-between align-items-center">
                        <h2 class="section-title mb-0">Spotlight Videos</h2>
                        <a href="{{ $videosExploreUrl }}" class="chat-src-btn">View All</a>
                    </div>

                    @if ($videoSlidesCollection->isNotEmpty())
                        <div class="player-video-slider" data-video-slider data-redirect-base="{{ $videosExploreUrl }}"
                            data-videos='@json($videoSlidesCollection, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'>
                            <div class="player-video-stage" data-slider-stage role="button" tabindex="0"
                                aria-label="Open video spotlight">
                                <div class="player-video-cover" data-slider-cover
                                    style="--player-video-cover: url('{{ e($initialCover) }}'); background-image: url('{{ e($initialCover) }}');">
                                    <video data-slider-video preload="metadata" muted playsinline
                                        poster="{{ e($initialCover) }}"></video>
                                    <img src="{{ e($initialCover) }}" alt="Video thumbnail" data-slider-cover-img>
                                </div>
                                <span class="player-video-stage-label">
                                    <i class="fa-solid fa-play" aria-hidden="true"></i> Watch in Videos
                                </span>
                            </div>
                            <div class="player-video-meta">
                                <h4 data-slider-title>{{ data_get($initialSlide, 'title', 'Spotlight Video') }}</h4>
                                <div class="meta-line">
                                    <span data-slider-user>{{ data_get($initialSlide, 'user', 'Play2Earn') }}</span>
                                    <span class="mx-1" data-slider-separator>•</span>
                                    <span data-slider-time>{{ data_get($initialSlide, 'time', '') }}</span>
                                </div>
                                <p data-slider-description class="mb-0">{{ data_get($initialSlide, 'description', '') }}
                                </p>
                            </div>
                            <div class="player-video-controls">
                                <div class="player-video-dots" data-slider-dots></div>
                                <div class="player-video-nav">
                                    <button type="button" data-slider-prev aria-label="Previous video"><span
                                            aria-hidden="true">&#8249;</span></button>
                                    <button type="button" data-slider-next aria-label="Next video"><span
                                            aria-hidden="true">&#8250;</span></button>
                                </div>
                            </div>
                            <div class="player-video-indicator">
                                <span data-slider-indicator>1 / {{ $videoSlidesCount }}</span>
                                <span class="text-uppercase small">Click the preview to open the full video page</span>
                            </div>
                        </div>
                    @else
                        <div class="player-video-slider text-center text-white-50 h-100 d-flex align-items-center justify-content-center">
                            No spotlight videos yet. Upload your first highlight to showcase your officiating.
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-7">
                <div class="claimed-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="mb-1">Claimed Assignment Summary</h4>
                            <p class="mb-0 text-muted small">We’ve held this slot for you. Next, notify the assignor/league.</p>
                        </div>
                        <span class="game-status-pill available"><i class="fa-solid fa-check"></i> Claimed</span>
                    </div>
                    <div class="claimed-row">
                        <div class="claimed-label">Current Assignment</div>
                        <div class="claimed-value">
                            @if ($claimedAssignment)
                                {{ optional($claimedAssignment->tournament)->name ?? 'Independent Match' }}
                            @else
                                No claimed assignments yet
                            @endif
                        </div>
                    </div>
                    @if ($claimedAssignment)
                        @php
                            $claimedDate =
                                $claimedAssignment->match_date instanceof \Carbon\Carbon
                                    ? $claimedAssignment->match_date
                                    : \Carbon\Carbon::parse($claimedAssignment->match_date);
                            try {
                                $claimedTime = $claimedAssignment->match_time
                                    ? \Carbon\Carbon::parse($claimedAssignment->match_time)->format('g:i A')
                                    : 'TBD';
                            } catch (\Exception $e) {
                                $claimedTime = $claimedAssignment->match_time ?: 'TBD';
                            }
                        @endphp
                        <div class="claimed-row">
                            <div class="claimed-label">Match</div>
                            <div class="claimed-value">
                                {{ $claimedAssignment->homeClub->name ?? 'TBD' }} vs
                                {{ $claimedAssignment->awayClub->name ?? 'TBD' }}
                                <div class="text-muted small">
                                    {{ $claimedDate->format('D, M j') }} • {{ $claimedTime }}
                                </div>
                            </div>
                        </div>
                        <div class="claimed-row">
                            <div class="claimed-label">Venue</div>
                            <div class="claimed-value">{{ $claimedAssignment->venue ?? 'TBD' }}</div>
                        </div>
                    @endif
                    <div class="claimed-row">
                        <div class="claimed-label">Applications</div>
                        <div class="claimed-value">
                            Accepted: {{ $applicationStats['accepted'] }} • Pending: {{ $applicationStats['pending'] }} •
                            Total: {{ $applicationStats['total'] }}
                        </div>
                    </div>
                    <div class="claimed-actions">
                        <a href="{{ route('referee.email') }}" class="btn btn-secondary">Send Email</a>
                        <a href="{{ route('referee.matches.available') }}" class="btn btn-warning">Push to Platform</a>
                        <a href="{{ route('referee.matches.available') }}" class="btn btn-primary">Find New Match</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    @parent
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek'
                    },
                    height: 'auto',
                    events: @json($calendarEvents),
                    eventClick: function(info) {
                        if (info.event.url) {
                            info.jsEvent.preventDefault();
                            window.location.href = info.event.url;
                        }
                    },
                    eventDidMount: function(info) {
                        const props = info.event.extendedProps || {};
                        const tooltip = [
                            props.tournament ? 'Tournament: ' + props.tournament : null,
                            props.venue ? 'Venue: ' + props.venue : null,
                            props.status ? 'Status: ' + props.status : null
                        ].filter(Boolean).join('\n');

                        if (tooltip) {
                            info.el.setAttribute('title', info.event.title + '\n' + tooltip);
                        }
                    }
                });

                calendar.render();
            }

            const buildVideoUrl = (base, id) => {
                const cleanBase = (base || '/player/videos/explore').replace(/\/$/, '');
                return id ? `${cleanBase}/${id}` : cleanBase;
            };

            document.querySelectorAll('[data-video-slider]').forEach((slider) => {
                let slides = [];
                try {
                    const raw = slider.dataset.videos || '[]';
                    slides = JSON.parse(raw);
                } catch (error) {
                    console.warn('Unable to parse video slider data', error);
                    slides = [];
                }

                if (!Array.isArray(slides) || !slides.length) {
                    return;
                }

                const stage = slider.querySelector('[data-slider-stage]');
                const cover = slider.querySelector('[data-slider-cover]');
                const coverImg = slider.querySelector('[data-slider-cover-img]');
                const coverVideo = slider.querySelector('[data-slider-video]');
                const titleEl = slider.querySelector('[data-slider-title]');
                const userEl = slider.querySelector('[data-slider-user]');
                const timeEl = slider.querySelector('[data-slider-time]');
                const separatorEl = slider.querySelector('[data-slider-separator]');
                const descriptionEl = slider.querySelector('[data-slider-description]');
                const indicatorEl = slider.querySelector('[data-slider-indicator]');
                const dotsWrap = slider.querySelector('[data-slider-dots]');
                const prevBtn = slider.querySelector('[data-slider-prev]');
                const nextBtn = slider.querySelector('[data-slider-next]');
                const redirectBase = slider.dataset.redirectBase || '/player/videos/explore';

                let index = 0;
                let timerId = null;
                const AUTOPLAY_INTERVAL = 8000;

                const restartAutoplay = () => {
                    if (timerId) {
                        clearTimeout(timerId);
                    }
                    timerId = setTimeout(() => {
                        updateSlide((index + 1) % slides.length);
                    }, AUTOPLAY_INTERVAL);
                };

                const pauseAutoplay = () => {
                    if (timerId) {
                        clearTimeout(timerId);
                        timerId = null;
                    }
                };

                const setCoverImage = (url, preview, previewType) => {
                    const bgValue = url ? `url(${url})` : 'none';
                    if (cover) {
                        const displayBg = preview && previewType === 'file' ? 'none' : bgValue;
                        cover.style.setProperty('--player-video-cover', displayBg);
                        cover.style.backgroundImage = displayBg;
                    }
                    if (coverImg) {
                        coverImg.src = url || '';
                        coverImg.style.display = preview && previewType === 'file' ? 'none' : 'block';
                    }
                    if (coverVideo) {
                        if (preview && previewType === 'file') {
                            coverVideo.src = preview;
                            coverVideo.style.display = 'block';
                            coverVideo.currentTime = 0;
                            const playResult = coverVideo.play();
                            if (playResult && typeof playResult.catch === 'function') {
                                playResult.catch(() => {});
                            }
                        } else {
                            if (typeof coverVideo.pause === 'function') {
                                coverVideo.pause();
                            }
                            coverVideo.removeAttribute('src');
                            coverVideo.style.display = 'none';
                            if (typeof coverVideo.load === 'function') {
                                coverVideo.load();
                            }
                        }
                    }
                };

                const setActiveDot = (idx) => {
                    if (!dotsWrap) {
                        return;
                    }
                    dotsWrap.querySelectorAll('.player-video-dot').forEach((dot, dotIndex) => {
                        dot.classList.toggle('active', dotIndex === idx);
                    });
                };

                const updateSlide = (newIndex) => {
                    if (!slides[newIndex]) {
                        return;
                    }
                    index = newIndex;
                    const slide = slides[newIndex];

                    setCoverImage(slide.thumbnail || '', slide.preview || '', slide.preview_type ||
                        'file');

                    if (titleEl) titleEl.textContent = slide.title || 'Spotlight Video';
                    if (userEl) userEl.textContent = slide.user || 'Play2Earn';
                    if (timeEl) {
                        timeEl.textContent = slide.time || '';
                        if (separatorEl) {
                            separatorEl.style.display = slide.time ? 'inline' : 'none';
                        }
                    }
                    if (descriptionEl) descriptionEl.textContent = slide.description || '';
                    if (indicatorEl) indicatorEl.textContent = `${newIndex + 1} / ${slides.length}`;

                    setActiveDot(newIndex);
                    restartAutoplay();
                };

                if (dotsWrap) {
                    slides.forEach((_, idx) => {
                        const dot = document.createElement('button');
                        dot.type = 'button';
                        dot.className = 'player-video-dot';
                        dot.setAttribute('aria-label', `Show slide ${idx + 1}`);
                        dot.addEventListener('click', () => {
                            pauseAutoplay();
                            updateSlide(idx);
                        });
                        dotsWrap.appendChild(dot);
                    });
                }

                prevBtn?.addEventListener('click', () => {
                    pauseAutoplay();
                    const nextIndex = (index - 1 + slides.length) % slides.length;
                    updateSlide(nextIndex);
                });

                nextBtn?.addEventListener('click', () => {
                    pauseAutoplay();
                    updateSlide((index + 1) % slides.length);
                });

                if (stage) {
                    stage.addEventListener('click', () => {
                        const slide = slides[index];
                        window.location.href = buildVideoUrl(redirectBase, slide?.id);
                    });
                }

                slider.addEventListener('mouseenter', pauseAutoplay);
                slider.addEventListener('mouseleave', restartAutoplay);

                updateSlide(0);
            });
        });
    </script>
@endsection
