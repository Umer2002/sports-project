@extends('layouts.coach-dashboard')

@section('title', 'Match Stats - ' . ($match->homeClub->name ?? 'TBD') . ' vs ' . ($match->awayClub->name ?? 'TBD'))
@section('page_title', 'Match Statistics')

@section('content')
<div class="container-fluid">
    <!-- Match Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-futbol fa-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-1">
                                        {{ $match->homeClub->name ?? 'TBD' }} vs {{ $match->awayClub->name ?? 'TBD' }}
                                    </h4>
                                    <p class="text-muted mb-0">
                                        {{ $match->tournament->name ?? 'Tournament Match' }}
                                    </p>
                                    <div class="mt-2">
                                        <span class="badge bg-info me-2">
                                            {{ $match->match_date ? $match->match_date->format('M d, Y') : 'Date TBD' }}
                                        </span>
                                        @if($match->match_time)
                                            <span class="badge bg-secondary">
                                                {{ $match->match_time->format('h:i A') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            @if($match->score && is_array($match->score))
                                <div class="display-6 text-center">
                                    <span class="badge bg-success fs-2">
                                        {{ $match->score['home'] ?? 0 }} - {{ $match->score['away'] ?? 0 }}
                                    </span>
                                </div>
                            @else
                                <div class="text-center">
                                    <span class="text-muted">Score not available</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Match Stats Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Enter Match Statistics</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('coach.tournaments.update-match-stats', $match) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Score Section -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Match Score</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="home_score" class="form-label">
                                                    {{ $match->homeClub->name ?? 'Home Team' }} Score
                                                </label>
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="home_score" 
                                                       name="home_score" 
                                                       value="{{ $match->score['home'] ?? 0 }}" 
                                                       min="0" 
                                                       required>
                                            </div>
                                            <div class="col-6">
                                                <label for="away_score" class="form-label">
                                                    {{ $match->awayClub->name ?? 'Away Team' }} Score
                                                </label>
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="away_score" 
                                                       name="away_score" 
                                                       value="{{ $match->score['away'] ?? 0 }}" 
                                                       min="0" 
                                                       required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Match Status -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Match Status</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="match_status" class="form-label">Status</label>
                                            <select class="form-select" id="match_status" name="match_status" required>
                                                <option value="scheduled" {{ ($match->status ?? 'scheduled') === 'scheduled' ? 'selected' : '' }}>
                                                    Scheduled
                                                </option>
                                                <option value="in_progress" {{ ($match->status ?? '') === 'in_progress' ? 'selected' : '' }}>
                                                    In Progress
                                                </option>
                                                <option value="completed" {{ ($match->status ?? '') === 'completed' ? 'selected' : '' }}>
                                                    Completed
                                                </option>
                                                <option value="cancelled" {{ ($match->status ?? '') === 'cancelled' ? 'selected' : '' }}>
                                                    Cancelled
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Match Notes</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Additional Notes</label>
                                            <textarea class="form-control" 
                                                      id="notes" 
                                                      name="notes" 
                                                      rows="4" 
                                                      placeholder="Enter any additional notes about the match...">{{ $match->notes ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('coach.tournaments.show', $match->tournament_id) }}" 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Tournament
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Match Stats
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Teams Information -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">{{ $match->homeClub->name ?? 'Home Team' }}</h6>
                </div>
                <div class="card-body">
                    @if($homeTeamPlayers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Player</th>
                                        <th>Position</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($homeTeamPlayers as $player)
                                        <tr>
                                            <td>{{ $player->name }}</td>
                                            <td>{{ $player->position ?? 'N/A' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-chart-line"></i> Stats
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No players available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">{{ $match->awayClub->name ?? 'Away Team' }}</h6>
                </div>
                <div class="card-body">
                    @if($awayTeamPlayers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Player</th>
                                        <th>Position</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($awayTeamPlayers as $player)
                                        <tr>
                                            <td>{{ $player->name }}</td>
                                            <td>{{ $player->position ?? 'N/A' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-chart-line"></i> Stats
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No players available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
