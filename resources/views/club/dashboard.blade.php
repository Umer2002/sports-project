@extends('layouts.club-dashboard')

@section('title', 'Club Dashboard')
@section('page_title', 'Dashboard')


    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


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

@include('club.modals.assign-award', [
    'availableAwards' => $availableAwards ?? collect(),
    'clubPlayers' => $clubPlayers ?? collect(),
])
@include('club.modals.create-tournament', [
    'clubTeams' => $clubTeams ?? collect(),
])
@include('club.modals.player-attributes', [
    'clubPlayers' => $clubPlayers ?? collect(),
    'clubTeams' => $clubTeams ?? collect(),
])

@php
    $currentUser = auth()->user();
    $clubModel = $club ?? optional($currentUser)->club;

    $resolveMediaUrl = function ($path) {
        if (!$path) {
            return null;
        }
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        $trimmed = ltrim($path, '/');
        if (\Illuminate\Support\Str::startsWith($trimmed, 'storage/')) {
            return asset($trimmed);
        }
        if (file_exists(public_path($trimmed))) {
            return asset($trimmed);
        }
        return asset('storage/' . $trimmed);
    };

    $clubLogoUrl = $resolveMediaUrl($clubModel ? $clubModel->logo : null);
    $clubSportIconUrl = $resolveMediaUrl(optional($clubModel?->sport)->icon_path ?? null);
    $clubName = $clubModel?->name ?? 'Club';

    $clubEntityMedia = $clubEntityMedia ?? [
        'image' => $clubLogoUrl ?: $clubSportIconUrl,
        'alt' => $clubName,
        'attributes' => $clubLogoUrl || $clubSportIconUrl ? 'style="height:70px;border-radius:50px"' : '',
        'text' =>
            $clubLogoUrl || $clubSportIconUrl
                ? null
                : \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($clubName, 0, 2)),
        'text_class' => 'sport-fallback d-flex align-items-center justify-content-center h-100',
        'fallback_image' => asset('assets/club-dashboard-main/assets/football.png'),
        'fallback_alt' => 'Club',
    ];

    $rawSocialLinks = $socialLinks ?? [];
    if ($rawSocialLinks instanceof \Illuminate\Support\Collection) {
        $rawSocialLinks = $rawSocialLinks->toArray();
    }
    $rawSocialLinks = is_array($rawSocialLinks) ? $rawSocialLinks : [];

    $clubSocialLinksData =
        $clubSocialLinksData ??
        (function () use ($rawSocialLinks, $paypalLink) {
            $links = [];
            if (!empty($paypalLink)) {
                $links[] = [
                    'type' => 'image',
                    'class' => '',
                    'url' => $paypalLink,
                    'src' => asset('assets/club-dashboard-main/assets/paypal.svg'),
                    'alt' => 'PayPal',
                    'label' => 'PayPal',
                    'width' => 28,
                    'height' => 28,
                ];
            }

            $iconMap = [
                'facebook' => ['class' => 'facebook', 'icon' => 'facebook-f', 'label' => 'Facebook'],
                'instagram' => ['class' => 'instagram', 'icon' => 'instagram', 'label' => 'Instagram'],
                'twitter' => ['class' => 'twitter', 'icon' => 'twitter', 'label' => 'Twitter'],
                'linkedin' => ['class' => 'linkedin', 'icon' => 'linkedin-in', 'label' => 'LinkedIn'],
                'youtube' => ['class' => 'youtube', 'icon' => 'youtube', 'label' => 'YouTube'],
                'tiktok' => ['class' => 'tiktok', 'icon' => 'tiktok', 'label' => 'TikTok'],
                'pinterest' => ['class' => 'pinterest', 'icon' => 'pinterest', 'label' => 'Pinterest'],
                'snapchat' => ['class' => 'snapchat', 'icon' => 'snapchat-ghost', 'label' => 'Snapchat'],
                'reddit' => ['class' => 'reddit', 'icon' => 'reddit-alien', 'label' => 'Reddit'],
            ];

            foreach ($iconMap as $key => $meta) {
                $url = $rawSocialLinks[$key] ?? null;
                $links[] = [
                    'class' => trim($meta['class'] . (empty($url) ? ' disabled' : '')),
                    'url' => $url,
                    'icon' => $meta['icon'],
                    'label' => $meta['label'],
                ];
            }

            return $links;
        })();

@endphp

@section('footer_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (!sidebarToggle || !sidebar) {
                return;
            }

            sidebarToggle.addEventListener('click', function () {
                if (window.innerWidth < 992) {
                    sidebar.classList.toggle('show');
                    if (overlay) {
                        overlay.classList.toggle('show');
                    }
                }
            });

            if (overlay) {
                overlay.addEventListener('click', function () {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
        });
    </script>
    <script>
        // Dynamic Countdown Timer
        function startCountdown() {
            // Check if countdown elements exist
            const weeksEl = document.getElementById('weeks');
            const daysEl = document.getElementById('days');
            const hoursEl = document.getElementById('hours');
            const minutesEl = document.getElementById('minutes');
            const secondsEl = document.getElementById('seconds');

            if (!weeksEl || !daysEl || !hoursEl || !minutesEl || !secondsEl) {
                console.log('Countdown elements not found, skipping countdown initialization');
                return;
            }

            // Use the onboarding end date from the controller
            @if (isset($onboardingEndDate))
                const targetDate = new Date('{{ $onboardingEndDate->toISOString() }}');
            @else
                const targetDate = new Date();
                targetDate.setDate(targetDate.getDate() + 14);
            @endif

            function updateCountdown() {
                const now = new Date().getTime();
                const distance = targetDate.getTime() - now;

                if (distance < 0) {
                    // Countdown finished
                    weeksEl.textContent = '00';
                    daysEl.textContent = '00';
                    hoursEl.textContent = '00';
                    minutesEl.textContent = '00';
                    secondsEl.textContent = '00';
                    return;
                }

                // Calculate time units
                const weeks = Math.floor(distance / (1000 * 60 * 60 * 24 * 7));
                const days = Math.floor((distance % (1000 * 60 * 60 * 24 * 7)) / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Update the display
                weeksEl.textContent = weeks.toString().padStart(2, '0');
                daysEl.textContent = days.toString().padStart(2, '0');
                hoursEl.textContent = hours.toString().padStart(2, '0');
                minutesEl.textContent = minutes.toString().padStart(2, '0');
                secondsEl.textContent = seconds.toString().padStart(2, '0');
            }

            // Update countdown every second
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        // End onboarding button functionality (guarded to avoid double-binding)
        if (!window.__clubEndOnboardingInit) {
            window.__clubEndOnboardingInit = true;
            const endOnboardingBtn = document.getElementById('endOnboardingBtn');
            if (endOnboardingBtn) {
                endOnboardingBtn.addEventListener('click', function() {
                    if (confirm(
                            'Are you sure you want to end the onboarding period? This action cannot be undone.')) {
                        // Here you would typically make an AJAX call to update the club's onboarding status
                        alert('Onboarding period ended successfully!');
                        // Hide the countdown container
                        const countdownContainer = document.querySelector('.countdown-container');
                        if (countdownContainer) {
                            countdownContainer.style.display = 'none';
                        }
                    }
                });
            }
        }

        function setupChatSearch() {
            const chatSearchInput = document.querySelector('.chat-search input');
            const chatList = document.getElementById('clubContactList');
            const filterButtons = document.querySelectorAll('.chat-filter-buttons button');

            if (!chatList) {
                return;
            }

            const allItems = Array.from(chatList.querySelectorAll('.chat-item'));
            const emptyState = chatList.querySelector('.chat-empty');
            let activeFilter = 'all';

            const applyFilters = () => {
                const query = (chatSearchInput?.value || '').trim().toLowerCase();
                let visibleCount = 0;

                allItems.forEach((item) => {
                    const name = (item.getAttribute('data-contact-name') || '').toLowerCase();
                    const role = (item.getAttribute('data-contact-role') || '').toLowerCase();
                    const tagline = (item.getAttribute('data-contact-tagline') || '').toLowerCase();
                    const group = item.getAttribute('data-chat-group') || '';

                    const matchesText = !query || name.includes(query) || role.includes(query) || tagline
                        .includes(query);
                    const matchesGroup = activeFilter === 'all' || group === activeFilter;
                    const shouldShow = matchesText && matchesGroup;

                    item.style.display = shouldShow ? 'flex' : 'none';
                    if (shouldShow) {
                        visibleCount++;
                    }
                });

                if (emptyState) {
                    emptyState.style.display = visibleCount ? 'none' : 'block';
                }
            };

            chatSearchInput?.addEventListener('input', applyFilters);

            filterButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    filterButtons.forEach((b) => b.classList.remove('active'));
                    btn.classList.add('active');
                    activeFilter = btn.getAttribute('data-chat-filter') || 'all';
                    applyFilters();
                });
            });

            applyFilters();
        }

        // Start countdown when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startCountdown();
            setupPayoutButton();
            setupChatSearch();
        });
    </script>

    <script>
        // Assign Award Modal JavaScript (copied from coach dashboard)
        document.addEventListener('DOMContentLoaded', function() {
                    console.log('Club dashboard JavaScript loaded');

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

                    console.log('Modal elements found:', {
                        modalAwardSelect: !!modalAwardSelect,
                        modalDescription: !!modalDescription,
                        modalRequirements: !!modalRequirements,
                        modalRewards: !!modalRewards
                    });

                    // Player data from server
                    const players = @json($clubPlayers ?? []);
                    const selectedPlayers = new Map();

                    // Award selection change
                    if (modalAwardSelect) {
                        console.log('Adding event listener to award select');
                        modalAwardSelect.addEventListener('change', function() {
                            console.log('Award selection changed to:', this.value);
                            const awardId = this.value;
                            if (awardId) {
                                fetchAwardDetails(awardId);
                            }
                        });
                    } else {
                        console.error('modalAwardSelect element not found!');
                    }

                    // Fetch award details
                    function fetchAwardDetails(awardId) {
                        console.log('Fetching award details for ID:', awardId);
                        fetch(`/club/awards/${awardId}/details`)
                            .then(response => {
                                console.log('Response status:', response.status);
                                return response.json();
                            })
                            .then(data => {
                                console.log('Award details response:', data);
                                if (data.success) {
                                    const award = data.award;
                                    console.log('Setting description:', award.description);
                                    console.log('Award image URL:', award.image);

                                    modalDescription.value = award.description || '';
                                    modalRequirements.value = award.requirements || '';
                                    modalRewards.value = award.rewards || '';

                                    // Load award image
                                    loadAwardImage(award.image, award.name);
                                } else {
                                    console.error('API returned success: false');
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

                        if (imageUrl && imageUrl.trim() !== '') {
                            previewImg.src = imageUrl;
                            previewImg.alt = awardName + ' Award';
                            imagePreview.style.display = 'block';
                            uploadPlaceholder.style.display = 'none';
                        } else {
                            clearAwardImage();
                        }
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
                    const modalFileInput = document.getElementById('modalFileInput');
                    if (modalFileInput) {
                        modalFileInput.addEventListener('change', function() {
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

                        // Reward chip selection
                        document.querySelectorAll('.small-reward-chip').forEach(chip => {
                            chip.addEventListener('click', function() {
                                const rewardId = this.getAttribute('data-reward-id');
                                const rewardName = this.getAttribute('data-reward-name');

                                // Update the award select dropdown
                                if (modalAwardSelect) {
                                    modalAwardSelect.value = rewardId;

                                    // Trigger change event to load award details
                                    const changeEvent = new Event('change', {
                                        bubbles: true
                                    });
                                    modalAwardSelect.dispatchEvent(changeEvent);
                                }

                                // Update chip classes - selected becomes primary, others become mid/light
                                document.querySelectorAll('.small-reward-chip').forEach((otherChip,
                                    index) => {
                                    otherChip.classList.remove('primary', 'mid', 'light');
                                    if (otherChip === this) {
                                        otherChip.classList.add('primary');
                                    } else {
                                        otherChip.classList.add(index % 2 === 0 ? 'mid' : 'light');
                                    }
                                });

                                // Visual feedback
                                this.style.transform = 'scale(1.1)';
                                this.style.boxShadow = '0 0 10px rgba(0, 212, 170, 0.5)';

                                setTimeout(() => {
                                    this.style.transform = 'scale(1)';
                                    this.style.boxShadow = 'none';
                                }, 200);
                            });
                        });

                        // Initialize
                        updateSelectAllButton();
                    });

                // Existing calendar and chart scripts
                document.addEventListener('DOMContentLoaded', function() {

                    // Chart.js configurations
                    const ensureChart = (canvasId, config) => {
                        if (typeof Chart === 'undefined') {
                            return;
                        }
                        const canvas = document.getElementById(canvasId);
                        if (!canvas) {
                            return;
                        }
                        const existing = Chart.getChart(canvasId);
                        if (existing) {
                            existing.destroy();
                        }
                        return new Chart(canvas, config);
                    };

                    ensureChart('chart1', {
                        type: 'doughnut',
                        data: {
                            labels: ['Active Players', 'Inactive Players'],
                            datasets: [{
                                data: [{{ $activePlayers }}, {{ $inactivePlayers }}],
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

                    ensureChart('chart2', {
                        type: 'line',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                            datasets: [{
                                label: 'Revenue',
                                data: [12000, 19000, 15000, 25000, 22000, 30000],
                                borderColor: '#007bff',
                                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: 'white'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    ticks: {
                                        color: 'white'
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: 'white'
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    }
                                }
                            }
                        }
                    });
                });
    </script>

@section('content')

    @php
        $clubStatCards = $clubStatCards ?? [
            [
                'color' => 'green',
                'class' => 'd-flex flex-column align-items-center mb-4 text-center p-3',
                'before' => '<div class="w-75"><canvas id="chart1" height="60px"></canvas></div>',
                'image' => asset('assets/club-dashboard-main/assets/bars.png'),
                'image_class' => 'mb-1',
                'badge' => 'Estimated Payout',
                'badge_class' => 'mt-2 mb-2 px-2 py-1 w-75 text-truncate fs-12px',
                'badge_attributes' =>
                    'style="background: linear-gradient(92.43deg, #74d876 95.8% !important ,#468477 20.65% !important);"',
                'value' => '$' . number_format($estimatedPayout ?? 0, 0),
                'format' => 'raw',
                'value_class' => 'fs-24 fw-bold d-flex gap-2 align-items-center',
                'trend_icon' => asset('assets/club-dashboard-main/assets/ic_trending_up.svg'),
                'trend_class' => 'fs-12px mb-2',
                'footer' => '21% Higher than Average',
            ],
            [
                'color' => 'orange',
                'class' => 'd-flex flex-column align-items-center mb-4 text-center p-3',
                'before' => '<div class="w-75"><canvas id="chart2" height="60px"></canvas></div>',
                'image' => asset('assets/club-dashboard-main/assets/graph.png'),
                'image_class' => 'mb-1',
                'badge' => 'Coaches',
                'badge_class' => 'mt-2 mb-2 px-2 py-1 fs-12px',
                'value' => $coachCount ?? 0,
                'value_class' => 'fs-24 fw-bold d-flex gap-2 align-items-center',
                'trend_icon' => asset('assets/club-dashboard-main/assets/ic_trending_up.svg'),
                'trend_class' => 'fs-12px mb-2',
                'footer' => '13% Higher than Average',
            ],
            [
                'color' => 'blue',
                'class' => 'd-flex flex-column align-items-center mb-4 text-center p-3',
                'before' => '<div class="w-75"><canvas id="chart3" height="60px"></canvas></div>',
                'image' => asset('assets/club-dashboard-main/assets/pie.png'),
                'image_class' => 'mb-1',
                'badge' => 'Teams',
                'badge_class' => 'mt-2 mb-2 px-2 py-1 w-75 fs-12px',
                'value' => $teamCount ?? 0,
                'value_class' => 'fs-24 fw-bold d-flex gap-2 align-items-center',
                'trend_icon' => asset('assets/club-dashboard-main/assets/ic_trending_down.svg'),
                'trend_class' => 'fs-12px mb-2',
                'footer' => '34% Lower than Average',
            ],
            [
                'color' => 'purple',
                'class' => 'd-flex flex-column align-items-center mb-4 text-center p-3',
                'before' => '<div class="w-75"><canvas id="chart4" height="60px"></canvas></div>',
                'image' => asset('assets/club-dashboard-main/assets/bars.png'),
                'image_class' => 'mb-1',
                'badge' => 'Total Registered',
                'badge_class' => 'mt-2 mb-2 px-2 py-1 w-75 text-truncate fs-12px',
                'value' => $activePlayers ?? 0,
                'value_class' => 'fs-24 fw-bold d-flex gap-2 align-items-center',
                'trend_icon' => asset('assets/club-dashboard-main/assets/ic_trending_down.svg'),
                'trend_class' => 'fs-12px mb-2',
                'footer' => '06% Lower than Average',
            ],
        ];
    @endphp

    @include('partials.dashboard_weather_widget', [
        'entity' => $clubEntityMedia,
        'weather' => $weather ?? null,
        'locationFallback' => '--',
        'socialLinks' => $clubSocialLinksData,
        'fallbackTemperature' => '-°',
    ])

    @include('partials.dashboard_stat_card_grid', [
        'cards' => $clubStatCards,
        'rowClass' => '',
        'colClasses' => 'col-sm-6 col-md-6 col-lg-3',
    ])

    <!-- Sponsorship Payout Countdown -->
    @if ($showPayoutCountdown ?? true)
        <div class="countdown-container">
            <div class="countdown-header">
                <div class="d-flex align-items-center">
                    <h2 class="mb-0">Sponsorship Payout</h2>
                </div>
                <button class="end-btn" id="endPayoutBtn" style="color: #fff">
                    @if (isset($actualPayoutTimeRemaining) && $actualPayoutTimeRemaining)
                        {{ $actualPayoutTimeRemaining['weeks'] }}w {{ $actualPayoutTimeRemaining['days'] }}d left
                    @else
                        End in 14 Days
                    @endif
                </button>
            </div>
            <div class="fs-12px text-gray mb-2">{{ $payoutStatusDescription ?? 'COUNTDOWN' }}</div>
            @if (isset($payoutAmount) && $payoutAmount > 0)
                <div class="fs-12px text-success mb-2 fw-bold">${{ number_format($payoutAmount, 2) }}</div>
            @endif

            <div class="countdown-grid" style="color: #fff;">
                <div class="countdown-card purple-grad" style="color: #fff;">
                    <span class="number" id="payoutMonths">00</span>
                    <span class="label" style="color: #fff;">Months</span>
                </div>
                <div class="countdown-card orange-grad" style="color: #fff;">
                    <span class="number" id="payoutWeeks">02</span>
                    <span class="label" style="color: #fff;">Weeks</span>
                </div>
                <div class="countdown-card purple-grad" style="color: #fff;">
                    <span class="number" id="payoutDays">00</span>
                    <span class="label" style="color: #fff;">Days</span>
                </div>
                <div class="countdown-card blue-grad" style="color: #fff;">
                    <span class="number" id="payoutHours">00</span>
                    <span class="label" style="color: #fff;">Hours</span>
                </div>
                <div class="countdown-card pink-grad" style="color: #fff;">
                    <span class="number" id="payoutMinutes">00</span>
                    <span class="label" style="color: #fff;">Minutes</span>
                </div>
                <div class="countdown-card green-grad" style="color: #fff;">
                    <span class="number" id="payoutSeconds">00</span>
                    <span class="label" style="color: #fff;">Seconds</span>
                </div>
            </div>

            <p class="tip">
                @if (isset($payoutStatusDescription))
                    @if ($payoutStatusDescription === 'Onboarding Period')
                        Tip: Complete your onboarding period to start earning payouts.
                    @elseif($payoutStatusDescription === 'Payout Period')
                        Tip: Maintain your player count to maximize your payout amount.
                    @elseif($payoutStatusDescription === 'Calculated - Ready for Payment')
                        Tip: Your payout has been calculated and is ready for processing.
                    @elseif($payoutStatusDescription === 'Payout Period Starts After Onboarding')
                        Tip: Register more players during onboarding to increase your payout amount.
                    @elseif($payoutStatusDescription === 'Onboarding Complete - Payout Period Starting Soon')
                        Tip: Your payout period will begin soon. Keep recruiting players!
                    @else
                        Tip: Complete your sponsorship requirements to receive your payout on time.
                    @endif
                @else
                    Tip: Complete your sponsorship requirements to receive your payout on time.
                @endif
            </p>
        </div>
    @endif
    <!-- Metric Widget -->
    <div class="metric-widget gray-card p-4 pb-0 mb-4 mt-4">
        <div class="section-header mb-3 d-flex justify-content-between">
            <h2 class="fs-5 mb-0">Club Performance</h2>
            <div><i class="bi bi-three-dots"></i></div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6 col-xxl-3">
                <div class="metric-card p-3 mb-4 d-flex gap-2">
                    <div class="time-breakdown-chart">
                        <div class="percentage-chart percentage-chart-meeting">
                            <svg viewBox="0 0 36 36">
                                <defs>
                                    <linearGradient id="circleGradient" x1="0%" y1="0%" x2="100%"
                                        y2="100%">
                                        <stop offset="0%" style="stop-color: #74d876; stop-opacity: 1" />
                                        <stop offset="90%" style="stop-color: #468477; stop-opacity: 1" />
                                    </linearGradient>
                                </defs>
                                <path class="percentage-chart-bg"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="percentage-chart-stroke" stroke="url(#circleGradient)"
                                    stroke-dasharray="{{ min(($totalRevenue ?? 0) / 100, 100) }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="counter" style="--counter-end: {{ min(($totalRevenue ?? 0) / 100, 100) }}"></div>
                        </div>
                    </div>
                    <div class="metric-text">
                        <h3 class="metric-title fs-14px mb-0">Total Revenue</h3>
                        <div class="progress-xy my-2" style="height: 5px">
                            <div class="progress-bar green-grad"
                                style="width: {{ min(($totalRevenue ?? 0) / 100, 100) }}%"></div>
                        </div>
                        <div class="metric-subtitle fs-12px">
                            ${{ number_format($totalRevenue ?? 0, 0) }} Total<br />Revenue
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

            <div class="col-sm-12 col-md-6 col-lg-6 col-xxl-3">
                <div class="metric-card p-3 mb-4 d-flex gap-2">
                    <div class="time-breakdown-chart">
                        <div class="percentage-chart percentage-chart-meeting">
                            <svg viewBox="0 0 36 36">
                                <defs>
                                    <linearGradient id="circleGradient3" x1="0%" y1="0%" x2="100%"
                                        y2="100%">
                                        <stop offset="0%" style="stop-color: #667df2; stop-opacity: 1" />
                                        <stop offset="90%" style="stop-color: #7b4ed7; stop-opacity: 1" />
                                    </linearGradient>
                                </defs>
                                <path class="percentage-chart-bg"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="percentage-chart-stroke" stroke="url(#circleGradient3)"
                                    stroke-dasharray="{{ min(($newRegistrations ?? 0) * 2, 100) }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="counter" style="--counter-end: {{ min(($newRegistrations ?? 0) * 2, 100) }}">
                            </div>
                        </div>
                    </div>
                    <div class="metric-text">
                        <h3 class="metric-title fs-14px mb-0">New Registrations</h3>
                        <div class="progress-xy my-2" style="height: 5px">
                            <div class="progress-bar purple-grad"
                                style="width: {{ min(($newRegistrations ?? 0) * 2, 100) }}%"></div>
                        </div>
                        <div class="metric-subtitle fs-12px">
                            {{ $newRegistrations ?? 0 }} New<br />Registrations
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="two-column">
        <div class="left-column">


            <!-- Onboarding Countdown -->
            @if ($isOnboardingActive ?? true)
                <div class="countdown-container">
                    <div class="countdown-header">
                        <div class="d-flex align-items-center">
                            <h2 class="mb-0">Onboarding Period Remaining</h2>
                        </div>
                        <button class="end-btn" id="endOnboardingBtn">
                            @if (isset($onboardingEndDate))
                                End {{ $onboardingEndDate->format('M d') }}
                            @else
                                End in 14 Days
                            @endif
                        </button>
                    </div>
                    <div class="fs-12px text-gray mb-2">COUNTDOWN</div>
                    <div class="countdown-grid">
                        <div class="countdown-card purple-grad" style="color: #fff;">
                            <span class="number" id="weeks">02</span>
                            <span class="label" style="color: #fff;">Weeks</span>
                        </div>
                        <div class="countdown-card orange-grad" style="color: #fff;">
                            <span class="number" id="days">00</span>
                            <span class="label" style="color: #fff;">Days</span>
                        </div>
                        <div class="countdown-card blue-grad" style="color: #fff;">
                            <span class="number" id="hours">00</span>
                            <span class="label" style="color: #fff;">Hours</span>
                        </div>
                        <div class="countdown-card pink-grad" style="color: #fff;">
                            <span class="number" id="minutes">00</span>
                            <span class="label" style="color: #fff;">Minutes</span>
                        </div>
                        <div class="countdown-card green-grad" style="color: #fff;">
                            <span class="number" id="seconds">00</span>
                            <span class="label" style="color: #fff;">Seconds</span>
                        </div>
                    </div>

                    <p class="tip">
                        Tip: Complete your club setup to unlock all features and start managing your teams effectively.
                    </p>
                </div>
            @endif




            <!-- Action Button section -->
            <div class="action-widget p-4 mb-0">
                <div class="section-header">
                    <h2 class="section-title">Quick Actions</h2>
                    <button class="section-menu">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>
                <div class="d-flex flex-wrap gap-3 w-100">
                    <a href="{{ route('club.coaches.create') }}" class="action-btn green">
                        <i class="fas fa-plus"></i> Add Coach
                    </a>
                    <button type="button" class="action-btn cyan" data-bs-toggle="modal" data-bs-target="#assignAward">
                        <i class="bi bi-trophy"></i> Assign Award
                    </button>
                    <button type="button" class="action-btn teal" data-bs-toggle="modal"
                        data-bs-target="#createTournamentModal">
                        <i class="bi bi-diagram-3"></i> Tournament Engine
                    </button>

                    <button type="button" class="action-btn red" data-bs-toggle="modal"
                        data-bs-target="#playerAttributesModal">
                        <i class="fas fa-user-friends" style="color: transparent; -webkit-text-stroke: 1px #f0ecec"></i>
                        Player Attributes
                    </button>
                    <button type="button" class="action-btn blue" data-bs-toggle="modal"
                        data-bs-target="#createMatchModal">
                        <i class="fas fa-handshake" style="color: transparent; -webkit-text-stroke: 1px #f0ecec"></i>
                        Create Match
                    </button>
                    <a href="{{ route('player.blogs.index', ['context' => 'club']) }}" class="action-btn orange">
                        <i class="bi bi-files"></i> Blog Post
                    </a>

                </div>
            </div>

            <style>
                .action-btn {
                    border: none !important;
                    color: white !important;
                }

                .action-btn i {
                    color: white !important;
                }

                .action-btn:hover {
                    color: white !important;
                }

                .action-btn:hover i {
                    color: white !important;
                }
            </style>

            <!--countdown timer section-->
            @php
                $daysRemaining = is_array($onboardingTimeRemaining ?? 14) ? 14 : $onboardingTimeRemaining ?? 14;
                $weeks = floor($daysRemaining / 7);
                $days = $daysRemaining % 7;
            @endphp
            <div class="countdown-container">
                <div class="countdown-header">
                    <div class="d-flex align-items-center">
                        <h2 class="mb-0">Onboarding Period Remaining</h2>
                    </div>
                    <button class="end-btn">End in {{ $daysRemaining }} Days</button>
                </div>
                <div class="fs-12px text-gray mb-2">COUNTDOWN</div>
                <div class="countdown-grid">
                    <div class="countdown-card purple-grad" style="color: #fff;">
                        <span class="number">{{ $weeks }}</span>
                        <span class="label" style="color: #fff;">Weeks</span>
                    </div>
                    <div class="countdown-card orange-grad" style="color: #fff;">
                        <span class="number">{{ $days }}</span>
                        <span class="label" style="color: #fff;">Days</span>
                    </div>
                    <div class="countdown-card blue-grad" style="color: #fff;">
                        <span class="number">00</span>
                        <span class="label" style="color: #fff;">Hours</span>
                    </div>
                    <div class="countdown-card pink-grad" style="color: #fff;">
                        <span class="number">00</span>
                        <span class="label" style="color: #fff;">Minutes</span>
                    </div>
                    <div class="countdown-card green-grad" style="color: #fff;">
                        <span class="number">00</span>
                        <span class="label" style="color: #fff;">Seconds</span>
                    </div>
                </div>

                <p class="tip">
                    Tip: Turn this into a component and expose the five numbers as
                    properties for a live countdown.
                </p>
            </div>

            <!-- Calendar section -->
            <div class="my-4 calendar-container">
                <div id="calendar"></div>
            </div>

            <!-- Tournament Card -->
            <div class="tournament-card p-4 mb-4">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-2">
                    <div class="col-md-auto">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('assets/club-dashboard-main/assets/tr.png') }}" alt="stadium"
                                class="tournament-img" />
                            <h3 class="tournament-title" style="color: #fff;">Tournament Directory</h3>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center align-items-center">
                        <div class="subtitle fw-semibold text-white mb-0" style="color: #fff;">
                            Tournament Engine
                        </div>
                        <a href="#" class="start-btn" data-bs-toggle="modal"
                            data-bs-target="#createTournamentModal" role="button">
                            Start
                        </a>

                    </div>
                </div>
                <div class="card-desc" style="color: #fff;">
                    Search and register for tournaments by location, date, sport, division, age, and more.
                </div>
            </div>

            <!-- Tournament Search Section -->
<div class="tournament-chart-container rounded-4" data-tournament-search>
                <!-- Header -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h1 class="main-header" style="color: #fff;">Tournament Search</h1>
                    <div class="d-flex align-items-center gap-2">
                        <button class="chat-src-btn" type="button" data-filter-apply>Search</button>
                        <button class="chat-clr-btn" type="button" data-filter-reset>Clear filters</button>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="filters-section">
                    <!-- First Row -->
                    <div class="row filter-row">
                        <div class="col-md-3">
                            <label class="filter-label">Province</label>
                            <select class="form-select" data-filter="state">
                                <option value="">All Provinces</option>
                                @foreach ($tournamentFilterOptions['states'] as $state)
                                    <option value="{{ \Illuminate\Support\Str::slug($state, '_') }}">{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="filter-label">City</label>
                            <select class="form-select" data-filter="city">
                                <option value="">All Cities</option>
                                @foreach ($tournamentFilterOptions['cities'] as $city)
                                    <option value="{{ \Illuminate\Support\Str::slug($city, '_') }}">{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="filter-label">Sport</label>
                            <select class="form-select" data-filter="sport">
                                <option value="">All Sports</option>
                                @foreach ($tournamentFilterOptions['sports'] as $sport)
                                    <option value="{{ \Illuminate\Support\Str::slug($sport, '_') }}">{{ $sport }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="filter-label">Month</label>
                            <select class="form-select" data-filter="month">
                                <option value="">All Dates</option>
                                @foreach ($tournamentFilterOptions['months'] as $month)
                                    <option value="{{ $month['value'] }}">{{ $month['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Second Row -->
                    <div class="row g-3">
                        <div class="col">
                            <label class="form-label fs-12px fw-400 text-gray">Division</label>
                            <select class="form-select" data-filter="division">
                                <option value="">All Divisions</option>
                                @foreach ($tournamentFilterOptions['divisions'] as $division)
                                    <option value="{{ \Illuminate\Support\Str::slug($division, '_') }}">{{ $division }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label fs-12px fw-400 text-gray">Status</label>
                            <select class="form-select" data-filter="status">
                                <option value="">All Statuses</option>
                                @foreach ($tournamentFilterOptions['statuses'] as $status)
                                    <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="results-header" data-result-count>Results • {{ $tournamentDirectoryEntries->count() ?? 0 }} Tournaments</div>

                <!-- Tournament Table -->
                <div class="tournament-table">
                    <div class="table-responsive">
                        <table class="table align-middle custom-table">
                            <thead>
                                <tr>
                                    <th>Tournament</th>
                                    <th>City / Province</th>
                                    <th>Dates</th>
                                    <th>Divisions</th>
                                    <th>Teams</th>
                                    <th>Fee</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tournamentDirectoryEntries ?? [] as $tournament)
                                    @php
                                        $stateSlug = \Illuminate\Support\Str::slug($tournament['state'] ?? '', '_');
                                        $citySlug = \Illuminate\Support\Str::slug($tournament['city'] ?? '', '_');
                                        $sportSlug = \Illuminate\Support\Str::slug($tournament['sport'] ?? '', '_');
                                        $divisionSlug = \Illuminate\Support\Str::slug($tournament['division'] ?? '', '_');
                                        $monthKey = $tournament['month_key'] ?? '';
                                        $statusType = $tournament['status_type'] ?? '';
                                        $feeValue = is_null($tournament['fee']) ? '' : (float) $tournament['fee'];
                                    @endphp
                                    <tr data-tournament-row
                                        data-state="{{ $stateSlug }}"
                                        data-state-label="{{ $tournament['state'] }}"
                                        data-city="{{ $citySlug }}"
                                        data-city-label="{{ $tournament['city'] }}"
                                        data-sport="{{ $sportSlug }}"
                                        data-division="{{ $divisionSlug }}"
                                        data-status="{{ $statusType }}"
                                        data-month="{{ $monthKey }}"
                                        data-fee="{{ $feeValue }}">
                                        <td>{{ $tournament['name'] }}</td>
                                        <td>{{ $tournament['city'] }}, {{ $tournament['state'] }}</td>
                                        <td>{{ $tournament['dates'] }}</td>
                                        <td>{{ $tournament['division'] }}</td>
                                        <td>{{ $tournament['teams'] }}</td>
                                        <td>
                                            @if (!is_null($tournament['fee']))
                                                ${{ number_format((float) $tournament['fee'], 2) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            <span class="status-badge {{ $tournament['status_class'] }}">
                                                {{ $tournament['status_label'] }}
                                            </span>
                                            @if (empty($tournament['action_disabled']))
                                                <a href="{{ route('player.tournaments.directory') }}"
                                                    class="status-badge status-register" data-player-tournament-link
                                                    data-url="{{ route('player.tournaments.directory') }}"
                                                    data-tournament-id="{{ $tournament['id'] }}">
                                                    {{ $tournament['action_label'] }}
                                                </a>
                                            @else
                                                <span class="status-badge status-register disabled" aria-disabled="true">
                                                    {{ $tournament['action_label'] }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No tournaments available yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tip Section -->
                <div class="tip-section">
                    <p class="tip-text">
                        Tip: Use filters to narrow by province, city, date range, division, age, surface, status, and fee.
                    </p>
                </div>
            </div>

            @php
                $recentVideos = ($recentVideos ?? collect())->values();
                $featuredVideo = $recentVideos->first();
                $additionalVideos = $recentVideos->slice(1);
            @endphp

            <div class="video-card">
                @if ($featuredVideo)
                    <div class="video-thumb">
                        <a href="{{ $featuredVideo['show_url'] }}" class="d-block">
                            <img src="{{ $featuredVideo['thumbnail'] }}" alt="{{ $featuredVideo['title'] }}">
                            <div class="overlay">
                                <span class="play-btn" aria-hidden="true">&#9658;</span>
                            </div>
                            <span class="visually-hidden">Watch {{ $featuredVideo['title'] }}</span>
                        </a>
                    </div>

                    <div class="video-info-bar">
                        <div class="text-muted small">
                            <span
                                class="badge bg-primary bg-opacity-25 text-white text-uppercase small me-2">{{ $featuredVideo['duration'] }}</span>
                            Uploaded {{ $featuredVideo['uploaded_human'] }}
                        </div>
                        <a href="{{ $featuredVideo['show_url'] }}" class="btn btn-sm btn-outline-light">Watch now</a>
                    </div>

                    <div class="video-meta">
                        <h4 class="mb-1">{{ $featuredVideo['title'] }}</h4>
                        <div class="text-muted small mb-2">By {{ $featuredVideo['author'] }}</div>
                        <p>{{ $featuredVideo['description'] }}</p>
                    </div>
                @else
                    <div class="video-empty">
                        <p class="mb-2">No club videos yet.</p>
                        <a href="{{ route('player.videos.explore') }}" class="btn btn-sm btn-primary">Explore videos</a>
                    </div>
                @endif

                @if ($additionalVideos->isNotEmpty())
                    <div class="video-playlist">
                        @foreach ($additionalVideos as $video)
                            <a class="playlist-item" href="{{ $video['show_url'] }}">
                                <img src="{{ $video['thumbnail'] }}" alt="{{ $video['title'] }}">
                                <div>
                                    <div class="playlist-title">{{ $video['title'] }}</div>
                                    <div class="playlist-meta">{{ $video['duration'] }} • {{ $video['author'] }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
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
                @php
                    $chatSegments = collect([
                        ['key' => 'recent', 'label' => 'Recent Chats', 'items' => collect($recentChats ?? [])],
                        ['key' => 'owners', 'label' => 'Club Owners', 'items' => collect($chatOwners ?? [])],
                        ['key' => 'coaches', 'label' => 'Coaches', 'items' => collect($chatCoaches ?? [])],
                        ['key' => 'players', 'label' => 'Players', 'items' => collect($chatPlayers ?? [])],
                    ])->map(function ($segment) {
                        $segment['items'] = $segment['items']->filter();
                        return $segment;
                    });
                    $filterSegments = $chatSegments->filter(fn($segment) => $segment['items']->count() > 0);
                    $totalContacts = $chatSegments->sum(fn($segment) => $segment['items']->count());
                    $hasContacts = $totalContacts > 0;
                @endphp
                <div class="chat-filter-buttons">
                    <button type="button" class="active" data-chat-filter="all">
                        All
                        <span class="count-pill">{{ $totalContacts }}</span>
                    </button>
                    @foreach ($filterSegments as $segment)
                        <button type="button" data-chat-filter="{{ $segment['key'] }}">
                            {{ $segment['label'] }}
                            <span class="count-pill">{{ $segment['items']->count() }}</span>
                        </button>
                    @endforeach
                </div>
                <div class="chat-list" id="clubContactList">
                    @foreach ($chatSegments as $segment)
                        @foreach ($segment['items'] as $contact)
                            <div class="chat-item {{ empty($contact['user_id']) && empty($contact['chat_id']) ? 'disabled' : '' }}"
                                data-chat-group="{{ $segment['key'] }}" data-chat-id="{{ $contact['chat_id'] ?? '' }}"
                                data-contact-name="{{ e($contact['name']) }}"
                                data-contact-role="{{ e($contact['role'] ?? '') }}"
                                data-contact-label="{{ e($contact['name']) }}"
                                data-user-id="{{ $contact['user_id'] ?? '' }}"
                                data-contact-tagline="{{ e($contact['tagline'] ?? '') }}"
                                data-contact-status="{{ e($contact['status_label'] ?? '') }}"
                                data-contact-initials="{{ e($contact['initials'] ?? '') }}"
                                data-contact-avatar="{{ e($contact['avatar'] ?? '') }}"
                                title="{{ empty($contact['user_id']) ? 'Chat unavailable for this contact' : '' }}">
                                <div class="chat-avatar">
                                    <img src="{{ $contact['avatar'] }}" alt="{{ $contact['name'] }}" loading="lazy" />
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
                    @endforeach

                    <div class="chat-empty" @if ($hasContacts) style="display: none;" @endif>
                        <p class="mb-1">No club contacts yet.</p>
                        <p class="small mb-0">Add players or coaches to activate chat.</p>
                    </div>
                </div>
            </div>

            <!-- Tournament Engine Card -->
            <div class="container my-3">
                <div class="d-flex align-items-center justify-content-between gradient-card p-2 rounded">
                    <div class="d-flex align-items-center text-white gap-2">
                        <img src="{{ asset('assets/club-dashboard-main/assets/acheivement.png') }}" alt="acheivement"
                            width="28" height="28" />
                        <span class="fw-semibold fst-italic">Tournament Engine</span>
                    </div>
                    <a href="#" class="start-btn" data-bs-toggle="modal" data-bs-target="#createTournamentModal"
                        role="button">
                        Start
                    </a>

                </div>
            </div>



            <!-- Tournament Chat -->
            <div class="chat-card p-3 rounded">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-white mb-0">Tournament Chat All Clubs</h5>
                    <i class="fas fa-ellipsis-h text-white"></i>
                </div>
                <div class="mb-2">
                    <div class="input-group d-flex align-items-center px-2 py-1">
                        <input type="text" class="form-control border-0 text-white p-0 bg-transparent"
                            placeholder="Search tournaments" id="tournamentChatSearch" />
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <p class="note-text small mb-3">
                    Note: Your chats are visible to all clubs. ✅
                </p>
                <div class="chat-list tournament-chat-list">
                    @forelse ($tournamentChatRooms as $room)
                        <div class="chat-item d-flex align-items-center gap-3 py-2 tournament-chat-item {{ $room['is_closed'] ? 'disabled' : '' }}"
                            data-tournament-id="{{ $room['id'] }}" data-tournament-status="{{ $room['status'] }}"
                            data-tournament-name="{{ e($room['name']) }}"
                            data-tournament-location="{{ e($room['location'] ?? 'TBD') }}"
                            data-tournament-initials="{{ e($room['initials']) }}">
                            <div class="rounded-circle bg-light text-dark fw-semibold d-flex align-items-center justify-content-center"
                                style="width: 42px; height: 42px;">
                                {{ $room['initials'] }}
                            </div>
                            <div class="flex-grow-1 user-name">
                                <div class="text-white fw-semibold">{{ $room['name'] }}</div>
                                <small class="text-muted">{{ $room['location'] }}</small><br>
                                <small class="text-warning">
                                    {{ $room['status'] }}
                                    @if ($room['start_label'] || $room['end_label'])
                                        ·
                                        {{ $room['start_label'] ?? 'TBD' }}{{ $room['end_label'] ? ' - ' . $room['end_label'] : '' }}
                                    @endif
                                </small>
                            </div>
                            @if ($room['is_closed'])
                                <span class="badge bg-secondary">Closed</span>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-light open-tournament-chat"
                                    data-tournament-id="{{ $room['id'] }}">Open Chat</button>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            No upcoming tournament chats available yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Season Stats -->
            <div class="season-stats-card mt-xxl-4">
                <!-- Header -->
                <div class="season-header">
                    <h5>Season Stats</h5>
                    <i class="fas fa-ellipsis-h"></i>
                </div>

                <!-- Stats List -->
                <ul class="season-list">
                    <li class="season-item">
                        <div class="text-block">
                            <span class="label">Tournaments This Season:</span>
                            <span class="value mt-1">
                                <span class="active">{{ $activeTournaments ?? 3 }} Active</span> ·
                                <span class="upcoming">{{ $upcomingTournaments ?? 2 }} Upcoming</span>
                            </span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </li>

                    <li class="season-item">
                        <div class="text-block">
                            <span class="label">Teams Registered:</span>
                            <span class="value white">{{ $registeredTeams ?? 24 }}</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </li>

                    <li class="season-item">
                        <div class="text-block">
                            <span class="label">Approved vs. Pending:</span>
                            <span class="value">
                                <span class="approved">{{ $approvedTeams ?? 18 }} Approved</span> ·
                                <span class="pending">{{ $pendingTeams ?? 6 }} Pending</span>
                            </span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </li>

                    <li class="season-item">
                        <div class="text-block">
                            <span class="label">Matches:</span>
                            <span class="value">
                                <span class="active">{{ $scheduledMatches ?? 25 }} Scheduled</span> ·
                                <span class="upcoming">{{ $completedMatches ?? 17 }} Completed</span>
                            </span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </li>

                    <li class="season-item last">
                        <div class="text-block">
                            <span class="label">Winners Finalized:</span>
                            <span class="value white">{{ $finalizedWinners ?? 3 }}</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </li>
                </ul>
            </div>

            <!-- Active Tournament Card -->
            <div class="active-tournament-card mt-xxl-4">
                <!-- Header -->
                <div class="tournament-header">
                    <h5>Active Tournament</h5>
                    <i class="fas fa-ellipsis-h"></i>
                </div>

                <!-- Search -->
                <div class="tournament-search">
                    <div class="search-box">
                        <input type="text" placeholder="Search" />
                        <i class="fas fa-search"></i>
                    </div>
                    <button class="btn-search">Search</button>
                </div>

                <!-- Tournament Details -->
                <ul class="tournament-list">
                    <li class="tournament-item">
                        <div class="text-block">
                            <span class="label">Tournament Name:</span>
                            <span class="value">{{ $activeTournamentName ?? 'Spring Cup 2025' }}</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </li>

                    <li class="tournament-item">
                        <div class="text-block">
                            <span class="label">Format:</span>
                            <span class="value">{{ $tournamentFormat ?? 'Round Robin' }}</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </li>

                    <li class="tournament-item">
                        <div class="text-block">
                            <span class="label">Dates:</span>
                            <span class="value">{{ $tournamentDates ?? 'May 10 - May 14' }}</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </li>

                    <li class="tournament-item">
                        <div class="text-block">
                            <span class="label">Location:</span>
                            <span class="value">{{ $tournamentLocation ?? 'Ottawa Sports Dome' }}</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </li>

                    <li class="tournament-item">
                        <div class="text-block">
                            <span class="label">Teams/Divisions:</span>
                            <span class="value">{{ $tournamentTeams ?? '12 Teams. 2 Divisions' }}</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </li>

                    <li class="tournament-item last">
                        <div class="text-block">
                            <span class="label">Status:</span>
                            <span class="value">{{ $tournamentStatus ?? 'Registration Open' }}</span>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </li>
                </ul>
            </div>

            <!-- Assign Task / Reminders -->
            <div class="task-card mt-xxl-4">
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
        </div>
    </div>
    <!-- Club Chat Modal -->
    <div class="modal fade" id="clubChatModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="clubChatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content modal-content-padding modal-content-one">
                <div class="modal-header modal-header-one">
                    <div class="modal-headding modal-headding-two">
                        <div class="leftmodal-header d-flex align-items-center gap-3">
                            <div class="chat-box-icon personal-chat-text">
                                <h2 id="clubChatUserInitials">CC</h2>
                            </div>
                            <div class="modal-text-headding">
                                <h1 class="modal-title fs-5 mb-1" id="clubChatUserName">Chat</h1>
                                <p id="clubChatUserStatus" class="mb-0 small text-muted">
                                    <label></label>
                                </p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="chat-container">
                        <div class="chat-left panel-box panel-box-a chat-left-card">
                            <div class="chat-body d-flex flex-column pt-0" id="clubChatMessages"></div>
                        </div>
                    </div>
                    <div class="chat-footer mt-3">
                        <form id="clubChatForm" class="chat-input-box gap-2 d-flex align-items-center"
                            autocomplete="off">
                            <input type="text" id="clubChatInput" class="form-control form-control-lg"
                                placeholder="Write a message..." autocomplete="off">
                            <button type="submit" class="btn btn-primary px-4" id="clubSendBtn" disabled>Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Match Modal -->
    <div class="modal fade" id="createMatchModal" tabindex="-1" aria-labelledby="createMatchModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body create-match-modal">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <div class="create-match-header p-3">
                        <div>
                            <h2 class="create-match-title" id="createMatchModalLabel" style="color: #000;">Create Match
                                Event</h2>
                            <p class="create-match-subtitle">
                                Set up a game with opponent, venue, roster, reminders, and repeats.
                            </p>
                        </div>
                        <div class="create-match-actions">
                            <button type="button" class="btn cm-btn cm-btn--publish" id="publishEventHeaderBtn">
                                Publish Event
                            </button>
                            <button type="button" class="btn cm-btn cm-btn--draft" id="saveDraftHeaderBtn">
                                Save as Draft
                            </button>
                        </div>
                    </div>

                    <div class="create-match-layout p-3">
                        <div class="cm-main">
                            <div class="cm-card">
                                <div class="cm-card-title">BASIC INFO</div>
                                <div class="cm-fields-group">
                                    <label class="cm-field">
                                        <span class="cm-section-title">EVENT TITLE</span>
                                        <input type="text" class="cm-input" id="eventTitle"
                                            placeholder="Enter event title">
                                    </label>
                                    <div class="cm-grid-three">
                                        <label class="cm-field">
                                            <span class="cm-section-title">OPPONENT</span>
                                            <input type="text" class="cm-input" id="opponent"
                                                placeholder="Opponent team name">
                                        </label>
                                        <label class="cm-field">
                                            <span class="cm-section-title">MATCH TYPE</span>
                                            <input type="text" class="cm-input" id="matchType"
                                                placeholder="League, Friendly, etc.">
                                        </label>
                                        <div class="cm-field">
                                            <span class="cm-section-title">HOME / AWAY</span>
                                            <div class="cm-toggle-group" id="homeAwayToggle">
                                                <button type="button" class="active" data-value="Home">Home</button>
                                                <button type="button" data-value="Away">Away</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="cm-card">
                                <div class="cm-card-title">DATE &amp; TIME</div>
                                <div class="cm-fields-group">
                                    <div class="cm-grid-two">
                                        <label class="cm-field">
                                            <span class="cm-section-title">DATE</span>
                                            <input type="date" class="cm-input" id="eventDate">
                                        </label>
                                        <label class="cm-field">
                                            <span class="cm-section-title">KICKOFF</span>
                                            <input type="time" class="cm-input" id="kickoffTime">
                                        </label>
                                    </div>
                                    <div class="cm-grid-two">
                                        <label class="cm-field">
                                            <span class="cm-section-title">ARRIVAL</span>
                                            <input type="time" class="cm-input" id="arrivalTime">
                                        </label>
                                        <label class="cm-field">
                                            <span class="cm-section-title">END</span>
                                            <input type="time" class="cm-input" id="endTime">
                                        </label>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="cm-section-title">REPEAT</span>
                                        <div class="form-check form-switch cm-switch">
                                            <input class="form-check-input" type="checkbox" id="repeatToggle" checked>
                                        </div>
                                    </div>
                                    <div id="repeatSection">
                                        <div class="cm-grid-two">
                                            <label class="cm-field">
                                                <span class="cm-section-title">FREQUENCY</span>
                                                <select class="cm-input" id="frequency">
                                                    <option value="">One-time</option>
                                                    <option value="Daily">Daily</option>
                                                    <option value="Weekly" selected>Weekly</option>
                                                    <option value="Monthly">Monthly</option>
                                                </select>
                                            </label>
                                            <label class="cm-field">
                                                <span class="cm-section-title">EVERY</span>
                                                <input type="text" class="cm-input" id="every" value="1 week">
                                            </label>
                                        </div>
                                        <div class="cm-grid-two">
                                            <label class="cm-field">
                                                <span class="cm-section-title">AT</span>
                                                <input type="time" class="cm-input" id="atTime">
                                            </label>
                                            <label class="cm-field">
                                                <span class="cm-section-title">ENDS</span>
                                                <input type="text" class="cm-input" id="ends"
                                                    value="After • 8 occurrences">
                                            </label>
                                        </div>
                                        <div>
                                            <div class="cm-section-title">DAYS</div>
                                            <div class="cm-pill-row mt-2" id="dayPills">
                                                <span class="cm-pill" data-day="Sunday">S</span>
                                                <span class="cm-pill" data-day="Monday">M</span>
                                                <span class="cm-pill active" data-day="Tuesday">T</span>
                                                <span class="cm-pill" data-day="Wednesday">W</span>
                                                <span class="cm-pill" data-day="Thursday">T</span>
                                                <span class="cm-pill" data-day="Friday">F</span>
                                                <span class="cm-pill" data-day="Saturday">S</span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="cm-section-title mb-1">EXCEPTIONS</div>
                                                <span class="cm-pill cm-pill--ghost">Skip dates…</span>
                                            </div>
                                            <button type="button" class="btn cm-btn cm-btn--sm cm-btn--accent">Add
                                                date</button>
                                        </div>
                                        <div class="cm-summary">
                                            Summary: Repeats weekly on Tue at 3:30 PM • Every 1 week • Ends after 8
                                            occurrences.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="cm-card">
                                <div class="cm-card-title">VENUE &amp; MAP</div>
                                <div class="cm-fields-group">
                                    <label class="cm-field">
                                        <span class="cm-section-title">VENUE</span>
                                        <input type="text" class="cm-input" id="venue"
                                            value="Riverside Sports Complex - Pitch 2">
                                    </label>
                                    <div class="cm-field">
                                        <span class="cm-section-title">ADDRESS</span>
                                        <div class="d-flex flex-column flex-sm-row gap-2">
                                            <input type="text" class="cm-input" id="address"
                                                value="100 Riverside Ave, Ottawa, CA">
                                            <button type="button" class="btn cm-btn cm-btn--sm cm-btn--maps">Open in
                                                Maps</button>
                                        </div>
                                    </div>
                                    <div class="cm-map-preview">
                                        <div class="cm-map-placeholder">
                                            Map preview (replace with embed in code)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="cm-aside">
                            <div class="cm-card cm-card-aside">
                                <div class="cm-section-block">
                                    <div class="cm-card-title">ROSTER</div>
                                    <div class="cm-fields-group">
                                        <label class="cm-field">
                                            <span class="cm-section-title">TEAM</span>
                                            <select class="cm-input" id="modalTeamSelect">
                                                <option value="">Select Team</option>
                                                @if (isset($clubTeams) && $clubTeams->count() > 0)
                                                    @foreach ($clubTeams as $index => $team)
                                                        <option value="{{ $team->id }}"
                                                            {{ $index === 0 ? 'selected' : '' }}>
                                                            {{ $team->name }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option value="" disabled>No teams available</option>
                                                @endif
                                            </select>
                                        </label>
                                        <div class="cm-player-grid">
                                            <div class="cm-player-empty">Select a team to view players</div>
                                        </div>

                                    </div>
                                </div>
                                <div class="cm-section-block">
                                    <div class="cm-card-title">ATTENDANCE</div>
                                    <div class="cm-toggle-group mt-3" id="rsvpToggle">
                                        <button type="button" class="active" data-value="RSVP">RSVP</button>
                                        <button type="button" data-value="No RSVP">No RSVP</button>
                                    </div>
                                    <div class="cm-section-helper mt-2">
                                        Players will be asked: Yes / Maybe / No
                                    </div>
                                </div>
                                <div class="cm-section-block">
                                    <div class="cm-card-title">ATTACHMENTS</div>
                                    <div class="cm-fields-group">
                                        <span class="cm-section-helper">
                                            Drag &amp; drop or click to upload
                                        </span>
                                        <button type="button"
                                            class="btn cm-btn cm-btn--sm cm-btn--upload">Upload</button>
                                    </div>
                                </div>
                                <div class="cm-section-block">
                                    <div class="cm-card-title">NOTIFICATIONS</div>
                                    <div class="cm-pill-row mt-3" id="notificationPills">
                                        <span class="cm-pill active" data-value="24h before">24h before</span>
                                        <span class="cm-pill" data-value="2h">2h</span>
                                        <span class="cm-pill" data-value="30m">30m</span>
                                    </div>
                                </div>
                                <!--
                                        <div class="cm-section-block">
                                            <div class="cm-card-title">ASSIGN COLOUR</div>
                                            <div class="cm-color-palette mt-3">
                                                <div class="cm-color-swatch" style="background-color: #ffffff;" data-color="#ffffff"></div>
                                                <div class="cm-color-swatch" style="background-color: #4caf50;" data-color="#4caf50"></div>
                                                <div class="cm-color-swatch" style="background-color: #2196f3;" data-color="#2196f3"></div>
                                                <div class="cm-color-swatch" style="background-color: #f44336;" data-color="#f44336"></div>
                                                <div class="cm-color-swatch" style="background-color: #ff9800;" data-color="#ff9800"></div>
                                                <div class="cm-color-swatch" style="background-color: #9c27b0;" data-color="#9c27b0"></div>
                                                <div class="cm-color-swatch" style="background-color: #00bcd4;" data-color="#00bcd4"></div>
                                                <div class="cm-color-swatch" style="background-color: #8bc34a;" data-color="#8bc34a"></div>
                                                <div class="cm-color-swatch" style="background-color: #ff5722;" data-color="#ff5722"></div>
                                                <div class="cm-color-swatch" style="background-color: #607d8b;" data-color="#607d8b"></div>
                                                <div class="cm-color-swatch" style="background-color: #795548;" data-color="#795548"></div>
                                                <div class="cm-color-swatch" style="background-color: #e91e63;" data-color="#e91e63"></div>
                                                <div class="cm-color-swatch" style="background-color: #3f51b5;" data-color="#3f51b5"></div>
                                                <div class="cm-color-swatch" style="background-color: #009688;" data-color="#009688"></div>
                                                <div class="cm-color-swatch" style="background-color: #ffc107;" data-color="#ffc107"></div>
                                                <div class="cm-color-swatch" style="background-color: #673ab7;" data-color="#673ab7"></div>
                                                <div class="cm-color-swatch" style="background-color: #cddc39;" data-color="#cddc39"></div>
                                                <div class="cm-color-swatch" style="background-color: #ffeb3b;" data-color="#ffeb3b"></div>
                                                <div class="cm-color-swatch" style="background-color: #ff9800;" data-color="#ff9800"></div>
                                                <div class="cm-color-swatch" style="background-color: #000000;" data-color="#000000"></div>
                                            </div>
                                        </div> -->
                                <button type="button" class="btn cm-btn cm-btn--primary cm-btn--lg cm-btn--block mt-3"
                                    id="createEventBtn">
                                    Create Event
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="cm-footer">

                        <div class="cm-footer-actions">
                            <button type="button" class="btn cm-btn cm-btn--ghost"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn cm-btn cm-btn--accent" id="previewEventBtn">Preview
                                Event</button>
                            <button type="button" class="btn cm-btn cm-btn--publish" id="publishEventBtn">Publish
                                Event</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tournament Directory Modal -->
    <div class="modal fade" id="tournamentDirectoryModal" tabindex="-1" aria-labelledby="tournamentDirectoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tournamentDirectoryModalLabel">Tournament Directory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe src="{{ route('player.tournaments.directory') }}" title="Tournament Directory"
                        data-default-url="{{ route('player.tournaments.directory') }}"
                        style="width: 100%; height: 80vh; border: 0;" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>


    <!-- Modals Container -->
    <div id="modals"></div>
@endsection

@section('footer_scripts')
    <script>
        // Modal Loading System (matching index.html approach)
        async function loadModal(id, file) {
            try {
                const res = await fetch(file);
                const html = await res.text();
                document.getElementById("modals").insertAdjacentHTML("beforeend", html);
                console.log(`Modal ${id} loaded successfully`);
            } catch (error) {
                console.error(`Error loading modal ${id}:`, error);
            }
        }

        // Load all modals immediately (like index.html)
        document.addEventListener('DOMContentLoaded', function() {
            // Load all modals synchronously like index.html
            // loadModal("modal1", "/assets/modals/training_ad_tile.html");
            // loadModal("modal2", "/assets/modals/training_ad_banner.html");
            // loadModal("modal3", "/assets/modals/training_ad_card.html");
            // loadModal("modal4", "/assets/modals/create_match_event_with_repeat.html");
            // loadModal("modal11", "/assets/modals/create_tournment.html");
            // loadModal("assignAward", "/assets/modals/assign_award_modal_dark.html");
            // loadModal("blogTemplate", "/assets/modals/blog_post_template_light_figma.html");
            // loadModal("assignTaskModal", "/assets/modals/assign_task_modal_figma.html");
            // loadModal("blockPostTemplate", "/assets/modals/blog_post_template_light_figma.html");
            // loadModal("createMatchEvent", "/assets/modals/create_match_event_with_repeat.html");
            // loadModal("upcomingGameRule", "/assets/modals/upcoming_games_rules_light.html");
            // loadModal("playerAttribute", "/assets/modals/PlayerAttributes.html");
            // loadModal("tournamentModal", "/assets/modals/tournament_engine.html");
        });


        document.addEventListener('DOMContentLoaded', function() {});
    </script>

    <!--chart-->
    <script>
        (function() {
            const chatModalEl = document.getElementById('clubChatModal');
            const bootstrapModalCtor = window.bootstrap && window.bootstrap.Modal ? window.bootstrap.Modal : null;
            const CURRENT_USER_ID = {{ (int) auth()->id() }};
            const messagesEl = chatModalEl ? chatModalEl.querySelector('#clubChatMessages') : null;
            const nameEl = chatModalEl ? chatModalEl.querySelector('#clubChatUserName') : null;
            const statusEl = chatModalEl ? chatModalEl.querySelector('#clubChatUserStatus') : null;
            const initialsEl = chatModalEl ? chatModalEl.querySelector('#clubChatUserInitials') : null;
            const formEl = chatModalEl ? chatModalEl.querySelector('#clubChatForm') : null;
            const inputEl = chatModalEl ? chatModalEl.querySelector('#clubChatInput') : null;
            const sendBtn = chatModalEl ? chatModalEl.querySelector('#clubSendBtn') : null;
            const modal = bootstrapModalCtor ?
                (typeof bootstrapModalCtor.getOrCreateInstance === 'function' ?
                    bootstrapModalCtor.getOrCreateInstance(chatModalEl) :
                    new bootstrapModalCtor(chatModalEl)) :
                null;

            let activeChatId = null;
            let pollTimer = null;

            const setSendEnabled = () => {
                if (!sendBtn || !inputEl) {
                    return;
                }
                const hasText = Boolean((inputEl.value || '').trim());
                sendBtn.disabled = !hasText || !activeChatId;
            };

            const formatTime = (value) => {
                try {
                    return new Date(value).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } catch (err) {
                    return '';
                }
            };

            const renderMessages = (list) => {
                if (!messagesEl) {
                    return;
                }

                if (!Array.isArray(list) || list.length === 0) {
                    messagesEl.innerHTML =
                        '<div class="text-muted text-center py-4">No messages yet. Start the conversation!</div>';
                    return;
                }

                messagesEl.innerHTML = '';
                list.forEach((msg) => {
                    const mine = Number(msg.sender_id) === Number(CURRENT_USER_ID);
                    const bubbleClass = mine ? 'agent' : 'contact';
                    const wrapper = document.createElement('div');
                    wrapper.className =
                        `d-flex flex-column ${mine ? 'align-items-end' : 'align-items-start'} mb-2`;
                    wrapper.innerHTML = `
                        <div class="club-chat-bubble ${bubbleClass}">
                            <div>${(msg.content || '').replace(/\n/g, '<br>')}</div>
                        </div>
                        <span class="club-chat-meta">${formatTime(msg.created_at)}</span>
                    `;
                    messagesEl.appendChild(wrapper);
                });

                messagesEl.scrollTop = messagesEl.scrollHeight;
            };

            const resetMessages = (label = 'Loading conversation...') => {
                if (messagesEl) {
                    messagesEl.innerHTML = `<div class="text-muted text-center py-4">${label}</div>`;
                }
            };

            const stopPolling = () => {
                if (pollTimer) {
                    clearInterval(pollTimer);
                    pollTimer = null;
                }
            };

            const loadMessages = async (chatId, silent = false) => {
                if (!chatId) {
                    return;
                }
                if (!silent) {
                    resetMessages();
                }
                try {
                    const response = await fetch(`/player/chat/messages/${chatId}`);
                    if (!response.ok) {
                        throw new Error('Unable to load messages');
                    }
                    const data = await response.json();
                    renderMessages(data);
                } catch (error) {
                    console.warn(error);
                    if (!silent) {
                        resetMessages('Unable to load messages right now.');
                    }
                }
            };

            const openChatModal = ({
                name,
                status = '',
                initials = '',
                chatId
            }) => {
                if (!chatId) {
                    return;
                }

                activeChatId = chatId;
                if (nameEl) {
                    nameEl.textContent = name || 'Chat';
                }
                if (statusEl) {
                    statusEl.textContent = status || '';
                }
                if (initialsEl) {
                    initialsEl.textContent = initials || (name ? name.slice(0, 2).toUpperCase() : 'CC');
                }

                resetMessages();
                if (modal) {
                    modal.show();
                    loadMessages(chatId);
                    stopPolling();
                    pollTimer = setInterval(() => loadMessages(chatId, true), 5000);
                    setSendEnabled();
                } else if (typeof window.openChat === 'function') {
                    window.openChat(name, chatId);
                } else {
                    window.location.href = `/player/chat?chat_id=${encodeURIComponent(chatId)}`;
                }
            };

            const initiateDirectChat = async (userId, meta = {}) => {
                if (!userId) {
                    return;
                }
                try {
                    const response = await fetch(`/player/chat/initiate/${encodeURIComponent(userId)}`);
                    const payload = await response.json();
                    if (!response.ok || !payload?.chat_id) {
                        throw new Error(payload?.error || 'Unable to start chat.');
                    }
                    openChatModal({
                        chatId: payload.chat_id,
                        name: meta.name,
                        status: meta.status,
                        initials: meta.initials
                    });
                } catch (error) {
                    console.warn(error);
                    alert(error.message || 'Unable to open chat right now.');
                }
            };

            const contactList = document.getElementById('clubContactList');
            contactList?.addEventListener('click', (event) => {
                const item = event.target.closest('.chat-item');
                if (!item || item.classList.contains('disabled')) {
                    return;
                }
                event.preventDefault();
                const userId = item.dataset.userId;
                const name = item.dataset.contactLabel || item.querySelector('.name')?.textContent?.trim() ||
                    'Chat';
                const status = item.dataset.contactTagline || item.dataset.contactStatus || '';
                const initials = item.dataset.contactInitials || name.split(' ').map((part) => part[0]).join('')
                    .slice(0, 2)
                    .toUpperCase();
                const chatId = item.dataset.chatId;

                if (chatId) {
                    openChatModal({
                        chatId,
                        name,
                        status,
                        initials
                    });
                    return;
                }

                if (!userId) {
                    return;
                }
                initiateDirectChat(userId, {
                    name,
                    status,
                    initials
                });
            });

            formEl?.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (!activeChatId || !inputEl) {
                    return;
                }
                const message = (inputEl.value || '').trim();
                if (!message) {
                    return;
                }

                sendBtn.disabled = true;
                try {
                    const response = await fetch(`{{ route('player.chat.send') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.content || ''
                        },
                        body: JSON.stringify({
                            chat_id: activeChatId,
                            message
                        })
                    });

                    if (!response.ok) {
                        const payload = await response.json().catch(() => ({}));
                        throw new Error(payload?.error || 'Unable to send message.');
                    }

                    inputEl.value = '';
                    if (modal) {
                        loadMessages(activeChatId);
                    }
                } catch (error) {
                    console.warn(error);
                    alert(error.message || 'Failed to send message.');
                } finally {
                    setSendEnabled();
                }
            });

            inputEl?.addEventListener('input', setSendEnabled);

            if (chatModalEl && modal) {
                chatModalEl.addEventListener('hidden.bs.modal', () => {
                    stopPolling();
                    activeChatId = null;
                    resetMessages('Select a contact to start messaging.');
                    if (inputEl) {
                        inputEl.value = '';
                    }
                    setSendEnabled();
                });
            }

            window.clubChat = {
                open: openChatModal,
                initiate: initiateDirectChat,
                loadMessages
            };
        })();
    </script>

    <script>
        window.App = Object.assign(window.App || {}, {
            events: {!! json_encode($events ?? []) !!}
        });
    </script>

    <!-- Invite Link Sharing Functions -->
    <script>
        function copyInviteLink() {
            const inviteLink = document.getElementById('inviteLink');
            inviteLink.select();
            inviteLink.setSelectionRange(0, 99999); // For mobile devices

            try {
                document.execCommand('copy');
                showToast('Invite link copied to clipboard!', 'success');
            } catch (err) {
                // Fallback for modern browsers
                navigator.clipboard.writeText(inviteLink.value).then(function() {
                    showToast('Invite link copied to clipboard!', 'success');
                }).catch(function() {
                    showToast('Failed to copy link. Please copy manually.', 'error');
                });
            }
        }

        function shareViaWhatsApp() {
            const inviteLink = document.getElementById('inviteLink').value;
            const message = `Join my club! Click this link: ${inviteLink}`;
            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }

        function shareViaEmail() {
            const inviteLink = document.getElementById('inviteLink').value;
            const subject = 'Join My Club!';
            const body = `Hi! I'd like to invite you to join my club. Click this link to join: ${inviteLink}`;
            const mailtoUrl = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
            window.open(mailtoUrl);
        }

        function shareViaSMS() {
            const inviteLink = document.getElementById('inviteLink').value;
            const message = `Join my club! Click this link: ${inviteLink}`;
            const smsUrl = `sms:?body=${encodeURIComponent(message)}`;
            window.open(smsUrl);
        }

        function shareViaSocial() {
            const inviteLink = document.getElementById('inviteLink').value;
            const message = `Join my club! Click this link: ${inviteLink}`;

            if (navigator.share) {
                navigator.share({
                    title: 'Join My Club',
                    text: message,
                    url: inviteLink
                }).catch(console.error);
            } else {
                // Fallback for browsers that don't support Web Share API
                showToast('Copy the link and share it on your social media!', 'info');
            }
        }

        function showToast(message, type = 'info') {
            // Create a simple toast notification
            const toast = document.createElement('div');
            toast.className =
                `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

            document.body.appendChild(toast);

            // Auto-remove after 3 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 3000);
        }
    </script>

    <script>
        (function() {
            const list = document.querySelector('.tournament-chat-list');
            if (!list) return;

            const csrf =
                document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // Helper: POST join and navigate
            async function joinAndGo(tournamentId, button, rowEl) {
                if (!tournamentId || !csrf) {
                    alert('Unable to open chat. Please refresh and try again.');
                    return;
                }

                if (button) {
                    button.disabled = true;
                    button.classList.add('disabled');
                }

                try {
                    const res = await fetch(`/tournaments/${encodeURIComponent(tournamentId)}/chat/join`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({}),
                        redirect: 'manual',
                    });

                    // Attempt to parse JSON, or surface a clear message if HTML/redirect
                    const ct = res.headers.get('content-type') || '';
                    const data = ct.includes('application/json') ? await res.json() : null;

                    if (!res.ok) {
                        const msg =
                            data?.message ||
                            data?.error ||
                            (res.status === 419 ?
                                'Session expired (419). Please refresh and try again.' :
                                res.status === 401 || res.status === 302 ?
                                'Please log in to join this chat.' :
                                `Unable to join tournament chat (${res.status}).`);
                        throw new Error(msg);
                    }

                    if (!data?.chat_id) throw new Error('Chat not available for this tournament yet.');

                    const redirect = data.redirect_url ||
                        `/player/chat?chat_id=${encodeURIComponent(data.chat_id)}`;

                    const name = (rowEl?.dataset.tournamentName || 'Tournament Chat').trim();
                    const location = rowEl?.dataset.tournamentLocation || '';
                    const status = rowEl?.dataset.tournamentStatus || '';
                    const initials = rowEl?.dataset.tournamentInitials || 'TC';

                    if (window.clubChat && typeof window.clubChat.open === 'function') {
                        window.clubChat.open({
                            chatId: data.chat_id,
                            name: name,
                            status: [status, location].filter(Boolean).join(' • '),
                            initials: initials
                        });
                    } else {
                        window.location.href = redirect;
                    }
                } catch (err) {
                    console.warn('Tournament chat join failed:', err);
                    alert(err.message || 'Unable to join tournament chat right now.');
                } finally {
                    if (button && !document.hidden) {
                        button.disabled = false;
                        button.classList.remove('disabled');
                    }
                }
            }

            // Click handling (delegation)
            list.addEventListener('click', (ev) => {
                const btn = ev.target.closest('.open-tournament-chat');
                const item = ev.target.closest('.tournament-chat-item');

                if (!btn && !item) return;

                // If a “Closed” row, do nothing
                const row = btn ? btn.closest('.tournament-chat-item') : item;
                const isClosed = row?.classList.contains('disabled') || row?.dataset.tournamentStatus ===
                    'closed';
                if (isClosed) return;

                // If clicking the button, stop row click duplication
                if (btn) ev.stopPropagation();

                const tournamentId = (btn || row)?.getAttribute('data-tournament-id');
                joinAndGo(tournamentId, btn, row);
            });
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEl = document.getElementById('tournamentDirectoryModal');
            if (!modalEl) {
                return;
            }

            const links = document.querySelectorAll('[data-player-tournament-link]');
            if (!links.length) {
                return;
            }

            const iframe = modalEl.querySelector('iframe');
            const defaultUrl = iframe?.dataset.defaultUrl || links[0].dataset.url || links[0].getAttribute('href');
            let pendingUrl = defaultUrl;

            const normalizeUrl = (url) => url || defaultUrl;

            const loadFrame = (url) => {
                if (!iframe) {
                    return;
                }
                const target = normalizeUrl(url);
                if (!target) {
                    return;
                }
                if (iframe.getAttribute('src') !== target) {
                    iframe.setAttribute('src', target);
                }
            };

            const openModal = (url) => {
                const target = normalizeUrl(url);
                if (!target) {
                    return;
                }

                if (!window.bootstrap || !window.bootstrap.Modal) {
                    window.open(target, '_blank');
                    return;
                }

                pendingUrl = target;
                const instance = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                instance.show();
            };

            links.forEach((link) => {
                link.addEventListener('click', (event) => {
                    const targetUrl = link.dataset.url || link.getAttribute('href');
                    if (!targetUrl) {
                        return;
                    }

                    if (window.bootstrap && modalEl) {
                        event.preventDefault();
                        openModal(targetUrl);
                    }
                });
            });

            if (window.bootstrap && modalEl) {
                modalEl.addEventListener('show.bs.modal', () => {
                    loadFrame(pendingUrl);
                });
            }
        });
    </script>

    <!-- Create Match Modal JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createMatchModal = document.getElementById('createMatchModal');
            if (!createMatchModal) return;

            // Initialize form with default values
            initializeForm();

            // Add event listeners
            addEventListeners();

            // Also add event listeners when modal is shown
            createMatchModal.addEventListener('shown.bs.modal', function() {
                console.log('Modal shown, re-attaching event listeners');
                // Small delay to ensure DOM is ready
                setTimeout(() => {
                    const teamSelect = document.getElementById('modalTeamSelect');
                    console.log('Team select in modal shown:', teamSelect);
                    addEventListeners();

                    // Load players if a team is already selected
                    if (teamSelect && teamSelect.value) {
                        console.log('Modal shown with team selected, loading players:', teamSelect
                            .value);
                        loadTeamPlayers(teamSelect.value);
                    }
                }, 100);
            });

            function initializeForm() {
                // Set current date as default
                const today = new Date();
                const formattedDate = today.toISOString().split('T')[0];
                const dateInput = document.getElementById('eventDate');
                if (dateInput) dateInput.value = formattedDate;

                // Set current time + 1 hour as default kickoff
                const now = new Date();
                now.setHours(now.getHours() + 1);
                const formattedTime = now.toTimeString().slice(0, 5);
                const kickoffInput = document.getElementById('kickoffTime');
                if (kickoffInput) kickoffInput.value = formattedTime;

                // Set arrival time (30 minutes before kickoff)
                const arrivalTime = new Date(now);
                arrivalTime.setMinutes(arrivalTime.getMinutes() - 30);
                const formattedArrival = arrivalTime.toTimeString().slice(0, 5);
                const arrivalInput = document.getElementById('arrivalTime');
                if (arrivalInput) arrivalInput.value = formattedArrival;

                // Set end time (2 hours after kickoff)
                const endTime = new Date(now);
                endTime.setHours(endTime.getHours() + 2);
                const formattedEnd = endTime.toTimeString().slice(0, 5);
                const endInput = document.getElementById('endTime');
                if (endInput) endInput.value = formattedEnd;

                // Set default at time same as kickoff
                const atInput = document.getElementById('atTime');
                if (atInput) atInput.value = formattedTime;
            }

            function addEventListeners() {
                // Home/Away toggle
                const homeAwayToggle = document.getElementById('homeAwayToggle');
                if (homeAwayToggle) {
                    homeAwayToggle.addEventListener('click', function(e) {
                        if (e.target.tagName === 'BUTTON') {
                            homeAwayToggle.querySelectorAll('button').forEach(btn => btn.classList.remove(
                                'active'));
                            e.target.classList.add('active');
                        }
                    });
                }

                // RSVP toggle
                const rsvpToggle = document.getElementById('rsvpToggle');
                if (rsvpToggle) {
                    rsvpToggle.addEventListener('click', function(e) {
                        if (e.target.tagName === 'BUTTON') {
                            rsvpToggle.querySelectorAll('button').forEach(btn => btn.classList.remove(
                                'active'));
                            e.target.classList.add('active');
                        }
                    });
                }

                // Day pills
                const dayPills = document.getElementById('dayPills');
                if (dayPills) {
                    dayPills.addEventListener('click', function(e) {
                        if (e.target.classList.contains('cm-pill')) {
                            e.target.classList.toggle('active');
                        }
                    });
                }

                // Notification pills
                const notificationPills = document.getElementById('notificationPills');
                if (notificationPills) {
                    notificationPills.addEventListener('click', function(e) {
                        if (e.target.classList.contains('cm-pill')) {
                            e.target.classList.toggle('active');
                        }
                    });
                }

                // Color swatches
                const colorSwatches = document.querySelectorAll('.cm-color-swatch');
                colorSwatches.forEach(swatch => {
                    swatch.addEventListener('click', function() {
                        colorSwatches.forEach(s => s.classList.remove('selected'));
                        this.classList.add('selected');
                    });
                });

                // Repeat toggle
                const repeatToggle = document.getElementById('repeatToggle');
                const repeatSection = document.getElementById('repeatSection');
                if (repeatToggle && repeatSection) {
                    repeatToggle.addEventListener('change', function() {
                        if (this.checked) {
                            repeatSection.style.display = 'block';
                        } else {
                            repeatSection.style.display = 'none';
                        }
                    });
                }

                // Team selection - load players
                const teamSelect = document.getElementById('modalTeamSelect');
                if (teamSelect) {
                    console.log('Team select element found:', teamSelect);
                    console.log('Current team select value:', teamSelect.value);
                    console.log('Team select options:', Array.from(teamSelect.options).map(opt => ({
                        value: opt.value,
                        text: opt.text
                    })));

                    // Show available teams for debugging
                    const availableTeams = Array.from(teamSelect.options)
                        .filter(opt => opt.value && opt.value !== '')
                        .map(opt => ({
                            id: opt.value,
                            name: opt.text
                        }));
                    console.log('Available teams:', availableTeams);

                    teamSelect.addEventListener('change', function() {
                        const teamId = this.value;
                        console.log('Team changed to:', teamId);
                        if (teamId) {
                            loadTeamPlayers(teamId);
                        } else {
                            clearPlayerGrid();
                        }
                    });

                    // Load players if a team is already selected
                    if (teamSelect.value) {
                        console.log('Team select already has value, loading players:', teamSelect.value);
                        setTimeout(() => {
                            loadTeamPlayers(teamSelect.value);
                        }, 500);
                    }
                } else {
                    console.log('Team select element not found');
                }

                // Create Event button
                const createEventBtn = document.getElementById('createEventBtn');
                if (createEventBtn) {
                    createEventBtn.addEventListener('click', function() {
                        saveEvent(false);
                    });
                }

                const publishEventHeaderBtn = document.getElementById('publishEventHeaderBtn');
                if (publishEventHeaderBtn) {
                    publishEventHeaderBtn.addEventListener('click', function() {
                        saveEvent(true);
                    });
                }

                const saveDraftHeaderBtn = document.getElementById('saveDraftHeaderBtn');
                if (saveDraftHeaderBtn) {
                    saveDraftHeaderBtn.addEventListener('click', function() {
                        saveEvent(false);
                    });
                }

                // Publish Event button
                const publishEventBtn = document.getElementById('publishEventBtn');
                if (publishEventBtn) {
                    publishEventBtn.addEventListener('click', function() {
                        saveEvent(true);
                    });
                }

                // Preview Event button
                const previewEventBtn = document.getElementById('previewEventBtn');
                if (previewEventBtn) {
                    previewEventBtn.addEventListener('click', function() {
                        previewEvent();
                    });
                }
            }

            function saveEvent(publish = false) {
                // Collect form data
                const formData = {
                    title: document.getElementById('eventTitle')?.value || '',
                    match_type: document.getElementById('matchType')?.value || '',
                    opponent: document.getElementById('opponent')?.value || '',
                    home_away: document.querySelector('#homeAwayToggle .active')?.getAttribute('data-value') ||
                        'Home',
                    date: document.getElementById('eventDate')?.value || '',
                    kickoff: document.getElementById('kickoffTime')?.value || '',
                    arrival: document.getElementById('arrivalTime')?.value || '',
                    end: document.getElementById('endTime')?.value || '',
                    frequency: document.getElementById('frequency')?.value || '',
                    every: document.getElementById('every')?.value || '',
                    at: document.getElementById('atTime')?.value || '',
                    ends: document.getElementById('ends')?.value || '',
                    team: document.getElementById('modalTeamSelect')?.value || '',
                    rsvp: document.querySelector('#rsvpToggle .active')?.getAttribute('data-value') || 'RSVP',
                    venue: document.getElementById('venue')?.value || '',
                    address: document.getElementById('address')?.value || '',
                    notifications: Array.from(document.querySelectorAll('#notificationPills .cm-pill.active'))
                        .map(pill => pill.getAttribute('data-value')),
                    days: Array.from(document.querySelectorAll('#dayPills .cm-pill.active'))
                        .map(pill => pill.getAttribute('data-day')),
                    selected_color: document.querySelector('.cm-color-swatch.selected')?.getAttribute(
                        'data-color') || '#00bcd4',
                    repeat_enabled: document.getElementById('repeatToggle')?.checked || false,
                    selected_players: Array.from(document.querySelectorAll('.cm-player-chip.selected'))
                        .map(card => card.getAttribute('data-player-id')),
                    status: publish ? 'published' : 'draft'
                };

                // Validate required fields
                if (!formData.title || !formData.date || !formData.kickoff) {
                    alert('Please fill in all required fields (Title, Date, Kickoff)');
                    return;
                }

                // Format description
                const description = formatEventDescription(formData);

                // Show loading state
                const createBtn = document.getElementById('createEventBtn');
                const originalText = createBtn.innerHTML;
                createBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
                createBtn.disabled = true;

                // Send to server
                fetch('/club/events/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            title: formData.title,
                            description: description,
                            status: formData.status,
                            event_data: formData,
                            event_date: formData.date,
                            event_time: formData.kickoff,
                            location: formData.venue
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            createBtn.innerHTML = '<i class="bi bi-check me-2"></i>Created!';
                            createBtn.classList.remove('btn-gradient');
                            createBtn.classList.add('btn-success');

                            setTimeout(() => {
                                // Close modal and reload or redirect
                                bootstrap.Modal.getInstance(createMatchModal).hide();
                                if (publish) {
                                    window.location.href = '/club/events';
                                } else {
                                    location.reload();
                                }
                            }, 1500);
                        } else {
                            alert('Error creating event: ' + data.message);
                            createBtn.innerHTML = originalText;
                            createBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error creating event:', error);
                        alert('Error creating event. Please try again.');
                        createBtn.innerHTML = originalText;
                        createBtn.disabled = false;
                    });
            }

            function previewEvent() {
                const formData = {
                    title: document.getElementById('eventTitle')?.value || '',
                    match_type: document.getElementById('matchType')?.value || '',
                    opponent: document.getElementById('opponent')?.value || '',
                    home_away: document.querySelector('#homeAwayToggle .active')?.getAttribute('data-value') ||
                        'Home',
                    date: document.getElementById('eventDate')?.value || '',
                    kickoff: document.getElementById('kickoffTime')?.value || '',
                    arrival: document.getElementById('arrivalTime')?.value || '',
                    end: document.getElementById('endTime')?.value || '',
                    frequency: document.getElementById('frequency')?.value || '',
                    every: document.getElementById('every')?.value || '',
                    at: document.getElementById('atTime')?.value || '',
                    ends: document.getElementById('ends')?.value || '',
                    team: document.getElementById('modalTeamSelect')?.value || '',
                    rsvp: document.querySelector('#rsvpToggle .active')?.getAttribute('data-value') || 'RSVP',
                    venue: document.getElementById('venue')?.value || '',
                    address: document.getElementById('address')?.value || '',
                    notifications: Array.from(document.querySelectorAll('#notificationPills .cm-pill.active'))
                        .map(pill => pill.getAttribute('data-value')),
                    days: Array.from(document.querySelectorAll('#dayPills .cm-pill.active'))
                        .map(pill => pill.getAttribute('data-day')),
                    selected_color: document.querySelector('.cm-color-swatch.selected')?.getAttribute(
                        'data-color') || '#00bcd4',
                    repeat_enabled: document.getElementById('repeatToggle')?.checked || false,
                    selected_players: Array.from(document.querySelectorAll('.cm-player-chip.selected'))
                        .map(card => card.getAttribute('data-player-id'))
                };

                const description = formatEventDescription(formData);
                alert('Event Preview:\n\n' + description);
            }

            function formatEventDescription(data) {
                let description = `**${data.title}**\n\n`;

                description += `**Match Details:**\n`;
                description += `• Type: ${data.match_type || 'Not specified'}\n`;
                description += `• Opponent: ${data.opponent || 'Not specified'}\n`;
                description += `• Location: ${data.home_away}\n`;
                description += `• Team: ${data.team || 'Not specified'}\n\n`;

                description += `**Schedule:**\n`;
                description += `• Date: ${data.date}\n`;
                description += `• Kickoff: ${data.kickoff}\n`;
                description += `• Arrival: ${data.arrival}\n`;
                description += `• End: ${data.end}\n\n`;

                if (data.venue || data.address) {
                    description += `**Venue:**\n`;
                    if (data.venue) description += `• Venue: ${data.venue}\n`;
                    if (data.address) description += `• Address: ${data.address}\n`;
                    description += `\n`;
                }

                if (data.repeat_enabled && data.frequency && data.frequency !== 'One-time') {
                    description += `**Recurrence:**\n`;
                    description += `• Frequency: ${data.frequency}\n`;
                    description += `• Every: ${data.every || '1'}\n`;
                    description += `• At: ${data.at}\n`;
                    description += `• Ends: ${data.ends || '8'} occurrences\n`;
                    if (data.days.length > 0) {
                        description += `• Days: ${data.days.join(', ')}\n`;
                    }
                    description += `\n`;
                }

                description += `**Settings:**\n`;
                description += `• RSVP: ${data.rsvp}\n`;
                if (data.notifications.length > 0) {
                    description += `• Notifications: ${data.notifications.join(', ')}\n`;
                }
                if (data.selected_color) {
                    description += `• Event Color: ${data.selected_color}\n`;
                }
                if (data.selected_players && data.selected_players.length > 0) {
                    description += `• Selected Players: ${data.selected_players.length} players\n`;
                }

                return description;
            }

            // Load team players
            function loadTeamPlayers(teamId) {
                console.log('Loading players for team:', teamId);
                const playerGrid = document.querySelector('.cm-player-grid');
                if (!playerGrid) {
                    console.log('Player grid not found');
                    return;
                }

                console.log('Player grid found:', playerGrid);

                // Show loading state
                playerGrid.innerHTML =
                    '<div class="cm-player-empty"><div class="spinner-border text-info" role="status"></div><div class="mt-3">Loading players...</div></div>';

                // Fetch team players
                const url = `/club/team/${teamId}/players`;
                console.log('Fetching from URL:', url);

                fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success && data.players) {
                            displayTeamPlayers(data.players);
                        } else {
                            playerGrid.innerHTML =
                                '<div class="cm-player-empty">No players found for this team</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading team players:', error);
                        playerGrid.innerHTML =
                            '<div class="cm-player-empty text-danger">Error loading players</div>';
                    });
            }

            // Display team players
            function displayTeamPlayers(players) {
                const playerGrid = document.querySelector('.cm-player-grid');
                if (!playerGrid) return;

                if (!players || players.length === 0) {
                    playerGrid.innerHTML = '<div class="cm-player-empty">No players available for this team</div>';
                    return;
                }

                let html = '';
                players.forEach(player => {
                    const initials = getPlayerInitials(player.name);
                    html += `
                    <div class="cm-player-chip" data-player-id="${player.id}">
                        <div class="cm-player-avatar">${initials}</div>
                        <div class="cm-player-name">${player.name}</div>
                    </div>
                `;
                });

                playerGrid.innerHTML = html;

                // Add click handlers for player selection
                playerGrid.querySelectorAll('.cm-player-chip').forEach(card => {
                    card.addEventListener('click', function() {
                        this.classList.toggle('selected');
                    });
                });
            }

            // Clear player grid
            function clearPlayerGrid() {
                const playerGrid = document.querySelector('.cm-player-grid');
                if (playerGrid) {
                    playerGrid.innerHTML = '<div class="cm-player-empty">Select a team to view players</div>';
                }
            }

            // Get player initials
            function getPlayerInitials(name) {
                return name.split(' ').map(word => word.charAt(0)).join('').toUpperCase().substring(0, 2);
            }

            // Test function to manually load players
            window.testLoadPlayers = function() {
                const teamSelect = document.getElementById('modalTeamSelect');
                const teamId = teamSelect ? teamSelect.value : '1';
                console.log('Test loading players for team:', teamId);
                console.log('Team select element:', teamSelect);
                console.log('Available options:', teamSelect ? Array.from(teamSelect.options).map(opt => ({
                    value: opt.value,
                    text: opt.text
                })) : 'No team select found');
                if (teamId) {
                    loadTeamPlayers(teamId);
                }
            };

            // Also test the API directly
            window.testAPI = function() {
                const teamSelect = document.getElementById('modalTeamSelect');
                const teamId = teamSelect ? teamSelect.value : null;

                if (!teamId) {
                    console.log('No team selected for API test');
                    return;
                }

                console.log('Testing API with team ID:', teamId);

                fetch(`/club/team/${teamId}/players`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Direct API test response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Direct API test response data:', data);
                    })
                    .catch(error => {
                        console.error('Direct API test error:', error);
                    });
            };
        });
    </script>

    <!-- Admin Dashboard Card Styles -->
    <style>
        .support-box {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .support-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .l-bg-green {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .l-bg-orange {
            background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%);
            color: white;
        }

        .l-bg-cyan {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            color: white;
        }

        .l-bg-purple {
            background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
            color: white;
        }

        .support-box .icon {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .support-box .text {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .support-box h3 {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .support-box h3 i {
            font-size: 20px;
        }

        .support-box small {
            font-size: 12px;
            opacity: 0.9;
            display: block;
            margin-top: 5px;
        }

        .chart {
            width: 60px;
            height: 60px;
            margin: 0 auto;
        }

        .chart canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .m-b-10 {
            margin-bottom: 10px;
        }

        .m-b-0 {
            margin-bottom: 0;
        }

        .displayblock {
            display: block;
        }
    </style>

    <!-- Create Match Modal Styles -->
    <style>
        .create-match-modal {
            position: relative;
            background: radial-gradient(circle at top left, rgba(14, 165, 233, 0.28), transparent 52%),
                radial-gradient(circle at top right, rgba(99, 102, 241, 0.22), transparent 55%),
                #0b1120;
            color: #e2e8f0;
            border-radius: 28px;
            padding: 36px 40px;
            box-shadow: 0 28px 60px rgba(8, 11, 26, 0.65);
        }

        .create-match-modal .btn-close {
            position: absolute;
            top: 20px;
            right: 20px;
            filter: invert(1);
            opacity: 0.65;
        }

        .create-match-modal .btn-close:hover,
        .create-match-modal .btn-close:focus {
            opacity: 1;
        }

        .create-match-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            padding-right: 56px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.16);
            padding-bottom: 24px;
            margin-bottom: 32px;
        }

        .create-match-title {
            font-size: 1.9rem;
            font-weight: 600;
            color: #f8fafc;
            margin-bottom: 0.35rem;
        }

        .create-match-subtitle {
            color: rgba(148, 163, 184, 0.78);
            max-width: 480px;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 0;
        }

        .create-match-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin-right: 2.5rem;
        }

        .create-match-modal .cm-btn {
            border: none;
            border-radius: 999px;
            padding: 0.65rem 1.6rem;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.01em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: rgba(15, 23, 42, 0.65);
            color: #e2e8f0;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, opacity 0.2s ease;
        }

        .create-match-modal .cm-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.35);
        }

        .create-match-modal .cm-btn:hover {
            transform: translateY(-1px);
        }

        .create-match-modal .cm-btn--publish {
            background: linear-gradient(120deg, #22d3ee, #22c55e);
            color: #f8fafc;
            box-shadow: 0 14px 38px rgba(34, 197, 94, 0.35);
        }

        .create-match-modal .cm-btn--draft {
            background: linear-gradient(120deg, #6366f1, #a855f7);
            color: #f8fafc;
            box-shadow: 0 14px 38px rgba(99, 102, 241, 0.35);
        }

        .create-match-modal .cm-btn--ghost {
            background: rgba(15, 23, 42, 0.6);
            color: rgba(226, 232, 240, 0.85);
            border: 1px solid rgba(148, 163, 184, 0.28);
            box-shadow: none;
        }

        .create-match-modal .cm-btn--ghost:hover {
            background: rgba(30, 41, 59, 0.9);
        }

        .create-match-modal .cm-btn--accent {
            background: linear-gradient(120deg, #6366f1, #0ea5e9);
            color: #f8fafc;
            box-shadow: 0 12px 32px rgba(99, 102, 241, 0.35);
        }

        .create-match-modal .cm-btn--maps {
            background: linear-gradient(120deg, #2dd4bf, #0ea5e9);
            color: #022c22;
            box-shadow: 0 10px 26px rgba(45, 212, 191, 0.35);
        }

        .create-match-modal .cm-btn--upload {
            background: linear-gradient(120deg, #f59e0b, #f97316);
            color: #0f172a;
            font-weight: 700;
            box-shadow: 0 12px 32px rgba(245, 158, 11, 0.35);
        }

        .create-match-modal .cm-btn--primary {
            background: linear-gradient(120deg, #38bdf8, #818cf8);
            color: #0f172a;
            font-weight: 700;
            box-shadow: 0 18px 44px rgba(56, 189, 248, 0.4);
        }

        .create-match-modal .cm-btn--outline {
            background: transparent;
            border: 1px solid rgba(148, 163, 184, 0.28);
            color: rgba(226, 232, 240, 0.85);
        }

        .create-match-modal .cm-btn--sm {
            padding: 0.45rem 1.1rem;
            font-size: 0.8rem;
            border-radius: 14px;
        }

        .create-match-modal .cm-btn--lg {
            padding: 0.85rem 1.75rem;
            font-size: 1rem;
        }

        .create-match-modal .cm-btn--block {
            width: 100%;
        }

        .create-match-layout {
            display: grid;
            gap: 28px;
            grid-template-columns: 1fr;
        }

        .create-match-layout .cm-main,
        .create-match-layout .cm-aside {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        @media (min-width: 1200px) {
            .create-match-layout {
                grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr);
            }
        }

        .create-match-modal .cm-card {
            background: linear-gradient(
                145deg,
                color-mix(in srgb, var(--card-bg) 94%, rgba(255, 255, 255, 0.9)),
                color-mix(in srgb, var(--card-bg) 88%, rgba(15, 23, 42, 0.1))
            );
            border: 1px solid color-mix(in srgb, var(--border) 60%, transparent);
            border-radius: 26px;
            padding: 28px;
            box-shadow: inset 0 1px 0 rgba(148, 163, 184, 0.08), 0 22px 48px -30px rgba(15, 23, 42, 0.18);
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }
        [data-bs-theme='dark'] body .create-match-modal .cm-card {
            background: linear-gradient(145deg, rgba(15, 23, 42, 0.95), rgba(12, 18, 35, 0.85));
            border-color: rgba(148, 163, 184, 0.12);
            box-shadow: inset 0 1px 0 rgba(148, 163, 184, 0.1), 0 22px 48px -30px rgba(15, 23, 42, 0.9);
        }

        .create-match-modal .cm-card+.cm-card {
            margin-top: 4px;
        }

        .create-match-modal .cm-card-title {
            font-size: 0.78rem;
            letter-spacing: 0.32em;
            font-weight: 700;
            color: color-mix(in srgb, var(--text-primary) 70%, transparent);
        }
        [data-bs-theme='dark'] body .create-match-modal .cm-card-title {
            color: rgba(226, 232, 240, 0.75);
        }

        .create-match-modal .cm-section-title {
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.28em;
            color: color-mix(in srgb, var(--text-secondary) 75%, transparent);
        }
        [data-bs-theme='dark'] body .create-match-modal .cm-section-title {
            color: rgba(148, 163, 184, 0.72);
        }

        .create-match-modal .cm-section-helper {
            font-size: 0.76rem;
            color: color-mix(in srgb, var(--text-secondary) 70%, transparent);
            line-height: 1.4;
        }

        .create-match-modal label span.cm-section-title {
            color: color-mix(in srgb, var(--text-secondary) 80%, transparent);
        }

        .create-match-modal .cm-fields-group {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-top: 18px;
        }

        .create-match-modal .cm-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .create-match-modal .cm-field .cm-input {
            margin-top: 4px;
        }

        .create-match-modal .cm-grid-two {
            display: grid;
            gap: 18px;
        }

        .create-match-modal .cm-grid-three {
            display: grid;
            gap: 18px;
        }

        @media (min-width: 768px) {
            .create-match-modal .cm-grid-two {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .create-match-modal .cm-grid-three {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .create-match-modal .cm-input {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid color-mix(in srgb, var(--border) 60%, transparent);
            border-radius: 18px;
            padding: 14px 16px;
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .create-match-modal .cm-input::placeholder {
            color: color-mix(in srgb, var(--text-secondary) 55%, transparent);
        }

        .create-match-modal .cm-input:focus {
            outline: none;
            border-color: rgba(56, 189, 248, 0.8);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.3);
            background: rgba(9, 14, 28, 0.98);
        }

        .create-match-modal select.cm-input {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image:
                linear-gradient(45deg, transparent 50%, rgba(148, 163, 184, 0.6) 50%),
                linear-gradient(135deg, rgba(148, 163, 184, 0.6) 50%, transparent 50%);
            background-position:
                calc(100% - 18px) calc(50% - 3px),
                calc(100% - 13px) calc(50% - 3px);
            background-size: 7px 7px;
            background-repeat: no-repeat;
            padding-right: 42px;
        }

        .create-match-modal .cm-toggle-group {
            display: inline-flex;
            gap: 6px;
            background: rgba(15, 23, 42, 0.85);
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.2);
            padding: 6px;
        }

        .create-match-modal .cm-toggle-group button {
            border: none;
            background: transparent;
            color: rgba(226, 232, 240, 0.65);
            font-weight: 600;
            border-radius: 999px;
            padding: 8px 20px;
            transition: color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .create-match-modal .cm-toggle-group button:hover {
            color: rgba(226, 232, 240, 0.92);
        }

        .create-match-modal .cm-toggle-group button.active {
            background: linear-gradient(120deg, #38bdf8, #818cf8);
            color: #0b1120;
            box-shadow: 0 12px 30px rgba(56, 189, 248, 0.4);
        }

        .create-match-modal .cm-pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .create-match-modal .cm-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 18px;
            border-radius: 999px;
            background: rgba(30, 41, 59, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.18);
            color: rgba(226, 232, 240, 0.78);
            font-size: 0.82rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            min-width: 48px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
            cursor: pointer;
        }

        .create-match-modal .cm-pill:hover {
            border-color: rgba(56, 189, 248, 0.35);
            color: #f8fafc;
        }

        .create-match-modal .cm-pill.active {
            background: linear-gradient(120deg, #2dd4ff, #818cf8);
            color: #0b1120;
            border-color: transparent;
            box-shadow: 0 12px 32px rgba(94, 234, 212, 0.35);
        }

        .create-match-modal .cm-pill.cm-pill--ghost {
            background: rgba(148, 163, 184, 0.16);
            border: 1px dashed rgba(148, 163, 184, 0.35);
            color: rgba(226, 232, 240, 0.76);
        }

        .create-match-modal .cm-summary {
            background: rgba(15, 23, 42, 0.75);
            border: 1px dashed rgba(148, 163, 184, 0.35);
            border-radius: 18px;
            padding: 16px 18px;
            font-size: 0.82rem;
            color: rgba(226, 232, 240, 0.7);
            line-height: 1.5;
        }

        .create-match-modal .cm-switch.form-check {
            padding-left: 2.5em;
        }

        .create-match-modal .cm-switch .form-check-input {
            width: 2.8em;
            height: 1.4em;
            background-color: rgba(148, 163, 184, 0.25);
            border: 1px solid rgba(148, 163, 184, 0.35);
            cursor: pointer;
        }

        .create-match-modal .cm-switch .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.25);
        }

        .create-match-modal .cm-switch .form-check-input:checked {
            background-color: #22d3ee;
            border-color: #22d3ee;
        }

        .create-match-modal .cm-map-preview {
            background: rgba(15, 23, 42, 0.7);
            border: 1px dashed rgba(148, 163, 184, 0.35);
            border-radius: 20px;
            min-height: 200px;
            overflow: hidden;
        }

        .create-match-modal .cm-map-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: rgba(148, 163, 184, 0.7);
            font-size: 0.9rem;
            letter-spacing: 0.02em;
        }

        .create-match-modal .cm-card-aside .cm-section-block+.cm-section-block {
            padding-top: 24px;
            margin-top: 24px;
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .create-match-modal .cm-player-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
        }

        .create-match-modal .cm-player-chip {
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(56, 189, 248, 0.35);
            border-radius: 18px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #c4f1f9;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .create-match-modal .cm-player-chip:hover {
            transform: translateY(-2px);
            border-color: rgba(94, 234, 212, 0.45);
            box-shadow: 0 12px 28px rgba(56, 189, 248, 0.25);
        }

        .create-match-modal .cm-player-chip.selected {
            border-color: rgba(56, 189, 248, 0.55);
            background: linear-gradient(120deg, rgba(56, 189, 248, 0.18), rgba(99, 102, 241, 0.18));
            box-shadow: 0 14px 32px rgba(56, 189, 248, 0.35);
        }

        .create-match-modal .cm-player-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(56, 189, 248, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #38bdf8;
            font-weight: 700;
        }

        .create-match-modal .cm-player-name {
            font-size: 0.86rem;
            color: rgba(226, 232, 240, 0.9);
            letter-spacing: 0.01em;
        }

        .create-match-modal .cm-player-empty {
            padding: 32px 16px;
            border-radius: 18px;
            text-align: center;
            color: rgba(148, 163, 184, 0.75);
            background: rgba(15, 23, 42, 0.75);
            border: 1px dashed rgba(148, 163, 184, 0.18);
        }

        .create-match-modal .cm-show-more {
            color: rgba(94, 234, 212, 0.85);
            text-decoration: none;
            font-weight: 600;
            letter-spacing: 0.04em;
        }

        .create-match-modal .cm-show-more:hover {
            color: rgba(94, 234, 212, 1);
        }

        .create-match-modal .cm-color-palette {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
        }

        .create-match-modal .cm-color-swatch {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            border: 2px solid rgba(148, 163, 184, 0.2);
            cursor: pointer;
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .create-match-modal .cm-color-swatch:hover {
            transform: scale(1.06);
            border-color: rgba(148, 163, 184, 0.55);
        }

        .create-match-modal .cm-color-swatch.selected {
            border-color: #f8fafc;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.35);
            transform: scale(1.08);
        }

        .create-match-modal .cm-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: space-between;
            align-items: center;
            margin: 36px;
        }

        .create-match-modal .cm-footer .cm-footer-context {
            font-size: 0.75rem;
            letter-spacing: 0.18em;
            color: rgba(148, 163, 184, 0.7);
        }

        .create-match-modal .cm-footer-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        @media (max-width: 992px) {
            .create-match-header {
                padding-right: 0;
            }

            .create-match-modal {
                padding: 32px 24px;
            }

            .create-match-modal .btn-close {
                top: 16px;
                right: 16px;
            }
        }

        @media (max-width: 768px) {
            .create-match-modal {
                padding: 28px 20px;
            }

            .create-match-title {
                font-size: 1.5rem;
            }

            .create-match-modal .cm-card {
                padding: 22px;
            }

            .create-match-modal .cm-grid-two,
            .create-match-modal .cm-grid-three {
                grid-template-columns: 1fr;
            }

            .create-match-modal .cm-footer {
                align-items: flex-start;
                gap: 20px;
            }
        }

        @media (max-width: 576px) {
            .create-match-modal .cm-btn {
                width: 100%;
            }

            .create-match-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .create-match-modal .cm-footer-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>

@endsection
