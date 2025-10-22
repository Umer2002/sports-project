@extends('layouts.club-dashboard')
@section('title','Add Product')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Add Product</h3>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('club.store.products.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">— None —</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Price (USD)</label>
                    <input type="number" name="price" class="form-control" step="0.01" min="0.5" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" min="0" value="0" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('club.store.products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
@endsection

