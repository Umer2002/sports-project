@extends('layouts.app')

@section('title', 'Award Log')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white mb-1">Award Log</h2>
                    <p class="text-muted mb-0">Track all awards assigned to your players</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('club.awards.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-trophy me-2"></i>Manage Awards
                    </a>
                    <a href="{{ route('club.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-dark border-secondary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary rounded-circle p-3">
                                        <i class="fas fa-trophy text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">Total Awards</h6>
                                    <h4 class="text-white mb-0">{{ $awardAssignments->total() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark border-secondary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-success rounded-circle p-3">
                                        <i class="fas fa-users text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">Unique Players</h6>
                                    <h4 class="text-white mb-0">{{ $awardAssignments->unique('player_id')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark border-secondary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning rounded-circle p-3">
                                        <i class="fas fa-calendar text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">This Month</h6>
                                    <h4 class="text-white mb-0">{{ $awardAssignments->where('awarded_at', '>=', now()->startOfMonth())->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-dark border-secondary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-info rounded-circle p-3">
                                        <i class="fas fa-star text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">This Week</h6>
                                    <h4 class="text-white mb-0">{{ $awardAssignments->where('awarded_at', '>=', now()->startOfWeek())->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Awards Table -->
            <div class="card bg-dark border-secondary">
                <div class="card-header bg-transparent border-secondary">
                    <h5 class="text-white mb-0">Award Assignments</h5>
                </div>
                <div class="card-body p-0">
                    @if($awardAssignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-dark table-hover mb-0">
                                <thead class="bg-secondary">
                                    <tr>
                                        <th class="border-0">Player</th>
                                        <th class="border-0">Award</th>
                                        <th class="border-0">Team</th>
                                        <th class="border-0">Assigned By</th>
                                        <th class="border-0">Date</th>
                                        <th class="border-0">Visibility</th>
                                        <th class="border-0">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($awardAssignments as $assignment)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="player-avatar me-3"
                                                        style="width: 40px; height: 40px; background: linear-gradient(45deg, #007bff, #0056b3); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: bold;">
                                                        {{ strtoupper(substr($assignment->player_name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <div class="text-white fw-medium">{{ $assignment->player_name }}</div>
                                                        @if($assignment->coach_note)
                                                            <small class="text-muted">{{ Str::limit($assignment->coach_note, 50) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($assignment->award_image)
                                                        <img src="{{ asset('images/' . $assignment->award_image) }}" 
                                                             alt="{{ $assignment->award_name }}" 
                                                             class="me-2" 
                                                             style="width: 32px; height: 32px; border-radius: 4px; object-fit: cover;">
                                                    @else
                                                        <div class="me-2 bg-primary rounded d-flex align-items-center justify-content-center" 
                                                             style="width: 32px; height: 32px;">
                                                            <i class="fas fa-trophy text-white" style="font-size: 14px;"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="text-white fw-medium">{{ $assignment->award_name }}</div>
                                                        <small class="text-muted">{{ ucfirst($assignment->award_type) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $assignment->team_name }}</span>
                                            </td>
                                            <td>
                                                <div class="text-white">{{ $assignment->assigned_by_name }}</div>
                                            </td>
                                            <td>
                                                <div class="text-white">{{ \Carbon\Carbon::parse($assignment->awarded_at)->format('M j, Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($assignment->awarded_at)->format('g:i A') }}</small>
                                            </td>
                                            <td>
                                                @switch($assignment->visibility)
                                                    @case('public')
                                                        <span class="badge bg-success">Public</span>
                                                        @break
                                                    @case('team')
                                                        <span class="badge bg-warning">Team Only</span>
                                                        @break
                                                    @case('private')
                                                        <span class="badge bg-secondary">Private</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-info">{{ ucfirst($assignment->visibility) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if($assignment->notify_player)
                                                        <span class="badge bg-info" title="Player Notified">
                                                            <i class="fas fa-bell"></i>
                                                        </span>
                                                    @endif
                                                    @if($assignment->post_to_feed)
                                                        <span class="badge bg-primary" title="Posted to Feed">
                                                            <i class="fas fa-share"></i>
                                                        </span>
                                                    @endif
                                                    @if($assignment->add_to_profile)
                                                        <span class="badge bg-success" title="Added to Profile">
                                                            <i class="fas fa-user"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer bg-transparent border-secondary">
                            {{ $awardAssignments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-trophy text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-white mb-2">No Awards Assigned Yet</h5>
                            <p class="text-muted mb-4">Start recognizing your players' achievements by assigning awards.</p>
                            <a href="{{ route('club.awards.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Assign First Award
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.player-avatar {
    transition: transform 0.2s ease;
}

.player-avatar:hover {
    transform: scale(1.1);
}

.table-dark th {
    background-color: rgba(255, 255, 255, 0.05) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

.table-dark td {
    border-color: rgba(255, 255, 255, 0.1) !important;
}

.table-dark tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.05) !important;
}

.badge {
    font-size: 0.75rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}
</style>
@endsection
