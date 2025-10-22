@extends('layouts.club-dashboard')
@section('title', 'Create Event')

@section('content')
<div class="container">
    <h2>Create Event</h2>

    <form action="{{ route('club.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Event Name</label>
            <input type="text" name="eventname" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="eventdate" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Time</label>
            <input type="time" name="eventtime" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="eventlocation" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Cover Photo</label>
            <input type="file" name="coverphoto" class="form-control">
        </div>
        <div class="mb-3">
            <label>Privacy</label>
            <select name="privacy" class="form-control">
                <option value="public">Public</option>
                <option value="private">Private</option>
            </select>
        </div>
        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection
