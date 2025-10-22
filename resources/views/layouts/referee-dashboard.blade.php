<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Referee Dashboard') - {{ config('app.name') }}</title>
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
    <link rel="stylesheet" href="{{ asset('assets/referee-dashboard/dashboard3.css') }}">

    @yield('header_styles')

    <style>
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
            height: auto !important;
        }

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

        .user-profile .dropdown-item i {
            width: 16px;
            margin-right: 8px;
        }
    </style>
    <script>
        (function () {
            const storageKey = 'p2e-referee-theme';
            try {
                let storedTheme = localStorage.getItem(storageKey);
                if (!storedTheme) {
                    storedTheme = localStorage.getItem('referee_theme');
                }
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = storedTheme || (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-bs-theme', theme);
                if (storedTheme && !localStorage.getItem(storageKey)) {
                    localStorage.setItem(storageKey, theme);
                    localStorage.removeItem('referee_theme');
                }
            } catch (error) {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            }
        })();
    </script>
</head>

@php
    $user = auth()->user();
    $referee = $user?->referee;
    $refereeName = $referee->full_name ?? $user?->name ?? ($user?->first_name ? trim($user->first_name . ' ' . $user->last_name) : null) ?? ($user?->email ?? 'Referee');
    $profilePicture = null;
    if ($referee && $referee->profile_picture) {
        $profilePicture = \Illuminate\Support\Str::startsWith($referee->profile_picture, ['http://', 'https://'])
            ? $referee->profile_picture
            : asset($referee->profile_picture);
    }
    $profilePicture = $profilePicture ?: asset('assets/club-dashboard-main/assets/user.png');
    $refereeTagline = $referee && $referee->certification_level
        ? 'Level ' . $referee->certification_level . ' Referee'
        : 'REFEREE';
@endphp

<body class="referee-dashboard">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-chevron-left"></i>
    </button>

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

    <div id="mainContent" class="main-content">
        <header class="top-header">
            <div class="header-left">
                <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="header-center">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search">
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
                <div class="user-profile dropdown">
                    <a class="dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ $profilePicture }}" width="36" height="36" alt="{{ $refereeName }}"
                            class="rounded-circle" style="object-fit: cover;">
                        <span class="d-none d-md-inline">{{ $refereeName }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('my-account') }}">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('referee.availability.form') }}">
                                <i class="fas fa-calendar-check"></i> Availability
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('front.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Logout Account
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>
    @stack('scripts')
    @yield('footer_scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            sidebarToggle.addEventListener('click', function() {
                const isLargeScreen = window.innerWidth >= 992;

                if (isLargeScreen) {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    sidebarToggle.classList.toggle('collapsed');
                } else {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                }
            });

            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });

            window.addEventListener('resize', function() {
                const isLargeScreen = window.innerWidth >= 992;

                if (isLargeScreen) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                } else {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                    sidebarToggle.classList.remove('collapsed');
                }
            });

            document.querySelectorAll('.nav-link:not([data-bs-toggle])').forEach((link) => {
                link.addEventListener('click', function() {
                    document.querySelectorAll('.nav-link').forEach((l) => l.classList.remove('active'));
                    this.classList.add('active');
                    if (window.innerWidth < 992) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                    }
                });
            });

            document.querySelectorAll('[data-bs-toggle=\"collapse\"]').forEach((button) => {
                button.addEventListener('click', function() {
                    const target = document.querySelector(this.getAttribute('data-bs-target'));
                    const arrow = this.querySelector('.nav-arrow');

                    if (target.classList.contains('show')) {
                        arrow.style.transform = 'rotate(0deg)';
                    } else {
                        arrow.style.transform = 'rotate(180deg)';
                    }
                });
            });
        });

        (function() {
            const storageKey = 'p2e-referee-theme';
            const btn = document.getElementById('themeToggle');
            const icon = document.getElementById('themeIcon');
            const html = document.documentElement;

            const applyTheme = (theme) => {
                html.setAttribute('data-bs-theme', theme);
                if (icon) {
                    icon.classList.remove('fa-sun', 'fa-moon');
                    icon.classList.add(theme === 'dark' ? 'fa-moon' : 'fa-sun');
                }
                if (btn) {
                    btn.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
                }
            };

            let currentTheme = html.getAttribute('data-bs-theme') === 'light' ? 'light' : 'dark';
            applyTheme(currentTheme);

            if (btn) {
                btn.addEventListener('click', () => {
                    currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    try {
                        localStorage.setItem(storageKey, currentTheme);
                        localStorage.removeItem('referee_theme');
                    } catch (error) {
                        // ignore storage issues
                    }
                    applyTheme(currentTheme);
                });
            }
        })();
    </script>
</body>

</html>
