{{-- resources/views/therapy/show.blade.php --}}
@extends('layouts.therapy')

@section('content')
<section class="therapy-section">
  <div class="container">

    {{-- BACK --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
      <div>
        <h3 class="mb-1"></h3>
        <p class="text-muted mb-0"></p>
      </div>

      <a href="{{ route('therapy.index') }}" class="btn btn-back-therapy">
        ← Back to Therapy Group
      </a>
    </div>

    {{-- GROUP HEADER --}}
    <div class="therapy-chat-header card-shadow">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
          <h3 class="therapy-chat-title">{{ $group->title }}</h3>
          <p class="therapy-chat-desc">{{ $group->description }}</p>
        </div>

        <div>
          @if(!$member)
            <form action="{{ route('therapy.join', $group->slug) }}" method="POST">
              @csrf
              <button class="btn btn-therapy">
                Join Group (Anonymous)
              </button>
            </form>
          @else
            <span class="badge badge-anon">
              You are <strong>{{ $member->pseudonym }}</strong>
            </span>
          @endif
        </div>
      </div>
    </div>

    {{-- CHAT WRAPPER --}}
    <div class="therapy-chat-wrapper card-shadow">

      {{-- CHAT AREA --}}
      <div id="chat-box-wrapper" class="chat-area">
        <div id="chat-messages">
          @php $meId = auth()->id() ?? 0; @endphp

          @foreach($messages as $m)
            @php
              $msgId = $m->id ?? null;
              $senderId = $m->user_id ?? null;
              $pseudonym = $m->pseudonym ?? 'Anon';
              $created = $m->created_at ?? '';
              $isMine = ($senderId && $meId && ((int)$senderId === (int)$meId));
              $initial = strtoupper(substr($pseudonym, 0, 1));
            @endphp

            <div class="msg-row {{ $isMine ? 'msg-right' : 'msg-left' }}"
                 data-id="{{ $msgId }}"
                 data-user-id="{{ $senderId ?? '' }}"
                 data-pseudonym="{{ e($pseudonym) }}"
                 data-ts="{{ $created }}">

              @if($isMine)
                <div class="msg-box">
                  <div class="msg-meta">{{ $pseudonym }} · <small>{{ $created }}</small></div>
                  <div class="msg-text">{{ e($m->message) }}</div>
                </div>
                <div class="msg-avatar">{{ $initial }}</div>
              @else
                <div class="msg-avatar">{{ $initial }}</div>
                <div class="msg-box">
                  <div class="msg-meta">{{ $pseudonym }} · <small>{{ $created }}</small></div>
                  <div class="msg-text">{{ e($m->message) }}</div>
                </div>
              @endif

            </div>
          @endforeach
        </div>
      </div>

      {{-- INPUT --}}
      @if($member)
        <div class="chat-input">
          <form id="message-form">
            @csrf
            <div class="input-group">
              <input id="message-input"
                     type="text"
                     class="form-control"
                     placeholder="Write a message..."
                     maxlength="2000">
              <button class="btn-send" id="send-btn" type="submit">
                Send
              </button>
            </div>
          </form>
        </div>
      @else
        <div class="alert alert-info m-3">
          Join a group to start chatting anonymously.
        </div>
      @endif

    </div>

  </div>
</section>

<meta name="csrf-token" content="{{ csrf_token() }}">

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const currentUserId = Number(@json(auth()->id() ?? 0));
    const currentPseudonym = @json($member->pseudonym ?? null);
    const slug = @json($group->slug);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let lastTimestamp = null;

    function normalizeRenderedRows() {
        document.querySelectorAll('#chat-messages .msg-row').forEach(r => {
            const userIdAttr = r.getAttribute('data-user-id') || '';
            const pseudoAttr = r.getAttribute('data-pseudonym') || '';
            const uid = (userIdAttr !== '') ? Number(userIdAttr) : null;
            const isMine = (uid && currentUserId && uid === currentUserId) || (currentPseudonym && pseudoAttr && pseudoAttr === currentPseudonym);
            r.classList.toggle('msg-right', isMine);
            r.classList.toggle('msg-left', !isMine);
            const ts = r.getAttribute('data-ts');
            if (ts) lastTimestamp = ts;
        });
    }
    normalizeRenderedRows();

    function appendMessage(m) {
        if (!m || !('id' in m)) return;
        const container = document.getElementById('chat-messages');
        if (container.querySelector(`.msg-row[data-id="${m.id}"]`)) return;

        const uid = (typeof m.user_id !== 'undefined' && m.user_id !== null) ? Number(m.user_id) : null;
        const pseudo = m.pseudonym || currentPseudonym || 'Anon';
        const isMine = (uid && currentUserId && uid === currentUserId) || (currentPseudonym && pseudo === currentPseudonym);
        const initial = (pseudo.charAt(0) || 'A').toUpperCase();

        const row = document.createElement('div');
        row.className = 'msg-row ' + (isMine ? 'msg-right' : 'msg-left');
        row.setAttribute('data-id', m.id);
        row.setAttribute('data-user-id', (uid !== null) ? String(uid) : '');
        row.setAttribute('data-pseudonym', pseudo);
        row.setAttribute('data-ts', m.created_at || '');

        const avatar = document.createElement('div');
        avatar.className = 'msg-avatar';
        avatar.textContent = initial;
        avatar.title = pseudo;

        const box = document.createElement('div');
        box.className = 'msg-box';
        const esc = s => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        box.innerHTML = `<div class="msg-meta">${esc(pseudo)} • <small class="msg-ts">${esc(m.created_at||'')}</small></div>
                         <div class="msg-text">${esc(m.message||'')}</div>`;

        if (isMine) {
            row.appendChild(box);
            row.appendChild(avatar);
        } else {
            row.appendChild(avatar);
            row.appendChild(box);
        }

        container.appendChild(row);
        const wrapper = document.getElementById('chat-box-wrapper') || document.querySelector('.chat-area');
        if (wrapper) wrapper.scrollTop = wrapper.scrollHeight;

        if (m.created_at) lastTimestamp = m.created_at;
    }

    const form = document.getElementById('message-form');
    if (form) {
        form.addEventListener('submit', async function(e){
            e.preventDefault();
            const input = document.getElementById('message-input');
            const text = input.value.trim();
            if (!text) return;
            const btn = document.getElementById('send-btn');
            btn.disabled = true;
            try {
                const res = await fetch(`/therapy-groups/${encodeURIComponent(slug)}/messages`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
                    body: JSON.stringify({ message: text })
                });
                const payload = await res.json().catch(()=>null);
                if (!res.ok) {
                    console.error('send failed', payload);
                    alert(payload?.error || 'Gagal mengirim pesan');
                    return;
                }
                if (payload && payload.id) {
                    if (typeof payload.user_id === 'undefined' || payload.user_id === null) payload.user_id = currentUserId || null;
                    if (!payload.pseudonym) payload.pseudonym = currentPseudonym || 'Anon';
                    appendMessage(payload);
                    input.value = '';
                } else {
                    alert('Response tidak valid saat mengirim pesan.');
                }
            } catch(err) {
                console.error('network err', err);
                alert('Network error');
            } finally {
                btn.disabled = false;
            }
        });
    }

    async function poll() {
        try {
            const url = `/therapy-groups/${encodeURIComponent(slug)}/messages` + (lastTimestamp ? `?since=${encodeURIComponent(lastTimestamp)}` : '');
            const r = await fetch(url, { credentials:'same-origin', headers:{ 'Accept':'application/json' }});
            if (!r.ok) return;
            const payload = await r.json();
            if (!payload || !Array.isArray(payload.messages)) return;
            payload.messages.forEach(m => appendMessage(m));
        } catch(e) {
            console.debug('poll err', e);
        }
    }
    setInterval(poll, 2500);

    window.__therapyChatDebug = { currentUserId, currentPseudonym, normalizeRenderedRows, appendMessage };
});
</script>
@endpush
@endsection
