<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Booking Psikiater - Admin</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- Optional: admin standalone style --}}
  @vite('resources/css/admin-standalone.css')
</head>
<body class="bg-light">

<div class="container py-4">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-1">List of Booking Psikiater</h3>
      <p class="text-muted small mb-0">
        Manage schedule and status of psikiater consultation bookings.
      </p>
    </div>

    <a href="{{ route('admin.bookings') }}" class="btn btn-outline-secondary">
      ← Back to Main
    </a>
  </div>

  {{-- Table Card --}}
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th class="px-3">#</th>
              <th>User</th>
              <th>Psikiater</th>
              <th>Service</th>
              <th>Schedule</th>
              <th>Status</th>
            </tr>
          </thead>

          <tbody>
            @forelse($bookings as $b)
              <tr>
                <td class="px-3 fw-semibold">{{ $b->id }}</td>

                <td>
                  <div class="fw-medium">{{ $b->user_name }}</div>
                </td>

                <td>
                  <div class="fw-medium">{{ $b->psikiater_name }}</div>
                </td>

                <td>
                  <span class="text-muted">{{ $b->service }}</span>
                </td>

                <td>
                  <span class="text-muted">
                    {{ \Carbon\Carbon::parse($b->scheduled_at)->translatedFormat('d M Y, H:i') }}
                  </span>
                </td>

                <td>
                  @php
                    $status = strtolower($b->status);
                    $badge = match($status) {
                      'pending' => 'bg-warning text-dark',
                      'confirmed' => 'bg-success',
                      'rejected' => 'bg-danger',
                      default => 'bg-secondary'
                    };
                  @endphp

                  <span class="badge {{ $badge }}">
                    {{ ucfirst($status) }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                  No booking data available for psikiater.
                </td>
              </tr>
            @endforelse
          </tbody>

        </table>
      </div>

    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
