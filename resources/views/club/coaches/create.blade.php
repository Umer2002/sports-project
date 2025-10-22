@extends('layouts.club-dashboard')
@section('title', 'Coaches')
@section('page_title', 'Coaches')
@section('content')
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">{{ isset($coach) ? 'Edit Coach' : 'Create Coach' }}</h4>
                    <a href="{{ route('club.coaches.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Coaches
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST"
                        action="{{ isset($coach) ? route('club.coaches.update', $coach) : route('club.coaches.store') }}"
                        enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @if (isset($coach))
                            @method('PUT')
                        @endif

                        {{-- Row 1: Name --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input id="first_name" type="text" name="first_name" class="form-control"
                                    value="{{ old('first_name', $coach->first_name ?? '') }}" placeholder="First Name"
                                    required>
                                <div class="invalid-feedback">First name is required.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input id="last_name" type="text" name="last_name" class="form-control"
                                    value="{{ old('last_name', $coach->last_name ?? '') }}" placeholder="Last Name"
                                    required>
                                <div class="invalid-feedback">Last name is required.</div>
                            </div>
                        </div>

                        {{-- Row 2: Contact --}}
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" name="email" class="form-control"
                                    value="{{ old('email', $coach->email ?? '') }}" placeholder="name@example.com" required>
                                <div class="invalid-feedback">Valid email is required.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input id="phone" type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $coach->phone ?? '') }}" placeholder="+92 3xx xxxxxxx" required>
                                <div class="invalid-feedback">Phone is required.</div>
                            </div>
                        </div>
                        {{-- Row 5: Sport --}}
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="sport_id" class="form-label">Sport</label>
                                <select id="sport_id" name="sport_id" class="form-select" required>
                                    <option value="">Select Sport</option>
                                    @foreach ($sports as $id => $name)
                                        <option value="{{ $id }}"
                                            {{ old('sport_id', $coach->sport_id ?? '') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please choose a sport.</div>
                            </div>
                        </div>
                        {{-- Row 3: Gender & Age --}}
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="gender" class="form-label">Gender</label>
                                <select id="gender" name="gender" class="form-select" required>
                                    <option value="">Select Gender</option>
                                    <option value="male"
                                        {{ old('gender', $coach->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female"
                                        {{ old('gender', $coach->gender ?? '') == 'female' ? 'selected' : '' }}>Female
                                    </option>
                                    <option value="other"
                                        {{ old('gender', $coach->gender ?? '') == 'other' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                                <div class="invalid-feedback">Please choose a gender.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="age" class="form-label">Age</label>
                                <input id="age" type="number" name="age" class="form-control"
                                    value="{{ old('age', $coach->age ?? '') }}" min="18" max="100"
                                    placeholder="e.g., 28">
                            </div>
                        </div>

                        {{-- Row 4: Country / State / City --}}
                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label for="country_id" class="form-label">Country</label>
                                <select id="country_id" name="country_id" class="form-select" required
                                    data-initial="{{ old('country_id', $coach->country_id ?? '') }}"
                                    onchange="onCountryChange()">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Country is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="state_id" class="form-label">State / Province</label>
                                <select id="state_id" name="state_id" class="form-select" required
                                    data-initial="{{ old('state_id', $coach->state_id ?? '') }}"
                                    onchange="onStateChange()">
                                    <option value="">Select State / Province</option>
                                </select>
                                <div class="invalid-feedback">State/Province is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="city_id" class="form-label">City</label>
                                <select id="city_id" name="city_id" class="form-select" required
                                    data-initial="{{ old('city_id', $coach->city_id ?? '') }}">
                                    <option value="">Select City</option>
                                </select>
                                <div class="invalid-feedback">City is required.</div>
                            </div>
                        </div>



                        {{-- Row 6: Photo --}}
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label for="photo" class="form-label">Photo</label>
                                <input id="photo" type="file" name="photo" class="form-control"
                                    accept="image/*">
                                @if (isset($coach) && $coach->photo)
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset('storage/' . $coach->photo) }}" class="rounded border"
                                            width="80" height="80" alt="Coach Photo">
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Row 7: Bio --}}
                        <div class="row g-3 mt-1">
                            <div class="col-12">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea id="bio" name="bio" class="form-control" rows="4"
                                    placeholder="Tell us about the coach...">{{ old('bio', $coach->bio ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('club.coaches.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> {{ isset($coach) ? 'Update' : 'Create' }} Coach
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 1rem;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border-radius: .75rem;
        }
    </style>
@endpush


<script>
    (function() {
        'use strict';
        // Bootstrap client-side validation
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>


@push('scripts')
<script>
  const routes = {
    states: @json(route('locations.states')),
    cities: @json(route('locations.cities')),
  };

  function optionMsg(id, msg) {
    const el = document.getElementById(id);
    if (!el) return;
    el.innerHTML = `<option value="">${msg}</option>`;
    el.disabled = false;
  }
  function setLoading(id, loading, ph) {
    const el = document.getElementById(id);
    if (!el) return;
    el.disabled = !!loading;
    if (loading) el.innerHTML = `<option value="">${ph || 'Loading...'}</option>`;
  }
  function fillOptions(id, items, placeholder) {
    const el = document.getElementById(id);
    if (!el) return;

    // normalize payload
    try { if (typeof items === 'string') items = JSON.parse(items); } catch (_) {}
    let arr = Array.isArray(items) ? items : (items?.data ?? items?.items ?? items?.states ?? items?.cities ?? []);
    if (!Array.isArray(arr)) arr = [];

    el.innerHTML = '';
    const ph = document.createElement('option');
    ph.value = '';
    ph.textContent = placeholder || 'Select';
    el.appendChild(ph);

    arr.forEach(it => {
      const opt = document.createElement('option');
      opt.value = it.id;
      opt.textContent = it.name;
      el.appendChild(opt);
    });
    el.disabled = false;
  }
  async function fetchJSON(url) {
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    // If backend redirected to login (302) or returned HTML, show status
    const ct = res.headers.get('content-type') || '';
    if (!res.ok || !ct.includes('application/json')) {
      const text = await res.text().catch(()=>'');
      throw new Error(`Fetch ${res.status}: ${url}\n${text.slice(0,200)}`);
    }
    return res.json();
  }

  // GLOBAL handlers so inline onchange can find them
  window.onCountryChange = async function () {
    const $country = document.getElementById('country_id');
    const countryId = $country?.value || '';

    fillOptions('state_id', [], 'Select State / Province');
    fillOptions('city_id',  [], 'Select City');
    if (!countryId) return;

    const url = `${routes.states}?country=${encodeURIComponent(countryId)}`;
    setLoading('state_id', true, 'Loading states...');
    try {
      console.debug('[states] GET', url);
      const data = await fetchJSON(url);
      fillOptions('state_id', data, 'Select State / Province');
    } catch (e) {
      console.error('[states] error', e);
      optionMsg('state_id', 'Failed to load states');
    }
  };

  window.onStateChange = async function () {
    const $state = document.getElementById('state_id');
    const stateId = $state?.value || '';

    fillOptions('city_id', [], 'Select City');
    if (!stateId) return;

    const url = `${routes.cities}?state=${encodeURIComponent(stateId)}`;
    setLoading('city_id', true, 'Loading cities...');
    try {
      console.debug('[cities] GET', url);
      const data = await fetchJSON(url);
      fillOptions('city_id', data, 'Select City');
    } catch (e) {
      console.error('[cities] error', e);
      optionMsg('city_id', 'Failed to load cities');
    }
  };

  // EDIT rehydrate
  document.addEventListener('DOMContentLoaded', async () => {
    const $country = document.getElementById('country_id');
    const $state   = document.getElementById('state_id');
    const $city    = document.getElementById('city_id');

    if (!$country || !$state || !$city) return;

    const initCountry = $country.dataset.initial || '';
    const initState   = $state.dataset.initial   || '';
    const initCity    = $city.dataset.initial    || '';

    if (!initCountry) return; // create mode

    // 1) set country & load states
    $country.value = initCountry;
    await window.onCountryChange();

    // 2) set state & load cities
    if (initState) {
      $state.value = initState;
      await window.onStateChange();

      // 3) set city
      if (initCity) $city.value = initCity;
    }
  });
</script>
@endpush


