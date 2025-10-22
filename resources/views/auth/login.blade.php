@extends('layouts.default')

@section('title', 'Login')

@section('content')
    <div class="container">
        <div class="register-box">
            <h3 class="text-center text-heading mb-4">LOGIN</h3>

            @if (session('status'))
                <div class="alert alert-info text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" id="email" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" id="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        required placeholder="Enter your password">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                    <label class="form-check-label" for="remember_me">
                        Remember Me
                    </label>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-signup w-100">LOGIN</button>
                </div>

                <div class="text-center mt-3">
                    @if (Route::has('password.request'))
                        <a class="text-primary fw-bold" href="{{ route('password.request') }}">
                            Forgot your password?
                        </a>
                    @endif
                </div>

                <div class="text-center mt-3">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-warning fw-bold">SIGN UP</a>
                </div>
            </form>
        </div>
    </div>
@endsection
