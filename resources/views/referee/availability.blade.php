@extends('layouts.referee-dashboard')

@section('title', 'Availability Settings')
<style>
    input[type="checkbox"]{
    opacity: 1 !important;
}
</style>
@section('content')
<div class="container mt-4">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4>Set Your Weekly Availability</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('referee.availability.update') }}" method="POST">
                @csrf

                <div class="row">
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        <div class="col-md-6 mb-3">
                            <h6>{{ $day }}</h6>
                            <div class="form-check">
                                <input class="" type="checkbox" name="availability[{{ $day }}][available]" id="{{ $day }}_available" {{ old("availability.$day.available", $availability[$day]['available'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $day }}_available">Available</label>
                            </div>
                            <div class="row mt-2">
                                <div class="col">
                                    <input type="time" class="form-control" name="availability[{{ $day }}][from]" value="{{ old("availability.$day.from", $availability[$day]['from'] ?? '') }}">
                                </div>
                                <div class="col">
                                    <input type="time" class="form-control" name="availability[{{ $day }}][to]" value="{{ old("availability.$day.to", $availability[$day]['to'] ?? '') }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-success mt-3">Save Availability</button>
            </form>
        </div>
    </div>
</div>
@endsection
