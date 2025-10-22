@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Orders Management</h4>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['total_orders'] }}</h5>
                                    <small>Total Orders</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['pending_orders'] }}</h5>
                                    <small>Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['processing_orders'] }}</h5>
                                    <small>Processing</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5>{{ $stats['delivered_orders'] }}</h5>
                                    <small>Delivered</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h5>${{ number_format($stats['total_revenue'], 2) }}</h5>
                                    <small>Total Revenue</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-dark text-white">
                                <div class="card-body text-center">
                                    <a href="{{ route('admin.orders.export') }}" class="btn btn-sm btn-outline-light">
                                        Export CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $order->user->name }}</strong><br>
                                                <small class="text-muted">{{ $order->user->email }}</small>
                                            </div>
                                        </td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @switch($order->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                    @break
                                                @case('processing')
                                                    <span class="badge bg-info">Processing</span>
                                                    @break
                                                @case('shipped')
                                                    <span class="badge bg-primary">Shipped</span>
                                                    @break
                                                @case('delivered')
                                                    <span class="badge bg-success">Delivered</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($order->payment_status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($order->payment_status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-danger">{{ ucfirst($order->payment_status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.orders.show', $order) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>
                                                @if($order->status === 'pending')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success"
                                                            onclick="updateOrderStatus({{ $order->id }}, 'processing')">
                                                        Process
                                                    </button>
                                                @elseif($order->status === 'processing')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-info"
                                                            onclick="shipOrder({{ $order->id }})">
                                                        Ship
                                                    </button>
                                                @elseif($order->status === 'shipped')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success"
                                                            onclick="updateOrderStatus({{ $order->id }}, 'delivered')">
                                                        Deliver
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ship Order Modal -->
<div class="modal fade" id="shipOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ship Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="shipOrderForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tracking_number" class="form-label">Tracking Number</label>
                        <input type="text" class="form-control" id="tracking_number" name="tracking_number" placeholder="Enter tracking number">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Ship Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentOrderId = null;

function updateOrderStatus(orderId, status) {
    if (confirm('Are you sure you want to update this order status?')) {
        fetch(`/admin/orders/${orderId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating order status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating order status');
        });
    }
}

function shipOrder(orderId) {
    currentOrderId = orderId;
    const modal = new bootstrap.Modal(document.getElementById('shipOrderModal'));
    modal.show();
}

document.getElementById('shipOrderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const trackingNumber = document.getElementById('tracking_number').value;
    
    fetch(`/admin/orders/${currentOrderId}/ship`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ tracking_number: trackingNumber })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error shipping order: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error shipping order');
    });
});
</script>
@endpush
@endsection
