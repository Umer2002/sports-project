<?php

namespace Tests\Unit;

use App\Models\EventResponse;
use PHPUnit\Framework\TestCase;

class EventResponseTest extends TestCase
{
    public function test_create_event_response(): void
    {
        $response = new EventResponse([
            'event_id' => 1,
            'player_id' => 1,
            'status' => 'yes'
        ]);

        $this->assertSame('yes', $response->status);
    }
}
