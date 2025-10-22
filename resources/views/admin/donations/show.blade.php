@extends('layouts.admin')
@section('title', 'Donation Details')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Donation Details</h2>
        <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Donations
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Donation Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Donation ID:</strong></td>
                                    <td>#{{ $donation->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td>{{ $donation->formatted_amount }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Currency:</strong></td>
                                    <td>{{ strtoupper($donation->currency) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $donation->status === 'completed' ? 'success' : ($donation->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($donation->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $donation->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @if($donation->completed_at)
                                <tr>
                                    <td><strong>Completed:</strong></td>
                                    <td>{{ $donation->completed_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Stripe Session:</strong></td>
                                    <td>
                                        @if($donation->stripe_session_id)
                                            <code>{{ $donation->stripe_session_id }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Club:</strong></td>
                                    <td>{{ $donation->club->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Donor Name:</strong></td>
                                    <td>{{ $donation->donor_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Donor Email:</strong></td>
                                    <td>{{ $donation->donor_email }}</td>
                                </tr>
                                @if($donation->donor)
                                <tr>
                                    <td><strong>User Account:</strong></td>
                                    <td>{{ $donation->donor->name }} (ID: {{ $donation->donor->id }})</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    @if($donation->message)
                    <div class="mt-4">
                        <h6>Message from Donor:</h6>
                        <div class="alert alert-light">
                            {{ $donation->message }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Club Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($donation->club->logo)
                            <img src="{{ asset('uploads/clubs/' . $donation->club->logo) }}" 
                                 alt="{{ $donation->club->name }}" 
                                 class="img-fluid rounded" 
                                 style="max-height: 100px;">
                        @endif
                    </div>
                    
                    <h6>{{ $donation->club->name }}</h6>
                    <p class="text-muted mb-2">{{ $donation->club->email }}</p>
                    
                    @if($donation->club->phone)
                        <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $donation->club->phone }}</p>
                    @endif
                    
                    @if($donation->club->address)
                        <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>{{ $donation->club->address }}</p>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.clubs.show', $donation->club) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>View Club
                        </a>
                    </div>
                </div>
            </div>
            
            @if($donation->donor)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Donor Information</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $donation->donor->name }}</h6>
                    <p class="text-muted mb-2">{{ $donation->donor->email }}</p>
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.users.show', $donation->donor) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>View User
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
