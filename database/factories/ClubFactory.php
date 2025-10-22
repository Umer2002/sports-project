<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Club>
 */
class ClubFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'bio' => fake()->paragraph(),
            'paypal_link' => fake()->url(),
            'joining_url' => fake()->url(),
            'social_links' => [
                'facebook' => fake()->url(),
                'twitter' => fake()->url(),
                'instagram' => fake()->url(),
            ],
            'invite_token' => fake()->uuid(),
            'invites_count' => fake()->numberBetween(0, 100),
            'is_registered' => true,
            'user_id' => User::factory(),
            'registration_date' => now(),
            'initial_player_count' => null,
            'final_player_count' => null,
            'estimated_payout' => null,
            'final_payout' => null,
            'payout_calculated_at' => null,
            'payout_paid_at' => null,
            'payout_status' => 'pending',
        ];
    }
}
