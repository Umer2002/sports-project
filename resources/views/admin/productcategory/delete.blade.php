@extends('layouts.admin')
@section('title', 'Delete Category')

@section('content')
<section class="content-header">
    <h1>Delete Category</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.productcategory.index') }}">Product Categories</a></li>
        <li class="active">Delete Category</li>
    </ol>
</section>

<section class="content ps-3 pe-3">
    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0">Confirm Deletion</h4>
        </div>

        <div class="card-body">
            <p>Are you sure you want to delete the category: <strong>{{ $category->name }}</strong>?</p>

            <form method="POST" action="{{ route('admin.productcategory.destroy', $category) }}">
                @csrf
                @method('DELETE')

                <div class="mt-4">
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    <a href="{{ route('admin.productcategory.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
