@extends('layouts.coach-dashboard')
@section('title', $player->name)
@section('page_title', $player->name)

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('coach.players.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Players
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    @if($player->photo)
                        <img src="{{ Storage::url($player->photo) }}" alt="{{ $player->name }}" 
                             class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="rounded-circle mx-auto mb-3 bg-primary text-white d-flex align-items-center justify-content-center" 
                             style="width: 150px; height: 150px; font-weight: bold; font-size: 48px;">
                            {{ substr($player->name, 0, 1) }}
                        </div>
                    @endif
                    <h3>{{ $player->name }}</h3>
                    @if($player->position)
                        <p class="text-muted">{{ $player->position->name }}</p>
                    @endif
                    @if($player->jersey_no)
                        <h2 class="text-primary">#{{ $player->jersey_no }}</h2>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Player Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Email:</strong>
                            <p>{{ $player->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Phone:</strong>
                            <p>{{ $player->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Sport:</strong>
                            <p>{{ $player->sport->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Team:</strong>
                            <p>{{ $player->team->name ?? 'N/A' }}</p>
                        </div>
                        @if($player->birth_date)
                            <div class="col-md-6 mb-3">
                                <strong>Birth Date:</strong>
                                <p>{{ \Carbon\Carbon::parse($player->birth_date)->format('M d, Y') }}</p>
                            </div>
                        @endif
                        @if($player->height)
                            <div class="col-md-6 mb-3">
                                <strong>Height:</strong>
                                <p>{{ $player->height }} cm</p>
                            </div>
                        @endif
                        @if($player->weight)
                            <div class="col-md-6 mb-3">
                                <strong>Weight:</strong>
                                <p>{{ $player->weight }} kg</p>
                            </div>
                        @endif
                    </div>
                    @if($player->bio)
                        <div class="mt-3">
                            <strong>Bio:</strong>
                            <p>{{ $player->bio }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($player->stats && $player->stats->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Stat</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($player->stats as $stat)
                                        <tr>
                                            <td>{{ $stat->stat->name ?? 'N/A' }}</td>
                                            <td>{{ $stat->value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

