<!doctype html>
<html lang="id">
<head>
  <title>List Psikolog - Admin</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  @vite('resources/css/admin.css')
</head>
<body class="p-4">

<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0">List of Psikolog</h3>
        <p class="text-muted small mb-0">Management of registered psikolog data</p>
    </div>
    <div>
      <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">← Back to Dashboard</a>
      <a href="{{ route('admin.psikolog.create') }}" class="btn btn-primary ms-2">+ Add New Psikolog</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Registered</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($psikolog as $p)
          <tr>
            <td>{{ $p->id }}</td>
            <td class="fw-medium">{{ $p->name }}</td>
            <td>{{ $p->email }}</td>
            <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</td>
            <td class="text-center">
              <form action="{{ route('admin.psikolog.destroy',$p->id) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this psikolog?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
</body>
</html>
