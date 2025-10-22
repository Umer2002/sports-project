
<div class="text-white">
    <h6 class="text-lg font-semibold mb-2">SingleSlide Auto Play</h6>
    <div id="carousel" class="relative overflow-hidden">
        @if($videos->count())
            <video controls autoplay muted loop class="rounded w-full">
                <source src="{{ asset('storage/' . $videos->first()->path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        @else
            <p>No videos available.</p>
        @endif
    </div>
</div>
