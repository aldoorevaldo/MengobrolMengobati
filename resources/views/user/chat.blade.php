{{-- resources/views/user/chat.blade.php --}}
@extends('layouts.app')

@section('title', 'Chat dengan Psikolog — Booking #' . $booking->id)

@section('content')
<link rel="stylesheet" href="{{ asset('css/psikolog-chat.css') }}">

<div class="container my-4">
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h5 class="mb-0">Chat — Booking #{{ $booking->id }}</h5>
        <small class="text-muted">Jadwal: {{ $booking->scheduled_at ? \Carbon\Carbon::parse($booking->scheduled_at)->translatedFormat('d M Y, H:i') : '-' }}</small>
      </div>
      <div class="text-end">
        <div><strong>{{ $booking->psikolog->name ?? $booking->psikiater->name ?? 'Provider' }}</strong></div>
        <div class="small text-muted">{{ optional($booking->psikolog->user ?? $booking->psikiater->user)->email ?? '-' }}</div>
      </div>
    </div>

    <div class="card-body d-flex flex-column" style="height:560px;">
      {{-- messages box --}}
      <div id="chat-messages" class="flex-grow-1 overflow-auto p-3 bg-light border rounded">
        {{-- pesan akan di-load via JS --}}
      </div>

      {{-- input --}}
      <form id="chat-form" class="mt-3 d-flex gap-2">
        @csrf
        <input type="text" id="chat-input" name="content" class="form-control" placeholder="Tulis pesan..." autocomplete="off" />
        <button class="btn btn-primary" type="submit">Kirim</button>
      </form>
    </div>
  </div>
</div>

<script>
const bookingId = {{ $booking->id }};
const fetchUrl = "{{ route('user.chat.messages', $booking->id) }}";
const sendUrl = "{{ route('user.chat.send', $booking->id) }}";
const currentUserId = {{ auth()->user()->id }};
const currentUserIsPsikolog = {{ $isPsikolog ? 'true' : 'false' }};

function escapeHtml(unsafe) {
  return unsafe
       .replace(/&/g, "&amp;")
       .replace(/</g, "&lt;")
       .replace(/>/g, "&gt;")
       .replace(/"/g, "&quot;")
       .replace(/'/g, "&#039;");
}

async function loadMessages() {
  try {
    const res = await fetch(fetchUrl, { headers: { 'Accept': 'application/json' }});
    if (!res.ok) throw new Error('fetch failed');
    const json = await res.json();
    const container = document.getElementById('chat-messages');
    container.innerHTML = '';

    json.messages.forEach(m => {
      const isMine = (m.sender_type === 'psikolog' ? currentUserIsPsikolog : (m.sender_id === currentUserId));
      const wrapper = document.createElement('div');
      wrapper.className = 'd-flex mb-2 ' + (isMine ? 'justify-content-end' : 'justify-content-start');

      const bubble = document.createElement('div');
      bubble.className = 'p-2 rounded ' + (isMine ? 'bg-primary text-white' : 'bg-white border');
      bubble.style.maxWidth = '70%';
      bubble.innerHTML = `<div class="small text-muted">${escapeHtml(m.sender_type)}</div>
                          <div>${escapeHtml(m.content)}</div>
                          <div class="small text-muted text-end">${escapeHtml(m.created_at)}</div>`;
      wrapper.appendChild(bubble);
      container.appendChild(wrapper);
    });

    // scroll to bottom
    container.scrollTop = container.scrollHeight;
  } catch (err) {
    console.error('Gagal load messages', err);
  }
}

// polling every 2.5s
let pollInterval = setInterval(loadMessages, 2500);
document.addEventListener('visibilitychange', function() {
  if (document.hidden) clearInterval(pollInterval);
  else pollInterval = setInterval(loadMessages, 2500);
});

// send handler
document.getElementById('chat-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const input = document.getElementById('chat-input');
  const text = input.value.trim();
  if (!text) return;
  try {
    const token = document.querySelector('input[name=_token]').value;
    const res = await fetch(sendUrl, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({ content: text })
    });
    if (!res.ok) {
      const err = await res.json().catch(()=>({}));
      alert(err.error || 'Gagal mengirim pesan');
      return;
    }
    input.value = '';
    await loadMessages();
  } catch (err){
    console.error(err);
    alert('Gagal mengirim pesan');
  }
});

// initial load
loadMessages();
</script>
@endsection
