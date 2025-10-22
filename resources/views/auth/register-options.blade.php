@extends('layouts.default')

@section('content')
    <div class="container py-5">
        <div class="register-box text-center">
            <h3 class="text-heading mb-4">Create Your Account</h3>

            @if ($errors->has('user_type'))
                <div class="alert alert-danger">{{ $errors->first('user_type') }}</div>
            @endif

            <p class="mb-4">Choose the role that best describes you to start the registration process.</p>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <a href="{{ route('register.player') }}" class="btn btn-signup w-100 py-3">Register as Player</a>
                </div>
                <div class="col-12 col-md-6">
                    <a href="{{ route('register.club') }}" class="btn btn-outline-primary w-100 py-3">Register as Club</a>
                </div>
                <div class="col-12 col-md-6">
                    <a href="{{ route('register.ambassador') }}" class="btn btn-outline-primary w-100 py-3">Register as Ambassador</a>
                </div>
                <div class="col-12 col-md-6">
                    <a href="{{ route('register.college') }}" class="btn btn-outline-primary w-100 py-3">Register as College / University</a>
                </div>
            </div>
            
            <div class="mt-3 text-center">
                <p class="text-muted">Looking to register as a Coach? Use the main <a href="{{ route('register') }}" class="text-primary">registration form</a> and select "Coach" as your role.</p>
            </div>
        </div>
    </div>
@endsection
