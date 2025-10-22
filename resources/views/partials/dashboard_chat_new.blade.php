<div class="same-card chats-card">
    <div class="awards-headding">
        <h2>Chats</h2>
        <i class="fa-solid fa-ellipsis"></i>
    </div>
    <div class="search-box chats-search">
        <input type="text" id="chatSearch" placeholder="Search">
        <img src="{{ asset('assets/player-dashboard/images/search.svg') }}" alt="Icon">
    </div>
    <div class="chat-profile-all" id="chatList">
        @forelse($chats as $chat)
            @php
                $other = $chat->participants->firstWhere('id', '!=', $user->id);
                $last = $chat->messages->sortByDesc('created_at')->first();
            @endphp
            @if($other)
            <div class="chat-profile align-items-center" data-name="{{ strtolower($other->name ?? ($other->first_name . ' ' . $other->last_name)) }}">
                <div class="d-flex gap-2 align-items-center">
                    <span class="chat-user">
                        <img src="{{ asset('assets/player-dashboard/images/chat1.png') }}" alt="Chat Image">
                    </span>
                    <span class="chat-user flex-column gap-1">
                        <h5>{{ $other->name ?? ($other->first_name . ' ' . $other->last_name) }}</h5>
                        <h6><label></label>{{ Str::limit($last?->content ?? 'No messages yet', 40) }}</h6>
                    </span>
                </div>
                <div class="chat-icon">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop-four" onclick="openChat('{{ addslashes($other->name ?? ($other->first_name . ' ' . $other->last_name)) }}', {{ (int) $chat->id }}, this)">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            @endif
        @empty
            <div class="text-muted p-3">No chats yet</div>
        @endforelse
    </div>
</div>

<!-- Chat Modal -->
<div class="modal fade" id="staticBackdrop-four" data-bs-backdrop="static"
    data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-content-padding modal-content-one">
            <div class="modal-header modal-header-one">
                <div class="modal-headding modal-headding-two">
                    <div class="leftmodal-header">
                        <div class="chat-box-icon personal-chat-text">
                            <h2 id="chatUserInitials">VP</h2>
                        </div>
                        <div class="modal-text-headding">
                            <h1 class="modal-title fs-5" id="chatUserName">Chat</h1>
                            <p id="chatUserStatus"><label></label></p>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="chat-container">
                    <div class="chat-left panel-box panel-box-a chat-left-card">
                        <div class="chat-body d-flex flex-column pt-0" id="chatMessages"></div>
                    </div>
                </div>
                <div class="chat-footer">
                    <div class="chat-input-box">
                        <div class="write-message-main">
                            <div class="write-message-input">
                                <input type="text" id="chatInput" placeholder="Write a message..." autocomplete="off">
                            </div>
                            <div class="camera-buttons">
                                <input type="file" id="dashChatFile" class="d-none" accept="image/*,video/*,audio/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt">
                                <button type="button" id="dashAttachBtn" title="Attach" aria-label="Attach file"><img src="{{ asset('assets/player-dashboard/images/pin-icon.svg') }}" alt="Attach"></button>
                                <button type="button" id="dashCameraBtn" title="Camera" aria-label="Open camera"><img src="{{ asset('assets/player-dashboard/images/camera.svg') }}" alt="Camera"></button>
                            </div>
                        </div>
                        <div id="dashPendingAttachment" class="mt-2 d-none">
                            <div class="d-flex align-items-center gap-2">
                                <div id="dashPendingThumb"></div>
                                <div class="small text-muted" id="dashPendingName"></div>
                                <button type="button" id="dashClearPending" class="btn btn-sm btn-outline-light">Remove</button>
                            </div>
                        </div>
                        <div class="audio-three-btn position-relative">
                            <button type="button" class="share-btn" id="dashShareBtn" title="Send" aria-label="Send message"><img src="{{ asset('assets/player-dashboard/images/share-icon.svg') }}" alt="Send"></button>
                            <button type="button" class="share-btn audio-btn" id="dashAudioBtn" title="Audio"><img src="{{ asset('assets/player-dashboard/images/audio-icon.svg') }}" alt="Audio"></button>
                            <button type="button" class="share-btn emoji-btn" id="dashEmojiBtn" title="Emoji" aria-haspopup="true" aria-expanded="false"><img src="{{ asset('assets/player-dashboard/images/emoji-icon.svg') }}" alt="Emoji"></button>
                            <div id="dashEmojiPanel" class="d-none" role="dialog" aria-label="Emoji picker" style="position:absolute; bottom: 48px; right: 0; background:#1e1e1e; border:1px solid #444; border-radius:10px; padding:8px; max-width:280px; z-index:2000; box-shadow:0 8px 24px rgba(0,0,0,.4);">
                                <div style="display:grid; grid-template-columns: repeat(8, 1fr); gap:6px; font-size:20px; line-height:1;">
                                    @php($emojis = ['😀','😁','😂','🤣','😊','😍','😘','😎','😇','🙂','😉','😅','🤩','🥳','🤗','🤔','🙄','😴','🤤','😤','😭','😡','👍','👎','🙏','👏','🙌','💪','🔥','💯','🎉','✨','❤️','💙','💚','💛','💜','🖤','🤍','⚽','🏀','🏈','⚾','🎾','🏐'])
                                    @foreach($emojis as $e)
                                        <button type="button" class="dash-emoji-btn" style="background:none;border:none;color:#fff;cursor:pointer">{{ $e }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let activeChatId = null;
let pollHandle = null;
const CHAT_ATTACHMENT_URL = "{{ \Illuminate\Support\Facades\Route::has('player.chat.attachment') ? route('player.chat.attachment') : url('/player/chat/attachment') }}";

function esc(s){
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/\"/g,'&quot;').replace(/'/g,'&#039;');
}

function renderMessages(list){
    const box = document.getElementById('chatMessages');
    box.innerHTML = '';
    list.forEach(m => {
        const mine = Number(m.sender_id) === Number({{ (int) $user->id }});
        const side = mine ? 'msg-two' : 'joining-team-text';
        const msgClass = mine ? 'msg msg-agent mb-0' : 'msg text-msg-two msg-user';
        // attachments
        let att = '';
        if (m.attachment_url || m.attachment_path) {
            const url = m.attachment_url || m.attachment_path;
            const type = (m.attachment_type || '').toLowerCase();
            if (type.startsWith('image/')) {
                att = `<div class=\"mt-2\"><a href=\"${url}\" class=\"dash-img-lightbox\" data-url=\"${url}\"><img src=\"${url}\" alt=\"image\" style=\"max-height:180px;border-radius:8px\"></a></div>`;
            } else if (type.startsWith('video/')) {
                att = `<div class=\"mt-2\"><video src=\"${url}\" controls style=\"max-width:100%;max-height:220px;border-radius:8px\"></video></div>`;
            } else {
                const name = url.split('/').pop();
                att = `<div class=\"mt-2\"><a href=\"${url}\" target=\"_blank\">${name}</a></div>`;
            }
        }
        box.insertAdjacentHTML('beforeend', `
            <div class="${side}">
                <div class="${mine ? 'user-name' : ''}">
                    ${mine ? `<h2>{{ substr(auth()->user()->name, 0, 2) }}</h2>` : ''}
                </div>
                <div class="${mine ? 'msg-rignt' : ''}">
                    <div class="${msgClass}"><p>${esc(m.content || '')}</p>${att}</div>
                    <div class="msg-time">${new Date(m.created_at).toLocaleTimeString()}</div>
                </div>
            </div>
        `);
    });
    box.scrollTop = box.scrollHeight;
}

let lastChatTrigger = null;

function openChat(userName, chatId, triggerEl) {
    lastChatTrigger = triggerEl || lastChatTrigger;
    activeChatId = chatId;
    const initials = userName.split(' ').map(n => n[0]).join('').toUpperCase();
    document.getElementById('chatUserInitials').textContent = initials;
    document.getElementById('chatUserName').textContent = userName;
    document.getElementById('chatUserStatus').innerHTML = '';

    fetch(`/player/chat/messages/${chatId}`)
        .then(r => r.json())
        .then(renderMessages);

    if (window.Echo) {
        try {
            window.Echo.join(`chat.${chatId}`)
                .here(() => {})
                .listen('.message.sent', (e) => {
                    // append new message by re-rendering with previous + new
                    const box = document.getElementById('chatMessages');
                    box.insertAdjacentHTML('beforeend', `
                        <div class="joining-team-text">
                          <div class="msg text-msg-two msg-user"><p>${esc(e.content)}</p></div>
                          <div class="msg-time">${new Date(e.created_at).toLocaleTimeString()}</div>
                        </div>`);
                    box.scrollTop = box.scrollHeight;
                });
        } catch (e) { console.warn('Echo error', e); }
    } else {
        if (pollHandle) clearInterval(pollHandle);
        pollHandle = setInterval(() => {
            fetch(`/player/chat/messages/${chatId}`).then(r=>r.json()).then(renderMessages);
        }, 5000);
    }

    const input = document.getElementById('chatInput');
    input.onkeydown = function(ev){
        if (ev.key === 'Enter' && input.value.trim()) {
            const payload = { chat_id: chatId, message: input.value.trim() };
            fetch(`{{ route('player.chat.send') }}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify(payload)
            }).then(r=>r.json()).then(res => {
                input.value = '';
                // Optimistic append
                const mine = true;
                const box = document.getElementById('chatMessages');
                box.insertAdjacentHTML('beforeend', `
                    <div class="msg-two">
                      <div class="user-name"><h2>{{ substr(auth()->user()->name, 0, 2) }}</h2></div>
                      <div class="msg-rignt">
                        <div class="msg msg-agent mb-0"><p>${esc(res.message.content)}</p></div>
                        <div class="msg-time">${new Date(res.message.created_at).toLocaleTimeString()}</div>
                      </div>
                    </div>`);
                box.scrollTop = box.scrollHeight;
            });
        }
    }
}

// Filter chats by search
document.getElementById('chatSearch')?.addEventListener('input', function(){
    const q = this.value.trim().toLowerCase();
    document.querySelectorAll('#chatList .chat-profile').forEach(el => {
        el.style.display = !q || el.dataset.name.includes(q) ? '' : 'none';
    });
});

// Attachment, camera, and emoji handling for dashboard chat
(function(){
  const fileInput = document.getElementById('dashChatFile');
  const attachBtn = document.getElementById('dashAttachBtn');
  const cameraBtn = document.getElementById('dashCameraBtn');
  const emojiBtn = document.getElementById('dashEmojiBtn');
  const emojiPanel = document.getElementById('dashEmojiPanel');
  const input = document.getElementById('chatInput');
  const sendBtn = document.getElementById('dashShareBtn');
  // recorder
  let dashAudioStream = null;
  let dashMediaRecorder = null;
  let dashAudioChunks = [];

  let pendingFile = null;
  let pendingUrl = null;
  const pendingWrap = document.getElementById('dashPendingAttachment');
  const pendingThumb = document.getElementById('dashPendingThumb');
  const pendingName = document.getElementById('dashPendingName');
  const clearPendingBtn = document.getElementById('dashClearPending');

  function showPending(file, url) {
    if (!pendingWrap) return;
    pendingWrap.classList.remove('d-none');
    pendingName.textContent = file?.name || 'attachment';
    const type = (file?.type || '').toLowerCase();
    if (type.startsWith('image/')) {
      pendingThumb.innerHTML = `<img src="${url}" alt="preview" style="max-height:60px;border-radius:6px">`;
    } else if (type.startsWith('video/')) {
      pendingThumb.innerHTML = `<video src="${url}" style="max-height:60px;border-radius:6px" muted></video>`;
    } else {
      pendingThumb.innerHTML = '📄';
    }
  }
  function clearPending() {
    try{ if (pendingUrl) URL.revokeObjectURL(pendingUrl); }catch{}
    pendingUrl = null; pendingFile = null;
    if (!pendingWrap) return;
    pendingWrap.classList.add('d-none');
    pendingThumb.innerHTML = '';
    pendingName.textContent = '';
    if (fileInput) fileInput.value = '';
  }
  clearPendingBtn?.addEventListener('click', function(e){ e.preventDefault(); clearPending(); });

  attachBtn?.addEventListener('click', function(e){ e.preventDefault(); fileInput?.click(); });
  fileInput?.addEventListener('change', function(){
    const file = this.files && this.files[0];
    if (!file) return;
    clearPending();
    pendingFile = file;
    pendingUrl = URL.createObjectURL(file);
    showPending(file, pendingUrl);
  });

  emojiBtn?.addEventListener('click', function(e){
    e.preventDefault();
    if (!emojiPanel) return;
    const isOpen = !emojiPanel.classList.contains('d-none');
    emojiPanel.classList.toggle('d-none');
    emojiBtn.setAttribute('aria-expanded', String(!isOpen));
  });
  emojiPanel?.addEventListener('click', function(e){
    const target = e.target;
    if (target && target.classList && target.classList.contains('dash-emoji-btn')) {
        if (input) { input.value = (input.value || '') + target.textContent; input.focus(); }
    }
  });
  document.addEventListener('click', function(e){
    if (!emojiPanel || emojiPanel.classList.contains('d-none')) return;
    const within = emojiPanel.contains(e.target) || emojiBtn.contains(e.target);
    if (!within) { emojiPanel.classList.add('d-none'); emojiBtn.setAttribute('aria-expanded','false'); }
  });

  cameraBtn?.addEventListener('click', async function(e){
    e.preventDefault();
    if (!activeChatId) return;
    let stream;
    try { stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false }); }
    catch(err){ console.warn('Camera not available', err); return; }
    const overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.8);z-index:2000;display:flex;align-items:center;justify-content:center;';
    overlay.innerHTML = `
      <div style=\"background:#111;padding:16px;border-radius:12px;max-width:640px;width:95%;text-align:center;\">
        <video id=\"dashCamVideo\" autoplay playsinline style=\"max-width:100%;border-radius:8px;\"></video>
        <div class=\"mt-3 d-flex gap-2 justify-content-center\">
          <button id=\"dashSnapBtn\" class=\"btn btn-primary\">Capture</button>
          <button id=\"dashCancelCam\" class=\"btn btn-secondary\">Cancel</button>
        </div>
      </div>`;
    document.body.appendChild(overlay);
    const video = overlay.querySelector('#dashCamVideo'); video.srcObject = stream;
    const cleanup = () => { try{ stream.getTracks().forEach(t=>t.stop()); }catch{} overlay.remove(); };
    overlay.querySelector('#dashCancelCam').onclick = cleanup;
    overlay.querySelector('#dashSnapBtn').onclick = () => {
      const canvas = document.createElement('canvas');
      canvas.width = video.videoWidth; canvas.height = video.videoHeight;
      const ctx = canvas.getContext('2d'); ctx.drawImage(video, 0, 0);
      canvas.toBlob(function(blob){
        if (!blob) return cleanup();
        clearPending();
        const f = new File([blob], 'camera.jpg', { type: 'image/jpeg' });
        const url = URL.createObjectURL(blob);
        pendingFile = f; pendingUrl = url; showPending(f, url);
        cleanup();
      }, 'image/jpeg');
    };
  });

  // Voice recording toggle on the audio button
  const audioBtn = document.getElementById('dashAudioBtn');
  audioBtn?.addEventListener('click', async function(e){
    e.preventDefault();
    const btn = audioBtn;
    try {
      if (dashMediaRecorder && dashMediaRecorder.state === 'recording') {
        dashMediaRecorder.stop();
        btn.classList.remove('btn-danger');
        return;
      }
      dashAudioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
      dashAudioChunks = [];
      dashMediaRecorder = new MediaRecorder(dashAudioStream);
      dashMediaRecorder.ondataavailable = ev => { if (ev.data && ev.data.size) dashAudioChunks.push(ev.data); };
      dashMediaRecorder.onstop = () => {
        try { dashAudioStream.getTracks().forEach(t=>t.stop()); } catch{}
        const blob = new Blob(dashAudioChunks, { type: 'audio/webm' });
        if (blob.size > 0) {
          clearPending();
          const file = new File([blob], 'voice-message.webm', { type: 'audio/webm' });
          const url = URL.createObjectURL(blob);
          pendingFile = file; pendingUrl = url; showPending(file, url);
        }
      };
      dashMediaRecorder.start();
      btn.classList.add('btn-danger');
    } catch (err) {
      console.warn('Audio recording not available', err);
    }
  });

  // Simple lightbox for dashboard images
  const overlayId = 'dashImgLightboxOverlay';
  function ensureOverlay(){
    let d = document.getElementById(overlayId);
    if (d) return d;
    d = document.createElement('div');
    d.id = overlayId;
    d.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.92);display:none;align-items:center;justify-content:center;z-index:3000;';
    d.innerHTML = '<img alt="preview" style="max-width:95%;max-height:95%;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.6)" />';
    d.addEventListener('click', () => { d.style.display = 'none'; });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') d.style.display='none'; });
    document.body.appendChild(d);
    return d;
  }
  document.addEventListener('click', function(e){
    const a = e.target.closest('.dash-img-lightbox');
    if (!a) return;
    e.preventDefault();
    const url = a.getAttribute('data-url') || a.getAttribute('href');
    const ov = ensureOverlay();
    ov.querySelector('img').src = url;
    ov.style.display = 'flex';
  });

  function sendDashMessage() {
    if (!activeChatId) return;
    const text = (input?.value || '').trim();
    if (pendingFile) {
      const fd = new FormData();
      fd.append('chat_id', activeChatId);
      fd.append('attachment', pendingFile);
      if (text) fd.append('message', text);
      fetch(CHAT_ATTACHMENT_URL, {
        method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: fd
      }).then(r=>r.json()).then(()=>{
        if (input) input.value = '';
        clearPending();
        fetch(`/player/chat/messages/${activeChatId}`).then(r=>r.json()).then(renderMessages);
      });
    } else if (text) {
      fetch(`{{ route('player.chat.send') }}`, {
        method: 'POST', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ chat_id: activeChatId, message: text })
      }).then(r=>r.json()).then(()=>{
        if (input) input.value='';
        fetch(`/player/chat/messages/${activeChatId}`).then(r=>r.json()).then(renderMessages);
      });
    }
  }

  sendBtn?.addEventListener('click', function(e){ e.preventDefault(); sendDashMessage(); });
  input?.addEventListener('keydown', function(ev){ if (ev.key === 'Enter') { ev.preventDefault(); sendDashMessage(); } });
})();

// Accessibility: ensure focus is moved out of the modal before it's hidden
document.addEventListener('DOMContentLoaded', function(){
    const modalEl = document.getElementById('staticBackdrop-four');
    if (!modalEl) return;

    // When the modal opens, focus the chat input for convenience
    modalEl.addEventListener('shown.bs.modal', function(){
        const input = document.getElementById('chatInput');
        if (input) setTimeout(() => input.focus(), 0);
    });

    // Before Bootstrap sets aria-hidden on the modal, move focus elsewhere
    modalEl.addEventListener('hide.bs.modal', function(){
        // Prefer to return focus to the opener; otherwise, to the search field
        const fallback = document.getElementById('chatSearch')
            || document.querySelector('#chatList .chat-profile button')
            || document.querySelector('a, button, input, [tabindex]:not([tabindex="-1"])');
        const target = lastChatTrigger || fallback;
        if (target && typeof target.focus === 'function') {
            // Delay a tick to ensure assistive tech registers focus outside hidden region
            setTimeout(() => target.focus(), 0);
        }
    });
});
</script>
