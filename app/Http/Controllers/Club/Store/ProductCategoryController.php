<?php

namespace App\Http\Controllers\Club\Store;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $club = Auth::user()->club; abort_unless($club, 403);
        $categories = ProductCategory::where('club_id', $club->id)->latest()->paginate(20);
        return view('club.store.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('club.store.categories.create');
    }

    public function store(Request $request)
    {
        $club = Auth::user()->club; abort_unless($club, 403);
        $data = $request->validate([
            'name' => 'required|string|max:191',
        ]);
        $data['slug'] = \Str::slug($data['name']);
        $data['club_id'] = $club->id;
        ProductCategory::create($data);
        return redirect()->route('club.store.categories.index')->with('success', 'Category created');
    }
}

