@extends('layouts.admin')
@section('title', 'Players')

@section('content')
<section class="content-header">
    <h1>Players</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">Players</li>
    </ol>
</section>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
        <h4 class="mb-0">All Players</h4>
        <a href="{{ route('admin.players.create') }}" class="btn btn-light btn-sm">
            <i class="fa fa-plus"></i> Add Player
        </a>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($players as $player)
                        <tr>
                            <td>{{ $player->name }}</td>
                            <td>{{ $player->email }}</td>
                            <td>
                                <span class="badge text-white bg-{{ $player->is_registered ? 'success' : 'secondary' }}">
                                    {{ $player->is_registered ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.players.edit', $player) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.players.destroy', $player) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete player?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No players found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
