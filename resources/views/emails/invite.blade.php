<!DOCTYPE html>
<html>
<head>
    <title>You're Invited!</title>
</head>
<body>
    <h2>Hello!</h2>

    <p>Hello,</p>
    <p>{{ $user->name ?? $user->email }} has invited you to join Play2Earn Sports.</p>
    @if(!empty($meta['custom_message']))
        <p>{{ $meta['custom_message'] }}</p>
    @else
        <p>Join a community of clubs, players, and referees who compete, earn rewards, and share their highlights.</p>
    @endif

    <p><a href="{{ $inviteLink }}">Click here to get started</a></p>
    @if(!empty($meta['referral_code']))
        <p><strong>Referral Code:</strong> {{ $meta['referral_code'] }}</p>
    @endif

    <p>Thanks,<br>Play2Earn Sports Team</p>
</body>
</html>
