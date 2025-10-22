@extends('layouts.admin')

@section('title', 'Products')

@section('header_styles')
<link rel="stylesheet" href="{{ asset('vendors/datatables/css/dataTables.bootstrap5.css') }}">
@endsection

@section('content')
<section class="content-header">
    <h1>Product List</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="livicon" data-name="home" data-size="16"></i> Dashboard
            </a>
        </li>
        <li class="active">Products</li>
    </ol>
</section>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="livicon" data-name="box" data-size="16" data-loop="true" data-c="#fff" data-hc="white"></i>
                Product List
            </h4>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.products.pull-woocommerce') }}">
                    @csrf
                    <button class="btn btn-warning btn-sm" type="submit"><i class="fa fa-download"></i> Pull Products</button>
                </form>
                <a href="{{ route('admin.products.create') }}" class="btn btn-light btn-sm">
                    <i class="fa fa-plus"></i> Add Product
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
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>{{ $product->created_at->diffForHumans() }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                   
                                </td>
                            </tr>
                        @endforeach
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
