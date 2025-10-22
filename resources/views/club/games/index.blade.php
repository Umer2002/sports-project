@extends('layouts.club-dashboard')
@section('title', 'Games')
@section('page_title', 'Games')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Games</h2>
        <a href="{{ route('club.games.create') }}" class="btn btn-primary">Create Game</a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Opponent</th>
                <th>Date</th>
                <th>Time</th>
                <th>Venue</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($games as $game)
                <tr>
                    <td>{{ $game->awayClub->name ?? 'TBD' }}</td>
                    <td>{{ $game->match_date }}</td>
                    <td>{{ $game->match_time }}</td>
                    <td>{{ $game->venue }}</td>
                    <td>
                        <form action="{{ route('club.games.invite', $game->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            <input type="email" name="email" class="form-control form-control-sm d-inline-block w-auto" placeholder="Player email" required>
                            <button class="btn btn-sm btn-info">Invite</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
