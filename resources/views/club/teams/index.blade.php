@extends('layouts.club-dashboard')
@section('title', 'Teams')
@section('page_title', 'Teams')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4 class="mb-0" style="color: #000;">Teams</h4>
    <a href="{{ route('club.teams.wizard.step1') }}" class="btn btn-primary theme-btn">
        <i class="fas fa-plus"></i> Add Team
    </a>
</div>

@include('partials.alerts')

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover table-bordered text-white align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Logo</th>
                    <th>Players</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teams as $team)
                    <tr>
                        <td>{{ $team->name }}</td>
                        <td>
                            @if($team->logo)
                                <img src="{{ asset('storage/' . $team->logo) }}" width="40" class="img-thumbnail rounded-circle" alt="Team Logo">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                            @endif
                        </td>
                        <td>{{ $team->players()->count() }} players</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('club.teams.show', $team) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('club.teams.edit', $team) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('club.teams.destroy', $team) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this team?')">
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
                        <td colspan="4" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <p>No teams found. <a href="{{ route('club.teams.wizard.step1') }}">Create your first team</a></p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($teams->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $teams->links() }}
</div>
@endif
@endsection
