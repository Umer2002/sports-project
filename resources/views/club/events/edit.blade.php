@extends('layouts.club-dashboard')
@section('title', 'Edit Event')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit Event</h2>
        <a href="{{ route('club.events.show', $event->id) }}" class="btn btn-secondary">Back to Event</a>
    </div>

    @include('partials.alerts')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('club.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="eventname" class="form-label">Event Name</label>
                    <input type="text" name="eventname" id="eventname" class="form-control" value="{{ $event->title }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ $event->description }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="eventdate" class="form-label">Date</label>
                            <input type="date" name="eventdate" id="eventdate" class="form-control" value="{{ $event->event_date }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="eventtime" class="form-label">Time</label>
                            <input type="time" name="eventtime" id="eventtime" class="form-control" value="{{ $event->event_time }}" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="eventlocation" class="form-label">Location</label>
                    <input type="text" name="eventlocation" id="eventlocation" class="form-control" value="{{ $event->location }}" required>
                </div>

                <div class="mb-3">
                    <label for="coverphoto" class="form-label">Cover Photo</label>
                    <input type="file" name="coverphoto" id="coverphoto" class="form-control">
                    @if($event->banner)
                        <small class="text-muted">Current: {{ $event->banner }}</small>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="privacy" class="form-label">Privacy</label>
                    <select name="privacy" id="privacy" class="form-control">
                        <option value="public" {{ $event->privacy === 'public' ? 'selected' : '' }}>Public</option>
                        <option value="private" {{ $event->privacy === 'private' ? 'selected' : '' }}>Private</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Event</button>
                    <a href="{{ route('club.events.show', $event->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 