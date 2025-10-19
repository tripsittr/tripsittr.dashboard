@component('mail::message')
# You're invited to join {{ $team->name }}

@if($inviter)
**{{ $inviter->name }}** has invited you to join the team **{{ $team->name }}**.
@else
You have been invited to join the team **{{ $team->name }}**.
@endif

@isset($invitation->role)
**Role:** {{ $invitation->role }}
@endisset

Click a button below to continue:

@component('mail::button', ['url' => $acceptUrl])
View Invitation
@endcomponent

@component('mail::button', ['url' => $registerUrl, 'color' => 'success'])
Create Account & Accept
@endcomponent

@if($expires)
_This invitation expires {{ $expires->diffForHumans() }} (on {{ $expires->toDayDateTimeString() }})._
@endif

If you did not expect this, you can ignore this email.

Thanks,
{{ config('app.name') }}
@endcomponent