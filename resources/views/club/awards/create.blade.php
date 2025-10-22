@extends('layouts.app')

@section('title', 'Assign Award')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Assign Award</h1>
                    <p class="text-muted">Recognize player achievements with awards</p>
                </div>
                <a href="{{ route('club.awards.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Awards
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('club.awards.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Award Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="award_id" class="form-label">Select Award <span class="text-danger">*</span></label>
                                            <select class="form-select @error('award_id') is-invalid @enderror" id="award_id" name="award_id" required>
                                                <option value="">Choose an award...</option>
                                                @foreach($rewards as $reward)
                                                    <option value="{{ $reward->id }}" 
                                                            data-description="{{ $reward->achievement }}"
                                                            data-image="{{ $reward->image ? Storage::url($reward->image) : '' }}"
                                                            {{ old('award_id') == $reward->id ? 'selected' : '' }}>
                                                        {{ $reward->name }} ({{ ucfirst($reward->type) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('award_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="visibility" class="form-label">Visibility <span class="text-danger">*</span></label>
                                            <select class="form-select @error('visibility') is-invalid @enderror" id="visibility" name="visibility" required>
                                                <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Private (Player & Staff)</option>
                                                <option value="team" {{ old('visibility') == 'team' ? 'selected' : '' }}>Team Feed</option>
                                                <option value="club" {{ old('visibility') == 'club' ? 'selected' : '' }}>Club Members</option>
                                                <option value="public" {{ old('visibility') == 'public' ? 'selected' : '' }}>Public Profile</option>
                                            </select>
                                            @error('visibility')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4" 
                                              placeholder="Describe why this player earned the award...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="coach_note" class="form-label">Personalized Message</label>
                                    <textarea class="form-control @error('coach_note') is-invalid @enderror" 
                                              id="coach_note" 
                                              name="coach_note" 
                                              rows="3" 
                                              placeholder="Add a personal message for the player...">{{ old('coach_note') }}</textarea>
                                    @error('coach_note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="award_image" class="form-label">Award Image (Optional)</label>
                                    <input type="file" 
                                           class="form-control @error('award_image') is-invalid @enderror" 
                                           id="award_image" 
                                           name="award_image" 
                                           accept="image/*">
                                    <div class="form-text">Upload a custom image for this award assignment (max 2MB)</div>
                                    @error('award_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Select Players</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <input type="search" 
                                           class="form-control" 
                                           id="playerSearch" 
                                           placeholder="Search players...">
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label" for="selectAll">
                                            Select All Players
                                        </label>
                                    </div>
                                </div>

                                <div class="player-list" style="max-height: 400px; overflow-y: auto;">
                                    @foreach($clubPlayers as $player)
                                        <div class="form-check player-item mb-2" data-player-name="{{ strtolower($player->name) }}">
                                            <input class="form-check-input player-checkbox" 
                                                   type="checkbox" 
                                                   name="player_ids[]" 
                                                   value="{{ $player->id }}" 
                                                   id="player_{{ $player->id }}"
                                                   {{ in_array($player->id, old('player_ids', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label d-flex align-items-center w-100" for="player_{{ $player->id }}">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    {{ strtoupper(substr($player->name, 0, 2)) }}
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-medium">{{ $player->name }}</div>
                                                    <small class="text-muted">
                                                        {{ optional($player->team)->name ?? 'Unassigned' }}
                                                        @if(optional($player->position)->name)
                                                            • {{ $player->position->name }}
                                                        @endif
                                                    </small>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                @error('player_ids')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Notification Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="notify_player" name="notify_player" value="1" checked>
                                    <label class="form-check-label" for="notify_player">
                                        Notify Players
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="post_to_feed" name="post_to_feed" value="1" checked>
                                    <label class="form-check-label" for="post_to_feed">
                                        Post to Team Feed
                                    </label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="add_to_profile" name="add_to_profile" value="1" checked>
                                    <label class="form-check-label" for="add_to_profile">
                                        Add to Player Profile
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('club.awards.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-award-fill me-2"></i>Assign Award
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const awardSelect = document.getElementById('award_id');
    const descriptionField = document.getElementById('description');
    const playerSearch = document.getElementById('playerSearch');
    const selectAllCheckbox = document.getElementById('selectAll');
    const playerCheckboxes = document.querySelectorAll('.player-checkbox');
    const playerItems = document.querySelectorAll('.player-item');

    // Award selection handler
    awardSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const description = selectedOption.getAttribute('data-description');
        
        if (description && !descriptionField.value.trim()) {
            descriptionField.value = description;
        }
    });

    // Player search functionality
    playerSearch.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        let visibleCount = 0;
        
        playerItems.forEach(item => {
            const playerName = item.getAttribute('data-player-name');
            const isVisible = !query || playerName.includes(query);
            
            item.style.display = isVisible ? 'block' : 'none';
            if (isVisible) visibleCount++;
        });
    });

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        playerCheckboxes.forEach(checkbox => {
            if (checkbox.closest('.player-item').style.display !== 'none') {
                checkbox.checked = this.checked;
            }
        });
    });

    // Update select all when individual checkboxes change
    playerCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const visibleCheckboxes = Array.from(playerCheckboxes).filter(cb => 
                cb.closest('.player-item').style.display !== 'none'
            );
            const checkedVisible = Array.from(playerCheckboxes).filter(cb => 
                cb.checked && cb.closest('.player-item').style.display !== 'none'
            );
            
            selectAllCheckbox.checked = visibleCheckboxes.length > 0 && checkedVisible.length === visibleCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedVisible.length > 0 && checkedVisible.length < visibleCheckboxes.length;
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: 600;
}

.player-item {
    transition: all 0.2s ease;
}

.player-item:hover {
    background-color: rgba(0, 123, 255, 0.05);
    border-radius: 4px;
}
</style>
@endpush
