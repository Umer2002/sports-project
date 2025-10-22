<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $blogscategories = BlogCategory::all();
        return view('admin.blogcategory.index', compact('blogscategories'));
    }

    public function create()
    {
        return view('admin.blogcategory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $blogCategory = new BlogCategory($validated);

        if ($blogCategory->save()) {
            return redirect()->route('admin.blogcategory.index')->with('success', 'Blog category created successfully.');
        }

        return redirect()->route('admin.blogcategory.index')->with('error', 'Failed to create blog category.');
    }

    public function edit(BlogCategory $blogcategory)
    {
        return view('admin.blogcategory.edit', compact('blogcategory'));
    }

    public function update(Request $request, BlogCategory $blogcategory)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        if ($blogcategory->update($validated)) {
            return redirect()->route('admin.blogcategory.index')->with('success', 'Blog category updated successfully.');
        }

        return redirect()->route('admin.blogcategory.index')->with('error', 'Failed to update blog category.');
    }

    public function getModalDelete(BlogCategory $blogCategory)
    {
        $model = 'blogcategory';
        $confirm_route = $error = null;

        try {
            $confirm_route = route('admin.blogcategory.delete', ['id' => $blogCategory->id]);
            return view('admin.layouts.modal_confirmation', compact('error', 'model', 'confirm_route'));
        } catch (\Exception $e) {
            $error = 'Error while preparing delete confirmation.';
            return view('admin.layouts.modal_confirmation', compact('error', 'model', 'confirm_route'));
        }
    }

    public function destroy(BlogCategory $blogCategory)
    {
        if ($blogCategory->delete()) {
            return redirect()->route('admin.blogcategory.index')->with('success', 'Blog category deleted successfully.');
        }

        return redirect()->route('admin.blogcategory.index')->with('error', 'Failed to delete blog category.');
    }
}
