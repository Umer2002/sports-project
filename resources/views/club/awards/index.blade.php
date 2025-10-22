@extends('layouts.club-dashboard')

@section('title', 'Club Awards')
@section('page_title', 'Award Assignments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
        

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Awards Table Section -->
            <div class="card border-secondary">
                <div class="card-header bg-transparent border-secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-white mb-0">Award History</h5>
                        <span class="badge bg-primary">{{ $awardsAssigned->total() }} Total Assignments</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($awardsAssigned->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-secondary">
                                    <tr>
                                        <th class="border-0 text-dark">Player</th>
                                        <th class="border-0 text-dark">Award</th>
                                        <th class="border-0 text-dark">Type</th>
                                        <th class="border-0 text-dark">Visibility</th>
                                        <th class="border-0 text-dark">Assigned By</th>
                                        <th class="border-0 text-dark">Date Awarded</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($awardsAssigned as $award)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                  
                                                    <div>
                                                            <div class="fw-medium text-dark">{{ $award->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-medium text-dark">{{ $award->award_name }}</div>
                                                @if($award->coach_note)
                                                    <small class="text-muted">{{ Str::limit($award->coach_note, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($award->award_type) }}</span>
                                            </td>
                                            <td>
                                                @switch($award->visibility)
                                                    @case('public')
                                                        <span class="badge bg-success">Public</span>
                                                        @break
                                                    @case('team')
                                                        <span class="badge bg-warning">Team</span>
                                                        @break
                                                    @case('club')
                                                        <span class="badge bg-info">Club</span>
                                                        @break
                                                    @case('private')
                                                        <span class="badge bg-secondary">Private</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="text-dark">{{ $award->assigned_by_name }}</div>
                                            </td>
                                            <td>
                                                <div class="text-dark">{{ \Carbon\Carbon::parse($award->awarded_at)->format('M j, Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($award->awarded_at)->format('g:i A') }}</small>
                                            </td>
                                           
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing {{ $awardsAssigned->firstItem() }} to {{ $awardsAssigned->lastItem() }} of {{ $awardsAssigned->total() }} results
                                </div>
                                <div>
                                    {{ $awardsAssigned->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-trophy text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-white mb-2">No Awards Assigned Yet</h5>
                            <p class="text-muted mb-4">Start recognizing your players' achievements by assigning awards.</p>
                            <a href="{{ route('club.awards.create') }}" class="action-btn green">
                                <i class="bi bi-award-fill me-2"></i>Assign Your First Award
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: 600;
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

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.section-title {
    color: white;
    font-weight: 600;
    margin-bottom: 0.5rem;
}
</style>
@endpush
