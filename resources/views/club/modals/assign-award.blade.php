@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Storage;
    $assignAwardAction = Route::has('club.awards.store') ? route('club.awards.store') : '#';
    $availableAwards = $availableAwards ?? collect();
    $clubPlayers = $clubPlayers ?? collect();
@endphp

<div class="modal fade" id="assignAward" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
                <div class="modal-body assign-award-modal rounded-4">
                <button class="btn-close-ghost" aria-label="Close" data-bs-dismiss="modal">&times;</button>
                    <div class="modal-title-block p-4">
                        <div class="brand-dot rounded-4"></div>
                        <div>
                            <div class="popup-title-color">Assign Award</div>
                        <div class="popup-sub-color">CLUB • SELECT AWARD AND PLAYERS</div>
                    </div>
                    </div>

                <form action="{{ route('club.awards.store') }}" method="POST" enctype="multipart/form-data"
                    id="awardAssignmentForm">
                    @csrf
                    <div class="d-flex gap-4 flex-column flex-md-row px-4">
                        <div class="flex-1 gap-2 d-flex flex-column">
                            <div class="assign-award-card flex-1 rounded-4 p-3">
                                <label class="form-label">Award</label>
                                <select class="form-select" name="award_id" id="modalAwardSelect" required>
                                    <option value="">Select a reward</option>
                                    @foreach ($availableAwards ?? [] as $reward)
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
                                    <div class="d-flex align-items-center flex-wrap gap-1">
                                        @foreach ($availableAwards ?? [] as $reward)
                                            <span class="swatch small-reward-chip {{ $loop->first ? 'primary' : ($loop->index % 2 == 0 ? 'mid' : 'light') }}" 
                                                  data-reward-id="{{ $reward->id }}"
                                                  data-reward-name="{{ $reward->name }}"
                                                  title="{{ $reward->name }}"
                                                  style="background-image: url('{{ $reward->image ? asset('images/' . $reward->image) : '' }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
                                            </span>
                                        @endforeach
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
                                        @foreach ($clubPlayers ?? [] as $player)
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
                                        data-checkbox="modal_post_to_feed">Post to team</label>

                                    <input type="checkbox" name="add_to_profile" id="modal_add_to_profile"
                                        value="1" checked class="d-none">
                                    <label for="modal_add_to_profile"
                                        class="popup-btn popup-btn-active action-checkbox flex-1"
                                        data-checkbox="modal_add_to_profile">Add to profile</label>
                                </div>
                            </div>
                            <div class="flex-1 d-flex gap-3 d-flex d-md-none">
                                {{-- <button type="button" class="popup-btn py-2 px-4" id="modalSaveDraft">Save
                                    Draft</button> --}}
                                <button type="button" class="popup-btn popup-btn-active py-1 px-4"
                                    onclick="window.open('{{ route('club.awards.log') }}', '_blank')">View
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
                                onclick="window.open('{{ route('club.awards.log') }}', '_blank')">View Log</button>
                        </div>
                    </div>
                </form>
                </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Club assign award modal JavaScript loaded');
        
        // Get the correct elements
        const awardSelect = document.getElementById('modalAwardSelect');
        const descriptionField = document.getElementById('modalDescription');
        const playerSearch = document.getElementById('modalPlayerSearch');
        const playerItems = document.querySelectorAll('.player-item');
        const selectedPlayers = document.getElementById('modalSelectedPlayers');

        console.log('Modal elements found:', {
            awardSelect: !!awardSelect,
            descriptionField: !!descriptionField,
            playerSearch: !!playerSearch,
            playerItems: playerItems.length
        });

        // Award selection change
        if (awardSelect) {
            awardSelect.addEventListener('change', function() {
                console.log('Award selection changed to:', this.value);
                const awardId = this.value;
                if (awardId) {
                    fetchAwardDetails(awardId);
                }
            });
        }

        // Fetch award details
        function fetchAwardDetails(awardId) {
            console.log('Fetching award details for ID:', awardId);
            fetch(`/club/awards/${awardId}/details`)
                .then(response => response.json())
                .then(data => {
                    console.log('Award details response:', data);
                    if (data.success) {
                        const award = data.award;
                        descriptionField.value = award.description || '';
                    }
                })
                .catch(error => {
                    console.error('Error fetching award details:', error);
                });
        }

        // Player search functionality
        if (playerSearch) {
            playerSearch.addEventListener('input', function () {
                const query = this.value.toLowerCase();
                playerItems.forEach((item) => {
                    const playerName = item.getAttribute('data-player-name') || '';
                    const show = !query || playerName.includes(query);
                    item.style.display = show ? '' : 'none';
                });
            });
        }

        // Player selection functionality
        console.log('Setting up player selection for', playerItems.length, 'players');
        playerItems.forEach((item, index) => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox) {
                console.log('Adding event listener to player', index, checkbox.value);
                checkbox.addEventListener('change', function() {
                    console.log('Player checkbox changed:', this.checked, this.value);
                    updateSelectedPlayers();
                });
            } else {
                console.log('No checkbox found for player item', index);
            }
        });

        function updateSelectedPlayers() {
            const checkedBoxes = document.querySelectorAll('.player-item input[type="checkbox"]:checked');
            console.log('Updating selected players, found', checkedBoxes.length, 'checked boxes');
            selectedPlayers.innerHTML = '';
            
            checkedBoxes.forEach((checkbox) => {
                const playerItem = checkbox.closest('.player-item');
                const playerName = playerItem.querySelector('.fw-medium').textContent;
                const playerId = checkbox.value;
                console.log('Adding selected player:', playerName, playerId);
                
                const chip = document.createElement('div');
                chip.className = 'form-select w-auto d-flex gap-1 align-items-center h-42 m-w-200';
                chip.innerHTML = `
                    <div class="chip-badge">${playerName.substring(0, 2).toUpperCase()}</div>
                    <div>${playerName}</div>
                `;
                selectedPlayers.appendChild(chip);
            });
        }

        // Modal shown event
        document.getElementById('assignAward')?.addEventListener('shown.bs.modal', function () {
            console.log('Assign award modal shown');
            if (playerSearch) {
                playerSearch.focus();
            }
        });
    });
</script>
@endpush
