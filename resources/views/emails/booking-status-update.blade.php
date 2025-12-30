@component('mail::message')
# Perubahan Status Booking

Halo {{ $booking->user->name ?? '-' }},

Booking Anda dengan 
**{{ $booking->psikiater->name ?? $booking->psikolog->name ?? '-' }}**  
pada **{{ $booking->scheduled_at ? \Carbon\Carbon::parse($booking->scheduled_at)->translatedFormat('d M Y, H:i') : '-' }}**
telah berubah status menjadi **{{ ucfirst($booking->status) }}**.

@if(isset($booking->provider_notes) && $booking->provider_notes)
**Catatan dari Provider:**  
{{ $booking->provider_notes }}
@endif

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
