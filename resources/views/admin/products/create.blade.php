@extends('layouts.admin')

@section('title', isset($product) ? 'Edit Product' : 'Add Product')

@section('header_styles')
<link rel="stylesheet" href="{{ asset('vendors/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/form_layouts.css') }}">
@endsection

@section('content')
<section class="content-header">
    <h1>{{ isset($product) ? 'Edit Product' : 'Add Product' }}</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="livicon" data-name="home" data-size="16" data-color="#000"></i> Dashboard
            </a>
        </li>
        <li><a href="#">Products</a></li>
        <li class="active">{{ isset($product) ? 'Edit' : 'Add' }}</li>
    </ol>
</section>

<section class="content ps-3 pe-3">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="my-2">{{ isset($product) ? 'Edit Product' : 'Add New Product' }}</h4>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf
                @isset($product) @method('PUT') @endisset

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select select2" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id ?? '') == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? '') }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" rows="5" class="form-control">{{ old('description', $product->description ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" name="image" class="form-control">

                            @isset($product)
                                @if($product->image)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($product->image) }}" class="img-fluid rounded" style="width:120px;" alt="Product Image">
                                    </div>
                                @endif
                            @endisset
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success">{{ isset($product) ? 'Update Product' : 'Save Product' }}</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('footer_scripts')
<script src="{{ asset('vendors/select2/js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endsection
