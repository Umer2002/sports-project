<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Hidden Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <h2 class="card-inside-title">Email Address</h2>
        <div class="form-group">
            <div class="form-line">
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $request->email) }}" required autofocus placeholder="Enter your email">
            </div>
            @error('email')
                <span class="text-danger d-block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <h2 class="card-inside-title">New Password</h2>
        <div class="form-group">
            <div class="form-line">
                <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password" placeholder="Enter new password">
            </div>
            @error('password')
                <span class="text-danger d-block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <h2 class="card-inside-title">Confirm New Password</h2>
        <div class="form-group">
            <div class="form-line">
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password" placeholder="Confirm new password">
            </div>
            @error('password_confirmation')
                <span class="text-danger d-block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary m-t-10 waves-effect">
                {{ __('Reset Password') }}
            </button>
        </div>
    </form>
</x-guest-layout>
