<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MengobrolMengobati')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f7f9fa;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>

    <div class="py-4">
        @yield('content')
    </div>

</body>
</html>
