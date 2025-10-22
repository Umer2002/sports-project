<?php
namespace App\Http\Controllers\Admin;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Services\WooCommerceImporter;

class ProductCategoryController extends Controller
{
    public function __construct()
    {
        // Only allow Super Admin and Club Admin access
        //$this->middleware('can:manage-product-categories');
    }

    public function index()
    {
        // Fetch product categories with their respective clubs
        $categories = ProductCategory::all(); // Can adjust with club-specific filtering if needed
        return view('admin.productcategory.index', compact('categories'));
    }

    public function create()
    {
        // Show form to create a new product category
        return view('admin.productcategory.form');
    }

    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'name' => 'required|unique:product_categories,name',
        ]);

        // Store the new category
        ProductCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'club_id' => auth()->user()->club_id ?? null, // If club_id is not provided, it assumes the global category
        ]);

        return redirect()->route('admin.productcategory.index')->with('success', 'Product Category created successfully.');
    }

    public function show(ProductCategory $category)
    {
        // Show details of a specific category
        return view('admin.productcategory.show', compact('category'));
    }

    public function edit(ProductCategory $category)
    {
        // Show form to edit an existing category
        return view('admin.productcategory.form', compact('category'));
    }

    public function update(Request $request, ProductCategory $category)
    {
        // Validate the form data
        $request->validate([
            'name' => 'required|unique:product_categories,name,' . $category->id,
        ]);

        // Update the category
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.productcategory.index')->with('success', 'Product Category updated successfully.');
    }

    public function destroy(ProductCategory $category)
    {
        // Delete the category
        $category->delete();
        return redirect()->route('admin.productcategory.index')->with('success', 'Product Category deleted successfully.');
    }

    public function confirmDelete(ProductCategory $category)
    {
        // Show confirmation for deleting a category
        return view('admin.productcategory.delete', compact('category'));
    }

    public function pullWooCommerce(WooCommerceImporter $woo)
    {
        if (!config('services.woocommerce.url') || !config('services.woocommerce.consumer_key') || !config('services.woocommerce.consumer_secret')) {
            return redirect()->route('admin.productcategory.index')
                ->with('error', 'WooCommerce settings are missing. Please set WOOCOMMERCE_URL, WOOCOMMERCE_CONSUMER_KEY, WOOCOMMERCE_CONSUMER_SECRET in .env');
        }
        $data = $woo->categories();
        $created = 0; $updated = 0;
        foreach ($data as $c) {
            $name = $c['name'] ?? null;
            if (!$name) continue;
            $slug = Str::slug($name);
            $cat = ProductCategory::firstOrNew(['slug' => $slug]);
            if (!$cat->exists) { $created++; } else { $updated++; }
            $cat->name = $name;
            $cat->club_id = $cat->club_id ?: null;
            $cat->save();
        }
        return redirect()->route('admin.productcategory.index')
            ->with('success', "Pulled categories: created {$created}, updated {$updated}");
    }
}
