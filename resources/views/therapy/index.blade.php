@extends('layouts.therapy')

@section('title','Therapy Groups')

@section('content')
<section class="psikiater-section">
  <div class="container">

    {{-- CARD PUTIH UTAMA --}}
    <div class="page-card">

      {{-- HEADER --}}
      <div class="page-header">
        <h1 class="page-title">Therapy Groups</h1>
        <a href="{{ route('services') }}" class="back-link">
          ← Back to Services
        </a>
      </div>

      <p class="text-muted mb-4" style="max-width:720px;">
        Select the discussion forum you want to join. Your identity will remain
        <strong>anonymous</strong> to other participants.
      </p>

      {{-- GRID --}}
      <div class="row g-4">
        @foreach($groups as $g)
          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="therapy-card h-100">

              {{-- ILUSTRASI --}}
              <div class="therapy-card-media text-center">
                <svg viewBox="0 0 120 80" width="120" xmlns="http://www.w3.org/2000/svg">
                  <rect width="120" height="80" rx="12" fill="#e6f7f2"></rect>
                  <g transform="translate(10,12)" fill="#bfe9dd">
                    <rect x="0" y="0" width="40" height="10" rx="3"></rect>
                    <rect x="0" y="16" width="80" height="10" rx="3"></rect>
                    <rect x="0" y="32" width="55" height="10" rx="3"></rect>
                  </g>
                </svg>
              </div>

              {{-- BODY --}}
              <div class="therapy-card-body">
                <h3 class="therapy-card-title">{{ $g->title }}</h3>
                <p class="therapy-card-desc">
                  {{ \Illuminate\Support\Str::limit(
                    $g->description ?? 'A safe space for discussion, sharing, and mutual support.',
                    90
                  ) }}
                </p>
              </div>

              {{-- ACTION --}}
              <div class="mt-auto d-grid">
                <a href="{{ route('therapy.open', $g->slug) }}" class="btn btn-book">
                  Join Group
                </a>
              </div>

            </div>
          </div>
        @endforeach
      </div>

    </div>
  </div>
</section>
@endsection
