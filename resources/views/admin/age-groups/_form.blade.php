@php
    /** @var \Illuminate\Support\Collection<int, string> $sports */
    $ageGroup = $ageGroup ?? null;
    $selectedSport = old('sport_id', optional($ageGroup)->sport_id);
@endphp

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ isset($ageGroup) ? route('admin.age_groups.update', $ageGroup) : route('admin.age_groups.store') }}" method="POST">
    @csrf
    @if(isset($ageGroup))
        @method('PUT')
    @endif

    <div class="mb-4">
        <label for="sport_id" class="form-label">Sport</label>
        <select name="sport_id" id="sport_id" class="form-control" required>
            <option value="" disabled {{ $selectedSport ? '' : 'selected' }}>Select a sport</option>
            @foreach($sports as $id => $name)
                <option value="{{ $id }}" {{ (string) $id === (string) $selectedSport ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="code" class="form-label">Code</label>
        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', optional($ageGroup)->code) }}" maxlength="32" required>
    </div>

    <div class="mb-4">
        <label for="label" class="form-label">Label</label>
        <input type="text" name="label" id="label" class="form-control" value="{{ old('label', optional($ageGroup)->label) }}" maxlength="191" required>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <label for="min_age_years" class="form-label">Minimum Age (years)</label>
            <input type="number" name="min_age_years" id="min_age_years" class="form-control" value="{{ old('min_age_years', optional($ageGroup)->min_age_years) }}" min="0" max="120">
        </div>
        <div class="col-md-6 mb-4">
            <label for="max_age_years" class="form-label">Maximum Age (years)</label>
            <input type="number" name="max_age_years" id="max_age_years" class="form-control" value="{{ old('max_age_years', optional($ageGroup)->max_age_years) }}" min="0" max="120">
        </div>
    </div>

    <div class="form-check mb-4">
        <input type="checkbox" class="form-check-input" name="is_open_ended" id="is_open_ended" value="1" {{ old('is_open_ended', optional($ageGroup)->is_open_ended) ? 'checked' : '' }}>
        <label for="is_open_ended" class="form-check-label">Open ended (no maximum)</label>
    </div>

    <div class="mb-4">
        <label for="context" class="form-label">Context</label>
        <input type="text" name="context" id="context" class="form-control" value="{{ old('context', optional($ageGroup)->context) }}" maxlength="191" placeholder="Optional context hint">
    </div>

    <div class="mb-4">
        <label for="notes" class="form-label">Notes</label>
        <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Optional notes">{{ old('notes', optional($ageGroup)->notes) }}</textarea>
    </div>

    <div class="mb-4">
        <label for="sort_order" class="form-label">Sort Order</label>
        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', optional($ageGroup)->sort_order ?? 0) }}" min="0" max="65535">
    </div>

    <button type="submit" class="btn btn-primary">{{ isset($ageGroup) ? 'Update Age Group' : 'Create Age Group' }}</button>
    <a href="{{ route('admin.age_groups.index') }}" class="btn btn-secondary">Cancel</a>
</form>
