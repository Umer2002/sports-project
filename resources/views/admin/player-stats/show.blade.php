@extends('layouts.admin')

@section('title', 'Player Stat Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Player Stat Details</h2>
                    <p class="text-muted mb-0">View detailed information about this player statistic</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.player-stats.edit', $playerStat) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="{{ route('admin.player-stats.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Stats
                    </a>
                </div>
            </div>

            <!-- Stat Details -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Statistic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Player</label>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                            {{ strtoupper(substr($playerStat->player->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $playerStat->player->name }}</div>
                                            <small class="text-muted">{{ $playerStat->player->position->name ?? 'No Position' }}</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Team</label>
                                    <div>
                                        <div class="fw-medium">{{ $playerStat->player->team->name ?? 'No Team' }}</div>
                                        <small class="text-muted">{{ $playerStat->player->club->name ?? 'No Club' }}</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Statistic</label>
                                    <div>
                                        <span class="badge bg-info fs-6">{{ $playerStat->stat->name }}</span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Value</label>
                                    <div class="fs-5 fw-bold text-primary">{{ $playerStat->value }}</div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Game Date</label>
                                    <div>
                                        <div class="fw-medium">{{ $playerStat->game_date ? $playerStat->game_date->format('M j, Y') : '—' }}</div>
                                        <small class="text-muted">{{ $playerStat->game_date ? $playerStat->game_date->format('g:i A') : '' }}</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Location</label>
                                    <div class="text-muted">{{ $playerStat->game_location ?? '—' }}</div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Opponent</label>
                                    <div class="text-muted">{{ $playerStat->opponent_team ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Record Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Created</label>
                                <div>
                                    <div class="fw-medium">{{ $playerStat->created_at->format('M j, Y') }}</div>
                                    <small class="text-muted">{{ $playerStat->created_at->format('g:i A') }}</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Last Updated</label>
                                <div>
                                    <div class="fw-medium">{{ $playerStat->updated_at->format('M j, Y') }}</div>
                                    <small class="text-muted">{{ $playerStat->updated_at->format('g:i A') }}</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <div>
                                    @if($playerStat->is_current)
                                        <span class="badge bg-success">Current</span>
                                    @else
                                        <span class="badge bg-secondary">Historical</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.player-stats.edit', $playerStat) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil me-2"></i>Edit Stat
                                </a>
                                
                                <form action="{{ route('admin.player-stats.destroy', $playerStat) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this stat?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-trash me-2"></i>Delete Stat
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: 600;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.form-label {
    color: #495057;
    margin-bottom: 0.5rem;
}
</style>
@endpush
