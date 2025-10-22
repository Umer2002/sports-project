@extends('layouts.admin')

@section('title', 'Role Management')

@section('content')
<main class="main-content p-4 text-white">
    <h2 class="mb-4">Manage Roles</h2>

    <div class="card bg-dark text-white">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary">
            <h5 class="mb-0">Roles</h5>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-light btn-sm">
                <i class="fa fa-plus"></i> Create Role
            </a>
        </div>
        <div class="card-body">

            @if($roles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-dark table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Users</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->users()->count() }}</td>
                                <td>{{ $role->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    @if($role->id !== 1)
                                        @if($role->users()->count())
                                            <button class="btn btn-sm btn-info users-exists" data-bs-toggle="modal" data-bs-target="#usersExistsModal">
                                                <i class="fa fa-exclamation-triangle"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-danger delete-role-btn"
                                                data-id="{{ $role->id }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteRoleModal">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="mt-3">No roles found.</p>
            @endif

        </div>
    </div>
</main>

<!-- Modals -->
<!-- Users Exist Modal -->
<div class="modal fade" id="usersExistsModal" tabindex="-1" aria-labelledby="usersExistsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="usersExistsModalLabel">Users Exist</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                This role cannot be deleted because users are assigned to it.
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRoleModalLabel">Delete Role</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this role? This action is irreversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteRole" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<script>
    const baseUrl = '{{ url('/') }}';

    document.querySelectorAll('.delete-role-btn').forEach(button => {
        button.addEventListener('click', function() {
            const roleId = this.dataset.id;
            document.getElementById('confirmDeleteRole').href = `${baseUrl}/admin/roles/${roleId}/delete`;
        });
    });
</script>
@endsection
