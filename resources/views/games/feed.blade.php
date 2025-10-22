@extends('layouts.default')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-2 mb-4">
            @include('players.partials.sidebar')
        </div>
        <div class="col-lg-10">
            <div class="card bg-dark text-white shadow rounded-4">
                <div class="card-header">
                    <h5 class="mb-0">Live Feed - Game #{{ $game->id }}</h5>
                </div>
                <div class="card-body" id="feed" style="min-height:300px"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let lastId = 0;
function fetchEvents() {
    fetch(`/games/{{ $game->id }}/events?last_id=${lastId}`)
        .then(res => res.json())
        .then(data => {
            const feed = document.getElementById('feed');
            data.forEach(ev => {
                const div = document.createElement('div');
                div.className = 'mb-2';
                div.textContent = (ev.minute ? `[${ev.minute}\' ] ` : '') + ev.description;
                feed.appendChild(div);
                lastId = ev.id;
            });
            feed.scrollTop = feed.scrollHeight;
        });
}
setInterval(fetchEvents, 5000);
fetchEvents();
</script>
@endpush
