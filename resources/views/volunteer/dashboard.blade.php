@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Ambassador Dashboard</h2>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Clubs</h5>
                    <p class="text-muted">Invite, manage, send reminders, import/export.</p>
                    <a href="{{ route('volunteer.clubs.index') }}" class="btn btn-primary"><i class="fas fa-users"></i> Manage Clubs</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Promotions</h5>
                    <p class="text-muted">Create promotions, upload videos, share to social.</p>
                    <a href="{{ route('volunteer.promotions.index') }}" class="btn btn-primary"><i class="fas fa-bullhorn"></i> Promotions</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Ambassador Referral</h5>
                    <div class="mb-2">Players Registered: <strong>{{ $playerCount }}</strong></div>
                    <div class="mb-3">Commission Payout: <strong>${{ number_format($commission, 2) }}</strong></div>
                    <div class="mb-2">
                        <label class="form-label">Player Invite Link</label>
                        <input type="text" class="form-control" value="{{ $playerInvite }}" readonly onclick="this.select()">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Club Invite Link</label>
                        <input type="text" class="form-control" value="{{ $clubInvite }}" readonly onclick="this.select()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
