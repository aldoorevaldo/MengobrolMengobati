<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Therapy Group - Admin</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  @vite('resources/css/admin.css')
</head>
<body class="p-4 bg-light">

<div class="container">

  {{-- HEADER --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-1">Therapy Group</h3>
      <p class="text-muted small mb-0">Manage therapy groups and message statistics</p>
    </div>

    <div class="d-flex gap-2">
      <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        ← Back to Dashboard
      </a>
      <a href="{{ route('admin.therapy.groups.create') }}" class="btn btn-primary">
        + Add New Group
      </a>
    </div>
  </div>

  {{-- FLASH MESSAGE --}}
  @if(session('success'))
    <div class="alert alert-success small">
      {{ session('success') }}
    </div>
  @endif

  {{-- CARD: DAFTAR GROUP --}}
  <div class="card mb-5 shadow-sm">
    <div class="card-header bg-white fw-semibold">
      List of Therapy Group
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:60px">#</th>
            <th>Name Group</th>
            <th>Slug</th>
            <th>Created</th>
            <th style="width:120px">Action</th>
          </tr>
        </thead>
        <tbody>
        @forelse($groups as $i => $g)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td class="fw-medium">{{ $g->title }}</td>
            <td class="text-muted small">{{ $g->slug }}</td>
            <td class="small text-muted">
              {{ \Carbon\Carbon::parse($g->created_at)->translatedFormat('d M Y') }}
            </td>
            <td>
              <form action="{{ route('admin.therapy.destroy', $g->slug) }}"
                    method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this group and all its messages?');">
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
                No therapy groups found.
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- CARD: USER DENGAN PESAN TERBANYAK --}}
  <div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <span class="fw-semibold">User with Most Booking</span>
    </div>

    <div class="card-body">

      {{-- FILTER BULAN --}}
      @if(!$periods->isEmpty())
        <form method="GET" action="{{ route('admin.therapy.groups') }}" class="row g-2 mb-4">
          <div class="col-md-4">
            <label class="form-label small">Month Filter</label>
            <select name="year_month" class="form-select"
              onchange="if(this.value){ window.location='{{ route('admin.therapy.groups') }}?'+this.value; }">

              @foreach($periods as $p)
                @php
                  $value = 'year='.$p->year.'&month='.$p->month;
                  $isSelected = ($selectedYear == $p->year && $selectedMonth == $p->month);
                  $monthNames = [1=>'January','February','March','April','May','June','July','August','September','October','November','December'];
                @endphp
                <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }}>
                  {{ $monthNames[$p->month] }} {{ $p->year }}
                </option>
              @endforeach

            </select>
          </div>
        </form>
      @endif

      {{-- TABLE TOP USERS --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:60px">#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Total Booking</th>
            </tr>
          </thead>
          <tbody>
          @forelse($topUsers as $i => $u)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td class="fw-medium">{{ $u->name }}</td>
              <td class="text-muted small">{{ $u->email }}</td>
              <td>
                <span class="badge bg-primary-subtle text-primary">
                  {{ $u->total_messages }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-4">
                Not found any users with bookings.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>

    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
