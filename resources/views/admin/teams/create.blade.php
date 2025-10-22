@extends('layouts.admin')
@section('title', isset($team) ? 'Edit Team' : 'Add Team')

@section('header_styles')
<!-- Include if you have custom styles for select2/summernote -->
@endsection

@section('content')
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="header">
                <h2>{{ isset($team) ? 'Edit Team' : 'Add Team' }}</h2>
            </div>

            <div class="body">
                @if($errors->any())
                    <div class="alert alert-danger rounded-2xl">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <?php
                    $actingAsClub = auth()->check() && auth()->user()->hasRole('club');
                    $currentUser = auth()->user();
                    $currentClub = null;
                    if ($actingAsClub && $currentUser) {
                        // Resolve club either via users.club_id or clubs.user_id
                        $currentClub = $currentUser->club ?: \App\Models\Club::where('user_id', $currentUser->id)->first();
                    }
                ?>
                <form action="{{ isset($team)
                        ? ($actingAsClub ? route('club.teams.update', $team) : route('admin.teams.update', $team))
                        : ($actingAsClub ? route('club.teams.store') : route('admin.teams.store'))
                    }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($team)) @method('PUT') @endif

                    {{-- Team Name --}}
                    <h2 class="card-inside-title">Team Name</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="name" id="name" class="form-control"
                                   value="{{ old('name', $team->name ?? '') }}" placeholder="Enter team name" required>
                        </div>
                    </div>

                    {{-- Description --}}
                    <h2 class="card-inside-title">Description</h2>
                    <div class="form-group">
                        <textarea name="description" class="form-control summernote" rows="5">{{ old('description', $team->description ?? '') }}</textarea>
                    </div>

                    {{-- Club --}}
                    <h2 class="card-inside-title">Club</h2>
                    @if($actingAsClub && $currentClub)
                        <input type="hidden" name="club_id" id="club_id" value="{{ $currentClub->id }}">
                        <div class="form-group"><div class="alert alert-info mb-0">{{ $currentClub->name }}</div></div>
                    @else
                        <div class="form-group">
                            <select name="club_id" id="club_id" class="form-control select2" data-placeholder="Select Club" required>
                                <option value="">Select Club</option>
                                @foreach($clubs as $id => $club)
                                    <option value="{{ $id }}" {{ old('club_id', $team->club_id ?? '') == $id ? 'selected' : '' }}>
                                        {{ $club }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Sport (auto-selected from club, disabled) --}}
                    <h2 class="card-inside-title">Sport</h2>
                    @if($actingAsClub && $currentClub)
                        <input type="hidden" name="sport_id" id="sport_id" value="{{ $currentClub->sport_id }}">
                        <div class="form-group">
                            <div class="alert alert-info mb-0">{{ optional($currentClub->sport)->name ?? 'Sport not set' }}</div>
                            <small class="text-muted">Sport is tied to your club.</small>
                        </div>
                    @else
                        <div class="form-group">
                            <select id="sport_select" class="form-control" disabled>
                                <option value="">-- Sport will be set from club --</option>
                                @foreach($sports as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="sport_id" id="sport_id" value="{{ old('sport_id') }}">
                            <small class="text-muted">Sport is set based on the selected club and cannot be changed.</small>
                        </div>
                    @endif

                    {{-- Division --}}
                    <h2 class="card-inside-title">Division</h2>
                    <div class="form-group">
                        <select name="division_id" id="division_id" class="form-control select2" data-placeholder="Select Division" data-initial="{{ old('division_id', $team->division_id ?? '') }}" required>
                            <option value="">Select Division</option>
                        </select>
                        <small class="text-muted">Divisions automatically filter based on the club's sport.</small>
                    </div>

                    {{-- Logo --}}
                    <h2 class="card-inside-title">Team Logo</h2>
                    <div class="form-group">
                        <input type="file" name="logo" class="form-control">
                        @if(isset($team) && $team->logo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $team->logo) }}" width="60" class="rounded shadow-sm" alt="Team Logo">
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="form-group d-flex justify-content-between mt-4">
                        <a href="{{ $actingAsClub ? route('club.teams.index') : route('admin.teams.index') }}" class="btn btn-secondary btn-label">
                            <i class="ti ti-trash label-icon"></i> Discard
                        </a>
                        <button type="submit" class="btn btn-success btn-label">
                            <i class="ti ti-send label-icon"></i> {{ isset($team) ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<!-- Bootstrap + Summernote + Select2 -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
<script>
    $(function () {
        $('.summernote').summernote({ height: 200 });
        if (window.$) {
            $('.select2').each(function () {
                const placeholder = this.dataset.placeholder || 'Select an option';
                window.$(this).select2({
                    width: '100%',
                    placeholder,
                    allowClear: true
                });
            });
        }
    });

    const clubSelect = document.getElementById('club_id');
    const sportSelect = document.getElementById('sport_select');
    const sportIdInput = document.getElementById('sport_id');
    const divisionSelect = document.getElementById('division_id');
    let pendingDivisionId = divisionSelect ? divisionSelect.dataset.initial : '';

    function populateDivisions(divisions, selectedId) {
        if (!divisionSelect) { return; }

        divisionSelect.innerHTML = '';
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = 'Select Division';
        divisionSelect.appendChild(placeholderOption);
        divisionSelect.disabled = !divisions.length;

        const grouped = divisions.reduce((carry, division) => {
            const key = division.category || '';
            (carry[key] = carry[key] || []).push(division);
            return carry;
        }, {});

        Object.entries(grouped).forEach(([category, items]) => {
            const container = category
                ? (() => { const group = document.createElement('optgroup'); group.label = category; divisionSelect.appendChild(group); return group; })()
                : divisionSelect;

            items.forEach(({ id, name }) => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = name;
                if (String(id) === String(selectedId)) {
                    option.selected = true;
                }
                container.appendChild(option);
            });
        });

        const currentValue = divisions.some(division => String(division.id) === String(selectedId))
            ? String(selectedId)
            : '';
        divisionSelect.value = currentValue;

        if (window.$ && window.$(divisionSelect).data('select2')) {
            window.$(divisionSelect).val(currentValue).trigger('change.select2');
        }
    }

    async function syncSportWithClub(clubId) {
        if (!clubId) {
            if (sportSelect) { sportSelect.value = ''; }
            if (sportIdInput) { sportIdInput.value = ''; }
            populateDivisions([], '');
            return;
        }

        try {
            const response = await fetch(`/clubs/${clubId}/sport`, { headers: { 'Accept': 'application/json' } });
            const data = await response.json();

            const sid = data.sport_id || '';
            if (sportSelect) { sportSelect.value = sid; }
            if (sportIdInput) { sportIdInput.value = sid; }

            const selectedDivisionId = pendingDivisionId || '';
            populateDivisions(data.divisions || [], selectedDivisionId);
            if (pendingDivisionId) {
                pendingDivisionId = '';
            }
        } catch (error) {
            if (sportSelect) { sportSelect.value = ''; }
            if (sportIdInput) { sportIdInput.value = ''; }
            populateDivisions([], '');
        }
    }

    if (clubSelect) {
        const handleChange = () => {
            pendingDivisionId = '';
            syncSportWithClub(clubSelect.value);
        };

        clubSelect.addEventListener('change', handleChange);

        if (window.$ && window.$(clubSelect).data('select2')) {
            window.$(clubSelect).on('select2:select select2:clear', handleChange);
        }

        if (clubSelect.value) {
            syncSportWithClub(clubSelect.value);
        } else {
            populateDivisions([], '');
        }
    } else if (divisionSelect) {
        populateDivisions([], '');
    }
</script>
@endsection
