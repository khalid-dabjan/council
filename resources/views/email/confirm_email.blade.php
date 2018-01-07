@component('mail::message')
# One Last Step

We need you to confirm your email

@component('mail::button', ['url' => url('/register/confirm?token='.$user->confirmation_token)])
Confirm
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
