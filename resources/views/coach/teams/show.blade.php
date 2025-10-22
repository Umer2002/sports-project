@extends('layouts.coach-dashboard')
@section('title', $team->name)
@section('page_title', $team->name)

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('coach.teams.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Teams
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                @if($team->logo)
                    <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}" 
                         class="rounded-circle me-3" style="width: 80px; height: 80px; object-fit: cover;">
                @else
                    <div class="rounded-circle me-3 bg-primary text-white d-flex align-items-center justify-content-center" 
                         style="width: 80px; height: 80px; font-weight: bold; font-size: 32px;">
                        {{ substr($team->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h2 class="mb-1">{{ $team->name }}</h2>
                    @if($team->sport)
                        <p class="text-muted mb-0">{{ $team->sport->name }}</p>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>Club:</strong> {{ $team->club->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Players:</strong> {{ $team->players->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Players</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Jersey #</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($team->players as $player)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($player->photo)
                                            <img src="{{ Storage::url($player->photo) }}" alt="{{ $player->name }}" 
                                                 class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <strong>{{ $player->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $player->position->name ?? 'N/A' }}</td>
                                <td>{{ $player->jersey_no ?? 'N/A' }}</td>
                                <td>{{ $player->email }}</td>
                                <td>
                                    <a href="{{ route('coach.players.show', $player->id) }}" class="btn btn-sm btn-info composer-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No players in this team yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

