<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Edit Profile - MengobrolMengobati</title>

  {{-- Favicon --}}
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  @vite('resources/css/profile.css')
</head>

<body>

<div class="container profile-page py-2">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

      <div class="card profile-card p-4">
        <h4 class="mb-3">Edit Profile</h4>

        {{-- SUCCESS --}}
        @if(session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
        @endif

        {{-- ERROR --}}
        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('profile.update') }}" method="POST">
          @csrf

          <div class="mb-3">
            <label class="form-label fw-semibold">Name</label>
            <input
              type="text"
              name="name"
              class="form-control"
              value="{{ old('name', $user->name) }}"
              required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input
              type="email"
              name="email"
              class="form-control"
              value="{{ old('email', $user->email) }}"
              required>
          </div>

          <hr class="my-4">

          <div class="mb-3">
            <label class="form-label fw-semibold">
              New Password
              <span class="text-muted small">(optional)</span>
            </label>
            <input
              type="password"
              name="password"
              class="form-control"
              placeholder="Leave Blank to Keep Current Password">
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold">Confirm Password</label>
            <input
              type="password"
              name="password_confirmation"
              class="form-control">
          </div>

          <div class="d-flex gap-2">
            <button class="btn btn-primary mb-3">
              Save Changes
            </button>
            <a href="{{ route('profile.show') }}" class="btn-back mb-3">
              ← Back to Profile
            </a>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
