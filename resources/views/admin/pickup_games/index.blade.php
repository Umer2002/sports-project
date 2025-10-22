@extends('layouts.admin')
@section('title', 'Pickup Games')
@section('content')
<div class="card">
    <div class="header">
        <h2>Pickup Games</h2>
        <a href="{{ route('admin.pickup_games.create') }}" class="btn btn-primary float-end">Create Game</a>
    </div>
    <div class="body table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Sport</th>
                    <th>Date/Time</th>
                    <th>Location</th>
                    <th>Players</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($games as $game)
                    <tr>
                        <td>{{ $game->sport->name }}</td>
                        <td>{{ $game->game_datetime }}</td>
                        <td>{{ $game->location }}</td>
                        <td>{{ $game->participants->count() }} / {{ $game->max_players }}</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $games->links() }}
    </div>
</div>
@endsection
