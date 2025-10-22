@extends('layouts.admin')
@section('title', 'Blog Categories')

@section('content')
<section class="content-header">
    <h1>Blog Categories</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">Blog Categories</li>
    </ol>
</section>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
        <h4 class="mb-0">All Blog Categories</h4>
        <a href="{{ route('admin.blogcategory.create') }}" class="btn btn-light btn-sm">
            <i class="fa fa-plus"></i> Add Category
        </a>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Blogs</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blogscategories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->title }}</td>
                            <td>{{ $category->blog()->count() }}</td>
                            <td>{{ $category->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.blogcategory.edit', $category->id) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                @if($category->blog()->count())
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#blogcategory_exists" data-name="{{ $category->title }}">
                                        <i class="fa fa-exclamation-triangle"></i> In Use
                                    </button>
                                @else
                                    <form action="{{ route('admin.blogcategory.destroy', $category->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No blog categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Blog Category In Use Modal -->
<div class="modal fade" id="blogcategory_exists" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Delete Blog Category</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                This category is currently in use and cannot be deleted.
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
    });
</script>
@endsection
