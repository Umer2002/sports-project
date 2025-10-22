@php
    /** @var \Illuminate\Support\Collection<int, string> $sports */
    $group = $group ?? null;
    $selectedSport = old('sport_id', optional($group)->sport_id);
    $metaValue = old('meta', optional($group)->meta ? json_encode(optional($group)->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '');
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

<form action="{{ isset($group) ? route('admin.sport_classification_groups.update', $group) : route('admin.sport_classification_groups.store') }}" method="POST">
    @csrf
    @if(isset($group))
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
        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', optional($group)->code) }}" maxlength="64" required>
    </div>

    <div class="mb-4">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', optional($group)->name) }}" maxlength="191" required>
    </div>

    <div class="mb-4">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" rows="3" class="form-control" maxlength="512" placeholder="Optional description">{{ old('description', optional($group)->description) }}</textarea>
    </div>

    <div class="mb-4">
        <label for="sort_order" class="form-label">Sort Order</label>
        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', optional($group)->sort_order ?? 0) }}" min="0" max="65535">
    </div>

    <div class="mb-4">
        <label for="meta" class="form-label">Meta (JSON)</label>
        <textarea name="meta" id="meta" rows="4" class="form-control" placeholder='Optional JSON metadata e.g. {"team_size":5}'>{{ $metaValue }}</textarea>
        <small class="form-text text-muted">Leave blank if unused. Provide valid JSON.</small>
    </div>

    <button type="submit" class="btn btn-primary">{{ isset($group) ? 'Update Group' : 'Create Group' }}</button>
    <a href="{{ route('admin.sport_classification_groups.index') }}" class="btn btn-secondary">Cancel</a>
</form>
