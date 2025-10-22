@extends('layouts.player-new')

@section('title', 'My Transfer Requests')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Transfer Requests</h2>
        @php
            $hasPendingTransfer = $transfers->where('status', 'pending')->count() > 0;
        @endphp
        @if($hasPendingTransfer)
            <button class="btn btn-secondary" disabled title="You already have a pending transfer request">
                <i class="fas fa-clock me-2"></i>Transfer Pending
            </button>
        @else
            <a href="{{ route('player.transfers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Request Transfer
            </a>
        @endif
    </div>

    @include('partials.alerts')

    @if($hasPendingTransfer)
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Transfer Request Pending:</strong> You have a pending transfer request. Please wait for club approval before submitting a new request.
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Transfer History</h5>
                </div>
                <div class="card-body">
                    @if($transfers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Sport Change</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transfers as $transfer)
                                        <tr>
                                            <td>
                                                <strong>{{ $transfer->fromClub->name ?? 'No Club' }}</strong><br>
                                                <small class="text-muted">{{ $transfer->fromSport->name ?? 'No Sport' }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $transfer->toClub->name }}</strong><br>
                                                <small class="text-muted">{{ $transfer->toSport->name }}</small>
                                            </td>
                                            <td>
                                                @if($transfer->from_sport_id != $transfer->to_sport_id)
                                                    <span class="badge bg-info">Sport Change</span>
                                                @else
                                                    <span class="badge bg-secondary">Same Sport</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transfer->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($transfer->status === 'approved')
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
                                            <td>
                                                <a href="{{ route('player.transfers.show', $transfer) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($transfer->status === 'pending')
                                                    <form action="{{ route('player.transfers.destroy', $transfer) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Cancel this transfer request?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                            <h5>No Transfer Requests</h5>
                            <p class="text-muted">You haven't made any transfer requests yet.</p>
                            <a href="{{ route('player.transfers.create') }}" class="btn btn-primary">
                                Request Your First Transfer
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
