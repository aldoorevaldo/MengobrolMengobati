<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title','Profile Psikiater')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- Font & Bootstrap --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Custom CSS --}}
    @vite('resources/css/psikiater-profile.css')
</head>
<body class="psikiater-profile-page">

<header class="profile-topbar">
    <div class="container d-flex justify-content-between align-items-center">
        <strong class="brand">MengobrolMengobati</strong>

        <a href="{{ route('psikiater.dashboard') }}" class="btn btn-sm btn-outline-light">
            Dashboard
        </a>
    </div>
</header>

<main class="profile-wrapper">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
