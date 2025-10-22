@extends('layouts.shop')
@section('title', $product->name)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            @if($product->category)
                <li class="breadcrumb-item">{{ $product->category->name }}</li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="card-img-top" style="max-height: 420px; object-fit: cover;">
                @else
                    <div class="d-flex align-items-center justify-content-center bg-light" style="height: 420px;">
                        <i class="fas fa-image text-muted" style="font-size: 4rem;"></i>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h2 class="card-title">{{ $product->name }}</h2>
                    @if($product->category)
                        <span class="badge bg-primary mb-2">{{ $product->category->name }}</span>
                    @endif
                    <p class="text-muted">{!! nl2br(e($product->description)) !!}</p>

                    <div class="mt-auto">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="h3 mb-0">${{ number_format($product->price, 2) }}</div>
                            <small class="text-muted">Stock: {{ $product->stock }}</small>
                        </div>
                        @auth
                        <div class="input-group mb-3" style="max-width: 200px;">
                            <span class="input-group-text">Qty</span>
                            <input type="number" id="qty" class="form-control" value="1" min="1" max="99">
                        </div>
                        <button class="btn btn-success add-to-cart" 
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-secondary">Login to Add to Cart</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($relatedProducts->count())
    <div class="mt-5">
        <h4 class="mb-3">Related Products</h4>
        <div class="row">
            @foreach($relatedProducts as $rp)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        @if($rp->image)
                            <img src="{{ asset('storage/' . $rp->image) }}" class="card-img-top" alt="{{ $rp->name }}" style="height: 160px; object-fit: cover;">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light" style="height: 160px;">
                                <i class="fas fa-image text-muted" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-1">{{ $rp->name }}</h6>
                            <div class="text-muted mb-2">${{ number_format($rp->price, 2) }}</div>
                            <a href="{{ route('products.show', $rp) }}" class="btn btn-outline-primary mt-auto">View</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.querySelector('.add-to-cart');
    if (!addBtn) return;
    addBtn.addEventListener('click', function() {
        const qtyInput = document.getElementById('qty');
        const qty = qtyInput ? parseInt(qtyInput.value || '1', 10) : 1;
        const productId = this.dataset.productId;

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

        fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: Math.max(1, Math.min(99, qty))
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Update all cart count badges
                document.querySelectorAll('.cart-count-badge').forEach(el => el.textContent = data.cart_count);
            } else {
                alert('Error: ' + (data.message || 'Could not add to cart'));
            }
        })
        .catch(() => alert('Error adding item to cart'))
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
        });
    });
});
</script>
@endpush
@endsection

