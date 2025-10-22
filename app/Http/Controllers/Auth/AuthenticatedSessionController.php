<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user()->load('roles');

        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->hasRole('club')) {
            return redirect()->intended(route('club-dashboard'));
        }

        if ($user->hasRole('coach')) {
            return redirect()->route('coach-dashboard');
        }

        if ($user->hasRole('volunteer') || $user->hasRole('ambassador')) {
            return redirect()->intended(route('volunteer.dashboard'));
        }

        if ($user->hasRole('referee')) {
            return redirect()->intended(route('referee.dashboard'));
        }

        if ($user->hasRole('college')) {
            return redirect()->intended(route('college.dashboard'));
        }

        // Default redirection
        return redirect()->intended(route('player.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
