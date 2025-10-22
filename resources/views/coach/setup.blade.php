@extends('layouts.coach-dashboard')

@section('title', 'Coach Profile Setup')
@section('page_title', 'Complete Your Profile')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Welcome, Coach! Let's Set Up Your Profile</h3>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('coach.setup.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="{{ old('first_name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="{{ old('last_name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="sport_id" class="form-label">Primary Sport <span class="text-danger">*</span></label>
                            <select class="form-select" id="sport_id" name="sport_id" required>
                                <option value="">Select a sport</option>
                                @foreach ($sports as $id => $name)
                                    <option value="{{ $id }}" {{ old('sport_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone') }}">
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4" 
                                      placeholder="Tell us about your coaching experience...">{{ old('bio') }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check-circle me-2"></i>Complete Setup
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

