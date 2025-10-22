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
            <!-- <i class="fas fa-search"></i> -->
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