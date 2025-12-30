<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MengobrolMengobati</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite('resources/css/auth.css')
</head>

<body class="auth-page">

<div class="auth-wrapper">

    {{-- LEFT SIDE --}}
    <div class="auth-left">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-img">
    </div>

    {{-- RIGHT SIDE --}}
    <div class="auth-right">
        <div class="auth-card">

            <h3>Login</h3>
            <p class="subtitle">Log in to your account</p>

            @if ($errors->any())
                <div class="alert alert-danger small">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>

                <button class="btn btn-auth w-100">Login</button>
            </form>

            <div class="auth-links">
                <a href="{{ url('/register') }}">Don't have an account? Register</a>
                <a href="{{ url('/') }}">← Back to Home</a>
            </div>

        </div>
    </div>

</div>

</body>
</html>
