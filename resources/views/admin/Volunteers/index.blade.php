@extends('layouts.admin')

{{-- Page title --}}
@section('title')
Volunteers List
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/datatables/css/dataTables.bootstrap5.css') }}" />
<link href="{{ asset('css/pages/tables.css') }}" rel="stylesheet" type="text/css" />
@stop

{{-- Page content --}}
@section('content')
<section class="content-header">
    <h1>Volunteers</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="livicon" data-name="home" data-size="14" data-color="#000"></i>
                Dashboard
            </a>
        </li>
        <li><a href="#">Volunteers</a></li>
        <li class="active">Volunteers List</li>
    </ol>
</section>

<!-- Main content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title my-2 float-start">
                        <i class="livicon" data-name="user" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                        Volunteers List
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered width100" id="table">
                            <thead>
                                <tr class="filters">
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Users E-mail</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <!-- <th>Volunteer Type</th> -->
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->status }}</td>
                                    <td>{{ $user->created_at }}</td>
                                    <td>
                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.users.edit', $user->id) }}">
                                            <i class="livicon" data-name="edit" data-size="18" data-loop="true"
                                                data-c="#428BCA" data-hc="#428BCA" title="Edit Volunteer"></i>
                                        </a>

                                        <!-- Delete Button -->
                                        <!-- <a href="#" data-bs-toggle="modal" data-bs-target="#delete_confirm_{{ $user->id }}" class="ms-2">
                                                    <i class="livicon" data-name="remove-alt" data-size="18" data-loop="true"
                                                        data-c="#f56954" data-hc="#f56954" title="Delete Volunteer"></i>
                                                </a> -->
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

{{-- page level scripts --}}
@section('footer_scripts')
<script type="text/javascript" src="{{ asset('vendors/datatables/js/dataTables.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendors/datatables/js/dataTables.bootstrap5.js') }}"></script>
<script>
    $(function() {
        $('#table').DataTable();
    });

    var $url_path = '{!! url(' / ') !!}';
    $('#delete_confirm').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var $recipient = button.data('id');
        var modal = $(this)
        modal.find('.modal-footer a').prop("href", $url_path + "/admin/volunteers/" + $recipient + "/delete");
    })
</script>
@stop
