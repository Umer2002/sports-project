<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SportController extends Controller
{
    public function index()
    {
        $sports = Sport::latest()->get();
        return view('admin.sports.index', compact('sports'));
    }

    public function create()
    {
        return view('admin.sports.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_path' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'is_top_sport' => 'boolean',
        ]);

        if ($request->hasFile('icon_path')) {
            $data['icon_path'] = $request->file('icon_path')->store('logos', 'public');
        }

        Sport::create($data);

        return redirect()->route('admin.sports.index')->with('success', 'Sport created successfully!');
    }

    public function edit(Sport $sport)
    {
        return view('admin.sports.edit', compact('sport'));
    }

    public function update(Request $request, Sport $sport)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_path' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'is_top_sport' => 'nullable',
        ]);
        
        // Convert checkbox value to boolean
        $data['is_top_sport'] = $request->has('is_top_sport');

        if ($request->hasFile('icon_path')) {
            $data['icon_path'] = $request->file('icon_path')->store('logos', 'public');
        }

        $sport->update($data);

        return redirect()->route('admin.sports.index')->with('success', 'Sport updated successfully!');
    }

    public function destroy(Sport $sport)
    {
        $sport->delete();
        return redirect()->route('admin.sports.index')->with('success', 'Sport deleted.');
    }
}
