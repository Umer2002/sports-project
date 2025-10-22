@extends('layouts.admin')
@section('title', 'Referees')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between">
        <h5 class="mb-0">Referee List</h5>
        <a href="{{ route('admin.referees.create') }}" class="btn btn-light btn-sm">+ Add Referee</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($referees as $ref)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $ref->full_name }}</td>
                    <td>{{ $ref->email }}</td>
                    <td>{{ $ref->phone }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.referees.edit', $ref) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.referees.destroy', $ref) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete referee?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
