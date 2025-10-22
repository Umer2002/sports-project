<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class CheckPayment
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('player')) {
                $payment = Payment::where('player_id', $user->player->id)->first();
                if (!$payment) {
                    return redirect()->route('my-account')->with('error', 'Please complete your payment to access the dashboard.');
                }
            }
        }

        return $next($request);
    }
}
