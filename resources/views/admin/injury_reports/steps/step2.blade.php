<div class="card">
    <div class="card-header">
        <h5 class="mb-0">⚠️ Step 2: Incident Details</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Type of Injury</label>
                <select name="injury_type" class="form-control">
                    @foreach(['Muscle strain', 'Concussion', 'Fracture', 'Sprain', 'Other'] as $type)
                        <option value="{{ $type }}" {{ old('injury_type', $report->injury_type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                <input type="text" name="injury_type_other" class="form-control mt-2 {{ old('injury_type', $report->injury_type ?? '') !== 'Other' ? 'd-none' : '' }}" placeholder="Specify other injury" value="{{ old('injury_type_other', $report->injury_type_other ?? '') }}">
            </div>
            <div class="col-md-6">
                <label>Describe What Happened</label>
                <textarea name="incident_description" rows="4" class="form-control" minlength="150" maxlength="300">{{ old('incident_description', $report->incident_description ?? '') }}</textarea>
            </div>
        </div>

        <div class="mb-3">
            <label>Upload Images (Optional)</label>
            <input type="file" name="images[]" multiple class="form-control">
        </div>
    </div>
</div>
