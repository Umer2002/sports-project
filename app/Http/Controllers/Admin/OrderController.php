<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:255',
        ]);

        $order->update([
            'status' => $request->status,
            'tracking_number' => $request->tracking_number,
        ]);

        // Update timestamps based on status
        switch ($request->status) {
            case 'shipped':
                $order->update(['shipped_at' => now()]);
                break;
            case 'delivered':
                $order->update(['delivered_at' => now()]);
                break;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully'
            ]);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully');
    }

    public function markAsShipped(Request $request, Order $order)
    {
        $request->validate([
            'tracking_number' => 'nullable|string|max:255',
        ]);

        $order->markAsShipped($request->tracking_number);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order marked as shipped'
            ]);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order marked as shipped');
    }

    public function markAsDelivered(Order $order)
    {
        $order->markAsDelivered();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order marked as delivered'
            ]);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order marked as delivered');
    }

    public function cancel(Order $order)
    {
        $order->update([
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);
        }

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order cancelled successfully');
    }

    public function export(Request $request)
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->date_from, function ($query, $date) {
                return $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                return $query->whereDate('created_at', '<=', $date);
            })
            ->latest()
            ->get();

        $filename = 'orders_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Order Number',
                'Customer',
                'Email',
                'Status',
                'Payment Status',
                'Subtotal',
                'Tax',
                'Shipping',
                'Total',
                'Created At',
                'Items'
            ]);

            foreach ($orders as $order) {
                $items = $order->orderItems->map(function ($item) {
                    return $item->product_name . ' (x' . $item->quantity . ')';
                })->implode(', ');

                fputcsv($file, [
                    $order->order_number,
                    $order->user->name,
                    $order->user->email,
                    $order->status,
                    $order->payment_status,
                    $order->subtotal,
                    $order->tax,
                    $order->shipping,
                    $order->total_amount,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $items
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
