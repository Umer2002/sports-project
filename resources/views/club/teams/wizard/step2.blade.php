@extends('layouts.club-dashboard')
@section('title', 'Team Eligibility - Step 2')
@section('page_title', 'Team Eligibility - Step 2')

@section('content')
    <div class="row clearfix">
        @include('club.teams.wizard._progress')

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title">Step 2 of 4: Set Eligibility for <strong>{{ $team->name }}</strong></h4>
                    <p class="card-subtitle">Choose the age band, gender, and sport options to filter eligible players.</p>
                </div>
                <div class="card-body">
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
                                <a href="{{ route('club.teams.wizard.step1') }}" class="btn btn-sm btn-outline-secondary mt-1">
                                    <i class="fas fa-arrow-left"></i> Back to Step 1
                                </a>
                            </div>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php($selectedOptions = collect(old('classification_option_ids', $selectedOptionIds))->map(fn($id) => (int) $id)->all())

                    <form method="POST" action="{{ route('club.teams.wizard.storeStep2', $team) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="age_group_id" class="form-label fw-bold">Age Group</label>
                            <select
                                name="age_group_id"
                                id="age_group_id"
                                class="form-select select2"
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
                            <small class="text-muted">Only players whose age fits this band will appear in Step 3.</small>
                            @error('age_group_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="gender_id" class="form-label fw-bold">Gender</label>
                            <select
                                name="gender_id"
                                id="gender_id"
                                class="form-select select2"
                                data-placeholder="Select gender (optional)"
                            >
                                <option value="">Open / Co-ed</option>
                                @foreach ($genders as $gender)
                                    <option value="{{ $gender->id }}" {{ old('gender_id', $team->gender_id) == $gender->id ? 'selected' : '' }}>
                                        {{ $gender->label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Players must match this gender unless you leave it Open / Co-ed.</small>
                            @error('gender_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($classificationGroups->isNotEmpty())
                            <div class="mb-3">
                                <h5 class="fw-bold">Sport Options</h5>
                                <p class="text-muted">Tag the team with competition levels, formats, or other sport-specific labels.</p>
                            </div>
                            @foreach ($classificationGroups as $group)
                                <div class="mb-3">
                                    <label for="classification_group_{{ $group->id }}" class="form-label fw-bold">{{ $group->name }}</label>
                                    <select
                                        name="classification_option_ids[]"
                                        id="classification_group_{{ $group->id }}"
                                        class="form-select select2"
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
                            <a href="{{ route('club.teams.wizard.step1') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Step 1
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-adjust"></i> Save Eligibility & Continue
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
