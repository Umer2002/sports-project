@php
    $currentUser  = auth()->user();
    $playerModel  = $player ?? ($currentUser?->player);
    $sportName    = optional(optional($playerModel)->sport)->name;
    $sportLabel   = trim(($sportName ?: 'Soccer') . ' Dashboard');
    $cartCount    = optional($currentUser)->cart_item_count;

    // Helper: determine active state for parent if any child matches
    $isRouteGroupActive = function (array $patterns) {
        foreach ($patterns as $p) {
            if (request()->routeIs($p)) return true;
        }
        return false;
    };

    $menuItems = [
        [
            'route'  => 'player.dashboard',
            'match'  => ['player.dashboard', 'player.dashboard.new', 'player.dashboard.old'],
            'icon'   => 'fa-solid fa-house',
            'label'  => $sportLabel,
        ],
        [
            'route'  => 'player.invite.create',
            'match'  => ['player.invite.create', 'player.invite.store'],
            'icon'   => 'fa-regular fa-envelope',
            'label'  => 'Invite',
        ],
        [
            'route'  => 'player.invite.club.create',
            'match'  => ['player.invite.club.create', 'player.invite.club.store'],
            'icon'   => 'fa-regular fa-envelope',
            'label'  => 'Club Invite',
        ],
        [
            'route'  => 'player.invite.overview',
            'match'  => ['player.invite.overview'],
            'icon'   => 'fa-solid fa-money-check-dollar',
            'label'  => 'Invite Rewards',
        ],

        [
            'route'  => 'player.pickup-games.index',
            'match'  => ['player.pickup-games.*'],
            'icon'   => 'fa-solid fa-gamepad',
            'label'  => 'Pickup Games',
        ],
        [
            'route'  => 'player.blogs.index',
            'match'  => ['player.blogs.*'],
            'icon'   => 'fa-solid fa-feather-pointed',
            'label'  => 'Blogs',
            'children' => [
                [
                    'route' => 'player.blogs.index',
                    'match' => ['player.blogs.index', 'player.blogs.show'],
                    'icon'  => 'fa-solid fa-list',
                    'label' => 'My Posts',
                ],
                [
                    'route' => 'player.blogs.create',
                    'match' => ['player.blogs.create'],
                    'icon'  => 'fa-solid fa-pen-to-square',
                    'label' => 'Create Post',
                ],
            ],
        ],
        [
            'route'  => 'player.transfers.index',
            'match'  => ['player.transfers.*'],
            'icon'   => 'fa-solid fa-exchange-alt',
            'label'  => 'Transfers',
        ],
        [
            'route'  => 'player.videos.index',
            'match'  => ['player.videos.*'],
            'icon'   => 'fa-solid fa-video',
            'label'  => 'Videos',
        ],

        // === E-Commerce: Play2Earn ===
        [
            'route'    => 'player.products.store', // parent anchor can be Shop or '#'
            'icon'     => 'fa-solid fa-store',
            'label'    => 'E-Commerce · Play2Earn',
            // parent is active if any of its children are active
            'match'    => ['player.products.store', 'products.*', 'cart.*', 'checkout.*'],
            'children' => [
                [
                    'route' => 'player.products.store',
                    'match' => ['player.products.store', 'products.*'],
                    'icon'  => 'fa-solid fa-bag-shopping',
                    'label' => 'Shop',
                ],
                [
                    'route' => 'cart.index',
                    'match' => ['cart.*'],
                    'icon'  => 'fa-solid fa-cart-shopping',
                    'label' => 'Cart',
                    'badge' => $cartCount,
                ],
                [
                    'route' => 'checkout.index',
                    'match' => ['checkout.*'],
                    'icon'  => 'fa-solid fa-cash-register',
                    'label' => 'Checkout',
                ],
            ],
        ],
    ];

    // === E-Commerce: Clubs (only if player has a club) ===
        $menuItems[] = [
            'route'    => 'player.products.club',
            'icon'     => 'fa-solid fa-shirt',
            'label'    => 'E-Commerce · Clubs',
            'match'    => ['player.products.club', 'cart.*', 'checkout.*'], // include subroutes for active state
            'children' => [
                [
                    'route' => 'player.products.club',
                    'match' => ['player.products.club'],
                    'icon'  => 'fa-solid fa-store',
                    'label' => 'Shop',
                    // 'params' => ['club' => optional($playerModel->club)->slug], // uncomment if your route needs a slug
                ],
                [
                    'route' => 'cart.index',
                    'match' => ['cart.*'],
                    'icon'  => 'fa-solid fa-cart-shopping',
                    'label' => 'Cart',
                    'badge' => $cartCount,
                ],
                [
                    'route' => 'checkout.index',
                    'match' => ['checkout.*'],
                    'icon'  => 'fa-solid fa-cash-register',
                    'label' => 'Checkout',
                ],
            ],
        ];


    // Keep Orders as a separate top-level item (optional)
    $menuItems[] = [
        'route' => 'orders.index',
        'match' => ['orders.*'],
        'icon'  => 'fa-solid fa-receipt',
        'label' => 'Orders',
    ];
@endphp

@foreach ($menuItems as $item)
    @php
        $hasChildren = !empty($item['children'] ?? []);
        $matches     = $item['match'] ?? [$item['route']];
        $isActive    = $hasChildren
            ? $isRouteGroupActive($matches)
            : collect($matches)->some(fn($m) => request()->routeIs($m));
    @endphp

    <li class="menu-item {{ $isActive ? 'active' : '' }} {{ $hasChildren ? 'has-children' : '' }}">
        <a href="{{ route($item['route'], $item['params'] ?? []) }}" {{ $hasChildren ? 'data-toggle=submenu' : '' }}>
            <span>
                <i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}
            </span>
        </a>

        @if ($hasChildren)
            <ul class="submenu">
                @foreach ($item['children'] as $child)
                    @php
                        $childMatches = $child['match'] ?? [$child['route']];
                        $childActive  = collect($childMatches)->some(fn($m) => request()->routeIs($m));
                    @endphp
                    <li class="submenu-item {{ $childActive ? 'active' : '' }}">
                        <a href="{{ route($child['route'], $child['params'] ?? []) }}">
                            <span>
                                <i class="{{ $child['icon'] }}"></i> {{ $child['label'] }}
                                @if(!empty($child['badge']))
                                    <span class="badge rounded-pill bg-danger ms-1 cart-count-badge">{{ $child['badge'] }}</span>
                                @endif
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </li>
@endforeach
