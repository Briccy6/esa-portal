@extends('layouts.app')
@section('title', 'Live Class — ' . $liveClass->title)

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:#0f172a;color:#f1f5f9;overflow:hidden;height:100vh}
.room{height:100vh;display:flex;flex-direction:column}
/* Header */
.rh{height:60px;background:#1e293b;border-bottom:1px solid #334155;display:flex;align-items:center;justify-content:space-between;padding:0 20px;flex-shrink:0;gap:14px}
.live-pill{display:inline-flex;align-items:center;gap:6px;background:#dc2626;color:#fff;padding:4px 11px;border-radius:20px;font-size:10.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
.ldot{width:7px;height:7px;border-radius:50%;background:#fff;animation:ldot 1.8s infinite}
@keyframes ldot{0%,100%{opacity:1}50%{opacity:.3}}
.r-title{font-size:15px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.timer{font-family:monospace;font-size:13px;color:#94a3b8;padding:4px 10px;background:#0f172a;border:1px solid #334155;border-radius:7px}
/* Body */
.rb{flex:1;min-height:0;display:flex;overflow:hidden}
/* Video area */
.stage{flex:1;display:flex;flex-direction:column;min-width:0}
.varea{flex:1;background:#000;position:relative;display:flex;align-items:center;justify-content:center;overflow:hidden;min-height:0}
#remoteVideo{width:100%;height:100%;object-fit:contain;background:#000;display:block}
/* Waiting overlay */
.waiting{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:16px;text-align:center;padding:20px;background:rgba(0,0,0,.85)}
.waiting i{font-size:52px;opacity:.7;color:#dc2626}
.waiting h3{font-size:20px;font-weight:600}
.waiting p{font-size:14px;color:#94a3b8;max-width:300px;line-height:1.5}
.conn-dot{display:inline-block;width:10px;height:10px;border-radius:50%;background:#f59e0b;animation:pulse 1.5s infinite;margin-right:6px}
.conn-dot.connected{background:#10b981;animation:none}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
/* Controls bar */
.ctrl{height:60px;background:#1e293b;border-top:1px solid #334155;display:flex;align-items:center;justify-content:center;gap:10px;flex-shrink:0;padding:0 16px}
.cb{background:#334155;border:1px solid #475569;color:#e2e8f0;padding:8px 14px;border-radius:9px;cursor:pointer;font-size:12.5px;font-weight:500;display:inline-flex;align-items:center;gap:6px;transition:all .18s;font-family:inherit}
.cb:hover{background:#475569;color:#fff}
.cb.on{background:rgba(220,38,38,.25);border-color:#dc2626;color:#fff}
.cb.danger{background:rgba(220,38,38,.15);border-color:rgba(220,38,38,.4);color:#f87171}
.cb.danger:hover{background:#dc2626;color:#fff}
/* Sidebar */
.sidebar{width:320px;background:#1e293b;border-left:1px solid #334155;display:flex;flex-direction:column;flex-shrink:0}
.s-tabs{display:flex;border-bottom:1px solid #334155;flex-shrink:0}
.s-tab{flex:1;padding:13px 8px;background:transparent;border:none;color:#64748b;cursor:pointer;font-size:12px;font-weight:600;border-bottom:2px solid transparent;display:flex;align-items:center;justify-content:center;gap:5px;font-family:inherit;transition:all .18s}
.s-tab.active{color:#f1f5f9;border-bottom-color:#dc2626}
.s-body{flex:1;overflow-y:auto;overflow-x:hidden;padding:14px}
.s-body::-webkit-scrollbar{width:4px}
.s-body::-webkit-scrollbar-thumb{background:#334155;border-radius:2px}
.s-panel{display:none}
.s-panel.active{display:block}
/* Chat */
.chat-wrap{display:flex;flex-direction:column;gap:10px}
.chat-msgs{max-height:calc(100vh - 260px);overflow-y:auto;display:flex;flex-direction:column;gap:7px;padding-bottom:4px}
.chat-msg{background:#0f172a;border:1px solid #334155;border-radius:9px;padding:9px 11px}
.chat-msg.mine{background:rgba(220,38,38,.1);border-color:rgba(220,38,38,.3)}
.cm-author{font-size:11px;font-weight:600;color:#dc2626;margin-bottom:3px}
.cm-body{font-size:12.5px;color:#e2e8f0;line-height:1.45}
.chat-row{display:flex;gap:7px}
.chat-in{flex:1;background:#0f172a;border:1px solid #334155;border-radius:7px;padding:8px 11px;color:#f1f5f9;font-family:inherit;font-size:12.5px;outline:none;transition:border-color .18s}
.chat-in:focus{border-color:#dc2626}
.chat-send{background:#dc2626;border:none;border-radius:7px;padding:8px 13px;color:#fff;cursor:pointer;font-size:13px}
.chat-send:hover{background:#b91c1c}
/* Participants */
.ptcp{display:flex;align-items:center;gap:9px;padding:9px 0;border-bottom:1px solid #334155}
.ptcp:last-child{border-bottom:none}
.ptcp-av{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#dc2626,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;flex-shrink:0}
.ptcp-name{font-size:12.5px;font-weight:600;color:#f1f5f9}
.ptcp-role{font-size:11px;color:#94a3b8}
.hand-badge{background:#f59e0b;color:#000;font-size:10px;font-weight:700;padding:2px 6px;border-radius:8px;margin-left:4px}
/* Empty */
.s-empty{text-align:center;padding:36px 16px;color:#475569}
.s-empty-icon{font-size:28px;margin-bottom:10px}
.s-empty-text{font-size:12px;line-height:1.5}
/* Hand raised notification */
.notif{position:fixed;top:70px;right:20px;background:#1e293b;border:1px solid #334155;border-radius:10px;padding:12px 18px;font-size:13px;z-index:999;transform:translateX(120%);transition:transform .3s;box-shadow:0 4px 20px rgba(0,0,0,.4)}
.notif.show{transform:translateX(0)}
@media(max-width:768px){.sidebar{display:none}}
</style>
@endsection

@section('content')
<div class="room">
  <div class="rh">
    <div style="display:flex;align-items:center;gap:12px;min-width:0">
      <div class="live-pill"><div class="ldot"></div>Live</div>
      <div>
        <div class="r-title">{{ $liveClass->title }}</div>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
      <span style="font-size:12px;color:#94a3b8">👥 <span id="ptcpCount">1</span></span>
      <div class="timer" id="timer">00:00:00</div>
    </div>
  </div>

  <div class="rb">
    <div class="stage">
      <div class="varea">
        <video id="remoteVideo" autoplay playsinline></video>
        <div class="waiting" id="waiting">
          <div>📡</div>
          <h3>Waiting for instructor…</h3>
          <p>The live class will begin shortly. Please stay connected.</p>
          <div><span class="conn-dot" id="connDot"></span><span id="connText">Connecting…</span></div>
        </div>
      </div>
      <div class="ctrl">
        <button class="cb on" id="handBtn" onclick="toggleHand()">✋ Raise Hand</button>
        <button class="cb" onclick="switchTab('chat')">💬 Chat</button>
        <button class="cb" onclick="switchTab('people')">👥 People</button>
        <button class="cb danger" id="leaveBtn" onclick="leaveClass()">↩ Leave</button>
      </div>
    </div>

    <div class="sidebar">
      <div class="s-tabs">
        <button class="s-tab active" data-tab="chat" onclick="switchTab('chat')">💬 Chat</button>
        <button class="s-tab" data-tab="people" onclick="switchTab('people')">👥 People</button>
      </div>
      <div class="s-body">
        <div class="s-panel active" id="panel-chat">
          <div class="chat-wrap">
            <div class="chat-msgs" id="chatMsgs">
              <div class="s-empty"><div class="s-empty-icon">💬</div><div class="s-empty-text">No messages yet</div></div>
            </div>
            <div class="chat-row">
              <input class="chat-in" id="chatIn" placeholder="Type a message…" autocomplete="off">
              <button class="chat-send" id="chatSendBtn">➤</button>
            </div>
          </div>
        </div>
        <div class="s-panel" id="panel-people">
          <div id="peopleList"><div class="s-empty"><div class="s-empty-icon">👥</div><div class="s-empty-text">Loading…</div></div></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="notif" id="notif"></div>
@endsection

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
// ── Config ────────────────────────────────────────────────────────────────────
const LIVE_CLASS_ID = {{ $liveClass->id }};
// Generate a unique peer id for this viewer
const MY_PEER  = 'student_{{ auth()->guard("student")->id() ?? uniqid() }}';
const MY_NAME  = '{{ auth()->guard("student")->user()?->full_name ?? ("Guest_" . substr(uniqid(), -4)) }}';
const SIGNAL_SEND = '/live-signaling/' + LIVE_CLASS_ID + '/send';
const SIGNAL_POLL = '/live-signaling/' + LIVE_CLASS_ID + '/poll';
const CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
const ICE_SERVERS = { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }, { urls: 'stun:stun1.l.google.com:19302' }] };

// ── State ─────────────────────────────────────────────────────────────────────
let pc = null;
let lastSignalId = 0;
let handRaised   = false;
let startedAt    = Date.now();
let participants = {};

// ── Timer ─────────────────────────────────────────────────────────────────────
setInterval(() => {
    const s = Math.floor((Date.now() - startedAt) / 1000);
    document.getElementById('timer').textContent =
        [Math.floor(s/3600), Math.floor((s%3600)/60), s%60].map(n => String(n).padStart(2,'0')).join(':');
}, 1000);

// ── Signal helpers ─────────────────────────────────────────────────────────────
async function sendSignal(toPeer, type, payload) {
    await fetch(SIGNAL_SEND, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ from_peer: MY_PEER, to_peer: toPeer, type, payload })
    });
}

async function pollSignals() {
    try {
        const r = await fetch(`${SIGNAL_POLL}?peer=${MY_PEER}&since=${lastSignalId}`);
        const { signals } = await r.json();
        for (const sig of signals) {
            lastSignalId = Math.max(lastSignalId, sig.id);
            await handleSignal(sig);
        }
    } catch(e) {}
}

async function handleSignal(sig) {
    const { from_peer, type, payload } = sig;

    if (type === 'teacher-present') {
        setConnStatus(true, 'Teacher is live!');
        participants['teacher'] = { name: payload.name || 'Teacher', role: 'Teacher' };
        updatePeoplePanel();
    }

    if (type === 'stream-type') {
        // Teacher switched stream type — UI hint
    }

    if (type === 'offer' && from_peer === 'teacher') {
        await handleOffer(payload);
    }

    if (type === 'ice-candidate' && from_peer === 'teacher') {
        if (pc) { try { await pc.addIceCandidate(new RTCIceCandidate(payload)); } catch(e) {} }
    }

    if (type === 'chat') {
        addChatMsg(payload.name || from_peer, payload.message, false);
    }

    if (type === 'stream-started') {
        setConnStatus(true, 'Stream active');
    }
}

// ── WebRTC ─────────────────────────────────────────────────────────────────────
async function handleOffer(offer) {
    if (pc) { pc.close(); }
    pc = new RTCPeerConnection(ICE_SERVERS);

    pc.ontrack = e => {
        const video = document.getElementById('remoteVideo');
        if (e.streams && e.streams[0]) {
            video.srcObject = e.streams[0];
            document.getElementById('waiting').style.display = 'none';
            setConnStatus(true, 'Stream active');
        }
    };

    pc.onicecandidate = e => {
        if (e.candidate) sendSignal('teacher', 'ice-candidate', e.candidate.toJSON());
    };

    pc.onconnectionstatechange = () => {
        if (pc.connectionState === 'disconnected' || pc.connectionState === 'failed') {
            document.getElementById('waiting').style.display = 'flex';
            setConnStatus(false, 'Stream lost — reconnecting…');
        }
        if (pc.connectionState === 'connected') {
            setConnStatus(true, 'Connected');
        }
    };

    await pc.setRemoteDescription(new RTCSessionDescription(offer));
    const answer = await pc.createAnswer();
    await pc.setLocalDescription(answer);
    await sendSignal('teacher', 'answer', { sdp: answer.sdp, type: answer.type });
}

function setConnStatus(ok, text) {
    document.getElementById('connDot').className = 'conn-dot' + (ok ? ' connected' : '');
    document.getElementById('connText').textContent = text;
    if (ok) document.getElementById('waiting').style.display = 'none';
}

// ── Raise hand ─────────────────────────────────────────────────────────────────
async function toggleHand() {
    handRaised = !handRaised;
    const btn = document.getElementById('handBtn');
    btn.textContent = handRaised ? '✋ Lower Hand' : '✋ Raise Hand';
    btn.className = 'cb' + (handRaised ? ' on' : '');
    await sendSignal('teacher', 'hand-raise', { raised: handRaised, name: MY_NAME });
}

// ── Chat ────────────────────────────────────────────────────────────────────────
document.getElementById('chatSendBtn').addEventListener('click', sendChat);
document.getElementById('chatIn').addEventListener('keydown', e => { if (e.key === 'Enter') sendChat(); });

async function sendChat() {
    const inp = document.getElementById('chatIn');
    const msg = inp.value.trim(); if (!msg) return;
    inp.value = '';
    addChatMsg('You', msg, true);
    await sendSignal('all', 'chat', { name: MY_NAME, message: msg });
}

function addChatMsg(author, msg, mine) {
    const wrap = document.getElementById('chatMsgs');
    wrap.querySelector('.s-empty')?.remove();
    const el = document.createElement('div');
    el.className = 'chat-msg' + (mine ? ' mine' : '');
    el.innerHTML = `<div class="cm-author">${author}</div><div class="cm-body">${msg}</div>`;
    wrap.appendChild(el);
    wrap.scrollTop = wrap.scrollHeight;
    switchTab('chat');
}

// ── People panel ────────────────────────────────────────────────────────────────
function updatePeoplePanel() {
    const list = document.getElementById('peopleList');
    const keys = Object.keys(participants);
    document.getElementById('ptcpCount').textContent = keys.length;
    list.innerHTML = keys.map(pid => {
        const p = participants[pid];
        return `<div class="ptcp">
            <div class="ptcp-av">${(p.name||'?')[0].toUpperCase()}</div>
            <div><div class="ptcp-name">${p.name||pid}</div><div class="ptcp-role">${p.role||'Student'}</div></div>
        </div>`;
    }).join('') || '<div class="s-empty"><div class="s-empty-icon">👥</div><div class="s-empty-text">No one here yet</div></div>';
}

// ── Sidebar tabs ────────────────────────────────────────────────────────────────
function switchTab(name) {
    document.querySelectorAll('.s-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === name));
    document.querySelectorAll('.s-panel').forEach(p => p.classList.toggle('active', p.id === 'panel-' + name));
}

// ── Leave ───────────────────────────────────────────────────────────────────────
async function leaveClass() {
    if (!confirm('Leave this live class?')) return;
    await sendSignal('teacher', 'leave', { name: MY_NAME });
    window.location.href = '/';
}

// ── Notification ────────────────────────────────────────────────────────────────
function showNotif(msg) {
    const el = document.getElementById('notif');
    el.textContent = msg;
    el.classList.add('show');
    clearTimeout(el._t);
    el._t = setTimeout(() => el.classList.remove('show'), 4000);
}

// ── Boot ────────────────────────────────────────────────────────────────────────
(async () => {
    // Announce self to teacher
    await sendSignal('teacher', 'join', { name: MY_NAME });
    participants[MY_PEER] = { name: MY_NAME + ' (You)', role: 'Student' };
    updatePeoplePanel();

    // Start polling
    setInterval(pollSignals, 1500);
})();

window.addEventListener('beforeunload', () => {
    sendSignal('teacher', 'leave', { name: MY_NAME });
});
</script>
@endpush