<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TournamentFormat;
use Illuminate\Http\Request;

class TournamentFormatController extends Controller
{
    public function index() {
        $formats = TournamentFormat::all();
        // dd($formats);
        return view('admin.tournament_formats.index', compact('formats'));
    }

    public function create() {
        return view('admin.tournament_formats.create');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:round_robin,group,knockout',
            'games_per_team' => 'nullable|integer|min:1',
            'group_count' => 'nullable|integer|min:1',
            'elimination_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        TournamentFormat::create($request->all());
        return redirect()->route('admin.tournamentformats.index')->with('success', 'Tournament format created successfully.');
    }

    public function edit(TournamentFormat $tournamentformat){
    // dd($tournamentFormat);
        return view('admin.tournament_formats.edit', compact('tournamentformat'));
    }

    public function update(Request $request, TournamentFormat $tournamentFormat) {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:round_robin,group,knockout',
            'games_per_team' => 'nullable|integer|min:1',
            'group_count' => 'nullable|integer|min:1',
            'elimination_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $tournamentFormat->update($request->all());
        return redirect()->route('admin.tournamentformats.index')->with('success', 'Tournament format updated successfully.');
    }

    public function destroy(TournamentFormat $tournamentFormat) {
        $tournamentFormat->delete();
        return back()->with('success', 'Tournament format deleted.');
    }
}
