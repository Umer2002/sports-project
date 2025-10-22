@extends('layouts.player-new')

@section('title', (optional($player->sport)->name ?? 'Player') . ' Dashboard')

@vite('resources/js/player-dashboard.js')


@section('content')
    @php
        $resolvePlayback =
            $resolvePlayback ??
            function ($video) {
                $placeholder = asset('assets/player-dashboard/images/video-thumbnail.png');

                if (!$video) {
                    return ['type' => 'file', 'source' => '', 'thumbnail' => $placeholder];
                }

                $url = $video->playback_url ?? '';
                $type = 'file';
                $source = $url;
                $thumbnail = $placeholder;

                $rawThumbnail =
                    $video->thumbnail ??
                    ($video->thumbnail_url ??
                        ($video->poster ??
                            ($video->cover_image ?? ($video->image ?? ($video->mobile_app_image ?? null)))));

                if (is_string($rawThumbnail) && trim($rawThumbnail) !== '') {
                    if (Str::startsWith($rawThumbnail, ['http://', 'https://'])) {
                        $thumbnail = $rawThumbnail;
                    } else {
                        $normalizedThumb = ltrim($rawThumbnail, '/');
                        $normalizedThumb = preg_replace('#^storage/app/public/#', '', $normalizedThumb);
                        $candidates = array_unique(
                            array_filter([
                                $normalizedThumb,
                                Str::startsWith($normalizedThumb, 'public/storage/')
                                    ? Str::after($normalizedThumb, 'public/storage/')
                                    : null,
                                Str::startsWith($normalizedThumb, 'public/')
                                    ? Str::after($normalizedThumb, 'public/')
                                    : null,
                                Str::startsWith($normalizedThumb, 'storage/')
                                    ? Str::after($normalizedThumb, 'storage/')
                                    : null,
                            ]),
                        );

                        $resolvedUrl = null;

                        try {
                            $publicDisk = \Illuminate\Support\Facades\Storage::disk('public');
                            foreach ($candidates as $candidate) {
                                if ($publicDisk->exists($candidate)) {
                                    $resolvedUrl = $publicDisk->url($candidate);
                                    break;
                                }
                            }

                            if (!$resolvedUrl) {
                                $defaultDiskName = config('filesystems.default');
                                if ($defaultDiskName && $defaultDiskName !== 'public') {
                                    $defaultDisk = \Illuminate\Support\Facades\Storage::disk($defaultDiskName);
                                    foreach ($candidates as $candidate) {
                                        if ($defaultDisk->exists($candidate)) {
                                            $resolvedUrl = $defaultDisk->url($candidate);
                                            break;
                                        }
                                    }
                                }
                            }
                        } catch (\Throwable $e) {
                            $resolvedUrl = null;
                        }

                        if (!$resolvedUrl) {
                            foreach ($candidates as $candidate) {
                                if (file_exists(public_path($candidate))) {
                                    $resolvedUrl = asset($candidate);
                                    break;
                                }
                                if (file_exists(public_path('storage/' . $candidate))) {
                                    $resolvedUrl = asset('storage/' . $candidate);
                                    break;
                                }
                            }
                        }

                        if ($resolvedUrl) {
                            $thumbnail = $resolvedUrl;
                        }
                    }
                }

                if (!$url) {
                    return ['type' => 'file', 'source' => '', 'thumbnail' => $thumbnail];
                }

                $youtubeId = null;

                if (Str::contains($url, 'youtube.com/watch')) {
                    $queryString = parse_url($url, PHP_URL_QUERY);
                    parse_str($queryString ?? '', $queryParts);
                    if (!empty($queryParts['v'])) {
                        $youtubeId = $queryParts['v'];
                        $type = 'iframe';
                        $source = 'https://www.youtube.com/embed/' . $queryParts['v'];
                    }
                } elseif (Str::contains($url, 'youtu.be/')) {
                    $path = trim(parse_url($url, PHP_URL_PATH) ?? '', '/');
                    if ($path) {
                        $youtubeId = $path;
                        $type = 'iframe';
                        $source = 'https://www.youtube.com/embed/' . $path;
                    }
                }

                if ($youtubeId && $thumbnail === $placeholder) {
                    $thumbnail = 'https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg';
                }

                return ['type' => $type, 'source' => $source, 'thumbnail' => $thumbnail ?: $placeholder];
            };
    @endphp
    @if (!empty($paymentIncomplete) && $paymentIncomplete)
        <div class="alert alert-warning">
            Your membership payment is incomplete. Please complete payment to unlock the full player experience.
        </div>
    @endif
    @if (isset($hasPlayerProfile) && !$hasPlayerProfile)
        <div class="alert alert-info">
            You haven't finished creating a player profile yet. Complete it to personalise this dashboard.
        </div>
    @endif
    <!-- Profile Header Section -->
    <div class="same-card profile-header">
        <div class="left">
            @if ($player->sport && $player->sport->icon_path)
                <img src="{{ asset('storage/' . $player->sport->icon_path) }}" alt="{{ $player->sport->name }}"
                    style="width: 62px; height: 62px;">
            @else
                <img src="{{ asset('assets/player-dashboard/images/American-icon.svg') }}" alt="Sport Icon"
                    style="width: 62px; height: 62px;">
            @endif


            <div class="weather">
                @if ($weather)
                    <strong>{{ $weather['temp'] }}° <img
                            src="https://openweathermap.org/img/wn/{{ $weather['icon'] }}@2x.png" alt="Weather"
                            style="width: 30px; height: 30px;"></strong>
                    <h3>H:{{ $weather['temp_max'] }}° | L:{{ $weather['temp_min'] }}° <br><span>{{ $weather['city'] }},
                            {{ $weather['country'] }}</span></h3>
                @else
                    <strong>--° <img src="{{ asset('assets/player-dashboard/images/cloud-rain.svg') }}"
                            alt="Weather"></strong>
                    <h3>H:--° | L:--° <br><span>Weather Unavailable</span></h3>
                @endif
            </div>
        </div>
        <div class="right">
            <h2>Share Profile</h2>
            <!-- Public Profile Link -->

            @if ($hasPlayerProfile && $player->id)
                <a href="{{ route('public.player.profile', $player->id) }}" target="_blank" title="Public Profile">
                    <img src="{{ asset('assets/player-dashboard/images/icon10.svg') }}" alt="Public Profile">
                </a>
            @else
                <span class="text-muted" title="Complete your player profile to share it.">
                    <img src="{{ asset('assets/player-dashboard/images/icon10.svg') }}" alt="Public Profile"
                        style="opacity: 0.35; cursor: not-allowed;">
                </span>
            @endif
            @php
                $socials = is_array($player->social_links)
                    ? $player->social_links
                    : json_decode($player->social_links, true) ?? [];

                $socialIconMap = [
                    'facebook' => 'icon9.svg',
                    'twitter' => 'icon3.svg',
                    'instagram' => 'icon8.svg',
                    'tiktok' => 'icon7.svg',
                    'youtube' => 'icon1.svg',
                    'linkedin' => 'icon5.svg',
                    'snapchat' => 'icon4.svg',
                    'pinterest' => 'icon6.svg',
                    'reddit' => 'icon2.svg',
                ];
            @endphp

            @foreach ($socialIconMap as $platform => $icon)
                @if (isset($socials[$platform]) && !empty($socials[$platform]))
                    <a href="{{ $socials[$platform] }}" target="_blank">
                        <img src="{{ asset('assets/player-dashboard/images/' . $icon) }}" alt="{{ ucfirst($platform) }}">
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Player Profile Banner -->
    @php
        $sportBannerMap = [
            'volleyball' => 'volleyball_BG.png',
            'basketball' => 'basketball_BG.png',
            'baseball' => 'baseball_BG.png',
            'hockey' => 'hockey_BG.png',
            'swimming' => 'swimming_BG.png',
            'boxing' => 'boxing_BG.png',
            'wrestling' => 'wrestling_BG.png',
            'golf' => 'golf_BG.png',
            'gymnastics' => 'gymnastics_BG.png',
            'field hockey' => 'fieldhocket_BG.png',
        ];
        $sportName = optional($player->sport)->name;
        $bannerImage = $sportName
            ? $sportBannerMap[strtolower($sportName)] ?? 'screen-banner.png'
            : 'screen-banner.png';
    @endphp
    <div class="middle-banner middle-banner-12"
        style="background-image: url('{{ asset('assets/player-dashboard/banners/' . $bannerImage) }}') !important;">
        <div class="row">
            @php
                $sportLabel = optional($player->sport)->name ?? 'Sport';
                $clubLogo = $player->club && $player->club->logo
                    ? asset('storage/' . $player->club->logo)
                    : asset('assets/player-dashboard/images/Fullham-icon.png');
            @endphp
            <!-- Club & Sport -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3 col-xxl-3">
                <div class="banner-card-1">
                    <div class="club-logo-hex">
                        <div class="club-logo-inner">
                            <img src="{{ $clubLogo }}" alt="{{ $player->club->name ?? 'Club Logo' }}">
                        </div>
                    </div>
                    <h2 class="club-sport-label text-uppercase">{{ $sportLabel }}</h2>
                </div>
            </div>

            <!-- Player Info -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3 col-xxl-3">
                <div class="banner-card-2">
                    <h2 class="text-start">{{ ucfirst($player->name) }}</h2>
                    <div class="midfielder-main d-flex">
                        @if ($player->position)
                            <div class="Right-Foot d-flex">
                                <img src="{{ asset('assets/player-dashboard/images/forward-icon.png') }}">
                                <h6>{{ $player->position->position_name }}</h6>
                            </div>
                        @endif

                    </div>
                    <h5>INTL CAREER - {{ $player->debut ? date('Y', strtotime($player->debut)) : date('Y') }} -
                        {{ date('Y') }}</h5>
                    <div class="info-box text-start">
                        <div class="info-card">
                            <h6>BORN:</h6>
                            <h6>{{ $player->birthday ? date('d-m-Y', strtotime($player->birthday)) : 'N/A' }}</h6>
                        </div>
                        <div class="info-card">
                            <h6>AGE:</h6>
                            <h6>{{ $player->age ?? 'N/A' }} Years</h6>
                        </div>
                        <div class="info-card">
                            <h6>NATIONALITY:</h6>
                            <h6>{{ strtoupper($player->nationality ?? 'N/A') }}</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jersey Number -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3 col-xxl-3">
                <div class="banner-card-3">
                    <h2 class="text-center">{{ $player->jersey_no ?? '00' }}</h2>
                    @if ($player->photo)
                        <img src="{{ asset('storage/players/' . $player->photo) }}" alt="Player"
                            style="max-height: 200px; object-fit: contain;">
                    @endif
                </div>
            </div>

            <!-- Stats and Club Info -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3 col-xxl-3">
                <div class="banner-card-4">
                    <h6 class="stats">Stats</h6>
                    <div class="stats-box text-start">
                        @if (isset($statsWithValues) && count($statsWithValues) >= 3)
                            @for ($i = 0; $i < min(5, count($statsWithValues)); $i++)
                                <div class="stats-cards">
                                    <h6>{{ strtoupper($statsWithValues[$i]->name ?? 'STAT') }}</h6>
                                    <h6 class="text-warning fw-bold pt-2">{{ $statsWithValues[$i]->value ?? '0' }}</h6>
                                </div>
                            @endfor
                        @else
                            <div class="stats-cards">
                                <h6>GAMES</h6>
                                <h6 class="text-warning fw-bold pt-2">0</h6>
                            </div>
                            <div class="stats-cards">
                                <h6>GOALS</h6>
                                <h6 class="text-warning fw-bold pt-2">0</h6>
                            </div>
                            <div class="stats-cards">
                                <h6>ASSISTS</h6>
                                <h6 class="text-warning fw-bold pt-2">0</h6>
                            </div>
                        @endif
                    </div>

                    @if ($player->club)
                        <div class="real-madrid-main mt-4">
                            <div class="midfielder d-flex">
                                @if ($player->club && $player->club->logo)
                                    <img src="{{ asset('storage/' . $player->club->logo) }}"
                                        alt="{{ $player->club->name }}" style="width: 16px; height: 16px;">
                                @elseif($player->club)
                                    <img src="{{ asset('assets/player-dashboard/images/Fullham-icon.png') }}"
                                        alt="{{ $player->club->name }}" style="width: 16px; height: 16px;">
                                @else
                                    <img src="{{ asset('assets/player-dashboard/images/Fullham-icon.png') }}"
                                        alt="Club" style="width: 16px; height: 16px;">
                                @endif
                                <h6 class="d-flex ps-2">{{ $player->club->name }}</h6>
                            </div>
                        </div>
                    @endif

                    @php
                        // Get player rewards
                        $playerRewards = DB::table('rewards')
                            ->join('player_rewards', 'player_rewards.reward_id', '=', 'rewards.id')
                            ->select('rewards.name')
                            ->where('player_rewards.user_id', $player->user_id)
                            ->limit(2)
                            ->get();
                    @endphp
                    @if ($playerRewards->count() > 0)
                        <h5>AWARDS - {{ $playerRewards->pluck('name')->join(', ') }}</h5>
                    @endif

                    @if ($player->jersey_no)
                        <h5>JERSEY NUMBER - {{ $player->jersey_no }}</h5>
                    @endif

                    <div class="info-box text-start mt-4">
                        <div class="info-card border-0 pb-2">
                            <h6>HEIGHT</h6>
                            <h6>{{ $player->height ?? 'N/A' }}</h6>
                        </div>
                        <div class="info-card border-0 pb-2">
                            <h6>WEIGHT</h6>
                            <h6>{{ $player->weight ?? 'N/A' }}</h6>
                        </div>
                        <div class="info-card border-0 pb-2">
                            <h6>DEBUT</h6>
                            <h6>{{ $player->debut ? date('Y', strtotime($player->debut)) : 'N/A' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="four-card">
        <div class="row">
            @if (isset($statsWithValues) && count($statsWithValues) >= 4)
                @for ($i = 0; $i < min(4, count($statsWithValues)); $i++)
                    <div class="col-lg-3 col-md-6">
                        <div class="same-card boxone-text">
                            <h2>{{ ucfirst($statsWithValues[$i]->name ?? 'Stat') }}</h2>
                            <img src="{{ asset('assets/player-dashboard/images/box' . ($i + 1) . '.png') }}"
                                alt="Chart">
                            <div class="increase-card">
                                <div class="d-flex justify-content-between">
                                    <span>0%</span>
                                    <span>100%</span>
                                </div>
                                <p>{{ rand(10, 50) }}% Increase in 28 Days</p>
                            </div>
                        </div>
                    </div>
                @endfor
            @else
                <!-- Default stats if not enough player stats -->
                <div class="col-lg-3 col-md-6">
                    <div class="same-card boxone-text">
                        <h2>Performance</h2>
                        <img src="{{ asset('assets/player-dashboard/images/box1.png') }}" alt="Chart">
                        <div class="increase-card">
                            <div class="d-flex justify-content-between">
                                <span>0%</span>
                                <span>100%</span>
                            </div>
                            <p>10% Increase in 28 Days</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="same-card boxone-text">
                        <h2>Skills</h2>
                        <img src="{{ asset('assets/player-dashboard/images/box2.png') }}" alt="Chart">
                        <div class="increase-card">
                            <div class="d-flex justify-content-between">
                                <span>0%</span>
                                <span>100%</span>
                            </div>
                            <p>26% Increase in 28 Days</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="same-card boxone-text">
                        <h2>Speed</h2>
                        <img src="{{ asset('assets/player-dashboard/images/box3.png') }}" alt="Chart">
                        <div class="increase-card">
                            <div class="d-flex justify-content-between">
                                <span>0%</span>
                                <span>100%</span>
                            </div>
                            <p>14% Increase in 28 Days</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="same-card boxone-text">
                        <h2>Stamina</h2>
                        <img src="{{ asset('assets/player-dashboard/images/box4.png') }}" alt="Chart">
                        <div class="increase-card">
                            <div class="d-flex justify-content-between">
                                <span>0%</span>
                                <span>100%</span>
                            </div>
                            <p>50% Increase in 28 Days</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Awards Section -->
    <div class="same-card awards-card">
        <div class="awards-headding">
            <h2>Awards</h2>
            <i class="fa-solid fa-ellipsis"></i>
        </div>
        <ul class="hidden-list" id="awardList">
            @php
                // Get all rewards and player rewards
                $playerRewards = DB::table('rewards')
                    ->join('player_rewards', 'player_rewards.reward_id', '=', 'rewards.id')
                    ->select('rewards.id', 'rewards.name', 'rewards.image', 'rewards.achievement')
                    ->where('player_rewards.user_id', $player->user_id)
                    ->get();
                $playerRewardIds = $playerRewards->pluck('id')->toArray();

                // Show first few rewards (mix of earned and locked)
                $displayRewards = $allRewards->take(12);
            @endphp

            @foreach ($displayRewards as $index => $reward)
                @php $isPlayerReward = in_array($reward->id, $playerRewardIds); @endphp
                <li>
                    <button class="award-btn {{ $isPlayerReward ? 'award-btn-a' : '' }}"
                        onclick="openModalFunction('{{ asset($reward->image) }}', '{{ $reward->name }}', '{{ $reward->achievement ?? 'Great achievement!' }}')">
                        @if ($isPlayerReward)
                            <img src="{{ asset($reward->image) }}" alt="{{ $reward->name }}">
                        @else
                            <img src="{{ asset('assets/player-dashboard/images/lock.png') }}" alt="Locked Award">
                        @endif
                    </button>
                </li>
            @endforeach
        </ul>
        <button id="showMoreBtn" class="show-more-btn">Show More</button>

        <!-- Award Modal -->
        <div id="myModal-a" class="modal-a">
            <div class="modal-content-a">
                <div class="modal-headers">
                    <span class="close-a">&times;</span>
                    <h2>Award Unlocked</h2>
                    <p id="modal-tag">Award Name</p>
                </div>
                <div class="alldata-middle">
                    <div class="row g-4">
                        <div class="col-lg-4 d-flex flex-column">
                            <div class="award-img-block">
                                <img src="" alt="Award" class="award-img" id="modal-img">
                            </div>
                        </div>
                        <div class="col-lg-8 d-flex flex-column">
                            <div class="section-title">DESCRIPTION</div>
                            <div class="desc-block" id="desc-para">
                                Award description will appear here.
                            </div>
                            <div class="mb-3">
                                <div class="section-title">REQUIREMENTS</div>
                                <div class="key-passes-text">• Complete profile • Join a club • Show dedication</div>
                            </div>
                            <div class="mb-3">
                                <div class="section-title">REWARDS</div>
                                <div class="key-passes-text">• +250 XP • Achievement badge • Leaderboard highlight</div>
                            </div>
                            <div class="flex-wrap gap-3 mb-3 buttons3">
                                <button class="btn-share">Share</button>
                                <button class="btn-ack">Acknowledge Award</button>
                                <button class="btn-clos">Close</button>
                            </div>
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <div class="social-icon"><img
                                        src="{{ asset('assets/player-dashboard/images/prisma.png') }}" width="100%"
                                        height="100%" /></div>
                                <div class="social-icon"><img
                                        src="{{ asset('assets/player-dashboard/images/facebook.png') }}" width="100%"
                                        height="100%" /></div>
                                <div class="social-icon"><img
                                        src="{{ asset('assets/player-dashboard/images/insta.png') }}" width="100%"
                                        height="100%" /></div>
                                <div class="social-icon"><img
                                        src="{{ asset('assets/player-dashboard/images/tiktok.png') }}" width="100%"
                                        height="100%" /></div>
                                <div class="social-icon"><img
                                        src="{{ asset('assets/player-dashboard/images/linkedin.png') }}" width="100%"
                                        height="100%" /></div>
                                <div class="social-icon"><img src="{{ asset('assets/player-dashboard/images/x.png') }}"
                                        width="100%" height="100%" /></div>
                                <div class="social-icon"><img
                                        src="{{ asset('assets/player-dashboard/images/youtube.png') }}" width="100%"
                                        height="100%" /></div>
                            </div>
                        </div>
                        <div class="bottom-bar">
                            <div class="other-awards mt-auto">
                                <div class="section-title">Other Awards</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @for ($i = 0; $i < 8; $i++)
                                        <div
                                            class="award-circle {{ $i < $playerRewards->count() ? '' : 'award-circle-a' }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            <div class="mt-auto text-end">
                                <button class="footer-btn">View All Awards</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Two Column Layout -->
    <div class="row middle-twopart">
        <div class="col-lg-8 col-md-12 part-one">
            <!-- Calendar -->
            <div class="same-card calendar-card">
                <div class="calendar-container">
                    <div class="month-main d-flex justify-content-between">
                        <div class="calendar-header">
                            <h2 id="monthYear">{{ date('F Y') }}</h2>
                            <button onclick="prevMonth()">❮</button>
                            <button onclick="nextMonth()">❯</button>
                        </div>
                        <div class="tabs">
                            <div class="tab active">Month</div>
                            <div class="tab">Week</div>
                            <div class="tab">Day</div>
                            <div class="tab">List</div>
                        </div>
                    </div>
                    <div class="weekdays">
                        <div>Mon</div>
                        <div>Tue</div>
                        <div>Wed</div>
                        <div>Thu</div>
                        <div>Fri</div>
                        <div>Sat</div>
                        <div>Sun</div>
                    </div>
                    <div class="calendar" id="calendarDays">
                        <!-- Calendar days will be injected by JS -->
                    </div>
                </div>
            </div>

            <!-- Calendar Event Modal -->
            {{-- Tournament Detail Modal --}}
            <div class="modal fade" id="staticBackdrop-one" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="tmTitle" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content modal-content-one">
                        <div class="modal-header modal-header-one">
                            <div class="modal-headding">
                                <h1 class="modal-title fs-5" id="tmTitle">Tournament Title</h1>
                                <p id="tmWhen">—</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-8">
                                    <div class="big-panel">
                                        <strong class="d-block mb-1 text-muted-a small">Location &amp; Map</strong>
                                        <div id="tmVenue" class="mb-1">—</div>
                                        <div class="map-placeholder mt-2" id="tmMap" style="min-height:260px;">
                                            <!-- Google Maps iframe injected here -->
                                        </div>

                                        <div class="filters mt-3">
                                            <button class="filter-btn-a active" type="button">Hotels</button>
                                            <button class="filter-btn-a" type="button">Distance</button>
                                            <button class="filter-btn-a" type="button">Price</button>
                                            <button class="filter-btn-a" type="button">Rating</button>
                                        </div>

                                        <div id="tmHotels" class="d-flex gap-2 flex-wrap mt-2">
                                            <!-- (optional) hotel cards injected -->
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="panel-sm">
                                        <small class="d-block mb-2 text-muted-b fw-semibold">Attending?</small>
                                        <div class="d-flex gap-2">
                                            <button class="attend-btn btn-yes" type="button">Yes</button>
                                            <button class="attend-btn btn-maybe" type="button">Maybe</button>
                                            <button class="attend-btn btn-no" type="button">No</button>
                                        </div>
                                    </div>

                                    <div class="panel-sm">
                                    <small class="d-block mb-2 text-muted-b fw-semibold">Carpool /
                                            Transportation</small>
                                    <div class="d-flex gap-2 mb-2">
                                            <button class="carpool-btn" type="button">I can drive</button>
                                            <button class="carpool-btn" type="button">Need a ride</button>
                                    </div>
                                    <small class="d-block text-muted-c">Seats available</small>
                                    <input type="number" id="tmSeats" value="3" class="seat-input mt-1">
                                </div>

                                    <div class="panel-sm panel-hig">
                                        <small class="d-block mb-2 text-muted-b fw-semibold">Coach Note</small>
                                        <div id="tmCoachNote" class="arrive-text">—</div>
                                    </div>

                                    <div class="panel-sm">
                                        <small class="d-block mb-2 text-muted-b fw-semibold">Event Images</small>
                                        <div id="tmImages" class="tm-image-previews empty">
                                            <div class="text-muted small">No images uploaded yet.</div>
                                        </div>
                                        <div class="mb-2 text-muted drag-text">Drag &amp; drop or click to upload</div>
                                        <div class="upload-btn-one">
                                            <button class="upload-btn" id="tmUploadBtn" type="button">Upload</button>
                                            <input type="file" id="tmUploadInput" accept="image/*" class="d-none">
                                        </div>
                                    </div>

                                    <div class="panel-sm panel-hig weather-main">
                                        <small class="d-block mb-2 text-muted-b fw-semibold">Weather</small>
                                        <div id="tmWeather" class="drag-text">—</div>
                                        <img id="tmWeatherIcon" src="" alt="icon"
                                            style="width:40px;height:40px;display:none;">
                                    </div>

                                    <div class="panel-sm">
                                        <small class="d-block mb-2 text-muted-b fw-semibold">Venue &amp; Links</small>
                                        <div id="tmVenueName" class="drag-text">—</div>
                                        <div class="d-flex gap-2 mt-2">
                                            <button id="tmSaveBtn" class="save-btn save-btn-a"
                                                type="button">Save</button>
                                            <button id="tmChatBtn" class="chat-btn" type="button">Open Team
                                                Chat</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer-custom mb-3">
                                <button id="tmAddCalBtn" class="footer-btn1" type="button">Add to Calendar</button>
                                <button id="tmShareBtn" class="footer-btn1" type="button">Share</button>
                                <a id="tmMapBtn" class="footer-btn1 footer-btn-primary" href="#" target="_blank"
                                    rel="noopener">Open Map</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            @php
                $recentVideosCollection = ($recentVideos ?? collect())->values();
                $featuredPlayerVideo = $recentVideosCollection->first();
                $additionalPlayerVideos = $recentVideosCollection->slice(1);
            @endphp

            <div class="same-card player-media-card">
                <div class="awards-headding d-flex justify-content-between align-items-center">
                    <h2>Player Spotlight</h2>
                    <a href="{{ route('player.videos.explore') }}" class="btn btn-sm btn-outline-light">View All</a>
                </div>
                 <div class="video-card">
                @if ($featuredVideo)
                    <div class="video-thumb">
                        <a href="{{ $featuredVideo['show_url'] }}" class="d-block">
                            <img src="{{ $featuredVideo['thumbnail'] }}" alt="{{ $featuredVideo['title'] }}">
                            <div class="overlay">
                                <span class="play-btn" aria-hidden="true">&#9658;</span>
                            </div>
                            <span class="visually-hidden">Watch {{ $featuredVideo['title'] }}</span>
                        </a>
                    </div>

                    <div class="video-info-bar">
                        <div class="text-muted small">
                            <span class="badge bg-primary bg-opacity-25 text-white text-uppercase small me-2">{{ $featuredVideo['duration'] }}</span>
                            Uploaded {{ $featuredVideo['uploaded_human'] }}
                        </div>
                        <a href="{{ $featuredVideo['show_url'] }}" class="btn btn-sm btn-outline-light">Watch now</a>
                    </div>

                    <div class="video-meta">
                        <h4 class="mb-1">{{ $featuredVideo['title'] }}</h4>
                        <div class="text-muted small mb-2">By {{ $featuredVideo['author'] }}</div>
                        <p>{{ $featuredVideo['description'] }}</p>
                    </div>
                @else
                    <div class="video-empty">
                        <p class="mb-2">No club videos yet.</p>
                        <a href="{{ route('player.videos.explore') }}" class="btn btn-sm btn-primary">Explore videos</a>
                    </div>
                @endif

            </div>


            </div>

            @php
                $trainingVideo = $videoAds->first();
                $trainingEmbed = $trainingVideo ? $resolvePlayback($trainingVideo) : null;
                $imageAds = $ads->where('type', 'image')->take(4);
            @endphp

            <div class="same-card training-spotlight-card">
                <div class="awards-headding d-flex justify-content-between align-items-center">
                    <h2>Training Spotlight</h2>
                    <span class="text-muted small">Clubs &amp; Coaches</span>
                </div>
                <div class="row g-4 align-items-stretch">
                    @if ($trainingEmbed && $trainingEmbed['source'])
                        <div class="col-lg-6">
                            <div class="training-video">
                                @if ($trainingEmbed['type'] === 'iframe')
                                    <iframe src="{{ $trainingEmbed['source'] }}" title="Training promo" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                                @else
                                    <video controls preload="metadata">
                                        <source src="{{ $trainingEmbed['source'] }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif
                                <p class="mt-3 mb-0 text-muted small">{{ Str::limit($trainingVideo->description, 120) }}
                                </p>
                            </div>
                        </div>
                    @endif
                    <div class="col-lg-{{ $trainingEmbed && $trainingEmbed['source'] ? '6' : '12' }}">
                        <div class="training-spotlight-grid">
                            @forelse($imageAds as $ad)
                                @php
                                    $mediaPath =
                                        $ad->media && file_exists(public_path('storage/' . $ad->media))
                                            ? asset('storage/' . $ad->media)
                                            : asset('assets/player-dashboard/images/trainer-post1.png');
                                @endphp
                                <a href="{{ $ad->link ?? '#' }}" target="_blank" class="training-card" rel="noopener">
                                    <img src="{{ $mediaPath }}" alt="{{ $ad->title ?? 'Training Ad' }}">
                                    <span>{{ $ad->title ?? 'Featured Ad' }}</span>
                                </a>
                            @empty
                                <div class="text-muted small">No training promotions available right now.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="same-card tournament-buttons">
                <ul class="tournament-btn">
                    <li>
                        <a href="{{ route('player.tournaments.directory') }}">
                            <button class="tournament-search-btn" type="button">
                                Tournmanent Search <i class="fa-solid fa-caret-right"></i>
                            </button>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('player.invite.club.create') }}">
                            <button class="tournament-search-btn refer-club-bg" type="button">
                                Refer A Club Earn $1000 <i class="fa-solid fa-caret-right"></i>
                            </button>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('player.invite.create') }}">
                            <button class="tournament-search-btn refer-player-bg" type="button">
                                Refer A Player $10 <i class="fa-solid fa-caret-right"></i>
                            </button>
                        </a>
                    </li>
                    <li>
                        <button class="tournament-search-btn request-access-bg" type="button" data-bs-toggle="modal"
                            data-bs-target="#requestModal">
                            Volunteer<br> To Manage This Team <i class="fa-solid fa-caret-right"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4 col-md-12 part-one">
            <!-- Chat Section -->
            @include('partials.dashboard_chat_new')

            <!-- Task Assignment -->
            <div class="same-card assign-task-card">
                <div class="awards-headding">
                    <h2>Assign Task</h2>
                    <div class="dropdown">
                        <button class="filter-btn dropdown-toggle" type="button" id="dropdownMenuButton1"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('assets/player-dashboard/images/filter-icon.svg') }}" alt="Filter Icon">
                            Filter
                            <img class="buttom-icon" src="{{ asset('assets/player-dashboard/images/buttom-icon.png') }}"
                                alt="Filter Icon">
                        </button>
                        <ul class="dropdown-menu filter-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="#">All Tasks</a></li>
                            <li><a class="dropdown-item" href="#">Completed</a></li>
                            <li><a class="dropdown-item" href="#">Pending</a></li>
                        </ul>
                    </div>
                </div>
                <div class="select-player-btn">
                    <select id="player" name="player">
                        <option value="self">Myself</option>
                        <option value="coach">Coach</option>
                        <option value="team">Team</option>
                    </select>
                    <button class="assign-task">Assign New Task</button>
                </div>
                <div class="assign-list">
                    <table class="sem-table">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Status</th>
                                <th>Assigned By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tasks as $task)
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <img src="https://i.pravatar.cc/35?u={{ $task->id }}" alt="User">
                                            {{ $task->title }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match ($task->status) {
                                                'completed' => 'done',
                                                'in_progress' => 'progress',
                                                default => 'todo',
                                            };
                                        @endphp
                                        <span
                                            class="status {{ $statusClass }}">{{ Str::headline($task->status) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $task->user?->name ?? 'System' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No tasks assigned yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Help Chat -->
            <div class="same-card help-chat-card">
                <button class="help-chat-btn" type="button" data-bs-toggle="modal"
                    data-bs-target="#playerHelpChatModal">
                    <img src="{{ asset('assets/player-dashboard/images/HelpIcon.svg') }}" alt="Help Icon"> Help Chat
                </button>
                <button class="help-chat-btn report-bug-btn" data-bs-toggle="modal"
                    data-bs-target="#playerBugReportModal">
                    <img src="{{ asset('assets/player-dashboard/images/BugIcon.svg') }}" alt="Help Icon"> Report a Bug
                </button>
            </div>
        </div>
    </div>

    @include('players.partials.help-chat-modal')
    @include('players.partials.bug-report-modal')
    <!-- Include existing modals and functionality -->
    @include('players.partials.pickup-modal')
@endsection

@push('scripts')
   {{-- Pass all server-side data here --}}
<script id="dashboard-data" type="application/json">
{!! json_encode([
    'csrf'       => csrf_token(),
    'routes'     => [
        'item'       => url('/dashboard/calendar/item/__TYPE__/__ID__'),
        'preference' => url('/dashboard/calendar/preference/__TYPE__/__ID__'),
        'upload'     => url('/dashboard/calendar/upload/__TYPE__/__ID__'),
        'ics'        => url('/dashboard/calendar/ics/__TYPE__/__ID__'),
        'teamPlayer' => url('/player/teams/__ID__/chat'),
        'teamClub'   => url('/club/teams/__ID__/chat'),
    ],
    'roles'      => $playerRoleNames ?? [],
    // Build the three buckets the JS expects:
    'pickupGames'=> $pickupGames->map(function($g) use($user){
        return [
            'id'          => $g->id,
            'date'        => \Carbon\Carbon::parse($g->game_datetime)->format('Y-m-d'),
            'time'        => \Carbon\Carbon::parse($g->game_datetime)->format('H:i'),
            'text'        => 'Pickup: '.$g->sport->name,
            'type'        => 'pickup',
            'color'       => $g->participants->contains('id', $user->id) ? 'green' : 'blue',
            'location'    => $g->location ?? '',
            'description' => 'Join or leave this pickup game.',
            'url'         => url("/player/pickup-games/{$g->id}"),
        ];
    })->values(),
    'events'     => $events->map(function($e){
        $date = $e->event_date ?? ($e->start ?? now());
        return [
            'id'           => $e->id,
            'resource_type'=> 'event',
            'resource_id'  => $e->id,
            'date'         => \Carbon\Carbon::parse($date)->format('Y-m-d'),
            'time'         => $e->event_time ? \Carbon\Carbon::parse($e->event_time)->format('H:i') : ($e->start ? \Carbon\Carbon::parse($e->start)->format('H:i') : ''),
            'text'         => $e->title ?? 'Event',
            'type'         => $e->type ?? 'event',
            'color'        => $e->type === 'match' ? 'red' : ($e->type === 'training' ? 'blue' : 'green'),
            'location'     => $e->location ?? '',
            'description'  => \Illuminate\Support\Str::limit($e->description ?? '', 160),
            'url'          => '#',
        ];
    })->values(),
    'tournaments'=> $tournaments->map(function($t){
        $v       = $t->venue;
        $locLine = collect([
            $t->location,
            optional($v)->address,
            optional($t->city)->name,
            optional($t->state)->name,
            optional($t->country)->name,
        ])->filter()->implode(', ');
        $start   = optional($t->start_date);
        return [
            'id'           => $t->id,
            'resource_type'=> 'tournament',
            'resource_id'  => $t->id,
            'date'         => ($start?->format('Y-m-d')) ?? now()->format('Y-m-d'),
            'time'         => ($start?->format('H:i')) ?? '',
            'text'         => 'Tournament: '.$t->name,
            'type'         => 'tournament',
            'color'        => 'orange',
            'location'     => $locLine,
            'venue_name'   => optional($v)->name,
            'lat'          => optional($v)->lat ?? optional($v)->latitude,
            'lng'          => optional($v)->lng ?? optional($v)->longitude,
            'description'  => \Illuminate\Support\Str::limit($t->description ?? '', 160),
            'url'          => url('/tournaments/'.$t->id),
        ];
    })->values(),
]) !!}
</script>

{{-- Include your external JS (compiled/mix/vite or plain asset) --}}
<script src="{{ asset('assets/js/player-dashboard.js') }}" defer></script>

@endpush
