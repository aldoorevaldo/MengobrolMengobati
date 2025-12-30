<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Booking Management - Admin</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f6fbfb;
      font-family: Poppins, system-ui, -apple-system, "Segoe UI", Roboto;
      color: #24333a;
    }

    .page-header {
      margin-bottom: 32px;
    }

    .booking-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 28px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.06);
      height: 100%;
      transition: transform .25s ease, box-shadow .25s ease;
    }

    .booking-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 18px 40px rgba(0,0,0,0.10);
    }

    .booking-icon {
      width: 56px;
      height: 56px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
    }

    .icon-blue {
      background: #e8f1ff;
      color: #0d6efd;
    }

    .icon-green {
      background: #e6f6ee;
      color: #198754;
    }

    .booking-card h4 {
      font-weight: 700;
      margin-bottom: 8px;
    }

    .booking-card p {
      color: #6b7c86;
      font-size: 14px;
      margin-bottom: 18px;
    }

    .tips-box {
      margin-top: 40px;
      background: #ffffff;
      border-radius: 12px;
      padding: 16px 20px;
      border-left: 4px solid #0d6efd;
      box-shadow: 0 6px 18px rgba(0,0,0,0.04);
    }
  </style>
</head>
<body>

<div class="container py-5">

  <!-- HEADER -->
  <div class="page-header d-flex justify-content-between align-items-start">
    <div>
      <h2 class="fw-bold mb-1">Booking Management</h2>
      <p class="text-muted mb-0">
        Select booking type to view and manage user data
      </p>
    </div>

    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
      ← Back to Dashboard
    </a>
  </div>

  <!-- CARDS -->
  <div class="row g-4 mt-3">

    <!-- Psikiater -->
    <div class="col-md-6">
      <a href="{{ route('admin.bookings.psikiater') }}" class="text-decoration-none text-dark">
        <div class="booking-card">
          <div class="booking-icon icon-blue">
            👨‍⚕️
          </div>
          <h4>Booking Psikiater</h4>
          <p>
            Manage schedule and status of psikiater consultation bookings.
          </p>
          <span class="btn btn-primary btn-sm">View Booking</span>
        </div>
      </a>
    </div>

    <!-- Psikolog -->
    <div class="col-md-6">
      <a href="{{ route('admin.bookings.psikolog') }}" class="text-decoration-none text-dark">
        <div class="booking-card">
          <div class="booking-icon icon-green">
            🧠
          </div>
          <h4>Booking Psikolog</h4>
          <p>
            Manage counseling sessions and communication with the psikolog.
          </p>
          <span class="btn btn-primary btn-sm">View Booking</span>
        </div>
      </a>
    </div>

  </div>
</div>

</body>
</html>
