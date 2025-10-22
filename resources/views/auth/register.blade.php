<!-- resources/views/auth/register.blade.php -->
@extends('layouts.default')

@php
    $clubsForScript = $clubs->map(function ($club) {
        return [
            'id' => $club->id,
            'name' => $club->name,
            'sport_id' => $club->sport_id,
        ];
    });

    $oldGuardianRequired = false;
    $oldDob = old('dob');
    if ($oldDob && old('user_type') === 'player') {
        try {
            $oldGuardianRequired = \Carbon\Carbon::parse($oldDob)->age < 13;
        } catch (\Throwable $e) {
            $oldGuardianRequired = false;
        }
    }

    $referralCode = old('ref', request('ref'));
    $inviterName  = old('inviter_name', request('inviter'));
@endphp

@section('title', 'Play2Earn Registration')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        .pac-container {
            z-index: 2000 !important;
            color: #000;
        }


        /* #clubDropdown.select2-hidden-accessible {
                    position: absolute !important;
                    width: 1px !important;
                    height: 1px !important;
                    padding: 0 !important;
                    margin: 0 !important;
                    overflow: hidden !important;
                    clip: rect(0 0 0 0) !important;
                    white-space: nowrap !important;
                    border: 0 !important;
                } */



        /* .input-icon .select2-container--default .select2-selection--single .select2-selection__rendered {
                    padding-left: 0;
                    color: #fff;
                    width: 100%;
                }

                .input-icon .select2-container--default .select2-selection--single .select2-selection__rendered.select2-selection__placeholder {
                    display: none !important;
                }

                .input-icon .select2-container--default .select2-selection--single .select2-selection__placeholder {
                    display: none !important;
                }
                .select2-selection__placeholder{
                    display: none;
                }
                .input-icon .select2-container--default .select2-selection--single .select2-selection__arrow {
                    right: 24px;
                    top: 50%;
                    transform: translateY(-50%);
                } */
    </style>
@endsection

@section('content')
    <div class="register-hero py-5 w-100 position-relative overflow-hidden">
        <span class="floating-square square-1"></span>
        <span class="floating-square square-2"></span>
        <span class="floating-square square-3"></span>

        <div class="container position-relative">
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-5">
                    <div class="register-intro text-white h-100 d-flex flex-column justify-content-between">
                        <div>
                            <span class="intro-badge text-uppercase">Join the movement</span>
                            <h1 class="intro-heading display-5 fw-bold mt-3 mb-3">Level up your sports journey</h1>
                            <p class="intro-copy text-white-50 mb-4">Create a Play2Earn account to unlock training plans,
                                connect with clubs, and showcase your highlights across the community.</p>
                        </div>
                        <div class="row g-3 register-highlights">
                            <div class="col-sm-6">
                                <div class="highlight-card">
                                    <span class="highlight-icon">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M4 22H20"></path>
                                            <path d="M4 14H20"></path>
                                            <path d="M9 18H15"></path>
                                            <path d="M10 6L12 2L14 6"></path>
                                            <path d="M12 2V14"></path>
                                        </svg>
                                    </span>
                                    <h6 class="mb-1">Showcase Potential</h6>
                                    <p class="small mb-0 text-white-50">Let recruiters discover your skills with a polished
                                        profile.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="highlight-card">
                                    <span class="highlight-icon">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <circle cx="18" cy="5" r="3"></circle>
                                            <circle cx="6" cy="8" r="3"></circle>
                                            <circle cx="12" cy="19" r="3"></circle>
                                            <path d="M8.5 10.5L9.5 12.5"></path>
                                            <path d="M15.5 7.5L9.5 12.5"></path>
                                            <path d="M14.5 16.5L9.5 12.5"></path>
                                        </svg>
                                    </span>
                                    <h6 class="mb-1">Grow Your Network</h6>
                                    <p class="small mb-0 text-white-50">Connect instantly with clubs, teammates, and scouts.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="highlight-card">
                                    <span class="highlight-icon">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M4 11V4H11"></path>
                                            <path d="M20 13V20H13"></path>
                                            <path d="M20 4L13.5 10.5"></path>
                                            <path d="M4 20L10.5 13.5"></path>
                                        </svg>
                                    </span>
                                    <h6 class="mb-1">Unlock Rewards</h6>
                                    <p class="small mb-0 text-white-50">Earn recognition and incentives by staying active on
                                        the platform.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="register-card shadow-lg">
                        <div class="register-card-header text-center mb-4">
                            <h3 class="text-white mb-1">Create your account</h3>
                            <p class="text-white-50 mb-0">Pick your role, fill in your details, and start earning playtime
                                rewards.</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger rounded-4 shadow-sm">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" id="signupForm" class="register-form">
                            @csrf
                            @if(!empty($inviterName))
                                <input type="hidden" name="inviter_name" value="{{ $inviterName }}">
                                <div class="mb-4">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Invite Code</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M10 13a4.5 4.5 0 0 1 0-6.36l1.2-1.2a4.5 4.5 0 0 1 6.36 6.36l-1.1 1.1"></path>
                                                <path d="M14 11a4.5 4.5 0 0 1 0 6.36l-1.2 1.2a4.5 4.5 0 0 1-6.36-6.36l1.1-1.1"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control register-input" name="ref"
                                            value="{{ $referralCode }}" placeholder="Invite code"
                                            @if(!empty($referralCode)) readonly @endif>
                                    </div>
                                    <div class="small text-white-50 mt-2">
                                        You're joining with an invite from <span class="text-white">{{ $inviterName }}</span>.
                                    </div>
                                    @error('ref')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                <input type="hidden" name="ref" value="{{ $referralCode }}">
                            @endif
                            <input type="hidden" name="ref_type" value="{{ old('ref_type', request('ref_type')) }}">

                            <input type="hidden" name="club_selection_mode" id="clubSelectionMode"
                                value="{{ old('club_selection_mode', 'existing') }}">
                            <input type="hidden" name="new_club_name" id="newClubName" value="{{ old('new_club_name') }}">
                            <input type="hidden" name="new_club_address" id="newClubAddress"
                                value="{{ old('new_club_address') }}">
                            <input type="hidden" name="new_club_place_id" id="newClubPlaceId"
                                value="{{ old('new_club_place_id') }}">
                            <input type="hidden" name="new_club_phone" id="newClubPhone"
                                value="{{ old('new_club_phone') }}">
                            <input type="hidden" name="new_club_website" id="newClubWebsite"
                                value="{{ old('new_club_website') }}">
                            <input type="hidden" name="new_club_email" id="newClubEmail"
                                value="{{ old('new_club_email') }}">

                            <div class="mb-4">
                                <label class="form-label text-uppercase small fw-semibold text-white-50">Choose your role
                                    *</label>
                                <div class="register-type-grid">
                                    <div class="type-option">
                                        <input class="btn-check" type="radio" name="user_type" id="type-club"
                                            value="club" {{ old('user_type') === 'club' ? 'checked' : '' }} required>
                                        <label class="type-card" for="type-club">
                                            <span class="type-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M3 21V7L12 3L21 7V21"></path>
                                                    <path d="M9 21V12H15V21"></path>
                                                </svg>
                                            </span>
                                            <span>
                                                <strong class="d-block text-white">Club</strong>
                                                <small class="text-white-50">Build out rosters &amp; programs</small>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="type-option">
                                        <input class="btn-check" type="radio" name="user_type" id="type-player"
                                            value="player" {{ old('user_type') === 'player' ? 'checked' : '' }}>
                                        <label class="type-card" for="type-player">
                                            <span class="type-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <circle cx="12" cy="7" r="3.5"></circle>
                                                    <path d="M5 21C5 16.5 8.5 14 12 14C15.5 14 19 16.5 19 21"></path>
                                                </svg>
                                            </span>
                                            <span>
                                                <strong class="d-block text-white">Player</strong>
                                                <small class="text-white-50">Track stats &amp; highlights</small>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="type-option">
                                        <input class="btn-check" type="radio" name="user_type" id="type-referee"
                                            value="referee" {{ old('user_type') === 'referee' ? 'checked' : '' }}>
                                        <label class="type-card" for="type-referee">
                                            <span class="type-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M5 4H19V9H5Z"></path>
                                                    <path d="M9 4V20"></path>
                                                    <path d="M15 4V20"></path>
                                                </svg>
                                            </span>
                                            <span>
                                                <strong class="d-block text-white">Referee</strong>
                                                <small class="text-white-50">Manage officiating gigs</small>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="type-option">
                                        <input class="btn-check" type="radio" name="user_type" id="type-college"
                                            value="college" {{ old('user_type') === 'college' ? 'checked' : '' }}>
                                        <label class="type-card" for="type-college">
                                            <span class="type-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M2 9L12 4L22 9L12 14L2 9Z"></path>
                                                    <path d="M6 11V16C9 19 15 19 18 16V11"></path>
                                                </svg>
                                            </span>
                                            <span>
                                                <strong class="d-block text-white">College / University</strong>
                                                <small class="text-white-50">Scout student-athletes</small>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="type-option">
                                        <input class="btn-check" type="radio" name="user_type" id="type-coach"
                                            value="coach" {{ old('user_type') === 'coach' ? 'checked' : '' }}>
                                        <label class="type-card" for="type-coach">
                                            <span class="type-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path
                                                        d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21">
                                                    </path>
                                                    <circle cx="9" cy="7" r="4"></circle>
                                                    <path d="M23 21V19C23 18.1138 22.7654 17.2528 22.3259 16.5"></path>
                                                    <path
                                                        d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88">
                                                    </path>
                                                </svg>
                                            </span>
                                            <span>
                                                <strong class="d-block text-white">Coach</strong>
                                                <small class="text-white-50">Manage teams &amp; players</small>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="type-option">
                                        <input class="btn-check" type="radio" name="user_type" id="type-volunteer"
                                            value="volunteer" {{ old('user_type') === 'volunteer' ? 'checked' : '' }}>
                                        <label class="type-card" for="type-volunteer">
                                            <span class="type-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M12 20L7 17L3 12L7 7L12 4L17 7L21 12L17 17L12 20Z"></path>
                                                    <path d="M9.5 12L11.5 14.5L15 9.5"></path>
                                                </svg>
                                            </span>
                                            <span>
                                                <strong class="d-block text-white">Sports Ambassador</strong>
                                                <small class="text-white-50">Lead community outreach</small>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Primary Sport
                                        *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="9"></circle>
                                                <path d="M5.65 5.65L18.35 18.35"></path>
                                                <path d="M5.65 18.35L18.35 5.65"></path>
                                            </svg>
                                        </span>
                                        <select class="form-control register-input" name="sport" id="sportSelect"
                                            required>
                                            <option value="" disabled {{ old('sport') ? '' : 'selected' }}>Select
                                                sport</option>
                                            @foreach ($sports as $sport)
                                                <option value="{{ $sport->id }}"
                                                    {{ (string) old('sport') === (string) $sport->id ? 'selected' : '' }}>
                                                    {{ $sport->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" id="clubSelectWrapper">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Select
                                        Club</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M3 9L12 4L21 9"></path>
                                                <path d="M4 10V20H20V10"></path>
                                                <path d="M9 14H15V20H9Z"></path>
                                            </svg>
                                        </span>
                                        {{-- hide select2 placeholder --}}
                                        <select class="form-control register-input" name="club" id="clubDropdown"
                                            data-old-value="{{ old('club') }}">

                                        </select>
                                    </div>
                                    @error('club')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('new_club_name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('new_club_email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 d-none" id="clubInputWrapper">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Club
                                        Name</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M4 21V9L12 4L20 9V21"></path>
                                                <path d="M9 21V13H15V21"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control register-input" name="club_name"
                                            value="{{ old('club_name') }}" placeholder="Enter club name">
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-0 {{ old('user_type') === 'club' ? '' : 'd-none' }}"
                                id="clubLocationWrapper">
                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Country
                                        *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path
                                                    d="M3 12C3 7.029 7.029 3 12 3C16.971 3 21 7.029 21 12C21 16.971 16.971 21 12 21C7.029 21 3 16.971 3 12Z">
                                                </path>
                                                <path d="M2 12H22"></path>
                                                <path d="M12 2C14.5 5.5 15.75 8.75 15.75 12C15.75 15.25 14.5 18.5 12 22">
                                                </path>
                                                <path d="M12 2C9.5 5.5 8.25 8.75 8.25 12C8.25 15.25 9.5 18.5 12 22"></path>
                                            </svg>
                                        </span>
                                        <select class="form-control register-input" name="club_country_id"
                                            id="clubCountrySelect" data-placeholder="Select country"
                                            {{ old('user_type') === 'club' ? '' : 'disabled' }}>
                                            <option value="" disabled
                                                {{ old('club_country_id') ? '' : 'selected' }}>Select country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ (string) old('club_country_id') === (string) $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('club_country_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">State /
                                        Province *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M3 11L12 4L21 11"></path>
                                                <path d="M9 22V12H15V22"></path>
                                            </svg>
                                        </span>
                                        <select class="form-control register-input" name="club_state_id"
                                            id="clubStateSelect" data-placeholder="Select state"
                                            {{ old('user_type') === 'club' ? '' : 'disabled' }}>
                                            <option value="" disabled {{ old('club_state_id') ? '' : 'selected' }}>
                                                Select state</option>
                                        </select>
                                    </div>
                                    @error('club_state_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">City *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path
                                                    d="M12 2C8.134 2 5 5.134 5 9C5 14.25 12 22 12 22C12 22 19 14.25 19 9C19 5.134 15.866 2 12 2Z">
                                                </path>
                                                <circle cx="12" cy="9" r="2.5"></circle>
                                            </svg>
                                        </span>
                                        <select class="form-control register-input" name="club_city_id"
                                            id="clubCitySelect" data-placeholder="Select city"
                                            {{ old('user_type') === 'club' ? '' : 'disabled' }}>
                                            <option value=" {{ old('club_city_id') ? '' : 'selected' }}" disabled>
                                                Select city</option>
                                        </select>
                                    </div>
                                    @error('club_city_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div id="newClubSummary"
                                class="club-summary-card {{ old('club_selection_mode') === 'new' ? '' : 'd-none' }}">
                                <div class="club-summary-heading">Pending club addition</div>
                                <p class="club-summary-text">We'll add this club to your account and notify their staff so
                                    they
                                    can manage their roster on Play2Earn.</p>
                                <ul class="club-summary-list">
                                    <li><strong>Name:</strong> <span
                                            id="newClubSummaryName">{{ old('new_club_name') }}</span></li>
                                    <li><strong>Address:</strong> <span
                                            id="newClubSummaryAddress">{{ old('new_club_address') }}</span></li>
                                    <li><strong>Email:</strong> <span
                                            id="newClubSummaryEmail">{{ old('new_club_email') }}</span></li>
                                    <li><strong>Phone:</strong> <span
                                            id="newClubSummaryPhone">{{ old('new_club_phone') }}</span></li>
                                    <li><strong>Website:</strong> <span
                                            id="newClubSummaryWebsite">{{ old('new_club_website') }}</span></li>
                                </ul>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">First Name
                                        *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <circle cx="12" cy="8" r="4"></circle>
                                                <path d="M4 21C4 16 8 14 12 14C16 14 20 16 20 21"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control register-input" name="first_name"
                                            value="{{ old('first_name') }}" placeholder="Type first name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Last Name
                                        *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <circle cx="12" cy="8" r="4"></circle>
                                                <path d="M4 21C4 16 8 14 12 14C16 14 20 16 20 21"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control register-input" name="last_name"
                                            value="{{ old('last_name') }}" placeholder="Type last name" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Email Address
                                        *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M4 4H20V20H4Z"></path>
                                                <path d="M4 7L12 13L20 7"></path>
                                            </svg>
                                        </span>
                                        <input type="email" class="form-control register-input" name="email"
                                            value="{{ old('email') }}" placeholder="Email here..." required>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 {{ old('user_type') === 'player' ? '' : 'd-none' }}"
                                    id="dobWrapper">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Date of Birth
                                        *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                                <path d="M8 2V6"></path>
                                                <path d="M16 2V6"></path>
                                                <path d="M3 10H21"></path>
                                            </svg>
                                        </span>
                                        <input type="date" class="form-control register-input" name="dob"
                                            value="{{ old('dob') }}" placeholder="YYYY-MM-DD">
                                    </div>
                                    <small class="text-white-50 d-block mt-2">Players under 13 must provide guardian
                                        information.</small>
                                </div>
                                <div class="col-12 {{ $oldGuardianRequired ? '' : 'd-none' }}" id="guardianWrapper">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label
                                                class="form-label text-uppercase small fw-semibold text-white-50">Guardian
                                                First Name *</label>
                                            <div class="input-icon">
                                                <span class="input-icon-badge">
                                                    <svg width="22" height="22" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="1.6"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="7" r="3.5"></circle>
                                                        <path d="M5 21C5 16.5 8.5 14 12 14C15.5 14 19 16.5 19 21"></path>
                                                    </svg>
                                                </span>
                                                <input type="text" class="form-control register-input guardian-input"
                                                    name="guardian_first_name" value="{{ old('guardian_first_name') }}"
                                                    placeholder="Guardian first name"
                                                    {{ $oldGuardianRequired ? 'required' : '' }}>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label
                                                class="form-label text-uppercase small fw-semibold text-white-50">Guardian
                                                Last Name *</label>
                                            <div class="input-icon">
                                                <span class="input-icon-badge">
                                                    <svg width="22" height="22" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="1.6"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="7" r="3.5"></circle>
                                                        <path d="M5 21C5 16.5 8.5 14 12 14C15.5 14 19 16.5 19 21"></path>
                                                    </svg>
                                                </span>
                                                <input type="text" class="form-control register-input guardian-input"
                                                    name="guardian_last_name" value="{{ old('guardian_last_name') }}"
                                                    placeholder="Guardian last name"
                                                    {{ $oldGuardianRequired ? 'required' : '' }}>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label
                                                class="form-label text-uppercase small fw-semibold text-white-50">Guardian
                                                Email *</label>
                                            <div class="input-icon">
                                                <span class="input-icon-badge">
                                                    <svg width="22" height="22" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="1.6"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M4 4H20V20H4Z"></path>
                                                        <path d="M4 7L12 13L20 7"></path>
                                                    </svg>
                                                </span>
                                                <input type="email" class="form-control register-input guardian-input"
                                                    name="guardian_email" value="{{ old('guardian_email') }}"
                                                    placeholder="Guardian email"
                                                    {{ $oldGuardianRequired ? 'required' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="collegeWrapper">
                                    <label
                                        class="form-label text-uppercase small fw-semibold text-white-50">College</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M2 9L12 4L22 9L12 14L2 9Z"></path>
                                                <path d="M6 11V16C9 19 15 19 18 16V11"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control register-input" name="college"
                                            value="{{ old('college') }}" placeholder="College name">
                                    </div>
                                </div>
                                <div class="col-md-6" id="universityWrapper">
                                    <label
                                        class="form-label text-uppercase small fw-semibold text-white-50">University</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M2 9L12 4L22 9L12 14L2 9Z"></path>
                                                <path d="M6 11V16C9 19 15 19 18 16V11"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control register-input" name="university"
                                            value="{{ old('university') }}" placeholder="University name">
                                    </div>
                                </div>
                                <div class="col-md-12" id="affiliationWrapper">
                                    <label
                                        class="form-label text-uppercase small fw-semibold text-white-50">Affiliation</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M5 12H19"></path>
                                                <path d="M12 5L12 19"></path>
                                                <circle cx="12" cy="12" r="9"></circle>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control register-input"
                                            name="referee_affiliation" value="{{ old('referee_affiliation') }}"
                                            placeholder="Association / league">
                                    </div>
                                </div>

                                <!-- Coach-specific fields -->
                                <div class="col-md-6 {{ old('user_type') === 'coach' ? '' : 'd-none' }}"
                                    id="coachGenderWrapper">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Gender
                                        *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <circle cx="12" cy="8" r="4"></circle>
                                                <path d="M4 21C4 16 8 14 12 14C16 14 20 16 20 21"></path>
                                            </svg>
                                        </span>
                                        <select class="form-control register-input coach-input" name="gender">
                                            <option value="">Select gender</option>
                                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>
                                                Female</option>
                                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 {{ old('user_type') === 'coach' ? '' : 'd-none' }}"
                                    id="coachPhoneWrapper">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Phone
                                        Number</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path
                                                    d="M22 16.92V19.92C22 20.4696 21.7893 20.9989 21.4142 21.391C21.0391 21.7831 20.5304 22 20 22C15.0605 21.4479 10.4396 19.1925 6.85 15.68C3.61542 12.5164 1.42246 8.44163 1.01 3.99998C0.949995 3.46942 1.11948 2.93973 1.47876 2.54629C1.83804 2.15286 2.3513 1.92969 2.89 1.92969H5.89C6.65609 1.92303 7.3877 2.25591 7.88 2.83969C8.37231 3.42348 8.57587 4.19344 8.44 4.94969L8.44 4.94969C7.93 7.82969 8.48 10.8197 9.93 13.3997">
                                                </path>
                                                <path
                                                    d="M18.01 13.38C18.8117 14.0992 19.4704 14.9589 19.9511 15.9099C20.4318 16.8609 20.7251 17.8862 20.8177 18.9397C20.9103 19.9932 20.8003 21.0549 20.4942 22.0646">
                                                </path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control register-input coach-input"
                                            name="phone" value="{{ old('phone') }}" placeholder="Phone number">
                                    </div>
                                </div>
                                <div class="col-md-6 {{ old('user_type') === 'coach' ? '' : 'd-none' }}"
                                    id="coachCountryWrapper">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Country
                                        *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path
                                                    d="M21 10C21 17 12 23 12 23S3 17 3 10C3 5.58172 6.58172 2 12 2C17.4183 2 21 5.58172 21 10Z">
                                                </path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                        </span>
                                        <select class="form-control register-input coach-input" name="country_id"
                                            id="coachCountrySelect">
                                            <option value="" disabled>Select Country</option>
                                            @foreach (\App\Models\Country::orderBy('name')->get() as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 {{ old('user_type') === 'coach' ? '' : 'd-none' }}"
                                    id="coachCityWrapper">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">City *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path
                                                    d="M21 10C21 17 12 23 12 23S3 17 3 10C3 5.58172 6.58172 2 12 2C17.4183 2 21 5.58172 21 10Z">
                                                </path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                        </span>
                                        <select class="form-control register-input coach-input" name="city_id"
                                            id="coachCitySelect">
                                            <option value="" disabled>Select City</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 {{ old('user_type') === 'coach' ? '' : 'd-none' }}"
                                    id="coachExperienceWrapper">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Years of
                                        Experience</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <path d="M12 6V12L16 14"></path>
                                            </svg>
                                        </span>
                                        <input type="number" class="form-control register-input coach-input"
                                            name="experience_years" value="{{ old('experience_years') }}"
                                            placeholder="Years coaching" min="0">
                                    </div>
                                </div>
                                <div class="col-md-12 {{ old('user_type') === 'coach' ? '' : 'd-none' }}"
                                    id="coachBioWrapper">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Coaching
                                        Bio</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge" style="top: 24px;">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path
                                                    d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z">
                                                </path>
                                                <path d="M14 2V8H20"></path>
                                                <path d="M16 13H8"></path>
                                                <path d="M16 17H8"></path>
                                                <path d="M10 9H8"></path>
                                            </svg>
                                        </span>
                                        <textarea class="form-control register-input coach-input" name="bio" rows="3" style="padding-top: 20px;"
                                            placeholder="Tell us about your coaching experience and qualifications...">{{ old('bio') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Password
                                        *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                                                <path d="M7 11V7C7 4.239 9.239 2 12 2C14.761 2 17 4.239 17 7V11"></path>
                                            </svg>
                                        </span>
                                        <input type="password" class="form-control register-input" name="password"
                                            placeholder="********" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-uppercase small fw-semibold text-white-50">Confirm
                                        Password *</label>
                                    <div class="input-icon">
                                        <span class="input-icon-badge">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.6" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                                                <path d="M7 11V7C7 4.239 9.239 2 12 2C14.761 2 17 4.239 17 7V11"></path>
                                            </svg>
                                        </span>
                                        <input type="password" class="form-control register-input"
                                            name="password_confirmation" placeholder="********" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check register-terms mt-4 mb-4 text-start">
                                <input class="form-check-input" type="checkbox" value="1" id="termsCheck" required>
                                <label class="form-check-label ms-2 text-white-50" for="termsCheck">
                                    By signing up you agree to our <a href="#" class="link-highlight">Terms of
                                        Service</a> and
                                    <a href="#" class="link-highlight">Privacy Policy</a>.
                                </label>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-signup w-100 py-3">Sign Up</button>
                            </div>

                            <div class="text-center mt-4">
                                <span class="text-white-50">Already have an account?</span>
                                <a href="{{ route('login') }}" class="text-warning fw-bold ms-1">Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="clubLookupModal" tabindex="-1" aria-labelledby="clubLookupModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content register-modal text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="clubLookupModalLabel">Find your club</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-white-50">Search for your club using Google Places or fill in the details
                        manually.</p>
                    <div class="mb-3">
                        <label class="form-label text-uppercase small fw-semibold text-white-50">Search</label>
                        <input type="text" class="form-control register-input" id="clubLookupInput"
                            placeholder="Start typing club name">
                        @if (!config('services.google.places_key'))
                            <div class="text-warning small mt-1">Add your Google Places API key to enable autocomplete.
                            </div>
                        @endif
                    </div>
                    <div class="selected-club-preview p-3 rounded-4 mb-3 d-none" id="clubLookupDetails">
                        <div class="fw-bold mb-1" id="clubLookupName"></div>
                        <div class="small text-white-50" id="clubLookupAddress"></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-uppercase small fw-semibold text-white-50">Club name
                                (optional)</label>
                            <input type="text" class="form-control register-input" id="clubNameField"
                                placeholder="Club name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-uppercase small fw-semibold text-white-50">Contact email
                                (optional)</label>
                            <input type="email" class="form-control register-input" id="clubEmailField"
                                placeholder="coach@club.com">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-uppercase small fw-semibold text-white-50">Address</label>
                            <textarea class="form-control register-input" id="clubAddressField" rows="2"
                                placeholder="Street, City, State"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-uppercase small fw-semibold text-white-50">Phone</label>
                            <input type="text" class="form-control register-input" id="clubPhoneField"
                                placeholder="(555) 555-5555">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-uppercase small fw-semibold text-white-50">Website</label>
                            <input type="url" class="form-control register-input" id="clubWebsiteField"
                                placeholder="https://example.com">
                        </div>
                    </div>
                    <input type="hidden" id="clubPlaceIdField">
                </div>
                <div class="modal-footer border-0 d-flex justify-content-between align-items-center">
                    <span class="small text-white-50">We'll store these details so the club can claim their profile.</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-signup px-4" id="confirmClubSelection">Use this
                            club</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .register-hero {
            background: linear-gradient(135deg, rgba(5, 10, 30, 0.85), rgba(0, 45, 75, 0.75));
            backdrop-filter: blur(8px);
            /* border-radius: 32px; */
            /* margin: 48px auto; */
            opacity: 0.75;
        }
        }

        .floating-square {
            position: absolute;
            border: 1px solid rgba(0, 174, 239, 0.35);
            background: rgba(255, 247, 0, 0.08);
            border-radius: 24px;
            z-index: 1;
            filter: blur(0px);
        }

        .floating-square::after {
            content: '';
            position: absolute;
            inset: 10%;
            border: 1px dashed rgba(0, 174, 239, 0.25);
            border-radius: inherit;
        }

        .square-1 {
            width: 180px;
            height: 180px;
            top: -60px;
            right: -40px;
        }

        .square-2 {
            width: 140px;
            height: 140px;
            bottom: -50px;
            left: 5%;
        }

        .square-3 {
            width: 220px;
            height: 220px;
            top: 35%;
            left: -80px;
        }

        .register-intro .intro-badge {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 999px;
            background: rgba(255, 247, 0, 0.12);
            color: #fff700;
            letter-spacing: 0.12em;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .register-intro .intro-heading {
            line-height: 1.1;
        }

        .register-highlights .highlight-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 20px;
            height: 100%;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            box-shadow: 0 18px 40px rgba(0, 20, 40, 0.35);
        }

        .highlight-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, rgba(0, 174, 239, 0.45), rgba(0, 80, 120, 0.85));
            color: #fff;
        }

        .register-card {
            position: relative;
            z-index: 2;
            background: rgba(10, 10, 25, 0.88);
            border-radius: 28px;
            padding: 36px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 25px 60px rgba(0, 18, 40, 0.55);
        }

        .club-summary-card {
            background: rgba(0, 174, 239, 0.08);
            border: 1px solid rgba(0, 174, 239, 0.18);
            border-radius: 18px;
            padding: 22px 24px;
            margin-bottom: 28px;
        }

        .club-summary-heading {
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #fff700;
            font-size: 0.8rem;
            margin-bottom: 6px;
        }

        .club-summary-text {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 12px;
        }

        .club-summary-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 6px;
        }

        .club-summary-list strong {
            color: #00aeef;
            margin-right: 6px;
        }

        .register-card-header h3 {
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .register-type-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }

        .type-option {
            position: relative;
        }

        .type-option .btn-check {
            position: absolute;
            opacity: 0;
        }

        .type-card {
            display: flex;
            gap: 14px;
            align-items: center;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid transparent;
            transition: all 0.25s ease;
            cursor: pointer;
            height: 100%;
        }

        .type-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, rgba(0, 174, 239, 0.9), rgba(0, 85, 130, 0.75));
            color: #fff;
            box-shadow: 0 12px 25px rgba(0, 174, 239, 0.35);
        }

        .type-option .btn-check:checked+.type-card,
        .type-card:hover {
            border-color: rgba(0, 174, 239, 0.75);
            background: rgba(0, 174, 239, 0.18);
            box-shadow: 0 16px 40px rgba(0, 174, 239, 0.35);
        }

        .input-icon {
            position: relative;
        }

        .input-icon-badge {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: rgba(0, 174, 239, 0.18);
            color: #00aeef;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .register-input.form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #fff;
            padding: 14px 18px 14px 74px;
            border-radius: 18px;
            min-height: 56px;
        }

        .register-input.form-control:focus {
            background: rgba(0, 174, 239, 0.12);
            border-color: rgba(0, 174, 239, 0.85);
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(0, 174, 239, 0.2);
        }

        .register-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .register-terms .form-check-input {
            border-radius: 8px;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(0, 174, 239, 0.6);
            background: rgba(255, 255, 255, 0.05);
        }

        .register-terms .form-check-input:checked {
            background-color: #00aeef;
            border-color: #00aeef;
        }

        .register-modal {
            background: rgba(12, 16, 32, 0.96);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 22px 55px rgba(0, 18, 40, 0.55);
        }

        .register-modal .modal-header,
        .register-modal .modal-footer {
            background: transparent;
        }

        .register-modal .register-input {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: #fff;
        }

        .register-modal .register-input::placeholder {
            color: rgba(255, 255, 255, 0.55);
        }

        .register-modal .btn-outline-light {
            border-radius: 50px;
            border-width: 2px;
        }

        /* .selected-club-preview {
                    background: rgba(0, 174, 239, 0.12);
                    border: 1px solid rgba(0, 174, 239, 0.22);
                } */

        .link-highlight {
            color: #fff700;
            text-decoration: none;
        }

        .link-highlight:hover {
            color: #00aeef;
            text-decoration: underline;
        }

        @media (max-width: 991.98px) {
            .register-hero {
                border-radius: 24px;
                margin: 32px auto;
            }

            .floating-square {
                display: none;
            }
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @if (config('services.google.places_key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.places_key') }}&libraries=places">
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userTypeRadios = document.querySelectorAll("input[name='user_type']");
            const clubSelectWrapper = document.getElementById('clubSelectWrapper');
            const clubInputWrapper = document.getElementById('clubInputWrapper');
            const clubDropdown = document.getElementById('clubDropdown');
            const clubNameInput = document.querySelector("input[name='club_name']");
            const sportSelect = document.getElementById('sportSelect');
            const collegeWrapper = document.getElementById('collegeWrapper');
            const universityWrapper = document.getElementById('universityWrapper');
            const affiliationWrapper = document.getElementById('affiliationWrapper');
            const dobWrapper = document.getElementById('dobWrapper');
            const dobInput = document.querySelector("input[name='dob']");
            const guardianWrapper = document.getElementById('guardianWrapper');
            const guardianInputs = guardianWrapper ? Array.from(guardianWrapper.querySelectorAll(
                '.guardian-input')) : [];
            const coachGenderWrapper = document.getElementById('coachGenderWrapper');
            const coachPhoneWrapper = document.getElementById('coachPhoneWrapper');
            const coachCountryWrapper = document.getElementById('coachCountryWrapper');
            const coachCityWrapper = document.getElementById('coachCityWrapper');
            const coachCountrySelect = document.getElementById('coachCountrySelect');
            const coachCitySelect = document.getElementById('coachCitySelect');
            const coachExperienceWrapper = document.getElementById('coachExperienceWrapper');
            const coachBioWrapper = document.getElementById('coachBioWrapper');
            const coachInputs = document.querySelectorAll('.coach-input');
            const clubSelectionModeInput = document.getElementById('clubSelectionMode');
            const newClubNameInput = document.getElementById('newClubName');
            const newClubAddressInput = document.getElementById('newClubAddress');
            const newClubPlaceInput = document.getElementById('newClubPlaceId');
            const newClubPhoneInput = document.getElementById('newClubPhone');
            const newClubWebsiteInput = document.getElementById('newClubWebsite');
            const newClubEmailInput = document.getElementById('newClubEmail');
            const summaryCard = document.querySelector('.club-summary-card');
            const summaryName = document.getElementById('newClubSummaryName');
            const summaryAddress = document.getElementById('newClubSummaryAddress');
            const summaryEmail = document.getElementById('newClubSummaryEmail');
            const summaryPhone = document.getElementById('newClubSummaryPhone');
            const summaryWebsite = document.getElementById('newClubSummaryWebsite');
            const confirmClubSelectionBtn = document.getElementById('confirmClubSelection');
            const clubLookupModalEl = document.getElementById('clubLookupModal');
            const clubLookupInput = document.getElementById('clubLookupInput');
            const clubNameField = document.getElementById('clubNameField');
            const clubEmailField = document.getElementById('clubEmailField');
            const clubAddressField = document.getElementById('clubAddressField');
            const clubPhoneField = document.getElementById('clubPhoneField');
            const clubWebsiteField = document.getElementById('clubWebsiteField');
            const clubPlaceIdField = document.getElementById('clubPlaceIdField');
            const clubLookupDetails = document.getElementById('clubLookupDetails');
            const clubLookupName = document.getElementById('clubLookupName');
            const clubLookupAddress = document.getElementById('clubLookupAddress');
            const clubLocationWrapper = document.getElementById('clubLocationWrapper');
            const clubCountrySelect = document.getElementById('clubCountrySelect');
            const clubStateSelect = document.getElementById('clubStateSelect');
            const clubCitySelect = document.getElementById('clubCitySelect');
            const placeholderText = clubDropdown && clubDropdown.dataset.placeholder ?
                clubDropdown.dataset.placeholder :
                'Select club';

            const clubsData = @json($clubsForScript->values());
            const clubsEndpoint = @json(route('register.clubs-for-sport', [], false));
            const statesEndpoint = @json(route('register.states-for-country', [], false));
            const citiesEndpoint = @json(route('register.cities-for-state', [], false));
            const hasjQuery = typeof window.$ !== 'undefined' && typeof window.$.fn !== 'undefined';
            const $clubDropdown = hasjQuery && clubDropdown ? window.$(clubDropdown) : null;
            const clubsCache = {};
            let clubsForCurrentSport = [];
            let clubsRequestSerial = 0;
            let select2Initialized = false;
            let lastValidClubValue = clubDropdown ? (clubDropdown.dataset.oldValue || clubDropdown.value || '') :
                '';
            if (lastValidClubValue === '__other__') {
                lastValidClubValue = '';
            }
            const oldClubCountryId = @json(old('club_country_id'));
            const oldClubStateId = @json(old('club_state_id'));
            const oldClubCityId = @json(old('club_city_id'));
            clubsCache[sportCacheKey(null)] = Array.isArray(clubsData) ? clubsData.slice() : [];
            clubsForCurrentSport = clubsForSport(sportSelect ? sportSelect.value : null);
            if (sportSelect) {
                setClubsForCurrentSport(sportSelect.value || null, clubsForCurrentSport);
            } else {
                clubsForCurrentSport = clubsCache[sportCacheKey(null)];
            }

            const disallowedPlaceTypes = new Set([
                'night_club',
                'bar',
                'restaurant',
                'cafe',
                'lodging',
                'hotel',
                'motel',
                'store',
                'shopping_mall',
                'liquor_store',
                'real_estate_agency',
                'insurance_agency',
                'lawyer',
                'local_government_office'
            ]);
            const sportsKeywordList = [
                'sport',
                'athletic',
                'gym',
                'fitness',
                'club',
                'arena',
                'stadium',
                'soccer',
                'football',
                'basketball',
                'baseball',
                'hockey',
                'tennis',
                'golf',
                'cricket',
                'lacrosse',
                'rowing',
                'swim',
                'pool',
                'martial',
                'karate',
                'boxing',
                'dojo',
                'yoga',
                'dance',
                'skate',
                'skating',
                'ice',
                'track',
                'field',
                'rugby',
                'volleyball',
                'cycling',
                'bike'
            ];
            const sportsRegex = new RegExp(sportsKeywordList.join('|'), 'i');

            function looksLikeSportsClub(place) {
                if (!place) {
                    return false;
                }
                const types = Array.isArray(place.types) ? place.types : [];
                if (types.some(function(type) {
                        return disallowedPlaceTypes.has(type);
                    })) {
                    return false;
                }
                if (types.some(function(type) {
                        return sportsRegex.test(type);
                    })) {
                    return true;
                }
                const textContext = [place.name || '', place.formatted_address || '', place.vicinity || '']
                    .join(' ')
                    .toLowerCase();
                return sportsKeywordList.some(function(keyword) {
                    return textContext.includes(keyword);
                });
            }

            function markPlaceInvalid(input, cleanup) {
                if (typeof cleanup === 'function') {
                    cleanup();
                }
                if (!input) {
                    return;
                }
                input.value = '';
                input.classList.add('is-invalid');
                const message = 'Please select a sports club or athletic facility.';
                if (typeof input.setCustomValidity === 'function') {
                    input.setCustomValidity(message);
                    input.reportValidity();
                    window.setTimeout(function() {
                        input.setCustomValidity('');
                        input.classList.remove('is-invalid');
                    }, 2400);
                } else if (window.alert) {
                    window.alert(message);
                    input.classList.remove('is-invalid');
                }
            }

            function clearPlaceValidity(input) {
                if (!input) {
                    return;
                }
                input.classList.remove('is-invalid');
                if (typeof input.setCustomValidity === 'function') {
                    input.setCustomValidity('');
                }
            }

            if (clubNameInput) {
                clubNameInput.addEventListener('input', function() {
                    clearPlaceValidity(clubNameInput);
                });
            }
            if (clubLookupInput) {
                clubLookupInput.addEventListener('input', function() {
                    clearPlaceValidity(clubLookupInput);
                });
            }

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

                (items || []).forEach(function(item) {
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
                if (!clubStateSelect) {
                    return Promise.resolve([]);
                }
                const statePlaceholder = clubStateSelect.dataset.placeholder || 'Select state';
                const cityPlaceholder = clubCitySelect ? (clubCitySelect.dataset.placeholder || 'Select city') :
                    'Select city';

                if (!countryId) {
                    resetSelect(clubStateSelect, statePlaceholder);
                    resetSelect(clubCitySelect, cityPlaceholder);
                    return Promise.resolve([]);
                }

                resetSelect(clubStateSelect, statePlaceholder);
                resetSelect(clubCitySelect, cityPlaceholder);

                return fetch(statesEndpoint + '?country_id=' + encodeURIComponent(countryId), {
                        headers: {
                            'Accept': 'application/json'
                        },
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Failed to load states');
                        }
                        return response.json();
                    })
                    .then(function(payload) {
                        const items = Array.isArray(payload?.data) ? payload.data : [];
                        setSelectOptions(clubStateSelect, items, selectedStateId, statePlaceholder);
                        if (selectedStateId) {
                            return loadCities(selectedStateId, selectedCityId);
                        }
                        return items;
                    })
                    .catch(function() {
                        resetSelect(clubStateSelect, statePlaceholder);
                        resetSelect(clubCitySelect, cityPlaceholder);
                        return [];
                    });
            }

            function loadCities(stateId, selectedCityId) {
                if (!clubCitySelect) {
                    return Promise.resolve([]);
                }
                const cityPlaceholder = clubCitySelect.dataset.placeholder || 'Select city';

                if (!stateId) {
                    resetSelect(clubCitySelect, cityPlaceholder);
                    return Promise.resolve([]);
                }

                resetSelect(clubCitySelect, cityPlaceholder);

                return fetch(citiesEndpoint + '?state_id=' + encodeURIComponent(stateId), {
                        headers: {
                            'Accept': 'application/json'
                        },
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Failed to load cities');
                        }
                        return response.json();
                    })
                    .then(function(payload) {
                        const items = Array.isArray(payload?.data) ? payload.data : [];
                        setSelectOptions(clubCitySelect, items, selectedCityId, cityPlaceholder);
                        return items;
                    })
                    .catch(function() {
                        resetSelect(clubCitySelect, cityPlaceholder);
                        return [];
                    });
            }

            function loadCoachCities(countryId) {
                if (!coachCitySelect) {
                    return Promise.resolve([]);
                }
                const cityPlaceholder = 'Select City';

                if (!countryId) {
                    resetSelect(coachCitySelect, cityPlaceholder);
                    return Promise.resolve([]);
                }

                resetSelect(coachCitySelect, cityPlaceholder);

                return fetch('/api/cities?country_id=' + encodeURIComponent(countryId), {
                        headers: {
                            'Accept': 'application/json'
                        },
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Failed to load cities');
                        }
                        return response.json();
                    })
                    .then(function(payload) {
                        const items = Array.isArray(payload?.data) ? payload.data : [];
                        setSelectOptions(coachCitySelect, items, null, cityPlaceholder);
                        return items;
                    })
                    .catch(function() {
                        resetSelect(coachCitySelect, cityPlaceholder);
                        return [];
                    });
            }

            function initSelect2() {
                if ($clubDropdown && !select2Initialized) {
                    const parent = window.$('body');
                    // $clubDropdown.select2({
                    //     // placeholder: placeholderText,
                    //     allowClear: true,
                    //     width: '100%',
                    //     // dropdownParent: parent && parent.length ? parent : undefined,
                    // });
                    select2Initialized = true;
                }
            }

            function clubsForSport(sportId) {
                if (!sportId) {
                    return clubsData;
                }
                return clubsData.filter(function(club) {
                    if (club.sport_id === null || typeof club.sport_id === 'undefined') {
                        return true;
                    }
                    return String(club.sport_id) === String(sportId);
                });
            }

            function sportCacheKey(sportId) {
                if (sportId === null || typeof sportId === 'undefined' || sportId === '') {
                    return 'all';
                }
                return String(sportId);
            }

            function setClubsForCurrentSport(sportId, clubs) {
                clubsForCurrentSport = Array.isArray(clubs) ? clubs.slice() : [];
                clubsCache[sportCacheKey(sportId)] = clubsForCurrentSport;
            }

            function setClubDropdownDisabled(disabled) {
                if ($clubDropdown) {
                    $clubDropdown.prop('disabled', disabled);
                }
                if (clubDropdown) {
                    clubDropdown.disabled = disabled;
                }
            }

            function requestClubsForSport(sportId) {
                const key = sportCacheKey(sportId);
                if (clubsCache[key]) {
                    return Promise.resolve({
                        sportId: sportId,
                        clubs: clubsCache[key],
                        fromCache: true,
                    });
                }
                const params = new URLSearchParams();
                if (sportId) {
                    params.append('sport', sportId);
                }
                const url = clubsEndpoint + (params.toString() ? `?${params.toString()}` : '');
                return fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                }).then(function(response) {
                    if (!response.ok) {
                        throw new Error('Failed to load clubs');
                    }
                    return response.json();
                }).then(function(payload) {
                    const clubs = Array.isArray(payload?.data) ? payload.data : (Array.isArray(payload) ?
                        payload : []);
                    clubsCache[key] = clubs;
                    return {
                        sportId: sportId,
                        clubs: clubs,
                        fromCache: false,
                    };
                });
            }

            function refreshClubsForSport(sportId, requestedValue) {
                if (!clubDropdown) {
                    return;
                }
                const requestSerial = ++clubsRequestSerial;
                setClubDropdownDisabled(true);
                clubsForCurrentSport = [];
                rebuildClubOptions(requestedValue, []);
                requestClubsForSport(sportId).then(function(result) {
                    if (requestSerial !== clubsRequestSerial) {
                        return;
                    }
                    setClubsForCurrentSport(result.sportId || null, result.clubs || []);
                    rebuildClubOptions(requestedValue, result.clubs || []);
                }).catch(function(error) {
                    if (requestSerial !== clubsRequestSerial) {
                        return;
                    }
                    console.error('Unable to fetch clubs for sport selection', error);
                    const fallback = clubsForSport(sportId);
                    setClubsForCurrentSport(sportId, fallback);
                    rebuildClubOptions(requestedValue, fallback);
                }).finally(function() {
                    if (requestSerial === clubsRequestSerial) {
                        setClubDropdownDisabled(false);
                    }
                });
            }

            function rebuildClubOptions(requestedValue, overrideClubs) {
                if (!clubDropdown) {
                    return;
                }
                const currentValue = typeof requestedValue !== 'undefined' ?
                    requestedValue :
                    ($clubDropdown ? $clubDropdown.val() : clubDropdown.value);
                const options = [];
                // const placeholderOption = new Option(placeholderText, '', false, false);
                // placeholderOption.dataset.placeholder = 'true';
                // options.push(placeholderOption);
                const clubsToRender = Array.isArray(overrideClubs) ? overrideClubs : clubsForCurrentSport;
                clubsToRender.forEach(function(club) {
                    options.push(new Option(club.name, String(club.id), false, false));
                });
                if (clubSelectionModeInput && clubSelectionModeInput.value === 'new' && newClubNameInput &&
                    newClubNameInput.value) {
                    const label = 'New Club: ' + newClubNameInput.value;
                    const newOption = new Option(label, '__new__', false, false);
                    newOption.dataset.dynamic = 'true';
                    options.push(newOption);
                }
                options.push(new Option('Other / My club is not listed', '__other__', false, false));
                if ($clubDropdown) {
                    $clubDropdown.empty();
                    options.forEach(function(option) {
                        $clubDropdown.append(option);
                    });
                    if (currentValue && $clubDropdown.find("option[value='" + currentValue + "']").length) {
                        $clubDropdown.val(currentValue);
                    } else {
                        $clubDropdown.val('');
                        if (lastValidClubValue === currentValue) {
                            lastValidClubValue = '';
                        }
                    }
                    $clubDropdown.trigger('change.select2');
                } else if (clubDropdown) {
                    while (clubDropdown.firstChild) {
                        clubDropdown.removeChild(clubDropdown.firstChild);
                    }
                    options.forEach(function(option) {
                        clubDropdown.appendChild(option);
                    });
                    const hasMatch = currentValue && Array.from(clubDropdown.options).some(function(option) {
                        return option.value === currentValue;
                    });
                    if (hasMatch) {
                        clubDropdown.value = currentValue;
                    } else {
                        clubDropdown.value = '';
                        if (lastValidClubValue === currentValue) {
                            lastValidClubValue = '';
                        }
                    }
                }
            }

            function updateSummary() {
                if (!summaryCard) {
                    return;
                }
                if (clubSelectionModeInput.value === 'new' && newClubNameInput.value) {
                    summaryName.textContent = newClubNameInput.value;
                    summaryAddress.textContent = newClubAddressInput.value || '—';
                    summaryEmail.textContent = newClubEmailInput.value || '—';
                    summaryPhone.textContent = newClubPhoneInput.value || '—';
                    summaryWebsite.textContent = newClubWebsiteInput.value || '—';
                    summaryCard.classList.remove('d-none');
                } else {
                    summaryCard.classList.add('d-none');
                }
            }

            function resetNewClubData() {
                if (!clubSelectionModeInput) {
                    return;
                }
                clubSelectionModeInput.value = 'existing';
                [newClubNameInput, newClubAddressInput, newClubPlaceInput, newClubPhoneInput, newClubWebsiteInput,
                    newClubEmailInput
                ].forEach(function(input) {
                    if (input) {
                        input.value = '';
                    }
                });
                updateSummary();
            }

            function handleClubValue(value) {
                if (!value) {
                    resetNewClubData();
                    lastValidClubValue = '';
                    return;
                }
                if (value === '__other__') {
                    if ($clubDropdown) {
                        $clubDropdown.val(lastValidClubValue);
                        $clubDropdown.trigger('change.select2');
                    } else if (clubDropdown) {
                        clubDropdown.value = lastValidClubValue;
                    }
                    if (clubLookupModalEl && window.bootstrap) {
                        const modalInstance = bootstrap.Modal.getOrCreateInstance(clubLookupModalEl);
                        modalInstance.show();
                    }
                    return;
                }
                if (value === '__new__') {
                    clubSelectionModeInput.value = 'new';
                    updateSummary();
                    lastValidClubValue = value;
                    return;
                }
                lastValidClubValue = value;
                resetNewClubData();
            }

            if ($clubDropdown) {
                initSelect2();
                rebuildClubOptions(lastValidClubValue);
                $clubDropdown.on('change', function(event) {
                    handleClubValue(event.target.value);
                });
            } else if (clubDropdown) {
                clubDropdown.addEventListener('change', function(event) {
                    handleClubValue(event.target.value);
                });
            }

            if (clubSelectionModeInput && clubSelectionModeInput.value === 'new' && newClubNameInput &&
                newClubNameInput.value) {
                updateSummary();
                if ($clubDropdown) {
                    $clubDropdown.val('__new__').trigger('change.select2');
                    lastValidClubValue = '__new__';
                }
            } else if (lastValidClubValue) {
                handleClubValue(lastValidClubValue);
            }

            sportSelect && sportSelect.addEventListener('change', function() {
                const selectedSportId = sportSelect.value || null;
                lastValidClubValue = '';
                resetNewClubData();
                refreshClubsForSport(selectedSportId, '');
            });

            clubCountrySelect && clubCountrySelect.addEventListener('change', function() {
                const countryId = clubCountrySelect.value || '';
                loadStates(countryId, null, null);
            });

            clubStateSelect && clubStateSelect.addEventListener('change', function() {
                const stateId = clubStateSelect.value || '';
                loadCities(stateId, null);
            });

            let clubAutocomplete = null;
            let clubPlacesService = null;

            function initClubAutocomplete() {
                if (!clubNameInput || typeof google === 'undefined' || !google.maps || !google.maps.places) {
                    return;
                }
                if (clubAutocomplete) {
                    return;
                }
                clubAutocomplete = new google.maps.places.Autocomplete(clubNameInput, {
                    types: ['establishment'],
                    fields: ['name', 'formatted_address', 'place_id', 'address_components',
                        'formatted_phone_number', 'website'
                    ]
                });
                clubPlacesService = new google.maps.places.PlacesService(document.createElement('div'));

                clubAutocomplete.addListener('place_changed', function() {
                    const place = clubAutocomplete.getPlace();
                    if (!place) {
                        return;
                    }

                    if (!looksLikeSportsClub(place)) {
                        markPlaceInvalid(clubNameInput, function() {
                            if (newClubAddressInput) {
                                newClubAddressInput.value = '';
                            }
                            if (newClubPlaceInput) {
                                newClubPlaceInput.value = '';
                            }
                            if (newClubPhoneInput) {
                                newClubPhoneInput.value = '';
                            }
                            if (newClubWebsiteInput) {
                                newClubWebsiteInput.value = '';
                            }
                            const hiddenAddress = document.querySelector("input[name='club_address']");
                            if (hiddenAddress) {
                                hiddenAddress.value = '';
                            }
                            const hiddenPlace = document.querySelector("input[name='club_place_id']");
                            if (hiddenPlace) {
                                hiddenPlace.value = '';
                            }
                        });
                        return;
                    }

                    // Basic fields
                    if (place.name) {
                        clubNameInput.value = place.name;
                    }
                    if (place.formatted_address && document.querySelector("input[name='club_address']")) {
                        document.querySelector("input[name='club_address']").value = place
                        .formatted_address;
                    }
                    if (place.place_id && document.querySelector("input[name='club_place_id']")) {
                        document.querySelector("input[name='club_place_id']").value = place.place_id;
                    }

                    // Address components
                    if (place.address_components) {
                        const addressComponents = place.address_components;
                        const country = addressComponents.find(c => c.types.includes('country'));
                        const state = addressComponents.find(c => c.types.includes(
                            'administrative_area_level_1'));
                        const city = addressComponents.find(c => c.types.includes('locality'));

                        // Set country if found
                        if (country && clubCountrySelect) {
                            const countryCode = country.short_name;
                            const countryName = country.long_name;
                            // Find matching option in dropdown
                            const option = Array.from(clubCountrySelect.options).find(opt =>
                                opt.value === countryCode || opt.textContent.includes(countryName)
                            );
                            if (option) {
                                clubCountrySelect.value = option.value;
                                clubCountrySelect.dispatchEvent(new Event('change'));
                            }
                        }

                        // Set state if found
                        if (state && clubStateSelect) {
                            const stateName = state.long_name;
                            // Wait for states to load
                            setTimeout(() => {
                                const stateOption = Array.from(clubStateSelect.options).find(opt =>
                                    opt.textContent.includes(stateName)
                                );
                                if (stateOption) {
                                    clubStateSelect.value = stateOption.value;
                                    clubStateSelect.dispatchEvent(new Event('change'));
                                }
                            }, 500);
                        }

                        // Set city if found
                        if (city && clubCitySelect) {
                            const cityName = city.long_name;
                            // Wait for cities to load
                            setTimeout(() => {
                                const cityOption = Array.from(clubCitySelect.options).find(opt =>
                                    opt.textContent.includes(cityName)
                                );
                                if (cityOption) {
                                    clubCitySelect.value = cityOption.value;
                                }
                            }, 1000);
                        }
                    }

                    // Get additional details if place_id exists
                    if (place.place_id && clubPlacesService) {
                        clubPlacesService.getDetails({
                            placeId: place.place_id,
                            fields: ['formatted_phone_number', 'website']
                        }, function(details, status) {
                            if (status === google.maps.places.PlacesServiceStatus.OK && details) {
                                if (details.formatted_phone_number && document.querySelector(
                                        "input[name='club_phone']")) {
                                    document.querySelector("input[name='club_phone']").value =
                                        details.formatted_phone_number;
                                }
                                if (details.website && document.querySelector(
                                        "input[name='club_website']")) {
                                    document.querySelector("input[name='club_website']").value =
                                        details.website;
                                }
                            }
                        });
                    }
                });
            }

            // Coach country change event
            coachCountrySelect && coachCountrySelect.addEventListener('change', function() {
                const countryId = coachCountrySelect.value || '';
                loadCoachCities(countryId);
            });

            function toggleCoachFields(value) {
                const isCoach = value === 'coach';
                [coachGenderWrapper, coachPhoneWrapper, coachCountryWrapper, coachCityWrapper,
                    coachExperienceWrapper, coachBioWrapper
                ]
                .forEach(function(wrapper) {
                    if (wrapper) {
                        wrapper.classList.toggle('d-none', !isCoach);
                    }
                }); +
                setCoachFieldsEnabled(isCoach);
            }


            function setCoachFieldsEnabled(isCoach) {
                // All inputs/selects with .coach-input
                coachInputs.forEach(function(el) {
                    if (isCoach) {
                        el.removeAttribute('disabled');
                        el.setAttribute('required', 'required');
                    } else {
                        el.removeAttribute('required');
                        el.setAttribute('disabled', 'disabled');
                        // Clear values when hiding to avoid stale state
                        if (el.tagName === 'SELECT') {
                            el.selectedIndex = 0;
                        } else {
                            el.value = '';
                        }
                    }
                });
            }


            function toggleClubField(value) {
                if (value === 'club') {
                    clubSelectWrapper?.classList.add('d-none');
                    clubInputWrapper?.classList.remove('d-none');
                    if ($clubDropdown) {
                        $clubDropdown.val('').trigger('change.select2');
                    }
                    if (clubNameInput) {
                        clubNameInput.required = true;
                        initClubAutocomplete();
                    }
                    clubLocationWrapper?.classList.remove('d-none');
                    if (clubCountrySelect) {
                        clubCountrySelect.removeAttribute('disabled');
                        clubCountrySelect.setAttribute('required', 'required');
                    }
                    if (clubStateSelect) {
                        clubStateSelect.setAttribute('required', 'required');
                        if (!clubCountrySelect || !clubCountrySelect.value) {
                            resetSelect(clubStateSelect, clubStateSelect.dataset.placeholder || 'Select state');
                        } else {
                            clubStateSelect.removeAttribute('disabled');
                        }
                    }
                    if (clubCitySelect) {
                        clubCitySelect.setAttribute('required', 'required');
                        if (!clubStateSelect || !clubStateSelect.value) {
                            resetSelect(clubCitySelect, clubCitySelect.dataset.placeholder || 'Select city');
                        } else {
                            clubCitySelect.removeAttribute('disabled');
                        }
                    }
                    const activeCountry = clubCountrySelect ? clubCountrySelect.value : '';
                    if (activeCountry) {
                        const hasStateOptions = clubStateSelect && clubStateSelect.options && clubStateSelect
                            .options.length > 1;
                        const hasCityOptions = clubCitySelect && clubCitySelect.options && clubCitySelect.options
                            .length > 1;
                        const targetState = hasStateOptions && clubStateSelect ? clubStateSelect.value :
                            oldClubStateId;
                        const targetCity = hasCityOptions && clubCitySelect ? clubCitySelect.value : oldClubCityId;
                        loadStates(activeCountry, targetState, targetCity);
                    }
                    lastValidClubValue = '';
                    resetNewClubData();
                } else if (value === 'player' || value === '') {
                    clubSelectWrapper?.classList.remove('d-none');
                    clubInputWrapper?.classList.add('d-none');
                    if (clubNameInput) {
                        clubNameInput.value = '';
                        clubNameInput.required = false;
                    }
                    initSelect2();
                    rebuildClubOptions(lastValidClubValue);
                    const activeSportId = sportSelect ? (sportSelect.value || null) : null;
                    refreshClubsForSport(activeSportId, lastValidClubValue);
                    clubLocationWrapper?.classList.add('d-none');
                    [clubCountrySelect, clubStateSelect, clubCitySelect].forEach(function(select) {
                        if (select) {
                            select.removeAttribute('required');
                            select.setAttribute('disabled', 'disabled');
                        }
                    });
                } else {
                    // For other types (referee, college, volunteer, coach), hide club fields
                    clubSelectWrapper?.classList.add('d-none');
                    clubInputWrapper?.classList.add('d-none');
                    if ($clubDropdown) {
                        $clubDropdown.val('').trigger('change.select2');
                    }
                    if (clubNameInput) {
                        clubNameInput.value = '';
                        clubNameInput.required = false;
                    }
                    clubLocationWrapper?.classList.add('d-none');
                    [clubCountrySelect, clubStateSelect, clubCitySelect].forEach(function(select) {
                        if (select) {
                            select.removeAttribute('required');
                            select.setAttribute('disabled', 'disabled');
                        }
                    });
                    lastValidClubValue = '';
                    resetNewClubData();
                }
            }

            function toggleSportField(value) {
                if (!sportSelect) {
                    return;
                }
                const wrapper = sportSelect.closest('.col-md-6');
                if (value === 'college') {
                    wrapper?.classList.add('d-none');
                    sportSelect.disabled = true;
                    sportSelect.removeAttribute('required');
                    sportSelect.value = '';
                } else {
                    wrapper?.classList.remove('d-none');
                    sportSelect.disabled = false;
                    sportSelect.setAttribute('required', 'required');
                    // For coach, change the name attribute to sport_id
                    if (value === 'coach') {
                        sportSelect.setAttribute('name', 'sport_id');
                    } else {
                        sportSelect.setAttribute('name', 'sport');
                    }
                }
            }

            function toggleAcademicFields(value) {
                const hideCollegeUni = value === 'club' || value === 'player' || value === 'coach';
                collegeWrapper?.classList.toggle('d-none', hideCollegeUni);
                universityWrapper?.classList.toggle('d-none', hideCollegeUni);
                const showAffiliation = value === 'referee' || /affiliation/i.test(window.location.href);
                affiliationWrapper?.classList.toggle('d-none', !showAffiliation);
            }

            function calculateAge(dobValue) {
                if (!dobValue) {
                    return null;
                }
                const dobDate = new Date(dobValue);
                if (Number.isNaN(dobDate.getTime())) {
                    return null;
                }
                const today = new Date();
                let age = today.getFullYear() - dobDate.getFullYear();
                const monthDiff = today.getMonth() - dobDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dobDate.getDate())) {
                    age--;
                }
                return age;
            }

            function updateGuardianFields() {
                if (!guardianWrapper) {
                    return;
                }
                const selected = document.querySelector("input[name='user_type']:checked");
                const isPlayer = selected ? selected.value === 'player' : false;
                const age = calculateAge(dobInput?.value || '');
                const showGuardian = isPlayer && age !== null && age < 13;
                guardianWrapper.classList.toggle('d-none', !showGuardian);
                guardianInputs.forEach(function(input) {
                    if (showGuardian) {
                        input.setAttribute('required', 'required');
                    } else {
                        input.removeAttribute('required');
                        if (!isPlayer) {
                            input.value = '';
                        }
                    }
                });
            }

            function togglePlayerFields(value) {
                const isPlayer = value === 'player';
                if (dobWrapper) {
                    dobWrapper.classList.toggle('d-none', !isPlayer);
                }
                if (dobInput) {
                    if (isPlayer) {
                        dobInput.setAttribute('required', 'required');
                    } else {
                        dobInput.removeAttribute('required');
                        dobInput.value = '';
                    }
                }
                if (!isPlayer && guardianWrapper) {
                    guardianWrapper.classList.add('d-none');
                }
                updateGuardianFields();
            }

            function applyInitialState() {
                const selected = document.querySelector("input[name='user_type']:checked");
                const value = selected ? selected.value : '';
                toggleClubField(value);
                toggleSportField(value);
                toggleAcademicFields(value);
                togglePlayerFields(value);
                toggleCoachFields(value);
            }

            userTypeRadios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    toggleClubField(this.value);
                    toggleSportField(this.value);
                    toggleAcademicFields(this.value);
                    togglePlayerFields(this.value);
                    toggleCoachFields(this.value);
                });
            });

            if (dobInput) {
                dobInput.addEventListener('change', updateGuardianFields);
                dobInput.addEventListener('input', updateGuardianFields);
            }

            applyInitialState();

            function updateLookupPreview(name, address) {
                if (!clubLookupDetails) {
                    return;
                }
                if (name || address) {
                    clubLookupName.textContent = name || '';
                    clubLookupAddress.textContent = address || '';
                    clubLookupDetails.classList.remove('d-none');
                } else {
                    clubLookupDetails.classList.add('d-none');
                }
            }

            let placesAutocomplete = null;
            let placesService = null;

            function initPlacesAutocomplete() {
                if (!clubLookupInput || typeof google === 'undefined' || !google.maps || !google.maps.places) {
                    return;
                }
                if (placesAutocomplete) {
                    return;
                }
                placesAutocomplete = new google.maps.places.Autocomplete(clubLookupInput, {
                    types: ['establishment'],
                    fields: ['place_id', 'name', 'formatted_address'],
                });
                placesService = new google.maps.places.PlacesService(document.createElement('div'));
                placesAutocomplete.addListener('place_changed', function() {
                    const place = placesAutocomplete.getPlace();
                    if (!place) {
                        return;
                    }
                    if (!looksLikeSportsClub(place)) {
                        markPlaceInvalid(clubLookupInput, function() {
                            if (clubNameField) {
                                clubNameField.value = '';
                            }
                            if (clubEmailField) {
                                clubEmailField.value = '';
                            }
                            if (clubAddressField) {
                                clubAddressField.value = '';
                            }
                            if (clubPhoneField) {
                                clubPhoneField.value = '';
                            }
                            if (clubWebsiteField) {
                                clubWebsiteField.value = '';
                            }
                            if (clubPlaceIdField) {
                                clubPlaceIdField.value = '';
                            }
                            updateLookupPreview('', '');
                        });
                        return;
                    }
                    if (place.name && clubNameField) {
                        clubNameField.value = place.name;
                    }
                    if (place.formatted_address && clubAddressField) {
                        clubAddressField.value = place.formatted_address;
                    }
                    if (clubPlaceIdField) {
                        clubPlaceIdField.value = place.place_id || '';
                    }
                    updateLookupPreview(place.name, place.formatted_address);
                    if (placesService && place.place_id) {
                        placesService.getDetails({
                            placeId: place.place_id,
                            fields: ['formatted_phone_number', 'website']
                        }, function(details, status) {
                            if (status === google.maps.places.PlacesServiceStatus.OK && details) {
                                if (details.formatted_phone_number && clubPhoneField && !
                                    clubPhoneField.value) {
                                    clubPhoneField.value = details.formatted_phone_number;
                                }
                                if (details.website && clubWebsiteField && !clubWebsiteField
                                    .value) {
                                    clubWebsiteField.value = details.website;
                                }
                            }
                        });
                    }
                });
            }

            clubLookupModalEl && clubLookupModalEl.addEventListener('show.bs.modal', function() {
                if (clubSelectionModeInput.value === 'new') {
                    if (clubLookupInput) {
                        clubLookupInput.value = newClubNameInput.value || '';
                    }
                    if (clubNameField) {
                        clubNameField.value = newClubNameInput.value || '';
                    }
                    if (clubEmailField) {
                        clubEmailField.value = newClubEmailInput.value || '';
                    }
                    if (clubAddressField) {
                        clubAddressField.value = newClubAddressInput.value || '';
                    }
                    if (clubPhoneField) {
                        clubPhoneField.value = newClubPhoneInput.value || '';
                    }
                    if (clubWebsiteField) {
                        clubWebsiteField.value = newClubWebsiteInput.value || '';
                    }
                    if (clubPlaceIdField) {
                        clubPlaceIdField.value = newClubPlaceInput.value || '';
                    }
                    updateLookupPreview(newClubNameInput.value, newClubAddressInput.value);
                } else {
                    [clubLookupInput, clubNameField, clubEmailField, clubAddressField, clubPhoneField,
                        clubWebsiteField, clubPlaceIdField
                    ].forEach(function(input) {
                        if (input) {
                            input.value = '';
                            input.classList.remove('is-invalid');
                        }
                    });
                    updateLookupPreview('', '');
                }
                setTimeout(function() {
                    clubLookupInput && clubLookupInput.focus();
                }, 150);
                initPlacesAutocomplete();
            });

            clubLookupModalEl && clubLookupModalEl.addEventListener('hidden.bs.modal', function() {
                if (clubLookupInput) {
                    clubLookupInput.value = '';
                }
                updateLookupPreview('', '');
            });

            confirmClubSelectionBtn && confirmClubSelectionBtn.addEventListener('click', function() {
                const name = clubNameField ? clubNameField.value.trim() : '';
                const email = clubEmailField ? clubEmailField.value.trim() : '';
                clubNameField && clubNameField.classList.remove('is-invalid');
                clubEmailField && clubEmailField.classList.remove('is-invalid');
                clubSelectionModeInput.value = 'new';
                if (newClubNameInput) {
                    newClubNameInput.value = name;
                }
                if (newClubEmailInput) {
                    newClubEmailInput.value = email;
                }
                if (newClubAddressInput) {
                    newClubAddressInput.value = clubAddressField ? clubAddressField.value.trim() : '';
                }
                if (newClubPhoneInput) {
                    newClubPhoneInput.value = clubPhoneField ? clubPhoneField.value.trim() : '';
                }
                if (newClubWebsiteInput) {
                    newClubWebsiteInput.value = clubWebsiteField ? clubWebsiteField.value.trim() : '';
                }
                if (newClubPlaceInput) {
                    newClubPlaceInput.value = clubPlaceIdField ? clubPlaceIdField.value.trim() : '';
                }
                updateSummary();
                rebuildClubOptions('__new__');
                if ($clubDropdown) {
                    $clubDropdown.val('__new__').trigger('change.select2');
                } else if (clubDropdown) {
                    clubDropdown.value = '__new__';
                }
                lastValidClubValue = '__new__';
                if (clubLookupModalEl && window.bootstrap) {
                    const modalInstance = bootstrap.Modal.getInstance(clubLookupModalEl);
                    modalInstance && modalInstance.hide();
                }
            });
        });
    </script>
@endsection
