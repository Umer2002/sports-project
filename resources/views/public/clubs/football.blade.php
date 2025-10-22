@extends('public.clubs.base')
<!-- $themeKey = trim($__env->yieldContent('club_theme', $themeKey ?? 'football'));
if ($themeKey === '') {
$themeKey = 'football';
} -->
@php
if ($theme === 'default') {
$theme = 'football';
}
$assetPath = "assets/clubs/{$theme}/assets";
@endphp

@section('theme-css')
<link rel="stylesheet" href="{{ asset($assetPath . '/css/style.css') }}" />
<link rel="stylesheet" href="{{ asset($assetPath . '/css/font.css') }}" />
<link rel="stylesheet" href="{{ asset($assetPath . '/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset($assetPath . '/css/profile.css') }}" />
@yield('custom-theme-css')
<style>
  .join-club-btn {
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
  }

  .sidebar-player-list .sidebar-card {
    display: block;
    position: relative;
    color: inherit;
  }
</style>
@endsection

@section('sidebar')
<aside class="profile-sidebar d-none d-lg-flex flex-column">
  <!-- Sidebar Dropdown -->
  <div class="sidebar-dropdown">
    {{ strtoupper($club->name ?? 'CLUB') }} <i class="fa-solid fa-angle-down ms-2"></i>
  </div>
  <!-- Sidebar Dropdown End -->

  <!-- Scroll Up Button -->
  <button class="sidebar-scroll-btn" onclick="scrollSidebar(-1)" aria-label="Scroll Up">
    <i class="fa-solid fa-angle-up"></i>
  </button>

  <!-- Scrollable Player List -->
  <div class="sidebar-player-list" id="sidebarPlayerList">
    @if($players && $players->count() > 0)
    @foreach($players as $player)
    <a
      href="{{ route('public.player.profile', $player->id) }}"
      class="sidebar-card text-decoration-none"
      data-player-id="{{ $player->id }}"
      aria-label="View {{ $player->name }}'s public profile">
      <img src="{{ asset($assetPath . '/image/Base.png') }}" class="sidebar-card-bg" alt="card background">
      <img
        src="{{ $player->photo ? asset($player->photo) : asset($assetPath . '/image/player-box-img.png') }}"
        class="sidebar-card-player"
        alt="{{ $player->name }}">
      <div class="sidebar-card-name">{{ strtoupper($player->name) }}</div>
    </a>
    @endforeach
    @else
    <!-- No players available message -->
    <div class="text-center p-3">
      <p class="text-muted mb-0">No players available</p>
    </div>
    @endif
  </div>

  <!-- Scroll Down Button -->
  <button class="sidebar-scroll-btn" onclick="scrollSidebar(1)" aria-label="Scroll Down">
    <i class="fa-solid fa-angle-down"></i>
  </button>
</aside>
@endsection

@section('header')
<header class="header">
  <div class="container p-0 px-sm-2">
    <nav class="navbar navbar-expand-lg">
      <div class="container-fluid align-items-center">
        <div class="d-flex g-2">
          <!-- Logo -->
          <a class="navbar-brand" href="#">
            <img src="{{ asset($assetPath . '/image/logo.png') }}" alt="Logo" />
          </a>
          <!-- Language Selector -->
          <div class="lang-dropdown d-none d-lg-flex align-items-center gap-2">
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
            <li class="nav-item"><a class="nav-link" href="#club" onclick="scrollToSection('club')">Club</a></li>
            <li class="nav-item"><a class="nav-link" href="#teams" onclick="scrollToSection('teams')">Teams</a></li>
            <li class="nav-item">
              <a class="nav-link" href="#awards" onclick="scrollToSection('awards')">Awards</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="#bio" onclick="scrollToSection('bio')">Bio</a></li>
          </ul>
          <!-- Search + Login -->
          <div class="d-flex align-items-center gap-3">
            <div class="position-relative search-wrapper">
              <input type="text" class="search-box" placeholder="Search Here..." />
              <img src="{{ asset($assetPath . '/image/search.svg') }}" class="search-icon" alt="icon" />
            </div>
            <button class="login-btn">LOGIN</button>
          </div>
        </div>
      </div>
    </nav>
  </div>
</header>
@endsection

@section('hero')
<section class="hero-section-custom">
  <div class="mx-0">
    <div class="row align-items-stretch mx-0">
      <!-- Left Content -->
      <div class="col-lg-8 col-12 mb-4 mb-lg-0 px-0">
        <div class="hero-left-content h-100 pb-0">
          <div class="weather-location mb-3 d-flex align-items-center gap-2">
            <span class="weather-temp">{{ $weather['temp'] ?? '13' }}&deg;</span>
            <span class="weather-icon"><img src="{{ asset($assetPath . '/image/Sun-cloud.svg') }}" alt="weather" style="height:56px;"></span>
            <div class="d-flex flex-column align-items-start">
              <span class="weather-desc">H:{{ $weather['high'] ?? '16' }}&deg; L:{{ $weather['low'] ?? '8' }}&deg;</span>
              <span class="weather-city">{{ $club->city ?? 'Ottawa' }}, {{ $club->state ?? 'South' }}</span>
            </div>
          </div>
          <div class="hero-heading mb-3">
            <span class="hero-label">We are the</span>
            <h1 class="hero-title mt-4">{{ $club->name ?? 'Ottawa Football' }}<br></h1>
          </div>
          <button class="donate-btn mb-4" onclick="openDonationModal()">MAKE A DONATION</button>
          <div class="d-flex justify-content-center d-lg-none">
            <div class="mobile-hexagon-badge hexagon-badge mb-3">
              @if($club->sport && $club->sport->icon_path)
              <img src="{{ asset('storage/' . $club->sport->icon_path) }}" alt="{{ $club->sport->name }} Icon" class="hex-logo-img">
              @endif
              @if($club->logo)
              <img src="{{ asset('storage/' . $club->logo) }}" alt="{{ $club->name }} Logo" class="hex-logo-img" />
              @endif

            </div>
          </div>
          <div class="share-profile mb-4">
            <span>Share Profile</span>
            <div class="social-icons mt-2">
              @hasSection('social-links')
              @yield('social-links')
              @else
              @php
              $socialLinks = [];
              if ($club->social_links) {
              if (is_string($club->social_links)) {
              $socialLinks = json_decode($club->social_links, true) ?: [];
              } elseif (is_array($club->social_links)) {
              $socialLinks = $club->social_links;
              }
              }
              @endphp

              @if(isset($socialLinks['facebook']) && $socialLinks['facebook'])
              <a href="{{ $socialLinks['facebook'] }}" class="social-icon facebook" target="_blank"><i class="fab fa-facebook-f"></i></a>
              @endif

              @if(isset($socialLinks['instagram']) && $socialLinks['instagram'])
              <a href="{{ $socialLinks['instagram'] }}" class="social-icon instagram" target="_blank"><i class="fab fa-instagram"></i></a>
              @endif

              @if(isset($socialLinks['tiktok']) && $socialLinks['tiktok'])
              <a href="{{ $socialLinks['tiktok'] }}" class="social-icon tiktok" target="_blank"><i class="fab fa-tiktok"></i></a>
              @endif

              @if(isset($socialLinks['snapchat']) && $socialLinks['snapchat'])
              <a href="{{ $socialLinks['snapchat'] }}" class="social-icon snapchat" target="_blank"><i class="fab fa-snapchat-ghost"></i></a>
              @endif

              @if(isset($socialLinks['pinterest']) && $socialLinks['pinterest'])
              <a href="{{ $socialLinks['pinterest'] }}" class="social-icon pinterest" target="_blank"><i class="fab fa-pinterest-p"></i></a>
              @endif

              @if(isset($socialLinks['linkedin']) && $socialLinks['linkedin'])
              <a href="{{ $socialLinks['linkedin'] }}" class="social-icon linkedin" target="_blank"><i class="fab fa-linkedin-in"></i></a>
              @endif

              @if(isset($socialLinks['reddit']) && $socialLinks['reddit'])
              <a href="{{ $socialLinks['reddit'] }}" class="social-icon reddit" target="_blank"><i class="fab fa-reddit-alien"></i></a>
              @endif

              @if(isset($socialLinks['twitter']) && $socialLinks['twitter'])
              <a href="{{ $socialLinks['twitter'] }}" class="social-icon twitter" target="_blank"><i class="fab fa-x-twitter"></i></a>
              @endif

              @if(isset($socialLinks['youtube']) && $socialLinks['youtube'])
              <a href="{{ $socialLinks['youtube'] }}" class="social-icon youtube" target="_blank"><i class="fab fa-youtube"></i></a>
              @endif
              @endif
            </div>
          </div>
          <div class="hero-info-cards d-flex flex-column justify-content-center justify-content-md-start align-items-center flex-md-row gap-3 mb-4 flex-wrap" style="overflow: visible;">
            <div class="info-card mb-2">
              <div class="info-card-title"><img src="{{ asset($assetPath . '/image/location.svg') }}" style="height:24px;">
                <span>{{ $club->city ?? 'Ottawa' }}, {{ $club->country ?? 'Canada' }}</span>
              </div>
              <div class="info-card-desc">{{ $club->description ?? 'Our club serves athletes across the region with a dedicated training facility and professional coaching staff.' }}</div>
            </div>
            <div class="info-card mb-2">
              <div class="info-card-desc">{{ $club->league_info ?? 'We proudly compete in the Ontario Youth Football Club League (OYSL), one of the most recognized development leagues in Canada.' }}</div>
              @if($club->invite_link)
              <a href="{{ $club->invite_link }}" class="join-club-btn mt-4" target="_blank">JOIN CLUB</a>
              @else
              <a href="{{ route('club.register', $club->id) }}" class="join-club-btn mt-4">JOIN CLUB</a>
              @endif
            </div>
          </div>
          <div class="hero-contact-bar d-flex align-items-center gap-3 flex-wrap">
            @if($club->phone)
            <span><img src="{{ asset($assetPath . '/image/whatsapp.svg') }}" alt="whatsapp" style="height:24px;"> {{ $club->phone }}</span>
            @endif
            @if($club->email)
            <span><img src="{{ asset($assetPath . '/image/email.svg') }}" alt="email" style="height:24px;"> {{ $club->email }}</span>
            @endif
            @if($club->instagram)
            <span><img src="{{ asset($assetPath . '/image/insta.svg') }}" alt="instagram" style="height:24px;"> {{ $club->instagram }}</span>
            @endif
            @if($club->facebook)
            <span><img src="{{ asset($assetPath . '/image/fb.svg') }}" alt="facebook" style="height:24px;"> {{ $club->facebook }}</span>
            @endif
          </div>
        </div>
      </div>
      <!-- Right Content -->
      <div class="d-none d-lg-flex col-lg-4 col-12 d-flex flex-column align-items-center justify-content-center position-relative px-0">
        <div class="hero-right-content h-100 w-100 d-flex flex-column align-items-center justify-content-center position-relative">
          <div class="hexagon-badge mb-3">
            @if($club->sport && $club->sport->icon_path)
            <img src="{{ asset('storage/' . $club->sport->icon_path) }}" alt="{{ $club->sport->name }} Icon" class="hex-logo-img">
            @endif
            @if($club->logo)
            <img src="{{ asset('storage/' . $club->logo) }}" alt="{{ $club->name }} Logo" class="hex-logo-img" />
            @endif
          </div>
          <div class="vertical-hockey-text">{{ $club->sport->name ?? 'Football' }}</div>
          <div class="hero-managed-by">This club is managed by {{ $club->managed_by ?? 'volunteer' }}</div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('tabs')
<!-- ===================== Club List Tab Section Start ===================== -->
<section class="tab-content club-list-section">
  <div class="container">
    <!-- Tab Buttons -->
    <input type="radio" name="css-tabs" id="tab-clublist-club" class="css-tab-input" checked>
    <input type="radio" name="css-tabs" id="tab-clublist-post" class="css-tab-input">
    <input type="radio" name="css-tabs" id="tab-clublist-bio" class="css-tab-input">
    <input type="radio" name="css-tabs" id="tab-clublist-coach" class="css-tab-input">
    <input type="radio" name="css-tabs" id="tab-clublist-teams" class="css-tab-input">
    <input type="radio" name="css-tabs" id="tab-clublist-players" class="css-tab-input">
    <input type="radio" name="css-tabs" id="tab-clublist-skills" class="css-tab-input">
    <div class="row justify-content-center">
      <div class="d-flex justify-content-center">
        <div class="menu-bar">
          <label for="tab-clublist-club" class="css-tab-label">Club</label>
          <label for="tab-clublist-post" class="css-tab-label">Post</label>
          <label for="tab-clublist-bio" class="css-tab-label">Bio</label>
          <label for="tab-clublist-coach" class="css-tab-label">Coach</label>
          <label for="tab-clublist-teams" class="css-tab-label">Teams</label>
          <label for="tab-clublist-players" class="css-tab-label">Players</label>
          <label for="tab-clublist-skills" class="css-tab-label">Skills</label>
        </div>
      </div>
      <div class="col-12 mt-4">
        <div class="tab-wrapper mx-auto">
          <!-- Club Tab Content -->
          <div class="tab-pane" id="clublist-club" role="tabpanel">
            <div class="club-list-header d-flex justify-content-between align-items-center">
              <h3 class="club-list-title mb-0">Club List</h3>
              <div class="club-list-filters d-flex align-items-center gap-2">
                <div class="club-search-box position-relative">
                  <input name="club-search-input" id="club-search-input" type="text" class="club-search-input" placeholder="Search Here..." />
                  <button class="club-search-btn" type="button">
                    <i class="fa fa-search"></i>
                  </button>
                </div>

                <div class="dropdown" id="cityDropdown">
                  <button
                    class="btn btn-outline-secondary d-flex justify-content-between align-items-center city-dropdown-btn"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <span class="flex-grow-1 text-center" id="citySelected">City</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" class="flex-shrink-0">
                      <path d="M8.11875 9.28859L11.9988 13.1686L15.8787 9.28859C16.2688 8.89859 16.8988 8.89859 17.2888 9.28859C17.6788 9.67859 17.6788 10.3086 17.2888 10.6986L12.6988 15.2886C12.3088 15.6786 11.6788 15.6786 11.2888 15.2886L6.69875 10.6986C6.30875 10.3086 6.30875 9.67859 6.69875 9.28859C7.08875 8.90859 7.72875 8.89859 8.11875 9.28859Z" fill="white" />
                    </svg>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-dark w-100" aria-labelledby="cityDropdown">
                    {{-- Backend: loop through $cities or any collection passed from controller --}}
                    @foreach($cities ?? ['Toronto','Ottawa','Montreal','Vancouver'] as $city)
                    <li>
                      <a class="dropdown-item" href="#" data-value="{{ $city }}" onclick="selectCity('{{ $city }}')">
                        {{ $city }}
                      </a>
                    </li>
                    @endforeach
                  </ul>
                </div>
              </div>
            </div>



            <div class="club-list-grid-container">
              <div class="club-list-grid">
                <div class="club-list-grid-inner">
                  <!-- Club Stats Cards -->
                  <div class="club-card">
                    <img src="{{ $club->logo ? asset('storage/' . $club->logo) : asset($assetPath . '/image/logo.png') }}" alt="Club Logo" class="club-logo">
                    <div class="club-meta">
                      <span class="club-id">{{ $totalPlayers }}</span>
                    </div>
                    <div class="club-footer d-flex justify-content-between align-items-center">
                      <span class="footer-item d-flex align-items-center gap-1">
                        <img src="{{ asset($assetPath . '/image/flag.jpeg') }}" alt="flag" class="footer-flag" />
                        <span class="footer-number">{{ $totalPlayers }}</span>
                      </span>
                      <span class="footer-item d-flex align-items-center gap-1">
                        <img src="{{ asset($assetPath . '/image/player-card.svg') }}" alt="player" class="footer-player" />
                        <span class="footer-number">{{ $totalTeams }}</span>
                      </span>
                    </div>
                  </div>


                </div>
              </div>
            </div>
          </div>

          <!-- Post Tab Content -->
          <div class="tab-pane" id="clublist-post" role="tabpanel">
            <div class="post-section">
              <div class="post-title mb-3">Posts</div>
              <div class="post-body-outer">
                @if($posts && $posts->count() > 0)
                @foreach($posts as $post)
                <div class="post-card">
                  <div class="post-header d-flex align-items-center gap-3 mb-2">
                    <img src="{{ $club->logo ? asset('storage/' . $club->logo) : asset($assetPath . '/image/logo.png') }}" alt="avatar" class="post-avatar">
                    <div>
                      <div class="post-author fw-bold">{{ $club->name }}</div>
                      <div class="post-time">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                  </div>
                  <div class="post-body my-4">
                    {{ $post->content }}
                  </div>
                  @if($post->image)
                  <div class="post-image mb-3">
                    <img src="{{ asset('uploads/posts/' . $post->image) }}" alt="post" class="img-fluid rounded-3 w-100">
                  </div>
                  @endif
                  <div class="post-actions d-flex align-items-center gap-4 mt-2">
                    <button class="post-action-btn"><i class="fa-regular fa-thumbs-up"></i> <span>Like</span></button>
                    <button class="post-action-btn"><i class="fa-regular fa-comment"></i> <span>Comment</span></button>
                    <button class="post-action-btn"><i class="fa-regular fa-share-from-square"></i> <span>Share</span></button>
                  </div>
                </div>
                @endforeach
                @else
                <div class="post-card">
                  <div class="post-header d-flex align-items-center gap-3 mb-2">
                    <img src="{{ asset($assetPath . '/image/logo.png') }}" alt="avatar" class="post-avatar">
                    <div>
                      <div class="post-author fw-bold">{{ $club->name ?? 'Club' }}</div>
                      <div class="post-time">Just now</div>
                    </div>
                  </div>
                  <div class="post-body my-4">
                    Welcome to our club! We're excited to share updates and news with our community.
                  </div>
                  <div class="post-actions d-flex align-items-center gap-4 mt-2">
                    <button class="post-action-btn"><i class="fa-regular fa-thumbs-up"></i> <span>Like</span></button>
                    <button class="post-action-btn"><i class="fa-regular fa-comment"></i> <span>Comment</span></button>
                    <button class="post-action-btn"><i class="fa-regular fa-share-from-square"></i> <span>Share</span></button>
                  </div>
                </div>
                @endif
              </div>
            </div>
          </div>

          <!-- Bio Tab Content -->
          <div class="tab-pane" id="clublist-bio" role="tabpanel">
            <div class="bio-section">
              <div class="bio-card">
                <div class="bio-title">Bio</div>
                <div class="bio-divider"></div>
                <div class="bio-text mb-5">
                  {{ $club->bio ?? 'Our club is dedicated to developing young athletes and promoting sports excellence in our community.' }}
                </div>
                <div class="achievements-title">Achievements</div>
                <div class="bio-divider"></div>
                <ul class="achievements-list mt-4">
                  @if($club->achievements)
                  @foreach(json_decode($club->achievements, true) ?? [] as $achievement)
                  <li><img src="{{ asset($assetPath . '/image/tick-mark.svg') }}" class="achievement-icon" alt="icon">{{ $achievement }}</li>
                  @endforeach
                  @else
                  <li><img src="{{ asset($assetPath . '/image/tick-mark.svg') }}" class="achievement-icon" alt="icon">2023 Provincial Champions – U16</li>
                  <li><img src="{{ asset($assetPath . '/image/tick-mark.svg') }}" class="achievement-icon" alt="icon">2022 League Winners – U12</li>
                  <li><img src="{{ asset($assetPath . '/image/tick-mark.svg') }}" class="achievement-icon" alt="icon">30+ Players Awarded College Scholarships</li>
                  <li><img src="{{ asset($assetPath . '/image/tick-mark.svg') }}" class="achievement-icon" alt="icon">Players Represented in National Teams</li>
                  @endif
                </ul>
              </div>
            </div>
          </div>

          <!-- Coach Tab Content -->
          <div class="tab-pane" id="clublist-coach" role="tabpanel">
            <div class="coach-section">
              <div class="coach-grid">
                @if($coaches && $coaches->count() > 0)
                @foreach($coaches as $coach)
                <div class="coach-card">
                  <div class="coach-card-header">
                    <img src="{{ $coach->photo ? asset('uploads/coaches/' . $coach->photo) : asset($assetPath . '/image/coach-image.png') }}" alt="{{ $coach->name }}" class="coach-img">
                  </div>
                  <div class="coach-card-body">
                    <div class="coach-name">{{ $coach->name }}</div>
                    @if($coach->role)
                    <div class="coach-role">{{ $coach->role }}</div>
                    @endif
                  </div>
                  @if($coach->qualifications)
                  <div class="coach-card-footer">
                    {{ $coach->qualifications }}
                  </div>
                  @endif
                </div>
                @endforeach
                @else
                <!-- No coaches available message -->
                <div class="text-center">
                  <div class="alert alert-info">
                    <h4>No Coaches Available</h4>
                    <p>This club doesn't have any coaches yet.</p>
                  </div>
                </div>
                @endif
              </div>
            </div>
          </div>

          <!-- Teams Tab Content -->
          <div class="tab-pane" id="clublist-teams" role="tabpanel">
            <div class="teams-section">
              <div class="teams-grid">
                @if($teams && $teams->count() > 0)
                @foreach($teams as $team)
                <div class="team-card">
                  <div class="team-score">{{ $team->players->count() }}</div>
                  <img src="{{ asset($assetPath . '/image/team-logo.svg') }}" alt="{{ $team->name }}" class="team-logo">
                  <div class="team-info">
                    <div class="team-name">{{ $team->name }}</div>
                    @if($team->age_group)
                    <div class="team-country-bar">
                      <span class="team-country">{{ $team->age_group }}</span>
                    </div>
                    @endif
                  </div>
                </div>
                @endforeach
                @else
                <!-- No teams available message -->
                <div class="text-center">
                  <div class="alert alert-info">
                    <h4>No Teams Available</h4>
                    <p>This club doesn't have any teams yet.</p>
                  </div>
                </div>
                @endif
              </div>
            </div>
          </div>

          <!-- Players Tab Content -->
          <div class="tab-pane" id="clublist-players" role="tabpanel">
            <div class="players-section">
              <div class="players-grid row justify-content-center g-4 mt-3">
                @if($players && $players->count() > 0)
                @foreach($players->take(6) as $player)
                <div class="col-12 col-md-6 col-lg-4">
                  <div class="player-card">
                    <div class="card-background">
                      <img src="{{ asset($assetPath . '/image/edited_BG.png') }}" alt="Card Background" class="card-bg-img" />
                      <div class="sidebar">
                        <div class="h25-donation"></div>
                        <div class="flag-container">
                          <div class="flag">
                            <!-- <img src="{{ asset($assetPath . '/image/flag.jpeg') }}" alt="flag" class="img-fluid" /> -->
                            <div class="flag-blue"></div>
                            <div class="flag-white"></div>
                            <div class="flag-red"></div>
                          </div>
                        </div>
                        @if($player->jersey_number)
                        <div class="jersey-number">
                          <h3>JERSEY - {{ $player->jersey_number }}</h3>
                        </div>
                        @endif
                        <div class="badges">
                          @if($player->rewards && $player->rewards->count() > 0)
                          @foreach($player->rewards->take(4) as $reward)
                          <div class="badge-item">
                            <img src="{{ asset('images/rewards/' . $reward->image) }}" alt="{{ $reward->name ?: 'Reward' }}" class="badge-img" />
                          </div>
                          @endforeach
                          @else
                          <div class="badge-item">
                            <img src="{{ asset($assetPath . '/image/player-tag-1.png') }}" alt="Badge" class="badge-img" />
                          </div>
                          @endif
                        </div>
                      </div>
                      <div class="main-content">
                        @if($player->photo)
                        <div class="player-image-on-card">
                          <img src="{{ asset($player->photo) }}" alt="{{ $player->name }}" class="img-fluid-card" />
                        </div>
                        @endif
                        <div class="player-name player-name-on-card text-capitalize">
                          <h2>{{ strtoupper($player->name) }}</h2>
                        </div>
                        <div class="stats-section">
                          <div class="stats-header mb-0">
                            <div class="row g-0 text-center">
                              <div class="col">AGE</div>
                              <div class="col">DOB</div>
                              <div class="col">POSITION</div>
                            </div>
                          </div>
                          <div class="stats-values">
                            <div class="row g-0 text-center">
                              <div class="col">{{ $player->age ?: '' }}</div>
                              <div class="col">{{ $player->date_of_birth ? $player->date_of_birth->format('Y') : '' }}</div>
                              <div class="col">{{ $player->position ? $player->position->name : '' }}</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
                @else
                <!-- No players available message -->
                <div class="col-12 text-center">
                  <div class="alert alert-info">
                    <h4>No Players Available</h4>
                    <p>This club doesn't have any players yet.</p>
                  </div>
                </div>
                @endif
              </div>
            </div>
          </div>

          <!-- Skills Tab Content -->
          <div class="tab-pane" id="clublist-skills" role="tabpanel">
            <div class="skills-section">
              <div class="skills-grid">
                @if($tasks && $tasks->count() > 0)
                @foreach($tasks->take(8) as $task)
                <div class="skill-card">
                  <div class="skill-image-wrapper">
                    <img src="{{ asset($assetPath . '/image/skill-video-image.png') }}" alt="Skill" class="skill-image">
                    <span class="play-btn"><img src="{{ asset($assetPath . '/image/play-btn.svg') }}" alt="Play"></span>
                  </div>
                  <div class="skill-title">{{ $task->title }}</div>
                </div>
                @endforeach
                @else
                <!-- No skills/tasks available message -->
                <div class="text-center">
                  <div class="alert alert-info">
                    <h4>No Skills/Tasks Available</h4>
                    <p>This club doesn't have any skills or training tasks yet.</p>
                  </div>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('newsletter')
<!-- news letter section start -->
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
<!-- footer start -->
<footer class="footer">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 col-md-3">
        <div class="overlap-group-1 position-relative">
          <div class="rectangle-36"></div>
          <img src="{{ asset($assetPath . '/image/footer-img.png') }}" alt="P2ES logo 1" class="img-fluid" />
        </div>
      </div>
      <div class="col-12 col-md-5">
        <div class="mail-content">
          info@play2earnsports.com, 223-23EARN-60
        </div>
      </div>
      <div class="col-12 col-md-4">
        <div class="footer-btn">
          <a href="#" class="btn sign-up-now" style="margin: auto;">SIGN UP NOW</a>
        </div>
      </div>
    </div>
  </div>
</footer>
@endsection

<!-- Donation Modal -->
<div class="modal fade" id="donationModal" tabindex="-1" aria-labelledby="donationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="donationModalLabel">Make a Donation to {{ $club->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="donationForm">
          @csrf
          <input type="hidden" name="club_id" value="{{ $club->id }}">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="donor_name" class="form-label">Your Name</label>
              <input type="text" class="form-control" id="donor_name" name="donor_name" value="{{ auth()->user() ? auth()->user()->name : '' }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="donor_email" class="form-label">Your Email</label>
              <input type="email" class="form-control" id="donor_email" name="donor_email" value="{{ auth()->user() ? auth()->user()->email : '' }}" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="amount" class="form-label">Donation Amount ($)</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" value="10" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="message" class="form-label">Message (Optional)</label>
            <textarea class="form-control" id="message" name="message" rows="3" placeholder="Leave a message of support..."></textarea>
          </div>

          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Your donation will help support {{ $club->name }}'s programs, equipment, and activities.
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="processDonation()">
          <i class="fas fa-heart me-2"></i>Make Donation
        </button>
      </div>
    </div>
  </div>
</div>

@section('theme-scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
  function scrollSidebar(direction) {
    const sidebarPlayerList = document.getElementById('sidebarPlayerList');

    if (!sidebarPlayerList) return;

    // Check if we're on mobile (horizontal layout)
    const isMobile = window.innerWidth <= 991.98;

    if (isMobile) {
      // Mobile view horizontal scroll
      const scrollAmount = 200; // Adjust scroll amount as needed
      const currentScroll = sidebarPlayerList.scrollLeft;

      if (direction === 1) {
        // Right scroll (next)
        sidebarPlayerList.scrollTo({
          left: currentScroll + scrollAmount,
          behavior: 'smooth'
        });
      } else {
        // Left scroll (previous)
        sidebarPlayerList.scrollTo({
          left: currentScroll - scrollAmount,
          behavior: 'smooth'
        });
      }
    } else {
      // Desktop view vertical scroll (existing code)
      const scrollAmount = 150;
      const currentScroll = sidebarPlayerList.scrollTop;

      if (direction === 1) {
        sidebarPlayerList.scrollTo({
          top: currentScroll + scrollAmount,
          behavior: 'smooth'
        });
      } else {
        sidebarPlayerList.scrollTo({
          top: currentScroll - scrollAmount,
          behavior: 'smooth'
        });
      }
    }
  }

  function openDonationModal() {
    const modal = new bootstrap.Modal(document.getElementById('donationModal'));
    modal.show();
  }

  function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
      section.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  }

  function processDonation() {
    const form = document.getElementById('donationForm');
    const formData = new FormData(form);
    const submitBtn = document.querySelector('#donationModal .btn-primary');

    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

    fetch('{{ route("donation.checkout") }}', {
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

  function selectCity(city) {
    document.getElementById("citySelected").textContent = city;
    // Optional: trigger live filtering via AJAX
    // fetchClubsByCity(city);
  }

  // Enhanced Horizontal Dragging Functionality and Mobile Enhancements
  document.addEventListener("DOMContentLoaded", function() {
    try {
      // Initialize dragging functionality
      initializeDraggingFunctionality();

      // Initialize mobile enhancements
      initializeMobileEnhancements();
    } catch (error) {
      console.error("JavaScript initialization error:", error);
    }
  });

  // Dragging Functionality
  function initializeDraggingFunctionality() {
    const filtersContainer = document.querySelector(".club-list-filters");

    if (!filtersContainer) {
      console.warn("Filters container not found");
      return;
    }

    let isDragging = false;
    let startX = 0;
    let scrollLeft = 0;
    let velocity = 0;
    let lastX = 0;
    let lastTime = 0;
    let animationFrame = null;

    // Enhanced drag state management
    const dragState = {
      isActive: false,
      startPosition: 0,
      currentPosition: 0,
      velocity: 0,
      momentum: 0,
    };

    // Mouse Down Event - Start Dragging
    filtersContainer.addEventListener("mousedown", function(e) {
      // Prevent dragging on form elements
      if (
        e.target.tagName === "INPUT" ||
        e.target.tagName === "BUTTON" ||
        e.target.closest("button") ||
        e.target.closest(".dropdown")
      ) {
        return;
      }

      isDragging = true;
      dragState.isActive = true;

      startX = e.pageX - filtersContainer.offsetLeft;
      scrollLeft = filtersContainer.scrollLeft;
      lastX = e.pageX;
      lastTime = Date.now();
      velocity = 0;

      // Visual feedback
      filtersContainer.style.cursor = "grabbing";
      filtersContainer.style.userSelect = "none";
      filtersContainer.classList.add("dragging");

      // Stop any ongoing momentum
      if (animationFrame) {
        cancelAnimationFrame(animationFrame);
      }

      e.preventDefault();
    });

    // Mouse Move Event - Handle Dragging
    document.addEventListener("mousemove", function(e) {
      if (!isDragging || !dragState.isActive) return;

      e.preventDefault();

      const currentTime = Date.now();
      const deltaTime = currentTime - lastTime;

      if (deltaTime > 0) {
        // Calculate movement
        const x = e.pageX - filtersContainer.offsetLeft;
        const walk = (x - startX) * 1.2; // Sensitivity multiplier

        // Calculate velocity for momentum
        velocity = (e.pageX - lastX) / deltaTime;

        // Apply horizontal scroll
        const newScrollLeft = scrollLeft - walk;
        filtersContainer.scrollLeft = Math.max(0, newScrollLeft);

        // Update tracking variables
        lastX = e.pageX;
        lastTime = currentTime;
        dragState.currentPosition = e.pageX;
        dragState.velocity = velocity;
      }
    });

    // Mouse Up Event - End Dragging
    document.addEventListener("mouseup", function() {
      if (!isDragging) return;

      isDragging = false;
      dragState.isActive = false;

      // Reset visual feedback
      filtersContainer.style.cursor = "grab";
      filtersContainer.style.userSelect = "";
      filtersContainer.classList.remove("dragging");

      // Apply momentum scrolling if velocity is significant
      if (Math.abs(velocity) > 0.1) {
        applyMomentumScrolling();
      }
    });

    // Mouse Leave Event - Handle dragging outside container
    filtersContainer.addEventListener("mouseleave", function() {
      if (isDragging) {
        isDragging = false;
        dragState.isActive = false;
        filtersContainer.style.cursor = "grab";
        filtersContainer.style.userSelect = "";
        filtersContainer.classList.remove("dragging");

        // Apply momentum if there was significant movement
        if (Math.abs(velocity) > 0.1) {
          applyMomentumScrolling();
        }
      }
    });

    // Momentum Scrolling Function
    function applyMomentumScrolling() {
      let currentVelocity = velocity * 15; // Momentum multiplier

      const momentumScroll = () => {
        if (Math.abs(currentVelocity) < 0.1) {
          return; // Stop momentum when velocity is too low
        }

        // Apply friction
        currentVelocity *= 0.92;

        // Calculate new scroll position
        const currentScroll = filtersContainer.scrollLeft;
        const newScroll = currentScroll - currentVelocity;

        // Apply bounds checking
        const maxScroll =
          filtersContainer.scrollWidth - filtersContainer.clientWidth;
        const boundedScroll = Math.max(0, Math.min(maxScroll, newScroll));

        filtersContainer.scrollLeft = boundedScroll;

        // Continue animation if not at bounds and velocity is significant
        if (
          boundedScroll > 0 &&
          boundedScroll < maxScroll &&
          Math.abs(currentVelocity) > 0.1
        ) {
          animationFrame = requestAnimationFrame(momentumScroll);
        }
      };

      animationFrame = requestAnimationFrame(momentumScroll);
    }

    // Touch Events for Mobile Support
    let touchStartX = 0;
    let touchScrollLeft = 0;

    filtersContainer.addEventListener(
      "touchstart",
      function(e) {
        if (
          e.target.tagName === "INPUT" ||
          e.target.tagName === "BUTTON" ||
          e.target.closest("button") ||
          e.target.closest(".dropdown")
        ) {
          return;
        }

        touchStartX = e.touches[0].pageX - filtersContainer.offsetLeft;
        touchScrollLeft = filtersContainer.scrollLeft;
        lastX = e.touches[0].pageX;
        lastTime = Date.now();
        velocity = 0;

        filtersContainer.classList.add("dragging");
      }, {
        passive: true,
      }
    );

    filtersContainer.addEventListener(
      "touchmove",
      function(e) {
        const currentTime = Date.now();
        const deltaTime = currentTime - lastTime;

        if (deltaTime > 0) {
          const x = e.touches[0].pageX - filtersContainer.offsetLeft;
          const walk = (x - touchStartX) * 1.5;

          velocity = (e.touches[0].pageX - lastX) / deltaTime;
          filtersContainer.scrollLeft = touchScrollLeft - walk;

          lastX = e.touches[0].pageX;
          lastTime = currentTime;
        }
      }, {
        passive: true,
      }
    );

    filtersContainer.addEventListener(
      "touchend",
      function() {
        filtersContainer.classList.remove("dragging");

        if (Math.abs(velocity) > 0.1) {
          applyMomentumScrolling();
        }
      }, {
        passive: true,
      }
    );

    // Initialize cursor style
    filtersContainer.style.cursor = "grab";

    // Prevent text selection during drag
    filtersContainer.addEventListener("selectstart", function(e) {
      if (isDragging) {
        e.preventDefault();
      }
    });

    // Keyboard navigation support
    filtersContainer.addEventListener("keydown", function(e) {
      if (e.key === "ArrowLeft") {
        e.preventDefault();
        filtersContainer.scrollBy({
          left: -50,
          behavior: "smooth",
        });
      } else if (e.key === "ArrowRight") {
        e.preventDefault();
        filtersContainer.scrollBy({
          left: 50,
          behavior: "smooth",
        });
      }
    });

    // Make container focusable for keyboard navigation
    filtersContainer.setAttribute("tabindex", "0");
  }

  // Mobile-specific enhancements for search and dropdown
  function initializeMobileEnhancements() {
    const searchInput = document.getElementById("club-search-input");
    const cityDropdown = document.getElementById("cityDropdown");
    const cityDropdownBtn = cityDropdown?.querySelector(".city-dropdown-btn");
    const dropdownMenu = cityDropdown?.querySelector(".dropdown-menu");

    // Enhanced search input for mobile
    if (searchInput) {
      // Prevent zoom on iOS when focusing input
      searchInput.addEventListener("touchstart", function(e) {
        try {
          if (window.innerWidth <= 768) {
            // Add temporary font-size to prevent zoom
            this.style.fontSize = "16px";
          }
        } catch (error) {
          console.error("Search touchstart error:", error);
        }
      });

      // Enhanced search functionality
      searchInput.addEventListener("input", function(e) {
        try {
          const searchTerm = e.target.value.toLowerCase();
          // Add your search logic here
          console.log("Searching for:", searchTerm);
        } catch (error) {
          console.error("Search input error:", error);
        }
      });

      // Handle search button click
      const searchBtn = document.querySelector(".club-search-btn");
      if (searchBtn) {
        searchBtn.addEventListener("click", function(e) {
          try {
            e.preventDefault();
            const searchTerm = searchInput.value.toLowerCase();
            // Add your search logic here
            console.log("Search button clicked:", searchTerm);
          } catch (error) {
            console.error("Search button error:", error);
          }
        });
      }
    }

    // Enhanced city dropdown for mobile
    if (cityDropdownBtn && dropdownMenu) {
      // Improve touch handling for dropdown
      cityDropdownBtn.addEventListener("touchstart", function(e) {
        try {
          if (window.innerWidth <= 768) {
            // Add visual feedback
            this.style.transform = "scale(0.98)";
          }
        } catch (error) {
          console.error("Dropdown touchstart error:", error);
        }
      });

      cityDropdownBtn.addEventListener("touchend", function(e) {
        try {
          if (window.innerWidth <= 768) {
            // Reset visual feedback
            setTimeout(() => {
              this.style.transform = "scale(1)";
            }, 100);
          }
        } catch (error) {
          console.error("Dropdown touchend error:", error);
        }
      });

      // Enhanced dropdown item selection
      const dropdownItems = dropdownMenu.querySelectorAll(".dropdown-item");
      dropdownItems.forEach((item) => {
        item.addEventListener("touchstart", function(e) {
          try {
            if (window.innerWidth <= 768) {
              // Add visual feedback
              this.style.background = "#3a3f3b";
            }
          } catch (error) {
            console.error("Dropdown item touchstart error:", error);
          }
        });

        item.addEventListener("touchend", function(e) {
          try {
            if (window.innerWidth <= 768) {
              // Reset visual feedback
              setTimeout(() => {
                this.style.background = "";
              }, 100);
            }
          } catch (error) {
            console.error("Dropdown item touchend error:", error);
          }
        });
      });

      // Close dropdown when clicking outside on mobile
      document.addEventListener("touchstart", function(e) {
        try {
          if (window.innerWidth <= 768) {
            if (!cityDropdown.contains(e.target)) {
              // Close dropdown if it's open
              if (
                typeof bootstrap !== "undefined" &&
                bootstrap.Dropdown
              ) {
                const bsDropdown =
                  bootstrap.Dropdown.getInstance(cityDropdownBtn);
                if (bsDropdown) {
                  bsDropdown.hide();
                }
              }
            }
          }
        } catch (error) {
          console.error("Outside click error:", error);
        }
      });
    }

    // Prevent double-tap zoom on mobile
    if (window.innerWidth <= 768) {
      let lastTouchEnd = 0;
      document.addEventListener(
        "touchend",
        function(e) {
          try {
            const now = new Date().getTime();
            if (now - lastTouchEnd <= 300) {
              e.preventDefault();
            }
            lastTouchEnd = now;
          } catch (error) {
            console.error("Double-tap prevention error:", error);
          }
        },
        false
      );
    }
  }
</script>
@endsection