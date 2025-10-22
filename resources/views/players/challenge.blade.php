@extends('layouts.player')
@section('title','New Challenge')
@section('content')
<div class="card">
    <div class="card-header">Send Challenge</div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('player.challenge.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Challenge Type</label>
                <select name="challenge_type" class="form-control">
                    <option value="video">Video</option>
                    <option value="game">Game</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Video URL (optional)</label>
                <input type="url" name="video_url" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Game Info (optional)</label>
                <input type="text" name="game_info" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Send Challenge</button>
        </form>
    </div>
</div>
@endsection
