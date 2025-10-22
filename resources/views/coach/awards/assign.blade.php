@extends('layouts.coach-dashboard')

@section('title', 'Assign Award')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('coach-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('coach.awards.index') }}">Awards</a></li>
                        <li class="breadcrumb-item active">Assign Award</li>
                    </ol>
                </div>
                <h4 class="page-title">Assign Award</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('coach.awards.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Award Selection -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Award Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Award *</label>
                                            <select class="form-select @error('award_id') is-invalid @enderror" name="award_id" id="awardSelect" required>
                                                <option value="">Select a reward</option>
                                                @foreach($rewards as $reward)
                                                    <option value="{{ $reward->id }}" {{ old('award_id') == $reward->id ? 'selected' : '' }}>
                                                        {{ $reward->name }} ({{ ucfirst($reward->type) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('award_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Award Preview -->
                                        <div id="awardPreview" class="mb-3" style="display: none;">
                                            <div class="award-preview p-3 border rounded">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="award-icon me-2" style="width: 40px; height: 40px; background: var(--award-color, #007bff); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; overflow: hidden;">
                                                        <img id="awardPreviewImage" src="" alt="Award" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                                        <i class="fas fa-trophy" id="awardPreviewIcon"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0" id="awardName">Award Name</h6>
                                                        <small class="text-muted" id="awardDescription">Award Description</small>
                                                    </div>
                                                </div>
                                                <div class="award-details">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <small class="text-muted">Requirements:</small>
                                                            <div id="awardRequirements" class="small"></div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">Rewards:</small>
                                                            <div id="awardRewards" class="small"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Award Color Swatches -->
                                                <div class="mt-3">
                                                    <small class="text-muted d-block mb-2">Award Colors</small>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="swatch primary" id="awardColorSwatch" style="width: 20px; height: 20px; border-radius: 50%; background: #007bff; border: 2px solid #fff; box-shadow: 0 0 0 1px #dee2e6;"></span>
                                                        <span class="swatch mid" style="width: 20px; height: 20px; border-radius: 50%; background: #6c757d; border: 2px solid #fff; box-shadow: 0 0 0 1px #dee2e6;"></span>
                                                        <span class="swatch light" style="width: 20px; height: 20px; border-radius: 50%; background: #e9ecef; border: 2px solid #fff; box-shadow: 0 0 0 1px #dee2e6;"></span>
                                                        <span class="swatch light" style="width: 20px; height: 20px; border-radius: 50%; background: #f8f9fa; border: 2px solid #fff; box-shadow: 0 0 0 1px #dee2e6;"></span>
                                                        <span class="swatch light" style="width: 20px; height: 20px; border-radius: 50%; background: #fff; border: 2px solid #fff; box-shadow: 0 0 0 1px #dee2e6;"></span>
                                                        <span class="swatch light" style="width: 20px; height: 20px; border-radius: 50%; background: #fff; border: 2px solid #fff; box-shadow: 0 0 0 1px #dee2e6;"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Award Image</label>
                                            <div class="award-image-container">
                                                <!-- Presaved Award Image Display -->
                                                <div id="presavedAwardImage" class="mb-3" style="display: none;">
                                                    <div class="d-flex align-items-center gap-3 p-3 border rounded bg-light">
                                                        <div class="award-image-preview" style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                            <img id="awardImagePreview" src="" alt="Award Image" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                            <div class="d-flex align-items-center justify-content-center w-100 h-100" style="display: none;">
                                                                <i class="fas fa-trophy text-muted" style="font-size: 24px;"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-medium" id="awardImageName">Award Image</div>
                                                            <small class="text-muted">Presaved award image</small>
                                                            <div class="mt-1">
                                                                <button type="button" class="btn btn-sm btn-outline-primary" id="usePresavedImageBtn">
                                                                    <i class="fas fa-check me-1"></i>Use This Image
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="uploadCustomImageBtn">
                                                                    <i class="fas fa-upload me-1"></i>Upload Custom
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Custom Image Upload -->
                                                <div id="customImageUpload" class="mb-3">
                                                    <input type="file" class="form-control" name="award_image" id="awardImageInput" accept="image/*">
                                                    <small class="text-muted">Upload a custom image for this award assignment</small>
                                                </div>
                                                
                                                <!-- Hidden field for presaved image -->
                                                <input type="hidden" name="use_presaved_image" id="usePresavedImage" value="0">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" id="descriptionField" rows="3" placeholder="Custom description for this award assignment...">{{ old('description') }}</textarea>
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="autoFromCatalogBtn">
                                                    <i class="fas fa-magic me-1"></i>Auto from catalog
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <label class="form-label small">Requirements</label>
                                                <textarea class="form-control" name="requirements" rows="2" placeholder="Award requirements...">{{ old('requirements') }}</textarea>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Rewards</label>
                                                <textarea class="form-control" name="rewards" rows="2" placeholder="Award rewards...">{{ old('rewards') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Player Selection -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Assign to Players</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Search Players</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                <input type="text" class="form-control" id="playerSearch" placeholder="Type player name to search...">
                                                <button type="button" class="btn btn-outline-secondary" id="selectAllBtn">Select All</button>
                                                <button type="button" class="btn btn-outline-danger" id="clearSelectionBtn">Clear</button>
                                            </div>
                                            <small class="text-muted">Start typing to filter players</small>
                                        </div>

                                        <!-- Selected Players Summary -->
                                        <div id="selectedPlayersSummary" class="mb-3" style="display: none;">
                                            <label class="form-label small">Selected Players (<span id="selectedCount">0</span>)</label>
                                            <div id="selectedPlayersList" class="d-flex flex-wrap gap-2"></div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">Available Players</label>
                                                <small class="text-muted" id="playerCount">{{ $players->count() }} players</small>
                                            </div>
                                            <div class="player-list border rounded" style="max-height: 300px; overflow-y: auto;">
                                                @if($players->count() > 0)
                                                    @foreach($players as $player)
                                                        <div class="form-check player-item p-2 border-bottom" data-player-name="{{ strtolower($player->name) }}" data-player-team="{{ strtolower($player->teams->first()->name ?? '') }}">
                                                            <input class="form-check-input" type="checkbox" name="player_ids[]" value="{{ $player->id }}" id="player_{{ $player->id }}">
                                                            <label class="form-check-label d-flex align-items-center w-100" for="player_{{ $player->id }}" style="cursor: pointer;">
                                                                <div class="player-avatar me-3" style="width: 40px; height: 40px; background: linear-gradient(45deg, #007bff, #0056b3); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold;">
                                                                    {{ strtoupper(substr($player->name, 0, 2)) }}
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-medium">{{ $player->name }}</div>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-users me-1"></i>{{ $player->teams->first()->name ?? 'No Team' }}
                                                                    </small>
                                                                </div>
                                                                <div class="player-status">
                                                                    <span class="badge bg-success">Active</span>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="text-center py-4">
                                                        <i class="fas fa-users text-muted mb-2" style="font-size: 24px;"></i>
                                                        <p class="text-muted mb-0">No players available</p>
                                                        <small class="text-muted">Players will appear here when they join your teams</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <label class="form-label small">Date Awarded</label>
                                                <input type="datetime-local" class="form-control" name="awarded_at" value="{{ old('awarded_at', now()->format('Y-m-d\TH:i')) }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Visibility</label>
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="visibility" id="visibility_public" value="public" {{ old('visibility', 'public') == 'public' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-primary btn-sm" for="visibility_public">Public</label>

                                                    <input type="radio" class="btn-check" name="visibility" id="visibility_team" value="team" {{ old('visibility') == 'team' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-primary btn-sm" for="visibility_team">Team</label>

                                                    <input type="radio" class="btn-check" name="visibility" id="visibility_private" value="private" {{ old('visibility') == 'private' ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-primary btn-sm" for="visibility_private">Private</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small">Coach Note (optional)</label>
                                            <textarea class="form-control" name="coach_note" rows="3" placeholder="Write a short message to appear with the award...">{{ old('coach_note') }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small">Additional Actions</label>
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="notify_player" id="notify_player" value="1" {{ old('notify_player', true) ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="notify_player">Notify player</label>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="post_to_feed" id="post_to_feed" value="1" {{ old('post_to_feed') ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="post_to_feed">Post to feed</label>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="add_to_profile" id="add_to_profile" value="1" {{ old('add_to_profile', true) ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="add_to_profile">Add to profile</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('coach.awards.index') }}" class="btn btn-secondary">Cancel</a>
                                    <div>
                                        {{-- <button type="button" class="btn btn-outline-primary me-2" id="saveDraftBtn">Save Draft</button> --}}
                                        <button type="submit" class="btn btn-primary composer-primary">Assign Award</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const awardSelect = document.getElementById('awardSelect');
    const awardPreview = document.getElementById('awardPreview');
    const playerSearch = document.getElementById('playerSearch');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    const playerItems = document.querySelectorAll('.player-item');
    const selectedPlayersSummary = document.getElementById('selectedPlayersSummary');
    const selectedPlayersList = document.getElementById('selectedPlayersList');
    const selectedCount = document.getElementById('selectedCount');
    const playerCount = document.getElementById('playerCount');

    // Award selection and preview
    awardSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            awardPreview.style.display = 'block';
            document.getElementById('awardName').textContent = selectedOption.text;
            
            // Fetch award details from the server
            fetchAwardDetails(selectedOption.value);
        } else {
            awardPreview.style.display = 'none';
        }
    });

    // Function to fetch award details
    function fetchAwardDetails(awardId) {
        fetch(`/coach/awards/${awardId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const award = data.award;
                    
                    // Update award preview
                    document.getElementById('awardName').textContent = award.name;
                    document.getElementById('awardDescription').textContent = award.description || 'No description available';
                    document.getElementById('awardRequirements').textContent = award.requirements || 'No requirements specified';
                    document.getElementById('awardRewards').textContent = award.rewards || 'No rewards specified';
                    
                    // Update award icon color and image
                    const awardIcon = document.querySelector('.award-icon');
                    const awardPreviewImage = document.getElementById('awardPreviewImage');
                    const awardPreviewIcon = document.getElementById('awardPreviewIcon');
                    
                    if (award.image && award.image.trim() !== '') {
                        // Show award image
                        awardPreviewImage.src = award.image;
                        awardPreviewImage.style.display = 'block';
                        awardPreviewIcon.style.display = 'none';
                        awardIcon.style.background = 'transparent';
                    } else {
                        // Show trophy icon with color
                        awardPreviewImage.style.display = 'none';
                        awardPreviewIcon.style.display = 'block';
                        awardIcon.style.background = award.color || '#007bff';
                    }
                    
                    // Update award color swatch
                    const awardColorSwatch = document.getElementById('awardColorSwatch');
                    if (awardColorSwatch) {
                        awardColorSwatch.style.background = award.color || '#007bff';
                    }
                    
                    // Auto-populate form fields with award details
                    document.querySelector('textarea[name="description"]').value = award.description || '';
                    document.querySelector('textarea[name="requirements"]').value = award.requirements || '';
                    document.querySelector('textarea[name="rewards"]').value = award.rewards || '';
                    
                    // Update award name color to match award color
                    const awardName = document.getElementById('awardName');
                    awardName.style.color = award.color || '#007bff';
                    
                    // Handle presaved award image
                    handlePresavedAwardImage(award);
                }
            })
            .catch(error => {
                console.error('Error fetching award details:', error);
                // Fallback to basic display
                document.getElementById('awardDescription').textContent = 'Description not available';
                document.getElementById('awardRequirements').textContent = 'Requirements not available';
                document.getElementById('awardRewards').textContent = 'Rewards not available';
            });
    }

    // Enhanced player search with team filtering
    playerSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        let visibleCount = 0;
        
        playerItems.forEach(item => {
            const playerName = item.dataset.playerName;
            const playerTeam = item.dataset.playerTeam;
            const matchesName = playerName.includes(searchTerm);
            const matchesTeam = playerTeam.includes(searchTerm);
            
            if (matchesName || matchesTeam || searchTerm === '') {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Update player count
        playerCount.textContent = `${visibleCount} players`;
        
        // Update select all button state
        updateSelectAllButton();
    });

    // Select all visible players
    selectAllBtn.addEventListener('click', function() {
        const visibleItems = Array.from(playerItems).filter(item => item.style.display !== 'none');
        const allChecked = visibleItems.every(item => item.querySelector('input[type="checkbox"]').checked);
        
        visibleItems.forEach(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            checkbox.checked = !allChecked;
            updatePlayerSelection(checkbox);
        });
        
        updateSelectAllButton();
        updateSelectedPlayersSummary();
    });

    // Clear all selections
    clearSelectionBtn.addEventListener('click', function() {
        playerItems.forEach(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            checkbox.checked = false;
            updatePlayerSelection(checkbox);
        });
        
        updateSelectAllButton();
        updateSelectedPlayersSummary();
    });

    // Individual player selection
    playerItems.forEach(item => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        checkbox.addEventListener('change', function() {
            updatePlayerSelection(this);
            updateSelectAllButton();
            updateSelectedPlayersSummary();
        });
    });

    function updatePlayerSelection(checkbox) {
        const playerItem = checkbox.closest('.player-item');
        if (checkbox.checked) {
            playerItem.classList.add('selected');
        } else {
            playerItem.classList.remove('selected');
        }
    }

    function updateSelectAllButton() {
        const visibleItems = Array.from(playerItems).filter(item => item.style.display !== 'none');
        const checkedItems = visibleItems.filter(item => item.querySelector('input[type="checkbox"]').checked);
        
        if (checkedItems.length === 0) {
            selectAllBtn.textContent = 'Select All';
            selectAllBtn.classList.remove('btn-warning');
            selectAllBtn.classList.add('btn-outline-secondary');
        } else if (checkedItems.length === visibleItems.length) {
            selectAllBtn.textContent = 'Deselect All';
            selectAllBtn.classList.remove('btn-outline-secondary');
            selectAllBtn.classList.add('btn-warning');
        } else {
            selectAllBtn.textContent = `Select All (${checkedItems.length}/${visibleItems.length})`;
            selectAllBtn.classList.remove('btn-warning');
            selectAllBtn.classList.add('btn-outline-secondary');
        }
    }

    function updateSelectedPlayersSummary() {
        const checkedItems = Array.from(playerItems).filter(item => item.querySelector('input[type="checkbox"]').checked);
        
        if (checkedItems.length > 0) {
            selectedPlayersSummary.style.display = 'block';
            selectedCount.textContent = checkedItems.length;
            
            selectedPlayersList.innerHTML = checkedItems.map(item => {
                const playerName = item.querySelector('.fw-medium').textContent;
                const playerId = item.querySelector('input[type="checkbox"]').value;
                return `
                    <span class="badge bg-primary d-flex align-items-center gap-1" id="selected-${playerId}">
                        <i class="fas fa-user"></i>
                        ${playerName}
                        <button type="button" class="btn-close btn-close-white" style="font-size: 10px;" onclick="removePlayer('${playerId}')"></button>
                    </span>
                `;
            }).join('');
        } else {
            selectedPlayersSummary.style.display = 'none';
        }
    }

    // Remove individual player from selection
    window.removePlayer = function(playerId) {
        const checkbox = document.getElementById(`player_${playerId}`);
        if (checkbox) {
            checkbox.checked = false;
            updatePlayerSelection(checkbox);
            updateSelectAllButton();
            updateSelectedPlayersSummary();
        }
    };

    // Handle presaved award image
    function handlePresavedAwardImage(award) {
        const presavedImageContainer = document.getElementById('presavedAwardImage');
        const customImageUpload = document.getElementById('customImageUpload');
        const awardImagePreview = document.getElementById('awardImagePreview');
        const awardImageName = document.getElementById('awardImageName');
        
        if (award.image && award.image.trim() !== '') {
            // Show presaved image
            presavedImageContainer.style.display = 'block';
            customImageUpload.style.display = 'none';
            
            // Set image source
            awardImagePreview.src = award.image;
            awardImageName.textContent = 'Presaved Award Image';
            
            // Reset hidden field
            document.getElementById('usePresavedImage').value = '1';
        } else {
            // Hide presaved image, show custom upload
            presavedImageContainer.style.display = 'none';
            customImageUpload.style.display = 'block';
            
            // Reset hidden field
            document.getElementById('usePresavedImage').value = '0';
        }
    }

    // Use presaved image button
    document.getElementById('usePresavedImageBtn').addEventListener('click', function() {
        document.getElementById('usePresavedImage').value = '1';
        document.getElementById('customImageUpload').style.display = 'none';
        
        // Clear any uploaded file
        document.getElementById('awardImageInput').value = '';
        
        // Show success state
        const button = this;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check me-1"></i>Selected!';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
        }, 2000);
    });

    // Upload custom image button
    document.getElementById('uploadCustomImageBtn').addEventListener('click', function() {
        document.getElementById('usePresavedImage').value = '0';
        document.getElementById('customImageUpload').style.display = 'block';
        document.getElementById('presavedAwardImage').style.display = 'none';
        
        // Focus on file input
        document.getElementById('awardImageInput').click();
    });

    // Custom image upload change handler
    document.getElementById('awardImageInput').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            document.getElementById('usePresavedImage').value = '0';
            
            // Show preview of uploaded image
            const reader = new FileReader();
            reader.onload = function(e) {
                // You could show a preview here if needed
                console.log('Custom image selected:', e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Auto from catalog functionality
    document.getElementById('autoFromCatalogBtn').addEventListener('click', function() {
        const selectedAwardId = awardSelect.value;
        if (selectedAwardId) {
            // Re-fetch award details and auto-populate all fields
            fetchAwardDetails(selectedAwardId);
            
            // Show success message
            const button = this;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check me-1"></i>Auto-populated!';
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-success');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }, 2000);
        } else {
            alert('Please select an award first.');
        }
    });

    // Save draft functionality
    document.getElementById('saveDraftBtn').addEventListener('click', function() {
        const formData = new FormData(document.querySelector('form'));
        // In a real app, you'd save the form data as a draft
        alert('Draft saved! (This is a placeholder)');
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const selectedPlayers = Array.from(playerItems).filter(item => item.querySelector('input[type="checkbox"]').checked);
        
        if (selectedPlayers.length === 0) {
            e.preventDefault();
            alert('Please select at least one player to assign the award to.');
            return false;
        }
        
        if (!awardSelect.value) {
            e.preventDefault();
            alert('Please select an award to assign.');
            return false;
        }
    });
});
</script>

<style>
.award-preview {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
}

.player-avatar {
    background: linear-gradient(45deg, #007bff, #0056b3);
}

.form-check-label {
    cursor: pointer;
    transition: all 0.2s ease;
}

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
    background: #f1f1f1;
    border-radius: 3px;
}

.player-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.player-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

#selectedPlayersList .badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.5rem;
}

#selectedPlayersList .btn-close {
    padding: 0.125rem;
    font-size: 0.5rem;
}

.player-status .badge {
    font-size: 0.7rem;
}

/* Search input focus effect */
#playerSearch:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Button hover effects */
#selectAllBtn:hover, #clearSelectionBtn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Award color swatches */
.swatch {
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px #dee2e6;
}

.swatch:hover {
    transform: scale(1.1);
    box-shadow: 0 0 0 2px #007bff;
}

.swatch.primary {
    border: 3px solid #007bff;
    box-shadow: 0 0 0 1px #007bff;
}

/* Award preview enhancements */
.award-preview {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.award-preview:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.award-icon {
    transition: all 0.3s ease;
}

.award-icon:hover {
    transform: scale(1.05);
}

/* Auto from catalog button */
#autoFromCatalogBtn {
    transition: all 0.2s ease;
}

#autoFromCatalogBtn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Award image styling */
.award-image-preview {
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
}

.award-image-preview:hover {
    border-color: #007bff;
    transform: scale(1.05);
}

#presavedAwardImage .bg-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

#presavedAwardImage .bg-light:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

#usePresavedImageBtn, #uploadCustomImageBtn {
    transition: all 0.2s ease;
}

#usePresavedImageBtn:hover, #uploadCustomImageBtn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Award image preview in the main preview */
.award-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .player-item {
        padding: 12px 8px;
    }
    
    .player-avatar {
        width: 32px !important;
        height: 32px !important;
        font-size: 12px !important;
    }
    
    .swatch {
        width: 16px !important;
        height: 16px !important;
    }
    
    .award-image-preview {
        width: 50px !important;
        height: 50px !important;
    }
}
</style>
@endsection
