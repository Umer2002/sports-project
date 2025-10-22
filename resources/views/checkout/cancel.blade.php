@extends('layouts.shop')
@section('title', 'Payment Cancelled')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-times-circle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="text-warning mb-3">Payment Cancelled</h2>
                    <p class="lead mb-4">Your payment was cancelled. No charges have been made to your account.</p>
                    
                    <p class="text-muted mb-4">
                        Your cart items are still available. You can complete your purchase at any time.
                    </p>
                    
                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('cart.index') }}" class="btn btn-primary">Return to Cart</a>
                        <a href="{{ route('home') }}" class="btn btn-secondary">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
