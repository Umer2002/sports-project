@extends('layouts.player-new')

@section('title', 'Videos')

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

    <div class="row">
        <div class="col-lg-8">
            <div class="same-card mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Latest Videos</h4>
                    <a href="{{ route('player.videos.explore') }}" class="btn btn-sm btn-outline-primary">Explore All</a>
                </div>
                <div id="latest-videos-react" class="mt-2"></div>
            </div>
            <!-- Calendar (same structure as dashboard) -->


            <!-- Main Video (Baseball-style) -->
            <div class="video-card mb-3">
                @php
                    $hasVideoAd = isset($videoAd);
                @endphp
                @if($hasVideoAd)
                    @php
                        $adPath = $videoAd->media ?? null;
                        $adUrl = null;
                        if ($adPath) {
                            if (Str::startsWith($adPath, ['http://','https://'])) {
                                $adUrl = $adPath;
                            } elseif (Storage::disk('public')->exists($adPath)) {
                                $adUrl = Storage::disk('public')->url($adPath);
                            }
                        }
                    @endphp
                    @if($adUrl)
                        <div class="video-card">
                            <video class="w-100" controls style="border-radius: 8px;" preload="metadata">
                                <source src="{{ $adUrl }}">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">Ad video unavailable. Please contact your coach or admin.</div>
                    @endif
                @elseif(isset($featuredVideo))
                    @php
                        $isYT = Str::contains($featuredVideo->url, 'youtube') || Str::contains($featuredVideo->url, 'youtu.be');
                        $localUrl = $featuredVideo->playback_url ?: $featuredVideo->url;
                    @endphp
                    <div class="video-card">
                        @if($isYT)
                            @php preg_match('/(?:v=|youtu\\.be\\/)([A-Za-z0-9_-]+)/', $featuredVideo->url, $mm); $fid = $mm[1] ?? 'GyO1MtLhyt0'; @endphp
                            <img src="https://img.youtube.com/vi/{{ $fid }}/hqdefault.jpg" alt="Video Thumbnail" class="thumbnail">
                            <div class="play-btn"></div>
                            <iframe id="videoFrame" src="https://www.youtube.com/embed/{{ $fid }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="display:block;"></iframe>
                        @else
                            <video class="w-100" controls style="border-radius: 8px;">
                                <source src="{{ $localUrl }}" type="video/mp4">
                            </video>
                        @endif
                    </div>
                @else
                    <div class="alert alert-info">No videos yet. Upload below.</div>
                @endif
            </div>

            <!-- Uploaded Videos grid -->
            <div class="same-card mt-3">
                <div class="awards-headding d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">My Uploads</h2>
                    <a href="{{ route('player.videos.index') }}" class="btn btn-sm btn-outline-secondary">Refresh</a>
                </div>
                @if(($videos ?? collect())->count() > 0)
                    <div class="row g-3 mt-1">
                        @foreach($videos as $v)
                            @php
                                $isYT = Str::contains($v->url, 'youtube') || Str::contains($v->url, 'youtu.be');
                                $localUrl = $v->playback_url ?: $v->url;
                            @endphp
                            <div class="col-12 col-md-6">
                                <div class="video-card p-2 border rounded">
                                    <div class="mb-2 fw-semibold">{{ $v->title }}</div>
                                    @if($isYT)
                                        @php preg_match('/(?:v=|youtu\\.be\\/)([A-Za-z0-9_-]+)/', $v->url, $mm); $vid = $mm[1] ?? null; @endphp
                                        @if($vid)
                                            <iframe width="100%" height="220" src="https://www.youtube.com/embed/{{ $vid }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        @else
                                            <a href="{{ $v->url }}" target="_blank">Open on YouTube</a>
                                        @endif
                                    @else
                                        <video class="w-100" controls style="border-radius: 8px;">
                                            <source src="{{ $localUrl }}" type="video/mp4">
                                        </video>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="text-muted small">{{ $v->created_at?->diffForHumans() }}</div>
                                        <div class="small d-flex align-items-center gap-2">
                                            @php $liked = $v->likes()->where('user_id', $user->id)->exists(); @endphp
                                            <form action="{{ $liked ? route('player.videos.unlike', $v->id) : route('player.videos.like', $v->id) }}" method="POST">
                                                @csrf
                                                <button class="btn btn-sm {{ $liked ? 'btn-outline-secondary' : 'btn-outline-primary' }}">{{ $liked ? 'Unlike' : 'Like' }}</button>
                                            </form>
                                            <span class="ms-2">👍 {{ $v->likes()->count() }}</span>
                                            <span>💬 {{ $v->comments()->count() }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        <form action="{{ route('player.videos.comment', $v->id) }}" method="POST" class="d-flex gap-2">
                                            @csrf
                                            <input type="text" name="content" class="form-control form-control-sm" placeholder="Write a comment..." required>
                                            <button class="btn btn-sm btn-primary">Post</button>
                                        </form>
                                        <div class="mt-2" style="max-height: 180px; overflow:auto;">
                                            @foreach($v->comments()->latest()->take(5)->get() as $c)
                                                <div class="small mb-1">
                                                    <strong>{{ $c->user?->name ?? $c->author_name ?? 'User' }}</strong>:
                                                    <span>{{ $c->content }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info m-2">You haven’t uploaded any videos yet.</div>
                @endif
            </div>


            <!-- Calendar Event Modal -->
            <div class="modal fade" id="calendarEventModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="calEvtTitle">Event</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-2"><strong>Date/Time:</strong> <span id="calEvtWhen"></span></div>
                            <div class="mb-2"><strong>Type:</strong> <span id="calEvtType"></span></div>
                            <div class="mb-2"><strong>Location:</strong> <span id="calEvtLocation"></span></div>
                            <div class="mb-2"><strong>Details:</strong>
                                <div id="calEvtDesc" class="text-muted"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-primary d-none" id="calEvtLink" target="_blank">Open</a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Ads grid 4x4 (4 per row) -->
            <div class="same-card trainer-banners">
                <div class="row">
                    @forelse(($adsImages ?? collect()) as $ad)
                        <div class="col-6 col-md-6 col-lg-6 mb-3">
                            <div class="trainer-banner">
                                @php $img = Str::startsWith($ad->media, ['http://','https://']) ? $ad->media : asset('storage/' . $ad->media); @endphp
                                <a href="{{ $ad->link ?? '#' }}" target="_blank">
                                    <img src="{{ $img }}" alt="{{ $ad->title }}">
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-12"><em>No ads available.</em></div>
                    @endforelse
                </div>
            </div>

            <!-- Upload Video (composer) -->
            <div class="mt-3">
                @include('players.partials.video-composer', ['composerId' => 'index', 'isCoach' => $isCoach])
            </div>
        </div>
    </div>
</div>


@endsection
@push('scripts')
@vite(['resources/js/react/videos-feed.jsx', 'resources/js/video-composer.js'])
@endpush
