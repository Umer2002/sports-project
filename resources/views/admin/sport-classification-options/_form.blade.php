@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\SportClassificationGroup> $groups */
    $option = $option ?? null;
    $selectedGroup = old('group_id', optional($option)->group_id);
    $metaValue = old('meta', optional($option)->meta ? json_encode(optional($option)->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '');
    $groupsEmpty = $groups->isEmpty();
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

@if($groups->isEmpty())
    <div class="alert alert-warning">
        You need to create a classification group before adding options.
    </div>
@endif

<form action="{{ isset($option) ? route('admin.sport_classification_options.update', $option) : route('admin.sport_classification_options.store') }}" method="POST">
    @csrf
    @if(isset($option))
        @method('PUT')
    @endif

    <div class="mb-4">
        <label for="group_id" class="form-label">Classification Group</label>
        <select name="group_id" id="group_id" class="form-control" required {{ $groupsEmpty ? 'disabled' : '' }}>
            <option value="" disabled {{ $selectedGroup ? '' : 'selected' }}>Select a group</option>
            @foreach($groups as $group)
                <option value="{{ $group->id }}" {{ (string) $group->id === (string) $selectedGroup ? 'selected' : '' }}>
                    {{ $group->name }} @if($group->sport) ({{ $group->sport->name }}) @endif
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="code" class="form-label">Code</label>
        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', optional($option)->code) }}" maxlength="64" required>
    </div>

    <div class="mb-4">
        <label for="label" class="form-label">Label</label>
        <input type="text" name="label" id="label" class="form-control" value="{{ old('label', optional($option)->label) }}" maxlength="191" required>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', optional($option)->sort_order ?? 0) }}" min="0" max="65535">
        </div>
        <div class="col-md-6 mb-4">
            <label for="numeric_rank" class="form-label">Numeric Rank</label>
            <input type="number" name="numeric_rank" id="numeric_rank" class="form-control" value="{{ old('numeric_rank', optional($option)->numeric_rank) }}">
        </div>
    </div>

    <div class="mb-4">
        <label for="meta" class="form-label">Meta (JSON)</label>
        <textarea name="meta" id="meta" rows="4" class="form-control" placeholder='Optional JSON metadata e.g. {"tier":1}'>{{ $metaValue }}</textarea>
        <small class="form-text text-muted">Leave blank if unused. Provide valid JSON.</small>
    </div>

    <button type="submit" class="btn btn-primary" {{ $groupsEmpty ? 'disabled' : '' }}>{{ isset($option) ? 'Update Option' : 'Create Option' }}</button>
    <a href="{{ route('admin.sport_classification_options.index') }}" class="btn btn-secondary">Cancel</a>
</form>
