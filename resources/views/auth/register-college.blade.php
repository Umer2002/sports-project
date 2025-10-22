@extends('layouts.default')

@section('content')
    <div class="container">
        <div class="register-box">
            <h3 class="text-center text-heading mb-4">College / University Registration</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.college.store') }}">
                @csrf
                <input type="hidden" name="user_type" value="college">

                @if(request('ref'))
                    <input type="hidden" name="ref" value="{{ request('ref') }}">
                @endif
                @if(request('ref_type'))
                    <input type="hidden" name="ref_type" value="{{ request('ref_type') }}">
                @endif

                <div class="mb-3">
                    <label class="form-label">College / University Name *</label>
                    <input type="text" class="form-control @error('college_name') is-invalid @enderror" name="college_name" value="{{ old('college_name') }}" placeholder="Institution Name" required>
                    @error('college_name')
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
                    <input class="form-check-input" type="checkbox" id="collegeTerms" required>
                    <label class="form-check-label" for="collegeTerms">
                        By signing up you agree to our Terms of Services and Privacy Policy.
                    </label>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-signup w-100">Create Institution Account</button>
                </div>

                <div class="text-center mt-3">
                    Already have an account? <a href="{{ route('login') }}" class="text-warning fw-bold">Login</a>
                </div>
            </form>
        </div>
    </div>
@endsection
