@extends('layouts.club-dashboard')
@section('title','Store Orders')
@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Orders</h3>
    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Order #</th><th>Date</th><th>Customer</th><th>Status</th><th>Total</th><th></th></tr></thead>
                <tbody>
                @forelse($orders as $o)
                    <tr>
                        <td>{{ $o->order_number }}</td>
                        <td>{{ $o->created_at->format('M d, Y H:i') }}</td>
                        <td>{{ $o->user->name ?? '—' }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($o->status) }}</span></td>
                        <td>${{ number_format($o->total_amount,2) }}</td>
                        <td><a href="{{ route('club.store.orders.show', $o) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No orders found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="card-footer">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection

