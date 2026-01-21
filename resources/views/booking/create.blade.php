@extends('layouts.booking-layout')

@section('title','Create Booking - ' . ($ps->name ?? 'Provider'))

@vite(['resources/css/create.css'])
@section('content')
<div class="booking-wrapper">

  <div class="booking-card">

    {{-- tombol kembali --}}
    <a href="{{ url()->previous() }}" class="btn-back">
      ← Back to List
    </a>

    <h4 class="booking-title">
      Create Booking - {{ $ps->name }}
    </h4>

    <div class="booking-subtitle">
      {{ $type === 'psikolog' ? 'Psikolog' : 'Psikiater' }}
      @if($ps->hospital)
        • {{ $ps->hospital }}
      @endif
    </div>

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('booking.store') }}">
      @csrf

      <input type="hidden" name="type" value="{{ $type }}">

      @if($type === 'psikolog')
        <input type="hidden" name="psikolog_id" value="{{ $ps->id }}">
      @else
        <input type="hidden" name="psikiater_id" value="{{ $ps->id }}">
      @endif

      <div class="mb-3">
        <label class="form-label">Service (optional)</label>
        <input name="service" value="{{ old('service') }}" class="form-control">
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Date</label>
          <input
            type="date"
            id="booking-date"
            name="date"
            value="{{ old('date') }}"
            class="form-control"
            required
            min="{{ \Carbon\Carbon::now()->toDateString() }}"
          >
        </div>

        <div class="col-md-6">
          <label class="form-label">Time</label>
          <select id="booking-time" name="time" class="form-select" required>
            <option value="">Choose Date First</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">
          Notes for {{ $type === 'psikolog' ? 'Psikolog' : 'Psikiater' }} (optional)
        </label>
        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button class="btn-send" type="submit">
          Send Booking
        </button>
        <a class="btn-cancel" href="{{ route('services') }}">
          Cancel Booking
        </a>
      </div>

    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const dateInput = document.getElementById('booking-date');
  const timeSelect = document.getElementById('booking-time');

  const providerId = "{{ $ps->id }}";
  const type = "{{ $type }}";

  async function fetchTimes(date) {
    timeSelect.innerHTML = '<option>Loading...</option>';

    const url = `/psikiater/${providerId}/available-times?date=${date}&type=${type}`;

    try {
      const res = await fetch(url, {
        headers: { 'Accept': 'application/json' }
      });
      const json = await res.json();
      const times = json.times || [];

      if (times.length === 0) {
        timeSelect.innerHTML = '<option value="">No slots available.</option>';
        return;
      }

      timeSelect.innerHTML = '<option value="">Choose Time</option>';
      times.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t;
        opt.textContent = t;
        timeSelect.appendChild(opt);
      });
    } catch (err) {
      console.error(err);
      timeSelect.innerHTML = '<option value="">Failed to load times</option>';
    }
  }

  dateInput.addEventListener('change', function () {
    if (this.value) fetchTimes(this.value);
  });

  @if(old('date'))
    fetchTimes("{{ old('date') }}").then(() => {
      @if(old('time'))
        document.querySelector('#booking-time').value = "{{ old('time') }}";
      @endif
    });
  @endif
});
</script>
@endsection
