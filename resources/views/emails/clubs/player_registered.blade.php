<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Player Registered</title>
</head>

<body style="font-family: Arial, sans-serif; color: #1a1a1a;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="600" cellpadding="24" cellspacing="0" role="presentation"
                    style="background: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb;">
                    <tr>
                        <td>
                            <p style="font-size: 16px; margin: 0 0 16px;">Hello {{ $club->name }} Team,</p>

                            <p style="font-size: 15px; margin: 0 0 16px;">
                                {{ $player->name }} just created a Play2Earn player profile
                                @if ($isNewClub)
                                    and added your club while signing up.
                                @else
                                    and selected your club while signing up.
                                @endif
                            </p>

                            <p style="font-size: 15px; margin: 0 0 16px;">
                                @if ($isNewClub)
                                    We've set up a club listing so you can claim it and start earning alongside your
                                    athletes.
                                @else
                                    They would love for you to connect with them on Play2Earn.
                                @endif
                            </p>

                            <div style="background: #f4f6f8; border-radius: 10px; padding: 16px; margin-bottom: 16px;">
                                <p style="font-weight: bold; margin: 0 0 8px;">Player Details</p>
                                <p style="margin: 0 0 4px;">Name: {{ $player->name }}</p>
                                <p style="margin: 0 0 4px;">Sport: {{ optional($player->sport)->name ?? '—' }}</p>
                                <p style="margin: 0;">Email: {{ $player->email }}</p>
                            </div>

                            <p style="font-size: 15px; margin: 0 0 16px;">
                                Sign in or complete your club profile to welcome them and unlock your earning tools:
                                <a href="{{ route('register.club') }}" style="color: #0077cc;">Register or Sign In</a>
                            </p>

                            <p style="font-size: 14px; margin: 0; color: #6b7280;">— The Play2Earn Team</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
