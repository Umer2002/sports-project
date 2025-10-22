@extends('layouts.admin')
@section('title', 'Edit Event')

@section('content')
<div class="container">
    <h2>Edit Event</h2>

    <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Event Name</label>
            <input type="text" name="eventname" class="form-control" value="{{ $event->title }}" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ $event->description }}</textarea>
        </div>
        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="eventdate" class="form-control" value="{{ $event->event_date }}" required>
        </div>
        <div class="mb-3">
            <label>Time</label>
            <input type="time" name="eventtime" class="form-control" value="{{ $event->event_time }}" required>
        </div>
        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="eventlocation" class="form-control" value="{{ $event->location }}" required>
        </div>
        <div class="mb-3">
            <label>Cover Photo</label>
            <input type="file" name="coverphoto" class="form-control">
            @if($event->banner)
                <img src="{{ asset('storage/event/thumbnail/' . $event->banner) }}" width="80" class="mt-2">
            @endif
        </div>
        <div class="mb-3">
            <label>Privacy</label>
            <select name="privacy" class="form-control">
                <option value="public" @if($event->privacy == 'public') selected @endif>Public</option>
                <option value="private" @if($event->privacy == 'private') selected @endif>Private</option>
            </select>
        </div>
        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
