@extends('public.profiles.base')

@section('player_theme', 'american')

@section('player-custom-css')
    @php($themeAssetPath = $playerAssetPath ?? 'assets/players/swimming/assets')
    <style>
        /* American Football-specific styles */
        /* .hero {
                background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            } */
        .hero {
            background: linear-gradient(to bottom, #059669 0%, #10b981 100%), url("{{ asset($themeAssetPath . '/image/hero-bg.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;

        }

        .player-name {
            color: #064e3b;
        }

        .stats-value {
            color: #059669;
        }

        .donate-btn {
            background: #059669;
            border-color: #059669;
        }

        .donate-btn:hover {
            background: #047857;
            border-color: #047857;
        }

        .soccer-text {
            color: #059669;
        }

        .menu-bar a.active {
            background: #059669;
            border-color: #059669;
        }

        .menu-bar a:hover {
            background: #10b981;
            border-color: #10b981;
        }
    </style>
@endsection
@section('header')
    <header class="header">
        <div class="container p-0 px-sm-2">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid align-items-center">
                    <div class="d-flex g-2">
                        <!-- Logo -->
                        <a class="navbar-brand" href="{{ route('home') }}">
                            <img src="{{ asset($themeAssetPath . '/image/logo.png') }}" alt="Logo" />
                        </a>

                        <!-- Language Selector -->
                        <div class="lang-dropdown d-flex align-items-center gap-2">
                            EN
                            <span class="d-inline-block"><i class="fa-solid fa-angle-down"></i></span>
                        </div>
                    </div>
                    <!-- Hamburger -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                        <div class="navbar-toggle">
                            <div class="lines">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </button>

                    <!-- Navbar content -->
                    <div class="collapse navbar-collapse" id="navbarContent">
                        <!-- Nav links -->
                        <ul class="navbar-nav mb-2 mb-lg-0">
                            <li class="nav-item"><a class="nav-link" href="#club"
                                    onclick="scrollToSection('club')">Club</a></li>
                            <li class="nav-item"><a class="nav-link" href="#teams"
                                    onclick="scrollToSection('teams')">Teams</a></li>
                            <li class="nav-item">
                                <a class="nav-link" href="#awards" onclick="scrollToSection('awards')">Awards</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#bio" onclick="scrollToSection('bio')">Bio</a>
                            </li>
                        </ul>

                        <!-- Search + Login -->
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="position-relative search-wrapper">
                                <input type="text" class="search-box" placeholder="Search Here..." />
                                <img src="{{ asset($themeAssetPath . '/image/search.svg') }}" class="search-icon"
                                    alt="icon" />
                            </div>
                            <a href="{{ route('login') }}" class="login-btn">LOGIN</a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>
@endsection

@section('hero')
    <section class="hero pb-5">
        <img src="{{ asset($themeAssetPath . '/image/Ball.png') }}" alt="ball" class="hero-ball" />

        <div class="container">
            <div class="row">
                <div class="profile-container py-1 py-lg-5 pb-0">
                    <div class="soccer-text d-none d-lg-block">{{ strtoupper($player->sport->name ?? 'FOOTBALL') }}</div>

                    <div class="row">
                        <div class="col-12 col-lg-4 col-xl-3 px-1 text-center text-lg-start">
                            <div class="social-icons d-lg-none">
                                @php($socialLinks = $playerSocialLinks ?? [])
                                @foreach ($socialLinks as $platform => $link)
                                    @continue(empty($link))
                                    @php
                                        $normalizedPlatform = \Illuminate\Support\Str::slug($platform, '-');
                                        $iconLookup = [
                                            // FA5-safe set (remove tiktok; keep twitter)
                                            'facebook' => 'facebook-f',
                                            'facebook-f' => 'facebook-f',
                                            'instagram' => 'instagram',
                                            'twitter' => 'twitter',
                                            'x' => 'twitter', // For FA5
                                            'linkedin' => 'linkedin-in',
                                            'linkedin-in' => 'linkedin-in',
                                            'youtube' => 'youtube',
                                            // 'tiktok'      => 'tiktok',       // FA6 only
                                            'pinterest' => 'pinterest',
                                            'snapchat' => 'snapchat-ghost',
                                            'snapchat-ghost' => 'snapchat-ghost',
                                            'reddit' => 'reddit-alien',
                                            'reddit-alien' => 'reddit-alien',
                                        ];
                                        $iconClass = $iconLookup[$normalizedPlatform] ?? $normalizedPlatform;
                                    @endphp
                                    <a href="{{ $link }}" class="social-icon {{ $normalizedPlatform }}"
                                        target="_blank" rel="noopener">
                                        <i class="fab fa-{{ $iconClass }}"></i>
                                    </a>
                                @endforeach
                            </div>

                            <h1 class="player-name text-center text-lg-start">{{ $player->name ?? 'Unknown Player' }}</h1>

                            <div class="position-badges">
                                @if ($player->position)
                                    <span class="position-badge">
                                        <i class="fas fa-futbol"></i> {{ $player->position->position_name }}
                                    </span>
                                @endif
                                @if ($player->gender)
                                    <span class="position-badge">
                                        <i class="fas fa-{{ $player->gender === 'Male' ? 'mars' : 'venus' }}"></i>
                                        {{ $player->gender }}
                                    </span>
                                @endif
                            </div>

                            <div class="player-stats d-none d-lg-block">
                                <div class="stats-grid">
                                    @foreach ($statsWithValues->take(3) as $stat)
                                        <div class="stat-item">
                                            <div class="stats-value">{{ $stat->value ?? '0' }}</div>
                                            <div class="stats-label">{{ $stat->name }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="donate-section d-none d-lg-block">
                                <button class="btn donate-btn w-100">
                                    <i class="fas fa-heart"></i> DONATE
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-lg-8 col-xl-9 px-1">
                            <div class="profile-image-container">
                                @if ($player->photo)
                                    <img src="{{ asset('storage/' . $player->photo) }}" alt="{{ $player->name }}"
                                        class="profile-image" />
                                @else
                                    <img src="{{ asset($themeAssetPath . '/image/player.png') }}" alt="Default Player"
                                        class="profile-image" />
                                @endif
                            </div>

                            <div class="player-stats d-lg-none">
                                <div class="stats-grid">
                                    @foreach ($statsWithValues->take(3) as $stat)
                                        <div class="stat-item">
                                            <div class="stats-value">{{ $stat->value ?? '0' }}</div>
                                            <div class="stats-label">{{ $stat->name }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="donate-section d-lg-none">
                                <button class="btn donate-btn w-100">
                                    <i class="fas fa-heart"></i> DONATE
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('tabs')
    <section class="tabs-section">
        <div class="container">
            <div class="menu-bar">
                <a href="#donation" class="active">Donation</a>
                <a href="#club">Club</a>
                <a href="#teams">Teams</a>
                <a href="#awards">Awards</a>
                <a href="#bio">Bio</a>
            </div>

            <div class="tab-content">
                <!-- Donation Tab -->
                <div id="donation" class="tab-pane show active">
                    <div class="donation-content">
                        <h2>Support {{ $player->name }}</h2>
                        <p>Help this talented player achieve their dreams by making a donation.</p>
                        <div class="donation-form">
                            <form action="{{ route('donation.checkout') }}" method="POST">
                                @csrf
                                <input type="hidden" name="recipient_id" value="{{ $player->id }}">
                                <input type="hidden" name="recipient_type" value="player">
                                <input type="hidden" name="amount" value="25">
                                <button type="submit" class="btn donate-btn">
                                    <i class="fas fa-heart"></i> DONATE $25
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Club Tab -->
                <div id="club" class="tab-pane">
                    <div class="club-content">
                        @if ($player->club)
                            <h2>{{ $player->club->name }}</h2>
                            <p>{{ $player->club->description ?? 'No description available.' }}</p>
                            @if ($player->club->logo)
                                <img src="{{ asset('storage/' . $player->club->logo) }}" alt="{{ $player->club->name }}"
                                    class="club-logo" />
                            @endif
                        @else
                            <p>No club information available.</p>
                        @endif
                    </div>
                </div>

                <!-- Teams Tab -->
                <div id="teams" class="tab-pane">
                    <div class="teams-content">
                        @if ($teamMembers->count() > 0)
                            <h2>Team Members</h2>
                            <div class="team-members">
                                @foreach ($teamMembers as $member)
                                    <div class="team-member">
                                        <h3>{{ $member->name }}</h3>
                                        @if ($member->position)
                                            <p>{{ $member->position->position_name }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>No team information available.</p>
                        @endif
                    </div>
                </div>

                <!-- Awards Tab -->
                <div id="awards" class="tab-pane">
                    <div class="awards-content">
                        @if ($playerRewards->count() > 0)
                            <h2>Awards & Achievements</h2>
                            <div class="rewards-grid">
                                @foreach ($playerRewards as $reward)
                                    <div class="reward-item">
                                        @if ($reward->image)
                                            <img src="{{ asset('storage/' . $reward->image) }}"
                                                alt="{{ $reward->name }}" />
                                        @endif
                                        <h3>{{ $reward->name }}</h3>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>No awards or achievements available.</p>
                        @endif
                    </div>
                </div>

                <!-- Bio Tab -->
                <div id="bio" class="tab-pane">
                    <div class="bio-content">
                        <h2>Biography</h2>
                        <p>{{ $player->bio ?? 'No biography available.' }}</p>

                        @if ($posts->count() > 0)
                            <h3>Recent Posts</h3>
                            <div class="posts-list">
                                @foreach ($posts as $post)
                                    <div class="post-item">
                                        <h4>{{ $post->title }}</h4>
                                        <p>{{ Str::limit($post->content, 200) }}</p>
                                        <small>{{ $post->created_at->format('M d, Y') }}</small>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('newsletter')
    <section class="news-letter">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="cta-title">Join our online community today</h1>
                    <p class="cta-subtitle">
                        Create a profile that showcases your child's talent.
                    </p>
                    <form class="cta-form" role="form">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Email Here..." required />
                            <button class="btn btn-subscribe" type="submit">
                                SUBSCRIBE
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="overlap-group-1 position-relative">
                        <div class="rectangle-36"></div>
                        <img src="{{ asset($themeAssetPath . '/image/footer-img.png') }}" alt="P2ES logo 1"
                            class="img-fluid" />
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <div class="mail-content">
                        info@play2earnsports.com, 223-23EARN-60
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="footer-btn">
                        <a href="{{ route('register') }}" class="btn sign-up-now" style="margin: auto;">SIGN UP NOW</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
@endsection

@section('theme-scripts')
    <!-- Theme-specific scripts -->
    <script>
        // Function to scroll to sections
        function scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const tabs = document.querySelectorAll(".menu-bar a"); // All tab links
            const tabContents = document.querySelectorAll(".tab-pane"); // All tab contents

            // Function to show the clicked tab and hide others
            function activateTab(targetId) {
                tabs.forEach(tab => tab.classList.remove("active"));
                tabContents.forEach(content => content.classList.remove("show", "active"));

                const targetTab = document.querySelector(`a[href="${targetId}"]`);
                const targetContent = document.querySelector(targetId);

                if (targetTab && targetContent) {
                    targetTab.classList.add("active");
                    targetContent.classList.add("show", "active");
                }
            }

            // Initialize by showing the first tab content
            activateTab("#donation");

            // Add event listeners to tabs
            tabs.forEach(tab => {
                tab.addEventListener("click", function(e) {
                    e.preventDefault(); // Prevent the default anchor link behavior
                    const targetId = tab.getAttribute("href");
                    activateTab(targetId);
                });
            });
        });
    </script>
@endsection
