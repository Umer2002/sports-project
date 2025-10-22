@extends('layouts.club-dashboard')
@section('title','Add Category')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Add Category</h3>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('club.store.categories.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
                <a href="{{ route('club.store.categories.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
@endsection

