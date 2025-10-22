@extends('layouts.admin')

@section('title', 'All Rewards')

@section('content')
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Reward List</h5>
        <a href="{{ route('admin.rewards.create') }}" class="btn btn-success btn-sm float-end">+ Add Reward</a>
    </div>
    <div class="card-body table-responsive">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Achievement</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rewards as $reward)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $reward->name }}</td>
                    <td>{{ ucfirst($reward->type) }}</td>
                    <td>{{ $reward->achievement }}</td>
                    <td>
                        @if($reward->image)
                            <img src="{{ asset('images/' . $reward->image) }}" alt="Image" width="80">
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.rewards.edit', $reward) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.rewards.destroy', $reward) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this reward?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No rewards found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
