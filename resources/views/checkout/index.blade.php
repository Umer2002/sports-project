@extends('layouts.shop')
@section('title', 'Checkout')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Checkout</h2>
                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back to Cart</a>
            </div>

            <form id="checkout-form">
                @csrf
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header"><strong>Shipping Information</strong></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="shipping_address" class="form-label">Shipping Address</label>
                                    <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required>{{ auth()->user()->address ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header"><strong>Billing Information</strong></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="billing_address" class="form-label">Billing Address</label>
                                    <textarea class="form-control" id="billing_address" name="billing_address" rows="3" required>{{ auth()->user()->address ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><strong>Payment</strong></div>
                            <div class="card-body">
                                <p class="text-muted mb-0">You will be redirected to Stripe to complete your payment securely.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header"><strong>Order Summary</strong></div>
                            <div class="card-body">
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm align-middle">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cartItems as $item)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($item->product->image)
                                                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                                     alt="{{ $item->product->name }}" 
                                                                     class="me-2 rounded" 
                                                                     style="width: 36px; height: 36px; object-fit: cover;">
                                                            @endif
                                                            <span class="small">{{ $item->product->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{ $item->quantity }}</td>
                                                    <td class="text-end">${{ number_format($item->total_price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax (8%):</span>
                                    <span>${{ number_format($tax, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Shipping:</span>
                                    <span>${{ number_format($shipping, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-2 mb-3">
                                    <strong>Total:</strong>
                                    <strong>${{ number_format($total, 2) }}</strong>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" id="checkout-btn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <i class="fas fa-lock"></i> Proceed to Payment - ${{ number_format($total, 2) }}
                                </button>
                                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                                    <i class="fas fa-arrow-left"></i> Back to Cart
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- Stripe.js v3 -->
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load Stripe.js
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const form = document.getElementById('checkout-form');
    const checkoutBtn = document.getElementById('checkout-btn');
    const spinner = checkoutBtn.querySelector('.spinner-border');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        checkoutBtn.disabled = true;
        spinner.classList.remove('d-none');
        checkoutBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        // Get form data
        const formData = new FormData(form);
        
        fetch('/checkout/create-session', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Prefer Stripe.js redirect to checkout with sessionId
                if (data.session_id) {
                    stripe.redirectToCheckout({ sessionId: data.session_id });
                } else if (data.checkout_url) {
                    window.location.href = data.checkout_url;
                }
            } else {
                alert('Error creating checkout session: ' + data.message);
                // Reset button state
                checkoutBtn.disabled = false;
                spinner.classList.add('d-none');
                checkoutBtn.innerHTML = 'Proceed to Payment - ${{ number_format($total, 2) }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error creating checkout session');
            // Reset button state
            checkoutBtn.disabled = false;
            spinner.classList.add('d-none');
            checkoutBtn.innerHTML = 'Proceed to Payment - ${{ number_format($total, 2) }}';
        });
    });
});
</script>
@endpush
@endsection
