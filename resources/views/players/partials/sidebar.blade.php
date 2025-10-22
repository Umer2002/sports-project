@once
    @push('styles')
        <style>
            .player-sidebar__nav .menu-item {
                margin-bottom: 0.25rem;
            }

            .player-sidebar__nav .menu-item a {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.5rem 0.75rem;
                border-radius: 0.5rem;
                color: inherit;
                text-decoration: none;
                transition: background-color 0.2s ease, color 0.2s ease;
            }

            .player-sidebar__nav .menu-item a:hover,
            .player-sidebar__nav .menu-item.active a {
                background-color: rgba(255, 255, 255, 0.1);
                color: #fff;
            }

            .player-sidebar__nav .menu-item i {
                width: 1.25rem;
                text-align: center;
            }

            .player-sidebar__nav .cart-count-badge {
                font-size: 0.7rem;
            }
        </style>
    @endpush
@endonce

<div class="player-sidebar bg-dark text-white rounded-3 p-3">
    <div class="mb-3">
        <h6 class="text-uppercase text-muted mb-0">Main</h6>
    </div>
    <ul class="list-unstyled mb-0 player-sidebar__nav">
        @include('players.partials.sidebar-menu-items', ['player' => $player ?? null])
    </ul>
</div>
