<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>List of Psikiater - MengobrolMengobati</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  @vite('resources/css/psikiater.css')
</head>
<body class="psikiater-page">

<section class="psikiater-section">
  <div class="container">

    <div class="page-card">

      <div class="page-header">
        <h1 class="page-title">List of Psikiater</h1>
        <a href="{{ url('/services') }}" class="back-link">← Back to Services</a>
      </div>

      @if(isset($psikiaters) && $psikiaters->count() > 0)
        <div class="row g-4">
          @foreach($psikiaters as $p)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
              <div class="card doctor-card h-100">

                @php
                    $photoUrl = $p->photo
                        ? asset('storage/' . $p->photo)
                        : asset('images/default-avatar.png');
                @endphp


                <img src="{{ $photoUrl }}" alt="{{ $p->name }}" class="doctor-image">

                <div class="card-body d-flex flex-column">
                  <div>
                    <div class="doctor-name">{{ $p->name }}</div>

                    @if($p->hospital)
                      <div class="doctor-meta">{{ $p->hospital }}</div>
                    @endif

                    @if($p->work_start || $p->work_end)
                      <div class="doctor-meta">
                        {{ $p->work_start ? date('H:i', strtotime($p->work_start)) : '' }}
                        @if($p->work_start && $p->work_end) - @endif
                        {{ $p->work_end ? date('H:i', strtotime($p->work_end)) : '' }}
                      </div>
                    @endif

                    @if($p->description)
                      <p class="doctor-desc">
                        {{ Str::limit($p->description, 80) }}
                      </p>
                    @endif
                  </div>

                  <div class="mt-auto d-grid">
                    <a href="{{ route('booking.create', $p->id) }}" class="btn btn-book">
                      Book
                    </a>
                  </div>
                </div>

              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="no-data">
          <p>There are currently no registered psikiater.</p>
          {{-- <a href="{{ url('/services') }}" class="back-link">Back to Services</a> --}}
        </div>
      @endif

      @if(method_exists($psikiaters, 'links'))
        <div class="mt-4">
          {{ $psikiaters->links() }}
        </div>
      @endif

    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
