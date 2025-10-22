@extends($layout ?? 'layouts.player-new')

@section('title', 'Explore Videos')
@section('page-title', 'Videos')

@php use Illuminate\Support\Str; @endphp

@section('header_styles')
@parent
<style>
    .shorts-wrapper {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .shorts-feed {
        max-height: calc(100vh - 260px);
        overflow-y: auto;
        scroll-snap-type: y mandatory;
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding-right: 4px;
    }
    .short-card {
        position: relative;
        border-radius: 18px;
        overflow: hidden;
        background: color-mix(in srgb, var(--bg-card) 88%, #04081a 12%);
        box-shadow: 0 18px 36px rgba(4, 8, 26, 0.35);
        scroll-snap-align: start;
    }
    [data-bs-theme="dark"] .short-card {
        background: color-mix(in srgb, #04081a 88%, transparent);
        box-shadow: 0 18px 36px rgba(4, 8, 26, 0.55);
    }
    .short-video-wrapper {
        position: relative;
        min-height: 420px;
        background: color-mix(in srgb, var(--bg-primary) 65%, #04081a 35%);
    }
    .short-card video,
    .short-live {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .short-card video {
        pointer-events: none;
        background: #000;
    }
    .short-live {
        background: radial-gradient(circle at center, rgba(255, 255, 255, 0.08), rgba(9, 12, 32, 0.92));
        font-size: 1.1rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .short-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: flex-end;
        background: linear-gradient(180deg, rgba(4, 6, 18, 0.05) 40%, rgba(4, 6, 18, 0.92) 100%);
        padding: 18px 18px 20px;
    }
    .short-meta {
        color: #fff;
        width: 100%;
    }
    .short-meta h5 {
        margin: 0 0 6px;
        font-size: 1.08rem;
        font-weight: 600;
    }
    .short-meta-line {
        font-size: 0.82rem;
        opacity: 0.75;
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }
    .short-stats {
        display: flex;
        gap: 14px;
        font-size: 0.78rem;
        opacity: 0.75;
        margin-top: 12px;
    }
    .shorts-sentinel {
        text-align: center;
        padding: 12px 0 24px;
        font-size: 0.85rem;
        color: var(--muted-color);
    }
    @media (max-width: 991.98px) {
        .shorts-feed {
            max-height: none;
        }
        .short-video-wrapper {
            min-height: 360px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-3">
        <h3 class="mb-1">Explore Videos</h3>
        <p class="text-muted mb-0">Share your highlights, drills, or challenges and discover what others are posting.</p>
    </div>

    @include('players.partials.video-composer', [
        'composerId' => 'explore',
        'allowLive' => $allowLive ?? false,
    ])

    <div
        id="video-explore-app"
        data-endpoint="{{ $feedEndpoint }}"
        data-base-show-url="{{ $showBaseUrl }}"
        data-my-videos='@json($initialMyVideos, JSON_UNESCAPED_UNICODE)'
        data-community-videos='@json($initialCommunityVideos, JSON_UNESCAPED_UNICODE)'
        class="mt-3"
    ></div>

    <div id="video-explore-fallback" class="d-grid gap-3 mt-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>My Recent Uploads</strong>
                <span class="text-muted small">Visible to your network</span>
            </div>
            <div class="card-body">
                @if(collect($initialMyVideos)->isEmpty())
                    <div class="text-muted">You haven’t shared any videos yet. Upload one above to get started.</div>
                @else
                    <div class="row g-3">
                        @foreach($initialMyVideos as $video)
                            @php
                                $previewUrl = $video['playback_url'] ?? $video['url'] ?? null;
                                $videoType = strtolower((string)($video['video_type'] ?? ''));
                                $isLive = ($video['is_live'] ?? false)
                                    || Str::endsWith(strtolower($previewUrl ?? ''), '.m3u8')
                                    || $videoType === 'live';
                                $likes = $video['likes_count'] ?? 0;
                                $comments = $video['comments_count'] ?? 0;
                            @endphp
                            <div class="col-12 col-sm-6 col-lg-4">
                                <a href="{{ $showBaseUrl }}/{{ $video['id'] ?? '' }}" class="card h-100 text-decoration-none text-reset">
                                    <div class="ratio ratio-16x9">
                                        @if($isLive)
                                            <div class="d-flex align-items-center justify-content-center bg-dark text-white">Live Stream</div>
                                        @else
                                            <video src="{{ $previewUrl }}" muted playsinline preload="metadata" class="w-100 h-100" style="object-fit: cover;"></video>
                                        @endif
                                    </div>
                                    <div class="card-body py-2">
                                        <h6 class="mb-1 text-truncate" title="{{ $video['title'] ?? 'Video' }}">{{ $video['title'] ?? 'Video' }}</h6>
                                        <div class="small text-muted">{{ strtoupper($video['category'] ?? $video['video_type'] ?? 'video') }}</div>
                                        <div class="small text-muted mt-2">👍 {{ $likes }} · 💬 {{ $comments }}</div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Community Feed</strong>
                <span class="text-muted small">Newest first</span>
            </div>
            <div class="card-body">
                @if(collect($initialCommunityVideos)->isEmpty())
                    <div class="text-muted">Community videos will appear here once teammates share their clips.</div>
                @else
                    <div class="row g-3">
                        @foreach($initialCommunityVideos as $video)
                            @php
                                $previewUrl = $video['playback_url'] ?? $video['url'] ?? null;
                                $videoType = strtolower((string)($video['video_type'] ?? ''));
                                $isLive = ($video['is_live'] ?? false)
                                    || Str::endsWith(strtolower($previewUrl ?? ''), '.m3u8')
                                    || $videoType === 'live';
                                $likes = $video['likes_count'] ?? 0;
                                $comments = $video['comments_count'] ?? 0;
                            @endphp
                            <div class="col-12 col-sm-6 col-lg-4">
                                <a href="{{ $showBaseUrl }}/{{ $video['id'] ?? '' }}" class="card h-100 text-decoration-none text-reset">
                                    <div class="ratio ratio-16x9">
                                        @if($isLive)
                                            <div class="d-flex align-items-center justify-content-center bg-dark text-white">Live Stream</div>
                                        @else
                                            <video src="{{ $previewUrl }}" muted playsinline preload="metadata" class="w-100 h-100" style="object-fit: cover;"></video>
                                        @endif
                                    </div>
                                    <div class="card-body py-2">
                                        <div class="fw-semibold text-truncate" title="{{ $video['title'] ?? 'Video' }}">{{ $video['title'] ?? 'Video' }}</div>
                                        <div class="small text-muted">{{ strtoupper($video['category'] ?? $video['video_type'] ?? 'video') }}</div>
                                        <div class="small text-muted mt-2">👍 {{ $likes }} · 💬 {{ $comments }}</div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/react/videos-feed.jsx', 'resources/js/react/video-explore.jsx', 'resources/js/video-composer.js'])
@endpush
