<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $userRoles = $request->user()->roles->pluck('name');

        if ($userRoles->intersect($roles)->isEmpty()) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
