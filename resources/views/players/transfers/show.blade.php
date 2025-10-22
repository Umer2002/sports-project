@extends('layouts.player-new')

@section('title', 'Transfer Details')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Transfer Details</h4>
                        <a href="{{ route('player.transfers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back to Transfers
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>From</h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>{{ $transfer->fromClub->name ?? 'No Club' }}</h5>
                                    <p class="text-muted mb-0">{{ $transfer->fromSport->name ?? 'No Sport' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>To</h6>
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>{{ $transfer->toClub->name }}</h5>
                                    <p class="mb-0">{{ $transfer->toSport->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h6>Transfer Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($transfer->status === 'pending')
                                            <span class="badge bg-warning">Pending Approval</span>
                                        @elseif($transfer->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($transfer->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($transfer->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Requested:</strong></td>
                                    <td>{{ $transfer->created_at->format('M d, Y \a\t H:i') }}</td>
                                </tr>
                                @if($transfer->approved_at)
                                    <tr>
                                        <td><strong>Approved:</strong></td>
                                        <td>{{ $transfer->approved_at->format('M d, Y \a\t H:i') }}</td>
                                    </tr>
                                @endif
                                @if($transfer->rejected_at)
                                    <tr>
                                        <td><strong>Rejected:</strong></td>
                                        <td>{{ $transfer->rejected_at->format('M d, Y \a\t H:i') }}</td>
                                    </tr>
                                @endif
                                @if($transfer->notes)
                                    <tr>
                                        <td><strong>Notes:</strong></td>
                                        <td>{{ $transfer->notes }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($transfer->status === 'pending')
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Waiting for Approval</strong><br>
                            Your transfer request is pending approval from {{ $transfer->toClub->name }}.
                        </div>
                    @elseif($transfer->status === 'approved')
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Transfer Approved!</strong><br>
                            You are now a member of {{ $transfer->toClub->name }} playing {{ $transfer->toSport->name }}.
                        </div>
                    @elseif($transfer->status === 'rejected')
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Transfer Rejected</strong><br>
                            Your transfer request was not approved by {{ $transfer->toClub->name }}.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 