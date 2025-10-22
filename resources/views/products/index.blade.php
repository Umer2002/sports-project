@extends('layouts.shop')
@section('title', 'Products')

@section('content')
    <div class="container">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5>Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('products.index') }}">
                            <!-- Search -->
                            <div class="mb-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search"
                                    value="{{ request('search') }}" placeholder="Search products...">
                            </div>

                            <!-- Category Filter -->
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sort -->
                            <div class="mb-3">
                                <label for="sort" class="form-label">Sort By</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First
                                    </option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price:
                                        Low to High</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>
                                        Price: High to Low</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary w-100 mt-2">Clear Filters</a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Products</h2>
                    <div class="text-muted">
                        {{ $products->total() }} products found
                    </div>
                </div>

                @if ($products->count() > 0)
                    <div class="row">
                        @foreach ($products as $product)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                            alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                            style="height: 200px;">
                                            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>

                                        @if ($product->category)
                                            <span class="badge bg-primary mb-2">{{ $product->category->name }}</span>
                                        @endif

                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="h5 mb-0">${{ number_format($product->price, 2) }}</span>
                                                <small class="text-muted">Stock: {{ $product->stock }}</small>
                                            </div>

                                            <div class="d-grid gap-2">
                                                <a href="{{ route('products.show', $product) }}"
                                                    class="btn btn-outline-primary">
                                                    View Details
                                                </a>
                                                @auth
                                                    <button class="btn btn-success add-to-cart"
                                                        data-product-id="{{ $product->id }}"
                                                        data-product-name="{{ $product->name }}"
                                                        data-product-price="{{ $product->price }}">
                                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                                    </button>
                                                @else
                                                    <a href="{{ route('login') }}" class="btn btn-secondary">
                                                        Login to Add to Cart
                                                    </a>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <h4>No products found</h4>
                        <p class="text-muted">Try adjusting your filters or search terms.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Add to cart functionality
                document.querySelectorAll('.add-to-cart').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const productId = this.dataset.productId;
                        const productName = this.dataset.productName;
                        const productPrice = this.dataset.productPrice;

                        // Show quantity prompt
                        const quantity = prompt(
                            `How many "${productName}" would you like to add to cart? (1-99)`, '1');

                        if (quantity && !isNaN(quantity) && quantity >= 1 && quantity <= 99) {
                            // Disable button and show loading
                            this.disabled = true;
                            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

                            fetch('/cart/add', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        product_id: productId,
                                        quantity: parseInt(quantity)
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Show success message
                                        alert(data.message);

                                        // Update cart count badges (all instances)
                                        document.querySelectorAll('.cart-count-badge').forEach(
                                        el => {
                                            el.textContent = data.cart_count;
                                        });
                                    } else {
                                        alert('Error: ' + data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Error adding item to cart');
                                })
                                .finally(() => {
                                    // Re-enable button
                                    this.disabled = false;
                                    this.innerHTML =
                                        '<i class="fas fa-shopping-cart"></i> Add to Cart';
                                });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
