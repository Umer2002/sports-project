@extends('layouts.admin')

@section('title', 'Tournament Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card rounded-2xl border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            {{ $tournament->name }}
                        </h4>
                        <div>
                            <a href="{{ route('admin.tournaments.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to Tournaments
                            </a>
                            <a href="{{ route('admin.tournaments.edit', $tournament) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit Tournament
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    {{-- Tournament Information --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Tournament Information
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-semibold">Host Club:</td>
                                        <td>{{ $tournament->hostClub->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Start Date:</td>
                                        <td>{{ $tournament->start_date ? $tournament->start_date->format('M d, Y') : 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">End Date:</td>
                                        <td>{{ $tournament->end_date ? $tournament->end_date->format('M d, Y') : 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Location:</td>
                                        <td>{{ $tournament->location }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Format:</td>
                                        <td>{{ $tournament->format->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Teams:</td>
                                        <td>{{ $tournament->teams->count() }} teams</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-success mb-3">
                                <i class="fas fa-users me-2"></i>Participating Teams
                            </h5>
                            @if($tournament->teams->count() > 0)
                                <div class="list-group">
                                    @foreach($tournament->teams as $team)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $team->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $team->club->name }}</small>
                                        </div>
                                        <span class="badge bg-primary">{{ $team->sport->name ?? 'N/A' }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No teams assigned to this tournament.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Tournament Matches --}}
                    <div class="row">
                        <div class="col-lg-12">
                            <h5 class="text-info mb-3">
                                <i class="fas fa-calendar-alt me-2"></i>Tournament Matches
                            </h5>
                            @if($matches->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Match ID</th>
                                                <th>Home Team</th>
                                                <th>Away Team</th>
                                                <th>Date & Time</th>
                                                <th>Venue</th>
                                                <th>Assigned Referee</th>
                                                <th>Available Referees</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($matches as $match)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary">#{{ $match->id }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <strong>{{ $match->homeClub->name }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <strong>{{ $match->awayClub->name }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $match->match_date ? $match->match_date->format('M d, Y') : 'Not set' }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $match->match_time ? \Carbon\Carbon::parse($match->match_time)->format('h:i A') : 'Not set' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($match->venue)
                                                        <span class="badge bg-info">{{ $match->venue }}</span>
                                                    @else
                                                        <span class="text-muted">TBD</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($match->referee)
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge badge-success mr-2">
                                                                <i class="fas fa-user-check"></i> {{ $match->referee->full_name }}
                                                            </span>
                                                            <button class="btn btn-sm btn-outline-danger"
                                                                    onclick="removeRefereeFromMatch({{ $match->id }})"
                                                                    title="Remove Referee">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Not Assigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary"
                                                            onclick="showAppliedReferees({{ $match->id }})"
                                                            title="View Applied Referees">
                                                        <i class="fas fa-eye"></i> View ({{ $match->applications->count() }})
                                                    </button>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-sm btn-outline-info" title="View Match Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-warning" title="Edit Match">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No matches have been scheduled for this tournament yet.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Applied Referees Modal --}}
<div class="modal fade" id="appliedRefereesModal" tabindex="-1" aria-labelledby="appliedRefereesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appliedRefereesModalLabel">
                    <i class="fas fa-users me-2"></i>Applied Referees
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="refereesList">
                    <!-- Applied referees will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div class="toast-container position-fixed top-0 end-0 p-3">
    <!-- Toast notifications will appear here -->
</div>
@endsection

@push('scripts')
<script>
// Show applied referees for a match
function showAppliedReferees(matchId) {
    // Get the match data from the page
    const matches = @json($matches);
    const match = matches.find(m => m.id === matchId);
    
    if (!match || !match.applications || match.applications.length === 0) {
        document.getElementById('refereesList').innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No referees have applied for this match yet.</div>';
    } else {
        let html = '<div class="table-responsive">';
        html += '<table class="table table-sm">';
        html += '<thead><tr><th>Referee Name</th><th>Email</th><th>Applied At</th><th>Priority Score</th><th>Actions</th></tr></thead>';
        html += '<tbody>';
        
        match.applications.forEach(application => {
            const referee = application.referee;
            const appliedAt = new Date(application.applied_at).toLocaleDateString();
            
            html += '<tr>';
            html += `<td><strong>${referee.full_name}</strong></td>`;
            html += `<td>${referee.user ? referee.user.email : 'N/A'}</td>`;
            html += `<td>${appliedAt}</td>`;
            html += `<td><span class="badge bg-info">${application.priority_score || 0}</span></td>`;
            html += '<td>';
            html += `<button class="btn btn-sm btn-success" onclick="assignRefereeToMatch(${matchId}, ${referee.id})">`;
            html += '<i class="fas fa-user-plus"></i> Assign';
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        document.getElementById('refereesList').innerHTML = html;
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('appliedRefereesModal'));
    modal.show();
}

// Assign referee to match
function assignRefereeToMatch(matchId, refereeId) {
    fetch(`/admin/tournaments/matches/${matchId}/assign-referee`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            referee_id: refereeId
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
        showToast('error', 'Error assigning referee to match');
    });
}

// Remove referee from match
function removeRefereeFromMatch(matchId) {
    if (!confirm('Are you sure you want to remove the referee from this match?')) {
        return;
    }
    
    fetch(`/admin/tournaments/matches/${matchId}/remove-referee`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
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
        showToast('error', 'Error removing referee from match');
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
                <strong class="me-auto">Tournament Management</strong>
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
@endpush
