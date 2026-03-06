@extends('admin.layout')

@section('title', 'Live Class Room - {{ $liveClass->title }}')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
    :root {
        --bg-void: #080b10;
        --bg-surface: #0d1117;
        --bg-panel: rgba(13, 17, 23, 0.92);
        --bg-card: rgba(22, 28, 38, 0.85);
        --border-subtle: rgba(255,255,255,0.06);
        --border-glow: rgba(99, 179, 237, 0.25);
        --accent-blue: #63b3ed;
        --accent-teal: #4fd1c5;
        --accent-red: #fc8181;
        --accent-green: #68d391;
        --text-primary: #e8edf3;
        --text-secondary: #8b96a8;
        --text-muted: #4a5568;
        --live-red: #e53e3e;
        --live-glow: rgba(229, 62, 62, 0.4);
        --control-hover: rgba(255,255,255,0.08);
        --control-active: rgba(99, 179, 237, 0.15);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    .lc-root {
        font-family: 'Sora', sans-serif;
        background: var(--bg-void);
        color: var(--text-primary);
        width: 100%;
        height: calc(100vh - 56px);
        display: grid;
        grid-template-rows: 1fr auto;
        overflow: hidden;
        position: relative;
    }

    /* ── Stage ── */
    .lc-stage {
        position: relative;
        overflow: hidden;
        background: #000;
    }

    .lc-stage::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(99,179,237,0.04) 0%, transparent 70%);
        pointer-events: none;
        z-index: 1;
    }

    #mainVideo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* ── Local pip ── */
    .lc-pip {
        position: absolute;
        bottom: 88px;
        right: 20px;
        width: 188px;
        height: 140px;
        border-radius: 10px;
        overflow: hidden;
        border: 1.5px solid rgba(255,255,255,0.12);
        box-shadow: 0 8px 32px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.04);
        z-index: 30;
        background: #111;
        cursor: move;
        transition: box-shadow 0.2s;
    }

    .lc-pip:hover {
        box-shadow: 0 12px 40px rgba(0,0,0,0.7), 0 0 0 1.5px var(--border-glow);
    }

    .lc-pip video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .lc-pip-label {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 6px 10px;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.7);
    }

    /* ── Live badge ── */
    .lc-live-badge {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        align-items: center;
        gap: 7px;
        background: rgba(8,11,16,0.75);
        backdrop-filter: blur(12px) saturate(1.5);
        border: 1px solid rgba(229,62,62,0.3);
        border-radius: 100px;
        padding: 6px 16px 6px 10px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-primary);
        z-index: 40;
        box-shadow: 0 0 20px rgba(229,62,62,0.2);
    }

    .lc-live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--live-red);
        box-shadow: 0 0 8px var(--live-glow);
        animation: livePulse 1.8s ease-in-out infinite;
    }

    @keyframes livePulse {
        0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 8px var(--live-glow); }
        50% { opacity: 0.8; transform: scale(1.2); box-shadow: 0 0 16px var(--live-glow); }
    }

    .lc-live-badge.recording .lc-live-dot { background: var(--accent-red); animation-duration: 0.9s; }
    .lc-live-badge.recording { border-color: rgba(252,129,129,0.4); box-shadow: 0 0 20px rgba(252,129,129,0.2); }

    /* ── Timer ── */
    .lc-timer {
        font-family: 'JetBrains Mono', monospace;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-secondary);
        letter-spacing: 0.04em;
    }

    /* ── Floating Panels ── */
    .lc-panel {
        position: absolute;
        background: var(--bg-panel);
        backdrop-filter: blur(20px) saturate(1.6);
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 16px 48px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.02);
        z-index: 35;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.2s;
    }

    .lc-panel:hover {
        box-shadow: 0 20px 56px rgba(0,0,0,0.6), 0 0 0 1px var(--border-glow);
    }

    .lc-panel-header {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-subtle);
        background: rgba(255,255,255,0.025);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .lc-panel-icon {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: rgba(99,179,237,0.1);
        border: 1px solid rgba(99,179,237,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-blue);
        font-size: 11px;
        flex-shrink: 0;
    }

    .lc-panel-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-primary);
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .lc-panel-badge {
        margin-left: auto;
        background: rgba(99,179,237,0.12);
        color: var(--accent-blue);
        border: 1px solid rgba(99,179,237,0.2);
        border-radius: 100px;
        padding: 2px 8px;
        font-size: 10px;
        font-weight: 600;
        font-family: 'JetBrains Mono', monospace;
    }

    /* Chat panel */
    .lc-chat {
        top: 20px;
        left: 20px;
        width: 280px;
        height: 380px;
    }

    .lc-chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.08) transparent;
    }

    .lc-chat-messages::-webkit-scrollbar { width: 4px; }
    .lc-chat-messages::-webkit-scrollbar-track { background: transparent; }
    .lc-chat-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 4px; }

    .lc-message {
        display: flex;
        gap: 8px;
        animation: msgSlide 0.2s ease-out;
    }

    @keyframes msgSlide {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .lc-message-avatar {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-blue), var(--accent-teal));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .lc-message-body { flex: 1; min-width: 0; }

    .lc-message-name {
        font-size: 10px;
        font-weight: 600;
        color: var(--accent-blue);
        letter-spacing: 0.03em;
        margin-bottom: 3px;
    }

    .lc-message-text {
        font-size: 12px;
        color: var(--text-secondary);
        line-height: 1.5;
        word-break: break-word;
    }

    .lc-chat-input-area {
        padding: 10px;
        border-top: 1px solid var(--border-subtle);
        display: flex;
        gap: 6px;
        flex-shrink: 0;
    }

    .lc-chat-input {
        flex: 1;
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--border-subtle);
        border-radius: 7px;
        color: var(--text-primary);
        font-family: 'Sora', sans-serif;
        font-size: 12px;
        padding: 7px 10px;
        outline: none;
        transition: border-color 0.15s, background 0.15s;
    }

    .lc-chat-input::placeholder { color: var(--text-muted); }
    .lc-chat-input:focus { border-color: var(--border-glow); background: rgba(255,255,255,0.06); }

    .lc-chat-send {
        width: 32px;
        height: 32px;
        border-radius: 7px;
        background: var(--accent-blue);
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--bg-void);
        font-size: 12px;
        transition: background 0.15s, transform 0.1s;
        flex-shrink: 0;
    }

    .lc-chat-send:hover { background: #90cdf4; transform: scale(1.05); }
    .lc-chat-send:active { transform: scale(0.96); }

    /* Participants panel */
    .lc-participants {
        top: 20px;
        right: 20px;
        width: 260px;
        max-height: 380px;
    }

    .lc-participants-list {
        flex: 1;
        overflow-y: auto;
        padding: 8px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.08) transparent;
    }

    .lc-participant {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 8px;
        transition: background 0.15s;
    }

    .lc-participant:hover { background: rgba(255,255,255,0.04); }

    .lc-participant-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }

    .lc-participant-info { flex: 1; min-width: 0; }

    .lc-participant-name {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .lc-participant-role {
        font-size: 10px;
        color: var(--text-muted);
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .lc-participant-indicators {
        display: flex;
        gap: 4px;
    }

    .lc-indicator {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
        color: var(--text-muted);
    }

    .lc-indicator.active { color: var(--accent-green); background: rgba(104,211,145,0.08); border-color: rgba(104,211,145,0.2); }

    /* Host badge */
    .lc-host-badge {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--accent-teal);
        background: rgba(79,209,197,0.08);
        border: 1px solid rgba(79,209,197,0.2);
        border-radius: 4px;
        padding: 1px 5px;
        margin-top: 2px;
        display: inline-block;
    }

    /* ── Controls bar ── */
    .lc-controls {
        height: 68px;
        background: rgba(8,11,16,0.95);
        backdrop-filter: blur(20px);
        border-top: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        gap: 12px;
        z-index: 50;
        position: relative;
    }

    .lc-controls::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(99,179,237,0.15), transparent);
    }

    .lc-ctrl-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .lc-ctrl-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        width: 52px;
        height: 48px;
        border: 1px solid var(--border-subtle);
        border-radius: 10px;
        background: rgba(255,255,255,0.03);
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s;
        font-family: 'Sora', sans-serif;
        padding: 0;
        position: relative;
        overflow: hidden;
    }

    .lc-ctrl-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: var(--control-hover);
        opacity: 0;
        transition: opacity 0.15s;
    }

    .lc-ctrl-btn:hover::before { opacity: 1; }
    .lc-ctrl-btn:hover { border-color: rgba(255,255,255,0.12); color: var(--text-primary); }

    .lc-ctrl-btn:active { transform: scale(0.96); }

    .lc-ctrl-btn.active {
        background: var(--control-active);
        border-color: var(--border-glow);
        color: var(--accent-blue);
    }

    .lc-ctrl-btn.danger.active,
    .lc-ctrl-btn.danger:hover {
        background: rgba(252,129,129,0.1);
        border-color: rgba(252,129,129,0.3);
        color: var(--accent-red);
    }

    .lc-ctrl-btn i {
        font-size: 14px;
        position: relative;
    }

    .lc-ctrl-label {
        font-size: 9px;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        position: relative;
    }

    .lc-ctrl-divider {
        width: 1px;
        height: 32px;
        background: var(--border-subtle);
        margin: 0 4px;
    }

    /* Leave button */
    .lc-leave-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0 20px;
        height: 40px;
        background: rgba(229,62,62,0.1);
        border: 1px solid rgba(229,62,62,0.3);
        border-radius: 10px;
        color: #fc8181;
        font-family: 'Sora', sans-serif;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.04em;
        cursor: pointer;
        transition: all 0.15s;
    }

    .lc-leave-btn:hover {
        background: rgba(229,62,62,0.18);
        border-color: rgba(229,62,62,0.5);
        color: #feb2b2;
        box-shadow: 0 0 16px rgba(229,62,62,0.2);
    }

    .lc-leave-btn:active { transform: scale(0.97); }

    /* Class title */
    .lc-class-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 220px;
    }

    .lc-class-meta {
        font-size: 10px;
        color: var(--text-muted);
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-top: 1px;
    }

    /* Mic/cam off overlay */
    .lc-video-off {
        position: absolute;
        inset: 0;
        background: #111;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: var(--text-muted);
    }

    /* Notifications */
    .lc-toast {
        position: absolute;
        bottom: 88px;
        left: 50%;
        transform: translateX(-50%) translateY(10px);
        background: rgba(8,11,16,0.9);
        backdrop-filter: blur(12px);
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 12px;
        color: var(--text-secondary);
        z-index: 60;
        opacity: 0;
        transition: opacity 0.2s, transform 0.2s;
        pointer-events: none;
        white-space: nowrap;
    }

    .lc-toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
</style>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="lc-root">

    <!-- ─── Stage ─── -->
    <div class="lc-stage">

        <!-- Live / Recording badge -->
        <div class="lc-live-badge" id="statusIndicator">
            <div class="lc-live-dot"></div>
            Live Now
        </div>

        <!-- Main remote video -->
        <video id="mainVideo" autoplay muted style="width:100%;height:100%;object-fit:cover;display:block;"></video>

        <!-- Local PiP -->
        <div class="lc-pip">
            <video id="localVideo" autoplay muted style="width:100%;height:100%;object-fit:cover;"></video>
            <div class="lc-pip-label">You · Host</div>
        </div>

        <!-- ── Chat Panel ── -->
        @if($liveClass->chat_enabled)
        <div class="lc-panel lc-chat">
            <div class="lc-panel-header">
                <div class="lc-panel-icon"><i class="fas fa-comments"></i></div>
                <span class="lc-panel-title">Chat</span>
            </div>
            <div class="lc-chat-messages" id="chatMessages">
                <!-- messages -->
            </div>
            <div class="lc-chat-input-area">
                <input type="text" id="chatInput" class="lc-chat-input" placeholder="Send a message…">
                <button class="lc-chat-send" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
        @endif

        <!-- ── Participants Panel ── -->
        @if($liveClass->screen_share_enabled)
        <div class="lc-panel lc-participants">
            <div class="lc-panel-header">
                <div class="lc-panel-icon"><i class="fas fa-users"></i></div>
                <span class="lc-panel-title">Participants</span>
                <span class="lc-panel-badge" id="participantCount">1</span>
            </div>
            <div class="lc-participants-list" id="participantsList">
                <div class="lc-participant">
                    <div class="lc-participant-avatar">Y</div>
                    <div class="lc-participant-info">
                        <div class="lc-participant-name">You</div>
                        <span class="lc-host-badge">Host</span>
                    </div>
                    <div class="lc-participant-indicators">
                        <div class="lc-indicator active" title="Mic on"><i class="fas fa-microphone"></i></div>
                        <div class="lc-indicator active" title="Camera on"><i class="fas fa-video"></i></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- ── Student Requests Panel ── -->
        @if((Auth::guard('admin')->check() || Auth::guard('teacher')->check() || Auth::guard('staff')->check()) && $studentRequests->count() > 0)
        <div class="lc-panel" style="top: 410px; right: 20px; width: 260px; max-height: 280px;">
            <div class="lc-panel-header">
                <div class="lc-panel-icon"><i class="fas fa-user-clock"></i></div>
                <span class="lc-panel-title">Requests</span>
                <span class="lc-panel-badge">{{ $studentRequests->count() }}</span>
            </div>
            <div class="lc-participants-list" style="max-height: 200px;">
                @foreach($studentRequests as $request)
                <div class="lc-participant" style="border-left: 3px solid var(--accent-blue);">
                    <div class="lc-participant-avatar" style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-teal));">
                        {{ substr($request->user->full_name ?? 'S', 0, 1) }}
                    </div>
                    <div class="lc-participant-info">
                        <div class="lc-participant-name">{{ $request->user->full_name ?? 'Unknown' }}</div>
                        <div class="lc-participant-role">{{ $request->user->course?->course_name ?? 'No Course' }}</div>
                        <div style="font-size: 9px; color: var(--text-muted); margin-top: 2px;">
                            {{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}
                        </div>
                    </div>
                    <div class="lc-participant-indicators">
                        <button onclick="approveRequest({{ $request->id }})" class="lc-indicator" style="background: rgba(104,211,145,0.1); border-color: rgba(104,211,145,0.2); color: var(--accent-green);" title="Approve">
                            <i class="fas fa-check"></i>
                        </button>
                        <button onclick="rejectRequest({{ $request->id }})" class="lc-indicator" style="background: rgba(252,129,129,0.1); border-color: rgba(252,129,129,0.2); color: var(--accent-red);" title="Reject">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Toast notification -->
        <div class="lc-toast" id="toast"></div>
    </div>

    <!-- ─── Controls Bar ─── -->
    <div class="lc-controls">

        <!-- Left: class info -->
        <div style="min-width:0;flex:0 0 auto;max-width:220px;">
            <div class="lc-class-title">{{ $liveClass->title }}</div>
            <div class="lc-class-meta lc-timer" id="timer">00:00:00</div>
        </div>

        <!-- Center: media controls -->
        <div class="lc-ctrl-group">
            <button class="lc-ctrl-btn" id="micBtn" onclick="toggleMicrophone()" title="Toggle Microphone">
                <i class="fas fa-microphone"></i>
                <span class="lc-ctrl-label">Audio</span>
            </button>
            <button class="lc-ctrl-btn" id="videoBtn" onclick="toggleCamera()" title="Toggle Camera">
                <i class="fas fa-video"></i>
                <span class="lc-ctrl-label">Video</span>
            </button>

            @if($liveClass->screen_share_enabled)
            <div class="lc-ctrl-divider"></div>
            <button class="lc-ctrl-btn" id="screenBtn" onclick="toggleScreenShare()" title="Share Screen">
                <i class="fas fa-desktop"></i>
                <span class="lc-ctrl-label">Share</span>
            </button>
            @endif

            @if($liveClass->recording_enabled)
            <button class="lc-ctrl-btn danger" id="recordBtn" onclick="toggleRecording()" title="Record">
                <i class="fas fa-circle"></i>
                <span class="lc-ctrl-label">Record</span>
            </button>
            @endif
        </div>

        <!-- Right: leave -->
        <div style="flex:0 0 auto;">
            <button class="lc-leave-btn" onclick="leaveClass()">
                <i class="fas fa-phone-slash"></i>
                Leave Class
            </button>
        </div>
    </div>
</div>

<script>
let localStream = null;
let isRecording = false;
let mediaRecorder = null;
let recordedChunks = [];
let timerInterval = null;
let startTime = Date.now();
let micEnabled = true;
let camEnabled = true;

/* ── Notification Toast ── */
function showToast(msg, duration = 2400) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.remove('show'), duration);
}

/* ── WebRTC Init ── */
async function initializeWebRTC() {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        document.getElementById('localVideo').srcObject = localStream;
        startTimer();
        initializeSignaling();
    } catch (error) {
        console.error('Media access error:', error);
        showToast('⚠ Unable to access camera/microphone');
    }
}

function initializeSignaling() {
    console.log('WebRTC initialized for class: {{ $liveClass->id }}');
    setTimeout(simulateRemoteConnection, 2000);
}

function simulateRemoteConnection() {
    const canvas = document.createElement('canvas');
    canvas.width = 1280; canvas.height = 720;
    const ctx = canvas.getContext('2d');
    let f = 0;
    setInterval(() => {
        ctx.fillStyle = '#060a0f';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        const grad = ctx.createRadialGradient(
            canvas.width/2 + Math.sin(f * 0.02) * 180,
            canvas.height/2 + Math.cos(f * 0.015) * 80,
            10,
            canvas.width/2, canvas.height/2, 500
        );
        grad.addColorStop(0, 'rgba(99,179,237,0.12)');
        grad.addColorStop(1, 'transparent');
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        f++;
    }, 1000/30);
    document.getElementById('mainVideo').srcObject = canvas.captureStream(30);
}

/* ── Controls ── */
function toggleMicrophone() {
    micEnabled = !micEnabled;
    if (localStream) localStream.getAudioTracks().forEach(t => t.enabled = micEnabled);
    const btn = document.getElementById('micBtn');
    btn.classList.toggle('active', !micEnabled);
    btn.querySelector('i').className = micEnabled ? 'fas fa-microphone' : 'fas fa-microphone-slash';
    showToast(micEnabled ? 'Microphone on' : 'Microphone muted');
}

function toggleCamera() {
    camEnabled = !camEnabled;
    if (localStream) localStream.getVideoTracks().forEach(t => t.enabled = camEnabled);
    const btn = document.getElementById('videoBtn');
    btn.classList.toggle('active', !camEnabled);
    btn.querySelector('i').className = camEnabled ? 'fas fa-video' : 'fas fa-video-slash';
    showToast(camEnabled ? 'Camera on' : 'Camera off');
}

function toggleScreenShare() {
    const btn = document.getElementById('screenBtn');
    const isSharing = !btn.classList.contains('active');
    if (isSharing) {
        navigator.mediaDevices.getDisplayMedia({ video: true })
            .then(stream => {
                document.getElementById('mainVideo').srcObject = stream;
                btn.classList.add('active');
                btn.querySelector('.lc-ctrl-label').textContent = 'Stop';
                stream.getVideoTracks()[0].addEventListener('ended', () => {
                    btn.classList.remove('active');
                    btn.querySelector('.lc-ctrl-label').textContent = 'Share';
                    showToast('Screen share ended');
                });
                showToast('Screen sharing started');
            })
            .catch(() => showToast('Screen share cancelled'));
    } else {
        btn.classList.remove('active');
        btn.querySelector('.lc-ctrl-label').textContent = 'Share';
        showToast('Screen share stopped');
    }
}

function toggleRecording() {
    const btn = document.getElementById('recordBtn');
    const badge = document.getElementById('statusIndicator');
    if (!isRecording) {
        startRecording();
        btn.classList.add('active');
        btn.querySelector('i').className = 'fas fa-stop';
        btn.querySelector('.lc-ctrl-label').textContent = 'Stop';
        badge.classList.add('recording');
        badge.innerHTML = '<div class="lc-live-dot"></div> Recording';
        showToast('Recording started');
    } else {
        stopRecording();
        btn.classList.remove('active');
        btn.querySelector('i').className = 'fas fa-circle';
        btn.querySelector('.lc-ctrl-label').textContent = 'Record';
        badge.classList.remove('recording');
        badge.innerHTML = '<div class="lc-live-dot"></div> Live Now';
        showToast('Recording saved');
    }
}

function startRecording() {
    if (!localStream) return;
    recordedChunks = [];
    mediaRecorder = new MediaRecorder(localStream);
    mediaRecorder.ondataavailable = e => { if (e.data.size > 0) recordedChunks.push(e.data); };
    mediaRecorder.onstop = () => {
        const blob = new Blob(recordedChunks, { type: 'video/webm' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `live-class-{{ $liveClass->id }}-${Date.now()}.webm`;
        a.click();
    };
    mediaRecorder.start();
    isRecording = true;
}

function stopRecording() {
    if (mediaRecorder && isRecording) { mediaRecorder.stop(); isRecording = false; }
}

/* ── Chat ── */
function sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if (!message) return;
    const feed = document.getElementById('chatMessages');
    const el = document.createElement('div');
    el.className = 'lc-message';
    el.innerHTML = `
        <div class="lc-message-avatar">Y</div>
        <div class="lc-message-body">
            <div class="lc-message-name">You</div>
            <div class="lc-message-text">${message.replace(/</g,'&lt;')}</div>
        </div>`;
    feed.appendChild(el);
    feed.scrollTop = feed.scrollHeight;
    input.value = '';
}

document.getElementById('chatInput')?.addEventListener('keypress', e => {
    if (e.key === 'Enter') sendMessage();
});

/* ── Timer ── */
function startTimer() {
    timerInterval = setInterval(() => {
        const e = Date.now() - startTime;
        const h = Math.floor(e / 3600000);
        const m = Math.floor((e % 3600000) / 60000);
        const s = Math.floor((e % 60000) / 1000);
        document.getElementById('timer').textContent =
            `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    }, 1000);
}

/* ── Leave ── */
function leaveClass() {
    if (confirm('Are you sure you want to leave this live class?')) {
        if (localStream) localStream.getTracks().forEach(t => t.stop());
        if (isRecording) stopRecording();
        if (timerInterval) clearInterval(timerInterval);
        window.location.href = '{{ route("admin.live-classes.index") }}';
    }
}

/* ── Student Requests ── */
function approveRequest(requestId) {
    if (confirm('Approve this student request?')) {
        fetch(`/live-classes/{{ $liveClass->id }}/requests/${requestId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('✓ Request approved');
                location.reload(); // Reload to update the requests list
            } else {
                showToast('✗ Failed to approve request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('✗ Error approving request');
        });
    }
}

function rejectRequest(requestId) {
    const reason = prompt('Reason for rejection (optional):');
    if (reason !== null) {
        fetch(`/live-classes/{{ $liveClass->id }}/requests/${requestId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('✗ Request rejected');
                location.reload(); // Reload to update the requests list
            } else {
                showToast('✗ Failed to reject request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('✗ Error rejecting request');
        });
    }
}

window.addEventListener('beforeunload', () => {
    if (localStream) localStream.getTracks().forEach(t => t.stop());
});

document.addEventListener('DOMContentLoaded', initializeWebRTC);
</script>
@endsection