@extends('layouts.coach-dashboard')
@section('title', 'Players')
@section('page_title', 'Players')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>All Players</h2>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Team</th>
                            <th>Position</th>
                            <th>Jersey #</th>
                            <th>Sport</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($players as $player)
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
                                <td>
                                    @if($player->team)
                                        <span class="badge bg-info">{{ $player->team->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">No Team</span>
                                    @endif
                                </td>
                                <td>{{ $player->position->name ?? 'N/A' }}</td>
                                <td>{{ $player->jersey_no ?? 'N/A' }}</td>
                                <td>{{ $player->sport->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('coach.players.show', $player->id) }}" class="btn btn-sm btn-primary composer-primary">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-user-friends fa-3x mb-3"></i>
                                    <p>No players found in your teams.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $players->links() }}
    </div>
</div>
@endsection

