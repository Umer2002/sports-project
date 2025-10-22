@extends('layouts.admin')

@section('title', isset($team) ? 'Edit Team' : 'Create Team')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card rounded-2xl border-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="ti ti-users me-2"></i>
                    {{ isset($team) ? 'Edit Team' : 'Create Team' }}
                </h4>
                <div class="dropdown">
                    <a href="#" class="text-white" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end select-2">
                        <li><a class="dropdown-item" href="{{ route('admin.teams.index') }}">Back to Teams</a></li>
                        @if(isset($team))
                            <li>
                                <a class="dropdown-item text-danger" href="#"
                                   onclick="event.preventDefault(); if(confirm('Are you sure?')) document.getElementById('delete-form').submit();">
                                    Delete Team
                                </a>
                                <form id="delete-form" action="{{ route('admin.teams.destroy', $team) }}" method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ isset($team) ? route('admin.teams.update', $team) : route('admin.teams.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if(isset($team)) @method('PUT') @endif

                    <div class="row">
                        {{-- Club --}}
                        <div class="col-md-4 mb-4">
                            <label for="club_id" class="form-label fw-semibold">Club</label>
                            <select name="club_id" id="club_id" class="form-select select2" data-placeholder="Select Club" required>
                                <option value="">-- Select Club --</option>
                                @foreach($clubs ?? [] as $id => $club)
                                    <option value="{{ $id }}" {{ old('club_id', $team->club_id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $club }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sport (auto-selected from club, disabled) --}}
                        <div class="col-md-4 mb-4">
                            <label class="form-label fw-semibold">Sport</label>
                            <select id="sport_select" class="form-select" disabled>
                                <option value="">-- Sport will be set from club --</option>
                                @foreach($sports ?? [] as $id => $sport)
                                    <option value="{{ $id }}" {{ (old('sport_id', $team->sport_id ?? '') == $id) ? 'selected' : '' }}>{{ $sport }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="sport_id" id="sport_id" value="{{ old('sport_id', $team->sport_id ?? '') }}">
                            <small class="text-muted">Sport is set based on the selected club and cannot be changed.</small>
                        </div>

                        {{-- Division --}}
                        <div class="col-md-4 mb-4">
                            <label for="division_id" class="form-label fw-semibold">Division</label>
                            <select name="division_id" id="division_id" class="form-select select2" data-placeholder="Select Division" data-initial="{{ old('division_id', $team->division_id ?? '') }}" required>
                                <option value="">Select Division</option>
                            </select>
                            <small class="text-muted">Divisions automatically align with the club's sport.</small>
                        </div>
                    </div>

                    {{-- Team Name --}}
                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">Team Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                               value="{{ old('name', $team->name ?? '') }}" placeholder="Enter team name" required>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4"
                                  placeholder="Enter team description">{{ old('description', $team->description ?? '') }}</textarea>
                    </div>

                    {{-- Logo Upload --}}
                    <div class="mb-4">
                        <label for="logo" class="form-label fw-semibold">Team Logo</label>
                        <input type="file" name="logo" id="logo" class="form-control">
                        @if(isset($team) && $team->logo)
                            <div class="mt-3">
                                <img src="{{ asset('storage/' . $team->logo) }}" width="60" class="rounded shadow-sm" alt="Team Logo">
                            </div>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="ti ti-check me-1"></i> {{ isset($team) ? 'Update' : 'Create' }} Team
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Team Management Section --}}
@if(isset($team))
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card rounded-2xl border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">
                    <i class="ti ti-users me-2"></i>
                    Team Management
                </h4>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    {{-- Team Players --}}
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">
                            <i class="ti ti-user me-2"></i>Team Players
                        </h5>
                        @if($team->players->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Player</th>
                                            <th>Position</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($team->players as $player)
                                        <tr>
                                            <td>{{ $player->name }}</td>
                                            <td>
                                                @if($player->pivot->position_id)
                                                    @php
                                                        $position = \App\Models\Position::find($player->pivot->position_id);
                                                    @endphp
                                                    <span class="badge bg-primary">{{ $position->position_name ?? 'Unknown' }}</span>
                                                @else
                                                    <span class="badge bg-secondary">No Position</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="removePlayer({{ $player->id }}, {{ $team->id }})"
                                                        title="Remove Player">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No players assigned to this team.</p>
                        @endif
                        
                        {{-- Add Player Form --}}
                        <div class="mt-3">
                            <h6>Add Player to Team</h6>
                            <form id="addPlayerForm" class="d-flex gap-2">
                                @csrf
                                <select name="player_id" class="form-select form-select-sm" required>
                                    <option value="">Select Player</option>
                                    @foreach(\App\Models\Player::whereNotIn('id', $team->players->pluck('id'))->get() as $player)
                                        <option value="{{ $player->id }}">{{ $player->name }}</option>
                                    @endforeach
                                </select>
                                <select name="position_id" class="form-select form-select-sm">
                                    <option value="">No Position</option>
                                    @foreach(\App\Models\Position::all() as $position)
                                        <option value="{{ $position->id }}">{{ $position->position_name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ti ti-plus"></i> Add
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Team Coaches --}}
                    <div class="col-md-6">
                        <h5 class="text-success mb-3">
                            <i class="ti ti-user-check me-2"></i>Team Coaches
                        </h5>
                        @if($team->coaches->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Coach</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($team->coaches as $coach)
                                        <tr>
                                            <td>{{ $coach->name }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="removeCoach({{ $coach->id }}, {{ $team->id }})"
                                                        title="Remove Coach">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No coaches assigned to this team.</p>
                        @endif
                        
                        {{-- Add Coach Form --}}
                        <div class="mt-3">
                            <h6>Add Coach to Team</h6>
                            <form id="addCoachForm" class="d-flex gap-2">
                                @csrf
                                <select name="coach_id" class="form-select form-select-sm" required>
                                    <option value="">Select Coach</option>
                                    @foreach(\App\Models\Coach::whereNotIn('id', $team->coaches->pluck('id'))->get() as $coach)
                                        <option value="{{ $coach->id }}">{{ $coach->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="ti ti-plus"></i> Add
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Add Player to Team
document.getElementById('addPlayerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const teamId = {{ $team->id }};
    
    fetch(`/admin/teams/${teamId}/add-player`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            player_id: formData.get('player_id'),
            position_id: formData.get('position_id') || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error adding player to team');
    });
});

// Add Coach to Team
document.getElementById('addCoachForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const teamId = {{ $team->id }};
    
    fetch(`/admin/teams/${teamId}/add-coach`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            coach_id: formData.get('coach_id')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error adding coach to team');
    });
});

// Remove Player from Team
function removePlayer(playerId, teamId) {
    if (!confirm('Are you sure you want to remove this player from the team?')) {
        return;
    }
    
    fetch(`/admin/teams/${teamId}/remove-player`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            player_id: playerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error removing player from team');
    });
}

// Remove Coach from Team
function removeCoach(coachId, teamId) {
    if (!confirm('Are you sure you want to remove this coach from the team?')) {
        return;
    }
    
    fetch(`/admin/teams/${teamId}/remove-coach`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            coach_id: coachId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error removing coach from team');
    });
}

// Show toast notification
function showToast(type, message) {
    // Create toast element if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-${type === 'success' ? 'check-circle text-success' : 'exclamation-circle text-danger'} me-2"></i>
                <strong class="me-auto">Team Management</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Show toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}
</script>

<script>
    $(function () {
        if (window.$) {
            $('.select2').each(function () {
                const placeholder = this.dataset.placeholder || 'Select an option';
                window.$(this).select2({
                    width: '100%',
                    placeholder,
                    allowClear: true
                });
            });
        }
    });

    const clubSelect = document.getElementById('club_id');
    const sportSelect = document.getElementById('sport_select');
    const sportIdInput = document.getElementById('sport_id');
    const divisionSelect = document.getElementById('division_id');
    let pendingDivisionId = divisionSelect ? divisionSelect.dataset.initial : '';

    function populateDivisions(divisions, selectedId) {
        if (!divisionSelect) { return; }

        divisionSelect.innerHTML = '';
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = 'Select Division';
        divisionSelect.appendChild(placeholderOption);
        divisionSelect.disabled = !divisions.length;

        const grouped = divisions.reduce((carry, division) => {
            const key = division.category || '';
            (carry[key] = carry[key] || []).push(division);
            return carry;
        }, {});

        Object.entries(grouped).forEach(([category, items]) => {
            const container = category
                ? (() => { const group = document.createElement('optgroup'); group.label = category; divisionSelect.appendChild(group); return group; })()
                : divisionSelect;

            items.forEach(({ id, name }) => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = name;
                if (String(id) === String(selectedId)) {
                    option.selected = true;
                }
                container.appendChild(option);
            });
        });

        const currentValue = divisions.some(division => String(division.id) === String(selectedId))
            ? String(selectedId)
            : '';
        divisionSelect.value = currentValue;

        if (window.$ && window.$(divisionSelect).data('select2')) {
            window.$(divisionSelect).val(currentValue).trigger('change.select2');
        }
    }

    async function syncSportWithClub(clubId) {
        if (!clubId) {
            if (sportSelect) { sportSelect.value = ''; }
            if (sportIdInput) { sportIdInput.value = ''; }
            populateDivisions([], '');
            return;
        }

        try {
            const response = await fetch(`/clubs/${clubId}/sport`, { headers: { 'Accept': 'application/json' } });
            const data = await response.json();

            const sid = data.sport_id || '';
            if (sportSelect) { sportSelect.value = sid; }
            if (sportIdInput) { sportIdInput.value = sid; }

            const selectedDivisionId = pendingDivisionId || '';
            populateDivisions(data.divisions || [], selectedDivisionId);
            if (pendingDivisionId) {
                pendingDivisionId = '';
            }
        } catch (error) {
            if (sportSelect) { sportSelect.value = ''; }
            if (sportIdInput) { sportIdInput.value = ''; }
            populateDivisions([], '');
        }
    }

    if (clubSelect) {
        const handleChange = () => {
            pendingDivisionId = '';
            syncSportWithClub(clubSelect.value);
        };

        clubSelect.addEventListener('change', handleChange);

        if (window.$ && window.$(clubSelect).data('select2')) {
            window.$(clubSelect).on('select2:select select2:clear', handleChange);
        }

        if (clubSelect.value) {
            syncSportWithClub(clubSelect.value);
        } else {
            populateDivisions([], '');
        }
    } else if (divisionSelect) {
        populateDivisions([], '');
    }
</script>
@endpush
