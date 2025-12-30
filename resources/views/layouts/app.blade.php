{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'MengobrolMengobati')</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  @vite(['resources/css/app.css','resources/css/user-dropdown.css'])
</head>
<body class="@yield('body-class') d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-custom navbar-blur fixed-top" id="mainNavbar">
  <div class="container d-flex align-items-center justify-content-between">

    {{-- BRAND --}}
    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" class="navbar-logo">
      <span class="brand-text">MengobrolMengobati</span>
    </a>

    {{-- TOGGLER --}}
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    {{-- MENU --}}
    <div class="collapse navbar-collapse justify-content-center" id="navMenu">
      <ul class="navbar-nav gap-lg-4 mt-3 mt-lg-0">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
            Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">
            Services
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->is('about') ? 'active' : '' }}" href="{{ url('/about') }}">
            About
          </a>
        </li>
      </ul>
    </div>

  </div>
</nav>

  @auth
    @unless(request()->routeIs('profile.show'))
      @include('shared.user-dropdown')
    @endunless
  @endauth

  {{-- main content --}}
  <main>
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  @yield('scripts')

<script>
  const navbar = document.getElementById('mainNavbar');

  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      navbar.classList.remove('navbar-blur');
      navbar.classList.add('navbar-solid');
    } else {
      navbar.classList.remove('navbar-solid');
      navbar.classList.add('navbar-blur');
    }
  });
</script>
</body>
</html>
