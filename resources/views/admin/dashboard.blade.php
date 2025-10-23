@extends('layouts.admin')

@section('title', 'Dashboard')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
@endpush
@section('content')



    <style>
        /* Social Links Styles */
        .social-icon {
            display: inline-block;
            margin: 0 5px;
            transition: transform 0.2s ease;
        }

        .social-icon:hover {
            transform: scale(1.1);
        }

        .social-icon a {
            color: inherit;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .social-icon a:hover {
            color: inherit;
            text-decoration: none;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .social-icon.facebook a:hover {
            background-color: #1877f2;
            color: white;
        }

        .social-icon.instagram a:hover {
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
            color: white;
        }

        .social-icon.twitter a:hover {
            background-color: #1da1f2;
            color: white;
        }

        .social-icon.linkedin a:hover {
            background-color: #0077b5;
            color: white;
        }

        .social-icon.youtube a:hover {
            background-color: #ff0000;
            color: white;
        }

        .social-icon.tiktok a:hover {
            background-color: #000000;
            color: white;
        }

        .social-icon.pinterest a:hover {
            background-color: #bd081c;
            color: white;
        }

        .social-icon.snapchat a:hover {
            background-color: #fffc00;
            color: black;
        }

        .social-icon.reddit a:hover {
            background-color: #ff4500;
            color: white;
        }

        /* Chat roster styles */
        .chat-container .chat-list {
            max-height: 360px;
            overflow-y: auto;
        }

        .chat-container .chat-group-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin: 16px 0 8px;
        }

        .chat-container .chat-group-count {
            background: rgba(13, 110, 253, 0.12);
            color: #0d6efd;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 11px;
        }

        .chat-container .chat-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.7);
        }

        .chat-container .chat-item:last-child {
            border-bottom: none;
        }

        .chat-container .chat-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            overflow: hidden;
            background: #eef2ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            color: #4f46e5;
        }

        .chat-container .chat-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .chat-container .chat-info .name {
            display: block;
            font-weight: 600;
            color: #111827;
        }

        .chat-container .chat-info .meta {
            display: block;
            font-size: 12px;
            color: #6c757d;
        }

        .chat-container .chat-info .status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            margin-top: 2px;
            color: #6c757d;
        }

        .chat-container .chat-info .status.online {
            color: #28a745;
        }

        .chat-container .chat-info .status.offline {
            color: #6c757d;
        }

        .chat-container .chat-item .arrow {
            margin-left: auto;
            color: #9ca3af;
            font-size: 18px;
        }

        .chat-container .chat-item[data-user-id]:not([data-user-id='']) {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .chat-container .chat-item[data-user-id]:not([data-user-id='']):hover {
            background: rgba(13, 110, 253, 0.12);
        }

        .chat-container .chat-item.disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .chat-container .chat-empty {
            text-align: center;
            padding: 24px 12px;
            border: 1px dashed #d1d9e6;
            border-radius: 12px;
            color: #6c757d;
            background: rgba(248, 249, 252, 0.8);
        }

        .chat-container .chat-empty .small {
            font-size: 12px;
        }

        #clubChatMessages {
            max-height: 420px;
            overflow-y: auto;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        .club-chat-bubble {
            max-width: 75%;
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 6px;
        }

        .video-card .video-info-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: #0f172a;
            border-top: 1px solid rgba(148, 163, 184, 0.2);
        }

        .video-card .video-info-bar .btn {
            border-radius: 999px;
            padding: 6px 18px;
            font-weight: 600;
        }

        .video-card .video-meta {
            padding: 18px 20px 8px;
            background: #0b1222;
            color: #e2e8f0;
        }

        .video-card .video-meta h4 {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .video-card .video-meta p {
            margin-bottom: 0;
            color: rgba(226, 232, 240, 0.75);
        }

        .video-card .video-playlist {
            display: flex;
            gap: 16px;
            padding: 16px;
            background: #060b18;
            overflow-x: auto;
        }

        .video-card .playlist-item {
            display: flex;
            gap: 12px;
            min-width: 220px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 12px;
            padding: 12px;
            color: #f8fafc;
            text-decoration: none;
            transition: transform 0.2s ease;
        }

        .video-card .playlist-item:hover {
            transform: translateY(-2px);
            text-decoration: none;
        }

        .video-card .playlist-item img {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            object-fit: cover;
        }

        .video-card .playlist-title {
            font-weight: 600;
            font-size: 14px;
            color: #f8fafc;
        }

        .video-card .playlist-meta {
            font-size: 12px;
            color: rgba(226, 232, 240, 0.7);
        }

        .video-card .video-empty {
            padding: 24px;
            text-align: center;
            background: #0b1222;
            color: #94a3b8;
        }

        .club-chat-bubble.agent {
            background: linear-gradient(135deg, #2563eb, #19a6ff);
            color: #fff;
            margin-left: auto;
        }

        .club-chat-bubble.contact {
            background: rgba(255, 255, 255, 0.08);
            color: #f8fafc;
            margin-right: auto;
        }

        .club-chat-meta {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
        }

        .btn-gradient {
            background: linear-gradient(120deg, #38bdf8, #818cf8);
            color: #0f172a;
            border: none;
            border-radius: 12px;
        }

        .btn-gradient:hover,
        .btn-gradient:focus {
            color: #0f172a;
            box-shadow: 0 0 0 0.2rem rgba(129, 140, 248, 0.25);
        }

        .chat-filter-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }

        .chat-filter-buttons button {
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(15, 23, 42, 0.6);
            color: rgba(226, 232, 240, 0.8);
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .chat-filter-buttons button.active {
            background: linear-gradient(120deg, #38bdf8, #8b5cf6);
            color: #0f172a;
            border-color: transparent;
        }

        .chat-filter-buttons .count-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 24px;
            border-radius: 12px;
            background: rgba(15, 23, 42, 0.35);
            color: inherit;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.status-closed {
            background: rgba(255, 255, 255, 0.12) !important;
            color: #f8f9fa !important;
        }

        .status-badge.disabled,
        .status-badge[aria-disabled='true'] {
            opacity: 0.6;
            pointer-events: none;
            cursor: default;
        }

        a.status-badge {
            text-decoration: none;
            display: inline-block;
        }

        /* Small reward chip styles */
        .small-reward-chip {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-block;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .small-reward-chip:hover {
            transform: scale(1.1);
            box-shadow: 0 0 8px rgba(0, 212, 170, 0.4);
            border-color: rgba(0, 212, 170, 0.6);
        }

        .small-reward-chip:active {
            transform: scale(0.95);
        }

        /* Fallback for rewards without images */
        .small-reward-chip:not([style*="background-image"]) {
            background: linear-gradient(45deg, #666, #999) !important;
        }

        ..weather-widget {
            height: 100px;
        }

        .modal-content {
            padding: 0 !important;
        }

        body.light .modal-body.card {
            border: none !important;
        }

        .light .player-attr-card {
            border: none !important;
            background-color: #f1f1f1 !important;
        }

        .theme-btn {
            background: linear-gradient(90deg, #7c3aed 0%, #ec4899 50%, #f59e0b 100%);
            border: none;
            border-radius: 6px;
            font-size: 10px;
            color: #ffffff;
            padding: 6px 16px;
            border-radius: none !important;
        }

        .left-column {
            justify-content: start !important;
        }
    </style>

    @include('admin.modals.admin-create-tournament', [
        'clubTeams' => $clubTeams ?? collect(),
    ])

    @include('admin.modals.to-do-task')
    @include('admin.modals.frontBlogPosts')
    @include('admin.modals.createNewAward')
    @include('admin.modals.createBlogPostModel')
    @include('admin.modals.assigntoCalendar')
    @include('admin.modals.admin-blog-post', [
        'clubTeams' => $clubTeams ?? collect(),
    ])




    <div class="cards_css main_cards_css">
        @include('partials.dashboard_stat_cards')
    </div>
    <div class="row main_cards_css">
        <div class="col-lg-8">
            <div class="cards_css">
                @include('partials.dashboard_quick_actions')
            </div>
            <div class="cards_css">
                @include('partials.dashboard_onboarding')
            </div>
            <div class="cards_css">
                @include('partials.dashboard_calendar')
            </div>
            <div class="cards_css">
                @include('partials.tournment_section')
            </div>

            <div class="cards_css">
                @include('partials.tournament_table_section')
            </div>
            <div class="cards_css">
                @include('partials.video_section')
            </div>
        </div>
        <div class="col-lg-4">
            <div>
                @include('partials.dashboard_side_chat_panel')
            </div>
            <div>
                @include('partials.dashboard_side_tournament_all_club_chat')
            </div>
            <div>
                @include('partials.dashboard_active_tournament')
            </div>
            <div>
                @include('partials.dashboard_assign_task_reminder')
            </div>
        </div>
    </div>








@endsection



@section('footer_scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="{{'/assets/js/pages/widgets/widget.js'}}" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        if (calendarEl) {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem: 'bootstrap5',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                events: {!! json_encode($events) !!},
                eventClick: function (info) {
                    info.jsEvent.preventDefault();
                    if (info.event.url) {
                        window.open(info.event.url, '_blank');
                    }
                }
            });

            calendar.render();
        }
    });
</script>
<script>
    $(function () {
        $(".dial").knob();
    });
</script>
@endsection
