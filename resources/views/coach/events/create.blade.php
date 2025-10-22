@extends('layouts.coach-dashboard')
@section('title', 'Create Event')
@section('page_title', 'Create Event')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Create New Event</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('coach.events.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Event Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="event_date" class="form-control" value="{{ old('event_date') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Time <span class="text-danger">*</span></label>
                                <input type="time" name="event_time" class="form-control" value="{{ old('event_time') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Team (Optional)</label>
                            <select name="team_id" class="form-select">
                                <option value="">General Event</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary composer-primary">
                                <i class="fas fa-save me-2"></i>Create Event
                            </button>
                            <a href="{{ route('coach.events.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

