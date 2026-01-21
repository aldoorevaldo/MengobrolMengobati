@component('mail::message')
# New Booking

@php
    // tentukan provider display name & tipe
    $providerName = null;
    $providerEmail = null;
    $providerType = $booking->type ?? 'psikiater';

    if ($providerType === 'psikolog') {
        // kalau ada relasi model Psikolog
        if (isset($booking->psikolog) && $booking->psikolog) {
            $providerName = $booking->psikolog->name ?? ($booking->psikolog->title ?? 'Psikolog');
            // bila psikolog ter-relasi ke users
            if (isset($booking->psikolog->user) && $booking->psikolog->user) {
                $providerEmail = $booking->psikolog->user->email ?? null;
            } elseif (isset($booking->psikolog->email)) {
                $providerEmail = $booking->psikolog->email;
            }
        } else {
            $providerName = 'Psikolog';
        }
    } else {
        if (isset($booking->psikiater) && $booking->psikiater) {
            $providerName = $booking->psikiater->name ?? 'Psikiater';
            if (isset($booking->psikiater->user) && $booking->psikiater->user) {
                $providerEmail = $booking->psikiater->user->email ?? null;
            } elseif (isset($booking->psikiater->email)) {
                $providerEmail = $booking->psikiater->email;
            }
        } else {
            $providerName = 'Psikiater';
        }
    }
@endphp

Hello **{{ $providerName }}**,

A patient has made a booking for you. Please check the app to view the details and to accept or decline this request.

— Booking Details:
- **Patient:** {{ $booking->user->name ?? '-' }}
- **Email:** {{ $booking->user->email ?? '-' }}
- **Service:** {{ $booking->service ?? '-' }}
- **Scheduled At:** {{ $booking->scheduled_at ? \Carbon\Carbon::parse($booking->scheduled_at)->translatedFormat('d M Y, H:i') : '-' }}

@if(isset($booking->notes) && $booking->notes)
- **Patient Notes:**
  {{ $booking->notes }}
@endif

Thank You,<br>
{{ config('app.name') }}
@endcomponent
