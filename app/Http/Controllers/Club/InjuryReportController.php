<?php
namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\InjuryReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InjuryReportController extends Controller
{
    public function index()
    {
        $reports = InjuryReport::with('player')->latest()->get();
        return view('club.injury_reports.index', compact('reports'));
    }

    public function create()
    {
        return view('club.injury_reports.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'player_id' => 'required|exists:players,id',
            'injury_datetime' => 'required|date',
            'team_name' => 'nullable|string',
            'location' => 'required|string',
            'injury_type' => 'required|string',
            'injury_type_other' => 'nullable|string',
            'incident_description' => 'required|string|min:15|max:300',
            'images.*' => 'nullable|image|max:2048',
            'first_aid' => 'required|boolean',
            'first_aid_description' => 'nullable|string',
            'emergency_called' => 'required|boolean',
            'hospital_referred' => 'required|boolean',
            'assisted_by' => 'nullable|string',
            'assisted_by_other' => 'nullable|string',
            'expected_recovery' => 'nullable|date',
            'medical_note' => 'nullable|file|max:2048',
            'return_to_play_required' => 'boolean'
        ]);

        $data['team_name'] = $request->team_name ?? "";

        if ($request->hasFile('images')) {
            $data['images'] = [];
            foreach ($request->file('images') as $file) {
                $data['images'][] = $file->store('injury_images', 'public');
            }
        }

        if ($request->hasFile('medical_note')) {
            $data['medical_note'] = $request->file('medical_note')->store('medical_notes', 'public');
        }

        InjuryReport::create($data);

        return redirect()->route('club.injury_reports.index')->with('success', 'Injury report submitted.');
    }

    public function destroy(InjuryReport $injuryReport)
    {
        $injuryReport->delete();
        return redirect()->route('club.injury_reports.index')->with('success', 'Injury report deleted.');
    }
}
