<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Shop') - Play2Earn</title>

    <link rel="icon" type="image/png" href="{{ asset('storage/theme/logo.png') }}">

    <!-- Player dashboard styles for consistent look -->
    <link rel="stylesheet" href="{{ asset('assets/player-dashboard/css/main.css') }}">
    <!-- Bootstrap + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* Minimal header styled to match dashboard palette */
        .shop-header { background: var(--primary-sport-color, #39a2ff); }
        .shop-header .brand { display:flex; align-items:center; gap:10px; color:#fff; text-decoration:none; }
        .shop-header img { height: 36px; width:auto; }
        .shop-header a.nav-link { color:#fff; opacity: .95; }
        .shop-header a.nav-link.active { font-weight: 600; opacity: 1; }
    </style>

    @yield('header_styles')
</head>
<body>
    <header class="shop-header py-2">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('products.index') }}" class="brand">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Play2Earn">
                <span>Shop</span>
            </a>
            <nav class="d-flex align-items-center gap-3">
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Products</a>
                <a class="nav-link {{ request()->routeIs('cart.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                    <i class="fas fa-shopping-cart"></i>
                    Cart
                    @auth
                        <span class="badge rounded-pill bg-danger ms-1 cart-count-badge">{{ auth()->user()->cart_item_count }}</span>
                    @endauth
                </a>
            </nav>
        </div>
    </header>

    @php
        $authUser = auth()->user();
        // Try to resolve $player for sidebar if not provided
        $playerCtx = isset($player) ? $player : ($authUser && method_exists($authUser, 'player') ? $authUser->player : null);
    @endphp

    @if($authUser && $authUser->hasRole('player'))
        <div class="main-dashboard">
            <i class="fa fa-bars hamburger" id="hamburger"></i>
            <div class="overlay" id="overlay"></div>

            <!-- Left Sidebar (player-style) -->
            <div class="left-bar" id="sidebar">
                <div class="left-main">
                    <div class="row">
                        <div class="col-12">
                            <div class="logo">
                                <a href="{{ route('player.dashboard') }}">
                                    <img src="{{ asset('assets/player-dashboard/images/logo.png') }}" alt="Logo">
                                </a>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="Left-profile">
                                @if(isset($playerCtx) && !empty($playerCtx->photo))
                                    <img src="{{ asset('storage/players/' . $playerCtx->photo) }}" alt="Profile" class="img-fluid w-50">
                                @else
                                    <img src="{{ asset('assets/player-dashboard/images/profile.png') }}" alt="Profile" class="img-fluid w-50">
                                @endif
                                <h2>{{ ucfirst($authUser->name) }}</h2>
                                <p>{{ isset($playerCtx) && $playerCtx->position ? $playerCtx->position->position_name : 'Player' }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="sidebar">
                                <h4>main</h4>
                                <ul>
                                    <li class="menu-item {{ request()->routeIs('player.dashboard*') ? 'active' : '' }}">
                                        <a href="{{ route('player.dashboard') }}">
                                            <span><i class="fa-solid fa-house"></i> Dashboard</span>
                                        </a>
                                    </li>
                                    <li class="menu-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                        <a href="{{ route('products.index') }}">
                                            <span><i class="fa-solid fa-store"></i> Shop</span>
                                        </a>
                                    </li>
                                    <li class="menu-item {{ request()->routeIs('cart.*') ? 'active' : '' }}">
                                        <a href="{{ route('cart.index') }}">
                                            <span><i class="fa-solid fa-cart-shopping"></i> Cart</span>
                                        </a>
                                    </li>
                                    <li class="menu-item {{ request()->routeIs('checkout.*') ? 'active' : '' }}">
                                        <a href="{{ route('checkout.index') }}">
                                            <span><i class="fa-solid fa-credit-card"></i> Checkout</span>
                                        </a>
                                    </li>
                                    <li class="menu-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                                        <a href="{{ route('orders.index') }}">
                                            <span><i class="fa-solid fa-receipt"></i> Orders</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="right-bar">
                @yield('content')
            </div>
        </div>
    @else
        <main class="py-4">
            @yield('content')
        </main>
    @endif

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ensure cart badge reflects server cart on every page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/cart/summary', { headers: { 'Accept': 'application/json' }})
                .then(r => r.ok ? r.json() : null)
                .then(data => {
                    if (!data) return;
                    document.querySelectorAll('.cart-count-badge').forEach(el => el.textContent = data.cart_count ?? 0);
                })
                .catch(() => {});
        });
    </script>
</body>
</html>
