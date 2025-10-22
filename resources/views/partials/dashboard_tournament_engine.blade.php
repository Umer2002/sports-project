@php
    $activeTournaments = \App\Models\Tournament::whereDate('end_date', '>=', now())->count();
    $latestTournament = \App\Models\Tournament::with('scheduledGames')->latest('start_date')->first();
@endphp

@if($latestTournament)
<div class="card stats-card widget-card-1">
    <div class="header">
        <h2><i class="fas fa-trophy fa-2x" style="color: gold"></i> Tournament Engine</h2>
        <ul class="header-dropdown">
            <li class="dropdown">
                <a href="#" onclick="return false;" class="dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="material-icons">more_vert</i>
                </a>
                <ul class="dropdown-menu pull-right">
                    <li>
                        <a href="#" onclick="return false;">Start</a>
                    </li>

                </ul>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="media d-flex align-items-center">

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
                Teams Registered <span>{{ $latestTournament->invites->count() }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between">
                Matches Scheduled <span>{{ $latestTournament->scheduledGames->count() }}</span>
            </li>
        </ul>
    </div>
</div>
@endif
