@extends('public.profiles.base')

@section('header')
    @php($themeAssetPath = $playerAssetPath ?? 'assets/players/swimming/assets')

    <header class="header">
        <div class="container p-0 px-sm-2">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid align-items-center">
                    <div class="d-flex g-2">
                        <!-- Logo -->
                        <a class="navbar-brand" href="{{ route('home') }}">
                            <img src="{{ asset(($playerAssetPath ?? 'assets/players/swimming/assets') . '/image/logo.png') }}"
                                alt="Logo" />
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
                                <img src="{{ asset(($playerAssetPath ?? 'assets/players/swimming/assets') . '/image/search.svg') }}"
                                    class="search-icon" alt="icon" />
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
        <img src="{{ asset(($playerAssetPath ?? 'assets/players/swimming/assets') . '/image/Ball.png') }}" alt="ball"
            class="hero-ball" />

        <div class="container">
            <div class="row">
                <div class="profile-container py-1 py-lg-5 pb-0">
                    <div class="soccer-text d-none d-lg-block">{{ strtoupper($player->sport->name ?? 'SWIMMING') }}</div>

                    <div class="row">
                        <div class="col-12 col-lg-4 col-xl-3 px-1 text-center text-lg-start">
                            <div class="social-icons d-lg-none">
                               @foreach ((array) ($playerSocialLinks ?? []) as $platform => $link)
    @php
        // Coerce URL
        $url = is_array($link) ? ($link['url'] ?? $link['link'] ?? $link['href'] ?? '') : (string) $link;
        if (!\Illuminate\Support\Str::startsWith($url, ['http://','https://'])) { $url = ''; }
    @endphp
    @continue(empty($url))
    @php($links = (array) ($playerSocialLinks ?? []))

    @php
        // Normalize platform + aliases
        $raw  = (string) $platform;
        $norm = \Illuminate\Support\Str::slug($raw, '-');
        $aliases = [
            'fb' => 'facebook',
            'facebook-page' => 'facebook',
            'ig' => 'instagram',
            'twitter-x' => 'twitter',   // use 'x-twitter' if on FA6
            'linkedin-profile' => 'linkedin-in',
            'youtube-channel' => 'youtube',
        ];
        $norm = $aliases[$norm] ?? $norm;

        // Icon lookup (FA5-safe; for FA6 map 'x' => 'x-twitter')
        $iconLookup = [
            'facebook' => 'facebook-f',
            'facebook-f' => 'facebook-f',
            'instagram' => 'instagram',
            'twitter' => 'twitter',
            'x' => 'twitter',           // FA5; change to 'x-twitter' for FA6
            'linkedin' => 'linkedin-in',
            'linkedin-in' => 'linkedin-in',
            'youtube' => 'youtube',
            'pinterest' => 'pinterest',
            'snapchat' => 'snapchat-ghost',
            'snapchat-ghost' => 'snapchat-ghost',
            'reddit' => 'reddit-alien',
            'reddit-alien' => 'reddit-alien',
        ];

        $icon = $iconLookup[$norm]
            ?? $iconLookup[\Illuminate\Support\Str::slug(strtolower(trim($raw)), '-')]
            ?? 'link';

        // Prefix (brand vs solid). Works with FA5/FA6 if CSS loaded.
        $brandIcons = ['facebook-f','instagram','twitter','x-twitter','linkedin-in','youtube','pinterest','snapchat-ghost','reddit-alien'];
        $prefix = in_array($icon, $brandIcons, true) ? 'fa-brands' : 'fa-solid'; // use 'fab'/'fas' if FA5 only
    @endphp

    <a href="{{ $url }}" class="social-icon {{ $norm }}" target="_blank" rel="noopener noreferrer">
        <i class="{{ $prefix }} fa-{{ $icon }}"></i>
    </a>
@endforeach


                            </div>

                            <h1 class="player-name text-center text-lg-start">{{ $player->name ?? 'Unknown Player' }}</h1>

                            <div class="position-badges mb-1">
                                @if ($player->position)
                                    <span class="position-badge">
                                        <i class="fas fa-futbol"></i> {{ $player->position->position_name }}
                                    </span>
                                @endif
                                @if ($player->gender)
                                    <span class="position-badge">
                                        <i class="fas fa-hand"></i> {{ ucfirst($player->gender) }}
                                    </span>
                                @endif
                            </div>
                            <br />

                            <button class="donate-btn mt-1" onclick="openDonationModal()" id="donateBtn">MAKE A
                                DONATION</button>


                            @if ($player->debut)
                                <div class="year-text mt-4 mt-lg-5">
                                    <span class="h5">DEBUT - {{ $player->debut->format('Y') }}</span>
                                </div>
                            @endif

                            @if ($player->jersey_no)
                                <p class="d-lg-none mb-0 mt-1 jn">JERSEY NUMBER - {{ $player->jersey_no }}</p>
                            @endif

                            <div class="mt-3 mar-left d-none d-lg-block">
                                <h6>Share Profile</h6>
                                <div class="social-icons">
                                    @foreach ($playerSocialLinks ?? [] as $platform => $link)
                                        @continue(empty($link))
                                        @php
                                            $normalizedPlatform = \Illuminate\Support\Str::slug($platform, '-');
                                            $iconLookup = [
                                                'facebook' => 'facebook-f',
                                                'facebook-f' => 'facebook-f',
                                                'instagram' => 'instagram',
                                                'twitter' => 'twitter',
                                                'x' => 'twitter',
                                                'linkedin' => 'linkedin-in',
                                                'linkedin-in' => 'linkedin-in',
                                                'youtube' => 'youtube',
                                                'tiktok' => 'tiktok',
                                                'pinterest' => 'pinterest',
                                                'snapchat' => 'snapchat-ghost',
                                                'reddit' => 'reddit-alien',
                                            ];
                                            $iconClass = $iconLookup[$normalizedPlatform] ?? $normalizedPlatform;
                                        @endphp
                                        <a href="{{ $link }}" class="social-icon {{ $normalizedPlatform }}"
                                            target="_blank" rel="noopener">
                                            <i class="fab fa-{{ $iconClass }}"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <div class="row d-none d-lg-block">
                                <div class="col-md-12">
                                    <div class="info-box mar-left">
                                        @if ($player->birthday)
                                            <div class="info-content">
                                                <div class="info-label">BORN</div>
                                                <div class="info-value">{{ $player->birthday->format('d-m-Y') }}</div>
                                            </div>
                                        @endif

                                        @if ($player->age)
                                            <div class="info-content">
                                                <div class="info-label">AGE</div>
                                                <div class="info-value">{{ $player->age }} YEARS</div>
                                            </div>
                                        @endif

                                        @if ($player->nationality)
                                            <div class="info-content">
                                                <div class="info-label">NATIONALITY</div>
                                                <div class="info-value">{{ $player->nationality }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-xl-6 d-none d-lg-block">
                            <div class="player-image">
                                @if ($player->photo)
                                    <img src="{{ asset($player->photo) }}" alt="{{ $player->name }}"
                                        class="img-fluid w-100 object-fit-contain" />
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4 col-xl-3">
                            <div class="row flex-column">
                                <div class="stats-container mb-xl-4 mb-lg-4 pt-lg-2">
                                    <div class="stats-label">Stats</div>
                                    <div class="stats-box">
                                        <div class="row">
                                            @if ($statsWithValues && $statsWithValues->count() > 0)
                                                @foreach ($statsWithValues->take(3) as $index => $stat)
                                                    <div class="col-4">
                                                        <div class="stats-title">{{ strtoupper($stat->name ?? 'N/A') }}
                                                        </div>
                                                        <div class="stats-value">{{ $stat->value ?? '0' }}</div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="col-12">
                                                    <div class="stats-title">NO Record</div>
                                                    <div class="stats-value">Found</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start">
                                    <div class="team-logos me-4">
                                        <div class="d-flex flex-column align-items-start flex-wrap">
                                            @if ($player->club)
                                                <div class="team-logo d-flex align-items-center mb-2">
                                                    @if ($player->club->logo)
                                                        <img src="{{ asset('storage/' . $player->club->logo) }}"
                                                            alt="{{ $player->club->name }}" class="img-fluid" />
                                                    @else
                                                        <img src="{{ asset($playerAssetPath . '/image/laker.png') }}"
                                                            alt="{{ $player->club->name }}" class="img-fluid" />
                                                    @endif
                                                    <span class="team-name">{{ $player->club->name }}</span>
                                                </div>
                                            @endif

                                            @if ($player->team)
                                                <div class="team-logo align-items-center">
                                                    <img src="{{ asset($playerAssetPath . '/image/hockey.png') }}"
                                                        alt="{{ $player->team->name }}" class="img-fluid"
                                                        style="display: none;" />
                                                    <span class="team-name" style="font-size: 12px;">TEAM -
                                                        {{ $player->team->name }}</span>
                                                </div>
                                            @endif

                                            @if ($player->debut)
                                                <div class="team-logo align-items-center">
                                                    <img src="{{ asset($playerAssetPath . '/image/hockey.png') }}"
                                                        alt="Debut" class="img-fluid" style="display: none;" />
                                                    <span class="team-name" style="font-size: 12px;">DEBUT -
                                                        {{ $player->debut->format('Y') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-md-5 mt-lg-1 mt-xl-5 mt-lg-5 offset-lg-2">
                                    <div class="info-box mt-4">
                                        @if ($player->birthday)
                                            <div class="info-content d-lg-none">
                                                <div class="info-label">BORN</div>
                                                <div class="info-value">{{ $player->birthday->format('d-m-Y') }}</div>
                                            </div>
                                        @endif

                                        @if ($player->age)
                                            <div class="info-content d-lg-none">
                                                <div class="info-label">AGE</div>
                                                <div class="info-value">{{ $player->age }} YEARS</div>
                                            </div>
                                        @endif

                                        @if ($player->nationality)
                                            <div class="info-content d-lg-none">
                                                <div class="info-label">NATIONALITY</div>
                                                <div class="info-value">{{ $player->nationality }}</div>
                                            </div>
                                        @endif

                                        @if ($player->height)
                                            <div class="info-content">
                                                <div class="info-label">HEIGHT</div>
                                                <div class="info-value">{{ $player->height }} M</div>
                                            </div>
                                        @endif

                                        @if ($player->weight)
                                            <div class="info-content">
                                                <div class="info-label">WEIGHT</div>
                                                <div class="info-value">{{ $player->weight }} KG</div>
                                            </div>
                                        @endif

                                        @if ($player->position)
                                            <div class="info-content">
                                                <div class="info-label">POSITION</div>
                                                <div class="info-value">{{ strtoupper($player->position->position_name) }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-xl-6 d-block d-lg-none">
                            <div class="mobile-player-image position-relative">
                                @if ($player->photo)
                                    <img src="{{ asset($player->photo) }}" alt="{{ $player->name }}"
                                        class="img-fluid w-100 h-100 object-fit-contain" />
                                @else
                                    <!-- <img src="{{ asset($playerAssetPath . '/image/player-mobile.png') }}" alt="{{ $player->name }}" class="img-fluid w-100 h-100 object-fit-contain" /> -->
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('tabs')
    <section class="tab-content about-content">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Nav Tabs -->
                <div>
                    <div class="menu-bar">
                        <a href="#donation" class="active">Donation</a>
                        <a href="#bio">Bio</a>
                        <a href="#club">Club</a>
                        <a href="#teams">Teams</a>
                        <a href="#awards">Awards</a>
                        <a href="#posts">Posts</a>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="tab-wrapper">
                    <div class="tab-content" id="customTabsContent">
                        <div class="tab-pane fade show active" id="donation" role="tabpanel">
                            <div class="row align-items-center">
                                <div class="man col-12 col-md-6">
                                    <div class="donation-content">
                                        <h2 class="heading">Donate us</h2>
                                        <div class="club-divider"></div>
                                        <p class="para">
                                            Support {{ $player->name }}'s journey in
                                            {{ $player->sport->name ?? 'sports' }}.
                                            Your donation helps cover training costs, equipment, and competition expenses.
                                        </p>
                                        <button class="btn btn-primary" onclick="openDonationModal()">MAKE A
                                            DONATION</button>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="donation-img text-end">
                                        <div class="player-card">
                                            <!-- Card background -->
                                            <div class="card-background">
                                                <img src="{{ asset($playerAssetPath . '/image/Base.png') }}"
                                                    alt="Card Background" class="card-bg-img" />

                                                <!-- Left sidebar -->
                                                <div class="sidebar">
                                                    <!-- Flag -->
                                                    <div class="h25-donation"></div>
                                                    <div class="flag-container">
                                                        <div class="flag">
                                                            <img src="{{ asset($playerAssetPath . '/image/flag.png') }}"
                                                                alt="flag" class="img-fluid" />
                                                            <div class="flag-blue"></div>
                                                            <div class="flag-white"></div>
                                                            <div class="flag-red"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Jersey number -->
                                                    @if ($player->jersey_no)
                                                        <div class="jersey-number">
                                                            <h3>JERSEY - {{ $player->jersey_no }}</h3>
                                                        </div>
                                                    @endif

                                                    <!-- Achievement badges -->
                                                    <div class="badges">
                                                        @if ($playerRewards && $playerRewards->count() > 0)
                                                            @foreach ($playerRewards->take(4) as $reward)
                                                                <div class="badge-item">
                                                                    @if ($reward->image)
                                                                        <img src="{{ asset($reward->image) }}"
                                                                            alt="{{ $reward->name ?? 'Reward' }}"
                                                                            class="badge-img" />
                                                                    @else
                                                                        <img src="{{ asset($playerAssetPath . '/image/player-tag-1.png') }}"
                                                                            alt="{{ $reward->name ?? 'Reward' }}"
                                                                            class="badge-img" />
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="badge-item">
                                                                <img src="{{ asset($playerAssetPath . '/image/player-tag-1.png') }}"
                                                                    alt="Default Reward" class="badge-img" />
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Main content -->
                                                <div class="main-content">
                                                    <!-- Player image -->
                                                    <div class="player-image-on-card">
                                                        @if ($player->photo)
                                                            <img src="{{ asset($player->photo) }}"
                                                                alt="{{ $player->name }}" class="img-fluid-card" />
                                                        @else
                                                            <!-- <img src="{{ asset($playerAssetPath . '/image/player-box-img.png') }}" alt="{{ $player->name }}" class="img-fluid-card" /> -->
                                                        @endif
                                                    </div>

                                                    <!-- Player name -->
                                                    <div class="player-name player-name-on-card">
                                                        <h2>{{ $player->name }}</h2>
                                                    </div>

                                                    <!-- Stats section -->
                                                    <div class="stats-section">
                                                        <div class="stats-header">
                                                            <div class="row g-0 text-center">
                                                                @foreach ($statsWithValues->take(3) as $stat)
                                                                    <div class="col">{{ strtoupper($stat->name) }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="stats-values">
                                                            <div class="row g-0 text-center">
                                                                @foreach ($statsWithValues->take(3) as $stat)
                                                                    <div class="col">{{ $stat->value }}</div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="bio" role="tabpanel">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-8">
                                    <div class="bio-content">
                                        <h2 class="heading">{{ $player->name }} Bio</h2>
                                        <div class="club-divider"></div>
                                        <p class="para">
                                            @if ($player->bio)
                                                {!! nl2br(e($player->bio)) !!}
                                            @else
                                                {{ $player->name }} is a talented {{ $player->sport->name ?? 'athlete' }}
                                                with a passion for excellence.
                                                With dedication and hard work, they continue to achieve remarkable
                                                milestones in their sporting career.
                                            @endif
                                        </p>
                                        <a href="#bio" class="btn">READ MORE</a>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="bio-img text-end">
                                        <div class="donation-img text-end">
                                            <div class="player-card">
                                                <!-- Card background -->
                                                <div class="card-background">
                                                    <img src="{{ asset($playerAssetPath . '/image/Base.png') }}"
                                                        alt="Card Background" class="card-bg-img" />

                                                    <!-- Left sidebar -->
                                                    <div class="sidebar">
                                                        <!-- Flag -->
                                                        <div class="h25-donation"></div>
                                                        <div class="flag-container">
                                                            <div class="flag">
                                                                <img src="{{ asset($playerAssetPath . '/image/flag.png') }}"
                                                                    alt="flag" class="img-fluid" />
                                                                <div class="flag-blue"></div>
                                                                <div class="flag-white"></div>
                                                                <div class="flag-red"></div>
                                                            </div>
                                                        </div>

                                                        <!-- Jersey number -->
                                                        @if ($player->jersey_no)
                                                            <div class="jersey-number">
                                                                <h3>JERSEY - {{ $player->jersey_no }}</h3>
                                                            </div>
                                                        @endif

                                                        <!-- Achievement badges -->
                                                        <div class="badges">
                                                            @foreach ($playerRewards->take(4) as $reward)
                                                                <div class="badge-item">
                                                                    @if ($reward->image)
                                                                        <img src="{{ asset($reward->image) }}"
                                                                            alt="{{ $reward->name }}"
                                                                            class="badge-img" />
                                                                    @else
                                                                        <img src="{{ asset($playerAssetPath . '/image/player-tag-1.png') }}"
                                                                            alt="{{ $reward->name }}"
                                                                            class="badge-img" />
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <!-- Main content -->
                                                    <div class="main-content">
                                                        <!-- Diagonal lines background -->
                                                        <div class="diagonal-lines"></div>

                                                        <!-- Player image -->
                                                        <div class="player-image-on-card">
                                                            @if ($player->photo)
                                                                <img src="{{ asset($player->photo) }}"
                                                                    alt="{{ $player->name }}" class="img-fluid" />
                                                            @else
                                                                <img src="{{ asset($playerAssetPath . '/image/player-box-img.png') }}"
                                                                    alt="{{ $player->name }}" class="img-fluid" />
                                                            @endif
                                                        </div>

                                                        <!-- Player name -->
                                                        <div class="player-name player-name-on-card">
                                                            <h2>{{ $player->name }}</h2>
                                                        </div>

                                                        <!-- Stats section -->
                                                        <div class="stats-section">
                                                            <div class="stats-header">
                                                                <div class="row g-0 text-center">
                                                                    @foreach ($statsWithValues->take(3) as $stat)
                                                                        <div class="col">{{ strtoupper($stat->name) }}
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="stats-values">
                                                                <div class="row g-0 text-center">
                                                                    @foreach ($statsWithValues->take(3) as $stat)
                                                                        <div class="col">{{ $stat->value }}</div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="club" role="tabpanel">
                            <div class="row">
                                <div class="col-12 club-section">
                                    <h2 class="club-title">Club History</h2>
                                    <div class="club-divider"></div>
                                    <div class="club-list">
                                        <div class="col-12 col-md-6">
                                            @forelse($clubHistory as $history)
                                                <div class="club-item">
                                                    <div class="club-dates">
                                                        {{ $history->started_at->format('Y') }} -
                                                        {{ $history->ended_at ? $history->ended_at->format('Y') : 'Present' }}
                                                    </div>
                                                    <div class="club-name">
                                                        {{ $history->club->name ?? 'Unknown Club' }}
                                                        @if ($history->team)
                                                            ({{ $history->team->name }})
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                @if ($player->club)
                                                    <div class="club-item">
                                                        <div class="club-dates">Current</div>
                                                        <div class="club-name">{{ $player->club->name }}</div>
                                                    </div>
                                                @else
                                                    <div class="club-item">
                                                        <div class="club-dates">No club history</div>
                                                        <div class="club-name">Available</div>
                                                    </div>
                                                @endif
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="teams" role="tabpanel">
                            <div class="row">
                                @forelse($teamMembers as $member)
                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                        <div class="player-card">
                                            <!-- Card background -->
                                            <div class="card-background">
                                                <img src="{{ asset($playerAssetPath . '/image/Base.png') }}"
                                                    alt="Card Background" class="card-bg-img" />

                                                <!-- Left sidebar -->
                                                <div class="sidebar">
                                                    <!-- Flag -->
                                                    <div class="h25"></div>
                                                    <div class="flag-container">
                                                        <div class="flag">
                                                            <img src="{{ asset($playerAssetPath . '/image/flag.png') }}"
                                                                alt="flag" class="img-fluid" />
                                                            <div class="flag-blue"></div>
                                                            <div class="flag-white"></div>
                                                            <div class="flag-red"></div>
                                                        </div>
                                                    </div>

                                                    <!-- Jersey number -->
                                                    @if ($member->jersey_no)
                                                        <div class="jersey-number">
                                                            <h3>JERSEY - {{ $member->jersey_no }}</h3>
                                                        </div>
                                                    @endif

                                                    <!-- Achievement badges -->
                                                    <div class="badges">
                                                        @foreach ($allRewards->take(4) as $reward)
                                                            <div class="badge-item adjust-badge-item">
                                                                @if ($reward->image)
                                                                    <img src="{{ asset($reward->image) }}"
                                                                        alt="{{ $reward->name }}" class="badge-img" />
                                                                @else
                                                                    <img src="{{ asset($playerAssetPath . '/image/player-tag-1.png') }}"
                                                                        alt="{{ $reward->name }}" class="badge-img" />
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Main content -->
                                                <div class="main-content">
                                                    <!-- Diagonal lines background -->
                                                    <div class="diagonal-lines"></div>

                                                    <!-- Player image -->
                                                    <div class="player-image-on-card">
                                                        @if ($member->photo)
                                                            <img src="{{ asset($member->photo) }}"
                                                                alt="{{ $member->name }}" class="img-fluid-card" />
                                                        @else
                                                            <img src="{{ asset($playerAssetPath . '/image/player-box-img-1.png') }}"
                                                                alt="{{ $member->name }}" class="img-fluid-card" />
                                                        @endif
                                                    </div>

                                                    <!-- Player name -->
                                                    <div class="player-name player-name-on-card">
                                                        <h2>{{ $member->name }}</h2>
                                                    </div>

                                                    <!-- Stats section -->
                                                    <div class="stats-section">
                                                        <div class="stats-header">
                                                            <div class="row g-0 text-center">
                                                                <div class="col">GOAL</div>
                                                                <div class="col">ASSIST</div>
                                                                <div class="col">GBR</div>
                                                            </div>
                                                        </div>
                                                        <div class="stats-values">
                                                            <div class="row g-0 text-center">
                                                                <div class="col">102</div>
                                                                <div class="col">24</div>
                                                                <div class="col">75%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <p>No team members found.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="tab-pane fade" id="awards" role="tabpanel">
                            <div class="row">
                                <div class="col-12 award-section">
                                    <h2 class="club-title">Awards & Achievements</h2>
                                    <div class="club-divider"></div>
                                    <div class="awards-img-list">
                                        @forelse($playerRewards as $reward)
                                            <div class="col-12 col-sm-6 col-md-4 mt-lg-2 p-lg-2 p-2">
                                                <div class="bg-awards-color">
                                                    @if ($reward->image)
                                                        <img src="{{ asset($reward->image) }}" alt="{{ $reward->name }}"
                                                            class="img-fluid" />
                                                    @else
                                                        <img src="{{ asset($playerAssetPath . '/image/award-1.png') }}"
                                                            alt="{{ $reward->name }}" class="img-fluid" />
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <p>No awards found.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="posts" role="tabpanel">
                            <div class="row">
                                <div class="col-12 post-section">
                                    <h2 class="club-title">Posts</h2>
                                    <div class="club-divider"></div>
                                    @forelse($posts as $post)
                                        <div class="post-list">
                                            <div class="profile">
                                                @if ($player->photo)
                                                    <img src="{{ asset($player->photo) }}" alt="pro"
                                                        class="img-fluid" />
                                                @else
                                                    <img src="{{ asset($playerAssetPath . '/image/ellipse-12@2x.png') }}"
                                                        alt="pro" class="img-fluid" />
                                                @endif
                                                <div class="frame-49">
                                                    <div class="heading">{{ $player->name }}</div>
                                                    <div class="sub">{{ $post->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                            <p class="para">
                                                {!! nl2br(e($post->content)) !!}
                                            </p>
                                            @if ($post->image)
                                                <div class="post-img">
                                                    <img src="{{ asset($post->image) }}" alt="Post Image"
                                                        class="img-fluid" />
                                                </div>
                                            @endif
                                            <div class="post-icon-list">
                                                <div class="post-icom">
                                                    <span><img src="{{ asset($playerAssetPath . '/image/like.svg') }}"
                                                            alt="icon" /></span>
                                                    Like
                                                </div>
                                                <div class="post-icom">
                                                    <span><img src="{{ asset($playerAssetPath . '/image/comment.svg') }}"
                                                            alt="icon" /></span>
                                                    Comment
                                                </div>
                                                <div class="post-icom">
                                                    <span><img src="{{ asset($playerAssetPath . '/image/share.svg') }}"
                                                            alt="icon" /></span>
                                                    Share
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="post-list">
                                            <p>No posts found.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('theme-scripts')
    <!-- Donation Modal -->
    <div class="modal fade" id="donationModal" tabindex="-1" aria-labelledby="donationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="donationModalLabel">Make a Donation to {{ $player->name }}</h5>
                    <button type="button" class="btn-close" onclick="closeDonationModal()" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="donationForm">
                        @csrf
                        <input type="hidden" name="recipient_id" value="{{ $player->id }}">
                        <input type="hidden" name="recipient_type" value="player">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="donor_name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="donor_name" name="donor_name"
                                    value="{{ auth()->user() ? auth()->user()->name : '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="donor_email" class="form-label">Your Email</label>
                                <input type="email" class="form-control" id="donor_email" name="donor_email"
                                    value="{{ auth()->user() ? auth()->user()->email : '' }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Donation Amount ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="amount" name="amount" min="1"
                                    step="0.01" value="10" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message (Optional)</label>
                            <textarea class="form-control" id="message" name="message" rows="3"
                                placeholder="Leave a message of support..."></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Your donation will help support {{ $player->name }}'s training, equipment, and competition
                            expenses.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeDonationModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="processDonation()">
                        <i class="fas fa-heart me-2"></i>Make Donation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
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

        function openDonationModal() {
            const modalElement = document.getElementById('donationModal');
            if (modalElement) {
                // Try Bootstrap 5 first
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                } else {
                    // Fallback: manually show the modal
                    modalElement.style.display = 'block';
                    modalElement.classList.add('show');
                    document.body.classList.add('modal-open');

                    // Add backdrop
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    backdrop.id = 'modalBackdrop';
                    document.body.appendChild(backdrop);
                }
            } else {
                console.error('Donation modal not found');
            }
        }

        function closeDonationModal() {
            const modalElement = document.getElementById('donationModal');
            if (modalElement) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                } else {
                    // Manual fallback
                    modalElement.style.display = 'none';
                    modalElement.classList.remove('show');
                    document.body.classList.remove('modal-open');

                    // Remove backdrop
                    const backdrop = document.getElementById('modalBackdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                }
            }
        }

        function processDonation() {
            const form = document.getElementById('donationForm');
            const formData = new FormData(form);
            const submitBtn = document.querySelector('#donationModal .btn-primary');

            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            fetch('{{ route('donation.checkout') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.url) {
                        window.location.href = data.url; // Redirect to Stripe Checkout
                    } else {
                        throw new Error('Could not create checkout session');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was an error processing your donation. Please try again.');

                    // Re-enable button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-heart me-2"></i>Make Donation';
                });
        }
    </script>
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
                        <img src="{{ asset($playerAssetPath . '/image/footer-img.png') }}" alt="P2ES logo 1"
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
