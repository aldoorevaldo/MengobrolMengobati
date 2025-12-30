{{-- resources/views/psikolog/chat.blade.php --}}
@extends('layouts.psikolog-chat-layout')

@section('title', 'Chat Booking #' . ($booking->id ?? ''))

@section('content')
<section class="chat-page">
  <div class="container chat-container">

    {{-- HEADER --}}
    <div class="chat-header card-shadow">
      <div class="chat-header-left">
        <h2>Consultation Chat</h2>
        <div class="chat-meta">
          Booking #{{ $booking->id }} ·
          {{ $booking->scheduled_at ? \Carbon\Carbon::parse($booking->scheduled_at)->translatedFormat('d M Y, H:i') : '-' }}
        </div>
      </div>

      <div class="chat-header-actions">
        @if($isPsikolog && ($booking->status ?? '') === 'confirmed')
          <form method="POST"
                action="{{ route('psikolog.chat.end', $booking->id) }}"
                onsubmit="return confirm('Finish this session?');">
            @csrf
            <button class="btn btn-danger btn-sm">Finish</button>
          </form>
        @endif
        <a href="{{ url()->previous() }}" class="btn btn-sm">
          ← Back to Dashboard
        </a>
      </div>
    </div>

    {{-- CHAT BOX --}}
    <div class="chat-box card-shadow">
      <div id="chat-messages" class="chat-messages">
        {{-- @php $meId = $currentUser->id ?? auth()->id(); @endphp

        @foreach($booking->messages()->orderBy('created_at')->get() as $m)
          @php
            $isMine = $m->sender_id === $meId;
            $initial = strtoupper(substr(optional($m->sender)->name ?? 'U',0,1));
          @endphp

          <div class="chat-row {{ $isMine ? 'mine' : 'theirs' }}">
            @if(!$isMine)
                <div class="chat-avatar {{ $m->sender_type }}">
                {{ $initial }}
                </div>
             @endif

            <div class="chat-bubble {{ $m->sender_type }}">
                @php
                $senderName = optional($m->sender)->name
                                ?? ucfirst($m->sender_type);
                @endphp

                <div class="bubble-meta">
                {{ $senderName }} · {{ $m->created_at->format('H:i') }}
                </div>
              <div class="bubble-text">
                {{ $m->content }}
              </div>
            </div>
            @if($isMine)
                <div class="chat-avatar {{ $m->sender_type }}">
                {{ $initial }}
                </div>
            @endif

          </div>
        @endforeach --}}
      </div>

      {{-- INPUT --}}
      <div class="chat-input-area">
        <form id="chat-form">
          @csrf
          <div class="input-group">
          <input id="chat-input"
                 type="text"
                 placeholder="Write a message..."
                 autocomplete="off">
          <button class="chat-btn-send" id="chat-send-btn" type="submit">Send</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</section>
@endsection


@push('scripts')
<script>
const fetchUrl = "{{ route('psikolog.chat.messages', $booking->id) }}";
const sendUrl  = "{{ route('psikolog.chat.send', $booking->id) }}";
const currentUserId = {{ auth()->id() }};

function escapeHtml(str) {
  return String(str ?? '')
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;');
}

async function loadMessages() {
  const res = await fetch(fetchUrl, { headers: { Accept: 'application/json' } });
  if (!res.ok) return;

  const data = await res.json();
  const container = document.getElementById('chat-messages');

  container.innerHTML = '';

  data.messages.forEach(m => {
    const isMine = Number(m.sender_id) === Number(currentUserId);
    const initial = (m.sender_name || 'U')[0].toUpperCase();

    const row = document.createElement('div');
    row.className = 'chat-row ' + (isMine ? 'mine' : 'theirs');

    const avatar = document.createElement('div');
    avatar.className = 'chat-avatar ' + m.sender_type;
    avatar.textContent = initial;
    avatar.title = m.sender_name;

    const bubble = document.createElement('div');
    bubble.className = 'chat-bubble ' + m.sender_type;
    bubble.innerHTML = `
      <div class="bubble-meta">
        ${escapeHtml(m.sender_name)} · ${m.created_at}
      </div>
      <div class="bubble-text">
        ${escapeHtml(m.content)}
      </div>
    `;

    if (isMine) {
      row.appendChild(bubble);
      row.appendChild(avatar);
    } else {
      row.appendChild(avatar);
      row.appendChild(bubble);
    }

    container.appendChild(row);
  });

  container.scrollTop = container.scrollHeight;
}

document.getElementById('chat-form').addEventListener('submit', async e => {
  e.preventDefault();
  const input = document.getElementById('chat-input');
  const text = input.value.trim();
  if (!text) return;

  await fetch(sendUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
      'Accept': 'application/json'
    },
    body: JSON.stringify({ content: text })
  });

  input.value = '';
  loadMessages();
});

// Initial + polling
loadMessages();
setInterval(loadMessages, 3500);
</script>
@endpush
