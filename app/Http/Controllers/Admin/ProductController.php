<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Services\WooCommerceImporter;

class ProductController extends Controller
{
    public function __construct()
    {
        // Middleware to ensure only authorized users can access these routes
        // $this->middleware('can:manage-products');
    }

    // Display all products
    public function index()
    {
        $products = Product::with('category')->get(); // Eager load categories
        return view('admin.products.index', compact('products'));
    }

    // Show create form for a new product
    public function create()
    {
        $categories = ProductCategory::all(); // Fetch all categories for dropdown
        return view('admin.products.create', compact('categories'));
    }

    // Store a new product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:products,name',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validation for image upload
        ]);

        // Handle image upload if any
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/products');
        }

        // Store the product
        Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'image' => $imagePath, // Store the image path
            'club_id' => auth()->user()->club_id ?? null, // Club ID for club admins, or null for global
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    // Show edit form for an existing product
    public function edit(Product $product)
    {
        $categories = ProductCategory::all(); // Fetch all categories for dropdown
        return view('admin.products.edit', compact('product', 'categories'));
    }

    // Update an existing product
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|unique:products,name,' . $product->id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:product_categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validation for image upload
        ]);

        // Handle image upload if any
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/products');
            $product->image = $imagePath; // Update image
        }

        // Update product details
        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    // Delete a product
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    // Show confirmation for delete action
    public function confirmDelete(Product $product)
    {
        return view('admin.products.confirm-delete', compact('product'));
    }

    public function pullWooCommerce(WooCommerceImporter $woo)
    {
        if (!config('services.woocommerce.url') || !config('services.woocommerce.consumer_key') || !config('services.woocommerce.consumer_secret')) {
            return redirect()->route('admin.products.index')
                ->with('error', 'WooCommerce settings are missing. Please set WOOCOMMERCE_URL, WOOCOMMERCE_CONSUMER_KEY, WOOCOMMERCE_CONSUMER_SECRET in .env');
        }
        $cats = ProductCategory::all()->keyBy('slug');
        $data = $woo->products();
        $created = 0; $updated = 0;
        foreach ($data as $p) {
            $name = $p['name'] ?? null;
            if (!$name) continue;
            $slug = Str::slug($name);
            // ensure slug uniqueness by appending id if needed
            $baseSlug = $slug;
            if (\App\Models\Product::where('slug', $slug)->where('name', '!=', $name)->exists()) {
                $slug = $baseSlug.'-'.$p['id'];
            }

            $product = Product::firstOrNew(['slug' => $slug]);
            if (!$product->exists) { $created++; } else { $updated++; }

            $categoryId = null;
            $firstCat = $p['categories'][0]['name'] ?? null;
            if ($firstCat) {
                $catSlug = Str::slug($firstCat);
                $cat = ProductCategory::firstOrCreate(['slug' => $catSlug], ['name' => $firstCat]);
                $categoryId = $cat->id;
            }

            $price = $p['price'] ?? $p['regular_price'] ?? '0';
            $stock = $p['stock_quantity'] ?? 0;

            $product->fill([
                'name' => $name,
                'slug' => $slug,
                'price' => is_numeric($price) ? $price : 0,
                'stock' => is_numeric($stock) ? $stock : 0,
                'category_id' => $categoryId,
                'description' => strip_tags($p['description'] ?? ''),
                'club_id' => $product->club_id ?: null,
            ]);
            $product->save();
        }

        return redirect()->route('admin.products.index')
            ->with('success', "Pulled products: created {$created}, updated {$updated}");
    }
}
