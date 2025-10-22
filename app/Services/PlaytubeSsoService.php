<?php

namespace App\Services;

use App\Models\User;

class PlaytubeSsoService
{
    public function makeToken(User $user, int $ttl = 300): string
    {
        $payload = [
            'sub'   => $user->id,
            'email' => $user->email,
            'name'  => $user->name,
            'exp'   => time() + $ttl,
        ];

        $json   = json_encode($payload);
        $body   = rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
        $secret = config('services.playtube.secret');
        $sig    = hash_hmac('sha256', $body, $secret, true);
        $sigB64 = rtrim(strtr(base64_encode($sig), '+/', '-_'), '=');

        return $body . '.' . $sigB64;
    }
}
