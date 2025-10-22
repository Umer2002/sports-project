@extends('layouts.coach-dashboard')

@section('title', 'Edit Profile')
@section('page_title', 'Edit Profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Edit Your Profile</h3>
                </div>
                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
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

                    <form method="POST" action="{{ route('coach.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="{{ old('first_name', $coach->first_name) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="{{ old('last_name', $coach->last_name) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sport_id" class="form-label">Primary Sport <span class="text-danger">*</span></label>
                                <select class="form-select" id="sport_id" name="sport_id" required>
                                    <option value="">Select a sport</option>
                                    @foreach ($sports as $id => $name)
                                        <option value="{{ $id }}" {{ old('sport_id', $coach->sport_id) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone', $coach->phone) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4" 
                                      placeholder="Tell us about your coaching experience...">{{ old('bio', $coach->bio) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Profile Photo</label>
                            @if ($coach->photo)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($coach->photo) }}" alt="Current photo" 
                                         class="img-thumbnail" style="max-width: 150px;">
                                </div>
                            @endif
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                            <small class="form-text text-muted">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                            <a href="{{ route('coach-dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

