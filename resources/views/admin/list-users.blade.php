<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar User - Admin</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <!-- Bootstrap -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Admin style -->
  @vite('resources/css/admin.css')
</head>
<body>

  <main class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h3 class="mb-1">List of Users</h3>
        <p class="text-muted small mb-0">Management of user data in the application</p>
      </div>

      <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        ← Back to Dashboard
      </a>
    </div>

    {{-- CARD TABLE --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Registration Date</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>

              @forelse($users as $u)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="fw-semibold">{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ \Carbon\Carbon::parse($u->created_at)->format('d M Y') }}</td>
                <td class="text-center">
                  <form action="{{ route('admin.users.destroy', $u->id) }}"
                        method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this user?');"
                        class="d-inline">
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
                  No user data available
                </td>
              </tr>
              @endforelse

            </tbody>
          </table>
        </div>
    </div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
