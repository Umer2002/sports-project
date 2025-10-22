<nav class="sidebar" id="sidebar">
        <div class="sidebar-container">
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="d-flex flex-column align-items-center">
                        <div class="mb-2">
                            <img src="{{ asset('assets/images/logo.png') }}" width="110" height="88" alt="Play2Earn"
                                class="log play-logo">
                        </div>
                        <div class="mb-2">
                            <img src="{{ $profilePicture }}" width="70" height="68" alt="{{ $refereeName }}"
                                class="log osu-logo rounded-3" style="object-fit: cover;">
                        </div>
                    </div>
                </div>
                <div class="club-info">
                    <div class="club-name">{{ $refereeName }}</div>
                    <div class="club-type">{{ $refereeTagline }}</div>
                </div>
            </div>

            <div class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">MAIN</div>

                    <div class="nav-item">
                        <a href="{{ route('referee.dashboard') }}"
                            class="nav-link {{ request()->routeIs('referee.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="{{ route('referee.availability.form') }}"
                            class="nav-link {{ request()->routeIs('referee.availability.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check"></i>
                            <span class="nav-text">Availability</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="{{ route('referee.matches.available') }}"
                            class="nav-link {{ request()->routeIs('referee.matches.*') ? 'active' : '' }}">
                            <i class="fas fa-flag-checkered"></i>
                            <span class="nav-text">Available Games</span>
                        </a>
                    </div>



                    <div class="nav-item">
                        <a href="{{ route('referee.email') }}"
                            class="nav-link {{ request()->routeIs('referee.email') ? 'active' : '' }}">
                            <i class="fas fa-envelope"></i>
                            <span class="nav-text">Email</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="{{ route('my-account') }}"
                            class="nav-link {{ request()->routeIs('my-account') ? 'active' : '' }}">
                            <i class="fas fa-user-circle"></i>
                            <span class="nav-text">My Account</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-button">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>
