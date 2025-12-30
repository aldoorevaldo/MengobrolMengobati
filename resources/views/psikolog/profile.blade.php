{{-- resources/views/psikolog/profile.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profile Psikolog - {{ $psikolog->name }}</title>

  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  @vite('resources/css/psikolog-profile.css')
</head>

<body class="profile-page">

<div class="container py-5">

  <div class="profile-card">

    {{-- HEADER --}}
    <div class="profile-header">

      {{-- FOTO --}}
      <div class="profile-avatar">
        @if($psikolog->photo)
          <img src="{{ asset('storage/'.$psikolog->photo) }}" alt="{{ $psikolog->name }}">
        @else
          <div class="avatar-initial">
            {{ strtoupper(substr($psikolog->name ?? 'U',0,1)) }}
          </div>
        @endif
      </div>

      {{-- INFO --}}
      <div class="profile-info">
        <h1>Profile Psikolog</h1>
      </div>

      {{-- ACTION --}}
      <div class="profile-actions">
        <a href="{{ route('psikolog.profile.edit') }}" class="btn-edit btn-sm">
          Edit Profile
        </a>
        <a href="{{ route('psikolog.dashboard') }}" class="btn-back btn-sm">
          ← Back to Dashboard
        </a>
      </div>

    </div>

    <hr>

    {{-- DETAIL --}}
    <div class="profile-details">

      <div class="detail-row">
        <span class="label">Name</span>
        <span class="value">{{ $psikolog->name }}</span>
      </div>

      <div class="detail-row">
        <span class="label">Workplace</span>
        <span class="value">{{ $psikolog->hospital ?? '-' }}</span>
      </div>

      <div class="detail-row">
        <span class="label">Working Hours</span>
        <span class="value">
          @if($psikolog->work_start || $psikolog->work_end)
            {{ \Carbon\Carbon::parse($psikolog->work_start)->format('H:i') }}
            -
            {{ \Carbon\Carbon::parse($psikolog->work_end)->format('H:i') }}
          @else
            -
          @endif
        </span>
      </div>

      <div class="detail-row">
        <span class="label">Email</span>
        <span class="value">{{ optional($psikolog->user)->email ?? '-' }}</span>
      </div>

      <div class="detail-row">
        <span class="label">Description</span>
        <span class="value">{{ $psikolog->description ?? '-' }}</span>
      </div>

    </div>

  </div>

</div>

</body>
</html>
