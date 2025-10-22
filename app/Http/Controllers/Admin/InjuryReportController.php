<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InjuryReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InjuryReportController extends Controller
{
    public function index()
    {
        $reports = InjuryReport::with('player')->latest()->get();
        return view('admin.injury_reports.index', compact('reports'));
    }

    public function create()
    {
        return view('admin.injury_reports.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'player_id' => 'required|exists:players,id',
            'injury_datetime' => 'required|date',
            'team_name' => 'required|string',
            'location' => 'required|string',
            'injury_type' => 'required|string',
            'injury_type_other' => 'nullable|string',
            'incident_description' => 'required|string|min:15|max:300',
            'images.*' => 'nullable|image|max:2048',
            'first_aid' => 'required|in:0,1',
            'first_aid_description' => 'nullable|string',
            'emergency_called' => 'required|in:0,1',
            'hospital_referred' => 'required|in:0,1',
            'assisted_by' => 'nullable|string',
            'assisted_by_other' => 'nullable|string',
            'expected_recovery' => 'nullable|date',
            'medical_note' => 'nullable|file|max:2048',
            'return_to_play_required' => 'nullable|in:0,1'
        ]);

        // Convert string values to boolean for database storage
        $data['first_aid'] = (bool) $data['first_aid'];
        $data['emergency_called'] = (bool) $data['emergency_called'];
        $data['hospital_referred'] = (bool) $data['hospital_referred'];
        $data['return_to_play_required'] = isset($data['return_to_play_required']) ? (bool) $data['return_to_play_required'] : false;

        // Handle file uploads if present
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

        return redirect()->route('admin.injury_reports.index')->with('success', 'Injury report submitted successfully.');
    }

    public function edit(InjuryReport $injuryReport)
    {
        return view('admin.injury_reports.edit', compact('injuryReport'));
    }

    public function update(Request $request, InjuryReport $injuryReport)
    { 
        // Debug the incoming data
        \Log::info('Injury Report Update Request:', $request->all());
        
        $data = $request->validate([
            'player_id' => 'required|exists:players,id',
            'injury_datetime' => 'required|date',
            'team_name' => 'nullable|string', // Changed to nullable since it's coming as null
            'location' => 'required|string',
            'injury_type' => 'required|string',
            'injury_type_other' => 'nullable|string',
            'incident_description' => 'required|string|min:15|max:300',
            'images.*' => 'nullable|image|max:2048',
            'first_aid' => 'required|in:0,1',
            'first_aid_description' => 'nullable|string',
            'emergency_called' => 'required|in:0,1',
            'hospital_referred' => 'required|in:0,1',
            'assisted_by' => 'nullable|string',
            'assisted_by_other' => 'nullable|string',
            'expected_recovery' => 'nullable|date',
            'medical_note' => 'nullable|file|max:2048',
            'return_to_play_required' => 'nullable|in:0,1'
        ]);

        // Convert string values to boolean for database storage
        $data['first_aid'] = (bool) $data['first_aid'];
        $data['emergency_called'] = (bool) $data['emergency_called'];
        $data['hospital_referred'] = (bool) $data['hospital_referred'];
        $data['return_to_play_required'] = isset($data['return_to_play_required']) ? (bool) $data['return_to_play_required'] : false;

        // Handle file uploads if present
        if ($request->hasFile('images')) {
            $data['images'] = [];
            foreach ($request->file('images') as $file) {
                $data['images'][] = $file->store('injury_images', 'public');
            }
        }

        if ($request->hasFile('medical_note')) {
            $data['medical_note'] = $request->file('medical_note')->store('medical_notes', 'public');
        }

        // Debug the data being updated
        \Log::info('Data to update:', $data);
        
        $result = $injuryReport->update($data);
        
        \Log::info('Update result:', ['success' => $result, 'injury_report_id' => $injuryReport->id]);

        return redirect()->route('admin.injury_reports.index')->with('success', 'Injury report updated successfully.');
    }

    public function destroy(InjuryReport $injuryReport)
    {
        $injuryReport->delete();
        return redirect()->route('admin.injury_reports.index')->with('success', 'Injury report deleted.');
    }
}
