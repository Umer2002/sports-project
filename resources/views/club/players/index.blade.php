@extends('layouts.club-dashboard')
@section('title', 'Players')
@section('page_title', 'Players')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4 class="mb-0" style="color: #000;">Player List</h4>
    <a href="{{ route('club.players.invite') }}" class="btn btn-primary theme-btn">
        <i class="fas fa-plus"></i> Invite Player
    </a>
</div>

@include('partials.alerts')

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover table-bordered text-white align-middle">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Age</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($players as $player)
                <tr>
                    <td>
                        @if($player->photo)
                            <img src="{{ Storage::url($player->photo) }}" alt="{{ $player->first_name }}" class="rounded-circle" width="40" height="40">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        @endif
                    </td>
                    <td>{{ $player->first_name }} {{ $player->last_name }}</td>
                    <td>{{ $player->email }}</td>
                    <td>{{ $player->phone }}</td>
                    <td>{{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : 'N/A' }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('club.players.show', $player) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('club.players.edit', $player) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('club.players.destroy', $player) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this player?')">
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
                    <td colspan="6" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>No players found. <a href="{{ route('club.players.invite') }}">Invite your first player</a></p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($players->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $players->links() }}
</div>
@endif
@endsection
