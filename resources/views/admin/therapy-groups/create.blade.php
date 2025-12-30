<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Therapy Group - Admin</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  @vite('resources/css/admin.css')
</head>
<body class="p-4">

  <div class="container">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="mb-0">Add New Therapy Group</h3>
      <a href="{{ route('admin.therapy.groups') }}" class="btn btn-outline-secondary">
        ← Back to List
      </a>
    </div>

    {{-- FORM --}}
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('admin.therapy.groups.store') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label fw-semibold">Group Name</label>
            <input type="text" name="title" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Slug</label>
            <input type="text" name="slug" class="form-control" required>
            <div class="form-text">
              Example: <code>self-healing</code> or <code>anxiety-support</code>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <textarea name="description" class="form-control" rows="4"
              placeholder="Brief description of therapy group (optional)"></textarea>
          </div>

          <div class="d-flex justify-content-end">
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
