@extends('layouts.club-dashboard')
@section('title', 'Coaches')
@section('page_title', 'Coaches')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h4 class="mb-0" style="color: #000;">Coach List</h4>
    <a href="{{ route('club.coaches.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Coach
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
                    <th>Sport</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coaches as $coach)
                <tr>
                    <td>
                        @if($coach->photo)
                            <img src="{{ Storage::url($coach->photo) }}" alt="{{ $coach->first_name }}" class="rounded-circle" width="40" height="40">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        @endif
                    </td>
                    <td>{{ $coach->first_name }} {{ $coach->last_name }}</td>
                    <td>{{ $coach->email }}</td>
                    <td>{{ $coach->phone }}</td>
                    <td>{{ $coach->sport->name ?? 'N/A' }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('club.coaches.show', $coach) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('club.coaches.edit', $coach) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('club.coaches.destroy', $coach) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this coach?')">
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
                                <p>No coaches found. <a href="{{ route('club.coaches.create') }}">Add your first coach</a></p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($coaches->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $coaches->links() }}
</div>
@endif
@endsection
