@extends('layouts.admin')
@section('title', 'Create Pickup Game')
@section('content')
<div class="card">
    <div class="header"><h2>Create Pickup Game</h2></div>
    <div class="body">
        <form method="POST" action="{{ route('admin.pickup_games.store') }}">
            @csrf
            <div class="mb-3">
                <label>Sport</label>
                <select name="sport_id" class="form-control" required>
                    @foreach ($sports as $sport)
                        <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Date & Time</label>
                <input type="datetime-local" name="game_datetime" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Location</label>
                <input type="text" name="location" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Latitude (optional)</label>
                <input type="text" name="latitude" class="form-control">
            </div>
            <div class="mb-3">
                <label>Longitude (optional)</label>
                <input type="text" name="longitude" class="form-control">
            </div>
            <div class="mb-3">
                <label>Max Players</label>
                <input type="number" name="max_players" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Privacy</label>
                <select name="privacy" class="form-control">
                    <option value="public">Public</option>
                    <option value="private">Private</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Join Fee</label>
                <input type="number" name="join_fee" class="form-control" step="0.01">
            </div>
            <button class="btn btn-success">Create Game</button>
        </form>
    </div>
</div>
@endsection
