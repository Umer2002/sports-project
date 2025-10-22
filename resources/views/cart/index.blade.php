@extends('layouts.shop')
@section('title', 'Your Cart')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Shopping Cart</h2>
                @if($cartItems->count() > 0)
                    <button class="btn btn-outline-secondary btn-sm clear-cart">
                        <i class="fas fa-trash"></i> Clear Cart
                    </button>
                @endif
            </div>

            @if($cartItems->count() > 0)
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th class="text-nowrap">Price</th>
                                            <th>Quantity</th>
                                            <th class="text-nowrap">Total</th>
                                            <th>Action</th>
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
                                                                 class="me-3 rounded" 
                                                                 style="width: 56px; height: 56px; object-fit: cover;">
                                                        @else
                                                            <div class="me-3 bg-light d-flex align-items-center justify-content-center rounded" style="width:56px;height:56px;">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-1">{{ $item->product->name }}</h6>
                                                            <small class="text-muted">{{ Str::limit($item->product->description, 80) }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>${{ number_format($item->price, 2) }}</td>
                                                <td style="max-width: 90px;">
                                                    <input type="number" 
                                                           class="form-control form-control-sm quantity-input" 
                                                           value="{{ $item->quantity }}" 
                                                           min="1" 
                                                           max="99" 
                                                           data-cart-item-id="{{ $item->id }}">
                                                </td>
                                                <td>${{ number_format($item->total_price, 2) }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger remove-item" 
                                                            data-cart-item-id="{{ $item->id }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header"><strong>Order Summary</strong></div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Items</span>
                                <span>{{ $cartItems->sum('quantity') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>${{ number_format($cartTotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted mb-3">
                                <small>Taxes and shipping</small>
                                <small>Calculated at checkout</small>
                            </div>
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-credit-card"></i> Proceed to Checkout
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h5>Your cart is empty</h5>
                        <p class="text-muted">Add some products to your cart to get started.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">Browse Products</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update quantity
    document.querySelectorAll('.quantity-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const cartItemId = this.dataset.cartItemId;
            const quantity = this.value;
            
            fetch(`/cart/${cartItemId}/update`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating cart: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating cart');
            });
        });
    });

    // Remove item
    document.querySelectorAll('.remove-item').forEach(function(button) {
        button.addEventListener('click', function() {
            const cartItemId = this.dataset.cartItemId;
            
            if (confirm('Are you sure you want to remove this item?')) {
                fetch(`/cart/${cartItemId}/remove`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error removing item: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing item');
                });
            }
        });
    });

    // Clear cart
    document.querySelector('.clear-cart')?.addEventListener('click', function() {
        if (confirm('Are you sure you want to clear your cart?')) {
            fetch('/cart/clear', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error clearing cart: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error clearing cart');
            });
        }
    });
});
</script>
@endpush
@endsection
