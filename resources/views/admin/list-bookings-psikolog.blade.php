<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Booking Psikolog - Admin</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-0">List of Booking Psikolog</h3>
      <small class="text-muted">Manage booking counseling sessions with the psikolog</small>
    </div>
    <a href="{{ route('admin.bookings') }}" class="btn btn-outline-secondary">
      ← Back to Main
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>User</th>
            <th>Psikolog</th>
            <th>Service</th>
            <th>Schedule</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        @forelse($bookings as $b)
          <tr>
            <td>{{ $b->id }}</td>
            <td>{{ $b->user_name }}</td>
            <td>{{ $b->psikolog_name }}</td>
            <td>{{ $b->service }}</td>
            <td>
              {{ \Carbon\Carbon::parse($b->scheduled_at)->translatedFormat('d M Y, H:i') }}
            </td>
            <td>
              @php
                $status = $b->status;
                $badge = $status === 'pending' ? 'warning'
                       : ($status === 'confirmed' ? 'success' : 'danger');
              @endphp
              <span class="badge bg-{{ $badge }}">
                {{ ucfirst($status) }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">
              No booking data available for psikolog.
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

</body>
</html>
