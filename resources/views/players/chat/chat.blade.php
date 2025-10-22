@extends('layouts.default')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Chat List -->
        <div class="col-lg-2 mb-4">
            @include('players.partials.sidebar')
        </div>
        <div class="col-lg-2">
            <div class="card bg-dark text-white shadow rounded-4 h-100">
                <div class="card-header border-bottom border-secondary">
                    <h5 class="mb-0">💬 Chats</h5>
                </div>
                <div class="card-body overflow-auto p-3" style="max-height: 550px;">
                    @foreach ($chats as $chat)
                        @php
                            // participants is a collection of App\Models\User
                            $otherUser = $chat->participants->firstWhere('id', '!=', $user->id);
                        @endphp
                        @if($otherUser)
                        <a href="{{ route('player.chat', ['chat_id' => $chat->id]) }}" class="text-decoration-none">
                            <div class="d-flex align-items-center mb-3 p-2 rounded-3 chat-item {{ $activeChatId == $chat->id ? 'active-chat' : '' }}">
                                <img src="{{ asset($otherUser->avatar ?? 'images/default.png') }}" alt="Avatar" width="45" height="45" class="rounded-circle me-3 border border-secondary">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $otherUser->name ?? $otherUser->first_name ?? 'User' }}</div>
                                    <small class="text-muted">{{ Str::limit($chat->messages->first()?->content ?? 'No messages yet.', 25) }}</small>
                                </div>
                            </div>
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Chat Box -->
        <div class="col-lg-8">
            <div class="card bg-dark text-white shadow rounded-4 h-100 d-flex flex-column">
                <div class="card-header border-bottom border-secondary">
                    <h5 class="mb-0">📨 Messages</h5>
                </div>
                <div class="card-body overflow-auto p-4" id="chat-box" style="height: 450px;">
                    @if ($messages->isEmpty())
                        <div class="text-center mt-5">
                            <h5 class="text-muted">Select a chat to start messaging</h5>
                        </div>
                    @else
                        @foreach ($messages as $msg)
                            <div class="clearfix mb-4">
                                <div class="d-flex {{ $msg->sender_id == $user->id ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="p-3 rounded-4 {{ $msg->sender_id == $user->id ? 'bg-primary text-white' : 'bg-secondary text-white' }}" style="max-width: 75%;">
                                        <strong>{{ $msg->user->first_name ?? $msg->user->name ?? 'Unknown' }}</strong><br>
                                        @if(!empty($msg->content))
                                            <span class="d-block mt-1">{{ $msg->content }}</span>
                                        @endif
                                        @if($msg->attachment_path)
                                            @php $type = strtolower($msg->attachment_type ?? ''); @endphp
                                            @if(strpos($type, 'image/') === 0)
                                                <div class="mt-2"><a href="{{ $msg->attachment_url }}" class="img-lightbox" data-url="{{ $msg->attachment_url }}"><img src="{{ $msg->attachment_url }}" alt="image" class="img-fluid rounded" style="max-height:220px"></a></div>
                                            @elseif(strpos($type, 'video/') === 0)
                                                <div class="mt-2"><video src="{{ $msg->attachment_url }}" controls class="w-100 rounded" style="max-height:260px"></video></div>
                                            @else
                                                <div class="mt-2"><a href="{{ $msg->attachment_url }}" target="_blank" class="link-light">{{ basename($msg->attachment_path) }}</a></div>
                                            @endif
                                        @endif
                                        <small class="d-block text-end mt-2" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($msg->created_at)->format('d M, h:i A') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                @if ($activeChatId)
                <div class="card-footer border-top border-secondary">
                    <form id="sendMessageForm" class="d-flex align-items-center gap-2 position-relative" autocomplete="off" enctype="multipart/form-data">
                        <input type="hidden" id="chat_id" value="{{ $activeChatId }}">
                        <button type="button" id="emojiBtn" class="btn btn-outline-secondary" aria-haspopup="true" aria-expanded="false" title="Emoji">😊</button>
                        <input type="file" id="fileInput" class="d-none" accept="image/*,video/*,audio/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt">
                        <button type="button" id="attachBtn" class="btn btn-outline-secondary" title="Attach file" aria-label="Attach">📎</button>
                        <button type="button" id="cameraBtn" class="btn btn-outline-secondary" title="Camera" aria-label="Camera">📷</button>
                        <button type="button" id="audioBtn" class="btn btn-outline-secondary" title="Voice Recorder" aria-label="Voice Recorder">🎙️</button>
                        <input type="text" id="message_content" class="form-control bg-dark text-white" placeholder="Type a message...">
                        <button type="submit" class="btn btn-primary px-4">Send</button>
                    </form>
                    <div id="pendingAttachment" class="mt-2 d-none">
                        <div class="d-flex align-items-center gap-2">
                            <div id="pendingThumb"></div>
                            <div class="small text-muted" id="pendingName"></div>
                            <button type="button" id="clearPending" class="btn btn-sm btn-outline-light">Remove</button>
                        </div>
                    </div>
                    <div id="emojiPanel" class="bg-dark border border-secondary rounded p-2 d-none" role="dialog" aria-label="Emoji picker" style="position:absolute; bottom:54px; left:8px; max-width:280px; box-shadow:0 8px 24px rgba(0,0,0,.4); z-index:2000;">
                        <div style="display:grid; grid-template-columns: repeat(8, 1fr); gap:6px; font-size:20px; line-height:1;">
                            @php($emojis = ['😀','😁','😂','🤣','😊','😍','😘','😎','😇','🙂','😉','😅','🤩','🥳','🤗','🤔','🙄','😴','🤤','😤','😭','😡','👍','👎','🙏','👏','🙌','💪','🔥','💯','🎉','✨','❤️','💙','💚','💛','💜','🖤','🤍','⚽','🏀','🏈','⚾','🎾','🏐'])
                            @foreach($emojis as $e)
                                <button type="button" class="emoji" style="background:none;border:none;color:#fff;cursor:pointer">{{ $e }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .chat-item {
        transition: background 0.3s;
    }
    .chat-item:hover {
        background-color: #2c2c2c;
    }
    .active-chat {
        background-color: #0d6efd !important;
    }
    #chat-box {
        scroll-behavior: smooth;
    }
</style>
@endpush

@push('scripts')
<script>
$(function() {
    const ATTACH_URL = "{{ \Illuminate\Support\Facades\Route::has('player.chat.attachment') ? route('player.chat.attachment') : url('/player/chat/attachment') }}";
    const chatId = $('#chat_id').val();

    function escapeHtml(str){
        return String(str ?? '')
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/\"/g,'&quot;')
            .replace(/'/g,'&#039;');
    }

    function appendMessage(msg) {
        const mine = Number(msg.sender_id) === Number({{ (int) $user->id }});
        const bubbleClass = mine ? 'bg-primary text-white' : 'bg-secondary text-white';
        const sideClass = mine ? 'justify-content-end' : 'justify-content-start';
        const when = new Date(msg.created_at || Date.now());
        const time = when.toLocaleString();
        let attachmentHtml = '';
        if (msg.attachment_url || msg.attachment_path) {
            const url = msg.attachment_url || msg.attachment_path;
            const type = (msg.attachment_type || '').toLowerCase();
            if (type.startsWith('image/')) {
                attachmentHtml = `<div class="mt-2"><a href="${url}" class="img-lightbox" data-url="${url}"><img src="${url}" alt="image" class="img-fluid rounded" style="max-height:220px"></a></div>`;
            } else if (type.startsWith('video/')) {
                attachmentHtml = `<div class="mt-2"><video src="${url}" controls class="w-100 rounded" style="max-height:260px"></video></div>`;
            } else {
                const name = url.split('/').pop();
                attachmentHtml = `<div class="mt-2"><a href="${url}" target="_blank" class="link-info">${name}</a></div>`;
            }
        }
        const html = `
            <div class="clearfix mb-4">
              <div class="d-flex ${sideClass}">
                <div class="p-3 rounded-4 ${bubbleClass}" style="max-width: 75%;">
                  <strong>${escapeHtml((msg.user && msg.user.name) || 'Me')}</strong><br>
                  <span class="d-block mt-1">${escapeHtml(msg.content || '')}</span>
                  ${attachmentHtml}
                  <small class="d-block text-end mt-2" style="font-size: 0.75rem;">${time}</small>
                </div>
              </div>
            </div>`;
        $('#chat-box').append(html).scrollTop($('#chat-box')[0].scrollHeight);
    }

    // Pending attachment state and helpers
    let pendingFile = null;
    let pendingUrl = null;
    function showPending(file, url) {
        $('#pendingAttachment').removeClass('d-none');
        const name = file?.name || 'attachment';
        $('#pendingName').text(name);
        const type = (file?.type || '').toLowerCase();
        if (type.startsWith('image/')) {
            $('#pendingThumb').html(`<img src="${url}" alt="preview" style="max-height:60px;border-radius:6px">`);
        } else if (type.startsWith('video/')) {
            $('#pendingThumb').html(`<video src="${url}" style="max-height:60px;border-radius:6px" muted></video>`);
        } else if (type.startsWith('audio/')) {
            $('#pendingThumb').html(`<audio src="${url}" controls style="max-width:260px"></audio>`);
        } else {
            $('#pendingThumb').html('📄');
        }
    }
    function clearPending() {
        if (pendingUrl) URL.revokeObjectURL(pendingUrl);
        pendingUrl = null; pendingFile = null;
        $('#pendingAttachment').addClass('d-none');
        $('#pendingThumb').empty();
        $('#pendingName').empty();
        $('#fileInput').val('');
    }
    $('#clearPending').on('click', clearPending);
    $('#attachBtn').on('click', function(){ $('#fileInput').click(); });
    $('#fileInput').on('change', function(){
        const f = this.files && this.files[0];
        if (!f) return;
        clearPending();
        pendingFile = f;
        pendingUrl = URL.createObjectURL(f);
        showPending(f, pendingUrl);
    });

    // Submit send (supports queued file attachments)
    $('#sendMessageForm').submit(function(e) {
        e.preventDefault();
        const message = $('#message_content').val();
        const file = pendingFile;

        if (file) {
            const fd = new FormData();
            fd.append('chat_id', chatId);
            fd.append('attachment', file);
            if (message.trim()) fd.append('message', message.trim());
            $.ajax({
                url: ATTACH_URL,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                processData: false,
                contentType: false,
                data: fd,
                success: function(response) {
                    $('#message_content').val('');
                    clearPending();
                    const m = response.message;
                    m.attachment_url = m.attachment_url || m.attachment_path;
                    appendMessage(m);
                },
                error: function(xhr) { console.error('Attachment send failed', xhr); }
            });
        } else {
            if (!message.trim()) return;
            $.ajax({
                url: '{{ route('player.chat.send') }}',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                contentType: 'application/json',
                data: JSON.stringify({ chat_id: chatId, message: message }),
                success: function(response) {
                    $('#message_content').val('');
                    appendMessage(response.message);
                },
                error: function(xhr) { console.error('Text send failed', xhr); }
            });
        }
    });

    // Realtime via Echo if available
    if (window.Echo && chatId) {
        try {
            window.Echo.join(`chat.${chatId}`)
                .here(() => {})
                .listen('.message.sent', (e) => {
                    appendMessage(e);
                });
        } catch (e) {
            console.warn('Echo join failed:', e);
        }
    } else if (chatId) {
        // Fallback: poll every 5s
        let lastCount = {{ $messages->count() }};
        setInterval(() => {
            $.getJSON(`/chat/messages/${chatId}`, function(list) {
                if (list.length > lastCount) {
                    // append only new
                    for (let i = lastCount; i < list.length; i++) appendMessage(list[i]);
                    lastCount = list.length;
                }
            });
        }, 5000);
    }
  // Simple image lightbox
  const overlayId = 'imgLightboxOverlay';
  function ensureOverlay(){
    if (document.getElementById(overlayId)) return document.getElementById(overlayId);
    const d = document.createElement('div');
    d.id = overlayId;
    d.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.9);display:none;align-items:center;justify-content:center;z-index:3000;';
    d.innerHTML = '<img alt="preview" style="max-width:95%;max-height:95%;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.6)" />';
    d.addEventListener('click', () => { d.style.display = 'none'; });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') d.style.display='none'; });
    document.body.appendChild(d);
    return d;
  }
  $(document).on('click', '.img-lightbox', function(e){
    e.preventDefault();
    const url = this.getAttribute('data-url') || this.getAttribute('href');
    const ov = ensureOverlay();
    ov.querySelector('img').src = url;
    ov.style.display = 'flex';
  });
});
</script>
<script>
// Emoji, attachment, and camera handlers
$(function(){
  const $emojiPanel = $('#emojiPanel');
  $('#emojiBtn').on('click', function(){
    const isOpen = !$emojiPanel.hasClass('d-none');
    $emojiPanel.toggleClass('d-none');
    $(this).attr('aria-expanded', String(!isOpen));
  });
  $emojiPanel.on('click', '.emoji', function(){
    const ch = $(this).text();
    const $input = $('#message_content');
    $input.val(($input.val() || '') + ch).focus();
  });
  $(document).on('click', function(e){
    const t = e.target;
    if (!$emojiPanel.hasClass('d-none') && !$(t).closest('#emojiPanel, #emojiBtn').length) {
      $emojiPanel.addClass('d-none');
      $('#emojiBtn').attr('aria-expanded', 'false');
    }
  });
  let stream;
  $('#cameraBtn').on('click', async function(){
    try {
      stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
    } catch(e) { console.warn('Camera not available', e); return; }
    const overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.8);z-index:1055;display:flex;align-items:center;justify-content:center;';
    overlay.innerHTML = `
      <div style="background:#111;padding:16px;border-radius:12px;max-width:640px;width:95%;text-align:center;">
        <video id="camVideo" autoplay playsinline style="max-width:100%;border-radius:8px;"></video>
        <div class="mt-3 d-flex gap-2 justify-content-center">
          <button id="snapBtn" class="btn btn-primary">Capture</button>
          <button id="cancelCam" class="btn btn-secondary">Cancel</button>
        </div>
      </div>`;
    document.body.appendChild(overlay);
    const video = overlay.querySelector('#camVideo');
    video.srcObject = stream;
    const cleanup = () => { try{ stream.getTracks().forEach(t=>t.stop()); }catch{} overlay.remove(); };
    overlay.querySelector('#cancelCam').onclick = cleanup;
    overlay.querySelector('#snapBtn').onclick = () => {
      const canvas = document.createElement('canvas');
      canvas.width = video.videoWidth; canvas.height = video.videoHeight;
      const ctx = canvas.getContext('2d'); ctx.drawImage(video, 0, 0);
      canvas.toBlob(function(blob){
        if (!blob) return cleanup();
        // Queue the snapshot; send when the user clicks Send
        clearPending();
        const f = new File([blob], 'camera.jpg', { type: 'image/jpeg' });
        const url = URL.createObjectURL(blob);
        pendingFile = f; pendingUrl = url; showPending(f, url);
        cleanup();
      }, 'image/jpeg');
    };
  });

  // Voice recording using MediaRecorder
  let audioStream = null;
  let mediaRecorder = null;
  let audioChunks = [];
  $('#audioBtn').on('click', async function(){
    const $btn = $(this);
    try {
      // If already recording, stop and queue the blob
      if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
        $btn.removeClass('btn-danger').addClass('btn-outline-secondary').text('🎙️');
        return;
      }

      // Start recording
      audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
      audioChunks = [];
      mediaRecorder = new MediaRecorder(audioStream);
      mediaRecorder.ondataavailable = e => { if (e.data && e.data.size) audioChunks.push(e.data); };
      mediaRecorder.onstop = () => {
        try { audioStream.getTracks().forEach(t=>t.stop()); } catch{}
        const blob = new Blob(audioChunks, { type: 'audio/webm' });
        if (blob.size > 0) {
          clearPending();
          const file = new File([blob], 'voice-message.webm', { type: 'audio/webm' });
          const url = URL.createObjectURL(blob);
          pendingFile = file; pendingUrl = url; showPending(file, url);
        }
      };
      mediaRecorder.start();
      $btn.removeClass('btn-outline-secondary').addClass('btn-danger').text('■');
    } catch (e) {
      console.warn('Audio recording not available', e);
    }
  });
});
</script>
@endpush
