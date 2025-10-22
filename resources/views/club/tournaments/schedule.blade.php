@extends('layouts.club-dashboard')

@section('title', 'Tournament Schedule')
@section('page_title', 'Tournament Schedule')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Schedule: {{ $tournament->name }}</h4>
            <p class="text-muted mb-0">Review and update the match schedule for this tournament.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('club.tournaments.show', $tournament) }}" class="btn btn-outline-light">
                <i class="fas fa-eye me-1"></i> View Tournament
            </a>
            <a href="{{ route('club.tournaments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to list
            </a>
        </div>
    </div>

    @include('partials.alerts')

    @if($matches->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                <p class="mb-2">There are no matches available to schedule yet.</p>
                <p class="small">Create tournament matches in the event calendar to manage their schedule here.</p>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('club.tournaments.schedule.store', $tournament) }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Home Team</th>
                                    <th>Away Team</th>
                                    <th style="width: 180px;">Date</th>
                                    <th style="width: 160px;">Time</th>
                                    <th style="width: 240px;">Venue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($matches as $index => $match)
                                    <tr>
                                        <td class="fw-semibold">
                                            {{ $match['home']->name }}
                                            <input type="hidden" name="matches[{{ $index }}][home_club_id]" value="{{ $match['home']->id }}">
                                        </td>
                                        <td class="fw-semibold">
                                            {{ $match['away']->name }}
                                            <input type="hidden" name="matches[{{ $index }}][away_club_id]" value="{{ $match['away']->id }}">
                                        </td>
                                        <td>
                                            <input type="date" class="form-control" name="matches[{{ $index }}][match_date]" value="{{ $match['match_date'] }}" required>
                                        </td>
                                        <td>
                                            <input type="time" class="form-control" name="matches[{{ $index }}][match_time]" value="{{ $match['match_time'] }}" required>
                                        </td>
                                        <td>
                                            <select class="form-select" name="matches[{{ $index }}][venue_id]" required>
                                                @foreach($venues as $venue)
                                                    <option value="{{ $venue->id }}" {{ $venue->id == $match['venue_id'] ? 'selected' : '' }}>
                                                        {{ $venue->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Update Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
