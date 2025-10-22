@php
    use Illuminate\Support\Str;
@endphp

@extends('layouts.player-new')

@section('title', $title)
@section('page-title', $title)

@section('content')
    <div class="container-fluid py-3">
        <div class="same-card mb-3 p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h2 class="mb-1">{{ $title }}</h2>
                    <p class="text-muted mb-0">{{ $subtitle }}</p>
                </div>
                <span class="badge bg-secondary align-self-start align-self-md-center">
                    {{ $products->total() }} {{ Str::plural('item', $products->total()) }}
                </span>
            </div>
        </div>

        @if ($products->count())
            <div class="row g-3">
                @foreach ($products as $product)
                    @php
                        $imagePath = $product->image;
                        if ($imagePath && !Str::startsWith($imagePath, ['http://', 'https://'])) {
                            $imagePath = Str::of($imagePath)->ltrim('/')->toString();
                            if (Str::startsWith($imagePath, 'public/')) {
                                $imagePath = Str::substr($imagePath, 7);
                            }
                            $imagePath = asset('storage/' . $imagePath);
                        }
                    @endphp
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm product-card">
                            <div class="ratio ratio-4x3 bg-light">
                                @if ($imagePath)
                                    <img src="{{ $imagePath }}" class="w-100 h-100" alt="{{ $product->name }}"
                                        style="object-fit: cover; border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
                                @else
                                    <div class="d-flex flex-column justify-content-center align-items-center text-muted">
                                        <i class="fa-solid fa-box-open fa-2xl mb-2"></i>
                                        <small>No image provided</small>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <h5 class="card-title mb-1">{{ $product->name }}</h5>
                                    <div class="text-muted small">
                                        {{ Str::limit($product->description, 110) ?: 'No description available.' }}
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @if ($product->category)
                                        <span class="badge bg-primary">{{ $product->category->name }}</span>
                                    @endif
                                    @if ($product->club)
                                        <span class="badge bg-info text-dark">{{ $product->club->name }}</span>
                                    @endif
                                </div>

                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="h5 mb-0">${{ number_format($product->price, 2) }}</span>
                                        <span class="text-muted small">Stock: {{ max(0, $product->stock) }}</span>
                                    </div>
                                    <button type="button" class="btn btn-primary w-100 add-to-cart"
                                        data-product-id="{{ $product->id }}"
                                        data-product-name="{{ $product->name }}"
                                        data-product-price="{{ $product->price }}">
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{-- {{ products->links()}} --}}
            </div>
        @else
            <div class="same-card p-4 text-center">
                <i class="fa-solid fa-bag-shopping fa-2xl text-muted mb-3"></i>
                <h4 class="text-muted">{{ $emptyMessage }}</h4>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.add-to-cart').forEach(function(button) {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const productName = this.dataset.productName;

                    const quantity = prompt(`How many "${productName}" would you like to add to cart? (1-10)`, '1');

                    if (quantity && !isNaN(quantity) && quantity >= 1 && quantity <= 10) {
                        const qty = parseInt(quantity, 10);
                        const originalLabel = this.innerHTML;
                        this.disabled = true;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

                        fetch('{{ route('cart.add') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    product_id: productId,
                                    quantity: qty
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert(data.message);
                                    if (data.cart_count !== undefined) {
                                        document.querySelectorAll('.cart-count-badge').forEach(el => {
                                            el.textContent = data.cart_count;
                                        });
                                    }
                                } else {
                                    alert(data.message || 'Unable to add item to cart.');
                                }
                            })
                            .catch(() => {
                                alert('Unable to add item to cart. Please try again.');
                            })
                            .finally(() => {
                                this.disabled = false;
                                this.innerHTML = originalLabel;
                            });
                    }
                });
            });
        });
    </script>
@endpush
