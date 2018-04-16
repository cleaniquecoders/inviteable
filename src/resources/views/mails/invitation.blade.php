@component('mail::message')
# Invitation Mail

Your have been invited.

@component('mail::button', ['url' => $url])
Go to invitation
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent