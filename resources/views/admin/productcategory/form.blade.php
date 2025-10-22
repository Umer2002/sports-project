@extends('layouts.admin')
@section('title', isset($category) ? 'Edit Category' : 'Create Category')

@section('content')
<section class="content-header">
    <h1>{{ isset($category) ? 'Edit Category' : 'Add Category' }}</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.productcategory.index') }}">Product Categories</a></li>
        <li class="active">{{ isset($category) ? 'Edit' : 'Add' }}</li>
    </ol>
</section>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">{{ isset($category) ? 'Edit Category' : 'Add New Category' }}</h4>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ isset($category) ? route('admin.productcategory.update', $category) : route('admin.productcategory.store') }}">
                @csrf
                @isset($category) @method('PUT') @endisset

                <div class="mb-3">
                    <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success">{{ isset($category) ? 'Update' : 'Save' }}</button>
                    <a href="{{ route('admin.productcategory.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
