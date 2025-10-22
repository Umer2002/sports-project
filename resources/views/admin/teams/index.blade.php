@extends('layouts.admin')
@section('title', 'Teams')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm rounded-2xl">

            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-2xl" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <h5 class="card-title mb-4">Teams</h5>
                <a href="{{ route('admin.teams.wizard.step1') }}" class="btn btn-primary mb-4">Add Team</a>
                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Club</th>
                                <th>Logo</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teams as $team)
                                <tr>
                                    <td>{{ $team->name }}</td>
                                    <td>{{ $team->club->name ?? '-' }}</td>
                                    <td>
                                        @if($team->logo)
                                            <img src="{{ asset('storage/' . $team->logo) }}" width="40" class="img-thumbnail rounded-circle" alt="Team Logo">
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.teams.show', $team) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-eye"></i> View
                                        </a>
                                        <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="ti ti-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.teams.destroy', $team) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete team?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="ti ti-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No teams found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
