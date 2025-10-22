<!-- Admin Chat Panel -->
<div class="chat-container">
    <div class="chat-header">
        <h3>Chats</h3>
        <button class="menu-btn">⋯</button>
    </div>

    <!-- Search -->
    <div class="chat-search">
        <input type="text" placeholder="Search" />
    </div>

    @php
        $chatSegments = collect([
            ['key' => 'recent', 'label' => 'Recent Chats', 'items' => collect($adminRecentChats ?? [])],
            ['key' => 'clubs', 'label' => 'Clubs', 'items' => collect($chatClubs ?? [])],
            ['key' => 'coaches', 'label' => 'Coaches', 'items' => collect($chatCoaches ?? [])],
            ['key' => 'players', 'label' => 'Players', 'items' => collect($chatPlayers ?? [])],
            ['key' => 'staff', 'label' => 'Support Staff', 'items' => collect($chatStaff ?? [])],
        ])->map(function ($segment) {
            $segment['items'] = $segment['items']->filter();
            return $segment;
        });

        $filterSegments = $chatSegments->filter(fn($segment) => $segment['items']->count() > 0);
        $totalContacts = $chatSegments->sum(fn($segment) => $segment['items']->count());
        $hasContacts = $totalContacts > 0;
    @endphp

    <!-- Filter Buttons -->
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

    <!-- Chat List -->
    <div class="chat-list" id="adminContactList">
        @foreach ($chatSegments as $segment)
            @foreach ($segment['items'] as $contact)
                <div class="chat-item {{ empty($contact['user_id']) && empty($contact['chat_id']) ? 'disabled' : '' }}"
                    data-chat-group="{{ $segment['key'] }}"
                    data-chat-id="{{ $contact['chat_id'] ?? '' }}"
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
                        <span class="status {{ $contact['status'] }}">● {{ $contact['status_label'] }}</span>
                    </div>
                    <span class="arrow">›</span>
                </div>
            @endforeach
        @endforeach

        <div class="chat-empty" @if ($hasContacts) style="display: none;" @endif>
            <p class="mb-1">No contacts yet.</p>
            <p class="small mb-0">Start a new chat.</p>
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
