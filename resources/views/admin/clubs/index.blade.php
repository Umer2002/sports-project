@extends('layouts.admin')

@section('title', 'Clubs')

@section('content')
<main class="main-content p-4 text-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">All Clubs</h2>
        <a href="{{ route('admin.clubs.create') }}" class="btn btn-success">
            <i class="fa fa-plus"></i> Add Club
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover table-bordered text-white align-middle">
                <thead class="table-light text-dark">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clubs as $club)
                        <tr>
                            <td>{{ $club->name }}</td>
                            <td>{{ $club->email }}</td>
                            <td>{{ $club->is_registered ? 'Yes' : 'No' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.clubs.show', $club) }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-sm btn-warning">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.clubs.destroy', $club) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete club?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No clubs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $clubs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</main>
@endsection
