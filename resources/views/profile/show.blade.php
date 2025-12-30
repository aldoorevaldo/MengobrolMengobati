@extends('layouts.profile-layout')

@section('title','Profile - ' . ($user->name ?? 'Profile'))

@section('content')
<div class="container profile-page py-4">
  <div class="row g-4">

    {{-- LEFT: PROFILE CARD --}}
    <div class="col-lg-4">
      <div class="card profile-card p-4 text-center h-100">

        <div class="profile-avatar mb-3 mx-auto d-flex align-items-center justify-content-center">
          {{ strtoupper(substr($user->name ?? 'U',0,1)) }}
        </div>

        <h4 class="mb-1">{{ $user->name }}</h4>
        <p class="text-muted small mb-4">{{ $user->email }}</p>

        <div class="d-grid gap-2">
          <a href="{{ route('profile.edit') }}" class="btn btn-primary">
            Edit Profile
          </a>

          <a href="{{ route('services') }}" class="btn btn-success">
            View Services
          </a>

          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-danger w-100">
              Logout
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- RIGHT: BOOKINGS --}}
    <div class="col-lg-8">

      {{-- PSIKIATER --}}
      <div class="card booking-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Booking History - Psikiater</h5>
          <span class="text-muted small">{{ $psikiaterBookings->count() }} Booking</span>
        </div>

        @if($psikiaterBookings->isEmpty())
          <div class="alert alert-info mb-0">
            No booking with a psikiater yet.
          </div>
        @else
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Psikiater</th>
                  <th>Services</th>
                  <th>Schedule</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($psikiaterBookings as $b)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    <div class="fw-semibold">
                      {{ optional($b->psikiater)->name ?? 'Not yet determined' }}
                    </div>
                    <div class="small text-muted">
                      {{ optional($b->psikiater)->hospital ?? '' }}
                    </div>
                  </td>
                  <td>{{ $b->service ?? '-' }}</td>
                  <td>
                    {{ $b->scheduled_at
                      ? \Carbon\Carbon::parse($b->scheduled_at)->translatedFormat('d M Y, H:i')
                      : '-' }}
                  </td>
                  <td>
                    @php
                      $status = $b->status ?? 'pending';
                      $badge = $status === 'pending' ? 'badge-pending'
                              : ($status === 'confirmed' ? 'badge-confirmed' : 'badge-rejected');
                    @endphp
                    <span class="badge-status {{ $badge }}">
                      {{ ucfirst($status) }}
                    </span>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

      {{-- PSIKOLOG --}}
      <div class="card booking-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Booking History - Psikolog</h5>
          <span class="text-muted small">{{ $psikologBookings->count() }} Booking</span>
        </div>

        @if($psikologBookings->isEmpty())
          <div class="alert alert-info mb-0">
            No booking with a psikolog yet.
          </div>
        @else
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Psikolog</th>
                  <th>Services</th>
                  <th>Schedule</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($psikologBookings as $b)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    <div class="fw-semibold">
                      {{ optional($b->psikolog)->name ?? 'Belum ditentukan' }}
                    </div>
                    <div class="small text-muted">
                      {{ optional($b->psikolog)->hospital ?? '' }}
                    </div>
                  </td>
                  <td>{{ $b->service ?? '-' }}</td>
                  <td>
                    {{ $b->scheduled_at
                      ? \Carbon\Carbon::parse($b->scheduled_at)->translatedFormat('d M Y, H:i')
                      : '-' }}
                  </td>
                  <td>
                    @php
                      $status = $b->status ?? 'pending';
                      $badge = $status === 'pending' ? 'badge-pending'
                              : ($status === 'confirmed' ? 'badge-confirmed' : 'badge-rejected');
                    @endphp
                    <span class="badge-status {{ $badge }}">
                      {{ ucfirst($status) }}
                    </span>
                  </td>
                  <td>
                    @if(($b->status ?? '') === 'confirmed')
                      <a href="{{ route('user.chat.show', $b->id) }}" class="btn-chat btn-sm">
                        Chat
                      </a>
                    @else
                      <span class="text-muted small">—</span>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

    </div>
  </div>
</div>
@endsection
