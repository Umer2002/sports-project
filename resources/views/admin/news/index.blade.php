@extends('layouts.admin')
@section('title', 'News List')

@section('content')
    <section class="content-header">
        <h1>News List</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="16" data-color="#000"></i> Dashboard
                </a>
            </li>
            <li><a href="#">News</a></li>
            <li class="active">News List</li>
        </ol>
    </section>
    <div class="row">
        <div class="col-lg-12">
            <div class="card rounded-2xl shadow-sm border-0">
                <div class="card-header bg-primary text-white clearfix">
                    <h4 class="card-title float-start">
                        <i class="livicon" data-name="newspaper" data-size="16" data-loop="true" data-c="#fff"
                            data-hc="white"></i>
                        News List
                    </h4>
                    <div class="float-end">
                        <a href="{{ route('admin.news.create') }}" class="btn btn-sm btn-secondary">
                            <i class="fa fa-plus"></i> Add News
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="news_table">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Created At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($newsItems as $news)
                                    <tr>
                                        <td>{{ $news->id }}</td>
                                        <td>{{ $news->title }}</td>
                                        <td>{{ ucfirst($news->category) }}</td>
                                        <td>{{ $news->created_at->diffForHumans() }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.news.show', $news->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.news.edit', $news->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#delete_confirm" data-id="{{ $news->id }}">
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
    <div class="modal fade" id="delete_confirm" tabindex="-1" role="dialog" aria-labelledby="deleteLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-2xl border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteLabel">Delete News</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure to delete this news item? This operation is irreversible.
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" class="btn btn-danger" id="confirmDeleteBtn" data-id="">Delete</a>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer_scripts')
<script>
    $(document).ready(function() {
        // $('#news_table').DataTable();
        $('#delete_confirm').on('show.bs.modal', function(event) {
            deleteId = $(event.relatedTarget).data('id');
        });
        // $('#news_table').DataTable();
        let deleteUrl = '{{ url('admin/news') }}';

        $('#delete_confirm').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');

            $('#confirmDeleteBtn').data('id', id); // store id on delete button
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (!deleteId) return;

            $.ajax({
                url: '{{ url('admin/news') }}/' + deleteId,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function() {
                    $('#delete_confirm').modal('hide');
                    window.location.reload();
                    // $('#news_table').DataTable().ajax.reload(null, false);
                },
                error: function() {
                    alert('Failed to delete news.');
                }
            });
        });



    });
</script>
@endsection
