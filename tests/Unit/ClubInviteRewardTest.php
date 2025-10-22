<?php

namespace Tests\Unit;

use App\Models\Club;
use Tests\TestCase;

class ClubInviteRewardTest extends TestCase
{
    public function test_reward_calculation()
    {
        $club = new Club(['invites_count' => 0]);
        $this->assertSame(0, $club->calculateInviteReward());

        $club->invites_count = 50;
        $this->assertSame(500, $club->calculateInviteReward());

        $club->invites_count = 70;
        $this->assertSame(700, $club->calculateInviteReward());

        $club->invites_count = 99;
        $this->assertSame(1000, $club->calculateInviteReward());

        $club->invites_count = 150;
        $this->assertSame(1000, $club->calculateInviteReward());

        $club->invites_count = 250;
        $this->assertSame(2000, $club->calculateInviteReward());
    }
}
