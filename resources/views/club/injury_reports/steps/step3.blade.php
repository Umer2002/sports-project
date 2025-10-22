<div class="card">
    <div class="card-header">
        <h5 class="mb-0">🚑 Step 3: Initial Response</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <label>First Aid Provided On Site?</label>
                <select name="first_aid" class="form-control">
                    <option value="1" {{ old('first_aid', $report->first_aid ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ old('first_aid', $report->first_aid ?? '') == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-8">
                <label>First Aid Description</label>
                <input type="text" name="first_aid_description" class="form-control" value="{{ old('first_aid_description', $report->first_aid_description ?? '') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Emergency Service Called?</label>
                <select name="emergency_called" class="form-control">
                    <option value="1" {{ old('emergency_called', $report->emergency_called ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ old('emergency_called', $report->emergency_called ?? '') == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Hospital/Clinic Referred?</label>
                <select name="hospital_referred" class="form-control">
                    <option value="1" {{ old('hospital_referred', $report->hospital_referred ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ old('hospital_referred', $report->hospital_referred ?? '') == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Who Assisted?</label>
                <input type="text" name="assisted_by" class="form-control" value="{{ old('assisted_by', $report->assisted_by ?? '') }}">
            </div>
        </div>
    </div>
</div>
