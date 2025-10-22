@extends('layouts.admin')

@section('content')
@include('partials.alerts')
    <div class="card p-4">
        <h4 class="mb-4">Schedule Preview for Tournament: {{ $tournament->name }}</h4>

        <form action="{{ route('admin.scheduler.store', $tournament) }}" method="POST">
            @csrf
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Home Team</th>
                        <th>Away Team</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Venue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($matches as $i => $match)
                        <tr>

                            <td>
                                {{ $match['home']->name }}
                                <input type="hidden" name="matches[{{ $i }}][home_club_id]"
                                    value="{{ $match['home']->id }}">
                            </td>
                            <td>
                                {{ $match['away']->name }}
                                <input type="hidden" name="matches[{{ $i }}][away_club_id]"
                                    value="{{ $match['away']->id }}">
                            </td>
                            <td>
                                <input type="date" name="matches[{{ $i }}][match_date]" class="form-control"
                                    value="{{ $match['match_date'] }}">
                            </td>
                            <td>
                                <input type="time" name="matches[{{ $i }}][match_time]" class="form-control"
                                    value="{{ $match['match_time'] }}">
                            </td>
                            <td>
                                <select name="matches[{{ $i }}][venue_id]" class="form-control">
                                    @foreach ($venues as $venue)
                                        <option value="{{ $venue->id }}"
                                            {{ $venue->id == $match['venue_id'] ? 'selected' : '' }}>
                                            {{ $venue->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button class="btn btn-primary">Update Schedule</button>
        </form>
    </div>
@endsection
