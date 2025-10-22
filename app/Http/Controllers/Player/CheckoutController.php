<?php

namespace App\Http\Controllers\Player;

use App\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Invite;

class CheckoutController extends Controller
{
    public function createCheckout(Request $request)
    {
        $user = $request->user();

        // Prevent duplicate payment
        if ($user->player && $user->player->has_paid) {
            return response()->json([
                'error' => 'You have already completed your payment.'
            ], 400);
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Player Registration Fee',
                    ],
                    'unit_amount' => 1000, // $10.00 in cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('player.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('player.payment.cancel'),
            'customer_email' => $user->email,
        ]);

        return response()->json(['id' => $session->id, 'url' => $session->url]);
    }

    public function paymentSuccess(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = StripeSession::retrieve($request->get('session_id'));

        $player = $request->user()->player;

        if (!$player) {
            $player = new \App\Models\Player();
            $player->user_id = $request->user()->id;
            $player->name = $request->user()->name;
            $player->email = $request->user()->email;
            $player->save();
        }

        Payment::create([
            'player_id' => $player->id,
            'club_id' => $player->club_id,
            'stripe_session_id' => $session->id,
            'amount' => $session->amount_total,
            'currency' => $session->currency,
            'type' => 'player',
        ]);

        Invite::markAcceptedForUser($request->user());

        return redirect()->route('player.dashboard')->with('success', 'Payment received! Your invitations have been marked as accepted.');
    }

    public function paymentCancel()
    {
        return view('frontend.payment_cancel');
    }
}
