@extends('layouts.psikolog-layout')

@section('title','Dashboard Psikolog - ' . ($psikolog->name ?? 'Psikolog'))

@section('content')

{{-- HEADER CARD --}}
<div class="psy-header">
  <h1>{{ $psikolog->name }}</h1>
  <div class="psy-sub">{{ $psikolog->hospital ?? '—' }}</div>
  <p class="psy-muted">Manage consultation bookings & chat sessions</p>
</div>

{{-- ALERT --}}
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- BOOKING CARD --}}
<div class="psy-card">
  <div class="psy-card-head">
    <h4>List of Bookings</h4>
    <span class="psy-count">{{ $bookings->total() ?? $bookings->count() }} Booking</span>
  </div>

  @if($bookings->isEmpty())
    <div class="alert alert-info mb-0">No booking data available.</div>
  @else
    <div class="table-responsive">
      <table class="table psy-table align-middle">
        <thead>
          <tr>
            <th>User</th>
            <th>Services</th>
            <th>Schedule</th>
            <th>Status</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($bookings as $b)
          <tr>
            <td>
              <div class="fw-semibold">
                {{ optional($b->user)->name ?? 'User deleted' }}
              </div>
              <small class="text-muted">
                {{ optional($b->user)->email ?? '-' }}
              </small>
            </td>

            <td>{{ $b->service ?? '-' }}</td>

            <td>
              {{ $b->scheduled_at
                ? \Carbon\Carbon::parse($b->scheduled_at)->translatedFormat('d M Y, H:i')
                : '-' }}
            </td>

            <td>
              @php $status = $b->status ?? 'pending'; @endphp
              <span class="badge
                @if($status=='pending') bg-warning text-dark
                @elseif($status=='confirmed') bg-success
                @elseif($status=='rejected') bg-danger
                @elseif($status=='finished') bg-secondary
                @endif">
                {{ ucfirst($status) }}
              </span>
            </td>

            <td class="text-end">
              @if($status === 'pending')
                <a href="{{ route('bookings.psikolog.approve.confirm', $b->id) }}"
                   class="btn btn-sm btn-success">
                  Approve
                </a>
                <a href="{{ route('bookings.psikolog.reject.confirm', $b->id) }}"
                   class="btn btn-sm btn-danger">
                  Reject
                </a>

              @elseif($status === 'confirmed')
                <a href="{{ route('psikolog.chat.show', $b->id) }}"
                   class="btn-chat btn-sm">
                  Chat
                </a>

                <form method="POST"
                      action="{{ route('psikolog.booking.finish', $b->id) }}"
                      class="d-inline">
                  @csrf
                  <button class="btn btn-danger btn-sm"
                          onclick="return confirm('Finish this session?')">
                    Finish
                  </button>
                </form>

              @else
                <span class="text-muted small">—</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $bookings->links() }}
    </div>
  @endif
</div>

@endsection
