@extends('layouts.admin')
@section('content')
<div class="card p-4">
    <h4>Venue: {{ $venue->name }}</h4>
    <div class="row g-3">
        <div class="col-md-3"><strong>Country:</strong> {{ optional($venue->country)->name ?? '—' }}</div>
        <div class="col-md-3"><strong>State:</strong> {{ optional($venue->state)->name ?? '—' }}</div>
        <div class="col-md-3"><strong>City:</strong> {{ optional($venue->city)->name ?? '—' }}</div>
        <div class="col-md-3"><strong>Type:</strong> {{ $venue->type ?? '—' }}</div>
        <div class="col-12"><strong>Location:</strong> {{ $venue->location }}</div>
        <div class="col-12"><strong>Capacity:</strong> {{ $venue->capacity ?? '—' }}</div>
    </div>

    <h5 class="mt-4">Add Availability</h5>
    <form method="POST" action="{{ route('admin.venues.availability.store', $venue) }}">
        @csrf
        <div class="row g-2">
            <div class="col-md-3"><input type="date" name="available_date" class="form-control" required></div>
            <div class="col-md-3"><input type="time" name="start_time" class="form-control" required></div>
            <div class="col-md-3"><input type="time" name="end_time" class="form-control" required></div>
            <div class="col-md-3"><button class="btn btn-success w-100">Add</button></div>
        </div>
    </form>

    <h5 class="mt-4">Availability List</h5>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venue->availabilities as $slot)
                <tr>
                    <td>{{ $slot->available_date }}</td>
                    <td>{{ $slot->start_time }}</td>
                    <td>{{ $slot->end_time }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
