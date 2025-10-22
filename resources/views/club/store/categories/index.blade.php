@extends('layouts.club-dashboard')
@section('title','Product Categories')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Categories</h3>
        <a href="{{ route('club.store.categories.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Category</a>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Name</th><th>Slug</th></tr></thead>
                <tbody>
                @forelse($categories as $c)
                    <tr><td>{{ $c->name }}</td><td>{{ $c->slug }}</td></tr>
                @empty
                    <tr><td colspan="2" class="text-center text-muted py-4">No categories yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="card-footer">{{ $categories->links() }}</div>
        @endif
    </div>
</div>
@endsection

