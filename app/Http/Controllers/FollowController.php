<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function follow(User $user): RedirectResponse
    {
        Auth::user()->following()->syncWithoutDetaching([$user->id]);
        return back();
    }

    public function unfollow(User $user): RedirectResponse
    {
        Auth::user()->following()->detach($user->id);
        return back();
    }
}
