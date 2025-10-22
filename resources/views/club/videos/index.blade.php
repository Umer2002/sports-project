@extends('layouts.club-dashboard')

@section('title', 'Club Videos')
@section('page_title', 'Videos')

@php use Illuminate\Support\Str; @endphp

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
        <h3 class="mb-1">Manage Club Videos</h3>
        <p class="text-muted mb-0">Upload highlights, drills, or go live with your athletes.</p>
    </div>

    @include('players.partials.video-composer', [
        'composerId' => 'club-explore',
        'allowLive' => $allowLive,
        'uploadAction' => route('club.videos.upload'),
    ])

    <div class="card mt-3 border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>My Recent Uploads</strong>
            <span class="text-muted small">Quick access to videos you have shared</span>
        </div>
        <div class="card-body">
            @if(($myVideos ?? collect())->isEmpty())
                <div class="text-muted">No videos yet. Upload one above to share with your club.</div>
            @else
                <div class="row g-3">
                    @foreach($myVideos as $video)
                        @php
                            $previewUrl = $video->playback_url ?: $video->url;
                            $isLive = Str::endsWith(strtolower($previewUrl ?? ''), '.m3u8') || strtolower($video->video_type ?? '') === 'live';
                        @endphp
                        <div class="col-12 col-sm-6 col-lg-4">
                            <a href="{{ route('club.videos.show', $video) }}" class="card h-100 text-decoration-none text-reset">
                                <div class="ratio ratio-16x9">
                                    @if($isLive)
                                        <div class="d-flex align-items-center justify-content-center bg-dark text-white">Live Stream</div>
                                    @else
                                        <video src="{{ $previewUrl }}" muted playsinline preload="metadata" class="w-100 h-100" style="object-fit: cover;"></video>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h6 class="mb-1 text-truncate" title="{{ $video->title }}">{{ $video->title }}</h6>
                                    <div class="small text-muted">{{ ucfirst($video->category ?? $video->video_type ?? 'video') }}</div>
                                    <div class="small text-muted mt-2">👍 {{ $video->likes_count ?? $video->likes->count() }} · 💬 {{ $video->comments_count ?? $video->comments->count() }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="card mt-3 border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <strong>Community Feed</strong>
            <span class="text-muted small">See what your network is sharing</span>
        </div>
        <div class="card-body p-0">
            <div id="react-videos-root"
                 data-endpoint="{{ route('player.videos.feed-json') }}"
                 data-base-show-url="{{ url('/club/videos') }}"
                 class="p-3">
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
    @vite(['resources/js/react/videos-feed.jsx', 'resources/js/video-composer.js'])
@endsection
