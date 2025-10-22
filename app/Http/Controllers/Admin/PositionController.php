<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Sport;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {

        $positions = Position::with('sport')->get();
        // dd($positions);
        return view('admin.positions.index', compact('positions'));
    }

    public function create()
    {
        $sports = Sport::all();
        return view('admin.positions.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'position_name' => 'required|string|max:255',
            'position_value' => 'required|string|max:255',
            'sports_id' => 'required|exists:sports,id',
            'is_active' => 'boolean',
        ]);

        Position::create($request->all());

        return redirect()->route('admin.positions.index')->with('success', 'Position created successfully.');
    }

    public function edit(Position $position)
    {
        $sports = Sport::all();
        return view('admin.positions.edit', compact('position', 'sports'));
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'position_name' => 'required|string|max:255',
            'position_value' => 'required|string|max:255',
            'sports_id' => 'required|exists:sports,id',
            'is_active' => 'boolean',
        ]);

        $position->update($request->all());

        return redirect()->route('admin.positions.index')->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return redirect()->route('admin.positions.index')->with('success', 'Position deleted successfully.');
    }
}
