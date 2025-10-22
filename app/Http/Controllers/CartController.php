<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the user's cart.
     */
    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();
        $cartTotal = Auth::user()->cart_total;
        $itemCount = Auth::user()->cart_item_count;

        return view('cart.index', compact('cartItems', 'cartTotal', 'itemCount'));
    }

    /**
     * Add an item to the cart.
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user = Auth::user();

        // Enforce single-club cart: prevent mixing products from different clubs
        $existingClubId = $user->cartItems()->with('product')->get()->pluck('product.club_id')->filter()->unique();
        if ($existingClubId->count() > 0) {
            $currentClubId = $existingClubId->first();
            if ($product->club_id && $product->club_id !== $currentClubId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart already contains items from another club. Please checkout or clear your cart before adding from a different club.'
                ], 422);
            }
        }

        // Check if item already exists in cart
        $existingItem = $user->cartItems()->where('product_id', $request->product_id)->first();

        if ($existingItem) {
            // Update quantity if item exists
            $newQuantity = $existingItem->quantity + $request->quantity;
            if ($newQuantity > 99) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum quantity limit reached (99 items)'
                ], 400);
            }

            $existingItem->update(['quantity' => $newQuantity]);
            $message = 'Cart updated successfully';
        } else {
            // Add new item to cart
            $user->cartItems()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
            $message = 'Item added to cart successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'cart_count' => $user->fresh()->cart_item_count,
            'cart_total' => $user->fresh()->cart_total,
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        // Ensure user owns this cart item
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        $user = Auth::user()->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cart_count' => $user->cart_item_count,
            'cart_total' => $user->cart_total,
            'item_total' => $cartItem->total_price,
        ]);
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(CartItem $cartItem): JsonResponse
    {
        // Ensure user owns this cart item
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $cartItem->delete();

        $user = Auth::user()->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $user->cart_item_count,
            'cart_total' => $user->cart_total,
        ]);
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): JsonResponse
    {
        Auth::user()->cartItems()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'cart_count' => 0,
            'cart_total' => 0,
        ]);
    }

    /**
     * Get cart summary for AJAX requests.
     */
    public function summary(): JsonResponse
    {
        $user = Auth::user();
        
        return response()->json([
            'cart_count' => $user->cart_item_count,
            'cart_total' => $user->cart_total,
        ]);
    }
}
