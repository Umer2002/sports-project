@php
    $tournament = $tournament ?? null;
    $selectedTeamIds = collect(old('team_ids', $tournament ? $tournament->teams->pluck('id')->all() : []))
        ->map(fn($id) => (string) $id)
        ->all();
    $selectedCountryId = old('country_id', optional($tournament)->country_id);
    $selectedStateId = old('state_id', optional($tournament)->state_id);
    $selectedCityId = old('city_id', optional($tournament)->city_id);
    $selectedVenueId = old('venue_id', optional($tournament)->venue_id);
    $selectedGenderId = old('gender_id', optional($tournament)->gender_id);
    $selectedAgeGroupId = old('age_group_id', optional($tournament)->age_group_id);
    $selectedClassificationOptionIds = collect(old('classification_option_ids', $tournament ? $tournament->classificationOptions->pluck('id')->all() : []))
        ->map(fn ($id) => (string) $id)
        ->values();
    $stateOptions = collect($states ?? []);
    $cityOptions = collect($cities ?? []);
    $venueOptions = collect($venues ?? []);
    $eligibilityData = $eligibilityData ?? ['genders' => [], 'age_groups' => [], 'classification_groups' => []];
    $genderOptions = collect($eligibilityData['genders'] ?? []);
    $ageGroupOptions = collect($eligibilityData['age_groups'] ?? []);
    $classificationGroups = collect($eligibilityData['classification_groups'] ?? []);
@endphp

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-secondary text-white fw-semibold">
        Eligibility &amp; Sport Options
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="gender_id" class="form-label">Gender Category</label>
                <select name="gender_id" id="gender_id" class="form-select @error('gender_id') is-invalid @enderror" data-selected="{{ $selectedGenderId }}">
                    <option value="">All / Mixed</option>
                    @foreach($genderOptions as $gender)
                        <option value="{{ $gender['id'] }}" {{ (string) $selectedGenderId === (string) $gender['id'] ? 'selected' : '' }}>
                            {{ $gender['label'] }}
                        </option>
                    @endforeach
                </select>
                @error('gender_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Leave blank to allow mixed participation.</div>
            </div>

            <div class="col-md-4">
                <label for="age_group_id" class="form-label">Age Group</label>
                <select name="age_group_id" id="age_group_id" class="form-select @error('age_group_id') is-invalid @enderror" data-selected="{{ $selectedAgeGroupId }}">
                    <option value="">All Ages</option>
                    @foreach($ageGroupOptions as $ageGroup)
                        <option value="{{ $ageGroup['id'] }}" {{ (string) $selectedAgeGroupId === (string) $ageGroup['id'] ? 'selected' : '' }}>
                            {{ $ageGroup['label'] }}
                        </option>
                    @endforeach
                </select>
                @error('age_group_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Match teams to a specific age bracket if required.</div>
            </div>

            <div class="col-md-12">
                <label class="form-label">Sport Options</label>
                <div id="classification-select-container" class="row g-2" data-selected='@json($selectedClassificationOptionIds)'>
                    @forelse($classificationGroups as $group)
                        @php
                            $groupId = (string) ($group['id'] ?? '');
                            $options = collect($group['options'] ?? []);
                            $groupSelected = $options
                                ->pluck('id')
                                ->map(fn ($id) => (string) $id)
                                ->first(fn ($id) => $selectedClassificationOptionIds->contains($id));
                        @endphp
                        <div class="col-lg-4 col-md-6 classification-group">
                            <label for="classification_group_{{ $groupId }}" class="form-label">{{ $group['name'] }}</label>
                            <select
                                name="classification_option_ids[]"
                                id="classification_group_{{ $groupId }}"
                                class="form-select classification-select @error('classification_option_ids') is-invalid @enderror"
                                data-group-id="{{ $groupId }}"
                            >
                                <option value="">
                                    Any {{ $group['name'] }}
                                </option>
                                @foreach($options as $option)
                                    <option value="{{ $option['id'] }}" {{ $groupSelected === (string) $option['id'] ? 'selected' : '' }}>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @empty
                        <div class="col-12 text-muted" data-empty-placeholder>
                            Sport-specific options will appear here when available.
                        </div>
                    @endforelse
                </div>
                @error('classification_option_ids')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="form-text">Use these to filter teams by skill tier, weight class, or other sport attributes.</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white fw-semibold">
        Tournament Information
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Tournament Name *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $tournament->name ?? '') }}" required minlength="3" maxlength="255">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Enter a unique tournament name (3-255 characters)</div>
            </div>

            <div class="col-md-6">
                <label for="tournament_format_id" class="form-label">Format *</label>
                <select name="tournament_format_id" id="tournament_format_id" class="form-select @error('tournament_format_id') is-invalid @enderror" required>
                    <option value="">Select Format</option>
                    @foreach($formats as $id => $name)
                        <option value="{{ $id }}" {{ (old('tournament_format_id', $tournament->tournament_format_id ?? '') == $id) ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('tournament_format_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    <strong>Round Robin:</strong> All teams play each other<br>
                    <strong>Knockout:</strong> Single elimination tournament<br>
                    <strong>Group:</strong> Teams divided into groups, then knockout
                </div>
            </div>
        </div>
        
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" name="sport_id" value="{{ $club->sport_id }}">
                <div class="form-text">Sport: {{ $club->sport->name ?? 'Not specified' }}</div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="team_ids" class="form-label">Teams *</label>
                <select name="team_ids[]" id="team_ids" class="form-select @error('team_ids') is-invalid @enderror" multiple required size="8">
                    @foreach($teams as $teamOption)
                        <option value="{{ $teamOption->id }}" {{ in_array((string) $teamOption->id, $selectedTeamIds, true) ? 'selected' : '' }}>
                            {{ $teamOption->name }}
                        </option>
                    @endforeach
                </select>
                @error('team_ids')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    <span id="team-count">0</span> teams selected. 
                    <span id="format-requirements" class="text-muted"></span>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Your Club Teams</label>
                <div class="border rounded p-3 bg-dark bg-opacity-10" style="max-height: 240px; overflow-y: auto;">
                    @forelse($teams as $teamOption)
                        <div class="small text-white-50">{{ $teamOption->name }}</div>
                    @empty
                        <div class="small text-warning">No teams are currently registered for your club.</div>
                    @endforelse
                </div>
                <div class="form-text">Teams listed here are available for selection.</div>
            </div>

            <div class="col-md-6">
                <label for="start_date" class="form-label">Start Date *</label>
                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', optional(optional($tournament)->start_date)->format('Y-m-d')) }}" required>
                @error('start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Tournament start date (must be today or future)</div>
            </div>

            <div class="col-md-6">
                <label for="end_date" class="form-label">End Date *</label>
                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', optional(optional($tournament)->end_date)->format('Y-m-d')) }}" required>
                @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Tournament end date (must be after start date)</div>
            </div>

            <div class="col-md-6">
                <label for="registration_cutoff_date" class="form-label">Registration Cutoff Date *</label>
                <input type="date" class="form-control @error('registration_cutoff_date') is-invalid @enderror" id="registration_cutoff_date" name="registration_cutoff_date" value="{{ old('registration_cutoff_date', optional(optional($tournament)->registration_cutoff_date)->format('Y-m-d')) }}" required>
                @error('registration_cutoff_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Other clubs can join until this date.</div>
            </div>

            <div class="col-md-3">
                <label for="joining_fee" class="form-label">Joining Fee (USD) *</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" min="0" class="form-control @error('joining_fee') is-invalid @enderror" id="joining_fee" name="joining_fee" value="{{ old('joining_fee', optional($tournament)->joining_fee ?? '0.00') }}" required>
                </div>
                @error('joining_fee')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="form-text">Charge this amount when clubs register.</div>
            </div>

            <div class="col-md-3">
                <label for="joining_type" class="form-label">Joining Type *</label>
                @php($joiningType = old('joining_type', optional($tournament)->joining_type ?? 'per_club'))
                <select name="joining_type" id="joining_type" class="form-select @error('joining_type') is-invalid @enderror" required>
                    <option value="per_club" {{ $joiningType === 'per_club' ? 'selected' : '' }}>Per Club</option>
                    <option value="per_team" {{ $joiningType === 'per_team' ? 'selected' : '' }}>Per Team</option>
                </select>
                @error('joining_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Choose how the fee applies to registrations.</div>
            </div>

            <div class="col-md-4">
                <label for="country_id" class="form-label">Country *</label>
                <select name="country_id" id="country_id" class="form-select @error('country_id') is-invalid @enderror" required data-selected="{{ $selectedCountryId }}">
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ (string) $selectedCountryId === (string) $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                @error('country_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="state_id" class="form-label">State / Province *</label>
                <select name="state_id" id="state_id" class="form-select @error('state_id') is-invalid @enderror" required data-selected="{{ $selectedStateId }}">
                    <option value="">Select State</option>
                    @foreach($stateOptions as $state)
                        <option value="{{ $state->id }}" {{ (string) $selectedStateId === (string) $state->id ? 'selected' : '' }}>
                            {{ $state->name }}
                        </option>
                    @endforeach
                </select>
                @error('state_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">States update automatically after choosing a country</div>
            </div>

            <div class="col-md-4">
                <label for="city_id" class="form-label">City *</label>
                <select name="city_id" id="city_id" class="form-select @error('city_id') is-invalid @enderror" required data-selected="{{ $selectedCityId }}">
                    <option value="">Select City</option>
                    @foreach($cityOptions as $city)
                        <option value="{{ $city->id }}" {{ (string) $selectedCityId === (string) $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
                @error('city_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Cities update automatically after choosing a state</div>
            </div>

            <div class="col-md-4">
                <label for="venue_id" class="form-label">Registered Venue</label>
                <select name="venue_id" id="venue_id" class="form-select @error('venue_id') is-invalid @enderror" data-selected="{{ $selectedVenueId }}">
                    <option value="">Select Venue</option>
                    @foreach($venueOptions as $venue)
                        <option value="{{ $venue->id }}" data-location="{{ $venue->location }}" {{ (string) $selectedVenueId === (string) $venue->id ? 'selected' : '' }}>
                            {{ $venue->name }}
                        </option>
                    @endforeach
                </select>
                @error('venue_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Venues update after selecting a city that has club facilities.</div>
            </div>

            <div class="col-md-12">
                <label for="location" class="form-label">Venue / Location Details *</label>
                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $tournament->location ?? '') }}" required maxlength="255">
                @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Include specific venue details (e.g., facility name, address line)</div>
            </div>

            <div class="col-md-12">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" maxlength="1000">{{ old('description', $tournament->description ?? '') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    <span id="char-count">0</span>/1000 characters
                </div>
            </div>
        </div>
    </div>
</div>
