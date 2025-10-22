<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\CartItem;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Show the checkout page.
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()->with('product')->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $subtotal = $cartItems->sum('total_price');
        $tax = $subtotal * 0.08; // 8% tax rate
        $shipping = 5.99; // Fixed shipping cost
        $total = $subtotal + $tax + $shipping;

        return view('checkout.index', compact('cartItems', 'subtotal', 'tax', 'shipping', 'total'));
    }

    /**
     * Create Stripe checkout session.
     */
    public function createCheckoutSession(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'billing_address' => 'required|string',
        ]);

        $user = Auth::user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ], 400);
        }

        $subtotal = $cartItems->sum('total_price');
        $tax = $subtotal * 0.08;
        $shipping = 5.99;
        $total = $subtotal + $tax + $shipping;

        // Create order first
        $order = Order::create([
            'user_id' => $user->id,
            'club_id' => optional($cartItems->first()->product)->club_id,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total_amount' => $total,
            'shipping_address' => $request->shipping_address,
            'billing_address' => $request->billing_address,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        // Create order items
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'product_name' => $cartItem->product->name,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->price,
                'total_price' => $cartItem->total_price,
                'old_price' => $cartItem->price, // keep legacy column satisfied
            ]);
        }

        // Prepare line items for Stripe
        $lineItems = [];
        foreach ($cartItems as $cartItem) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $cartItem->product->name,
                        'images' => $cartItem->product->image ? [asset('storage/' . $cartItem->product->image)] : [],
                    ],
                    'unit_amount' => (int) round(((float) $cartItem->price) * 100), // Convert to cents
                ],
                'quantity' => $cartItem->quantity,
            ];
        }

        // Add tax and shipping as separate line items
        if ($tax > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Tax',
                    ],
                    'unit_amount' => (int) round($tax * 100),
                ],
                'quantity' => 1,
            ];
        }

        if ($shipping > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Shipping',
                    ],
                    'unit_amount' => (int) round($shipping * 100),
                ],
                'quantity' => 1,
            ];
        }

        // Validate Stripe minimum amounts (50 cents minimum for USD)
        foreach ($lineItems as $li) {
            $amount = $li['price_data']['unit_amount'] ?? 0;
            if ($amount < 50) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart contains an item with price below $0.50, cannot create payment session.'
                ], 422);
            }
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                // Use current host + port and include order id for fallback
                'success_url' => url('/checkout/success') . '?session_id={CHECKOUT_SESSION_ID}&order=' . $order->id,
                'cancel_url' => url('/checkout/cancel'),
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                ],
                'customer_email' => $user->email,
            ]);

            // Update order with session ID
            $order->update(['stripe_session_id' => $session->id]);

            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'checkout_url' => $session->url,
            ]);

        } catch (\Exception $e) {
            // Delete the order if Stripe session creation fails
            $order->delete();
            Log::error('Stripe checkout session error', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'order_id' => $order->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Could not create checkout session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle successful checkout.
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        $orderId = $request->get('order');

        $order = null;

        // Prefer matching by Stripe session id
        if ($sessionId) {
            $order = Order::where('stripe_session_id', $sessionId)->first();
        }

        // Fallback by order id passed in success_url
        if (!$order && $orderId) {
            $order = Order::find($orderId);
        }

        if (!$order) {
            return redirect()->route('cart.index')->with('error', 'Order not found');
        }

        // Try retrieving session from Stripe (best-effort)
        if ($sessionId) {
            try {
                Session::retrieve($sessionId);
            } catch (\Exception $e) {
                // Continue; we will still present success using local order
            }
        }

        // Mark as paid if not already
        if ($order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'paid_at' => now(),
            ]);

            // Create payment record if not exists
            $player = optional($order->user)->player;

            $payment = Payment::firstOrCreate(
                ['stripe_session_id' => $sessionId, 'type' => 'player'],
                [
                    'user_id' => $order->user_id,
                    'player_id' => $player?->id,
                    'club_id' => $player?->club_id,
                    // store in cents, payments.amount is integer
                    'amount' => (int) round($order->total_amount * 100),
                    'currency' => 'usd',
                    'description' => 'Order payment for ' . $order->order_number,
                ]
            );

            if ($order->user && $order->user->hasRole('player')) {
                Invite::markAcceptedForUser($order->user);
            }
        }

        // Clear the cart by user id (do not rely on session auth)
        if ($order->user_id) {
            \App\Models\CartItem::where('user_id', $order->user_id)->delete();
        }

        return view('checkout.success', compact('order'));
    }

    /**
     * Handle cancelled checkout.
     */
    public function cancel()
    {
        return view('checkout.cancel');
    }

    /**
     * Handle Stripe webhook.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutSessionCompleted($session);
                break;
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle completed checkout session.
     */
    private function handleCheckoutSessionCompleted($session)
    {
        $order = Order::where('stripe_session_id', $session->id)->first();
        
        if ($order) {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'paid_at' => now(),
            ]);

            // Clear the user's cart server-side as a failsafe
            if ($order->user_id) {
                CartItem::where('user_id', $order->user_id)->delete();
            }
        }
    }

    /**
     * Handle successful payment intent.
     */
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        // Handle payment success if needed
    }

    /**
     * Handle failed payment intent.
     */
    private function handlePaymentIntentFailed($paymentIntent)
    {
        $order = Order::where('stripe_session_id', $paymentIntent->metadata->session_id ?? null)->first();
        
        if ($order) {
            $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
            ]);
        }
    }
}
