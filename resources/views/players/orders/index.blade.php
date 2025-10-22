@extends('layouts.shop')
@section('title', 'My Orders')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">My Orders</h2>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'secondary' }}">{{ ucfirst($order->payment_status) }}</span>
                                <span class="badge bg-info ms-1">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">No orders yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="card-footer">{{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</div>
@endsection
