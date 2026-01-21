{{-- resources/views/layouts/psikolog-chat-layout.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','Chat Psikolog')</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- @vite('resources/css/chat.css') --}}
   @vite('resources/css/psikolog-chat.css')

  @stack('head')
</head>
<body class="chat-page">

  <main class="container-chat">
    @yield('content')
  </main>

  {{-- app js --}}
  <script src="{{ asset('js/app.js') }}"></script>
  @stack('scripts')
</body>
</html>
