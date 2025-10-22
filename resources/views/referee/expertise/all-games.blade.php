@extends('layouts.referee')

@section('title', 'All Games')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> All Games
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('referee.expertise.index') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-cog"></i> Manage Expertise
                        </a>
                        <a href="{{ route('referee.expertise.available-games') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-check-circle"></i> Qualified Games Only
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Regular Games -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="text-primary">
                                <i class="fas fa-futbol"></i> Regular Games ({{ $games->count() }})
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Home Team</th>
                                            <th>Away Team</th>
                                            <th>Date & Time</th>
                                            <th>Venue</th>
                                            <th>Required Expertise</th>
                                            <th>Your Qualification</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($games as $game)
                                        <tr class="{{ $game->expertise_id && in_array($game->expertise_id, $refereeExpertises) ? 'table-success' : ($game->expertise_id ? 'table-warning' : '') }}">
                                            <td>{{ $game->id }}</td>
                                            <td>
                                                @if($game->homeClub)
                                                    {{ $game->homeClub->name }}
                                                @else
                                                    <span class="text-muted">TBD</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($game->awayClub)
                                                    {{ $game->awayClub->name }}
                                                @else
                                                    <span class="text-muted">TBD</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $game->match_date ? \Carbon\Carbon::parse($game->match_date)->format('M d, Y') : 'TBD' }}
                                                @if($game->match_time)
                                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($game->match_time)->format('g:i A') }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $game->venue ?: 'TBD' }}</td>
                                            <td>
                                                @if($game->expertise)
                                                    <span class="badge badge-info">{{ $game->expertise->expertise_level }}</span>
                                                @else
                                                    <span class="badge badge-secondary">Not Set</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($game->expertise_id && in_array($game->expertise_id, $refereeExpertises))
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> Qualified
                                                    </span>
                                                @elseif($game->expertise_id)
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-times"></i> Not Qualified
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-question"></i> No Requirement
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($game->referee_id)
                                                    <span class="badge badge-warning">Assigned</span>
                                                @else
                                                    <span class="badge badge-success">Available</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No games found
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Pickup Games -->
                    <div class="row">
                        <div class="col-12">
                            <h4 class="text-success">
                                <i class="fas fa-gamepad"></i> Pickup Games ({{ $pickupGames->count() }})
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Sport</th>
                                            <th>Date & Time</th>
                                            <th>Location</th>
                                            <th>Required Expertise</th>
                                            <th>Your Qualification</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pickupGames as $pickupGame)
                                        <tr class="{{ $pickupGame->expertise_id && in_array($pickupGame->expertise_id, $refereeExpertises) ? 'table-success' : ($pickupGame->expertise_id ? 'table-warning' : '') }}">
                                            <td>{{ $pickupGame->id }}</td>
                                            <td>{{ $pickupGame->title ?: 'Untitled Game' }}</td>
                                            <td>
                                                @if($pickupGame->sport)
                                                    {{ $pickupGame->sport->name }}
                                                @else
                                                    <span class="text-muted">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($pickupGame->game_datetime)
                                                    {{ \Carbon\Carbon::parse($pickupGame->game_datetime)->format('M d, Y g:i A') }}
                                                @else
                                                    <span class="text-muted">TBD</span>
                                                @endif
                                            </td>
                                            <td>{{ $pickupGame->location ?: 'TBD' }}</td>
                                            <td>
                                                @if($pickupGame->expertise)
                                                    <span class="badge badge-info">{{ $pickupGame->expertise->expertise_level }}</span>
                                                @else
                                                    <span class="badge badge-secondary">Not Set</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($pickupGame->expertise_id && in_array($pickupGame->expertise_id, $refereeExpertises))
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> Qualified
                                                    </span>
                                                @elseif($pickupGame->expertise_id)
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-times"></i> Not Qualified
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        <i class="fas fa-question"></i> No Requirement
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($pickupGame->referee_id)
                                                    <span class="badge badge-warning">Assigned</span>
                                                @else
                                                    <span class="badge badge-success">Available</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                <i class="fas fa-info-circle"></i> No pickup games found
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Legend</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <span class="badge badge-success mr-2">Green Row</span>
                                            <small>You are qualified for this game</small>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="badge badge-warning mr-2">Yellow Row</span>
                                            <small>You are not qualified for this game</small>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="badge badge-secondary mr-2">White Row</span>
                                            <small>No expertise requirement set</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection