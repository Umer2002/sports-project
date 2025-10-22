@extends('layouts.shop')
@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Order {{ $order->order_number }}</h2>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><strong>Items</strong></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('storage/'.$item->product->image) }}" class="me-2 rounded" style="width:40px;height:40px;object-fit:cover;">
                                            @endif
                                            <span>{{ $item->product_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">${{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header"><strong>Summary</strong></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-1"><span>Tax</span><span>${{ number_format($order->tax, 2) }}</span></div>
                    <div class="d-flex justify-content-between mb-1"><span>Shipping</span><span>${{ number_format($order->shipping, 2) }}</span></div>
                    <div class="d-flex justify-content-between border-top pt-2"><strong>Total</strong><strong>${{ number_format($order->total_amount, 2) }}</strong></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><strong>Status</strong></div>
                <div class="card-body">
                    <div class="mb-2">Payment: <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'secondary' }}">{{ ucfirst($order->payment_status) }}</span></div>
                    <div>Order: <span class="badge bg-info">{{ ucfirst($order->status) }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

