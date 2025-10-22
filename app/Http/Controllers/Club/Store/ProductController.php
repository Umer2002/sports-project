<?php

namespace App\Http\Controllers\Club\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $club = Auth::user()->club;
        abort_unless($club, 403);
        $products = Product::where('club_id', $club->id)->with('category')->latest()->paginate(15);
        return view('club.store.products.index', compact('products'));
    }

    public function create()
    {
        $club = Auth::user()->club;
        abort_unless($club, 403);
        $categories = ProductCategory::where('club_id', $club->id)->get();
        return view('club.store.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $club = Auth::user()->club; abort_unless($club, 403);
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.5',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'image' => 'nullable|image|max:4096',
        ]);
        $data['club_id'] = $club->id;
        $data['slug'] = \Str::slug($data['name']) . '-' . \Str::random(6);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        Product::create($data);
        return redirect()->route('club.store.products.index')->with('success', 'Product created');
    }
}

