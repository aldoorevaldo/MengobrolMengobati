<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard - MengobrolMengobati</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <!-- Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom Admin CSS -->
  @vite('resources/css/admin.css')
</head>
<body>

  {{-- TOPBAR --}}
  <header class="admin-topbar">
    <div class="container d-flex align-items-center justify-content-between">
      <div class="brand">
        <span class="fw-bold">MengobrolMengobati</span>
        <small class="ms-2 opacity-75">Admin Panel</small>
      </div>

      <div class="d-flex align-items-center gap-3">
        @if(auth()->check())
          <span class="small text-light">
            Login as <strong>{{ auth()->user()->name }}</strong>
          </span>
        @endif

        <form action="{{ route('logout') }}" method="POST" class="m-0">
          @csrf
          <button type="submit" class="btn btn-danger btn-sm">
            Logout
          </button>
        </form>
      </div>
    </div>
  </header>

  {{-- MAIN CONTENT --}}
  <main class="admin-content">
    <div class="container">

      {{-- PAGE HEADER --}}
      <div class="page-header mb-4">
        <h3 class="mb-1 fw-bold">Dashboard</h3>
        <p class="text-muted mb-0">
          System summary and quick access to management. Click on any statistic card to view more details.
        </p>
      </div>

      {{-- STATISTICS --}}
      <div class="row g-4 mb-4">

        <div class="col-md-4">
          <a href="{{ url('/admin/users') }}" class="stat-card">
            <div class="stat-title">Total Users</div>
            <div class="stat-value">{{ $counts['users'] ?? 0 }}</div>
          </a>
        </div>

        <div class="col-md-4">
          <a href="{{ url('/admin/psikiater') }}" class="stat-card">
            <div class="stat-title">Total Psikiater</div>
            <div class="stat-value">{{ $counts['psikiater'] ?? 0 }}</div>
          </a>
        </div>

        <div class="col-md-4">
          <a href="{{ url('/admin/psikolog') }}" class="stat-card">
            <div class="stat-title">Total Psikolog</div>
            <div class="stat-value">{{ $counts['psikolog'] ?? 0 }}</div>
          </a>
        </div>

      </div>

      {{-- SECOND ROW --}}
      <div class="row g-4 mb-5">

        <div class="col-md-6">
            <a href="{{ route('admin.therapy.groups') }}" class="stat-card">
                <div class="stat-title">Total Therapy Groups</div>
                <div class="stat-value">{{ $counts['therapy_group'] ?? 0 }}</div>
            </a>
        </div>

        <div class="col-md-6">
          <a href="{{ url('/admin/bookings') }}" class="stat-card text-center">
            <div class="stat-title">Total Booking</div>
            <div class="stat-value">{{ $bookings->count() ?? 0 }}</div>
          </a>
        </div>

      </div>

    </div>
  </main>

  {{-- FOOTER --}}
<footer class="site-footer">
  <div class="container text-center">
    <p>© {{ date('Y') }} MengobrolMengobati. All rights reserved.</p>
  </div>
</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
