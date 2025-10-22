@extends('layouts.referee')

@section('title', 'Available Games - Qualified')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-check-circle text-success"></i> Games You're Qualified For
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('referee.expertise.index') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-cog"></i> Manage Expertise
                        </a>
                        <a href="{{ route('referee.expertise.all-games') }}" class="btn btn-sm btn-info">
                            <i class="fas fa-list"></i> View All Games
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($games->count() > 0 || $pickupGames->count() > 0)
                        <!-- Regular Games -->
                        @if($games->count() > 0)
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
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($games as $game)
                                            <tr>
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
                                                        <span class="badge badge-success">{{ $game->expertise->expertise_level }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">Not Set</span>
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
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Pickup Games -->
                        @if($pickupGames->count() > 0)
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
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($pickupGames as $pickupGame)
                                            <tr>
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
                                                        <span class="badge badge-success">{{ $pickupGame->expertise->expertise_level }}</span>
                                                    @else
                                                        <span class="badge badge-secondary">Not Set</span>
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
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>No qualified games found!</strong><br>
                            You need to set your expertise levels first, or there are no games that match your current expertise.
                            <div class="mt-3">
                                <a href="{{ route('referee.expertise.index') }}" class="btn btn-primary">
                                    <i class="fas fa-cog"></i> Manage Expertise Levels
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection