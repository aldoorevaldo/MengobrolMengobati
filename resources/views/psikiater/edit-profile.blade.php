<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Profile - {{ $psikiater->name }}</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;800;900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  @vite('resources/css/psikiater.css')
</head>

<body class="psikiater-page">

<section class="psikiater-section-2">
  <div class="container">

    <div class="page-card profile-card">

      {{-- HEADER --}}
      <div class="profile-header">

        <div class="profile-avatar-lg">
          @if($psikiater->photo)
            <img src="{{ asset('storage/'.$psikiater->photo) }}">
          @else
            <div class="avatar-initial-lg">
              {{ strtoupper(substr($psikiater->name ?? 'U',0,1)) }}
            </div>
          @endif
        </div>

        <div class="profile-info">
          <h1 class="profile-name">Edit Profile</h1>
          <div class="profile-hospital">Update your account information</div>
        </div>

        <div class="profile-actions">
          <a href="{{ route('psikiater.profile') }}" class="btn-back btn-sm">
            ← Back to Dashboard
          </a>
        </div>

      </div>

      <hr class="my-4">

      {{-- FORM --}}
      <form method="POST"
            action="{{ route('psikiater.profile.update') }}"
            enctype="multipart/form-data">
        @csrf

        <div class="profile-detail-grid">

          <div class="label">Name</div>
          <div class="value">
            <input type="text" name="name"
                   value="{{ old('name',$psikiater->name) }}"
                   class="form-control" required>
          </div>

          <div class="label">Workplace</div>
          <div class="value">
            <input type="text" name="hospital"
                   value="{{ old('hospital',$psikiater->hospital) }}"
                   class="form-control">
          </div>

          <div class="label">Working Hours</div>
          <div class="value d-flex gap-2">
            <input type="time" name="work_start"
                   value="{{ old('work_start',$psikiater->work_start) }}"
                   class="form-control">
            <input type="time" name="work_end"
                   value="{{ old('work_end',$psikiater->work_end) }}"
                   class="form-control">
          </div>

          <div class="label">Email</div>
          <div class="value">
            <input type="text"
                   class="form-control"
                   value="{{ optional($psikiater->user)->email }}"
                   disabled>
          </div>

          <div class="label">Description</div>
          <div class="value">
            <textarea name="description"
                      class="form-control"
                      rows="4">{{ old('description',$psikiater->description) }}</textarea>
          </div>

          <div class="label">Profile Photo</div>
          <div class="value">
            <input type="file" name="photo" class="form-control">
            <small class="text-muted">JPG / PNG / WEBP • Max 5MB</small>

            {{-- PREVIEW --}}
            <div class="mt-2">
                @if($psikiater->photo)
                <img src="{{ asset('storage/'.$psikiater->photo) }}"
                        style="width:96px;height:96px;border-radius:50%;object-fit:cover;">
                @else
                <div class="avatar-initial-lg"
                        style="width:96px;height:96px;border-radius:50%;">
                    {{ strtoupper(substr($psikiater->name ?? 'U',0,1)) }}
                </div>
                @endif
            </div>
          </div>

        </div>

        <div class="mt-4 d-flex gap-2">
          <button class="btn-save">Save Changes</button>
          <a href="{{ route('psikiater.profile') }}" class="btn-back">
            Cancel
          </a>
        </div>

      </form>

    </div>

  </div>
</section>

</body>
</html>
