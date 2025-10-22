<aside id="leftsidebar" class="sidebar">
    <div class="menu">
        <ul class="list">
            <li class="sidebar-user-panel">
                <div class="user-panel">
                    <div class="image">
                        <img src="{{ asset('assets/images/usrbig.jpg') }}" class="user-img-style" alt="User Image" />
                    </div>
                </div>
                <div class="profile-usertitle">
                    <div class="sidebar-userpic-name">{{ Auth::user()->name }}</div>
                    <div class="profile-usertitle-job">Club Panel</div>
                </div>
            </li>


            <li class="{{ request()->routeIs('club-dashboard') ? 'active' : '' }}">
                <a href="{{ route('club-dashboard') }}">
                    <i class="zmdi zmdi-view-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->is('club/teams*') ? 'active' : '' }}">
                <a href="{{ route('club.teams.index') }}">
                    <i class="zmdi zmdi-accounts"></i>
                    <span>Teams</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('club.events.*') ? 'active' : '' }}">
                <a href="{{ route('club.events.index') }}">
                    <i class="zmdi zmdi-calendar"></i>
                    <span>Events</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('club.games.*') ? 'active' : '' }}">
                <a href="{{ route('club.games.index') }}">
                    <i class="zmdi zmdi-flag"></i>
                    <span>Games</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('club.injury_reports.*') ? 'active' : '' }}">
                <a href="{{ route('club.injury_reports.index') }}">
                    <i class="zmdi zmdi-hospital"></i>
                    <span>Injury Reports</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('club.calendar') ? 'active' : '' }}">
                <a href="{{ route('club.calendar') }}">
                    <i class="zmdi zmdi-calendar-note"></i>
                    <span>Calendar</span>
                </a>
            </li>
            @php($clubId = optional(auth()->user()->club)->id)
            <li class="{{ request()->routeIs('club.transfers.*') ? 'active' : '' }}">
                <a href="{{ route('club.transfers.index') }}">
                    <i class="zmdi zmdi-arrow-left-right"></i>
                    <span>Transfer Requests</span>
                    @if($clubId)
                        @php($pendingTransfers = \App\Models\PlayerTransfer::where('to_club_id', $clubId)->where('status', 'pending')->count())
                        @if($pendingTransfers > 0)
                            <span class="badge bg-warning text-dark ms-2">{{ $pendingTransfers }}</span>
                        @endif
                    @endif
                </a>
            </li>

            <!-- Store Management -->
            <li class="{{ request()->routeIs('club.store.products.*') ? 'active' : '' }}">
                <a href="{{ route('club.store.products.index') }}">
                    <i class="zmdi zmdi-store"></i>
                    <span>Store Products</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('club.store.categories.*') ? 'active' : '' }}">
                <a href="{{ route('club.store.categories.index') }}">
                    <i class="zmdi zmdi-label"></i>
                    <span>Product Categories</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('club.store.orders.*') ? 'active' : '' }}">
                <a href="{{ route('club.store.orders.index') }}">
                    <i class="zmdi zmdi-receipt"></i>
                    <span>Store Orders</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('club.store.settings.*') ? 'active' : '' }}">
                <a href="{{ route('club.store.settings.payments') }}">
                    <i class="zmdi zmdi-settings"></i>
                    <span>Store Settings</span>
                </a>
            </li>
        </ul>
    </div>

    <style>
        .sidebar .menu {
            height: 50vh;
        }
    </style>
    @include('partials.dashboard_reminders')
    @include('partials.dashboard_referral_club')

    <div class="p-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100">
                <i class="zmdi zmdi-power"></i> Logout
            </button>
        </form>
    </div>

</aside>
