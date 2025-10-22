@extends('layouts.admin')

@section('title', 'Game Expertise Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-star"></i> Game Expertise Management
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info">Set expertise requirements for games</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Tournament Matches Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="text-primary">
                                <i class="fas fa-trophy"></i> Tournament Matches
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Home Team</th>
                                            <th>Away Team</th>
                                            <th>Date & Time</th>
                                            <th>Current Expertise</th>
                                            <th>Required Expertise</th>
                                            <th>Assigned Referee</th>
                                            <th>Available Referees</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($games as $game)
                                        <tr>
                                            <td>{{ $game->id }}</td>
                                            <td>
                                                @if($game->homeClub)
                                                    {{ $game->homeClub->name }}
                                                @else
                                                    <span class="text-muted">TBD</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($game->awayClub)
                                                    {{ $game->awayClub->name }}
                                                @else
                                                    <span class="text-muted">TBD</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $game->match_date ? \Carbon\Carbon::parse($game->match_date)->format('M d, Y') : 'TBD' }}
                                                @if($game->match_time)
                                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($game->match_time)->format('g:i A') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($game->expertise)
                                                    <span class="badge badge-success">{{ $game->expertise->expertise_level }}</span>
                                                @else
                                                    <span class="badge badge-secondary">Not Set</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.game-expertise.update-game', $game) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="expertise_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                                        <option value="">Select Expertise</option>
                                                        @foreach($expertises as $expertise)
                                                            <option value="{{ $expertise->id }}" 
                                                                {{ $game->expertise_id == $expertise->id ? 'selected' : '' }}>
                                                                {{ $expertise->expertise_level }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                @if($game->referee)
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-success mr-2">
                                                            <i class="fas fa-user-check"></i> {{ $game->referee->full_name }}
                                                        </span>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="removeReferee({{ $game->id }}, 'game')" 
                                                                title="Remove Referee">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not Assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="showAvailableReferees({{ $game->id }}, 'game')">
                                                    <i class="fas fa-users"></i> View ({{ $game->availableReferees()->count() }})
                                                </button>
                                            </td>
                                            
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No games found
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Pickup Games Section -->
                    <div class="row">
                        <div class="col-12">
                            <h4 class="text-success">
                                <i class="fas fa-gamepad"></i> Pickup Games
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Sport</th>
                                            <th>Date & Time</th>
                                            <th>Location</th>
                                            <th>Current Expertise</th>
                                            <th>Required Expertise</th>
                                            <th>Assigned Referee</th>
                                            <th>Available Referees</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pickupGames as $pickupGame)
                                        <tr>
                                            <td>{{ $pickupGame->id }}</td>
                                            <td>{{ $pickupGame->title ?: 'Untitled Game' }}</td>
                                            <td>
                                                @if($pickupGame->sport)
                                                    {{ $pickupGame->sport->name }}
                                                @else
                                                    <span class="text-muted">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($pickupGame->game_datetime)
                                                    {{ \Carbon\Carbon::parse($pickupGame->game_datetime)->format('M d, Y g:i A') }}
                                                @else
                                                    <span class="text-muted">TBD</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $pickupGame->location ?: 'TBD' }}
                                            </td>
                                            <td>
                                                @if($pickupGame->expertise)
                                                    <span class="badge badge-success">{{ $pickupGame->expertise->expertise_level }}</span>
                                                @else
                                                    <span class="badge badge-secondary">Not Set</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.game-expertise.update-pickup-game', $pickupGame) }}" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="expertise_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                                        <option value="">Select Expertise</option>
                                                        @foreach($expertises as $expertise)
                                                            <option value="{{ $expertise->id }}" 
                                                                {{ $pickupGame->expertise_id == $expertise->id ? 'selected' : '' }}>
                                                                {{ $expertise->expertise_level }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                @if($pickupGame->referee)
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-success mr-2">
                                                            <i class="fas fa-user-check"></i> {{ $pickupGame->referee->full_name }}
                                                        </span>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                onclick="removeReferee({{ $pickupGame->id }}, 'pickup')" 
                                                                title="Remove Referee">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not Assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="showAvailableReferees({{ $pickupGame->id }}, 'pickup')">
                                                    <i class="fas fa-users"></i> View ({{ $pickupGame->availableReferees()->count() }})
                                                </button>
                                            </td>
                                             
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No pickup games found
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Referees Modal -->
<div class="modal fade" id="availableRefereesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-users"></i> Available Referees
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="refereesList">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Success/Error Messages -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="assignmentToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-info-circle text-primary me-2"></i>
            <strong class="me-auto">Assignment Status</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            <!-- Message will be inserted here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showAvailableReferees(gameId, type) {
    console.log('showAvailableReferees called with gameId:', gameId, 'type:', type);
    
    const modal = new bootstrap.Modal(document.getElementById('availableRefereesModal'));
    modal.show();
    $('#refereesList').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    
    let url = type === 'game' 
        ? `/admin/game-expertise/games/${gameId}/referees`
        : `/admin/game-expertise/pickup-games/${gameId}/referees`;
    
    console.log('Fetching URL:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
            let html = '';
            if (data.referees && data.referees.length > 0) {
                html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>Name</th><th>Email</th><th>Expertise Levels</th><th>Status</th></tr></thead><tbody>';
                
                data.referees.forEach(referee => {
                    html += '<tr>';
                    html += `<td>${referee.name || 'N/A'}</td>`;
                    html += `<td>${referee.email || 'N/A'}</td>`;
                    html += '<td>';
                    if (referee.expertise_levels && referee.expertise_levels.length > 0) {
                        referee.expertise_levels.forEach(expertise => {
                            html += `<span class="badge badge-primary mr-1">${expertise}</span>`;
                        });
                    } else {
                        html += '<span class="text-muted">No expertise set</span>';
                    }
                    html += '</td>';
                    html += '<td>';
                    
                    html += '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
            } else {
                html = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> No qualified referees found for this game.</div>';
            }
            $('#refereesList').html(html);
        })
        .catch(error => {
            console.error('Error loading referees:', error);
            console.error('Error details:', error.message);
            $('#refereesList').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error loading referees: ' + error.message + '</div>');
        });
}

// Assign referee to game
function assignReferee(gameId, refereeId, type) {
    let url = type === 'game' 
        ? `/admin/game-expertise/games/${gameId}/assign-referee`
        : `/admin/game-expertise/pickup-games/${gameId}/assign-referee`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            referee_id: refereeId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            const modal = bootstrap.Modal.getInstance(document.getElementById('availableRefereesModal'));
            modal.hide();
            // Reload the page to show updated assignments
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error assigning referee. Please try again.');
    });
}

// Remove referee from game
function removeReferee(gameId, type) {
    if (!confirm('Are you sure you want to remove the referee assignment?')) {
        return;
    }
    
    let url = type === 'game' 
        ? `/admin/game-expertise/games/${gameId}/remove-referee`
        : `/admin/game-expertise/pickup-games/${gameId}/remove-referee`;
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            // Reload the page to show updated assignments
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error removing referee assignment. Please try again.');
    });
}

// Show toast notification
function showToast(type, message) {
    const toast = document.getElementById('assignmentToast');
    const toastMessage = document.getElementById('toastMessage');
    const toastHeader = toast.querySelector('.toast-header i');
    
    // Update icon and message based on type
    if (type === 'success') {
        toastHeader.className = 'fas fa-check-circle text-success me-2';
        toastMessage.textContent = message;
    } else {
        toastHeader.className = 'fas fa-exclamation-circle text-danger me-2';
        toastMessage.textContent = message;
    }
    
    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}
</script>
@endpush