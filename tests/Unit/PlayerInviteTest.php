<?php

namespace Tests\Unit;

use App\Models\Invite;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class PlayerInviteTest extends TestCase
{
    public function test_invite_creation(): void
    {
        $invite = new Invite([
            'sender_id' => 1,
            'receiver_email' => '',
            'receiver_id' => null,
            'type' => 'club',
            'reference_id' => 1,
            'token' => (string) Str::uuid(),
        ]);

        $this->assertSame('club', $invite->type);
        $this->assertNotEmpty($invite->token);
    }
}
