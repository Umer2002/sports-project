@extends('layouts.referee-dashboard')

@section('title', 'Game Details')

@section('content')
<div class="card">
    <div class="header">
        <h2>Game Details</h2>
    </div>
    <div class="body">
        <p><strong>Teams:</strong> {{ $game->homeClub->name }} vs {{ $game->awayClub->name }}</p>
        <p><strong>Age Group:</strong> {{ $game->age_group ?? 'N/A' }}</p>
        <p><strong>Date:</strong> {{ $game->match_date }} at {{ $game->match_time }}</p>
        <p><strong>Location:</strong> {{ $game->venue }}</p>
    </div>
</div>
@endsection
