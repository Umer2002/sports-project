@extends('layouts.admin')

@section('title', 'Positions')

@section('content')
<section class="content-header">
    <h1>Player Positions</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">Positions</li>
    </ol>
</section>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
        <h4 class="mb-0">All Positions</h4>
        <a href="{{ route('admin.positions.create') }}" class="btn btn-light btn-sm">
            <i class="fa fa-plus"></i> Add Position
        </a>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Position Name</th>
                        <th>Position Value</th>
                        <th>Sport</th>
                        <th>Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($positions as $position)
                        <tr>
                            <td>{{ $position->id }}</td>
                            <td>{{ $position->position_name }}</td>
                            <td>{{ $position->position_value }}</td>
                            <td>{{ $position->sport->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $position->is_active ? 'success' : 'secondary' }}">
                                    {{ $position->is_active ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.positions.edit', $position->id) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.positions.destroy', $position->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this position?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No positions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
