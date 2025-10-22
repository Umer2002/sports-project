@extends('layouts.player-new')

@section('title', 'Referral Payouts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-dollar-sign"></i> Referral Payouts
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Earnings Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Invites</span>
                                    <span class="info-box-number">{{ $totalInvites }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Accepted</span>
                                    <span class="info-box-number">{{ $acceptedInvites }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending</span>
                                    <span class="info-box-number">{{ $pendingInvites }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Earnings</span>
                                    <span class="info-box-number">${{ $totalEarnings }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payout Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-success">
                                        <i class="fas fa-money-bill-wave"></i> Available for Payout
                                    </h5>
                                    <h2 class="text-success">${{ $availableEarnings }}</h2>
                                    @if($availableEarnings > 0)
                                        <form method="POST" action="{{ route('player.payout.request') }}" class="mt-3">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <i class="fas fa-hand-holding-usd"></i> Request Payout
                                            </button>
                                        </form>
                                    @else
                                        <p class="text-muted">No earnings available for payout yet</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-warning">
                                        <i class="fas fa-hourglass-half"></i> Pending (90-day hold)
                                    </h5>
                                    <h2 class="text-warning">${{ $pendingEarnings }}</h2>
                                    <p class="text-muted">Available after 90 days from signup</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Invites -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history"></i> Recent Accepted Invites
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($recentInvites->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Friend Name</th>
                                                <th>Email</th>
                                                <th>Accepted Date</th>
                                                <th>Days Since</th>
                                                <th>Payout Status</th>
                                                <th>Earnings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentInvites as $invite)
                                                <tr>
                                                    <td>{{ $invite['receiver_name'] }}</td>
                                                    <td>{{ $invite['receiver_email'] }}</td>
                                                    <td>{{ $invite['accepted_at'] ? $invite['accepted_at']->format('M d, Y') : 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $invite['days_since_accepted'] >= 90 ? 'success' : 'warning' }}">
                                                            {{ $invite['days_since_accepted'] }} days
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($invite['payout_processed'])
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check"></i> Paid
                                                            </span>
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ $invite['payout_processed_at']->format('M d, Y') }}
                                                            </small>
                                                        @elseif($invite['is_ready_for_payout'])
                                                            <span class="badge badge-info">
                                                                <i class="fas fa-clock"></i> Ready
                                                            </span>
                                                        @else
                                                            <span class="badge badge-warning">
                                                                <i class="fas fa-hourglass-half"></i> 
                                                                {{ $invite['days_until_payout'] }} days left
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">${{ $invite['earnings'] }}</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No accepted invites yet</h5>
                                    <p class="text-muted">Start inviting friends to earn referral rewards!</p>
                                    <a href="{{ route('player.invite.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Send Invites
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Upcoming Payouts -->
                    @if($upcomingPayouts->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-calendar-alt"></i> Upcoming Payouts (Next 30 Days)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Friend Name</th>
                                                <th>Accepted Date</th>
                                                <th>Payout Date</th>
                                                <th>Days Until Payout</th>
                                                <th>Earnings</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($upcomingPayouts as $payout)
                                                <tr>
                                                    <td>{{ $payout['receiver_name'] }}</td>
                                                    <td>{{ $payout['accepted_at'] ? $payout['accepted_at']->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $payout['payout_date'] ? $payout['payout_date']->format('M d, Y') : 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge badge-info">
                                                            {{ $payout['days_until_payout'] }} days
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">${{ $payout['earnings'] }}</strong>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Payout Information -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i> Payout Information
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-gift"></i> How It Works</h5>
                                    <ul>
                                        <li>Earn $10 for each friend who signs up using your referral code</li>
                                        <li>Payouts are held for 90 days to ensure quality referrals</li>
                                        <li>After 90 days, earnings become available for payout</li>
                                        <li>Request payouts anytime for available earnings</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-credit-card"></i> Payment Methods</h5>
                                    <ul>
                                        <li>PayPal (preferred)</li>
                                        <li>Bank Transfer</li>
                                        <li>Cryptocurrency (coming soon)</li>
                                    </ul>
                                    <p class="text-muted">
                                        <small>Payments are processed within 3-5 business days after request</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.info-box {
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.card {
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.badge {
    font-size: 0.8em;
}
</style>
@endpush
