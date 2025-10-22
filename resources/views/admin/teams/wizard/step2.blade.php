@extends('layouts.admin')
@section('title', 'Define Team Eligibility')

@section('content')
    <div class="row clearfix">
        @include('admin.teams.wizard._progress')

        <div class="col-lg-12">
            <div class="card">
                <div class="header bg-primary text-white">
                    <h2>Step 2: Configure Eligibility for <strong>{{ $team->name }}</strong></h2>
                </div>
                <div class="body">
                    <div class="mb-4 p-3 bg-light rounded border">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div>
                                <h5 class="mb-1">Team Overview</h5>
                                <p class="mb-0 text-muted">
                                    Sport: <strong>{{ $team->sport->name ?? 'Unknown' }}</strong>
                                    @if($team->division)
                                        <span class="ms-3">Division: <strong>{{ $team->division->name }}</strong></span>
                                    @endif
                                </p>
                                @if($team->ageGroup)
                                    <p class="mb-0 text-muted">Current Age Group: <strong>{{ $team->ageGroup->label }}</strong></p>
                                @endif
                                @if($team->genderCategory)
                                    <p class="mb-0 text-muted">Current Gender: <strong>{{ $team->genderCategory->label }}</strong></p>
                                @endif
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Need to adjust basics?</small>
                                <a href="{{ route('admin.teams.wizard.step1') }}" class="btn btn-sm btn-outline-secondary mt-1">
                                    <i class="ti ti-arrow-left"></i> Back to Team Info
                                </a>
                            </div>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-2xl">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php($selectedOptions = collect(old('classification_option_ids', $selectedOptionIds))->map(fn($id) => (int) $id)->all())

                    <form method="POST" action="{{ route('admin.teams.wizard.storeStep2', $team) }}">
                        @csrf

                        <h5 class="card-inside-title">Age Group</h5>
                        <div class="form-group">
                            <select
                                name="age_group_id"
                                id="age_group_id"
                                class="form-control select2"
                                data-placeholder="Select age group (optional)"
                            >
                                <option value="">No specific age group</option>
                                @foreach ($ageGroups as $group)
                                    <option value="{{ $group->id }}" {{ old('age_group_id', $team->age_group_id) == $group->id ? 'selected' : '' }}>
                                        {{ $group->label }}
                                        @if(!is_null($group->min_age_years) || !is_null($group->max_age_years))
                                            ({{ implode(' - ', array_filter([
                                                $group->min_age_years ? 'min '.$group->min_age_years : null,
                                                $group->max_age_years ? 'max '.$group->max_age_years : null,
                                            ])) }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Only players whose age falls within this band will be eligible.</small>
                            @error('age_group_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <h5 class="card-inside-title">Gender</h5>
                        <div class="form-group">
                            <select
                                name="gender_id"
                                id="gender_id"
                                class="form-control select2"
                                data-placeholder="Select gender (optional)"
                            >
                                <option value="">Open / Co-ed</option>
                                @foreach ($genders as $gender)
                                    <option value="{{ $gender->id }}" {{ old('gender_id', $team->gender_id) == $gender->id ? 'selected' : '' }}>
                                        {{ $gender->label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Players must match this gender unless set to Open / Co-ed.</small>
                            @error('gender_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($classificationGroups->isNotEmpty())
                            <h5 class="card-inside-title">Sport Options</h5>
                            <p class="text-muted">Choose any competition tiers, skill levels, or other sport-specific tags that apply to this team.</p>
                            @foreach ($classificationGroups as $group)
                                <div class="form-group">
                                    <label for="classification_group_{{ $group->id }}" class="form-label fw-bold mb-1">{{ $group->name }}</label>
                                    <select
                                        name="classification_option_ids[]"
                                        id="classification_group_{{ $group->id }}"
                                        class="form-control select2"
                                        data-placeholder="Select {{ strtolower($group->name) }}"
                                        multiple
                                    >
                                        @foreach ($group->options as $option)
                                            <option value="{{ $option->id }}" {{ in_array($option->id, $selectedOptions, true) ? 'selected' : '' }}>
                                                {{ $option->label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($group->description)
                                        <small class="text-muted d-block">{{ $group->description }}</small>
                                    @endif
                                </div>
                            @endforeach
                            @error('classification_option_ids')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        @endif

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.teams.wizard.step1') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> Back to Team Info
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-adjustments"></i> Save Eligibility & Continue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    <script>
        if (window.$) {
            $('.select2').each(function () {
                const placeholder = this.dataset.placeholder || 'Select an option';
                window.$(this).select2({
                    width: '100%',
                    placeholder,
                    allowClear: !this.hasAttribute('multiple')
                });
            });
        }
    </script>
@endsection
