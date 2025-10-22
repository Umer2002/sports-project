@extends('layouts.player-new')

@section('title', 'Pickup Games')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Pickup Games</h2>
        <a href="{{ route('player.pickup-games.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Pickup Game
        </a>
    </div>

    @include('partials.alerts')

    <!-- Search and Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-search me-2"></i>Search & Filter Games
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('player.pickup-games.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ $search }}" placeholder="Search by title, description, or location...">
                </div>
                <div class="col-md-3">
                    <label for="sport_id" class="form-label">Sport</label>
                    <select class="form-select" id="sport_id" name="sport_id">
                        <option value="">All Sports</option>
                        @foreach($sports as $sport)
                            <option value="{{ $sport->id }}" {{ $sport_id == $sport->id ? 'selected' : '' }}>
                                {{ $sport->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" 
                           value="{{ $location }}" placeholder="Enter location...">
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ $date_from }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ $date_to }}">
                </div>
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="{{ route('player.pickup-games.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- My Games -->
    @if($myGames->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-gamepad me-2"></i>My Games
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($myGames as $game)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $game->title }}</h6>
                                    <p class="card-text text-muted">{{ $game->sport ? $game->sport->name : 'Not specified' }}</p>
                                    <p class="card-text">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $game->game_datetime ? $game->game_datetime->format('M d, Y H:i') : 'Date not set' }}
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $game->location }}
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $game->participants->count() }}/{{ $game->max_players }} players
                                    </p>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('player.pickup-games.show', $game) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('player.pickup-games.edit', $game) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Search Results Summary -->
    @if($search || $sport_id || $location || $date_from || $date_to)
        <div class="alert alert-info mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>Search Results:</strong> Found {{ $games->total() }} game(s)
                    @if($search)
                        <span class="badge bg-primary ms-2">Search: "{{ $search }}"</span>
                    @endif
                    @if($sport_id)
                        <span class="badge bg-success ms-1">Sport: {{ $sports->find($sport_id)->name ?? 'Unknown' }}</span>
                    @endif
                    @if($location)
                        <span class="badge bg-warning ms-1">Location: "{{ $location }}"</span>
                    @endif
                    @if($date_from)
                        <span class="badge bg-info ms-1">From: {{ $date_from }}</span>
                    @endif
                    @if($date_to)
                        <span class="badge bg-info ms-1">To: {{ $date_to }}</span>
                    @endif
                </div>
                <a href="{{ route('player.pickup-games.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Clear Filters
                </a>
            </div>
        </div>
    @endif

    <!-- Available Games -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-search me-2"></i>Available Games
            </h5>
        </div>
        <div class="card-body">
            @if($games->count() > 0)
                <div class="row">
                    @foreach($games as $game)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $game->title }}</h6>
                                    <p class="card-text text-muted">{{ $game->sport ? $game->sport->name : 'Not specified' }}</p>
                                    <p class="card-text">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $game->game_datetime ? $game->game_datetime->format('M d, Y H:i') : 'Date not set' }}
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ $game->location }}
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $game->participants->count() }}/{{ $game->max_players }} players
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-user me-1"></i>
                                        Hosted by {{ $game->host ? $game->host->name : 'Unknown' }}
                                    </p>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('player.pickup-games.show', $game) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($game->canJoin(auth()->id()))
                                            <form action="{{ route('player.pickup-games.join', $game) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus"></i> Join
                                                </button>
                                            </form>
                                        @elseif($game->participants->contains(auth()->id()))
                                            <form action="{{ route('player.pickup-games.leave', $game) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-minus"></i> Leave
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-ban"></i> Full
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{ $games->links() }}
            @else
                <div class="text-center py-4">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    @if($search || $sport_id || $location || $date_from || $date_to)
                        <h5>No Games Found</h5>
                        <p class="text-muted">No pickup games match your search criteria. Try adjusting your filters or create a new game!</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('player.pickup-games.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Clear Filters
                            </a>
                            <a href="{{ route('player.pickup-games.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create Game
                            </a>
                        </div>
                    @else
                        <h5>No Available Games</h5>
                        <p class="text-muted">No pickup games are currently available. Create one to get started!</p>
                        <a href="{{ route('player.pickup-games.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create First Game
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
