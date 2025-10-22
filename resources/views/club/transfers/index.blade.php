@extends('layouts.club-dashboard')

@section('title', 'Transfer Requests')
@section('page_title', 'Transfer Requests')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">🔄 Transfer Requests</h4>
</div>

    @include('partials.alerts')

    <!-- Pending Transfers -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-clock me-2"></i>Pending Requests ({{ $pendingTransfers->count() }})
            </h5>
        </div>
        <div class="card-body">
            @if($pendingTransfers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>From Club</th>
                                <th>Sport Change</th>
                                <th>Requested</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingTransfers as $transfer)
                                <tr>
                                    <td>
                                        <strong>{{ $transfer->player->user->name }}</strong><br>
                                        <small class="text-muted">{{ $transfer->player->user->email }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $transfer->fromClub->name ?? 'No Club' }}</strong><br>
                                        <small class="text-muted">{{ $transfer->fromSport->name ?? 'No Sport' }}</small>
                                    </td>
                                    <td>
                                        @if($transfer->from_sport_id != $transfer->to_sport_id)
                                            <span class="badge bg-info">{{ $transfer->fromSport->name ?? 'Unknown' }} → {{ $transfer->toSport->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Same Sport</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transfer->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $transfer->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('club.transfers.show', $transfer) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye me-1"></i>Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>No Pending Requests</h5>
                    <p class="text-muted">All transfer requests have been processed.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Transfers -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Recent Transfers
            </h5>
        </div>
        <div class="card-body">
            @if($recentTransfers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>From Club</th>
                                <th>Sport Change</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransfers as $transfer)
                                <tr>
                                    <td>
                                        <strong>{{ $transfer->player->user->name }}</strong><br>
                                        <small class="text-muted">{{ $transfer->player->user->email }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $transfer->fromClub->name ?? 'No Club' }}</strong><br>
                                        <small class="text-muted">{{ $transfer->fromSport->name ?? 'No Sport' }}</small>
                                    </td>
                                    <td>
                                        @if($transfer->from_sport_id != $transfer->to_sport_id)
                                            <span class="badge bg-info">{{ $transfer->fromSport->name ?? 'Unknown' }} → {{ $transfer->toSport->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Same Sport</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transfer->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($transfer->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($transfer->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transfer->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $transfer->created_at->format('H:i') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5>No Recent Transfers</h5>
                    <p class="text-muted">No transfer requests have been processed yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 