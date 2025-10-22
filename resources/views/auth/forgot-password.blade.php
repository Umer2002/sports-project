<x-guest-layout>
    <div class="text-center m-b-20">
        <p class="text-muted">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <h2 class="card-inside-title">Password</h2>
        <div class="form-group">
            <div class="form-line">
                <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password" placeholder="Enter your password">
            </div>
            @error('password')
                <span class="text-danger d-block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary m-t-10 waves-effect">
                {{ __('Confirm') }}
            </button>
        </div>
    </form>
</x-guest-layout>
