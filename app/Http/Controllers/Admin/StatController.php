<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stat;  // <-- Import the Stat model
use App\Models\Sport; // <-- Import the Sport model
use Illuminate\Http\Request;

class StatController extends Controller
{
    public function index()
    {
        $stats = Stat::with('sport')->get();
        return view('admin.stats.index', compact('stats'));
    }

    public function create()
    {
        $sports = Sport::all();
        return view('admin.stats.create', compact('sports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sports_id' => 'required|exists:sports,id',
        ]);

        Stat::create($request->all());

        return redirect()->route('admin.stats.index')->with('success', 'Stat created successfully.');
    }

    public function edit(Stat $stat)
    {
        $sports = Sport::all();
        return view('admin.stats.edit', compact('stat', 'sports'));
    }

    public function update(Request $request, Stat $stat)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sports_id' => 'required|exists:sports,id',
        ]);

        $stat->update($request->all());

        return redirect()->route('admin.stats.index')->with('success', 'Stat updated successfully.');
    }

    public function destroy(Stat $stat)
    {
        $stat->delete();
        return redirect()->route('admin.stats.index')->with('success', 'Stat deleted successfully.');
    }
}
