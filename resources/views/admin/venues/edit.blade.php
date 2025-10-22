@extends('layouts.admin')
@section('content')
<div class="card p-4">
    <h4>Edit Venue</h4>
    <form action="{{ route('admin.venues.update', $venue) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name', $venue->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Type</label>
                <input type="text" name="type" value="{{ old('type', $venue->type) }}" class="form-control @error('type') is-invalid @enderror">
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Country</label>
                <select name="country_id" id="venue-country" class="form-select @error('country_id') is-invalid @enderror" required>
                    <option value="">Select a country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" @selected(old('country_id', $venue->country_id) == $country->id)>{{ $country->name }}</option>
                    @endforeach
                </select>
                @error('country_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">State</label>
                <select name="state_id" id="venue-state" class="form-select @error('state_id') is-invalid @enderror" required {{ $states->isEmpty() ? 'disabled' : '' }}>
                    <option value="">Select a state</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}" @selected(old('state_id', $venue->state_id) == $state->id)>{{ $state->name }}</option>
                    @endforeach
                </select>
                @error('state_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">City</label>
                <select name="city_id" id="venue-city" class="form-select @error('city_id') is-invalid @enderror" required {{ $cities->isEmpty() ? 'disabled' : '' }}>
                    <option value="">Select a city</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" @selected(old('city_id', $venue->city_id) == $city->id)>{{ $city->name }}</option>
                    @endforeach
                </select>
                @error('city_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Capacity</label>
                <input type="number" name="capacity" value="{{ old('capacity', $venue->capacity) }}" class="form-control @error('capacity') is-invalid @enderror" min="0">
                @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Location (override)</label>
                <input type="text" name="location" id="venue-location" value="{{ old('location', $venue->location) }}" class="form-control @error('location') is-invalid @enderror" placeholder="Search or keep default location">
                @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mt-4">
            <button class="btn btn-primary">Update</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (function ($) {
        const countrySelect = $('#venue-country');
        const stateSelect = $('#venue-state');
        const citySelect = $('#venue-city');
        const statesUrl = '{{ route('admin.locations.venue.states') }}';
        const citiesUrl = '{{ route('admin.locations.venue.cities') }}';
        const initialCountryId = @json(old('country_id', $venue->country_id));
        const initialStateId = @json(old('state_id', $venue->state_id));
        const initialCityId = @json(old('city_id', $venue->city_id));
        const locationInput = document.getElementById('venue-location');
        let locationManuallySet = !!(locationInput && locationInput.value.trim());

        function getSelectedName($select) {
            const $option = $select.find('option:selected');
            if (!$option.length || !$option.val()) {
                return '';
            }
            return $option.text().trim();
        }

        function composeLocationFromSelections() {
            const cityName = getSelectedName(citySelect);
            if (!cityName) {
                return '';
            }

            const stateName = getSelectedName(stateSelect);
            const countryName = getSelectedName(countrySelect);

            return [cityName, stateName, countryName].filter(Boolean).join(', ');
        }

        function updateLocationFromDropdown(options = {}) {
            if (!locationInput) {
                return;
            }

            const { force = false } = options;
            const locationLabel = composeLocationFromSelections();
            const currentValue = locationInput.value.trim();
            const wasAutofilled = locationInput.dataset.autofill === 'dropdown';
            const shouldUpdate = force || !currentValue || wasAutofilled || !locationManuallySet;

            if (!shouldUpdate) {
                return;
            }

            if (locationLabel) {
                locationInput.value = locationLabel;
                locationInput.dataset.autofill = 'dropdown';
                locationManuallySet = false;
                return;
            }

            if (wasAutofilled || force) {
                locationInput.value = '';
                delete locationInput.dataset.autofill;
                locationManuallySet = false;
            }
        }

        if (locationInput) {
            locationInput.addEventListener('input', () => {
                const trimmed = locationInput.value.trim();
                if (!trimmed) {
                    locationManuallySet = false;
                    delete locationInput.dataset.autofill;
                    return;
                }

                locationManuallySet = true;
                locationInput.dataset.autofill = 'manual';
            });
        }

        function resetSelect($select, placeholder) {
            $select.empty().append(`<option value="">${placeholder}</option>`).prop('disabled', true);
        }

        function populateSelect($select, items, selectedId, placeholder) {
            resetSelect($select, placeholder);
            if (!items.length) {
                return;
            }

            items.forEach(({ id, name }) => {
                const option = $('<option>').val(id).text(name);
                if (Number(selectedId) === Number(id)) {
                    option.attr('selected', 'selected');
                }
                $select.append(option);
            });
            $select.prop('disabled', false);
        }

        function fetchStates(countryId, selectedStateId) {
            if (!countryId) {
                resetSelect(stateSelect, 'Select a state');
                resetSelect(citySelect, 'Select a city');
                updateLocationFromDropdown();
                return $.Deferred().resolve();
            }

            resetSelect(stateSelect, 'Loading states...');
            resetSelect(citySelect, 'Select a city');
            updateLocationFromDropdown();

            return $.get(statesUrl, { country_id: countryId })
                .done(({ data }) => {
                    populateSelect(stateSelect, data, selectedStateId, 'Select a state');
                })
                .fail(() => {
                    resetSelect(stateSelect, 'Unable to load states');
                });
        }

        function fetchCities(stateId, selectedCityId) {
            if (!stateId) {
                resetSelect(citySelect, 'Select a city');
                updateLocationFromDropdown();
                return $.Deferred().resolve();
            }

            resetSelect(citySelect, 'Loading cities...');
            updateLocationFromDropdown();

            return $.get(citiesUrl, { state_id: stateId })
                .done(({ data }) => {
                    populateSelect(citySelect, data, selectedCityId, 'Select a city');
                    updateLocationFromDropdown();
                })
                .fail(() => {
                    resetSelect(citySelect, 'Unable to load cities');
                });
        }

        countrySelect.on('change', function () {
            const selectedCountry = $(this).val();
            fetchStates(selectedCountry).then(() => fetchCities(null)).then(() => {
                updateLocationFromDropdown({ force: true });
            });
        });

        stateSelect.on('change', function () {
            const selectedState = $(this).val();
            fetchCities(selectedState).then(() => updateLocationFromDropdown({ force: true }));
        });

        citySelect.on('change', function () {
            updateLocationFromDropdown({ force: true });
        });

        const hasPrefilledStates = stateSelect.children('option').length > 1;
        const hasPrefilledCities = citySelect.children('option').length > 1;

        if (initialCountryId && !hasPrefilledStates) {
            fetchStates(initialCountryId, initialStateId)
                .then(() => {
                    if (initialStateId) {
                        return fetchCities(initialStateId, initialCityId);
                    }
                    return $.Deferred().resolve();
                })
                .then(() => updateLocationFromDropdown());
        } else if (initialStateId && !hasPrefilledCities) {
            fetchCities(initialStateId, initialCityId).then(() => updateLocationFromDropdown());
        } else {
            updateLocationFromDropdown();
        }

        function findOptionByNames($select, names, options = {}) {
            if (!Array.isArray(names)) {
                names = [names];
            }

            const { triggerChange = false } = options;

            for (const rawName of names) {
                if (!rawName) {
                    continue;
                }

                const normalized = rawName.trim().toLowerCase();
                let matchedOption = null;

                $select.find('option').each(function () {
                    const $option = $(this);
                    if (!$option.val()) {
                        return;
                    }

                    const optionText = $option.text().trim().toLowerCase();
                    if (optionText === normalized) {
                        matchedOption = $option;
                        return false;
                    }

                    if (!matchedOption && optionText.includes(normalized)) {
                        matchedOption = $option;
                    }
                });

                if (matchedOption) {
                    $select.val(matchedOption.val());
                    if (triggerChange) {
                        $select.trigger('change');
                    }
                    return matchedOption.val();
                }
            }

            return null;
        }

        function extractComponent(components, desiredTypes) {
            if (!Array.isArray(desiredTypes)) {
                desiredTypes = [desiredTypes];
            }

            return components.find(component => desiredTypes.some(type => component.types.includes(type))) || null;
        }

        function applyPlaceDetails(place) {
            if (!place) {
                return;
            }

            if (place.formatted_address && locationInput) {
                locationInput.value = place.formatted_address;
                locationInput.dataset.autofill = 'places';
                locationManuallySet = true;
            }

            const components = place.address_components || [];
            if (!components.length) {
                return;
            }

            const countryComponent = extractComponent(components, 'country');
            const stateComponent = extractComponent(components, 'administrative_area_level_1');
            const cityComponent = extractComponent(components, ['locality', 'postal_town', 'administrative_area_level_2']);

            if (!countryComponent) {
                return;
            }

            const countryId = findOptionByNames(countrySelect, [countryComponent.long_name, countryComponent.short_name]);
            if (!countryId) {
                return;
            }

            fetchStates(countryId).then(() => {
                let stateId = null;
                if (stateComponent) {
                    stateId = findOptionByNames(stateSelect, [stateComponent.long_name, stateComponent.short_name]);
                }

                if (stateId) {
                    fetchCities(stateId).then(() => {
                        if (cityComponent) {
                            findOptionByNames(citySelect, [cityComponent.long_name, cityComponent.short_name]);
                        }
                    });
                }
            });
        }

        window.initVenueLocationAutocomplete = function () {
            if (!locationInput || typeof window.google === 'undefined' || !google.maps || !google.maps.places) {
                return;
            }

            const autocomplete = new google.maps.places.Autocomplete(locationInput, {
                fields: ['address_components', 'formatted_address', 'name'],
                types: ['geocode']
            });

            autocomplete.addListener('place_changed', function () {
                const place = autocomplete.getPlace();
                if (!place) {
                    return;
                }

                applyPlaceDetails(place);
            });
        };

        if (window.google && google.maps && google.maps.places) {
            window.initVenueLocationAutocomplete();
        }
    })(jQuery);
</script>
@if (config('services.google.places_key'))
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places_key') }}&libraries=places&callback=initVenueLocationAutocomplete" async defer></script>
@endif
@endpush
