@extends('layouts.admin')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Team Details</h1>
        <div>
            <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Teams
            </a>
            <a href="{{ route('admin.teams.edit', $team->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Team
            </a>
        </div>
    </div>

    <!-- Team Information Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Team Information</h6>
                    <span class="badge badge-{{ $team->is_active ? 'success' : 'danger' }}">
                        {{ $team->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            @if($team->logo)
                                <img src="{{ asset('storage/' . $team->logo) }}" alt="Team Logo"
                                     class="img-fluid rounded" style="max-width: 150px;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                     style="width: 150px; height: 150px;">
                                    <i class="fas fa-users fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h4 class="text-primary">{{ $team->name }}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Club:</strong> {{ $team->club->name ?? 'N/A' }}</p>
                                    <p><strong>Sport:</strong> {{ $team->sport->name ?? 'N/A' }}</p>
                                    <p><strong>Created:</strong> {{ $team->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Players:</strong> {{ $team->players->count() }}</p>
                                    <p><strong>Status:</strong>
                                        <span class="badge badge-{{ $team->is_active ? 'success' : 'danger' }}">
                                            {{ $team->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                    <p><strong>Last Updated:</strong> {{ $team->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            @if($team->description)
                                <div class="mt-3">
                                    <strong>Description:</strong>
                                    <p class="text-muted">{{ $team->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Statistics -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Team Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-right">
                                <h4 class="text-primary">{{ $team->players->count() }}</h4>
                                <small class="text-muted">Total Players</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div>
                                <h4 class="text-success">{{ $team->players->where('pivot.status', 'active')->count() }}</h4>
                                <small class="text-muted">Active Players</small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Team Players -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Team Players</h6>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPlayerModal">
                <i class="fas fa-plus"></i> Add Player
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="playersTable">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teamPlayers as $player)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($player->photo)
                                            <img src="{{ asset('storage/' . $player->photo) }}"
                                                 alt="Profile" class="rounded-circle mr-2" style="width: 40px; height: 40px;">
                                        @else
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mr-2"
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $player->name }}</strong>
                                            @if($player->pivot->is_captain)
                                                <span class="badge badge-warning ml-1">Captain</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td>{{ $player->email }}</td>
                                <td>{{ $player->phone ?? 'N/A' }}</td>
                                <td>{{ $player->pivot->created_at ? $player->pivot->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $player->pivot->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($player->pivot->status ?? 'inactive') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="editPlayerPosition({{ $player->id }}, {{ $player->pivot->position_id ?? 'null' }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="removePlayer({{ $player->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No players assigned to this team</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <!-- Team Formation Table -->
    @if($playersWithPositions->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Team Formation</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Position</th>
                            <th>Player Name</th>
                            <th>Email</th>
                            <th>Joined Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($playersWithPositions->groupBy('position_name') as $positionName => $players)
                            @foreach($players as $index => $player)
                                <tr class="{{ $index === 0 ? 'position-header' : '' }}">
                                    @if($index === 0)
                                        <td rowspan="{{ $players->count() }}" class="position-cell">
                                            <span class="badge badge-primary">{{ $positionName ?? 'No Position' }}</span>
                                            <br>
                                            <small class="text-muted">{{ $players->count() }} player(s)</small>
                                        </td>
                                    @endif
                                    <td>
                                        <div class="d-flex align-items-center">

                                            <div>
                                                <strong>{{ $player->player_name }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $player->player_email }}</td>
                                    <td>{{ \Carbon\Carbon::parse($player->joined_at)->format('M d, Y') }}</td>

                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif


</div>

<!-- Add Player Modal -->
<div class="modal fade" id="addPlayerModal" tabindex="-1" aria-labelledby="addPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPlayerModalLabel">Add Player to Team</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPlayerForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="player_id" class="form-label">Select Player</label>
                        <select class="form-select" id="player_id" name="player_id" required>
                            <option value="">Choose a player...</option>
                            @foreach($availablePlayers as $player)
                                <option value="{{ $player->id }}">{{ $player->name }} ({{ $player->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="position_id" class="form-label">Position</label>
                        <select class="form-select" id="position_id" name="position_id">
                            <option value="">No Position</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->position_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Player</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Player Position Modal -->
<div class="modal fade" id="editPlayerModal" tabindex="-1" aria-labelledby="editPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPlayerModalLabel">Edit Player Position</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPlayerForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_player_id" name="player_id">
                    <div class="mb-3">
                        <label class="form-label">Player Name</label>
                        <input type="text" class="form-control" id="edit_player_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_position_id" class="form-label">Position</label>
                        <select class="form-select" id="edit_position_id" name="position_id">
                            <option value="">No Position</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->position_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Position</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('css')
<style>
.formation-field {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 15px;
    padding: 30px;
    margin: 20px 0;
    position: relative;
    min-height: 400px;
}

.formation-field::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    height: 2px;
    background: rgba(255, 255, 255, 0.3);
    z-index: 1;
}

.formation-field::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 2px;
    height: 80%;
    background: rgba(255, 255, 255, 0.3);
    z-index: 1;
}

.formation-row {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin: 15px 0;
    position: relative;
    z-index: 2;
}

.goalkeeper-row {
    margin-bottom: 30px;
}

.defenders-row {
    margin-bottom: 25px;
}

.midfielders-row {
    margin-bottom: 25px;
}

.forwards-row {
    margin-bottom: 30px;
}

.player-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    min-width: 120px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
    position: relative;
}

.player-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.player-card.goalkeeper {
    border: 3px solid #dc3545;
}

.player-card.defender {
    border: 3px solid #007bff;
}

.player-card.midfielder {
    border: 3px solid #ffc107;
}

.player-card.forward {
    border: 3px solid #28a745;
}

.player-card.unassigned {
    border: 3px solid #6c757d;
    opacity: 0.7;
}

.player-avatar {
    margin-bottom: 10px;
}

.player-avatar img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.default-avatar {
    width: 50px;
    height: 50px;
    background: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: #6c757d;
    font-size: 20px;
}

.player-info {
    text-align: center;
}

.player-name {
    font-weight: bold;
    font-size: 12px;
    color: #333;
    margin-bottom: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.player-position {
    font-size: 10px;
    color: #666;
    text-transform: uppercase;
    font-weight: 500;
}

.unassigned-row {
    border-top: 2px solid rgba(255, 255, 255, 0.3);
    padding-top: 20px;
    margin-top: 20px;
}

.unassigned-row h6 {
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 15px;
}

.position-title {
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 15px;
    font-weight: bold;
    text-align: center;
}

.players-in-position {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

/* Table Formation Styles */
.position-cell {
    background-color: #f8f9fa;
    vertical-align: middle;
    text-align: center;
    font-weight: bold;
}

.position-header {
    background-color: #e9ecef;
}

.avatar-sm {
    width: 32px;
    height: 32px;
}

.default-avatar-sm {
    width: 32px;
    height: 32px;
    background: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 12px;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.1);
}

@media (max-width: 768px) {
    .formation-field {
        padding: 20px;
        min-height: 300px;
    }

    .player-card {
        min-width: 100px;
        padding: 10px;
    }

    .player-name {
        font-size: 11px;
    }

    .player-position {
        font-size: 9px;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    // $('#playersTable').DataTable({
    //     "order": [[ 4, "desc" ]]
    // });

    // Set up CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Add Player Form Submission
    $('#addPlayerForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '{{ route("admin.teams.addPlayer", $team->id) }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    // Close modal using Bootstrap 5
                    var modal = bootstrap.Modal.getInstance(document.getElementById('addPlayerModal'));
                    modal.hide();
                    location.reload();
                } else {
                    alert('Error adding player: ' + response.message);
                }
            },
            error: function() {
                alert('Error adding player to team');
            }
        });
    });
    });

    // Edit Player Form Submission
    $('#editPlayerForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '{{ route("admin.teams.updatePlayerPosition", $team->id) }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    // Close modal using Bootstrap 5
                    var modal = bootstrap.Modal.getInstance(document.getElementById('editPlayerModal'));
                    modal.hide();
                    location.reload();
                } else {
                    alert('Error updating player position: ' + response.message);
                }
            },
            error: function() {
                alert('Error updating player position');
            }
        });
    });

    function editPlayerPosition(playerId, playerName, positionId) {
    // Populate the edit modal
    $('#edit_player_id').val(playerId);
    $('#edit_player_name').val(playerName);
    $('#edit_position_id').val(positionId);

    // Show the edit modal
    var editModal = new bootstrap.Modal(document.getElementById('editPlayerModal'));
    editModal.show();
}

function removePlayer(playerId) {
    if (confirm('Are you sure you want to remove this player from the team?')) {
        $.ajax({
            url: '{{ route("admin.teams.removePlayer", $team->id) }}',
            method: 'POST',
            data: {
                player_id: playerId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error removing player: ' + response.message);
                }
            },
            error: function() {
                alert('Error removing player from team');
            }
        });
    }
}
</script>
@endpush
