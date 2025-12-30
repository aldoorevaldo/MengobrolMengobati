<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Psikiater - Admin</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <!-- Bootstrap -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Admin standalone style -->
  @vite('resources/css/admin.css')
</head>
<body class="admin-page p-4">

  <div class="container">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h3 class="mb-1">Add New Psikiater</h3>
        <p class="text-muted small mb-0">
          Complete the data of the psikiater to be added
        </p>
      </div>

      <a href="{{ route('admin.psikiater') }}" class="btn btn-outline-secondary">
        ← Back to List
      </a>
    </div>

    {{-- ERROR MESSAGE --}}
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- FORM CARD --}}
    <div class="card shadow-sm">
      <div class="card-body p-4">

        <form action="{{ route('admin.psikiater.store') }}"
              method="POST"
              enctype="multipart/form-data">
          @csrf

          <div class="row g-3">

            {{-- Nama --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Full Name</label>
              <input type="text"
                     name="name"
                     class="form-control"
                     required
                     value="{{ old('name') }}">
            </div>

            {{-- Email --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Email</label>
              <input type="email"
                     name="email"
                     class="form-control"
                     required
                     value="{{ old('email') }}">
            </div>

            {{-- Password --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Password</label>
              <input type="password"
                     name="password"
                     class="form-control"
                     required>
            </div>

            {{-- Rumah Sakit --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Hospital / Clinic</label>
              <input type="text"
                     name="hospital"
                     class="form-control"
                     value="{{ old('hospital') }}">
            </div>

            {{-- Jam Kerja --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Work Start Time</label>
              <input type="time"
                     name="work_start"
                     class="form-control"
                     value="{{ old('work_start') }}">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Work End Time</label>
              <input type="time"
                     name="work_end"
                     class="form-control"
                     value="{{ old('work_end') }}">
            </div>

            {{-- Deskripsi --}}
            <div class="col-12">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description"
                        class="form-control"
                        rows="3"
                        placeholder="Brief description about the psikiater">{{ old('description') }}</textarea>
            </div>

            {{-- Foto --}}
            <div class="col-12">
              <label class="form-label fw-semibold">Photo</label>
              <input type="file"
                     name="photo"
                     class="form-control">
              <div class="form-text">
                Format JPG / PNG, max 2MB
              </div>
            </div>

          </div>

          {{-- ACTION --}}
          <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary px-4">
              Save
            </button>
          </div>

        </form>

      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
