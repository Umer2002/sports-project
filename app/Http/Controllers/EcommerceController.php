<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class EcommerceController extends Controller
{
    /**
     * Display the player-facing product listing with filtering and sorting.
     */
    public function shop(Request $request)
    {
        $query = Product::with('category');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort results
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(9)->withQueryString();
        $categories = ProductCategory::all();

        return view('players.ecommerce.shop', compact('products', 'categories'));
    }
}

