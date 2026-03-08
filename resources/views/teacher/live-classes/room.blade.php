@extends('teacher.layout')

@section('title', 'Live Class Room — {{ $liveClass->title }}')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html, body { overflow: hidden !important; }

:root {
    --bg:        #080c12;
    --surface:   #0f1620;
    --card:      #151e2d;
    --card-hi:   #1c273a;
    --border:    rgba(255,255,255,.07);
    --border-hi: rgba(99,157,220,.3);

    --blue:      #4a8fd4;
    --blue-lt:   rgba(74,143,212,.15);
    --teal:      #38c9b2;
    --emerald:   #34d399;
    --emerald-dk:#059669;
    --rose:      #f87171;
    --rose-dk:   #ef4444;
    --amber:     #fbbf24;

    --text:      #dbe5f2;
    --text-2:    #6b7e94;
    --text-3:    #30404f;

    --live:      #e53e3e;
    --live-glow: rgba(229,62,62,.35);

    --sidebar-w: 310px;
    --bar-h:     68px;
    --header-h:  62px;

    --radius:    10px;
    --radius-sm: 7px;
    --font-d:    'Syne', sans-serif;
    --font-b:    'DM Sans', sans-serif;
    --font-m:    'DM Mono', monospace;
}

/* ─── Root layout ────────────────────────────────────────────── */
.room {
    font-family: var(--font-b);
    background: var(--bg);
    color: var(--text);
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ─── Top bar ────────────────────────────────────────────────── */
.room-header {
    height: var(--header-h);
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    flex-shrink: 0;
    gap: 16px;
}

.room-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}

.live-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--live);
    color: #fff;
    padding: 4px 11px;
    border-radius: 20px;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    flex-shrink: 0;
    box-shadow: 0 0 12px var(--live-glow);
}

.live-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: rgba(255,255,255,.9);
    animation: ldot 1.8s infinite;
}

@keyframes ldot { 0%,100%{opacity:1;} 50%{opacity:.3;} }

.room-title {
    font-family: var(--font-d);
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
    letter-spacing: -.01em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.room-subtitle {
    font-size: 12px;
    color: var(--text-2);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.timer {
    font-family: var(--font-m);
    font-size: 14px;
    color: var(--text-2);
    padding: 4px 12px;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    flex-shrink: 0;
}

/* ─── Body (stage + sidebar) ─────────────────────────────────── */
.room-body {
    flex: 1;
    min-height: 0;
    display: flex;
    overflow: hidden;
}

/* ─── Stage ──────────────────────────────────────────────────── */
.stage {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    position: relative;
}

/* ─── Video area ─────────────────────────────────────────────── */
.video-area {
    flex: 1;
    background: #000;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 0;
    overflow: hidden;
}

.video-area::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,.25) 0%, transparent 25%, transparent 75%, rgba(0,0,0,.4) 100%);
    pointer-events: none;
    z-index: 1;
}

.main-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    background: #0a0f1a;
    display: block;
}

.screen-pip {
    position: absolute;
    bottom: 80px;
    right: 16px;
    width: 220px;
    height: 138px;
    object-fit: cover;
    background: #0a0f1a;
    border-radius: var(--radius);
    border: 2px solid var(--blue);
    z-index: 10;
    display: none;
    cursor: move;
    box-shadow: 0 8px 32px rgba(0,0,0,.5);
    transition: width .2s, height .2s;
}

.screen-pip:hover { outline: 2px solid var(--blue); outline-offset: 2px; }

/* ─── No-video placeholder ───────────────────────────────────── */
.no-video {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 2;
    gap: 16px;
}

.no-video-ring {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    border: 2px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    color: var(--text-3);
    position: relative;
}

.no-video-ring::before {
    content: '';
    position: absolute;
    inset: -6px;
    border-radius: 50%;
    border: 1px dashed rgba(255,255,255,.08);
    animation: spin 12s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.no-video-title {
    font-family: var(--font-d);
    font-size: 18px;
    font-weight: 700;
    color: var(--text-2);
    letter-spacing: -.01em;
}

.no-video-sub {
    font-size: 13px;
    color: var(--text-3);
    text-align: center;
    max-width: 260px;
    line-height: 1.5;
}

.no-video-btn {
    background: var(--blue);
    color: #fff;
    border: none;
    padding: 10px 22px;
    border-radius: var(--radius);
    font-size: 13.5px;
    font-weight: 600;
    font-family: var(--font-b);
    cursor: pointer;
    transition: all .18s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.no-video-btn:hover {
    background: #3a7ec0;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(74,143,212,.4);
}

/* ─── Status toast ───────────────────────────────────────────── */
.video-toast {
    position: absolute;
    top: 16px;
    left: 50%;
    transform: translateX(-50%) translateY(-8px);
    background: rgba(15,22,32,.9);
    border: 1px solid var(--border);
    backdrop-filter: blur(10px);
    border-radius: var(--radius);
    padding: 10px 18px;
    font-size: 13px;
    color: var(--text);
    z-index: 20;
    opacity: 0;
    pointer-events: none;
    transition: all .25s;
    white-space: nowrap;
    box-shadow: 0 4px 20px rgba(0,0,0,.4);
}

.video-toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

.video-toast.success { border-color: rgba(52,211,153,.3); color: var(--emerald); }
.video-toast.error   { border-color: rgba(248,113,113,.3); color: var(--rose); }

/* ─── Controls ───────────────────────────────────────────────── */
.controls {
    height: var(--bar-h);
    background: var(--surface);
    border-top: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    flex-shrink: 0;
    gap: 12px;
    z-index: 5;
}

.ctrl-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.ctrl-btn {
    background: var(--card);
    border: 1px solid var(--border);
    color: var(--text-2);
    padding: 9px 14px;
    border-radius: var(--radius);
    cursor: pointer;
    transition: all .18s;
    font-family: var(--font-b);
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    position: relative;
    white-space: nowrap;
}

.ctrl-btn:hover {
    background: var(--card-hi);
    border-color: rgba(255,255,255,.14);
    color: var(--text);
}

.ctrl-btn.active {
    background: var(--blue-lt);
    border-color: var(--border-hi);
    color: #fff;
}

.ctrl-btn.danger {
    background: rgba(229,62,62,.12);
    border-color: rgba(229,62,62,.25);
    color: var(--rose);
}

.ctrl-btn.danger:hover {
    background: var(--live);
    border-color: var(--live);
    color: #fff;
}

.ctrl-btn:disabled {
    opacity: .35;
    cursor: not-allowed;
    pointer-events: none;
}

.ctrl-icon {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    background: rgba(255,255,255,.06);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    flex-shrink: 0;
}

/* ─── Sidebar ────────────────────────────────────────────────── */
.sidebar {
    width: var(--sidebar-w);
    background: var(--surface);
    border-left: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
}

.sidebar-tabs {
    display: flex;
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
    background: var(--bg);
}

.s-tab {
    flex: 1;
    padding: 14px 10px;
    background: transparent;
    border: none;
    color: var(--text-2);
    cursor: pointer;
    transition: all .18s;
    font-family: var(--font-b);
    font-size: 12.5px;
    font-weight: 600;
    border-bottom: 2px solid transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.s-tab.active {
    color: var(--text);
    border-bottom-color: var(--blue);
    background: var(--surface);
}

.s-tab:hover:not(.active) { color: var(--text); }

.s-badge {
    background: var(--live);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
}

.s-tab:not([data-badge]) .s-badge { display: none; }

.sidebar-content {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
}

.sidebar-content::-webkit-scrollbar { width: 4px; }
.sidebar-content::-webkit-scrollbar-track { background: transparent; }
.sidebar-content::-webkit-scrollbar-thumb { background: var(--text-3); border-radius: 2px; }

.s-panel { display: none; padding: 16px; }
.s-panel.active { display: block; }

/* ─── Request item ───────────────────────────────────────────── */
.req-item {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 14px;
    margin-bottom: 10px;
    transition: all .2s;
}

.req-item:hover { border-color: var(--border-hi); background: var(--card-hi); }
.req-item:last-child { margin-bottom: 0; }

.req-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 8px;
}

.req-name { font-weight: 600; color: var(--text); font-size: 13.5px; }
.req-time { font-size: 11px; color: var(--text-2); }

.req-msg {
    color: var(--text-2);
    font-size: 12.5px;
    line-height: 1.45;
    margin-bottom: 12px;
}

.req-actions { display: flex; gap: 7px; }

.req-btn {
    flex: 1;
    padding: 7px 10px;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all .18s;
    font-family: var(--font-b);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.req-btn.approve {
    background: rgba(52,211,153,.15);
    color: var(--emerald);
    border: 1px solid rgba(52,211,153,.25);
}

.req-btn.approve:hover {
    background: var(--emerald-dk);
    color: #fff;
    border-color: var(--emerald-dk);
}

.req-btn.reject {
    background: rgba(248,113,113,.1);
    color: var(--rose);
    border: 1px solid rgba(248,113,113,.2);
}

.req-btn.reject:hover {
    background: var(--rose-dk);
    color: #fff;
    border-color: var(--rose-dk);
}

/* ─── Empty state inside sidebar ────────────────────────────── */
.s-empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-3);
}

.s-empty-icon { font-size: 28px; margin-bottom: 12px; opacity: .6; }
.s-empty-text { font-size: 13px; line-height: 1.5; }

/* ─── Chat ───────────────────────────────────────────────────── */
.chat-wrap { display: flex; flex-direction: column; height: 100%; gap: 12px; }

.chat-messages {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-height: 0;
}

.chat-msg {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 10px 12px;
}

.chat-msg-author { font-size: 11.5px; font-weight: 600; color: var(--blue); margin-bottom: 4px; }
.chat-msg-body   { font-size: 13px; color: var(--text); line-height: 1.45; }

.chat-input-wrap {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}

.chat-input {
    flex: 1;
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 9px 12px;
    color: var(--text);
    font-family: var(--font-b);
    font-size: 13px;
    outline: none;
    transition: border-color .18s;
}

.chat-input::placeholder { color: var(--text-3); }
.chat-input:focus { border-color: var(--border-hi); }

.chat-send {
    background: var(--blue);
    border: none;
    border-radius: var(--radius-sm);
    padding: 9px 14px;
    color: #fff;
    cursor: pointer;
    font-size: 13px;
    transition: background .18s;
    font-family: var(--font-b);
}

.chat-send:hover { background: #3a7ec0; }

/* ─── Participants ───────────────────────────────────────────── */
.ptcp-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid var(--border);
}

.ptcp-item:last-child { border-bottom: none; }

.ptcp-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--blue), #7c3aed);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-d);
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
}

.ptcp-name { font-size: 13px; font-weight: 600; color: var(--text); }
.ptcp-role { font-size: 11px; color: var(--text-2); margin-top: 2px; }

/* ─── End button (header) ────────────────────────────────────── */
.end-btn {
    background: var(--live);
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: var(--radius);
    font-size: 13px;
    font-weight: 700;
    font-family: var(--font-b);
    cursor: pointer;
    transition: all .18s;
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
    box-shadow: 0 2px 10px var(--live-glow);
    flex-shrink: 0;
}

.end-btn:hover {
    background: #c53030;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px var(--live-glow);
}

/* ─── Responsive ─────────────────────────────────────────────── */
@media (max-width: 800px) {
    :root { --sidebar-w: 100%; }
    .room-body { flex-direction: column; }
    .sidebar { border-left: none; border-top: 1px solid var(--border); height: 280px; }
    .screen-pip { width: 140px; height: 88px; }
}
</style>
@endsection

@section('content')
<div class="room">
    {{-- ── Header ── --}}
    <div class="room-header">
        <div class="room-header-left">
            <div class="live-pill">
                <div class="live-dot"></div>
                Live
            </div>
            <div>
                <div class="room-title">{{ $liveClass->title }}</div>
                <div class="room-subtitle">{{ $liveClass->courses->pluck('name')->join(', ') }}</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <div class="timer" id="elapsedTimer">00:00:00</div>
            <form action="{{ route('teacher.live-classes.end', $liveClass) }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="end-btn" onclick="return confirm('End this live class for all participants?')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                    End Class
                </button>
            </form>
        </div>
    </div>

    {{-- ── Body ── --}}
    <div class="room-body">

        {{-- Stage --}}
        <div class="stage">
            <div class="video-area">
                <video id="mainVideo" class="main-video" autoplay muted playsinline></video>
                <video id="screenPip" class="screen-pip" autoplay muted playsinline></video>

                {{-- No-video state --}}
                <div class="no-video" id="noVideo">
                    <div class="no-video-ring">📹</div>
                    <div class="no-video-title">Camera Off</div>
                    <div class="no-video-sub">Turn on your camera or share your screen to start broadcasting.</div>
                    <button class="no-video-btn" id="quickStartBtn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        Start Camera
                    </button>
                </div>

                {{-- Toast --}}
                <div class="video-toast" id="videoToast"></div>
            </div>

            {{-- Controls --}}
            <div class="controls">
                <div class="ctrl-group">
                    <button class="ctrl-btn" id="micBtn">
                        <span class="ctrl-icon">🎤</span> Mic
                    </button>
                    <button class="ctrl-btn" id="camBtn">
                        <span class="ctrl-icon">📹</span> Camera
                    </button>
                    <button class="ctrl-btn" id="screenBtn">
                        <span class="ctrl-icon">🖥</span> Share
                    </button>
                </div>
                <div class="ctrl-group">
                    <button class="ctrl-btn" id="chatCtrlBtn" onclick="activateTab('chat')">
                        <span class="ctrl-icon">💬</span>
                        <span class="d-none d-sm-inline">Chat</span>
                    </button>
                    <button class="ctrl-btn" id="ptcpCtrlBtn" onclick="activateTab('participants')">
                        <span class="ctrl-icon">👥</span>
                        <span id="ptcpCount">0</span>
                    </button>
                    <button class="ctrl-btn danger" onclick="showSettings()">
                        <span class="ctrl-icon">⚙</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="sidebar">
            <div class="sidebar-tabs">
                <button class="s-tab active" data-tab="requests" onclick="activateTab('requests')">
                    Requests
                    <span class="s-badge">{{ $studentRequests->count() }}</span>
                </button>
                <button class="s-tab" data-tab="participants" onclick="activateTab('participants')">
                    People
                </button>
                <button class="s-tab" data-tab="chat" onclick="activateTab('chat')">
                    Chat
                </button>
            </div>

            <div class="sidebar-content">
                {{-- Requests --}}
                <div class="s-panel active" id="panel-requests">
                    @if($studentRequests->count() > 0)
                        @foreach($studentRequests as $req)
                        <div class="req-item" id="req-{{ $req->id }}">
                            <div class="req-top">
                                <div class="req-name">{{ $req->user->full_name ?? 'Unknown' }}</div>
                                <div class="req-time">{{ $req->created_at->diffForHumans() }}</div>
                            </div>
                            @if($req->reason)
                                <div class="req-msg">{{ $req->reason }}</div>
                            @endif
                            <div class="req-actions">
                                <button class="req-btn approve" onclick="approveReq({{ $req->id }})">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    Approve
                                </button>
                                <button class="req-btn reject" onclick="promptReject({{ $req->id }})">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    Reject
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="s-empty">
                            <div class="s-empty-icon">📭</div>
                            <div class="s-empty-text">No pending requests.<br>Students who request access will appear here.</div>
                        </div>
                    @endif
                </div>

                {{-- Participants --}}
                <div class="s-panel" id="panel-participants">
                    <div class="s-empty">
                        <div class="s-empty-icon">👥</div>
                        <div class="s-empty-text">No participants yet.<br>Approved students will appear here when they join.</div>
                    </div>
                </div>

                {{-- Chat --}}
                <div class="s-panel" id="panel-chat">
                    <div class="chat-wrap">
                        <div class="chat-messages" id="chatMessages">
                            <div class="s-empty">
                                <div class="s-empty-icon">💬</div>
                                <div class="s-empty-text">Chat is empty.<br>Messages will appear here.</div>
                            </div>
                        </div>
                        <div class="chat-input-wrap">
                            <input class="chat-input" id="chatInput" type="text" placeholder="Send a message…" autocomplete="off">
                            <button class="chat-send" id="chatSend">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
/* ── State ── */
let localStream = null;
let screenStream = null;
let micOn = false, camOn = false, screenOn = false;
let startedAt = Date.now();

/* ── Elements ── */
const mainVideo  = document.getElementById('mainVideo');
const screenPip  = document.getElementById('screenPip');
const noVideo    = document.getElementById('noVideo');
const micBtn     = document.getElementById('micBtn');
const camBtn     = document.getElementById('camBtn');
const screenBtn  = document.getElementById('screenBtn');

/* ── Timer ── */
setInterval(() => {
    const s = Math.floor((Date.now() - startedAt) / 1000);
    const h = String(Math.floor(s / 3600)).padStart(2,'0');
    const m = String(Math.floor((s % 3600) / 60)).padStart(2,'0');
    const sec = String(s % 60).padStart(2,'0');
    document.getElementById('elapsedTimer').textContent = `${h}:${m}:${sec}`;
}, 1000);

/* ── Camera ── */
async function startCamera() {
    try {
        toast('Accessing camera…');
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        mainVideo.srcObject = localStream;
        noVideo.style.display = 'none';
        micOn = true; camOn = true;
        updateBtns();
        toast('Camera ready', 'success');
    } catch(e) {
        toast('Cannot access camera: ' + e.message, 'error');
    }
}

function toggleMic() {
    if (!localStream) return startCamera();
    const tracks = localStream.getAudioTracks();
    if (!tracks.length) return;
    micOn = !micOn;
    tracks[0].enabled = micOn;
    updateBtns();
    toast(micOn ? 'Mic unmuted' : 'Mic muted');
}

function toggleCam() {
    if (!localStream) return startCamera();
    const tracks = localStream.getVideoTracks();
    if (!tracks.length) return;
    camOn = !camOn;
    tracks[0].enabled = camOn;
    updateBtns();
    toast(camOn ? 'Camera on' : 'Camera off');
}

async function toggleScreen() {
    if (screenOn) {
        screenStream?.getTracks().forEach(t => t.stop());
        screenStream = null; screenOn = false;
        screenPip.srcObject = null; screenPip.style.display = 'none';
        updateBtns(); toast('Screen share stopped');
    } else {
        try {
            screenStream = await navigator.mediaDevices.getDisplayMedia({ video: { cursor: 'always' }, audio: true });
            screenPip.srcObject = screenStream;
            screenPip.style.display = 'block';
            screenOn = true;
            updateBtns();
            toast('Screen sharing', 'success');
            screenStream.getVideoTracks()[0].addEventListener('ended', toggleScreen);
        } catch(e) {
            toast('Screen share failed: ' + e.message, 'error');
        }
    }
}

function updateBtns() {
    micBtn.className    = 'ctrl-btn' + (micOn    ? ' active' : '');
    camBtn.className    = 'ctrl-btn' + (camOn    ? ' active' : '');
    screenBtn.className = 'ctrl-btn' + (screenOn ? ' active' : '');
    micBtn.querySelector('.ctrl-icon').textContent    = micOn ? '🎤' : '🔇';
    camBtn.querySelector('.ctrl-icon').textContent    = camOn ? '📹' : '📷';
    screenBtn.querySelector('.ctrl-icon').textContent = screenOn ? '⏹' : '🖥';
}

document.getElementById('quickStartBtn').addEventListener('click', startCamera);
micBtn.addEventListener('click', toggleMic);
camBtn.addEventListener('click', toggleCam);
screenBtn.addEventListener('click', toggleScreen);

/* ── Sidebar tabs ── */
function activateTab(name) {
    document.querySelectorAll('.s-tab').forEach(t => {
        t.classList.toggle('active', t.dataset.tab === name);
    });
    document.querySelectorAll('.s-panel').forEach(p => {
        p.classList.toggle('active', p.id === 'panel-' + name);
    });
}

/* ── Requests ── */
function approveReq(id) {
    if (!confirm('Approve this student?')) return;
    apiPost(`{{ route('teacher.live-classes.requests.approve', [$liveClass->id, ':id']) }}`.replace(':id', id))
        .then(d => {
            if (d.success) { removeReq(id); toast('Approved ✓', 'success'); }
            else toast('Error approving', 'error');
        });
}

function promptReject(id) {
    const reason = prompt('Rejection reason (optional):');
    apiPost(`{{ route('teacher.live-classes.requests.reject', [$liveClass->id, ':id']) }}`.replace(':id', id), { reason: reason || '' })
        .then(d => {
            if (d.success) { removeReq(id); toast('Request rejected'); }
            else toast('Error rejecting', 'error');
        });
}

function removeReq(id) {
    const el = document.getElementById('req-' + id);
    if (el) { el.style.transition = 'all .3s'; el.style.opacity = '0'; el.style.transform = 'translateX(20px)'; setTimeout(() => el.remove(), 300); }
}

function apiPost(url, body = {}) {
    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(body)
    }).then(r => r.json());
}

/* ── Chat ── */
document.getElementById('chatSend').addEventListener('click', sendChat);
document.getElementById('chatInput').addEventListener('keydown', e => { if (e.key === 'Enter') sendChat(); });

function sendChat() {
    const inp = document.getElementById('chatInput');
    const msg = inp.value.trim();
    if (!msg) return;
    inp.value = '';
    const wrap = document.getElementById('chatMessages');
    // Remove empty state
    const empty = wrap.querySelector('.s-empty');
    if (empty) empty.remove();
    const el = document.createElement('div');
    el.className = 'chat-msg';
    el.innerHTML = `<div class="chat-msg-author">You (Teacher)</div><div class="chat-msg-body">${msg}</div>`;
    wrap.appendChild(el);
    wrap.scrollTop = wrap.scrollHeight;
}

/* ── Toast ── */
function toast(msg, type = '') {
    const el = document.getElementById('videoToast');
    el.textContent = msg;
    el.className = 'video-toast show' + (type ? ' ' + type : '');
    clearTimeout(el._t);
    el._t = setTimeout(() => el.className = 'video-toast', 3000);
}

function showSettings() {
    toast('Settings coming soon…');
}

/* ── Browser check ── */
if (!navigator.mediaDevices?.getUserMedia) {
    toast('WebRTC not supported in this browser', 'error');
    micBtn.disabled = camBtn.disabled = screenBtn.disabled = true;
}

/* ── Auto-refresh requests every 45s ── */
setInterval(() => {
    if (document.querySelector('.s-tab.active')?.dataset.tab === 'requests') {
        // silent refresh via fetch to update badge
    }
}, 45000);
</script>
@endsection