@extends('layouts.club-dashboard')

@section('title', 'Create Tournament')
@section('page_title', 'Create Tournament')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card text-white">
                <div class="card-header bg-primary">
                    <h4 class="card-title">New Tournament</h4>
                    <p class="card-subtitle">Create a new tournament for {{ $club->name }}</p>
                </div>
                @include('partials.alerts')
                <div class="card-body">
                    <form method="POST" action="{{ route('club.tournaments.store') }}" id="tournamentForm" novalidate>
                        @csrf

                        @include('club.tournaments.partials.form')

                        <div class="text-end">
                            <a href="{{ route('club.tournaments.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success" id="submitBtn">Create Tournament</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('tournamentForm');
    const submitBtn = document.getElementById('submitBtn');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const cutoffDate = document.getElementById('registration_cutoff_date');
    const teamSelect = document.getElementById('team_ids');
    const formatSelect = document.getElementById('tournament_format_id');
    const nameInput = document.getElementById('name');
    const descriptionTextarea = document.getElementById('description');
    const teamCountSpan = document.getElementById('team-count');
    const formatRequirementsSpan = document.getElementById('format-requirements');
    const charCountSpan = document.getElementById('char-count');
    const countrySelect = document.getElementById('country_id');
    const stateSelect = document.getElementById('state_id');
    const citySelect = document.getElementById('city_id');
    const genderSelect = document.getElementById('gender_id');
    const ageGroupSelect = document.getElementById('age_group_id');
    const classificationContainer = document.getElementById('classification-select-container');
    const classificationSelects = classificationContainer ? Array.from(classificationContainer.querySelectorAll('.classification-select')) : [];
    const venueSelect = document.getElementById('venue_id');
    const locationInput = document.getElementById('location');
    const joiningFeeInput = document.getElementById('joining_fee');
    const joiningTypeSelect = document.getElementById('joining_type');
    const preselectedTeams = new Set(@json(collect(old('team_ids', isset($tournament) ? $tournament->teams->pluck('id')->all() ?? [] : []))->map(fn($id) => (string) $id)->all()));
    const statesEndpoint = @json(route('register.states-for-country', [], false));
    const citiesEndpoint = @json(route('register.cities-for-state', [], false));
    const venuesEndpoint = @json(route('club.locations.venues', [], false));
    const initialVenueId = venueSelect ? (venueSelect.dataset.selected || venueSelect.value) : '';
    let locationAutoValue = locationInput ? locationInput.value : '';

    // Set minimum date for start_date to today
    const today = new Date().toISOString().split('T')[0];
    startDate.min = today;
    if (cutoffDate) {
        cutoffDate.min = today;
    }

    // Update end_date minimum when start_date changes
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
        if (cutoffDate) {
            cutoffDate.max = this.value || '';
            if (cutoffDate.value && this.value && cutoffDate.value > this.value) {
                cutoffDate.value = this.value;
            }
        }
        validateDates();
    });

    endDate.addEventListener('change', validateDates);
    if (cutoffDate) {
        if (startDate.value) {
            cutoffDate.max = startDate.value;
        }
        cutoffDate.addEventListener('change', validateDates);
    }
    if (joiningFeeInput) {
        joiningFeeInput.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    }
    if (joiningTypeSelect) {
        joiningTypeSelect.addEventListener('change', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    }

    // Team selection validation
    teamSelect.addEventListener('change', function() {
        updateTeamCount();
        validateTeamSelection();
        updateFormatRequirements();
    });

    // Format selection validation
    formatSelect.addEventListener('change', function() {
        updateFormatRequirements();
        validateTeamSelection();
    });

    // Character count for description
    descriptionTextarea.addEventListener('input', function() {
        const count = this.value.length;
        charCountSpan.textContent = count;
        
        if (count > 1000) {
            this.classList.add('is-invalid');
            charCountSpan.classList.add('text-danger');
        } else {
            this.classList.remove('is-invalid');
            charCountSpan.classList.remove('text-danger');
        }
    });

    countrySelect.addEventListener('change', function() {
        loadStates(this.value);
    });

    stateSelect.addEventListener('change', function() {
        loadCities(this.value);
    });

    citySelect.addEventListener('change', function() {
        loadVenues(this.value);
    });

    if (genderSelect) {
        genderSelect.addEventListener('change', function () {
            genderSelect.dataset.selected = genderSelect.value;
            loadTeamsForClub();
        });
    }

    if (ageGroupSelect) {
        ageGroupSelect.addEventListener('change', function () {
            ageGroupSelect.dataset.selected = ageGroupSelect.value;
            loadTeamsForClub();
        });
    }

    if (classificationContainer) {
        classificationContainer.addEventListener('change', function (event) {
            if (event.target && event.target.classList.contains('classification-select')) {
                loadTeamsForClub();
            }
        });
    }

    if (venueSelect) {
        venueSelect.addEventListener('change', syncLocationWithVenue);
    }

    if (locationInput) {
        locationInput.addEventListener('input', function () {
            if (locationAutoValue !== null && this.value !== locationAutoValue) {
                locationAutoValue = null;
            }
        });
    }

    // Name validation
    nameInput.addEventListener('input', function() {
        const value = this.value.trim();
        if (value.length < 3) {
            this.classList.add('is-invalid');
            showFieldError(this, 'Tournament name must be at least 3 characters long');
        } else if (value.length > 255) {
            this.classList.add('is-invalid');
            showFieldError(this, 'Tournament name cannot exceed 255 characters');
        } else {
            this.classList.remove('is-invalid');
            clearFieldError(this);
        }
    });

    // Real-time validation for all required fields
    const requiredFields = form.querySelectorAll('input[required], select[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
        
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...';
            form.submit();
        }
    });

    // Validation functions
    function validateForm() {
        let isValid = true;
        
        // Validate all required fields
        requiredFields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        // Validate team selection
        if (!validateTeamSelection()) {
            isValid = false;
        }

        // Validate dates
        if (!validateDates()) {
            isValid = false;
        }

        // Validate name length
        if (nameInput.value.trim().length < 3 || nameInput.value.trim().length > 255) {
            isValid = false;
        }

        // Validate description length
        if (descriptionTextarea.value.length > 1000) {
            isValid = false;
        }

        return isValid;
    }

    function buildTeamFilterParams() {
        const params = new URLSearchParams();
        if (genderSelect && genderSelect.value) {
            params.append('gender_id', genderSelect.value);
        }
        if (ageGroupSelect && ageGroupSelect.value) {
            params.append('age_group_id', ageGroupSelect.value);
        }
        classificationSelects.forEach(select => {
            if (select.value) {
                params.append('classification_option_ids[]', select.value);
            }
        });
        return params;
    }

    // Load teams filtered by club's sport and eligibility filters
    async function loadTeamsForClub() {
        try {
            const params = buildTeamFilterParams();
            const queryString = params.toString();
            const endpoint = `/club/teams-for-host-club/{{ $club->id }}` + (queryString ? `?${queryString}` : '');
            const res = await fetch(endpoint, { headers: { 'Accept': 'application/json' }});
            if (!res.ok) {
                throw new Error('Unable to load teams');
            }
            const data = await res.json();
            const existingSelections = new Set(Array.from(teamSelect.selectedOptions).map(opt => opt.value));
            teamSelect.innerHTML = '';

            (data.teams || []).forEach(t => {
                const opt = document.createElement('option');
                opt.value = String(t.id);
                opt.textContent = t.name;
                if (preselectedTeams.has(String(t.id)) || existingSelections.has(String(t.id))) {
                    opt.selected = true;
                }
                teamSelect.appendChild(opt);
            });
            updateTeamCount();
            validateTeamSelection();
            updateFormatRequirements();
        } catch(_) {}
    }

    function validateField(field) {
        const value = field.value.trim();
        
        if (field.hasAttribute('required') && !value) {
            field.classList.add('is-invalid');
            showFieldError(field, 'This field is required');
            return false;
        }

        // Specific field validations
        if (field === nameInput) {
            if (value.length < 3) {
                field.classList.add('is-invalid');
                showFieldError(field, 'Tournament name must be at least 3 characters long');
                return false;
            }
        }

        if (field === startDate) {
            if (value < today) {
                field.classList.add('is-invalid');
                showFieldError(field, 'Start date must be today or a future date');
                return false;
            }
        }

        if (field === endDate) {
            if (startDate.value && value < startDate.value) {
                field.classList.add('is-invalid');
                showFieldError(field, 'End date must be after start date');
                return false;
            }
        }

        if (field === cutoffDate) {
            if (value < today) {
                field.classList.add('is-invalid');
                showFieldError(field, 'Cutoff date must be today or a future date');
                return false;
            }
            if (startDate.value && value > startDate.value) {
                field.classList.add('is-invalid');
                showFieldError(field, 'Cutoff date must be on or before the start date');
                return false;
            }
        }

        if (field === joiningFeeInput) {
            const numeric = Number(value);
            if (Number.isNaN(numeric) || numeric < 0) {
                field.classList.add('is-invalid');
                showFieldError(field, 'Joining fee must be a positive amount');
                return false;
            }
        }

        field.classList.remove('is-invalid');
        clearFieldError(field);
        return true;
    }

    function validateTeamSelection() {
        const selectedTeams = Array.from(teamSelect.selectedOptions);
        const formatId = formatSelect.value;
        
        if (selectedTeams.length === 0) {
            teamSelect.classList.add('is-invalid');
            showFieldError(teamSelect, 'Please select at least one team');
            return false;
        }

        // Check for duplicate teams
        const teamIds = selectedTeams.map(option => option.value);
        const uniqueIds = [...new Set(teamIds)];
        if (teamIds.length !== uniqueIds.length) {
            teamSelect.classList.add('is-invalid');
            showFieldError(teamSelect, 'Each team can only be selected once');
            return false;
        }

        // Format-specific validation
        if (formatId) {
            const minTeams = getMinTeamsForFormat(formatId);
            if (selectedTeams.length < minTeams) {
                teamSelect.classList.add('is-invalid');
                showFieldError(teamSelect, `${getFormatName(formatId)} requires at least ${minTeams} teams`);
                return false;
            }
        }

        teamSelect.classList.remove('is-invalid');
        clearFieldError(teamSelect);
        return true;
    }

    function validateDates() {
        let isValid = true;
        
        if (startDate.value && endDate.value) {
            if (endDate.value < startDate.value) {
                endDate.classList.add('is-invalid');
                showFieldError(endDate, 'End date must be after start date');
                isValid = false;
            } else {
                endDate.classList.remove('is-invalid');
                clearFieldError(endDate);
            }
        }

        if (startDate.value && startDate.value < today) {
            startDate.classList.add('is-invalid');
            showFieldError(startDate, 'Start date must be today or a future date');
            isValid = false;
        }

        if (cutoffDate && cutoffDate.value) {
            if (cutoffDate.value < today) {
                cutoffDate.classList.add('is-invalid');
                showFieldError(cutoffDate, 'Cutoff date must be today or a future date');
                isValid = false;
            } else if (startDate.value && cutoffDate.value > startDate.value) {
                cutoffDate.classList.add('is-invalid');
                showFieldError(cutoffDate, 'Cutoff date must be on or before the start date');
                isValid = false;
            } else {
                cutoffDate.classList.remove('is-invalid');
                clearFieldError(cutoffDate);
            }
        }

        return isValid;
    }

    function updateTeamCount() {
        const count = teamSelect.selectedOptions.length;
        teamCountSpan.textContent = count;
    }

    function updateFormatRequirements() {
        const formatId = formatSelect.value;
        const teamCount = teamSelect.selectedOptions.length;
        
        if (formatId) {
            const minTeams = getMinTeamsForFormat(formatId);
            const formatName = getFormatName(formatId);
            
            if (teamCount < minTeams) {
                formatRequirementsSpan.textContent = `${formatName} requires at least ${minTeams} teams`;
                formatRequirementsSpan.className = 'text-danger';
            } else {
                formatRequirementsSpan.textContent = `✓ ${formatName} requirements met`;
                formatRequirementsSpan.className = 'text-success';
            }
        } else {
            formatRequirementsSpan.textContent = '';
        }
    }

    async function loadStates(countryId, selectedState = '', selectedCity = '') {
        resetSelect(stateSelect, 'Select State');
        resetSelect(citySelect, 'Select City');

        if (!countryId) {
            stateSelect.disabled = true;
            citySelect.disabled = true;
            return;
        }

        stateSelect.disabled = true;
        try {
            const response = await fetch(`${statesEndpoint}?country_id=${countryId}`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            const json = await response.json();
            populateSelect(stateSelect, json.data || [], 'Select State', selectedState);
            stateSelect.disabled = false;
            if (selectedState) {
                await loadCities(selectedState, selectedCity);
            }
        } catch (error) {
            resetSelect(stateSelect, 'Unable to load states');
            stateSelect.disabled = false;
        }
    }

    async function loadCities(stateId, selectedCity = '') {
        resetSelect(citySelect, 'Select City');
        resetSelect(venueSelect, 'Select Venue');

        if (!stateId) {
            citySelect.disabled = true;
            if (venueSelect) {
                venueSelect.disabled = true;
            }
            return;
        }

        citySelect.disabled = true;
        try {
            const response = await fetch(`${citiesEndpoint}?state_id=${stateId}`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            const json = await response.json();
            populateSelect(citySelect, json.data || [], 'Select City', selectedCity);
            citySelect.disabled = false;
            if (selectedCity) {
                await loadVenues(selectedCity, initialVenueId);
            }
        } catch (error) {
            resetSelect(citySelect, 'Unable to load cities');
            citySelect.disabled = false;
            if (venueSelect) {
                resetSelect(venueSelect, 'Unable to load venues');
                venueSelect.disabled = false;
            }
        }
    }

    async function loadVenues(cityId, selectedVenue = '') {
        if (!venueSelect) {
            return;
        }

        resetSelect(venueSelect, 'Select Venue');

        if (!cityId) {
            venueSelect.disabled = true;
            return;
        }

        venueSelect.disabled = true;
        try {
            const response = await fetch(`${venuesEndpoint}?city_id=${cityId}`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            const json = await response.json();
            const items = json.data || [];
            const fragment = document.createDocumentFragment();
            const placeholderOption = document.createElement('option');
            placeholderOption.value = '';
            placeholderOption.textContent = 'Select Venue';
            fragment.appendChild(placeholderOption);

            items.forEach(item => {
                const option = document.createElement('option');
                option.value = String(item.id);
                option.textContent = item.name;
                option.dataset.location = item.location || '';
                if (selectedVenue && String(selectedVenue) === String(item.id)) {
                    option.selected = true;
                }
                fragment.appendChild(option);
            });

            venueSelect.innerHTML = '';
            venueSelect.appendChild(fragment);
            venueSelect.disabled = false;

            if (selectedVenue) {
                syncLocationWithVenue();
            }
        } catch (error) {
            resetSelect(venueSelect, 'Unable to load venues');
            venueSelect.disabled = false;
        }
    }

    function populateSelect(selectEl, items, placeholder, selectedValue = '') {
        const fragment = document.createDocumentFragment();
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = placeholder;
        fragment.appendChild(placeholderOption);

        items.forEach(item => {
            const option = document.createElement('option');
            option.value = String(item.id);
            option.textContent = item.name;
            if (selectedValue && String(selectedValue) === String(item.id)) {
                option.selected = true;
            }
            fragment.appendChild(option);
        });

        selectEl.innerHTML = '';
        selectEl.appendChild(fragment);
    }

    function resetSelect(selectEl, placeholder) {
        selectEl.innerHTML = '';
        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;
        selectEl.appendChild(option);
        selectEl.value = '';
        selectEl.classList.remove('is-invalid');
        clearFieldError(selectEl);
    }

    async function initializeLocationSelectors() {
        const initialCountry = countrySelect.dataset.selected || countrySelect.value;
        const initialState = stateSelect.dataset.selected || stateSelect.value;
        const initialCity = citySelect.dataset.selected || citySelect.value;

        if (initialCountry) {
            await loadStates(initialCountry, initialState, initialCity);
        } else {
            stateSelect.disabled = true;
            citySelect.disabled = true;
            if (venueSelect) {
                venueSelect.disabled = true;
            }
        }
    }

    function syncLocationWithVenue() {
        if (!venueSelect || !locationInput) {
            return;
        }

        const selectedOption = venueSelect.options[venueSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.location) {
            locationInput.value = selectedOption.dataset.location;
            locationAutoValue = selectedOption.dataset.location;
        }
    }

    function getMinTeamsForFormat(formatId) {
        switch (parseInt(formatId)) {
            case 1: return 2; // Round Robin
            case 2: return 2; // Knockout
            case 3: return 4; // Group Stage
            default: return 2;
        }
    }

    function getFormatName(formatId) {
        switch (parseInt(formatId)) {
            case 1: return 'Round Robin';
            case 2: return 'Knockout';
            case 3: return 'Group Stage';
            default: return 'Tournament';
        }
    }

    function showFieldError(field, message) {
        clearFieldError(field);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    function clearFieldError(field) {
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
    }

    // Initialize
    initializeLocationSelectors();
    loadTeamsForClub();
    updateTeamCount();
    updateFormatRequirements();
    if (descriptionTextarea.value) {
        charCountSpan.textContent = descriptionTextarea.value.length;
    }
    if (initialVenueId) {
        syncLocationWithVenue();
    }
});
</script>
@endsection
