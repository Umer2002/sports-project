<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\PlayerReward;
use App\Models\Reward;
use App\Models\User;

class PaymentObserver
{
    /**
     * Handle the payment "created" event.
     */
    public function created(Payment $payment): void
    {
        if ($payment->type !== 'player') {
            return;
        }

        $user = optional($payment->player)->user;

        if (! $user && $payment->user_id) {
            $user = User::find($payment->user_id);
        }

        if (! $user || ! $user->hasRole('player')) {
            return;
        }

        $reward = Reward::where('name', 'New Recruit')->first();

        if (! $reward) {
            return;
        }

        PlayerReward::firstOrCreate(
            [
                'user_id'   => $user->id,
                'reward_id' => $reward->id,
            ],
            [
                'issued_by' => null,
                'status'    => 'active',
            ],
        );
    }
}
