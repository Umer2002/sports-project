<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Coach Dashboard') - {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('storage/theme/logo.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/main.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Dashboard Styles -->
    <link rel="stylesheet" href="{{ asset('assets/club-dashboard-main/index.css') }}">

    @yield('header_styles')

    <style>
        /* Full screen height for coach dashboard */
        body {
            height: 100vh !important;
            overflow: hidden !important;
        }

        .main-content {
            height: 100vh !important;
            overflow-y: auto !important;
        }

        .content-area {
            min-height: calc(100vh - 80px) !important;
            /* Subtract header height */
            height: auto !important;
        }

        /* User Profile Dropdown Styles */
        .user-profile .dropdown-toggle {
            text-decoration: none;
            color: inherit;
        }

        .user-profile .dropdown-toggle::after {
            display: none;
        }

        .user-profile .dropdown-menu {
            min-width: 200px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #dee2e6;
        }

        .user-profile .dropdown-item {
            padding: 8px 16px;
            font-size: 14px;
        }

        .user-profile .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .user-profile .dropdown-item i {
            width: 16px;
            margin-right: 8px;
        }
        .composer-primary{
            background: linear-gradient(130deg, #38bdf8, #6366f1);
            color: #fff;
            box-shadow: 0 18px 35px -22px rgba(99, 102, 241, 0.6);
        }
    </style>
</head>

<body class="dark" data-bs-theme="dark">
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-chevron-left"></i>
    </button>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-container">
            <!-- Header -->
            <div class="sidebar-header">
                <div class="logo-container">
                    <div class="d-flex flex-column align-items-center">
                        <div class="mb-2">
                            <img src="{{ asset('assets/images/logo.png') }}" width="110" height="88"
                                alt="play2earn" class="log play-logo" />
                        </div>
                        @php($currentCoach = auth()->user()->coach ?? null)
                        @if ($currentCoach)
                            <div class="mb-2">
                                @if ($currentCoach->photo)
                                    <img src="{{ Storage::url($currentCoach->photo) }}" width="70" height="68"
                                        alt="{{ $currentCoach->first_name }} {{ $currentCoach->last_name }}"
                                        class="log osu-logo" />
                                @else
                                    <div class="log osu-logo d-flex align-items-center justify-content-center"
                                        style="width: 70px; height: 68px; background: #4299e1; color: white; border-radius: 8px; font-weight: bold;">
                                        {{ substr($currentCoach->first_name, 0, 1) }}{{ substr($currentCoach->last_name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="club-info">
                    <div class="club-name">
                        {{ $currentCoach ? $currentCoach->first_name . ' ' . $currentCoach->last_name : 'Coach Name' }}
                    </div>
                    <div class="club-type">COACH</div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">MAIN</div>

                    <!-- Dashboard -->
                    <div class="nav-item">
                        <a href="{{ route('coach-dashboard') }}"
                            class="nav-link {{ request()->routeIs('coach-dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </div>

                    <!-- Awards -->
                    <div class="nav-item">
                        <a href="{{ route('coach.awards.index') }}"
                            class="nav-link {{ request()->routeIs('coach.awards.*') ? 'active' : '' }}">
                            <i class="fas fa-trophy"></i>
                            <span class="nav-text">Awards</span>
                        </a>
                    </div>
                    <!-- Events -->
                    <div class="nav-item">
                        <a href="{{ route('coach.events.index') }}"
                            class="nav-link {{ request()->routeIs('coach.events.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="nav-text">Events</span>
                        </a>
                    </div>

                    <!-- Teams -->
                    <div class="nav-item">
                        <button class="nav-link {{ request()->routeIs('coach.teams.*') ? 'active' : '' }}"
                            type="button" data-bs-toggle="collapse" data-bs-target="#teamsSubmenu"
                            aria-expanded="{{ request()->routeIs('coach.teams.*') ? 'true' : 'false' }}"
                            aria-controls="teamsSubmenu">
                            <i class="fas fa-users"></i>
                            <span class="nav-text">Teams</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </button>
                        <div class="collapse {{ request()->routeIs('coach.teams.*') ? 'show' : '' }} sub-nav"
                            id="teamsSubmenu">
                            <a href="{{ route('coach.teams.index') }}" class="nav-link">
                                <span class="nav-text">All Teams</span>
                            </a>
                        </div>
                    </div>

                    <!-- Players -->
                    <div class="nav-item">
                        <button class="nav-link {{ request()->routeIs('coach.players.*') ? 'active' : '' }}"
                            type="button" data-bs-toggle="collapse" data-bs-target="#playersSubmenu"
                            aria-expanded="{{ request()->routeIs('coach.players.*') ? 'true' : 'false' }}"
                            aria-controls="playersSubmenu">
                            <i class="fas fa-user-friends"></i>
                            <span class="nav-text">Players</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </button>
                        <div class="collapse {{ request()->routeIs('coach.players.*') ? 'show' : '' }} sub-nav"
                            id="playersSubmenu">
                            <a href="{{ route('coach.players.index') }}" class="nav-link">
                                <span class="nav-text">All Players</span>
                            </a>
                        </div>
                    </div>

                    {{-- <!-- Training Sessions -->
                    <div class="nav-item">
                        <a href="{{ route('coach.training.index') }}"
                            class="nav-link {{ request()->routeIs('coach.training.*') ? 'active' : '' }}">
                            <i class="fas fa-dumbbell"></i>
                            <span class="nav-text">Training Sessions</span>
                        </a>
                    </div>

                    <!-- Match Analysis -->
                    <div class="nav-item">
                        <a href="{{ route('coach.matches.index') }}"
                            class="nav-link {{ request()->routeIs('coach.matches.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-text">Match Analysis</span>
                        </a>
                    </div> --}}

                    <!-- Tournaments -->
                    <div class="nav-item">
                        <a href="{{ route('coach.tournaments.index') }}"
                            class="nav-link {{ request()->routeIs('coach.tournaments.*') ? 'active' : '' }}">
                            <i class="fas fa-trophy"></i>
                            <span class="nav-text">Tournaments</span>
                        </a>
                    </div>
                    <!-- Tasks -->
                    <div class="nav-item">
                        <a href="{{ route('coach.tasks.index') }}"
                            class="nav-link {{ request()->routeIs('coach.tasks.*') ? 'active' : '' }}">
                            <i class="fas fa-tasks"></i>
                            <span class="nav-text">Tasks</span>
                        </a>
                    </div>
                    <!-- Blog -->
                    <div class="nav-item">
                        <a href="{{ route('coach.blog.index') }}"
                            class="nav-link {{ request()->routeIs('coach.blog.*') ? 'active' : '' }}">
                            <i class="fas fa-blog"></i>
                            <span class="nav-text">Blog</span>
                        </a>
                    </div>
                    <!-- Settings -->
                    <div class="nav-item">
                        <a href="{{ route('coach.profile.edit') }}"
                            class="nav-link {{ request()->routeIs('coach.profile.*') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span class="nav-text">Settings</span>
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

    <!-- Main Content -->
    <div id="mainContent" class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="header-center">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search" id="search-box" />
                </div>
            </div>
            <div class="header-right">
                <div class="header-icon">
                    <i class="fas fa-expand"></i>
                </div>
                <div class="header-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="header-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="header-icon" id="themeToggle">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </div>

            </div>
        </header>

        <!-- Content Area -->
        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>
    @stack('scripts')
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("mainContent");
            const sidebarToggle = document.getElementById("sidebarToggle");
            const sidebarOverlay = document.getElementById("sidebarOverlay");

            // Toggle sidebar
            sidebarToggle.addEventListener("click", function() {
                const isLargeScreen = window.innerWidth >= 992;

                if (isLargeScreen) {
                    // Large screen: collapse/expand
                    sidebar.classList.toggle("collapsed");
                    mainContent.classList.toggle("expanded");
                    sidebarToggle.classList.toggle("collapsed");
                } else {
                    // Mobile: show/hide drawer
                    sidebar.classList.toggle("show");
                    sidebarOverlay.classList.toggle("show");
                }
            });

            // Close mobile drawer when clicking overlay
            sidebarOverlay.addEventListener("click", function() {
                sidebar.classList.remove("show");
                sidebarOverlay.classList.remove("show");
            });

            // Handle window resize
            window.addEventListener("resize", function() {
                const isLargeScreen = window.innerWidth >= 992;

                if (isLargeScreen) {
                    // Remove mobile classes
                    sidebar.classList.remove("show");
                    sidebarOverlay.classList.remove("show");
                } else {
                    // Remove desktop classes
                    sidebar.classList.remove("collapsed");
                    mainContent.classList.remove("expanded");
                    sidebarToggle.classList.remove("collapsed");
                }
            });

            // Handle nav link clicks
            document.querySelectorAll(".nav-link:not([data-bs-toggle])").forEach((link) => {
                link.addEventListener("click", function(e) {
                    // Remove active class from all nav links
                    document.querySelectorAll(".nav-link").forEach((l) => l.classList.remove(
                        "active"));
                    // Add active class to clicked link
                    this.classList.add("active");
                    // Close mobile sidebar after selection
                    if (window.innerWidth < 992) {
                        sidebar.classList.remove("show");
                        sidebarOverlay.classList.remove("show");
                    }
                });
            });

            // Handle collapse arrows
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach((button) => {
                button.addEventListener("click", function() {
                    const target = document.querySelector(this.getAttribute("data-bs-target"));
                    const arrow = this.querySelector(".nav-arrow");

                    // Toggle arrow rotation
                    if (target.classList.contains("show")) {
                        arrow.style.transform = "rotate(0deg)";
                    } else {
                        arrow.style.transform = "rotate(180deg)";
                    }
                });
            });
        });
    </script>

    @yield('footer_scripts')

    <!-- Theme Toggle Script -->
    <script>
        const btn = document.getElementById("themeToggle");
        const icon = document.getElementById("themeIcon");

        // Get theme from localStorage or default to dark
        let darkMode = localStorage.getItem('theme') === 'dark' || localStorage.getItem('theme') === null;

        // Function to apply theme
        function applyTheme(isDark) {
            if (isDark) {
                document.body.setAttribute("data-bs-theme", "dark");
                document.body.classList.add("dark");
                document.body.classList.remove("light");
                icon.classList.remove("fa-sun");
                icon.classList.add("fa-moon");
            } else {
                document.body.setAttribute("data-bs-theme", "light");
                document.body.classList.add("light");
                document.body.classList.remove("dark");
                icon.classList.remove("fa-moon");
                icon.classList.add("fa-sun");
            }
        }

        // Apply theme on page load
        applyTheme(darkMode);

        if (btn && icon) {
            btn.addEventListener("click", () => {
                darkMode = !darkMode;
                // Save theme preference to localStorage
                localStorage.setItem('theme', darkMode ? 'dark' : 'light');
                applyTheme(darkMode);
            });
        }
    </script>
</body>

</html>
