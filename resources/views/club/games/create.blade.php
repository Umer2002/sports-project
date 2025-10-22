@extends('layouts.club-dashboard')
@section('title', 'Create Game')
@section('page_title', 'Create Game')
@section('content')
<div class="container">
    <h2>Create Game</h2>
    <form action="{{ route('club.games.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Opponent Club</label>
            <select name="away_club_id" class="form-control" required>
                @foreach ($clubs as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="match_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Time</label>
            <input type="time" name="match_time" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Venue</label>
            <input type="text" name="venue" class="form-control" required>
        </div>
        <button class="btn btn-success">Create Game</button>
    </form>
</div>
@endsection
