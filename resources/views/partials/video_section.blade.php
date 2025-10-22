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
    @else
        <div class="video-empty">
            <p class="mb-2">No videos yet.</p>
            <a href="{{ route('player.videos.explore') }}" class="btn btn-sm btn-primary">Explore videos</a>
        </div>
    @endif


</div>