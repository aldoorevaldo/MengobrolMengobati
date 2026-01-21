@component('mail::message')
# Booking Status Changes

Hello {{ $booking->user->name ?? '-' }},

Your Booking with
**{{ $booking->psikiater->name ?? $booking->psikolog->name ?? '-' }}**
at **{{ $booking->scheduled_at ? \Carbon\Carbon::parse($booking->scheduled_at)->translatedFormat('d M Y, H:i') : '-' }}**
has changed status to **{{ ucfirst($booking->status) }}**.

@if(isset($booking->provider_notes) && $booking->provider_notes)
**Provider Notes:**
{{ $booking->provider_notes }}
@endif

Thank You,<br>
{{ config('app.name') }}
@endcomponent
