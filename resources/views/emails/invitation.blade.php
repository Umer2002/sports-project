@component('mail::message')
# You're Invited!

@if(!empty($recipientName))
Hi {{ $recipientName }},

@else
Hello,

@endif
You have been invited to join the club **{{ $club->name }}**.

@if(!empty($personalMessage))
{{ $personalMessage }}

@endif
Please use the button below to accept the invitation and finish setting up your account.

@php($joinUrl = $inviteLink ?? route('home'))
@component('mail::button', ['url' => $joinUrl])
Join {{ $club->name }}
@endcomponent

If the button does not work, copy and paste this link into your browser:
<br>
{{ $joinUrl }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
