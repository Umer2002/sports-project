@extends('layouts.admin')
@section('title', 'Coaches')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-3">
        <h4 class="mb-0">👨‍🏫 Coach List</h4>
        <a href="{{ route('admin.coaches.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Coach
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Filter by Club</label>
                    <select name="club_id" class="form-select">
                        <option value="">All Clubs</option>
                        @foreach($clubs as $id => $name)
                            <option value="{{ $id }}" {{ (string) $selectedClubId === (string) $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Filter by Sport</label>
                    <select name="sport_id" class="form-select" {{ $selectedClubId ? 'disabled' : '' }}>
                        <option value="">All Sports</option>
                        @foreach($sports as $id => $name)
                            <option value="{{ $id }}" {{ (string) $selectedSportId === (string) $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @if($selectedClubId && $selectedSportId)
                        <input type="hidden" name="sport_id" value="{{ $selectedSportId }}">
                    @endif
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <a href="{{ route('admin.coaches.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
            @if($club)
                <div class="mt-3 text-muted">
                    Showing coaches for <strong>{{ $club->name }}</strong> (Sport: {{ $club->sport->name ?? 'N/A' }})
                </div>
            @elseif($selectedSportId)
                <div class="mt-3 text-muted">
                    Showing coaches for sport: <strong>{{ $sports->get($selectedSportId) ?? 'Unknown' }}</strong>
                </div>
            @endif
        </div>
    </div>

    <div class="card  shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-bordered text-white align-middle">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Sport</th>
                        <th>City</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coaches as $coach)
                        <tr>
                            <td>
                                @if($coach->photo)
                                    <img src="{{ asset('storage/' . $coach->photo) }}" alt="photo" width="50" class="rounded-circle">
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $coach->first_name }} {{ $coach->last_name }}</td>
                            <td>{{ $coach->sport->name ?? 'N/A' }}</td>
                            <td>{{ $coach->city }}</td>
                            <td>{{ $coach->email }}</td>
                            <td>{{ $coach->phone }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.coaches.edit', $coach->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('admin.coaches.destroy', $coach->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this coach?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">No coaches found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
