@extends('layouts.admin')
@section('title', 'Blog List')

@section('content')
<section class="content-header">
    <h1>Blog List</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="livicon" data-name="home" data-size="16" data-color="#000"></i>
                Dashboard
            </a>
        </li>
        <li><a href="#">Blog</a></li>
        <li class="active">Blog List</li>
    </ol>
</section>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary text-white clearfix">
                    <h4 class="card-title float-start">
                        <i class="livicon" data-name="users" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                        Blog List
                    </h4>
                    <div class="float-end">
                        <a href="{{ route('admin.blog.create') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-plus"></i> Create
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Views</th>
                                    <th>Clicks</th>
                                    <th>Comments</th>
                                    <th>Created At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($blogs as $blog)
                                    <tr>
                                        <td>{{ $blog->id }}</td>
                                        <td>{{ $blog->title }}</td>
                                        <td>{{ $blog->views }}</td>
                                        <td>{{ $blog->clicks }}</td>
                                        <td>{{ $blog->comments->count() }}</td>
                                        <td>{{ $blog->created_at->diffForHumans() }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.blog.show', $blog->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.blog.edit', $blog->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#delete_confirm" data-id="{{ $blog->id }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
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

<!-- Delete Modal -->
<div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="deleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="deleteLabel">Delete Blog</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure to delete this blog? This operation is irreversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a class="btn btn-danger Remove_square">Delete</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<script src="{{ asset('vendors/datatables/js/dataTables.js') }}"></script>
<script src="{{ asset('vendors/datatables/js/dataTables.bootstrap5.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#table').DataTable();

        $('#delete_confirm').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var url = '{{ url('admin/blog') }}/' + id + '/delete';
            $(this).find('.modal-footer a').attr('href', url);
        });
    });
</script>
@endsection
