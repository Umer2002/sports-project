<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('players.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        $order->load('orderItems.product');
        return view('players.orders.show', compact('order'));
    }
}

