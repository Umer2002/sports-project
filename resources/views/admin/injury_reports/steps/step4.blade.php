<div class="card">
    <div class="card-header">
        <h5 class="mb-0">📄 Step 4: Medical Follow-Up</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Expected Recovery Date</label>
                <input type="date" name="expected_recovery" class="form-control" value="{{ old('expected_recovery', isset($report) && $report->expected_recovery ? $report->expected_recovery->format('Y-m-d') : '') }}">
            </div>
            <div class="col-md-6">
                <label>Upload Medical Note (optional)</label>
                <input type="file" name="medical_note" class="form-control">
            </div>
        </div>

        <div class="form-check mb-3">
            <input type="hidden" name="return_to_play_required" value="0">
            <input class="form-check-input" type="checkbox" name="return_to_play_required" value="1" {{ old('return_to_play_required', $report->return_to_play_required ?? false) ? 'checked' : '' }}>
            <label class="form-check-label">Return-to-Play Clearance Required</label>
        </div>
    </div>
</div>
