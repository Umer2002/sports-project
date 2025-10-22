@extends('layouts.admin')
@section('title', 'View Category')

@section('content')
<section class="content-header">
    <h1>Category Details</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.productcategory.index') }}">Product Categories</a></li>
        <li class="active">View Category</li>
    </ol>
</section>

<section class="content ps-3 pe-3">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Category Info</h4>
        </div>

        <div class="card-body">
            <p><strong>Name:</strong> {{ $category->name }}</p>
            <p><strong>Slug:</strong> {{ $category->slug }}</p>
            <p><strong>Created At:</strong> {{ $category->created_at ? $category->created_at->format('d M Y') : '-' }}</p>

            <div class="mt-4">
                <a href="{{ route('admin.productcategory.edit', $category) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('admin.productcategory.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</section>
@endsection
