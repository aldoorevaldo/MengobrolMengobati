<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Add Psikolog - Admin</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  @vite('resources/css/admin.css')
</head>
<body>

<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-0">Add New Psikolog</h3>
      <small class="text-muted">Complete the data of the psikolog to be added</small>
    </div>

    <a href="{{ route('admin.psikolog') }}" class="btn btn-outline-secondary">
      ← Back to List
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">

      <form action="{{ route('admin.psikolog.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="row g-3">
        @csrf

        <div class="col-md-6">
          <label class="form-label fw-semibold">Full Name</label>
          <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Email</label>
          <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Hospital / Clinic</label>
          <input type="text" name="hospital" class="form-control" value="{{ old('hospital') }}">
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Work Start Time</label>
          <input type="time" name="work_start" class="form-control" value="{{ old('work_start') }}">
        </div>

        <div class="col-md-6">
          <label class="form-label fw-semibold">Work End Time</label>
          <input type="time" name="work_end" class="form-control" value="{{ old('work_end') }}">
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold">Description</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Brief description about the psikolog">{{ old('description') }}</textarea>
        </div>

        <div class="col-12">
          <label class="form-label fw-semibold">Photo</label>
          <input type="file" name="photo" class="form-control">
          <div class="form-text">
                Format JPG / PNG, max 2MB
              </div>
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-primary px-4">
            Save
          </button>
        </div>

      </form>

    </div>
  </div>

</div>

</body>
</html>
