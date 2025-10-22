<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Club;
use App\Models\Player;
use App\Models\PayoutPlan;
use Carbon\Carbon;

class ClubPayoutWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_club_onboarding_period()
    {
        // Create a payout plan
        PayoutPlan::create([
            'player_count' => 50,
            'payout_amount' => 500.00
        ]);

        // Create a club with registration date 1 week ago
        $club = Club::factory()->create([
            'registration_date' => now()->subWeek(),
        ]);

        // Add some players
        Player::factory()->count(30)->create(['club_id' => $club->id]);

        // Club should still be in onboarding period
        $this->assertTrue($club->isInOnboardingPeriod());
        $this->assertFalse($club->isInPayoutPeriod());
        $this->assertFalse($club->isEligibleForFinalPayout());

        // Initial player count should not be set yet
        $this->assertNull($club->initial_player_count);
    }

    public function test_club_payout_period()
    {
        // Create payout plans
        PayoutPlan::create([
            'player_count' => 30,
            'payout_amount' => 300.00
        ]);
        PayoutPlan::create([
            'player_count' => 40,
            'payout_amount' => 500.00
        ]);
        PayoutPlan::create([
            'player_count' => 50,
            'payout_amount' => 600.00
        ]);

        // Create a club with registration date 3 weeks ago
        $club = Club::factory()->create([
            'registration_date' => now()->subWeeks(3),
        ]);

        // Add some players
        Player::factory()->count(40)->create(['club_id' => $club->id]);

        // Refresh the club to ensure relationships are loaded
        $club->refresh();

        // Club should be in payout period
        $this->assertFalse($club->isInOnboardingPeriod());
        $this->assertTrue($club->isInPayoutPeriod());
        $this->assertFalse($club->isEligibleForFinalPayout());

        // Process initial player count
        $club->processInitialPlayerCount();
        $this->assertEquals(40, $club->initial_player_count);
        $this->assertEquals(500.00, $club->estimated_payout);
    }

    public function test_club_final_payout_eligibility()
    {
        // Create payout plans
        PayoutPlan::create([
            'player_count' => 30,
            'payout_amount' => 300.00
        ]);
        PayoutPlan::create([
            'player_count' => 45,
            'payout_amount' => 500.00
        ]);
        PayoutPlan::create([
            'player_count' => 50,
            'payout_amount' => 600.00
        ]);

        // Create a club with registration date 15 weeks ago (2 weeks + 90 days + 1 week)
        $club = Club::factory()->create([
            'registration_date' => now()->subWeeks(15),
        ]);

        // Add some players
        Player::factory()->count(45)->create(['club_id' => $club->id]);

        // Refresh the club to ensure relationships are loaded
        $club->refresh();

        // Club should be eligible for final payout
        $this->assertFalse($club->isInOnboardingPeriod());
        $this->assertFalse($club->isInPayoutPeriod());
        $this->assertTrue($club->isEligibleForFinalPayout());

        // Process final payout
        $club->processFinalPayout();
        $this->assertEquals(45, $club->final_player_count);
        $this->assertEquals(500.00, $club->final_payout);
        $this->assertEquals('calculated', $club->payout_status);
    }

    public function test_club_payout_processing()
    {
        // Create a payout plan
        PayoutPlan::create([
            'player_count' => 50,
            'payout_amount' => 500.00
        ]);

        // Create a club that's ready for payout
        $club = Club::factory()->create([
            'registration_date' => now()->subWeeks(15),
            'payout_status' => 'calculated',
            'final_payout' => 500.00,
        ]);

        // Mark as paid
        $club->markPayoutAsPaid();
        $this->assertEquals('paid', $club->payout_status);
        $this->assertNotNull($club->payout_paid_at);
    }

    public function test_payout_time_remaining_calculation()
    {
        $club = Club::factory()->create([
            'registration_date' => now()->subDays(10),
        ]);

        $onboardingTime = $club->getOnboardingTimeRemaining();
        $this->assertNotNull($onboardingTime);
        $this->assertArrayHasKey('weeks', $onboardingTime);
        $this->assertArrayHasKey('days', $onboardingTime);
        $this->assertArrayHasKey('hours', $onboardingTime);
        $this->assertArrayHasKey('minutes', $onboardingTime);
    }

    public function test_payout_plan_lookup()
    {
        // Create a payout plan
        $payoutPlan = PayoutPlan::create([
            'player_count' => 50,
            'payout_amount' => 500.00
        ]);

        // Debug: Check what we have
        $allPlans = PayoutPlan::all();
        $this->assertEquals(1, $allPlans->count());
        
        // Test that we can find the payout plan for 40 players (should find the 50 player plan)
        $foundPlan = PayoutPlan::where('player_count', '<=', 40)
            ->orderBy('player_count', 'desc')
            ->first();

        // This should be null because 50 is not <= 40
        $this->assertNull($foundPlan);

        // Now test with 60 players (should find the 50 player plan)
        $foundPlan = PayoutPlan::where('player_count', '<=', 60)
            ->orderBy('player_count', 'desc')
            ->first();

        $this->assertNotNull($foundPlan);
        $this->assertEquals(50, $foundPlan->player_count);
        $this->assertEquals(500.00, $foundPlan->payout_amount);
    }

    public function test_payout_plan_creation()
    {
        // Create a payout plan
        $payoutPlan = PayoutPlan::create([
            'player_count' => 50,
            'payout_amount' => 500.00
        ]);

        // Verify it was created
        $this->assertNotNull($payoutPlan->id);
        $this->assertEquals(50, $payoutPlan->player_count);
        $this->assertEquals(500.00, $payoutPlan->payout_amount);

        // Verify we can find it
        $foundPlan = PayoutPlan::find($payoutPlan->id);
        $this->assertNotNull($foundPlan);
    }
}
