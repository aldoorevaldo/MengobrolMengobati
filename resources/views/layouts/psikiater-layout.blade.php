<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Dashboard Psikiater')</title>

  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  @vite('resources/css/psikiater-dashboard.css')
</head>

<body>

{{-- TOPBAR --}}
<header class="psy-topbar">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="psy-brand">
      <span class="fw-bold">MengobrolMengobati</span>
      <small class="ms-2 opacity-75">Psikiater Panel</small>
    </div>

    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('psikiater.profile') }}" class="psy-avatar">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="#2c4b3f"
          stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          viewBox="0 0 24 24">
          <circle cx="12" cy="7" r="4"/>
          <path d="M5.5 21a6.5 6.5 0 0 1 13 0"/>
        </svg>
      </a>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-danger btn-sm">Logout</button>
      </form>
    </div>
  </div>
</header>

{{-- CONTENT --}}
<main class="container py-4">
  @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
