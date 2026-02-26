@component('mail::message')
# ✅ Appointment Confirmed

Hello **{{ $recipientName }}**,

Your booking has been saved successfully. Below are your appointment details:

@component('mail::panel')
- **Reference #:** {{ $bookingToken }}  
- **Doctor:** {{ $doctorName }}  
- **Patient:** {{ $patientName }}  
- **Date:** {{ $slotDate }}  
- **Time:** {{ $slotTime }}
@endcomponent

@component('mail::button', ['url' => config('app.url')])
View in Portal
@endcomponent

If you have questions or need to reschedule, simply reply to this e-mail.

Thanks&nbsp;for choosing **{{ config('app.name') }}**!  
{{ config('app.name') }} Team
@endcomponent
