@extends('layouts.shop')
@section('title', 'Payment Successful')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="text-success mb-3">Payment Successful!</h2>
                    <p class="lead mb-4">Thank you for your order. Your payment has been processed successfully. Your cart has been cleared.</p>
                    
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5>Order Details</h5>
                            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                            <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                            <p><strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($order->status) }}</span></p>
                            <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    
                    <p class="text-muted mb-4">
                        You will receive an email confirmation shortly. We'll notify you when your order ships.
                    </p>
                    
                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary">View Order Status</a>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // Immediately reflect cleared cart in nav badges
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.cart-count-badge').forEach(el => el.textContent = '0');
    });
</script>
@endpush
@endsection
