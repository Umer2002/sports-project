@extends('layouts.admin')

@section('title', 'Edit Player Stat')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Edit Player Stat</h2>
                    <p class="text-muted mb-0">Update player statistic information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.player-stats.show', $playerStat) }}" class="btn btn-outline-info">
                        <i class="bi bi-eye me-2"></i>View Details
                    </a>
                    <a href="{{ route('admin.player-stats.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Stats
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">Please fix the following errors:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Edit Form -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Edit Statistic</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.player-stats.update', $playerStat) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row g-3">
                                    <!-- Sport Selection -->
                                    <div class="col-md-6">
                                        <label for="sport_id" class="form-label fw-semibold">Sport <span class="text-danger">*</span></label>
                                        <select name="sport_id" id="sport_id" class="form-select" required>
                                            <option value="">Choose a sport...</option>
                                            @foreach($sports as $sport)
                                                <option value="{{ $sport->id }}" {{ old('sport_id', $playerStat->sport_id) == $sport->id ? 'selected' : '' }}>
                                                    {{ $sport->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Stat 1 -->
                                    <div class="col-md-6">
                                        <label for="stat1_id" class="form-label fw-semibold">Stat 1 <span class="text-danger">*</span></label>
                                        <select name="stat1_id" id="stat1_id" class="form-select" required>
                                            <option value="">Choose stat...</option>
                                            @foreach($stats as $stat)
                                                <option value="{{ $stat->id }}" {{ old('stat1_id') == $stat->id ? 'selected' : '' }}>
                                                    {{ $stat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Stat 2 -->
                                    <div class="col-md-6">
                                        <label for="stat2_id" class="form-label fw-semibold">Stat 2 <span class="text-danger">*</span></label>
                                        <select name="stat2_id" id="stat2_id" class="form-select" required>
                                            <option value="">Choose stat...</option>
                                            @foreach($stats as $stat)
                                                <option value="{{ $stat->id }}" {{ old('stat2_id') == $stat->id ? 'selected' : '' }}>
                                                    {{ $stat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Stat 3 -->
                                    <div class="col-md-6">
                                        <label for="stat3_id" class="form-label fw-semibold">Stat 3 <span class="text-danger">*</span></label>
                                        <select name="stat3_id" id="stat3_id" class="form-select" required>
                                            <option value="">Choose stat...</option>
                                            @foreach($stats as $stat)
                                                <option value="{{ $stat->id }}" {{ old('stat3_id') == $stat->id ? 'selected' : '' }}>
                                                    {{ $stat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Stat 4 -->
                                    <div class="col-md-6">
                                        <label for="stat4_id" class="form-label fw-semibold">Stat 4 <span class="text-danger">*</span></label>
                                        <select name="stat4_id" id="stat4_id" class="form-select" required>
                                            <option value="">Choose stat...</option>
                                            @foreach($stats as $stat)
                                                <option value="{{ $stat->id }}" {{ old('stat4_id') == $stat->id ? 'selected' : '' }}>
                                                    {{ $stat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Update Statistic
                                    </button>
                                </div>
                            </form>
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
                            <h5 class="mb-0">Danger Zone</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Once you delete this statistic, there is no going back. Please be certain.</p>
                            
                            <form action="{{ route('admin.player-stats.destroy', $playerStat) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this stat? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-trash me-2"></i>Delete Statistic
                                </button>
                            </form>
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

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
</style>
@endpush
