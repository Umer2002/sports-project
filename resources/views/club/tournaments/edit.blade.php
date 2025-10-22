@extends('layouts.club-dashboard')

@section('title', 'Edit Tournament')
@section('page_title', 'Edit Tournament')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title">Edit Tournament</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('club.tournaments.update', $tournament) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Tournament Name *</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $tournament->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror"
                                        value="{{ old('location', $tournament->location) }}">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date *</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                        value="{{ old('start_date', $tournament->start_date ? $tournament->start_date->format('Y-m-d') : '') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date *</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                        value="{{ old('end_date', $tournament->end_date ? $tournament->end_date->format('Y-m-d') : '') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="registration_cutoff_date" class="form-label">Registration Cutoff Date *</label>
                                    <input type="date" name="registration_cutoff_date" id="registration_cutoff_date" class="form-control @error('registration_cutoff_date') is-invalid @enderror"
                                        value="{{ old('registration_cutoff_date', optional($tournament->registration_cutoff_date)->format('Y-m-d')) }}" required>
                                    @error('registration_cutoff_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="joining_fee" class="form-label">Joining Fee (USD) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="joining_fee" id="joining_fee" class="form-control @error('joining_fee') is-invalid @enderror"
                                            value="{{ old('joining_fee', $tournament->joining_fee) }}" min="0" step="0.01" required>
                                    </div>
                                    @error('joining_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="joining_type" class="form-label">Joining Type *</label>
                                    @php($joiningType = old('joining_type', $tournament->joining_type))
                                    <select name="joining_type" id="joining_type" class="form-select @error('joining_type') is-invalid @enderror" required>
                                        <option value="per_club" {{ $joiningType === 'per_club' ? 'selected' : '' }}>Per Club</option>
                                        <option value="per_team" {{ $joiningType === 'per_team' ? 'selected' : '' }}>Per Team</option>
                                    </select>
                                    @error('joining_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_teams" class="form-label">Maximum Teams</label>
                                    <input type="number" name="max_teams" id="max_teams" class="form-control @error('max_teams') is-invalid @enderror"
                                        value="{{ old('max_teams', $tournament->max_teams) }}" min="2">
                                    @error('max_teams')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="entry_fee" class="form-label">Entry Fee ($)</label>
                                    <input type="number" name="entry_fee" id="entry_fee" class="form-control @error('entry_fee') is-invalid @enderror"
                                        value="{{ old('entry_fee', $tournament->entry_fee) }}" min="0" step="0.01">
                                    @error('entry_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $tournament->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('club.tournaments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Tournaments
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Tournament
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const cutoffDate = document.getElementById('registration_cutoff_date');

    // Set minimum date for start_date to today
    const today = new Date().toISOString().split('T')[0];
    startDate.min = today;
    if (cutoffDate) {
        cutoffDate.min = today;
    }

    // Update end_date minimum when start_date changes
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (cutoffDate) {
            cutoffDate.max = this.value || '';
            if (cutoffDate.value && this.value && cutoffDate.value > this.value) {
                cutoffDate.value = this.value;
            }
        }
    });

    // Set initial minimum for end_date if start_date has a value
    if (startDate.value) {
        endDate.min = startDate.value;
        if (cutoffDate) {
            cutoffDate.max = startDate.value;
        }
    }

    if (cutoffDate) {
        cutoffDate.addEventListener('change', function() {
            if (this.value && this.value < today) {
                this.value = today;
            }
            if (startDate.value && this.value > startDate.value) {
                this.value = startDate.value;
            }
        });
    }
});
</script>
@endsection
