{{-- resources/views/bookings/email-action-result.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $title ?? 'Booking' }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f5f7f8; padding-top:60px; }
    .card { max-width:720px; margin: auto; }
  </style>
</head>
<body>
  <div class="container">
    <div class="card shadow-sm">
      <div class="card-body">
        <h3 class="card-title">{{ $title }}</h3>
        <p class="text-muted">{{ $message }}</p>

        @if($booking)
        <hr>
        <p><strong>Detail Booking:</strong></p>
        <ul>
          <li>Pasien: {{ $booking->user->name ?? '-' }}</li>
          <li>Service: {{ $booking->service ?? '-' }}</li>
          <li>Jadwal: {{ \Carbon\Carbon::parse($booking->scheduled_at)->translatedFormat('d M Y, H:i') }}</li>
          <li>Status sekarang: <strong>{{ ucfirst($booking->status) }}</strong></li>
        </ul>
        @endif

        <a href="{{ route('home') }}" class="btn btn-secondary">Kembali ke Home</a>
      </div>
    </div>
  </div>
</body>
</html>
