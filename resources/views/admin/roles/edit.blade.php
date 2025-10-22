@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
<main class="main-content p-4 text-white">
    <h2 class="mb-4">Edit Role</h2>

    <div class="card bg-dark text-white">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fa fa-wrench me-2"></i> Edit Role
            </h5>
        </div>
        <div class="card-body">

            @if($role)
                <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name</label>
                        <input type="text"
                            name="name"
                            id="name"
                            class="form-control bg-dark text-white border-secondary @error('name') is-invalid @enderror"
                            value="{{ old('name', $role->name) }}"
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Role Slug</label>
                        <input type="text"
                            id="slug"
                            class="form-control bg-dark text-white border-secondary"
                            value="{{ $role->slug }}"
                            readonly>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary me-2">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            Save Changes
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-danger">
                    Role not found.
                </div>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-light">
                    Back to Roles
                </a>
            @endif

        </div>
    </div>
</main>
@endsection
