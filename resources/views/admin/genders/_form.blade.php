@php
    /** @var \Illuminate\Support\Collection<int, string> $sports */
    $gender = $gender ?? null;
    $selectedSport = old('sport_id', optional($gender)->sport_id);
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

<form action="{{ isset($gender) ? route('admin.genders.update', $gender) : route('admin.genders.store') }}" method="POST">
    @csrf
    @if(isset($gender))
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
        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', optional($gender)->code) }}" maxlength="32" required>
    </div>

    <div class="mb-4">
        <label for="label" class="form-label">Label</label>
        <input type="text" name="label" id="label" class="form-control" value="{{ old('label', optional($gender)->label) }}" maxlength="100" required>
    </div>

    <div class="mb-4">
        <label for="sort_order" class="form-label">Sort Order</label>
        <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', optional($gender)->sort_order ?? 0) }}" min="0" max="65535">
    </div>

    <button type="submit" class="btn btn-primary">{{ isset($gender) ? 'Update Gender' : 'Create Gender' }}</button>
    <a href="{{ route('admin.genders.index') }}" class="btn btn-secondary">Cancel</a>
</form>
