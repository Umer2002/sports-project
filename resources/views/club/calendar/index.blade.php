@extends('layouts.club-dashboard')
@section('title', 'Calendar')
@section('page_title', 'Calendar')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4 class="mb-0">📅 Upcoming Events & Games</h4>
</div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Type</th>
                <th>Title</th>
                <th>Date</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
                <tr>
                    <td>Event</td>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->event_date }}</td>
                    <td>{{ $event->event_time }}</td>
                </tr>
            @endforeach
            @foreach ($games as $game)
                <tr>
                    <td>Game</td>
                    <td>{{ $game->awayClub->name ?? 'TBD' }}</td>
                    <td>{{ $game->match_date }}</td>
                    <td>{{ $game->match_time }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
