@php
    $activeTournaments = \App\Models\Tournament::whereDate('end_date', '>=', now())
        ->whereHas('games', fn($q) => $q->where('referee_id', auth()->user()->referee->id))
        ->count();
    $latestTournament = \App\Models\Tournament::with('scheduledGames')
        ->whereHas('games', fn($q) => $q->where('referee_id', auth()->user()->referee->id))
        ->latest('start_date')->first();
@endphp

@if($latestTournament)
<div class="card stats-card widget-card-1">
    <div class="card-body">
        <div class="media d-flex align-items-center">
            <div class="widget-icon bg-primary text-white"><i class="fas fa-trophy"></i></div>
            <div class="media-body ms-3">
                <h4 class="mb-0">{{ $latestTournament->name }}</h4>
                <p class="mb-0 text-muted">
                    {{ $latestTournament->format->name ?? 'N/A' }} • {{ $latestTournament->location }}
                </p>
            </div>
        </div>
        <ul class="list-group list-group-flush mt-3">
            <li class="list-group-item d-flex justify-content-between">
                Active Tournaments <span>{{ $activeTournaments }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                Matches Scheduled <span>{{ $latestTournament->scheduledGames->count() }}</span>
            </li>
        </ul>
    </div>
</div>
@endif

