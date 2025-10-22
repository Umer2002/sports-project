    @extends('layouts.player-new')
    @section('title', 'New Challenge')
    @section('content')

        <div class="mb-3 overflow-hidden position-relative">
            <div class="px-3">
                <h4 class="fs-6 mb-0">Shop</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="/">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">Shop</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card position-relative overflow-hidden">
            <div class="shop-part d-flex w-100">
                <!-- Filters Sidebar -->
                <div class="shop-filters flex-shrink-0 border-end d-none d-lg-block">
                    <ul class="list-group pt-2 border-bottom rounded-0">
                        <h6 class="my-3 mx-4">Filter by Category</h6>
                        <li class="list-group-item border-0 p-0 mx-4 mb-2">
                            <a class="d-flex align-items-center gap-6 list-group-item-action text-body px-3 py-6 rounded-1 {{ request('category') ? '' : 'active' }}"
                                href="{{ route('ecommerce.shop', array_merge(request()->except('page', 'category'))) }}">
                                <i class="ti ti-circles fs-5"></i>All
                            </a>
                        </li>
                        @foreach ($categories as $category)
                            <li class="list-group-item border-0 p-0 mx-4 mb-2">
                                <a class="d-flex align-items-center gap-6 list-group-item-action text-body px-3 py-6 rounded-1 {{ request('category') == $category->id ? 'active' : '' }}"
                                    href="{{ route('ecommerce.shop', array_merge(request()->except('page'), ['category' => $category->id])) }}">
                                    <i class="ti ti-tag fs-5"></i>{{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <ul class="list-group pt-2 border-bottom rounded-0">
                        <h6 class="my-3 mx-4">Sort By</h6>
                        <li class="list-group-item border-0 p-0 mx-4 mb-2">
                            <a class="d-flex align-items-center gap-6 list-group-item-action text-body px-3 py-6 rounded-1 {{ request('sort') == 'newest' || !request('sort') ? 'active' : '' }}"
                                href="{{ route('ecommerce.shop', array_merge(request()->except('page'), ['sort' => 'newest'])) }}">
                                <i class="ti ti-ad-2 fs-5"></i>Newest
                            </a>
                        </li>
                        <li class="list-group-item border-0 p-0 mx-4 mb-2">
                            <a class="d-flex align-items-center gap-6 list-group-item-action text-body px-3 py-6 rounded-1 {{ request('sort') == 'price_high' ? 'active' : '' }}"
                                href="{{ route('ecommerce.shop', array_merge(request()->except('page'), ['sort' => 'price_high'])) }}">
                                <i class="ti ti-sort-ascending-2 fs-5"></i>Price: High-Low
                            </a>
                        </li>
                        <li class="list-group-item border-0 p-0 mx-4 mb-2">
                            <a class="d-flex align-items-center gap-6 list-group-item-action text-body px-3 py-6 rounded-1 {{ request('sort') == 'price_low' ? 'active' : '' }}"
                                href="{{ route('ecommerce.shop', array_merge(request()->except('page'), ['sort' => 'price_low'])) }}">
                                <i class="ti ti-sort-descending-2 fs-5"></i>Price: Low-High
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Products Grid -->
                <div class="card-body p-4 pb-0">
                    <div class="d-flex justify-content-between align-items-center gap-6 mb-4">
                        <a class="btn btn-primary d-lg-none d-flex" data-bs-toggle="offcanvas" href="#filtercategory"
                            role="button" aria-controls="filtercategory">
                            <i class="ti ti-menu-2 fs-6"></i>
                        </a>
                        <h5 class="fs-5 mb-0 d-none d-lg-block">Products</h5>
                        <form class="position-relative" method="GET" action="{{ route('ecommerce.shop') }}">
                            <input type="text" class="form-control search-chat py-2 ps-5" name="search"
                                value="{{ request('search') }}" placeholder="Search Product">
                        <i
                            class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-body-secondary ms-3"></i>
                        </form>
                    </div>
                    <div class="row">
                        @forelse($products as $product)
                            <div class="col-sm-6 col-xxl-4 mb-4">
                                <div class="card hover-img overflow-hidden h-100">
                                    <div class="position-relative">
                                        <a href="{{ route('products.show', $product) }}">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                                    alt="{{ $product->name }}" style="height:250px; object-fit:cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center"
                                                    style="height:250px;">
                                                    <i class="ti ti-image fs-1 text-muted"></i>
                                                </div>
                                            @endif
                                        </a>
                                        @auth
                                            <button
                                                class="text-bg-primary rounded-circle p-2 text-white d-inline-flex position-absolute bottom-0 end-0 mb-n3 me-3 add-to-cart"
                                                data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}"
                                                data-product-price="{{ $product->price }}" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Add To Cart">
                                                <i class="ti ti-basket fs-4"></i>
                                            </button>
                                        @else
                                            <a href="{{ route('login') }}"
                                                class="text-bg-primary rounded-circle p-2 text-white d-inline-flex position-absolute bottom-0 end-0 mb-n3 me-3"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Login to purchase">
                                                <i class="ti ti-login fs-4"></i>
                                            </a>
                                        @endauth
                                    </div>
                                    <div class="card-body pt-3 p-4 d-flex flex-column">
                                        <h6 class="fs-4 flex-grow-1">{{ $product->name }}</h6>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h6 class="fs-4 mb-0">${{ number_format($product->price, 2) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <h5>No products found</h5>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.add-to-cart').forEach(function(button) {
                        button.addEventListener('click', function() {
                            const productId = this.dataset.productId;
                            const productName = this.dataset.productName;
                            const quantity = 1;

                            this.disabled = true;

                            fetch('/cart/add', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        product_id: productId,
                                        quantity: quantity
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        alert(data.message);
                                    } else {
                                        alert('Error: ' + data.message);
                                    }
                                })
                                .catch(() => {
                                    alert('Error adding item to cart');
                                })
                                .finally(() => {
                                    this.disabled = false;
                                });
                        });
                    });
                });
            </script>
        @endpush
    @endsection
