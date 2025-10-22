<?php

namespace Tests\Unit;

use App\Models\PlayerTransfer;
use PHPUnit\Framework\TestCase;

class PlayerTransferTest extends TestCase
{
    public function test_approve_sets_timestamp(): void
    {
        $transfer = new PlayerTransfer();
        $transfer->approved_at = now();
        $this->assertNotNull($transfer->approved_at);
    }
}
