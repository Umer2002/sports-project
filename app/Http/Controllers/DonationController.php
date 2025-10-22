<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Donation;
use App\Models\Payment;
use App\Mail\DonationConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class DonationController extends Controller
{
    public function createCheckout(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:club,player',
            'recipient_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'donor_name' => 'nullable|string|max:255',
            'donor_email' => 'nullable|email|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        $amount = (int) ($request->amount * 100); // Convert to cents

        if ($request->recipient_type === 'club') {
            $club = Club::findOrFail($request->recipient_id);
            $recipientName = $club->name;
            $clubId = $club->id;
            $playerId = null;
        } else {
            $player = \App\Models\Player::findOrFail($request->recipient_id);
            $recipientName = $player->name;
            $clubId = $player->club_id;
            $playerId = $player->id;
        }

        // Create donation record
        $donation = Donation::create([
            'donor_id' => auth()->id(),
            'club_id' => $clubId,
            'player_id' => $playerId,
            'amount' => $amount,
            'currency' => 'usd',
            'donor_name' => $request->donor_name,
            'donor_email' => $request->donor_email,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Set up Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => "Donation to {$recipientName}",
                        'description' => $request->message ?: "Supporting {$recipientName}",
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('donation.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('donation.cancel') . '?donation_id=' . $donation->id,
            'customer_email' => $request->donor_email,
            'metadata' => [
                'donation_id' => $donation->id,
                'club_id' => $clubId,
                'player_id' => $playerId,
            ],
        ]);

        // Update donation with session ID
        $donation->update(['stripe_session_id' => $session->id]);

        return response()->json([
            'id' => $session->id,
            'url' => $session->url,
        ]);
    }

    public function success(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        
        try {
            $session = StripeSession::retrieve($request->get('session_id'));
            
            if ($session->payment_status === 'paid') {
                $donation = Donation::where('stripe_session_id', $session->id)->first();
                
                if ($donation) {
                    $donation->markAsCompleted();
                    
                    // Create payment record for tracking
                    Payment::create([
                        'club_id' => $donation->club_id,
                        'stripe_session_id' => $session->id,
                        'amount' => $donation->amount,
                        'currency' => $donation->currency,
                        'type' => 'donation',
                        'notes' => $donation->message ?: "Donation from {$donation->donor_name}",
                    ]);
                    
                    // Send email confirmation
                    if ($donation->donor_email) {
                        try {
                            Mail::to($donation->donor_email)->send(new DonationConfirmation($donation));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send donation confirmation email: ' . $e->getMessage());
                        }
                    }
                    
                    return view('donations.success', [
                        'donation' => $donation,
                        'club' => $donation->club,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log error
            \Log::error('Donation success error: ' . $e->getMessage());
        }

        return redirect()->route('home')->with('error', 'There was an issue processing your donation.');
    }

    public function cancel(Request $request)
    {
        $donationId = $request->get('donation_id');
        $donation = Donation::find($donationId);
        
        if ($donation) {
            $donation->update(['status' => 'cancelled']);
        }

        return redirect()->route('home')->with('info', 'Your donation was cancelled.');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            
            $donation = Donation::where('stripe_session_id', $session->id)->first();
            
            if ($donation && $donation->status === 'pending') {
                $donation->markAsCompleted();
            }
        }

        return response()->json(['status' => 'success']);
    }
}
