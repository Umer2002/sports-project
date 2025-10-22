<?php

namespace Tests\Unit;

use App\Models\TournamentRegistration;
use PHPUnit\Framework\TestCase;

class TournamentRegistrationTest extends TestCase
{
    public function test_calculate_amount_due_for_per_team(): void
    {
        $registration = new TournamentRegistration([
            'joining_type' => 'per_team',
            'joining_fee' => 125.50,
            'team_quantity' => 3,
        ]);

        $this->assertSame(376.5, $registration->calculateAmountDue());
        $this->assertSame(251.0, $registration->calculateAmountDue(2));
    }

    public function test_calculate_amount_due_for_per_club(): void
    {
        $registration = new TournamentRegistration([
            'joining_type' => 'per_club',
            'joining_fee' => 500,
            'team_quantity' => 1,
        ]);

        $this->assertSame(500.0, $registration->calculateAmountDue());
        $this->assertSame(500.0, $registration->calculateAmountDue(4));
    }
}
