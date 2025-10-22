@extends('layouts.default')
@section('content')
    <div class="container">
        <div class="register-box">
            <h3 class="text-center text-heading mb-4">Player Registration</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.player.store') }}">
                @csrf
                <input type="hidden" name="user_type" value="player">

                @if (request('ref'))
                    <input type="hidden" name="ref" value="{{ request('ref') }}">
                @endif
                @if (request('ref_type'))
                    <input type="hidden" name="ref_type" value="{{ request('ref_type') }}">
                @endif
                @if(!empty($inviteToken))
                    <input type="hidden" name="invite_token" value="{{ $inviteToken }}">
                @endif
                @if(!$invitation && $is_club)
                <div class="row mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Sport *</label>
                        <select class="form-control txt-select @error('sport') is-invalid @enderror" name="sport"
                            required>
                            <option value="">Select Sport</option>
                            @foreach ($sports as $sport)
                                <option value="{{ $sport->id }}"
                                    {{ (string) old('sport') === (string) $sport->id ? 'selected' : '' }}>
                                    {{ $sport->name }}</option>
                            @endforeach
                        </select>
                        @error('sport')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Club</label>
                        <select class="form-control txt-select @error('club') is-invalid @enderror" name="club">
                            <option value="">Select Club</option>
                            @foreach ($clubs as $club)
                                <option value="{{ $club->id }}"
                                    {{ (string) old('club', $selectedClub) === (string) $club->id ? 'selected' : '' }}>
                                    {{ $club->name }}</option>
                            @endforeach
                        </select>
                        @error('club')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @elseif ($is_club)
                    <p class="alert alert-success" role="alert">Thanks for Choosing Play2Earn Your are invited by <strong>{{$invitation->name}} Club<storng></p>
                    <input type="hidden" value="{{$invitation->sport_id}}" name="sport" />
                    <input type="hidden" value="{{$invitation->id}}" name="club" />

                @endif

                <div class="row mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">First Name *</label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                            name="first_name" value="{{ old('first_name') }}" placeholder="First Name" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Last Name *</label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name"
                            value="{{ old('last_name') }}" placeholder="Last Name" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                        value="{{ old('email' , $emailInvited ?? '') }}" placeholder="Email" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Password *</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                            placeholder="********" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="********"
                            required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" id="dob" class="form-control @error('dob') is-invalid @enderror"
                        name="dob" value="{{ old('dob') }}" required>
                    @error('dob')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">We require guardian approval for players under 13.</small>
                </div>

                <div id="guardianFields" class="mb-3 d-none">
                    <div class="alert alert-info py-2">Guardian information is required for players under 13.</div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Guardian First Name *</label>
                            <input type="text" class="form-control @error('guardian_first_name') is-invalid @enderror"
                                name="guardian_first_name" value="{{ old('guardian_first_name') }}"
                                placeholder="Guardian First Name">
                            @error('guardian_first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Guardian Last Name *</label>
                            <input type="text" class="form-control @error('guardian_last_name') is-invalid @enderror"
                                name="guardian_last_name" value="{{ old('guardian_last_name') }}"
                                placeholder="Guardian Last Name">
                            @error('guardian_last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Guardian Email *</label>
                            <input type="email" class="form-control @error('guardian_email') is-invalid @enderror"
                                name="guardian_email" value="{{ old('guardian_email') }}" placeholder="Guardian Email">
                            @error('guardian_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">College</label>
                        <input type="text" class="form-control @error('college') is-invalid @enderror" name="college"
                            value="{{ old('college') }}" placeholder="College">
                        @error('college')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">University</label>
                        <input type="text" class="form-control @error('university') is-invalid @enderror"
                            name="university" value="{{ old('university') }}" placeholder="University">
                        @error('university')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Affiliation</label>
                    <input type="text" name="referee_affiliation"
                        class="form-control @error('referee_affiliation') is-invalid @enderror"
                        value="{{ old('referee_affiliation', $inviteToken ?? '') }}" placeholder="Club or League Affiliation" disabled/>
                    @error('referee_affiliation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                    <label class="form-check-label" for="termsCheck">
                        By signing up you agree to our Terms of Services and Privacy Policy.
                    </label>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-signup w-100">Create Account</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            const dobInput = document.getElementById('dob');
            const guardianSection = document.getElementById('guardianFields');

            function calculateAge(dateString) {
                const today = new Date();
                const birthDate = new Date(dateString);
                if (Number.isNaN(birthDate.getTime())) {
                    return null;
                }
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age;
            }

            function toggleGuardianFields() {
                const value = dobInput.value;
                const hasErrors = guardianSection.querySelector('.is-invalid') !== null;
                if (!value) {
                    guardianSection.classList.toggle('d-none', !hasErrors);
                    return;
                }
                const age = calculateAge(value);
                if (age !== null && age < 13) {
                    guardianSection.classList.remove('d-none');
                } else if (!hasErrors) {
                    guardianSection.classList.add('d-none');
                }
            }

            if (dobInput) {
                dobInput.addEventListener('change', toggleGuardianFields);
                toggleGuardianFields();
            }
        });
    </script>
@endsection
