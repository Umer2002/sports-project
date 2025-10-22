@extends('layouts.admin')

@section('title', 'Edit Tournament')

@section('content')
<section class="content-header">
    <h1>Edit Tournament</h1>
</section>

    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="card text-white">
                <div class="card-header bg-primary">
                    <h4 class="card-title">Edit Tournament</h4>
                </div>
                @include('partials.alerts')
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tournaments.update', $tournament->id) }}" id="tournamentForm" novalidate>
                        @csrf
                        @method('PUT')

                        @include('admin.tournaments.partials.form')

                        <div class="text-end">
                            <a href="{{ route('admin.tournaments.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success" id="submitBtn">Update Tournament</button>
                        </div>
                    </form>
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
    const teamSelect = document.getElementById('team_ids');
    const clubSelect = document.getElementById('host_club_id');
    const formatSelect = document.getElementById('tournament_format_id');
    const nameInput = document.getElementById('name');
    const descriptionTextarea = document.getElementById('description');
    const teamCountSpan = document.getElementById('team-count');
    const formatRequirementsSpan = document.getElementById('format-requirements');
    const charCountSpan = document.getElementById('char-count');
    const countrySelect = document.getElementById('country_id');
    const stateSelect = document.getElementById('state_id');
    const citySelect = document.getElementById('city_id');
    const venueSelect = document.getElementById('venue_id');
    const locationInput = document.getElementById('location');
    const statesEndpoint = @json(route('register.states-for-country', [], false));
    const citiesEndpoint = @json(route('register.cities-for-state', [], false));
    const venuesEndpoint = @json(route('admin.locations.venues', [], false));
    const initialVenueId = venueSelect ? (venueSelect.dataset.selected || venueSelect.value) : '';
    const genderSelect = document.getElementById('gender_id');
    const ageGroupSelect = document.getElementById('age_group_id');
    const classificationContainer = document.getElementById('classification-select-container');
    const initialEligibility = @json($eligibilityData);
    let currentGenderSelection = genderSelect ? (genderSelect.dataset.selected || '') : '';
    let currentAgeGroupSelection = ageGroupSelect ? (ageGroupSelect.dataset.selected || '') : '';
    let initialClassificationSelections = [];
    let currentClassificationSelections = {};
    if (classificationContainer) {
        const rawSelected = classificationContainer.dataset.selected || '[]';
        try {
            const parsed = JSON.parse(rawSelected);
            if (Array.isArray(parsed)) {
                initialClassificationSelections = parsed.map(String);
            } else if (typeof parsed === 'string') {
                initialClassificationSelections = parsed.split(',').filter(Boolean);
            }
        } catch (error) {
            initialClassificationSelections = rawSelected.split(',').filter(Boolean);
        }
    }
    let locationAutoValue = locationInput ? locationInput.value : '';

    // Set minimum date for start_date to today
    const today = new Date().toISOString().split('T')[0];
    startDate.min = today;

    // Update end_date minimum when start_date changes
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
        validateDates();
    });

    endDate.addEventListener('change', validateDates);

    // Team selection validation
    teamSelect.addEventListener('change', function() {
        updateTeamCount();
        validateTeamSelection();
        updateFormatRequirements();
        const selectedIds = Array.from(teamSelect.selectedOptions).map(option => option.value);
        teamSelect.dataset.selected = JSON.stringify(selectedIds);
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
            currentGenderSelection = genderSelect.value;
            genderSelect.dataset.selected = currentGenderSelection;
            handleEligibilityFilterChange();
        });
    }

    if (ageGroupSelect) {
        ageGroupSelect.addEventListener('change', function () {
            currentAgeGroupSelection = ageGroupSelect.value;
            ageGroupSelect.dataset.selected = currentAgeGroupSelection;
            handleEligibilityFilterChange();
        });
    }

    registerClassificationSelectListeners();
    updateCurrentClassificationSelectionsFromDOM();

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

    // Load teams filtered by selected club and eligibility criteria
    async function loadTeamsForClub(clubId, { preserveSelections = true, refreshEligibility = true } = {}) {
        const datasetSelections = JSON.parse(teamSelect.dataset.selected || '[]');
        const previousSelections = preserveSelections ? datasetSelections.map(String) : [];
        const criteria = getEligibilityCriteria();

        teamSelect.innerHTML = '';
        teamSelect.disabled = true;

        if (!clubId) {
            teamSelect.dataset.selected = JSON.stringify([]);
            if (refreshEligibility) {
                populateEligibilitySelectors({
                    genders: [],
                    age_groups: [],
                    classification_groups: [],
                }, {
                    genderId: '',
                    ageGroupId: '',
                    classificationSelection: {},
                });
            }
            updateTeamCount();
            validateTeamSelection();
            teamSelect.disabled = false;
            return;
        }

        const params = new URLSearchParams();
        if (criteria.genderId) {
            params.append('gender_id', criteria.genderId);
        }
        if (criteria.ageGroupId) {
            params.append('age_group_id', criteria.ageGroupId);
        }
        criteria.classificationOptionIds.forEach(id => {
            params.append('classification_option_ids[]', id);
        });

        const url = params.toString()
            ? `/admin/clubs/${clubId}/teams-for-sport?${params.toString()}`
            : `/admin/clubs/${clubId}/teams-for-sport`;

        try {
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (!res.ok) {
                throw new Error('Unable to load teams');
            }

            const data = await res.json();

            if (refreshEligibility) {
                const desiredClassification = Object.keys(currentClassificationSelections).length
                    ? currentClassificationSelections
                    : initialClassificationSelections;

                populateEligibilitySelectors(data, {
                    genderId: criteria.genderId,
                    ageGroupId: criteria.ageGroupId,
                    classificationSelection: desiredClassification,
                });
            }

            renderTeamOptions(data.teams || [], previousSelections);
        } catch (error) {
            renderTeamOptions([], []);
        } finally {
            teamSelect.disabled = false;
            teamSelect.dataset.selected = JSON.stringify(Array.from(teamSelect.selectedOptions).map(option => option.value));
            updateTeamCount();
            validateTeamSelection();
            updateFormatRequirements();
        }
    }

    // When host club changes, reload teams and eligibility data
    clubSelect.addEventListener('change', function(){
        currentGenderSelection = '';
        currentAgeGroupSelection = '';
        currentClassificationSelections = {};
        initialClassificationSelections = [];
        if (genderSelect) {
            genderSelect.value = '';
            genderSelect.dataset.selected = '';
        }
        if (ageGroupSelect) {
            ageGroupSelect.value = '';
            ageGroupSelect.dataset.selected = '';
        }
        if (classificationContainer) {
            classificationContainer.dataset.selected = '[]';
            classificationContainer.querySelectorAll('select.classification-select').forEach(select => {
                select.value = '';
            });
        }
        teamSelect.dataset.selected = JSON.stringify([]);
        loadTeamsForClub(this.value, { preserveSelections: false, refreshEligibility: true });
    });

    // Initial load if a club is preselected
    if (clubSelect.value) {
        loadTeamsForClub(clubSelect.value, { preserveSelections: true, refreshEligibility: true });
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

    async function loadStates(countryId, selectedState = '', selectedCity = '', selectedVenue = '') {
        resetSelect(stateSelect, 'Select State', true);
        resetSelect(citySelect, 'Select City', true);
        if (venueSelect) {
            resetSelect(venueSelect, 'Select Venue', true);
        }

        if (!countryId) {
            return;
        }

        try {
            const response = await fetch(`${statesEndpoint}?country_id=${countryId}`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            const json = await response.json();
            populateSelect(stateSelect, json.data || [], 'Select State', selectedState);
            stateSelect.disabled = false;
            if (selectedState) {
                await loadCities(selectedState, selectedCity, selectedVenue);
            }
        } catch (error) {
            resetSelect(stateSelect, 'Unable to load states');
            stateSelect.disabled = false;
        }
    }

    async function loadCities(stateId, selectedCity = '', selectedVenue = '') {
        resetSelect(citySelect, 'Select City', true);
        if (venueSelect) {
            resetSelect(venueSelect, 'Select Venue', true);
        }

        if (!stateId) {
            return;
        }

        try {
            const response = await fetch(`${citiesEndpoint}?state_id=${stateId}`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            const json = await response.json();
            populateSelect(citySelect, json.data || [], 'Select City', selectedCity);
            citySelect.disabled = false;
            if (selectedCity) {
                await loadVenues(selectedCity, selectedVenue);
            }
        } catch (error) {
            resetSelect(citySelect, 'Unable to load cities');
            citySelect.disabled = false;
        }
    }

    async function loadVenues(cityId, selectedVenue = '') {
        if (!venueSelect) {
            return;
        }

        resetSelect(venueSelect, 'Select Venue', true);

        if (!cityId) {
            return;
        }

        try {
            const response = await fetch(`${venuesEndpoint}?city_id=${cityId}`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            const json = await response.json();
            populateSelect(venueSelect, json.data || [], 'Select Venue', selectedVenue);
            venueSelect.disabled = false;

            if (venueSelect.value) {
                syncLocationWithVenue();
            }
        } catch (error) {
            resetSelect(venueSelect, 'Unable to load venues');
            venueSelect.disabled = false;
        }
    }

    function syncLocationWithVenue() {
        if (!venueSelect || !locationInput) {
            return;
        }

        const option = venueSelect.selectedOptions[0];
        if (!option) {
            return;
        }

        const venueLocation = option.dataset.location || option.textContent;

        if (locationAutoValue === null && locationInput.value && locationInput.value !== venueLocation) {
            return;
        }

        locationInput.value = venueLocation;
        locationAutoValue = venueLocation;
        locationInput.classList.remove('is-invalid');
        clearFieldError(locationInput);
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
            if (Object.prototype.hasOwnProperty.call(item, 'location')) {
                option.dataset.location = item.location ?? '';
            }
            if (selectedValue && String(selectedValue) === String(item.id)) {
                option.selected = true;
            }
            fragment.appendChild(option);
        });

        selectEl.innerHTML = '';
        selectEl.appendChild(fragment);
    }

    function resetSelect(selectEl, placeholder, disable = false) {
        if (!selectEl) {
            return;
        }
        selectEl.innerHTML = '';
        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;
        selectEl.appendChild(option);
        selectEl.value = '';
        selectEl.classList.remove('is-invalid');
        clearFieldError(selectEl);
        selectEl.disabled = disable;
    }

    function populateClassificationSelectors(groups, desiredSelection = {}) {
        if (!classificationContainer) {
            currentClassificationSelections = {};
            return;
        }

        const normalizedSelection = buildClassificationSelectionMap(groups, desiredSelection);
        classificationContainer.innerHTML = '';

        if (!groups || !groups.length) {
            const placeholder = document.createElement('div');
            placeholder.className = 'col-12 text-muted';
            placeholder.dataset.emptyPlaceholder = 'true';
            placeholder.textContent = 'Select a host club to load sport-specific options.';
            classificationContainer.appendChild(placeholder);
            currentClassificationSelections = {};
            classificationContainer.dataset.selected = '[]';
            initialClassificationSelections = [];
            return;
        }

        const fragment = document.createDocumentFragment();
        groups.forEach(group => {
            const column = document.createElement('div');
            column.className = 'col-lg-4 col-md-6 classification-group';

            const groupId = String(group.id);
            const label = document.createElement('label');
            label.className = 'form-label';
            label.setAttribute('for', `classification_group_${groupId}`);
            label.textContent = group.name;

            const select = document.createElement('select');
            select.name = 'classification_option_ids[]';
            select.id = `classification_group_${groupId}`;
            select.className = 'form-select classification-select';
            select.dataset.groupId = groupId;

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = `Any ${group.name}`;
            select.appendChild(placeholder);

            const desiredForGroup = normalizedSelection[groupId] ? String(normalizedSelection[groupId]) : '';

            (group.options || []).forEach(option => {
                const optionEl = document.createElement('option');
                optionEl.value = String(option.id);
                optionEl.textContent = option.label || option.name;
                if (desiredForGroup === String(option.id)) {
                    optionEl.selected = true;
                }
                select.appendChild(optionEl);
            });

            column.appendChild(label);
            column.appendChild(select);
            fragment.appendChild(column);
        });

        classificationContainer.appendChild(fragment);
        currentClassificationSelections = normalizedSelection;
        updateCurrentClassificationSelectionsFromDOM();
        registerClassificationSelectListeners();
        initialClassificationSelections = [];
    }

    function populateEligibilitySelectors(eligibility, overrides = {}) {
        const genderOptions = eligibility.genders || [];
        const ageGroupOptions = eligibility.age_groups || [];
        const classificationGroups = eligibility.classification_groups || [];

        const desiredGender = overrides.genderId !== undefined ? overrides.genderId : currentGenderSelection;
        const desiredAgeGroup = overrides.ageGroupId !== undefined ? overrides.ageGroupId : currentAgeGroupSelection;
        const desiredClassifications = overrides.classificationSelection !== undefined
            ? overrides.classificationSelection
            : (Object.keys(currentClassificationSelections).length ? currentClassificationSelections : initialClassificationSelections);

        if (genderSelect) {
            if (genderOptions.length) {
                populateSelect(genderSelect, genderOptions, 'All / Mixed', desiredGender);
                genderSelect.disabled = false;
            } else {
                resetSelect(genderSelect, 'All / Mixed');
                genderSelect.disabled = false;
            }
            currentGenderSelection = genderSelect.value;
            genderSelect.dataset.selected = currentGenderSelection;
        } else {
            currentGenderSelection = '';
        }

        if (ageGroupSelect) {
            if (ageGroupOptions.length) {
                populateSelect(ageGroupSelect, ageGroupOptions, 'All Ages', desiredAgeGroup);
                ageGroupSelect.disabled = false;
            } else {
                resetSelect(ageGroupSelect, 'All Ages');
                ageGroupSelect.disabled = false;
            }
            currentAgeGroupSelection = ageGroupSelect.value;
            ageGroupSelect.dataset.selected = currentAgeGroupSelection;
        } else {
            currentAgeGroupSelection = '';
        }

        populateClassificationSelectors(classificationGroups, desiredClassifications);
    }

    function buildClassificationSelectionMap(groups, desired) {
        const selectionMap = {};
        const normalizedGroups = Array.isArray(groups) ? groups : [];
        const desiredIds = new Set();

        if (Array.isArray(desired)) {
            desired.map(String).forEach(id => desiredIds.add(id));
        } else if (desired && typeof desired === 'object') {
            Object.entries(desired).forEach(([groupId, optionId]) => {
                if (optionId !== undefined && optionId !== null && optionId !== '') {
                    const groupKey = String(groupId);
                    const optionKey = String(optionId);
                    selectionMap[groupKey] = optionKey;
                    desiredIds.add(optionKey);
                }
            });
        } else if (typeof desired === 'string') {
            desired.split(',').filter(Boolean).forEach(id => desiredIds.add(String(id)));
        }

        normalizedGroups.forEach(group => {
            const groupId = String(group.id);
            const options = Array.isArray(group.options) ? group.options : [];

            if (selectionMap[groupId]) {
                const stillExists = options.some(option => String(option.id) === selectionMap[groupId]);
                if (!stillExists) {
                    delete selectionMap[groupId];
                }
                return;
            }

            const match = options.find(option => desiredIds.has(String(option.id)));
            if (match) {
                selectionMap[groupId] = String(match.id);
            }
        });

        return selectionMap;
    }

    function registerClassificationSelectListeners() {
        if (!classificationContainer) {
            return;
        }

        const selects = classificationContainer.querySelectorAll('select.classification-select');
        selects.forEach(select => {
            if (select.dataset.listenerAttached === 'true') {
                return;
            }
            select.addEventListener('change', () => {
                updateCurrentClassificationSelectionsFromDOM();
                handleEligibilityFilterChange();
            });
            select.dataset.listenerAttached = 'true';
        });
    }

    function updateCurrentClassificationSelectionsFromDOM() {
        if (!classificationContainer) {
            currentClassificationSelections = {};
            return;
        }

        const selects = classificationContainer.querySelectorAll('select.classification-select');
        const map = {};
        const ids = [];

        selects.forEach(select => {
            const groupId = select.dataset.groupId || '';
            const value = select.value;
            if (value) {
                const normalizedValue = String(value);
                map[groupId] = normalizedValue;
                ids.push(normalizedValue);
            }
        });

        currentClassificationSelections = map;
        if (classificationContainer) {
            classificationContainer.dataset.selected = JSON.stringify(ids);
        }
    }

    function getEligibilityCriteria() {
        return {
            genderId: genderSelect ? genderSelect.value : '',
            ageGroupId: ageGroupSelect ? ageGroupSelect.value : '',
            classificationOptionIds: classificationContainer
                ? Array.from(classificationContainer.querySelectorAll('select.classification-select'))
                    .map(select => select.value)
                    .filter(value => value)
                    .map(value => String(value))
                : [],
        };
    }

    function handleEligibilityFilterChange({ preserveTeamSelection = true } = {}) {
        const clubId = clubSelect.value;
        if (!clubId) {
            return;
        }

        teamSelect.dataset.selected = JSON.stringify(Array.from(teamSelect.selectedOptions).map(option => option.value));
        loadTeamsForClub(clubId, {
            preserveSelections: preserveTeamSelection,
            refreshEligibility: false,
        });
    }

    function renderTeamOptions(teams, previousSelections = []) {
        teamSelect.innerHTML = '';

        if (!Array.isArray(teams) || teams.length === 0) {
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.disabled = true;
            placeholder.textContent = 'No teams match current filters';
            teamSelect.appendChild(placeholder);
            return;
        }

        const previousSet = new Set(previousSelections.map(String));
        const fragment = document.createDocumentFragment();

        teams.forEach(team => {
            const option = document.createElement('option');
            option.value = String(team.id);
            option.textContent = team.name;
            if (previousSet.has(String(team.id))) {
                option.selected = true;
            }
            fragment.appendChild(option);
        });

        teamSelect.appendChild(fragment);
    }

    async function initializeLocationSelectors() {
        const initialCountry = countrySelect.dataset.selected || countrySelect.value;
        const initialState = stateSelect.dataset.selected || stateSelect.value;
        const initialCity = citySelect.dataset.selected || citySelect.value;

        if (initialCountry) {
            await loadStates(initialCountry, initialState, initialCity, initialVenueId);
            if (!initialCity && venueSelect) {
                venueSelect.disabled = true;
            }
        } else {
            stateSelect.disabled = true;
            citySelect.disabled = true;
            if (venueSelect) {
                venueSelect.disabled = true;
            }
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
    populateEligibilitySelectors(initialEligibility, {
        genderId: currentGenderSelection,
        ageGroupId: currentAgeGroupSelection,
        classificationSelection: Object.keys(currentClassificationSelections).length
            ? currentClassificationSelections
            : initialClassificationSelections,
    });
    initializeLocationSelectors();
    updateTeamCount();
    updateFormatRequirements();
    if (descriptionTextarea.value) {
        charCountSpan.textContent = descriptionTextarea.value.length;
    }
});
</script>
@endsection
