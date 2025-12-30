<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <title>@yield('title','Booking - MengobrolMengobati')</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body{
      min-height:100vh;
      font-family: "Poppins", system-ui, -apple-system, "Segoe UI", Roboto, Arial;
    }

    .booking-wrapper{
      max-width: 760px;
      margin: 0 auto;
    }

    .booking-card{
      background:#ffffff;
      border-radius:18px;
      box-shadow:0 20px 50px rgba(0,0,0,.15);
      padding:32px;
      position:relative;
    }

    .btn-back{
      position:absolute;
      top:20px;
      right:20px;
      font-size:14px;
      text-decoration:none;
      color:#ffffff;
      background:#0b3b36;
      padding:8px 18px;
      border-radius:999px;
      font-weight:600;
    }

    .btn-back:hover{
      background: #0b3b36;
      color: #ffffff;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .booking-title{
      font-size:22px;
      font-weight:700;
      color:#0b3b36;
      margin-bottom:4px;
    }

    .booking-subtitle{
      font-size:14px;
      color:#6b7f7b;
      margin-bottom:20px;
    }

    .form-label{
      font-weight:600;
      font-size:14px;
      color:#1f2d2b;
    }

    .form-control,
    .form-select{
      border-radius:10px;
      padding:10px 14px;
    }

    .btn-primary{
      background:#0b3b36;
      border:none;
      border-radius:10px;
      padding:10px 18px;
      font-weight:600;
    }

    .btn-primary:hover{
      background:#09403b;
    }

    .btn-secondary{
      background:#0b3b36;
      border:none;
      border-radius:10px;
      padding:10px 18px;
      font-weight:600;
    }

    .btn-secondary:hover{
      background:#09403b;
    }
  </style>

  @yield('head')
</head>
<body>

  <main class="py-5">
    <div class="container">
      @yield('content')
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  @yield('scripts')
</body>
</html>
