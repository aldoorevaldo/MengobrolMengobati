{{-- resources/views/shared/user-dropdown.blade.php --}}
@auth
@php $user = auth()->user(); @endphp

<button class="avatar-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#profileOffcanvas" aria-label="Open Profile Menu">
    <div class="user-icon-circle">
        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="#0b3b36" viewBox="0 0 16 16">
          <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
          <path fill-rule="evenodd" d="M8 9c-3.866 0-7 1.567-7 3.5v.5h14v-.5C15 10.567 11.866 9 8 9z"/>
        </svg>
    </div>
</button>

<div class="offcanvas offcanvas-end" tabindex="-1" id="profileOffcanvas">
  <div class="offcanvas-header">
    <h5 class="mb-0">My Profile</h5>
    <button class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    <div class="d-flex align-items-center gap-3 mb-4">

      <div class="user-icon-big">
        <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="#0b3b36" viewBox="0 0 16 16">
          <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
          <path fill-rule="evenodd" d="M8 9c-3.866 0-7 1.567-7 3.5v.5h14v-.5C15 10.567 11.866 9 8 9z"/>
        </svg>
      </div>

      <div class="info">
        <strong>{{ $user->name }}</strong>
        <div class="small text-muted">{{ $user->email }}</div>
        <div class="small text-muted">Role : {{ ucfirst($user->role ?? 'user') }}</div>
      </div>
    </div>

    <div class="d-grid gap-2 mb-3">
      <a href="{{ route('profile.show') }}" class="btn-view-profile">View Profile</a>
    </div>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn-logout">Logout</button>
    </form>
  </div>
</div>
@endauth
