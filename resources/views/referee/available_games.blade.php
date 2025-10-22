@extends('layouts.referee-dashboard')

@section('title', 'Available Games')

@section('content')
<div class="card">
    <div class="header">
        <h2>Available Games</h2>
    </div>
    <div class="body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Teams</th>
                    <th>Age Group</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($availableGames as $game)
                    <tr>
                        <td>{{ $game->homeClub->name }} vs {{ $game->awayClub->name }}</td>
                        <td>{{ $game->age_group ?? 'N/A' }}</td>
                        <td>{{ $game->match_date }}</td>
                        <td>{{ $game->match_time }}</td>
                        <td>{{ $game->venue }}</td>
                        <td>
                            <form method="POST" action="{{ route('referee.matches.apply', $game) }}">
                                @csrf
                                <button class="btn btn-primary btn-sm">Accept</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No games available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
