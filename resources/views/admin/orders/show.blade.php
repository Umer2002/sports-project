@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Order Details - {{ $order->order_number }}</h4>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Back to Orders</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Order Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Order Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Order Number:</strong></td>
                                            <td>{{ $order->order_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
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
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Status:</strong></td>
                                            <td>
                                                @if($order->payment_status === 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($order->payment_status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">{{ ucfirst($order->payment_status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($order->tracking_number)
                                            <tr>
                                                <td><strong>Tracking Number:</strong></td>
                                                <td>{{ $order->tracking_number }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Customer Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $order->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $order->user->email }}</td>
                                        </tr>
                                        @if($order->shipping_address)
                                            <tr>
                                                <td><strong>Shipping Address:</strong></td>
                                                <td>{{ $order->shipping_address }}</td>
                                            </tr>
                                        @endif
                                        @if($order->billing_address)
                                            <tr>
                                                <td><strong>Billing Address:</strong></td>
                                                <td>{{ $order->billing_address }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Order Items</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->orderItems as $item)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if($item->product && $item->product->image)
                                                                    <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                                         alt="{{ $item->product_name }}" 
                                                                         class="me-3" 
                                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                                @endif
                                                                <div>
                                                                    <h6 class="mb-0">{{ $item->product_name }}</h6>
                                                                    @if($item->product)
                                                                        <small class="text-muted">{{ $item->product->description }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>${{ number_format($item->unit_price, 2) }}</td>
                                                        <td>${{ number_format($item->total_price, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td>Subtotal:</td>
                                            <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tax:</td>
                                            <td class="text-end">${{ number_format($order->tax, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Shipping:</td>
                                            <td class="text-end">${{ number_format($order->shipping, 2) }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Total:</strong></td>
                                            <td class="text-end"><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Order Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        @if($order->status === 'pending')
                                            <button type="button" 
                                                    class="btn btn-success"
                                                    onclick="updateOrderStatus('processing')">
                                                Mark as Processing
                                            </button>
                                        @elseif($order->status === 'processing')
                                            <button type="button" 
                                                    class="btn btn-info"
                                                    onclick="shipOrder()">
                                                Mark as Shipped
                                            </button>
                                        @elseif($order->status === 'shipped')
                                            <button type="button" 
                                                    class="btn btn-success"
                                                    onclick="updateOrderStatus('delivered')">
                                                Mark as Delivered
                                            </button>
                                        @endif
                                        
                                        @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                                            <button type="button" 
                                                    class="btn btn-danger"
                                                    onclick="cancelOrder()">
                                                Cancel Order
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
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
function updateOrderStatus(status) {
    if (confirm('Are you sure you want to update this order status?')) {
        fetch(`/admin/orders/{{ $order->id }}/status`, {
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

function shipOrder() {
    const modal = new bootstrap.Modal(document.getElementById('shipOrderModal'));
    modal.show();
}

function cancelOrder() {
    if (confirm('Are you sure you want to cancel this order?')) {
        fetch(`/admin/orders/{{ $order->id }}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error cancelling order: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelling order');
        });
    }
}

document.getElementById('shipOrderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const trackingNumber = document.getElementById('tracking_number').value;
    
    fetch(`/admin/orders/{{ $order->id }}/ship`, {
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
