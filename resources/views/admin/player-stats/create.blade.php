@extends('layouts.admin')

@section('title', 'Add Game Stats')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1">Add Game Statistics</h2>
                    <p class="text-muted mb-0">Record player performance for a specific game</p>
                </div>
                <div>
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

            <!-- Sport Configuration Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Sport Stat Configuration</h5>
                </div>
                <div class="card-body">
                    <form id="sportStatsForm" action="{{ route('admin.player-stats.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Sport Selection -->
                            <div class="col-md-6">
                                <label for="sport_id" class="form-label fw-semibold">Select Sport <span class="text-danger">*</span></label>
                                <select name="sport_id" id="sport_id" class="form-select" required>
                                    <option value="">Choose a sport...</option>
                                    @foreach($sports as $sport)
                                        <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
                                            {{ $sport->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Select the sport to configure stats for</div>
                            </div>
                        </div>
                        
                        <!-- Stats Selection (initially hidden) -->
                        <div id="stats-selection" style="display: none;">
                            <div class="row g-3 mt-3">
                                <div class="col-md-3">
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
                                <div class="col-md-3">
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
                                <div class="col-md-3">
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
                                <div class="col-md-3">
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
                        </div>
                    </form>
                </div>
            </div>


            <!-- Save Configuration Button -->
            <div class="d-flex justify-content-end gap-2 mt-4" id="save-config-section" style="display: none;">
                <button type="submit" form="sportStatsForm" class="btn btn-primary" id="saveStatsBtn">
                    <i class="bi bi-save me-2"></i>Save Configuration
                </button>
            </div>

            <!-- Players Stats Entry Section -->
            <div class="card" id="players-stats-card" style="display: none;">
                <div class="card-header">
                    <h5 class="mb-0">Player Statistics Entry</h5>
                    <small class="text-muted">Enter values for each player's selected statistics</small>
                </div>
                <div class="card-body">
                    <div id="players-stats-container">
                        <!-- Player stats will be loaded dynamically -->
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-primary btn-lg" id="saveStatsBtn">
                            <i class="bi bi-save me-2"></i>Save Game Statistics
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stat-option {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 1rem;
}

.stat-option:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.stat-option.selected {
    border-color: #0d6efd;
    background-color: #e7f3ff;
}

.stat-option input[type="checkbox"] {
    display: none;
}

.player-stats-row {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    background-color: #f8f9fa;
}

.player-stats-row .player-info {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.player-stats-row .player-info .avatar {
    width: 40px;
    height: 40px;
    background-color: #0d6efd;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-weight: 600;
}

.stats-inputs {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stats-inputs .form-group {
    margin-bottom: 0;
}

.stats-inputs label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.25rem;
}

.stats-inputs input {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 0.5rem;
    font-size: 0.875rem;
}

.stats-inputs input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sportSelect = document.getElementById('sport_id');
    const saveStatsBtn = document.getElementById('saveStatsBtn');
    const sportStatsForm = document.getElementById('sportStatsForm');

    // Sport selection handler
    sportSelect.addEventListener('change', function() {
        const sportId = this.value;
        const saveConfigSection = document.getElementById('save-config-section');
        const statsSelection = document.getElementById('stats-selection');
        
        if (sportId) {
            statsSelection.style.display = 'block';
            saveConfigSection.style.display = 'flex';
        } else {
            statsSelection.style.display = 'none';
            saveConfigSection.style.display = 'none';
        }
    });

    // Save configuration
    saveStatsBtn.addEventListener('click', function() {
        // Validate that all stats are selected
        const stat1Id = document.getElementById('stat1_id').value;
        const stat2Id = document.getElementById('stat2_id').value;
        const stat3Id = document.getElementById('stat3_id').value;
        const stat4Id = document.getElementById('stat4_id').value;
        
        if (!stat1Id || !stat2Id || !stat3Id || !stat4Id) {
            alert('Please select all 4 statistics.');
            return;
        }
        
        // Check for duplicates
        const selectedStats = [stat1Id, stat2Id, stat3Id, stat4Id];
        const uniqueStats = [...new Set(selectedStats)];
        
        if (uniqueStats.length !== selectedStats.length) {
            alert('Please select 4 different statistics.');
            return;
        }
        
        // Submit the form
        sportStatsForm.submit();
    });
});
</script>
@endpush
