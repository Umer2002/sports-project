@extends('layouts.coach-dashboard')

@section('title', 'Award Assignment Log')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('coach-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('coach.awards.index') }}">Awards</a></li>
                        <li class="breadcrumb-item active">Assignment Log</li>
                    </ol>
                </div>
                <h4 class="page-title">Award Assignment Log</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Award Assignments</h5>
                        <div>
                            <a href="{{ route('coach.awards.assign') }}" class="btn btn-primary composer-primary">
                                <i class="fas fa-plus"></i> Assign New Award
                            </a>
                        </div>
                    </div>

                    @if($assignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Award</th>
                                        <th>Player</th>
                                        <th>Date Awarded</th>
                                        <th>Visibility</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assignment)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="award-icon me-2" style="width: 32px; height: 32px; background: {{ $assignment->award_color }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px;">
                                                        <i class="fas fa-trophy"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $assignment->award_name }}</div>
                                                        @if($assignment->coach_note)
                                                            <small class="text-muted">{{ Str::limit($assignment->coach_note, 50) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="player-avatar me-2" style="width: 32px; height: 32px; background: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                                                        {{ strtoupper(substr($assignment->player_name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $assignment->player_name }}</div>
                                                        <small class="text-muted">Player</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-medium">{{ \Carbon\Carbon::parse($assignment->awarded_at)->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($assignment->awarded_at)->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $assignment->visibility == 'public' ? 'success' : ($assignment->visibility == 'team' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($assignment->visibility) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    @if($assignment->notify_player)
                                                        <small class="text-success"><i class="fas fa-check"></i> Notified</small>
                                                    @endif
                                                    @if($assignment->post_to_feed)
                                                        <small class="text-info"><i class="fas fa-check"></i> Posted</small>
                                                    @endif
                                                    @if($assignment->add_to_profile)
                                                        <small class="text-primary"><i class="fas fa-check"></i> Profile</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewAssignmentModal{{ $assignment->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete({{ $assignment->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- View Assignment Modal -->
                                        <div class="modal fade" id="viewAssignmentModal{{ $assignment->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Award Assignment Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6>Award Information</h6>
                                                                <div class="d-flex align-items-center mb-3">
                                                                    <div class="award-icon me-3" style="width: 50px; height: 50px; background: {{ $assignment->award_color }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                                                        <i class="fas fa-trophy"></i>
                                                                    </div>
                                                                    <div>
                                                                        <div class="fw-bold">{{ $assignment->award_name }}</div>
                                                                        <small class="text-muted">Award Badge</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6>Player Information</h6>
                                                                <div class="d-flex align-items-center mb-3">
                                                                    <div class="player-avatar me-3" style="width: 50px; height: 50px; background: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; font-weight: bold;">
                                                                        {{ strtoupper(substr($assignment->player_name, 0, 2)) }}
                                                                    </div>
                                                                    <div>
                                                                        <div class="fw-bold">{{ $assignment->player_name }}</div>
                                                                        <small class="text-muted">Player</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6>Assignment Details</h6>
                                                                <p><strong>Date Awarded:</strong> {{ \Carbon\Carbon::parse($assignment->awarded_at)->format('M d, Y h:i A') }}</p>
                                                                <p><strong>Visibility:</strong> <span class="badge bg-{{ $assignment->visibility == 'public' ? 'success' : ($assignment->visibility == 'team' ? 'warning' : 'secondary') }}">{{ ucfirst($assignment->visibility) }}</span></p>
                                                                <p><strong>Assigned By:</strong> {{ $assignment->assigned_by_name }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6>Actions Taken</h6>
                                                                <ul class="list-unstyled">
                                                                    @if($assignment->notify_player)
                                                                        <li><i class="fas fa-check text-success"></i> Player Notified</li>
                                                                    @endif
                                                                    @if($assignment->post_to_feed)
                                                                        <li><i class="fas fa-check text-info"></i> Posted to Feed</li>
                                                                    @endif
                                                                    @if($assignment->add_to_profile)
                                                                        <li><i class="fas fa-check text-primary"></i> Added to Profile</li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                        </div>

                                                        @if($assignment->coach_note)
                                                            <div class="mt-3">
                                                                <h6>Coach Note</h6>
                                                                <div class="alert alert-light">
                                                                    {{ $assignment->coach_note }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $assignments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-history text-muted" style="font-size: 48px;"></i>
                            </div>
                            <h5 class="text-muted">No Award Assignments</h5>
                            <p class="text-muted">You haven't assigned any awards yet.</p>
                            <a href="{{ route('coach.awards.assign') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Assign Your First Award
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(assignmentId) {
    if (confirm('Are you sure you want to delete this award assignment?')) {
        // In a real app, you'd make an AJAX call to delete the assignment
        alert('Award assignment deleted! (This is a placeholder)');
    }
}
</script>

<style>
.award-icon {
    background: linear-gradient(45deg, #007bff, #0056b3);
}

.player-avatar {
    background: linear-gradient(45deg, #007bff, #0056b3);
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}
</style>
@endsection
