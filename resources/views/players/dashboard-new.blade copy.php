@extends('layouts.player-new')

@section('title', (optional($player->sport)->name ?? 'Player') . ' Dashboard')

@push('styles')
    <style>
        .video-card {
            border-radius: 10px;
            overflow: hidden;
            background: #000;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
            margin-top: 20px;
        }

        .video-thumb {
            position: relative;
            width: 100%;
            height: 420px;
        }

        .video-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .play-btn {
            background: rgba(0, 0, 0, 0.6);
            border: 2px solid #fff;
            color: #fff;
            border-radius: 50%;
            font-size: 30px;
            width: 60px;
            height: 60px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .four-card .boxone-text {
            background: linear-gradient(180deg, #ffffff 0%, #f4f7fb 100%);
            border-radius: 22px;
            padding: 24px 22px 28px;
            box-shadow: 0 22px 44px rgba(15, 23, 42, 0.08);
            display: flex;
            flex-direction: column;
            gap: 18px;
            min-height: 260px;
        }

        .four-card .boxone-text h2 {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0;
        }

        .stat-progress {
            position: relative;
            width: 100%;
            max-width: 190px;
            align-self: center;
        }

        .stat-progress__svg {
            width: 100%;
            height: auto;
            display: block;
        }

        .stat-progress__value {
            position: absolute;
            top: 52%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 78px;
            height: 78px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--stat-color-end, #0f172a);
            border: 5px solid transparent;
            background-image:
                linear-gradient(#ffffff, #ffffff),
                linear-gradient(135deg, var(--stat-color-start, #38bdf8), var(--stat-color-end, #6366f1));
            background-origin: border-box;
            background-clip: content-box, border-box;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.12);
            z-index: 2;
        }

        .stat-progress__value span {
            color: inherit;
            text-shadow: 0 2px 8px rgba(15, 23, 42, 0.18);
            display: block;
        }

        .four-card .boxone-text .increase-card {
            position: static;
            width: 100%;
            margin-top: 0;
            background: #eef1f7;
            border-radius: 18px;
            padding: 14px 18px;
            box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.12);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .four-card .boxone-text .increase-card .d-flex span {
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
        }

        .four-card .boxone-text .increase-card p {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            padding-top: 0;
            margin-bottom: 0;
        }

        .video-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 10px;
            background: #111;
        }

        .progress-bar {
            flex: 1;
            height: 5px;
            background: #444;
            border-radius: 5px;
            overflow: hidden;
            margin-right: 10px;
        }

        .progress {
            width: 60%;
            height: 100%;
            background: #f97316;
        }

        .video-info-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: #0f172a;
            border-top: 1px solid rgba(148, 163, 184, 0.2);
        }

        .video-info-bar .btn {
            border-radius: 999px;
            padding: 6px 18px;
            font-weight: 600;
        }

        .video-meta {
            padding: 18px 20px 8px;
            background: #0b1222;
            color: #e2e8f0;
        }

        .video-meta h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .video-meta p {
            margin-bottom: 0;
            color: rgba(226, 232, 240, 0.75);
        }

        .video-playlist {
            position: relative;
            padding: 24px 64px 32px;
            background: #060b18;
            border-top: 1px solid rgba(148, 163, 184, 0.18);
        }

        .video-track {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            overflow-y: hidden;
            scroll-behavior: smooth;
            padding-bottom: 8px;
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
        }

        .video-track::-webkit-scrollbar {
            height: 6px;
        }

        .video-track::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.4);
            border-radius: 999px;
        }

        .video-track::-webkit-scrollbar-track {
            background: transparent;
        }

        .playlist-item {
            width: 240px;
            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 12px;
            padding: 12px;
            color: #f8fafc;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .playlist-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.35);
            text-decoration: none;
        }

        .playlist-item img {
            width: 100%;
            height: 150px;
            border-radius: 10px;
            object-fit: cover;
        }

        .playlist-item > div {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .playlist-title {
            font-weight: 600;
            font-size: 15px;
            color: #f8fafc;
        }

        .playlist-meta {
            font-size: 12px;
            color: rgba(226, 232, 240, 0.7);
        }

        .video-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(148, 163, 184, 0.25);
            color: #f8fafc;
            display: none;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            z-index: 2;
        }

        .video-slider-ready .video-nav {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .video-nav:hover {
            background: rgba(249, 115, 22, 0.95);
            border-color: rgba(249, 115, 22, 0.95);
            color: #0f172a;
        }

        .video-nav:disabled {
            opacity: 0.35;
            cursor: not-allowed;
            background: rgba(15, 23, 42, 0.5);
            border-color: rgba(148, 163, 184, 0.15);
        }

        .video-prev {
            left: 18px;
        }

        .video-next {
            right: 18px;
        }

        .video-empty {
            padding: 24px;
            text-align: center;
            background: #0b1222;
            color: #94a3b8;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        .training-spotlight-card iframe,
        .training-spotlight-card video {
            width: 100%;
            border-radius: 16px;
            min-height: 220px;
            background: #0b0e1f;
        }

        .training-spotlight-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
        }

        .training-spotlight-grid .training-card {
            border-radius: 12px;
            overflow: hidden;
            display: block;
            position: relative;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .training-spotlight-grid .training-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .training-spotlight-grid .training-card span {
            position: absolute;
            left: 10px;
            bottom: 10px;
            font-size: 0.75rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            background: rgba(0, 0, 0, 0.55);
            padding: 4px 8px;
            border-radius: 999px;
        }

        @media (max-width: 1199px) {
            .video-thumb {
                height: 360px;
            }

            .playlist-item {
                flex-basis: 220px;
            }
        }

        @media (max-width: 992px) {
            .video-thumb {
                height: 320px;
            }

            .video-playlist {
                padding: 20px 56px 28px;
            }

            .playlist-item img {
                height: 140px;
            }
        }

        @media (max-width: 768px) {
            .video-thumb {
                height: 280px;
            }

            .video-playlist {
                padding: 20px 48px 28px;
            }
        }

        @media (max-width: 576px) {
            .video-thumb {
                height: 240px;
            }

            .video-playlist {
                padding: 16px 40px 24px;
            }

            .playlist-item img {
                height: 160px;
            }
        }
    </style>
@endpush

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
            <!-- Sport Icon -->
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-3 col-xxl-3">
                <div class="banner-card-1">
                    @if ($player->club && $player->club->logo)
                        <img src="{{ asset('storage/' . $player->club->logo) }}" alt="{{ $player->club->name }}">
                    @endif
                    <h2 class="text-center pt-3">{{ $player->club->name ?? 'Club' }}</h2>
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
            @php
                $statPalette = [
                    ['start' => '#34d399', 'end' => '#22d3ee'],
                    ['start' => '#f97316', 'end' => '#facc15'],
                    ['start' => '#3b82f6', 'end' => '#60a5fa'],
                    ['start' => '#8b5cf6', 'end' => '#ec4899'],
                ];
                $arcRadius = 40;
                $arcLength = pi() * $arcRadius;
            @endphp
            @if (isset($statsWithValues) && count($statsWithValues) >= 4)
                @for ($i = 0; $i < min(4, count($statsWithValues)); $i++)
                    @php
                        $stat = $statsWithValues[$i] ?? null;
                        $statName = ucfirst($stat['name'] ?? 'Stat');
                        $rawValue = $stat['value'] ?? 0;
                        $numericValue = is_numeric($rawValue)
                            ? (float) $rawValue
                            : (float) preg_replace('/[^0-9.]/', '', (string) $rawValue);
                        if (! is_finite($numericValue)) {
                            $numericValue = 0;
                        }
                        $progress = max(0, min(100, $numericValue));
                        $trendValue = isset($stat['trend']) && is_numeric($stat['trend'])
                            ? round($stat['trend'])
                            : max(5, min(95, $progress > 0 ? round($progress * 0.8) : 12));
                        $dash = $progress > 0 ? ($progress / 100) * $arcLength : 0;
                        $gap = max($arcLength - $dash, 0);
                        $colors = $statPalette[$i % count($statPalette)];
                        $gradientId = 'statGradient' . $i;
                        $displayValue = is_numeric($rawValue) ? round($numericValue) : $rawValue;
                    @endphp
                    <div class="col-lg-3 col-md-6">
                        <div class="same-card boxone-text">
                            <h2>{{ $statName }}</h2>
                            <div class="stat-progress">
                                <svg viewBox="0 0 100 60" class="stat-progress__svg" role="img" aria-hidden="true">
                                    <defs>
                                        <linearGradient id="{{ $gradientId }}" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="{{ $colors['start'] }}" />
                                            <stop offset="100%" stop-color="{{ $colors['end'] }}" />
                                        </linearGradient>
                                    </defs>
                                    <path d="M10 50 A40 40 0 0 1 90 50" stroke="#e2e8f0" stroke-width="10" fill="none"
                                        stroke-linecap="round" />
                                    <path d="M10 50 A40 40 0 0 1 90 50" stroke="url(#{{ $gradientId }})" stroke-width="10"
                                        fill="none" stroke-linecap="round"
                                        stroke-dasharray="{{ sprintf('%.2f %.2f', $dash, $gap) }}" />
                                </svg>
                                <div class="stat-progress__value"
                                    style="--stat-color-start: {{ $colors['start'] }}; --stat-color-end: {{ $colors['end'] }};">
                                    <span>{{ $displayValue }}</span>
                                </div>
                            </div>
                            <div class="increase-card">
                                <div class="d-flex justify-content-between">
                                    <span>0%</span>
                                    <span>100%</span>
                                </div>
                                <p>{{ $trendValue }}% Increase in 28 Days</p>
                            </div>
                        </div>
                    </div>
                @endfor
            @else
                @php
                    $defaultStats = [
                        ['name' => 'Performance', 'value' => 45, 'trend' => 10],
                        ['name' => 'Skills', 'value' => 69, 'trend' => 26],
                        ['name' => 'Speed', 'value' => 84, 'trend' => 14],
                        ['name' => 'Stamina', 'value' => 56, 'trend' => 18],
                    ];
                @endphp
                @foreach ($defaultStats as $index => $stat)
                    @php
                        $colors = $statPalette[$index % count($statPalette)];
                        $gradientId = 'statDefaultGradient' . $index;
                        $progress = max(0, min(100, $stat['value']));
                        $dash = ($progress / 100) * $arcLength;
                        $gap = max($arcLength - $dash, 0);
                    @endphp
                    <div class="col-lg-3 col-md-6">
                        <div class="same-card boxone-text">
                            <h2>{{ $stat['name'] }}</h2>
                            <div class="stat-progress">
                                <svg viewBox="0 0 100 60" class="stat-progress__svg" role="img" aria-hidden="true">
                                    <defs>
                                        <linearGradient id="{{ $gradientId }}" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="{{ $colors['start'] }}" />
                                            <stop offset="100%" stop-color="{{ $colors['end'] }}" />
                                        </linearGradient>
                                    </defs>
                                    <path d="M10 50 A40 40 0 0 1 90 50" stroke="#e2e8f0" stroke-width="10" fill="none"
                                        stroke-linecap="round" />
                                    <path d="M10 50 A40 40 0 0 1 90 50" stroke="url(#{{ $gradientId }})" stroke-width="10"
                                        fill="none" stroke-linecap="round"
                                        stroke-dasharray="{{ sprintf('%.2f %.2f', $dash, $gap) }}" />
                                </svg>
                                <div class="stat-progress__value"
                                    style="--stat-color-start: {{ $colors['start'] }}; --stat-color-end: {{ $colors['end'] }};">
                                    <span>{{ $stat['value'] }}</span>
                                </div>
                            </div>
                            <div class="increase-card">
                                <div class="d-flex justify-content-between">
                                    <span>0%</span>
                                    <span>100%</span>
                                </div>
                                <p>{{ $stat['trend'] }}% Increase in 28 Days</p>
                            </div>
                        </div>
                    </div>
                @endforeach
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

    <!-- Invite Tracking Section -->
    <style>
        .table-compact {
            background: transparent;
            border: none;
        }

        .table-compact thead th {
            background: #1a1a2e;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.6rem 0.75rem;
            border: none;
            border-bottom: 2px solid #007bff;
        }

        .table-compact tbody tr {
            background: #16213e;
            border: none;
            transition: all 0.2s ease;
        }

        .table-compact tbody tr:nth-child(even) {
            background: #0f1419;
        }

        .table-compact tbody tr:hover {
            background: #1e3a8a;
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
        }

        .table-compact tbody td {
            padding: 0.6rem 0.75rem;
            vertical-align: middle;
            border: none;
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .table-compact tbody td:first-child {
            font-weight: 700;
            color: #ffffff;
        }

        .table-compact tbody td:nth-child(2) {
            color: #cbd5e1;
            font-size: 0.8rem;
            font-weight: 400;
        }

        .table-compact .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-compact .badge-success {
            background: #10b981;
            color: #ffffff;
            border: none;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
        }

        .table-compact .badge-info {
            background: #3b82f6;
            color: #ffffff;
            border: none;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
        }

        .table-compact .badge-warning {
            background: #f59e0b;
            color: #ffffff;
            border: none;
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
        }

        .table-responsive {
            height: auto;
            overflow-x: auto;
            overflow-y: visible;
            border-radius: 10px;
            background: var(--fc-page-bg-color);
            padding: 0.75rem 0.75rem 0rem 0.75rem;
            border: 1px solid #1e293b;
        }

        .invite-tracking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .invite-tracking-header h2 {
            margin: 0;
        }

        .recent-invites h5 {
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .recent-invites h5 i {
            color: #3b82f6;
            margin-right: 0.5rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.2) 100%);
            border-radius: 12px;
            padding: 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            border-color: rgba(255, 255, 255, 0.3);
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.3) 100%);
        }

        .stat-total::before {
            background: linear-gradient(90deg, #6366f1, #4f46e5);
        }

        .stat-accepted::before {
            background: linear-gradient(90deg, #10b981, #059669);
        }

        .stat-pending::before {
            background: linear-gradient(90deg, #f59e0b, #d97706);
        }

        .stat-earnings::before {
            background: linear-gradient(90deg, #8b5cf6, #7c3aed);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .stat-total .stat-icon {
            background: rgba(99, 102, 241, 0.3);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }

        .stat-accepted .stat-icon {
            background: rgba(16, 185, 129, 0.3);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .stat-pending .stat-icon {
            background: rgba(245, 158, 11, 0.3);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
        }

        .stat-earnings .stat-icon {
            background: rgba(139, 92, 246, 0.3);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1;
            margin-bottom: 0.25rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .stat-label {
            font-size: 0.85rem;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
    </style>


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
                                        <div class="map-placeholder mt-2" id="tmMap" style="min-height:1500px;">
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
                                            <button class="carpool-btn active" type="button">I can drive</button>
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
                                        <div class="mb-2 text-muted drag-text">Drag &amp; drop or click to upload</div>
                                        <div class="upload-btn-one">
                                            <button class="upload-btn" type="button">Upload</button>
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
    <script>
        // Award modal functionality
        window.openModalFunction = function(imageSrc, title, description) {
            document.getElementById('modal-img').src = imageSrc;
            document.getElementById('modal-tag').textContent = title;
            document.getElementById('desc-para').textContent = description;
            document.getElementById('myModal-a').style.display = 'block';
        }

        // Close modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-video-slider]').forEach(function(container) {
                const track = container.querySelector('.video-track');
                const prev = container.querySelector('.video-prev');
                const next = container.querySelector('.video-next');

                if (!track || !prev || !next) {
                    return;
                }

                container.classList.add('video-slider-ready');

                const scrollStep = () => Math.max(Math.round(track.clientWidth * 0.85), 220);

                const updateControls = () => {
                    const maxScrollLeft = Math.max(track.scrollWidth - track.clientWidth - 4, 0);
                    const hasOverflow = track.scrollWidth > track.clientWidth + 4;

                    prev.hidden = next.hidden = !hasOverflow;
                    prev.disabled = track.scrollLeft <= 0;
                    next.disabled = track.scrollLeft >= maxScrollLeft;
                };

                prev.addEventListener('click', () => {
                    track.scrollBy({ left: -scrollStep(), behavior: 'smooth' });
                });

                next.addEventListener('click', () => {
                    track.scrollBy({ left: scrollStep(), behavior: 'smooth' });
                });

                let rafId;
                track.addEventListener(
                    'scroll',
                    () => {
                        cancelAnimationFrame(rafId);
                        rafId = requestAnimationFrame(updateControls);
                    },
                    { passive: true }
                );

                window.addEventListener('resize', updateControls);
                updateControls();
                setTimeout(updateControls, 400);
            });

            const modal = document.getElementById('myModal-a');
            const closeBtn = document.querySelector('.close-a');
            const closeBtnFooter = document.querySelector('.btn-clos');

            if (closeBtn) {
                closeBtn.onclick = function() {
                    modal.style.display = 'none';
                }
            }

            if (closeBtnFooter) {
                closeBtnFooter.onclick = function() {
                    modal.style.display = 'none';
                }
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        });

        // Build calendar events for games, events, tournaments
        window.calendarEvents = window.calendarEvents || {};

        // Helper to push entry
        function addCal(dateKey, entry) {
            (window.calendarEvents[dateKey] = window.calendarEvents[dateKey] || []).push(entry);
        }


        // Fun games (pickup)
        @foreach ($pickupGames as $game)
            addCal("{{ date('Y-m-d', strtotime($game->game_datetime)) }}", {
                id: {{ $game->id }},
                date: "{{ date('Y-m-d', strtotime($game->game_datetime)) }}",
                time: "{{ date('H:i', strtotime($game->game_datetime)) }}",
                text: "Pickup: {{ $game->sport->name }}",
                type: 'pickup',
                color: "{{ $game->participants->contains('id', $user->id) ? 'green' : 'blue' }}",
                location: "{{ addslashes($game->location ?? '') }}",
                description: "Join or leave this pickup game.",
                url: "/player/pickup-games/{{ $game->id }}"
            });
        @endforeach

        // Club/team events
        @foreach ($events as $e)
            addCal("{{ date('Y-m-d', strtotime($e->event_date ?? ($e->start ?? now()))) }}", {
                id: {{ $e->id }},
                date: "{{ date('Y-m-d', strtotime($e->event_date ?? ($e->start ?? now()))) }}",
                time: "{{ $e->event_time ? date('H:i', strtotime($e->event_time)) : (isset($e->start) ? date('H:i', strtotime($e->start)) : '') }}",
                text: "{{ addslashes($e->title ?? 'Event') }}",
                type: '{{ $e->type ?? 'event' }}',
                color: "{{ $e->type === 'match' ? 'red' : ($e->type === 'training' ? 'blue' : 'green') }}",
                location: "{{ addslashes($e->location ?? '') }}",
                description: "{{ addslashes(Str::limit($e->description ?? '', 160)) }}",
                url: "#"
            });
        @endforeach

        // Tournaments
        @foreach ($tournaments as $t)
            @php
                $v = $t->venue;

                // Compose a clean, comma-separated location line (no stray commas)
                $locLine = implode(
                    ', ',
                    array_filter([
                        $t->location, // optional freeform location on tournament
                        optional($v)->address, // venue address
                        optional($t->city)->name, // City
                        optional($t->state)->name, // State/Province
                        optional($t->country)->name, // Country
                    ]),
                );

                $dateYmd = optional($t->start_date)->format('Y-m-d') ?? now()->format('Y-m-d');
                $timeHi = optional($t->start_date)->format('H:i') ?? '';

                $lat = optional($v)->lat ?? optional($v)->latitude;
                $lng = optional($v)->lng ?? optional($v)->longitude;
            @endphp

            addCal(@json($dateYmd), {
                id: {{ $t->id }},
                date: @json($dateYmd),
                time: @json($timeHi),
                text: @json('Tournament: ' . $t->name),
                type: 'tournament',
                color: 'orange',
                location: @json($locLine),
                venue_name: @json(optional($v)->name),
                lat: {{ $lat !== null ? $lat : 'null' }},
                lng: {{ $lng !== null ? $lng : 'null' }},
                description: @json(\Illuminate\Support\Str::limit($t->description ?? '', 160)),
                url: @json(url('/tournaments/' . $t->id)) // or null if you don't have this route
            });
        @endforeach


        // Handle opening modal from main.js

        // helpers
        const $ = (sel) => document.querySelector(sel);
        const setText = (sel, v) => {
            const el = $(sel);
            if (el) el.textContent = v ?? '—';
        };

        function fmtWhen(dateStr, timeStr) {
            if (!dateStr) return '—';
            try {
                const d = new Date(dateStr + (timeStr ? 'T' + timeStr : ''));
                const D = new Intl.DateTimeFormat(undefined, {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric'
                }).format(d);
                return timeStr ? `${D} • ${timeStr}` : D;
            } catch {
                return [dateStr, timeStr].filter(Boolean).join(' ');
            }
        }

        function mapsUrls(lat, lng, query) {
            const q = (lat != null && lng != null) ? `${lat},${lng}` : (query || '');
            if (!q) return {
                embed: '',
                view: '#'
            };
            return {
                embed: `https://www.google.com/maps?q=${encodeURIComponent(q)}&hl=en&z=15&output=embed`,
                view: `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(q)}`
            };
        }

        // NEW: same name the calendar calls
        window.openCalendarEvent = function(d) {
            // title (strip "Tournament: " prefix if present)
            const title = (d.title || d.text || 'Event').replace(/^Tournament:\s*/i, '').trim();
            setText('#tmTitle', title);

            // when
            setText('#tmWhen', fmtWhen(d.date, d.time));

            // venue lines
            const venueName = d.venue_name || '';
            const venueLine = [venueName, d.location].filter(Boolean).join(', ');
            setText('#tmVenue', venueLine || '—');
            setText('#tmVenueName', venueName || venueLine || '—');

            // map (uses lat/lng if given; else the location string)
            const urls = mapsUrls(d.lat, d.lng, venueLine || d.location || '');
            const mapEl = document.getElementById('tmMap');
            if (mapEl) {
                mapEl.innerHTML = urls.embed ?
                    `<iframe src="${urls.embed}" width="100%" height="260" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>` :
                    `<div class="text-muted small">Map unavailable</div>`;
            }
            const mapBtn = document.getElementById('tmMapBtn');
            if (mapBtn) mapBtn.href = urls.view || '#';

            // coach note (if you ever add it to your calendar entries)
            setText('#tmCoachNote', d.coach_note || '—');

            // weather from your global
            const wx = window.DASHBOARD_WEATHER || {};
            const wxParts = [];
            if (wx.city) wxParts.push(wx.city);
            if (wx.temp_c != null && wx.temp_c !== '') wxParts.push(`${wx.temp_c}° C`);
            if (wx.condition) wxParts.push(wx.condition);
            setText('#tmWeather', wxParts.join(' • ') || '—');
            const wxIcon = document.getElementById('tmWeatherIcon');
            if (wxIcon) {
                if (wx.icon_url) {
                    wxIcon.src = wx.icon_url;
                    wxIcon.style.display = '';
                } else {
                    wxIcon.style.display = 'none';
                }
            }

            // footer actions (optional)
            const addCal = document.getElementById('tmAddCalBtn');
            if (addCal) addCal.onclick = () => {
                if (d.url) window.open(d.url, '_blank', 'noopener');
            };
            const shareBtn = document.getElementById('tmShareBtn');
            if (shareBtn) shareBtn.onclick = async () => {
                const payload = {
                    title: title || 'Event',
                    text: `${title} — ${$('#tmWhen').textContent}\n${venueLine}`,
                    url: d.url || location.href
                };
                try {
                    if (navigator.share) await navigator.share(payload);
                } catch (e) {}
            };

            // show the new modal
            const el = document.getElementById('staticBackdrop-one');
            const modal = bootstrap.Modal.getOrCreateInstance(el);
            modal.show();
        };



        // Merge and render after defining calendarEvents
        if (typeof events !== 'undefined' && window.calendarEvents) {
            for (const [k, v] of Object.entries(window.calendarEvents)) {
                events[k] = (events[k] || []).concat(v);
            }
            if (typeof renderCalendar === 'function' && typeof currentDate !== 'undefined') {
                renderCalendar(currentDate);
            }
        }

        // Player video slider interactions
        document.addEventListener('DOMContentLoaded', function() {
            const buildUrl = (base, id) => {
                const cleanBase = (base || '/player/videos/explore').replace(/\/$/, '');
                return id ? `${cleanBase}/${id}` : cleanBase;
            };

            document.querySelectorAll('[data-video-slider]').forEach((slider) => {
                let slides = [];
                try {
                    const raw = slider.dataset.videos || '[]';
                    slides = JSON.parse(raw);
                } catch (error) {
                    console.warn('Unable to parse player video slider data', error);
                    slides = [];
                }

                if (!Array.isArray(slides) || !slides.length) {
                    return;
                }

                const stage = slider.querySelector('[data-slider-stage]');
                const cover = slider.querySelector('[data-slider-cover]');
                const coverImg = slider.querySelector('[data-slider-cover-img]');
                const coverVideo = slider.querySelector('[data-slider-video]');
                const titleEl = slider.querySelector('[data-slider-title]');
                const userEl = slider.querySelector('[data-slider-user]');
                const timeEl = slider.querySelector('[data-slider-time]');
                const timeSeparator = slider.querySelector('[data-slider-separator]');
                const descriptionEl = slider.querySelector('[data-slider-description]');
                const indicatorEl = slider.querySelector('[data-slider-indicator]');
                const dotsWrap = slider.querySelector('[data-slider-dots]');
                const prevBtn = slider.querySelector('[data-slider-prev]');
                const nextBtn = slider.querySelector('[data-slider-next]');
                const redirectBase = slider.dataset.redirectBase || '/player/videos/explore';

                let index = 0;
                let timerId = null;
                const AUTOPLAY_INTERVAL = 8000;

                const setCoverImage = (url, preview, previewType) => {
                    const value = url ? `url(${url})` : 'none';
                    if (cover) {
                        const bgValue = preview && previewType === 'file' ? 'none' : value;
                        cover.style.setProperty('--player-video-cover', bgValue);
                        cover.style.backgroundImage = bgValue;
                    }
                    if (coverImg) {
                        coverImg.src = url || @json(asset('assets/player-dashboard/images/video-thumbnail.png'));
                        coverImg.style.display = preview ? 'none' : 'block';
                    }
                    if (coverVideo) {
                        if (preview && previewType === 'file') {
                            if (coverVideo.src !== preview) {
                                coverVideo.src = preview;
                                coverVideo.load();
                            }
                            coverVideo.poster = '';
                            coverVideo.style.display = 'block';
                        } else {
                            if (coverVideo.src) {
                                coverVideo.pause();
                                coverVideo.removeAttribute('src');
                                coverVideo.load();
                            }
                            coverVideo.poster = url || @json(asset('assets/player-dashboard/images/video-thumbnail.png'));
                            coverVideo.style.display = 'none';
                        }
                    }
                };

                const render = () => {
                    const current = slides[index] || {};
                    setCoverImage(current.thumbnail || '', current.preview || '', current
                        .preview_type || 'file');

                    if (titleEl) {
                        titleEl.textContent = current.title || 'Player video';
                    }
                    if (userEl) {
                        userEl.textContent = current.user || 'Play2Earn';
                    }
                    if (timeEl) {
                        timeEl.textContent = current.time || '';
                    }
                    if (timeSeparator) {
                        timeSeparator.style.visibility = current.time ? 'visible' : 'hidden';
                    }
                    if (descriptionEl) {
                        descriptionEl.textContent = current.description || '';
                    }
                    if (indicatorEl) {
                        indicatorEl.textContent = `${index + 1} / ${slides.length}`;
                    }
                    if (dotsWrap) {
                        dotsWrap.querySelectorAll('.player-video-dot').forEach((dot, dotIndex) => {
                            dot.classList.toggle('active', dotIndex === index);
                        });
                    }
                };

                const goTo = (targetIndex) => {
                    if (!slides.length) {
                        return;
                    }
                    index = (targetIndex + slides.length) % slides.length;
                    render();
                };

                const go = (delta) => goTo(index + delta);

                const stopAuto = () => {
                    if (timerId) {
                        window.clearInterval(timerId);
                        timerId = null;
                    }
                };

                const startAuto = () => {
                    if (timerId || slides.length <= 1) {
                        return;
                    }
                    timerId = window.setInterval(() => {
                        go(1);
                    }, AUTOPLAY_INTERVAL);
                };

                if (dotsWrap) {
                    dotsWrap.innerHTML = '';
                    slides.forEach((_, dotIndex) => {
                        const dot = document.createElement('button');
                        dot.type = 'button';
                        dot.className = 'player-video-dot' + (dotIndex === index ? ' active' : '');
                        dot.setAttribute('aria-label', `Show video ${dotIndex + 1}`);
                        dot.addEventListener('click', (event) => {
                            event.stopPropagation();
                            stopAuto();
                            goTo(dotIndex);
                            startAuto();
                        });
                        dotsWrap.appendChild(dot);
                    });
                }

                if (prevBtn) {
                    prevBtn.addEventListener('click', (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        stopAuto();
                        go(-1);
                        startAuto();
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        stopAuto();
                        go(1);
                        startAuto();
                    });
                }

                const openCurrent = () => {
                    const current = slides[index] || {};
                    const target = current.url || buildUrl(redirectBase, current.id);
                    if (target) {
                        window.location.href = target;
                    }
                };

                if (stage) {
                    stage.addEventListener('click', openCurrent);
                    stage.addEventListener('keydown', (event) => {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            openCurrent();
                        }
                    });
                    stage.addEventListener('mouseenter', stopAuto);
                    stage.addEventListener('mouseleave', startAuto);
                }

                slider.addEventListener('mouseenter', stopAuto);
                slider.addEventListener('mouseleave', startAuto);

                render();
                startAuto();
            });
        });

        // Pickup game functions
        function joinGame(btn) {
            const id = btn.dataset.id;
            fetch(`/pickup-games/${id}/join`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => location.reload());
        }

        function leaveGame(btn) {
            const id = btn.dataset.id;
            fetch(`/pickup-games/${id}/leave`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => location.reload());
        }
    </script>
@endpush





@push('styles')
    <style>
        /* Invite Tracking Styles */
        .invite-tracking-card {
            margin-bottom: 2rem;
        }

        .invite-tracking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .invite-tracking-header h2 {
            margin: 0;
            color: #333;
        }

        .invite-stat {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .recent-invites {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
        }

        .recent-invites h5 {
            margin-bottom: 1rem;
            color: #333;
        }

        .recent-invites .table {
            margin-bottom: 0;
        }

        .recent-invites .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            font-size: 0.85rem;
        }

        .recent-invites .table td {
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
@endpush
