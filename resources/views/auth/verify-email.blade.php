<x-guest-layout>
    <div class="alert alert-info text-sm">
        {{ __('Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed you. If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success font-weight-bold text-sm">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary waves-effect">
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-danger">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
