@component('mail::message')
# My First Email
  
The body of your message. 
   
@component('mail::button', ['url' => '#'])
Button
@endcomponent
   
Thanks,<br>
{{ config('app.name') }}
@endcomponent