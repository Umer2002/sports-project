import React, { useEffect, useMemo, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';
import axios from 'axios';

function useInterval(callback, delay) {
  const savedCallback = useRef();
  useEffect(() => { savedCallback.current = callback; }, [callback]);
  useEffect(() => {
    if (delay === null) return;
    const id = setInterval(() => savedCallback.current && savedCallback.current(), delay);
    return () => clearInterval(id);
  }, [delay]);
}

function MessageList({ messages, currentUserId }) {
  const boxRef = useRef(null);
  useEffect(() => {
    if (boxRef.current) {
      boxRef.current.scrollTop = boxRef.current.scrollHeight;
    }
  }, [messages]);
  return (
    <div ref={boxRef} style={{ maxHeight: 450, overflowY: 'auto' }} className="card-body p-3">
      {messages.length === 0 && (
        <div className="text-muted">No messages yet.</div>
      )}
      {messages.map(m => {
        const mine = Number(m.sender_id) === Number(currentUserId);
        return (
          <div key={m.id || `${m.created_at}-${Math.random()}`} className="clearfix mb-3">
            <div className={`d-flex ${mine ? 'justify-content-end' : 'justify-content-start'}`}>
              <div className={`p-2 rounded-4 ${mine ? 'bg-primary text-white' : 'bg-secondary text-white'}`} style={{ maxWidth: '75%' }}>
                <strong>{m.user?.name || (mine ? 'Me' : 'User')}</strong><br />
                <span className="d-block mt-1">{m.content}</span>
                <small className="d-block text-end mt-2" style={{ fontSize: '.75rem' }}>{new Date(m.created_at || Date.now()).toLocaleString()}</small>
              </div>
            </div>
          </div>
        );
      })}
    </div>
  );
}

function InputBar({ onSend, disabled }) {
  const [text, setText] = useState('');
  const submit = (e) => {
    e?.preventDefault();
    const t = text.trim();
    if (!t) return;
    onSend(t).then(() => setText(''));
  };
  return (
    <form onSubmit={submit} className="card-footer border-top p-2 d-flex gap-2">
      <input
        type="text"
        className="form-control"
        placeholder="Type a message..."
        value={text}
        onChange={(e) => setText(e.target.value)}
        disabled={disabled}
      />
      <button type="submit" className="btn btn-primary" disabled={disabled || !text.trim()}>Send</button>
    </form>
  );
}

function TeamChatApp({ teamId }) {
  const [chatId, setChatId] = useState(null);
  const [messages, setMessages] = useState([]);
  const [loading, setLoading] = useState(true);
  const [currentUserId, setCurrentUserId] = useState(window.__authUserId || null);

  // Bootstrap current user id if available via meta tag
  useEffect(() => {
    if (!currentUserId) {
      const meta = document.querySelector('meta[name="user-id"]');
      if (meta) setCurrentUserId(meta.content);
    }
  }, [currentUserId]);

  const msgsUrl = useMemo(() => `/player/teams/${teamId}/chat/messages`, [teamId]);

  const fetchMessages = async () => {
    const res = await axios.get(msgsUrl);
    setChatId(res.data.chat_id);
    setMessages(res.data.messages || []);
    setLoading(false);
  };

  useEffect(() => { fetchMessages(); }, [msgsUrl]);

  // Polling fallback every 4s
  useInterval(() => { fetchMessages().catch(() => {}); }, 4000);

  // If Laravel Echo is globally available, join presence channel for instant updates
  useEffect(() => {
    if (!chatId || !window.Echo) return;
    try {
      const channel = window.Echo.join(`chat.${chatId}`)
        .listen('.message.sent', (e) => {
          setMessages((prev) => [...prev, e]);
        });
      return () => { try { channel?.leave?.(); } catch (_) {} };
    } catch (e) {
      // no-op if Echo not configured
    }
  }, [chatId]);

  const sendMessage = async (content) => {
    const res = await axios.post(msgsUrl, { message: content }, {
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    });
    // Optimistic append
    setMessages((prev) => [...prev, res.data.message]);
  };

  return (
    <div className="card">
      <div className="card-header">Live Team Chat</div>
      {loading ? (
        <div className="p-3">Loading…</div>
      ) : (
        <>
          <MessageList messages={messages} currentUserId={currentUserId} />
          <InputBar onSend={sendMessage} disabled={!chatId} />
        </>
      )}
    </div>
  );
}

function boot() {
  const mount = document.getElementById('team-chat-root');
  if (!mount) return;
  const teamId = mount.getAttribute('data-team-id');
  const root = createRoot(mount);
  root.render(<TeamChatApp teamId={teamId} />);
}

boot();

