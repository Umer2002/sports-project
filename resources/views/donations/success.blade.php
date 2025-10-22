@extends('public.clubs.base')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="card-title mb-4">Thank You for Your Donation!</h2>
                    
                    <div class="alert alert-success mb-4">
                        <h5>Donation Details:</h5>
                        <p class="mb-1"><strong>Amount:</strong> {{ $donation->formatted_amount }}</p>
                        <p class="mb-1"><strong>Club:</strong> {{ $club->name }}</p>
                        <p class="mb-1"><strong>Date:</strong> {{ $donation->completed_at->format('M d, Y H:i') }}</p>
                        @if($donation->message)
                            <p class="mb-1"><strong>Message:</strong> {{ $donation->message }}</p>
                        @endif
                    </div>
                    
                    <p class="text-muted mb-4">
                        Your donation has been successfully processed and will help support {{ $club->name }}'s activities and programs.
                    </p>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ route('public.club.profile', $club->slug) }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Club Profile
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Go Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
