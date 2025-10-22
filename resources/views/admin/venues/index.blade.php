@extends('layouts.admin')
@section('content')
<div class="card p-4">
    <h4 class="mb-3">All Venues</h4>
    <a href="{{ route('admin.venues.create') }}" class="btn btn-primary mb-3">Add New Venue</a>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Country</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($venues as $venue)
                    <tr>
                        <td>{{ $venue->name }}</td>
                        <td>{{ optional($venue->city)->name ?? '—' }}</td>
                        <td>{{ optional($venue->state)->name ?? '—' }}</td>
                        <td>{{ optional($venue->country)->name ?? '—' }}</td>
                        <td>{{ $venue->location }}</td>
                        <td>{{ $venue->type ?? '—' }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('admin.venues.edit', $venue) }}" class="btn btn-sm btn-warning">Edit</a>
                            <a href="{{ route('admin.venues.show', $venue) }}" class="btn btn-sm btn-info">View</a>
                            <form action="{{ route('admin.venues.destroy', $venue) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this venue? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No venues found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
