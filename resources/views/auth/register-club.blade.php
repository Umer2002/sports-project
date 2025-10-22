@extends('layouts.default')

@section('content')
    <div class="container">
        <div class="register-box">
            <h3 class="text-center text-heading mb-4">Club Registration</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.club.store') }}">
                @csrf
                <input type="hidden" name="user_type" value="club">

                @if(request('ref'))
                    <input type="hidden" name="ref" value="{{ request('ref') }}">
                @endif
                @if(request('ref_type'))
                    <input type="hidden" name="ref_type" value="{{ request('ref_type') }}">
                @endif
                @if(!empty($inviteToken))
                    <input type="hidden" name="invite_token" value="{{ $inviteToken }}">
                    <div class="alert alert-info">
                        <i class="fas fa-ticket-alt me-2"></i>
                        You're accepting a tournament invitation. After sign up we'll guide you through the club setup and tournament fee.
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Club Name *</label>
                    <input type="text" class="form-control @error('club_name') is-invalid @enderror" name="club_name" value="{{ old('club_name') }}" placeholder="Official Club Name" required>
                    @error('club_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Sport *</label>
                    <select class="form-control txt-select @error('sport') is-invalid @enderror" name="sport" required>
                        <option value="">Select Sport</option>
                        @foreach ($sports as $sport)
                            <option value="{{ $sport->id }}" {{ (string) old('sport') === (string) $sport->id ? 'selected' : '' }}>{{ $sport->name }}</option>
                        @endforeach
                    </select>
                    @error('sport')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Contact First Name *</label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Contact Last Name *</label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contact Email *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Country *</label>
                        <select class="form-control @error('club_country_id') is-invalid @enderror" name="club_country_id" id="clubCountrySelect" data-placeholder="Select Country" required>
                            <option value="" disabled {{ old('club_country_id') ? '' : 'selected' }}>Select Country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" {{ (string) old('club_country_id') === (string) $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('club_country_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">State / Province *</label>
                        <select class="form-control @error('club_state_id') is-invalid @enderror" name="club_state_id" id="clubStateSelect" data-placeholder="Select State" required {{ $states->isEmpty() ? 'disabled' : '' }}>
                            <option value="" disabled {{ old('club_state_id') ? '' : 'selected' }}>Select State</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}" {{ (string) old('club_state_id') === (string) $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                            @endforeach
                        </select>
                        @error('club_state_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">City *</label>
                        <select class="form-control @error('club_city_id') is-invalid @enderror" name="club_city_id" id="clubCitySelect" data-placeholder="Select City" required {{ $cities->isEmpty() ? 'disabled' : '' }}>
                            <option value="" disabled {{ old('club_city_id') ? '' : 'selected' }}>Select City</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" {{ (string) old('club_city_id') === (string) $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @error('club_city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Password *</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="********" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="********" required>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="clubTerms" required>
                    <label class="form-check-label" for="clubTerms">
                        By signing up you agree to our Terms of Services and Privacy Policy.
                    </label>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-signup w-100">Create Club Account</button>
                </div>

                <div class="text-center mt-3">
                    Already have an account? <a href="{{ route('login') }}" class="text-warning fw-bold">Login</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const countrySelect = document.getElementById('clubCountrySelect');
            const stateSelect = document.getElementById('clubStateSelect');
            const citySelect = document.getElementById('clubCitySelect');
            const statesEndpoint = @json(route('register.states-for-country', [], false));
            const citiesEndpoint = @json(route('register.cities-for-state', [], false));
            const oldStateId = @json(old('club_state_id'));
            const oldCityId = @json(old('club_city_id'));

            function resetSelect(select, placeholder) {
                if (!select) {
                    return;
                }
                select.innerHTML = '';
                const placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = placeholder;
                placeholderOption.disabled = true;
                placeholderOption.selected = true;
                select.appendChild(placeholderOption);
                select.value = '';
                select.setAttribute('disabled', 'disabled');
            }

            function setSelectOptions(select, items, selectedValue, placeholder) {
                if (!select) {
                    return;
                }
                select.innerHTML = '';
                const placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = placeholder;
                placeholderOption.disabled = true;
                placeholderOption.selected = !selectedValue;
                select.appendChild(placeholderOption);

                const targetValue = selectedValue ? String(selectedValue) : '';
                (items || []).forEach(function (item) {
                    const option = document.createElement('option');
                    option.value = String(item.id);
                    option.textContent = item.name;
                    if (targetValue && String(item.id) === targetValue) {
                        option.selected = true;
                        placeholderOption.selected = false;
                    }
                    select.appendChild(option);
                });

                if ((items || []).length > 0) {
                    select.removeAttribute('disabled');
                    if (targetValue) {
                        select.value = targetValue;
                    }
                } else {
                    select.setAttribute('disabled', 'disabled');
                    select.value = '';
                }
            }

            function loadStates(countryId, selectedStateId, selectedCityId) {
                if (!stateSelect) {
                    return Promise.resolve([]);
                }
                const statePlaceholder = stateSelect.dataset.placeholder || 'Select State';
                const cityPlaceholder = citySelect ? (citySelect.dataset.placeholder || 'Select City') : 'Select City';

                if (!countryId) {
                    resetSelect(stateSelect, statePlaceholder);
                    resetSelect(citySelect, cityPlaceholder);
                    return Promise.resolve([]);
                }

                resetSelect(stateSelect, statePlaceholder);
                resetSelect(citySelect, cityPlaceholder);

                return fetch(statesEndpoint + '?country_id=' + encodeURIComponent(countryId), {
                    headers: {
                        'Accept': 'application/json'
                    },
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Failed to load states');
                        }
                        return response.json();
                    })
                    .then(function (payload) {
                        const items = Array.isArray(payload?.data) ? payload.data : [];
                        setSelectOptions(stateSelect, items, selectedStateId, statePlaceholder);
                        if (selectedStateId) {
                            return loadCities(selectedStateId, selectedCityId);
                        }
                        return items;
                    })
                    .catch(function () {
                        resetSelect(stateSelect, statePlaceholder);
                        resetSelect(citySelect, cityPlaceholder);
                        return [];
                    });
            }

            function loadCities(stateId, selectedCityId) {
                if (!citySelect) {
                    return Promise.resolve([]);
                }
                const cityPlaceholder = citySelect.dataset.placeholder || 'Select City';

                if (!stateId) {
                    resetSelect(citySelect, cityPlaceholder);
                    return Promise.resolve([]);
                }

                resetSelect(citySelect, cityPlaceholder);

                return fetch(citiesEndpoint + '?state_id=' + encodeURIComponent(stateId), {
                    headers: {
                        'Accept': 'application/json'
                    },
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Failed to load cities');
                        }
                        return response.json();
                    })
                    .then(function (payload) {
                        const items = Array.isArray(payload?.data) ? payload.data : [];
                        setSelectOptions(citySelect, items, selectedCityId, cityPlaceholder);
                        return items;
                    })
                    .catch(function () {
                        resetSelect(citySelect, cityPlaceholder);
                        return [];
                    });
            }

            countrySelect && countrySelect.addEventListener('change', function () {
                const countryId = countrySelect.value || '';
                loadStates(countryId, null, null);
            });

            stateSelect && stateSelect.addEventListener('change', function () {
                const stateId = stateSelect.value || '';
                loadCities(stateId, null);
            });

            const hasInitialStateOptions = stateSelect && stateSelect.options && stateSelect.options.length > 1;
            const hasInitialCityOptions = citySelect && citySelect.options && citySelect.options.length > 1;
            const initialCountry = countrySelect ? (countrySelect.value || '') : '';

            if (hasInitialStateOptions && stateSelect) {
                stateSelect.removeAttribute('disabled');
            }

            if (hasInitialCityOptions && citySelect) {
                citySelect.removeAttribute('disabled');
            }

            if (initialCountry) {
                if (!hasInitialStateOptions) {
                    loadStates(initialCountry, oldStateId, oldCityId);
                } else if (oldStateId && !hasInitialCityOptions) {
                    loadCities(oldStateId, oldCityId);
                }
            } else {
                if (stateSelect) {
                    stateSelect.setAttribute('disabled', 'disabled');
                    stateSelect.value = '';
                }
                if (citySelect) {
                    citySelect.setAttribute('disabled', 'disabled');
                    citySelect.value = '';
                }
            }
        });
    </script>
@endsection
