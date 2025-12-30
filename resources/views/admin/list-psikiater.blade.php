<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>List of Psikiater - Admin</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Admin CSS -->
  @vite('resources/css/admin.css')
</head>

<body class="admin-page p-4">
<div class="container">
  {{-- HEADER --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-1">List of Psikiater</h3>
      <p class="text-muted small mb-0">Management of registered psikiater data</p>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        ← Back to Dashboard
      </a>
      <a href="{{ route('admin.psikiater.create') }}" class="btn btn-primary">
        + Add New Psikiater
      </a>
    </div>
  </div>

  {{-- TABLE CARD --}}
  <div class="card shadow-sm">
    <div class="card-body p-0">

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:60px">#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Registered</th>
              <th style="width:120px">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($psikiater as $p)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="fw-medium">{{ $p->name }}</td>
                <td>{{ $p->email }}</td>
                <td>
                  {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}
                </td>
                <td>
                  <form action="{{ route('admin.psikiater.destroy', $p->id) }}"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this psikiater?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">
                  No psikiater data available.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body>
</html>
