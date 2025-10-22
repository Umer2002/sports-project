<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\BugReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BugReportController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'category' => 'required|string|max:50',
            'severity' => 'required|string|in:low,medium,high,critical',
            'description' => 'nullable|string|max:2000',
            'steps' => 'nullable|string|max:2000',
            'environment' => 'nullable|string|max:500',
            'include_logs' => 'nullable|boolean',
            'contact' => 'nullable|string|max:120',
            'share_diagnostics' => 'nullable|boolean',
            'attachment' => 'nullable|file|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('bug-reports', 'public');
        }

        $bugReport = BugReport::create([
            'user_id' => $request->user()?->id,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'severity' => $validated['severity'],
            'description' => $validated['description'] ?? null,
            'steps' => $validated['steps'] ?? null,
            'environment' => $validated['environment'] ?? null,
            'include_logs' => (bool) ($validated['include_logs'] ?? false),
            'contact' => $validated['contact'] ?? null,
            'share_diagnostics' => (bool) ($validated['share_diagnostics'] ?? false),
            'attachment_path' => $attachmentPath,
        ]);

        Log::info('Bug report submitted', [
            'bug_report_id' => $bugReport->id,
            'user_id' => $bugReport->user_id,
            'severity' => $bugReport->severity,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thanks! Your report was submitted successfully.',
        ]);
    }
}
