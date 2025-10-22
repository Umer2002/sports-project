@extends('layouts.admin')
@section('title', 'Club Details')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Club Details</h1>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Clubs
        </a>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    @if($club->logo)
                        <img src="{{ asset('storage/' . $club->logo) }}" alt="Club Logo" class="img-fluid rounded mb-3" style="max-width: 180px;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="width: 180px; height: 180px;">
                            <i class="fas fa-users fa-3x text-muted"></i>
                        </div>
                    @endif
                    <h4 class="text-primary">{{ $club->name }}</h4>
                    <p class="mb-1"><strong>Email:</strong> {{ $club->email }}</p>
                    <p class="mb-1"><strong>Sport:</strong> {{ $club->sport->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Registered:</strong> <span class="badge bg-{{ $club->is_registered ? 'success' : 'secondary' }}">{{ $club->is_registered ? 'Yes' : 'No' }}</span></p>
                    <p class="mb-1"><strong>Registration Date:</strong> {{ $registrationDate ? $registrationDate->format('M d, Y') : 'Not set' }}</p>
                    <p class="mb-1"><strong>Days Since Registration:</strong> {{ $daysSinceRegistration }} days</p>
                    <p class="mb-1"><strong>Payout Status:</strong> 
                        <span class="badge bg-{{ $payoutStatus === 'Paid' ? 'success' : ($payoutStatus === 'Calculated - Ready for Payment' ? 'warning' : 'info') }}">
                            {{ $payoutStatus }}
                        </span>
                    </p>
                    @if($club->paypal_link)
                        <p class="mb-1"><strong>PayPal:</strong> <a href="{{ $club->paypal_link }}" target="_blank">{{ $club->paypal_link }}</a></p>
                    @endif
                    @if($club->joining_url)
                        <p class="mb-1"><strong>Joining URL:</strong> <a href="{{ $club->joining_url }}" target="_blank">{{ $club->joining_url }}</a></p>
                    @endif
                    @if($club->bio)
                        <div class="mt-3"><strong>Bio:</strong><p class="text-muted">{{ $club->bio }}</p></div>
                    @endif
                    @php
                        $socialLinks = [];
                        if (!empty($club->social_links)) {
                            if (is_string($club->social_links)) {
                                $socialLinks = json_decode($club->social_links, true) ?: [];
                            } elseif (is_array($club->social_links)) {
                                $socialLinks = $club->social_links;
                            }
                        }
                    @endphp
                    @if(!empty($socialLinks))
                        <div class="mt-2">
                            <strong>Social Links:</strong>
                            <ul class="list-unstyled mb-0">
                                @foreach($socialLinks as $platform => $link)
                                    @if(!empty($link))
                                        <li><a href="{{ $link }}" target="_blank">{{ ucfirst($platform) }}: {{ $link }}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <!-- Payout Timeline Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-2"></i>
                        Payout Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6>Initial Player Count</h6>
                                <p class="text-muted">{{ $club->initial_player_count ?? 'Not calculated' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6>Final Player Count</h6>
                                <p class="text-muted">{{ $club->final_player_count ?? 'Not calculated' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6>Estimated Payout</h6>
                                <p class="text-muted">${{ number_format($estimatedPayout ?? 0, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h6>Final Payout</h6>
                                <p class="text-muted">${{ number_format($finalPayout ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    @if($onboardingTimeRemaining)
                        <div class="alert alert-warning mt-3">
                            <h6><i class="fas fa-hourglass-start me-2"></i>Onboarding Period Remaining</h6>
                            <p class="mb-0">
                                {{ $onboardingTimeRemaining['weeks'] }} weeks, 
                                {{ $onboardingTimeRemaining['days'] }} days, 
                                {{ $onboardingTimeRemaining['hours'] }} hours, 
                                {{ $onboardingTimeRemaining['minutes'] }} minutes
                            </p>
                        </div>
                    @endif

                    @if($payoutTimeRemaining)
                        <div class="alert alert-info mt-3">
                            <h6><i class="fas fa-hourglass-half me-2"></i>Payout Period Remaining</h6>
                            <p class="mb-0">
                                {{ $payoutTimeRemaining['weeks'] }} weeks, 
                                {{ $payoutTimeRemaining['days'] }} days, 
                                {{ $payoutTimeRemaining['hours'] }} hours, 
                                {{ $payoutTimeRemaining['minutes'] }} minutes
                            </p>
                        </div>
                    @endif

                    @if($club->payout_status === 'calculated')
                        <div class="alert alert-success mt-3">
                            <h6><i class="fas fa-check-circle me-2"></i>Ready for Payout</h6>
                            <p class="mb-0">
                                Final payout amount: <strong>${{ number_format($finalPayout, 2) }}</strong><br>
                                Calculated on: {{ $club->payout_calculated_at ? $club->payout_calculated_at->format('M d, Y H:i') : 'N/A' }}
                            </p>
                            <form method="POST" action="{{ route('admin.clubs.processPayout', $club) }}" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-dollar-sign me-2"></i>
                                    Process Payout
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($club->payout_status === 'paid')
                        <div class="alert alert-secondary mt-3">
                            <h6><i class="fas fa-check-double me-2"></i>Payout Completed</h6>
                            <p class="mb-0">
                                Paid amount: <strong>${{ number_format($finalPayout, 2) }}</strong><br>
                                Paid on: {{ $club->payout_paid_at ? $club->payout_paid_at->format('M d, Y H:i') : 'N/A' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Players Associated with Club</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($players as $player)
                                    <tr>
                                        <td>{{ $player->name }}</td>
                                        <td>{{ $player->email }}</td>
                                        <td>{{ $player->phone ?? 'N/A' }}</td>
                                        <td><span class="badge bg-{{ $player->is_registered ? 'success' : 'secondary' }}">{{ $player->is_registered ? 'Yes' : 'No' }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">No players found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            @if($payments->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Payment History</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                            <td>${{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->type)) }}</td>
                                            <td><span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($payment->status) }}</span></td>
                                            <td>{{ $payment->notes ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 