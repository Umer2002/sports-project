@extends('layouts.club-dashboard')
@section('title','Store Products')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Products</h3>
        <a href="{{ route('club.store.products.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Product</a>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th></tr></thead>
                <tbody>
                @forelse($products as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->category->name ?? '-' }}</td>
                        <td>${{ number_format($p->price,2) }}</td>
                        <td>{{ $p->stock }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No products yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="card-footer">{{ $products->links() }}</div>
        @endif
    </div>
</div>
@endsection

