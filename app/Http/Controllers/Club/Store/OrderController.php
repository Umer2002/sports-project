<?php

namespace App\Http\Controllers\Club\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $club = Auth::user()->club; abort_unless($club, 403);
        $orders = Order::with('orderItems')
            ->where('club_id', $club->id)
            ->latest()->paginate(15);
        return view('club.store.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $club = Auth::user()->club; abort_unless($club && $order->club_id === $club->id, 403);
        $order->load('orderItems.product', 'user');
        return view('club.store.orders.show', compact('order'));
    }
}

