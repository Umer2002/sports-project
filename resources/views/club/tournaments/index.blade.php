@extends('layouts.club-dashboard')
@section('title', 'Tournaments')
@section('page_title', 'Tournaments')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4 class="mb-0" style="color: #000;"> Tournaments</h4>
    <a href="{{ route('club.tournaments.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Tournament
    </a>
</div>

@include('partials.alerts')

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover table-bordered text-white align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Host Club</th>
                    <th>Format</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Location</th>
                    <th>Schedule</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tournaments as $tournament)
                    <tr>
                        <td>{{ $tournament->name }}</td>
                        <td>{{ $tournament->hostClub->name ?? '-' }}</td>
                        <td>{{ $tournament->format->name ?? '-' }}</td>
                        <td>{{ $tournament->start_date ? \Carbon\Carbon::parse($tournament->start_date)->format('M d, Y') : '-' }}</td>
                        <td>{{ $tournament->end_date ? \Carbon\Carbon::parse($tournament->end_date)->format('M d, Y') : '-' }}</td>
                        <td>{{ $tournament->location ?? '-' }}</td>
                        @php
                            $scheduleAllowed = ! $tournament->registration_cutoff_date || now()->greaterThanOrEqualTo($tournament->registration_cutoff_date);
                        @endphp
                        <td>
                            @if($scheduleAllowed)
                                <a href="{{ route('club.tournaments.schedule', $tournament) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-calendar-alt"></i> Manage
                                </a>
                            @else
                                <span class="badge bg-secondary">Opens {{ $tournament->registration_cutoff_date->format('M d, Y') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('club.tournaments.show', $tournament) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('club.tournaments.edit', $tournament) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('club.tournaments.destroy', $tournament) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tournament?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-trophy fa-3x mb-3"></i>
                                <p>No tournaments found. <a href="{{ route('club.tournaments.create') }}">Create your first tournament</a></p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($tournaments->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $tournaments->links() }}
</div>
@endif
@endsection
