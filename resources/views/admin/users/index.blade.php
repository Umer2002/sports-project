@extends('layouts.admin')

@section('title')
    Users List
    @parent
@stop

@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/datatables/css/dataTables.bootstrap5.css') }}" />
    <link href="{{ asset('css/pages/tables.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content')

    <section class="content-header">
        <h1>Users</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="14"></i> Dashboard</a>
            </li>
            <li><a href="#">Users</a></li>
            <li class="active">Users List</li>
        </ol>
    </section>

    <div class="row">
        <div class="col-12">
            <div class="card ">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title my-2 float-start">
                        <i class="livicon" data-name="user" data-size="16" data-loop="true" data-c="#fff"></i> Users List
                    </h4>
                    <div class="float-end">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-plus"></i> Add User
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-striped width100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        {{-- First Name --}}
                                        <td>
                                            @if ($user->player)
                                                {{ $user->player->first_name }}
                                            @elseif($user->coach)
                                                {{ $user->coach->first_name }}
                                            @else
                                                {{ '-' }}
                                            @endif
                                        </td>

                                        {{-- Last Name --}}
                                        <td>
                                            @if ($user->player)
                                                {{ $user->player->last_name }}
                                            @elseif($user->coach)
                                                {{ $user->coach->last_name }}
                                            @else
                                                {{ '-' }}
                                            @endif
                                        </td>

                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>
                                            @foreach ($user->roles as $role)
                                                <span class="badge bg-success">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $user->status == 1 ? 'Active' : 'Inactive' }}</td>
                                        <td>{{ $user->created_at->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                                class="btn btn-sm btn-warning">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

@stop

@section('footer_scripts')

    <script type="text/javascript" src="{{ asset('vendors/datatables/js/dataTables.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendors/datatables/js/dataTables.bootstrap5.js') }}"></script>

    <script>
        $(function() {
            $('#table').DataTable(); // no serverSide, no ajax
        });
    </script>

    <div class="modal fade" id="delete_confirm" tabindex="-1" aria-labelledby="deleteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Delete User</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this User? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a type="button" class="btn btn-danger Remove_square">Delete</a>
                </div>
            </div>
        </div>
    </div>

@stop
