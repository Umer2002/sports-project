@extends('layouts.club-dashboard')

@section('title', 'Review Transfer Request')
@section('page_title', 'Review Transfer Request')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Review Transfer Request</h4>
                        <a href="{{ route('club.transfers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back to Transfers
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Player Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Player Information</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>{{ $transfer->player->user->name }}</h5>
                                    <p class="text-muted mb-1">{{ $transfer->player->user->email }}</p>
                                    <p class="text-muted mb-0">Player ID: {{ $transfer->player->id }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Current Club</h6>
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>{{ $transfer->fromClub->name ?? 'No Club' }}</h5>
                                    <p class="mb-0">{{ $transfer->fromSport->name ?? 'No Sport' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Details -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6>Transfer Details</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Requested:</strong></td>
                                    <td>{{ $transfer->created_at->format('M d, Y \a\t H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sport Change:</strong></td>
                                    <td>
                                        @if($transfer->from_sport_id != $transfer->to_sport_id)
                                            <span class="badge bg-info">{{ $transfer->fromSport->name ?? 'Unknown' }} → {{ $transfer->toSport->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Same Sport</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($transfer->notes)
                                    <tr>
                                        <td><strong>Player Notes:</strong></td>
                                        <td>{{ $transfer->notes }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if($transfer->status === 'pending')
                        <div class="row">
                            <div class="col-md-6">
                                <form action="{{ route('club.transfers.approve', $transfer) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="team_id" class="form-label">Assign to Team (Optional)</label>
                                        <select name="team_id" id="team_id" class="form-control">
                                            <option value="">No team assignment</option>
                                            @foreach($teams as $team)
                                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Approval Notes (Optional)</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Add any notes about this approval..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="fas fa-check me-2"></i>Approve Transfer
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('club.transfers.reject', $transfer) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Rejection Reason (Optional)</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Explain why this transfer is being rejected..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-lg w-100" onclick="return confirm('Are you sure you want to reject this transfer?')">
                                        <i class="fas fa-times me-2"></i>Reject Transfer
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Transfer {{ ucfirst($transfer->status) }}</strong><br>
                            This transfer request has already been {{ $transfer->status }}.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
