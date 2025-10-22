<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'donor_id' => User::factory(),
            'club_id' => Club::factory(),
            'stripe_session_id' => 'cs_test_' . $this->faker->regexify('[A-Za-z0-9]{24}'),
            'amount' => $this->faker->numberBetween(500, 10000), // $5 to $100 in cents
            'currency' => 'usd',
            'donor_name' => $this->faker->name(),
            'donor_email' => $this->faker->email(),
            'message' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'cancelled']),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the donation is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Indicate that the donation is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'completed_at' => null,
        ]);
    }
}
