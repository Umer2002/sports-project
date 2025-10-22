@extends('layouts.coach-dashboard')

@section('title', $tournament->name)
@section('page_title', $tournament->name)

@section('content')
<div class="container-fluid">
    <!-- Tournament Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($tournament->logo)
                                        <img src="{{ Storage::url($tournament->logo) }}" 
                                             alt="{{ $tournament->name }}" 
                                             class="rounded" 
                                             width="60" height="60">
                                    @else
                                        <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-trophy fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="mb-1" style="color: #000;">{{ $tournament->name }}</h4>
                                    <p class="text-muted mb-0">{{ $tournament->description ?? 'Tournament details' }}</p>
                                    <div class="mt-2">
                                        @if($tournament->sport)
                                            <span class="badge bg-info me-2" style="color: #000;">{{ $tournament->sport->name }}</span>
                                        @endif
                                        @php
                                            $status = $tournament->status ?? 'upcoming';
                                            $statusColors = [
                                                'upcoming' => 'warning',
                                                'ongoing' => 'success',
                                                'completed' => 'secondary',
                                                'cancelled' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h6 class="mb-0" style="color: #000;">{{ $tournament->teams->count() }}</h6>
                                    <small class="text-muted">Teams</small>
                                </div>
                                <div class="col-4">
                                    <h6 class="mb-0" style="color: #000;">{{ $matches->count() }}</h6>
                                    <small class="text-muted">Matches</small>
                                </div>
                                <div class="col-4">
                                    <h6 class="mb-0" style="color: #000;">{{ $matches->where('status', 'completed')->count() }}</h6>
                                    <small class="text-muted">Completed</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Matches Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tournament Matches</h5>
                    <div class="card-tools">
                        <span class="badge bg-primary">{{ $matches->count() }} Matches</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($matches->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Match</th>
                                        <th>Date & Time</th>
                                        <th>Venue</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($matches as $match)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 40px;">
                                                            <i class="fas fa-futbol"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">
                                                            {{ $match->homeClub->name ?? 'TBD' }} vs {{ $match->awayClub->name ?? 'TBD' }}
                                                        </h6>
                                                        <small class="text-muted">Match #{{ $match->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $match->match_date ? $match->match_date->format('M d, Y') : 'TBD' }}</strong>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $match->match_time ? $match->match_time->format('h:i A') : 'Time TBD' }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($match->venue)
                                                    <span class="text-truncate d-inline-block" style="max-width: 150px;">
                                                        {{ $match->venue }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">TBD</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($match->score && is_array($match->score))
                                                    <span class="badge bg-success">
                                                        {{ $match->score['home'] ?? 0 }} - {{ $match->score['away'] ?? 0 }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $matchStatus = $match->status ?? 'scheduled';
                                                    $statusColors = [
                                                        'scheduled' => 'warning',
                                                        'in_progress' => 'info',
                                                        'completed' => 'success',
                                                        'cancelled' => 'danger'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$matchStatus] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $matchStatus)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('coach.tournaments.match-stats', $match) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-chart-line"></i> Stats
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-futbol fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No Matches Scheduled</h5>
                            <p class="text-muted">Matches for this tournament will be announced soon.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
