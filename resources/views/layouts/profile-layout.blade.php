<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title', 'Profile')</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  @vite (['resources/css/profile.css'])

  <style>
    body { background:#f6fbfb; font-family: Poppins, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; color:#24333a; }
    .profile-card { border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.05); background:#fff; padding:28px; }
    .profile-avatar {
      width:88px; height:88px; border-radius:50%;
      background:#e8f4f2; display:flex; align-items:center; justify-content:center;
      font-size:34px; color:#0b3b36;
    }
    .btn-ghost { background:transparent; border:1px solid #d7e5e3; color:#445; }
    .btn-ghost:hover { background:#f1f8f7; }
    .back-btn { margin-top:14px; }
    .booking-card { border-radius:8px; background:#fff; box-shadow:0 6px 18px rgba(0,0,0,0.03); padding:18px; }
    .badge-status { padding:6px 10px; border-radius:12px; font-size:13px; }
    .badge-pending { background:#fff3cd; color:#856404; border:1px solid #ffe8a8; }
    .badge-confirmed { background:#e6ffef; color:#0f7a4b; border:1px solid #bff0cf; }
    .badge-rejected { background:#ffecec; color:#7a1a1a; border:1px solid #ffcfcf; }
    /* responsive spacing */
    @media (min-width: 992px) {
      .profile-wrap { display:flex; gap:28px; align-items:flex-start; }
      .left-col { width:320px; }
      .right-col { flex:1; }
    }
  </style>

  @stack('head')
</head>
<body>

  <main class="py-5">
    <div class="container">
      @yield('content')
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
