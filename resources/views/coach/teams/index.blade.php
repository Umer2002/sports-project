@extends('layouts.coach-dashboard')
@section('title', 'My Teams')
@section('page_title', 'My Teams')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>My Teams</h2>
    </div>

    @if($teams->count() > 0)
        <div class="row">
            @foreach($teams as $team)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                @if($team->logo)
                                    <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}" 
                                         class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle me-3 bg-primary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px; font-weight: bold; font-size: 24px;">
                                        {{ substr($team->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <h5 class="card-title mb-0">{{ $team->name }}</h5>
                                    @if($team->sport)
                                        <small class="text-muted">{{ $team->sport->name }}</small>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <p class="mb-1"><i class="fas fa-users me-2"></i><strong>Players:</strong> {{ $team->players->count() }}</p>
                                @if($team->club)
                                    <p class="mb-0"><i class="fas fa-building me-2"></i><strong>Club:</strong> {{ $team->club->name }}</p>
                                @endif
                            </div>

                            <a href="{{ route('coach.teams.show', $team->id) }}" class="btn btn-primary w-100 composer-primary">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h4>No Teams Assigned</h4>
                <p class="text-muted">You haven't been assigned to any teams yet. Contact your club administrator to get assigned to teams.</p>
            </div>
        </div>
    @endif
</div>
@endsection

