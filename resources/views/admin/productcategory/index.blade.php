@extends('layouts.admin')

@section('title', 'Product Categories')

@section('header_styles')
<link rel="stylesheet" href="{{ asset('vendors/datatables/css/dataTables.bootstrap5.css') }}">
@endsection

@section('content')
<section class="content-header">
    <h1>Product Categories</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="livicon" data-name="home" data-size="16"></i> Dashboard
            </a>
        </li>
        <li class="active">Product Categories</li>
    </ol>
</section>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="livicon" data-name="list-ul" data-size="16" data-c="#fff" data-hc="#fff"></i> Product Categories</h4>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('admin.productcategory.pull-woocommerce') }}">
                @csrf
                <button class="btn btn-warning btn-sm" type="submit"><i class="fa fa-download"></i> Pull Categories</button>
            </form>
            <a href="{{ route('admin.productcategory.create') }}" class="btn btn-light btn-sm">
                <i class="fa fa-plus"></i> Add Category
            </a>
        </div>
    </div>

    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="table">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.productcategory.edit', $category->id) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                {{-- <a href="{{ route('admin.productcategory.confirm-delete', $category->id) }}" data-bs-toggle="modal" data-bs-target="#delete_confirm" class="btn btn-sm btn-outline-danger">
                                    <i class="fa fa-trash"></i> Delete
                                </a> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
