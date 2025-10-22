@extends('layouts.admin')
@section('title', isset($referee) ? 'Edit Referee' : 'Add Referee')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">{{ isset($referee) ? 'Edit Referee' : 'Add Referee' }}</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($referee) ? route('admin.referees.update', $referee) : route('admin.referees.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($referee)) @method('PUT') @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $referee->full_name ?? '') }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $referee->email ?? '') }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $referee->phone ?? '') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Preferred Contact Method</label>
                    <input type="text" name="preferred_contact_method" class="form-control" value="{{ old('preferred_contact_method', $referee->preferred_contact_method ?? '') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Government ID</label>
                    <input type="text" name="government_id" class="form-control" value="{{ old('government_id', $referee->government_id ?? '') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Languages Spoken</label>
                    <input type="text" name="languages_spoken" class="form-control" value="{{ old('languages_spoken', $referee->languages_spoken ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $referee->city ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Region</label>
                    <input type="text" name="region" class="form-control" value="{{ old('region', $referee->region ?? '') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-control" value="{{ old('country', $referee->country ?? '') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">License Type</label>
                    <input type="text" name="license_type" class="form-control" value="{{ old('license_type', $referee->license_type ?? '') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Certifying Body</label>
                    <input type="text" name="certifying_body" class="form-control" value="{{ old('certifying_body', $referee->certifying_body ?? '') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">License Expiry Date</label>
                    <input type="date" name="license_expiry_date" class="form-control" value="{{ old('license_expiry_date', isset($referee->license_expiry_date) ? $referee->license_expiry_date->format('Y-m-d') : '') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Background Check Passed</label>
                    <select name="background_check_passed" class="form-select">
                        <option value="1" {{ old('background_check_passed', $referee->background_check_passed ?? false) ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('background_check_passed', $referee->background_check_passed ?? false) ? '' : 'selected' }}>No</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Liability Insurance</label>
                    <select name="liability_insurance" class="form-select">
                        <option value="1" {{ old('liability_insurance', $referee->liability_insurance ?? false) ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('liability_insurance', $referee->liability_insurance ?? false) ? '' : 'selected' }}>No</option>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Sports Officiated</label>
                    <input type="text" name="sports_officiated[]" class="form-control" value="{{ old('sports_officiated', isset($referee->sports_officiated) ? implode(',', $referee->sports_officiated) : '') }}" placeholder="Comma separated">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Internal Notes</label>
                    <textarea name="internal_notes" class="form-control">{{ old('internal_notes', $referee->internal_notes ?? '') }}</textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Account Status</label>
                    <select name="account_status" class="form-select">
                        <option value="active" {{ (old('account_status', $referee->account_status ?? '') === 'active') ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ (old('account_status', $referee->account_status ?? '') === 'inactive') ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ (old('account_status', $referee->account_status ?? '') === 'suspended') ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Club (Optional)</label>
                    <select name="club_id" class="form-select">
                        <option value="">-- None --</option>
                        @foreach(\App\Models\Club::all() as $club)
                            <option value="{{ $club->id }}" {{ old('club_id', $referee->club_id ?? '') == $club->id ? 'selected' : '' }}>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Profile Picture</label>
                    <input type="file" name="profile_picture" class="form-control">
                    @if(isset($referee) && $referee->profile_picture)
                        <img src="{{ asset('storage/' . $referee->profile_picture) }}" class="mt-2 rounded" width="80">
                    @endif
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Liability Document</label>
                    <input type="file" name="liability_document" class="form-control">
                    @if(isset($referee) && $referee->liability_document)
                        <a href="{{ asset('storage/' . $referee->liability_document) }}" target="_blank" class="d-block mt-2">View Current Document</a>
                    @endif
                </div>
            </div>

            <button type="submit" class="btn btn-success mt-3">
                {{ isset($referee) ? 'Update Referee' : 'Create Referee' }}
            </button>
        </form>
    </div>
</div>
@endsection
