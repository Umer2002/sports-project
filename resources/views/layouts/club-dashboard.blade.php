<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Club Dashboard') - {{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('storage/theme/logo.png') }}">

    <script>
        (function() {
            const storageKey = 'p2e-club-theme';
            try {
                const storedTheme = localStorage.getItem(storageKey);
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = storedTheme || (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-bs-theme', theme);
            } catch (error) {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            }
        })();
    </script>

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
    @vite(['resources/css/club.css', 'resources/js/app.js'])

    @yield('header_styles')
    @stack('header_styles')
    @php
        $currentUser = auth()->user();
        $clubJsonRoles = [];
        if ($currentUser) {
            if (method_exists($currentUser, 'getRoleNames')) {
                $clubJsonRoles = $currentUser->getRoleNames()->toArray();
            } elseif (isset($currentUser->roles) && is_iterable($currentUser->roles)) {
                $clubJsonRoles = collect($currentUser->roles)->pluck('name')->filter()->values()->all();
            } elseif (!empty($currentUser->role)) {
                $clubJsonRoles = [(string) $currentUser->role];
            }
        }
        $clubRouteTemplates = [
            'chatSend' => route('player.chat.send'),
            'chatInitiateTpl' => url('/player/chat/initiate/__ID__'),
            'chatMessagesTpl' => url('/player/chat/messages/__ID__'),
            'tournamentJoinTpl' => url('/tournaments/__ID__/chat/join'),
            'teamPlayersTpl' => url('/club/team/__ID__/players'),
            'calendarItemTpl' => url('/dashboard/calendar/item/__TYPE__/__ID__'),
            'calendarPreferenceTpl' => url('/dashboard/calendar/preference/__TYPE__/__ID__'),
            'calendarUploadTpl' => url('/dashboard/calendar/upload/__TYPE__/__ID__'),
            'calendarIcsTpl' => url('/dashboard/calendar/ics/__TYPE__/__ID__'),
            'teamChatPlayerTpl' => url('/player/teams/__ID__/chat'),
            'teamChatClubTpl' => url('/club/teams/__ID__/chat'),
        ];
    @endphp

    <style>
        /* Full screen height for club dashboard */
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

        .btn-primary,
        .theme-btn {
            background: linear-gradient(90deg, #7c3aed 0%, #ec4899 50%, #f59e0b 100%);
            border: none;
            border-radius: 6px;
            font-size: 10px;
            color: #ffffff;
            padding: 6px 16px;
            border-radius: none !important;
        }

        .btn-secondary {
            background: linear-gradient(90deg, #3b3b3d 0%, #ec4899 50%, #f59e0b 100%);
            border: none;
            border-radius: 6px;
            font-size: 10px;
            color: #ffffff;
            padding: 6px 16px;
            border-radius: none !important;
        }

        .btn-success {
            background: linear-gradient(90deg, #4dbc12 0%, #ebe545 50%, #c5e925 100%);
            border: none;
            border-radius: 6px;
            font-size: 10px;
            color: #000;
            padding: 6px 16px;
            border-radius: none !important;
        }

        .text-muted {
            color: #999999 !important;
        }

        .stat-badge1 {
            background: linear-gradient(92.43deg,
                    rgba(70, 132, 119, 0.9) 20.65%,
                    #74d876 95.8% !important);
        }
        .btn-primary-x{
            background-color: #0d6efd;
            color:#fff;
        }
        .sub-nav.collapse {
            transition: height 0.25s ease;
        }
        .collapse.show{
            display: block !important;
            height: auto !important;
            visibility: visible !important;
        }
    </style>
</head>

<body class="club-dashboard">
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
                        @php($currentClub = $currentUser?->club)
                        @if ($currentClub)
                            <div class="mb-2">
                                @if ($currentClub->logo)
                                    <img src="{{ Storage::url($currentClub->logo) }}" width="70" height="68"
                                        alt="{{ $currentClub->name }}" class="log osu-logo" />
                                @else
                                    <div class="log osu-logo d-flex align-items-center justify-content-center"
                                        style="width: 70px; height: 68px; background: #4299e1; color: white; border-radius: 8px; font-weight: bold;">
                                        {{ substr($currentClub->name, 0, 2) }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="club-info">
                    <div class="club-name">{{ $currentClub->name ?? 'Club Name' }}</div>
                    <div class="club-type">CLUB</div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">MAIN</div>

                    <!-- Dashboard -->
                    <div class="nav-item">
                        <a href="{{ route('club-dashboard') }}"
                            class="nav-link {{ request()->routeIs('club-dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="{{ route('club.financial.dashboard') }}"
                            class="nav-link {{ request()->routeIs('club.financial.dashboard*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-text">Financials</span>
                        </a>
                    </div>

                    <!-- Events -->
                    <div class="nav-item">
                        <a href="{{ route('club.events.index') }}"
                            class="nav-link {{ request()->routeIs('club.events.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span class="nav-text">Events</span>
                        </a>
                    </div>

                    <!-- Teams -->
                    <div class="nav-item">
                        <button class="nav-link {{ request()->routeIs('club.teams.*') ? 'active' : '' }} {{ request()->routeIs('club.teams.*') ? '' : 'collapsed' }}"
                            type="button" data-bs-toggle="collapse" data-bs-target="#teamsSubmenu"
                            aria-expanded="{{ request()->routeIs('club.teams.*') ? 'true' : 'false' }}"
                            aria-controls="teamsSubmenu">
                            <i class="fas fa-users"></i>
                            <span class="nav-text">Teams</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </button>
                        <div class="sub-nav collapse {{ request()->routeIs('club.teams.*') ? 'show' : '' }}"
                            id="teamsSubmenu">
                            <a href="{{ route('club.teams.index') }}" class="nav-link">
                                <span class="nav-text">All Teams</span>
                            </a>
                            <a href="{{ route('club.teams.wizard.step1') }}" class="nav-link">
                                <span class="nav-text">Create Team</span>
                            </a>
                        </div>
                    </div>

                    <!-- Players -->
                    <div class="nav-item">
                        <button class="nav-link {{ request()->routeIs('club.players.*') ? 'active' : '' }} {{ request()->routeIs('club.players.*') ? '' : 'collapsed' }}"
                            type="button" data-bs-toggle="collapse" data-bs-target="#playersSubmenu"
                            aria-expanded="{{ request()->routeIs('club.players.*') ? 'true' : 'false' }}"
                            aria-controls="playersSubmenu">
                            <i class="fas fa-user-friends"></i>
                            <span class="nav-text">Players</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </button>
                        <div class="sub-nav collapse {{ request()->routeIs('club.players.*') ? 'show' : '' }}"
                            id="playersSubmenu">
                            <a href="{{ route('club.players.index') }}" class="nav-link">
                                <span class="nav-text">All Players</span>
                            </a>
                            <a href="{{ route('club.players.invite') }}" class="nav-link">
                                <span class="nav-text">Invite Players</span>
                            </a>
                        </div>
                    </div>

                    <!-- Coaches -->
                    <div class="nav-item">
                        <button class="nav-link {{ request()->routeIs('club.coaches.*') ? 'active' : '' }} {{ request()->routeIs('club.coaches.*') ? '' : 'collapsed' }}"
                            type="button" data-bs-toggle="collapse" data-bs-target="#coachesSubmenu"
                            aria-expanded="{{ request()->routeIs('club.coaches.*') ? 'true' : 'false' }}"
                            aria-controls="coachesSubmenu">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span class="nav-text">Coaches</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </button>
                        <div class="sub-nav collapse {{ request()->routeIs('club.coaches.*') ? 'show' : '' }}"
                            id="coachesSubmenu">
                            <a href="{{ route('club.coaches.index') }}" class="nav-link">
                                <span class="nav-text">All Coaches</span>
                            </a>
                            <a href="{{ route('club.coaches.create') }}" class="nav-link">
                                <span class="nav-text">Add Coach</span>
                            </a>
                        </div>
                    </div>

                    <!-- Tournaments -->
                    <div class="nav-item">
                        <a href="{{ route('club.tournaments.index') }}"
                            class="nav-link {{ request()->routeIs('club.tournaments.*') ? 'active' : '' }}">
                            <i class="fas fa-trophy"></i>
                            <span class="nav-text">Tournaments</span>
                        </a>

                    </div>
                    {{-- create nav item for invites.list --}}
                    <div class="nav-item">
                        <a href="{{ route('club.tournament-registrations.invites.list') }}"
                            class="nav-link {{ request()->routeIs('club.tournament-registrations.invites.list') ? 'active' : '' }}">
                            <i class="fas fa-envelope"></i>
                            <span class="nav-text">Invites</span>
                        </a>
                    </div>

                    <!-- Announcements -->
                    {{-- <div class="nav-item">
                        <a href="{{ route('club.announcements.index') }}" class="nav-link {{ request()->routeIs('club.announcements.*') ? 'active' : '' }}">
                        </div> --}}
                    <!-- Transfers -->
                    <div class="nav-item">
                        <a href="{{ route('club.transfers.index') }}"
                            class="nav-link {{ request()->routeIs('club.transfers.*') ? 'active' : '' }}">
                            <i class="fas fa-exchange-alt"></i>
                            <span class="nav-text">Transfers</span>
                        </a>
                    </div>
                    {{-- E-commerce nav item having sumb items Categories Products and Orders --}}
                    <div class="nav-item">
                        <button class="nav-link {{ request()->routeIs('club.store.*') ? 'active' : '' }}"
                            type="button" data-bs-toggle="collapse" data-bs-target="#ecommerceSubmenu"
                            aria-expanded="{{ request()->routeIs('club.ecommerce.*') ? 'true' : 'false' }}"
                            aria-controls="ecommerceSubmenu">
                            <i class="fas fa-store"></i>
                            <span class="nav-text">E-commerce</span>
                            <i class="fas fa-chevron-down nav-arrow"></i>
                        </button>
                        <div class="collapse {{ request()->routeIs('club.ecommerce.*') ? 'show' : '' }} sub-nav"
                            id="ecommerceSubmenu">
                            <a href="{{ route('club.store.categories.index') }}" class="nav-link">
                                <span class="nav-text">Categories</span>
                            </a>
                            <a href="{{ route('club.store.products.index') }}" class="nav-link">
                                <span class="nav-text">Products</span>
                            </a>
                            <a href="{{ route('club.store.orders.index') }}" class="nav-link">
                                <span class="nav-text">Orders</span>
                            </a>
                        </div>
                    </div>
                    <!-- Settings -->
                    <div class="nav-item">
                        <a href="{{ route('club.profile.edit') }}"
                            class="nav-link {{ request()->routeIs('club.profile.*') ? 'active' : '' }}">
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
                <div class="header-icon" id="themeToggle" role="button" tabindex="0" aria-label="Toggle theme">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </div>

            </div>
        </header>

        <!-- Content Area -->
        <div class="content-area">
            @yield('content')
        </div>
    </div>

    @include('layouts.partials.calendar-event-modal')

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>
    <script>
        (function() {
            const existing = (typeof window.App === 'object' && window.App) ? window.App : {};
            const cfg = {
                csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',
                userId: {{ (int) ($currentUser?->id ?? 0) }},
                userRoles: {!! json_encode($clubJsonRoles) !!},
                events: [],
                routes: {},
            };

            Object.assign(cfg, existing);

            if (!Array.isArray(cfg.userRoles) || cfg.userRoles.length === 0) {
                cfg.userRoles = {!! json_encode($clubJsonRoles) !!};
            }

            cfg.events = Array.isArray(existing.events) ? existing.events : [];
            cfg.routes = Object.assign(
                {!! json_encode($clubRouteTemplates) !!},
                existing.routes || {}
            );

            window.App = cfg;
        })();
    </script>
    @yield('footer_scripts')
    @stack('scripts')
    @vite('resources/js/club-dashboard.js')
    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const themeStorageKey = 'p2e-club-theme';
            const htmlElement = document.documentElement;
            const themeToggle = document.getElementById("themeToggle");
            const themeIcon = document.getElementById("themeIcon");

            const setThemeIcon = (theme) => {
                if (!themeIcon) {
                    return;
                }
                themeIcon.classList.remove('fa-sun', 'fa-moon');
                themeIcon.classList.add(theme === 'dark' ? 'fa-sun' : 'fa-moon');
            };

            const applyTheme = (theme) => {
                htmlElement.setAttribute('data-bs-theme', theme);
                setThemeIcon(theme);
                if (themeToggle) {
                    themeToggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
                }
            };

            let storedTheme = null;
            try {
                storedTheme = localStorage.getItem(themeStorageKey);
            } catch (error) {
                storedTheme = null;
            }
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const initialTheme = storedTheme || htmlElement.getAttribute('data-bs-theme') || (prefersDark ? 'dark' : 'light');
            applyTheme(initialTheme);

            const toggleTheme = () => {
                const currentTheme = htmlElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
                const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
                try {
                    localStorage.setItem(themeStorageKey, nextTheme);
                } catch (error) {
                    // ignore storage errors
                }
                applyTheme(nextTheme);
            };

            if (themeToggle) {
                themeToggle.addEventListener('click', toggleTheme);
                themeToggle.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        toggleTheme();
                    }
                });
            }

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
            function syncCollapseState(toggle, section) {
                if (!toggle || !section) return;

                const isOpen = section.classList.contains('show');
                const arrow = toggle.querySelector('.nav-arrow');

                toggle.classList.toggle('collapsed', !isOpen);
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                if (arrow) {
                    arrow.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
                }

                if (window.bootstrap?.Collapse) {
                    const instance = window.bootstrap.Collapse.getOrCreateInstance(section, { toggle: false });
                    isOpen ? instance.show() : instance.hide();
                }
            }

            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach((button) => {
                const targetSelector = button.getAttribute('data-bs-target');
                const section = document.querySelector(targetSelector);
                syncCollapseState(button, section);

                button.addEventListener('click', function () {
                    // Bootstraps collapse toggles .show asynchronously, so wait for it to finish.
                    setTimeout(() => syncCollapseState(button, section), 10);
                });
            });
        });
    </script>

    @yield('footer_scripts')
</body>

</html>
