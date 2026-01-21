{{-- resources/views/bookings/confirm-action.blade.php --}}
@extends('layouts.booking-layout')

@section('title', $action === 'approve' ? 'Approve Booking' : 'Reject Booking')

@section('content')
<div class="card p-4">
    <h4>{{ $action === 'approve' ? 'Approve Booking' : 'Reject Booking' }}</h4>
    {{-- Flash messages (success/error) --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <p>You will
        <strong>{{ $action === 'approve' ? 'approve' : 'reject' }}</strong>
        booking berikut:
    </p>

    <ul>
        <li><strong>Pasien:</strong> {{ optional($booking->user)->name }} ({{ optional($booking->user)->email }})</li>
        <li><strong>Jadwal:</strong> {{ \Carbon\Carbon::parse($booking->scheduled_at)->translatedFormat('d M Y, H:i') }}</li>
        <li><strong>Service:</strong> {{ $booking->service ?? '-' }}</li>
        <li><strong>Catatan pasien:</strong><br>{{ $booking->notes ?? '-' }}</li>
    </ul>

    @php
        if (isset($booking->type) && $booking->type === 'psikolog') {
            $approveRoute = route('psikolog.booking.approve', $booking->id);
            $rejectRoute  = route('psikolog.booking.reject', $booking->id);
            $dashboardRoute = route('psikolog.dashboard');
        } else {
            $approveRoute = route('psikiater.booking.approve', $booking->id);
            $rejectRoute  = route('psikiater.booking.reject', $booking->id);
            $dashboardRoute = route('psikiater.dashboard');
        }
        $selectedRoute = $action === 'approve' ? $approveRoute : $rejectRoute;
    @endphp

    @if($booking->status !== 'pending')
        @unless(session('success'))
            <div class="alert alert-warning">
                Booking cannot be processed because status is not pending.
            </div>
        @endunless

        <a href="{{ $dashboardRoute }}" class="btn btn-secondary">Back to Dashboard</a>
    @else
        <form id="confirm-form" method="POST" action="{{ $selectedRoute }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Notes (optional)</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>

            <div class="d-flex gap-2">
                <button id="confirm-btn" class="btn {{ $action === 'approve' ? 'btn-success' : 'btn-danger' }}">
                    {{ $action === 'approve' ? 'Approve' : 'Reject' }}
                </button>
                <a href="{{ $dashboardRoute }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    @endif
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('confirm-form');
  if (!form) return;
  const btn = document.getElementById('confirm-btn');
  form.addEventListener('submit', function () {
    if (btn) {
      btn.disabled = true;
      btn.innerText = 'Processing...';
    }
  });
});
</script>
@endsection

@endsection
