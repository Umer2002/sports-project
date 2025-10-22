@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')
<main class="main-content p-4 text-white">
    <h2 class="mb-4">Create New Role</h2>

    <div class="card bg-dark text-white">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fa fa-users me-2"></i> Create Role
            </h5>
        </div>

        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.roles.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Role Name</label>
                    <input type="text"
                        id="name"
                        name="name"
                        class="form-control bg-dark text-white border-secondary @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="Enter role name"
                        required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary me-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        Create Role
                    </button>
                </div>
            </form>

        </div>
    </div>
</main>
@endsection
