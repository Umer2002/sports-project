@extends('layouts.club-dashboard')
@section('title', 'Create Team - Step 1')
@section('page_title', 'Create Team - Step 1')

@section('header_styles')
    <!-- Include if you have custom styles for select2/summernote -->
@endsection

@section('content')
    <div class="row clearfix">
        @include('club.teams.wizard._progress')

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create New Team</h4>
                    <p class="card-subtitle">Step 1 of 4: Basic team information</p>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('club.teams.wizard.storeStep1') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Team Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Team Name *</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="Enter team name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Enter team description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Club Info --}}
                        <input type="hidden" name="club_id" value="{{ $club->id }}">
                        <input type="hidden" name="sport_id" value="{{ $club->sport_id }}">
                        <div class="mb-3">
                            <div class="alert alert-info mb-0">
                                <strong>Club:</strong> {{ $club->name }} | 
                                <strong>Sport:</strong> {{ $club->sport->name }}
                            </div>
                        </div>

                        {{-- Division --}}
                        <div class="mb-3">
                            <label for="division_id" class="form-label">Division *</label>
                            <select name="division_id" id="division_id" class="form-select @error('division_id') is-invalid @enderror" required>
                                <option value="">Select division</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                        {{ $division->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Available divisions are filtered by your club's sport.</small>
                            @error('division_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Logo --}}
                        <div class="mb-3">
                            <label for="logo" class="form-label">Team Logo</label>
                            <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('club.teams.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-arrow-right"></i> Continue to Eligibility
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
