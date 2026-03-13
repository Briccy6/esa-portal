@extends('admin.layout')

@section('title', 'Live Class Room - {{ $liveClass->title }}')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
    :root {
        --void: #07090d;
        --panel: #111620;
        --card: #18202e;
        --border: rgba(255,255,255,0.07);
        --border-hi: rgba(91,164,212,0.28);
        --blue: #5ba4d4;
        --teal: #3ecfb8;
        --red: #e05c5c;
        --green: #4dbf7f;
        --text: #dde3ec;
        --text-2: #6e7e94;
        --text-3: #2f3d52;
        --live-red: #d93737;
        --live-glow: rgba(217,55,55,0.4);
        --sidebar-w: 320px;
        --ctrl-h: 64px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body { overflow: hidden !important; }

    /* ROOT fills whatever space the admin layout leaves */
    .lc-root {
        font-family: 'DM Sans', sans-serif;
        background: var(--void);
        color: var(--text);
        width: 100%;
        height: 100%;
        min-height: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Inner wrapper: stage + sidebar side by side */
    .lc-body {
        flex: 1;
        min-height: 0;
        display: flex;
        overflow: hidden;
    }

    /* ════ STAGE ════ */
    .lc-stage {
        flex: 1;
        min-width: 0;
        position: relative;
        background: #000;
        overflow: hidden;
    }

    #mainVideo {
        width: 100%; height: 100%;
        object-fit: cover; display: block;
    }

    .lc-status-strip {
        position: absolute;
        top: 14px; left: 14px;
        display: flex; align-items: center; gap: 8px;
        z-index: 5; pointer-events: none;
    }

    .lc-live-pill {
        display: flex; align-items: center; gap: 6px;
        background: rgba(0,0,0,0.6); backdrop-filter: blur(10px);
        border: 1px solid rgba(217,55,55,0.3); border-radius: 100px;
        padding: 5px 11px 5px 8px;
        font-size: 11px; font-weight: 600;
        letter-spacing: 0.07em; text-transform: uppercase; color: #f0f0f0;
    }

    .lc-live-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--live-red); box-shadow: 0 0 8px var(--live-glow);
        animation: dotBlink 2s ease-in-out infinite;
    }

    @keyframes dotBlink {
        0%,100% { opacity:1; transform:scale(1); }
        50% { opacity:.65; transform:scale(1.35); }
    }

    .lc-timer-pill {
        background: rgba(0,0,0,0.55); backdrop-filter: blur(10px);
        border: 1px solid var(--border); border-radius: 100px;
        padding: 5px 11px; font-family: 'DM Mono', monospace;
        font-size: 12px; color: var(--text-2); letter-spacing: 0.04em;
    }

    /* ════ SIDEBAR ════ */
    .lc-sidebar {
        width: var(--sidebar-w); flex-shrink: 0;
        background: var(--panel); border-left: 1px solid var(--border);
        display: flex; flex-direction: column; overflow: hidden;
    }

    .lc-sb-head {
        padding: 14px 16px 12px; border-bottom: 1px solid var(--border);
        background: rgba(255,255,255,0.012); flex-shrink: 0;
    }

    .lc-sb-title {
        font-size: 13px; font-weight: 600; color: var(--text);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px;
    }

    .lc-sb-meta { font-size: 11px; color: var(--text-2); display: flex; align-items: center; gap: 10px; }

    /* Tabs */
    .lc-tabs { display: flex; border-bottom: 1px solid var(--border); flex-shrink: 0; }

    .lc-tab {
        flex: 1; padding: 10px 4px;
        font-size: 10.5px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;
        color: var(--text-2); border: none; background: none; cursor: pointer;
        transition: color 0.15s; border-bottom: 2px solid transparent; margin-bottom: -1px;
        display: flex; align-items: center; justify-content: center; gap: 5px;
        font-family: 'DM Sans', sans-serif;
    }
    .lc-tab:hover { color: var(--text); }
    .lc-tab.active { color: var(--blue); border-bottom-color: var(--blue); }

    .lc-req-badge {
        background: var(--red); color: #fff; font-size: 9px; font-weight: 700;
        padding: 1px 5px; border-radius: 100px; line-height: 1.5;
        animation: dotBlink 1.8s infinite;
    }

    .lc-tab-panel { display: none; flex: 1; flex-direction: column; overflow: hidden; min-height: 0; }
    .lc-tab-panel.active { display: flex; }

    /* Participants */
    .lc-pax-scroll {
        flex: 1; overflow-y: auto; padding: 8px;
        scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.05) transparent;
    }
    .lc-pax-scroll::-webkit-scrollbar { width: 3px; }
    .lc-pax-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.07); border-radius: 3px; }

    .lc-pax-row {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 10px; border-radius: 8px; transition: background 0.15s; margin-bottom: 2px;
    }
    .lc-pax-row:hover { background: rgba(255,255,255,0.03); }

    .lc-pax-av {
        width: 32px; height: 32px; border-radius: 50%;
        background: linear-gradient(135deg, #4a72e8, #9b5de5);
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
    }

    .lc-pax-info { flex: 1; min-width: 0; }
    .lc-pax-name { font-size: 12.5px; font-weight: 500; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .lc-host-tag {
        display: inline-block; margin-top: 2px; font-size: 9px; font-weight: 700;
        letter-spacing: 0.06em; text-transform: uppercase; color: var(--teal);
        background: rgba(62,207,184,0.08); border: 1px solid rgba(62,207,184,0.18);
        border-radius: 4px; padding: 1px 5px;
    }

    .lc-pax-ind {
        width: 20px; height: 20px; border-radius: 50%;
        background: rgba(255,255,255,0.04); border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center; font-size: 8px; color: var(--text-3);
    }
    .lc-pax-ind.on { color: var(--green); background: rgba(77,191,127,0.08); border-color: rgba(77,191,127,0.2); }

    /* Chat */
    .lc-chat-feed {
        flex: 1; overflow-y: auto; padding: 10px;
        display: flex; flex-direction: column; gap: 10px;
        scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.05) transparent;
    }
    .lc-chat-feed::-webkit-scrollbar { width: 3px; }
    .lc-chat-feed::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.07); border-radius: 3px; }

    .lc-msg { display: flex; gap: 8px; animation: msgIn .2s ease-out; }
    @keyframes msgIn { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:none; } }

    .lc-msg-av {
        width: 26px; height: 26px; border-radius: 50%;
        background: linear-gradient(135deg, var(--blue), var(--teal));
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 700; color: #fff; flex-shrink: 0; margin-top: 2px;
    }
    .lc-msg-name { font-size: 10px; font-weight: 600; color: var(--blue); margin-bottom: 3px; }
    .lc-msg-text { font-size: 12px; color: var(--text-2); line-height: 1.5; word-break: break-word; }

    .lc-chat-foot {
        padding: 10px 12px; border-top: 1px solid var(--border);
        display: flex; gap: 7px; flex-shrink: 0; background: rgba(0,0,0,0.12);
    }
    .lc-chat-inp {
        flex: 1; background: rgba(255,255,255,0.04); border: 1px solid var(--border);
        border-radius: 8px; color: var(--text); font-family: 'DM Sans', sans-serif;
        font-size: 12.5px; padding: 8px 11px; outline: none; transition: border-color 0.15s;
    }
    .lc-chat-inp::placeholder { color: var(--text-3); }
    .lc-chat-inp:focus { border-color: var(--border-hi); }

    .lc-chat-send {
        width: 36px; height: 36px; border-radius: 8px; background: var(--blue);
        border: none; cursor: pointer; display: flex; align-items: center;
        justify-content: center; color: #fff; font-size: 12px;
        transition: background 0.15s, transform 0.1s; flex-shrink: 0;
    }
    .lc-chat-send:hover { background: #7dbfe8; }
    .lc-chat-send:active { transform: scale(0.93); }

    /* Requests */
    .lc-req-scroll {
        flex: 1; overflow-y: auto; padding: 10px;
        scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.05) transparent;
    }
    .lc-req-scroll::-webkit-scrollbar { width: 3px; }
    .lc-req-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.07); border-radius: 3px; }

    .lc-req-card {
        background: rgba(255,255,255,0.02); border: 1px solid var(--border);
        border-left: 3px solid var(--blue); border-radius: 8px;
        padding: 12px 13px; margin-bottom: 8px;
        transition: border-color 0.2s, background 0.2s, opacity 0.3s, transform 0.3s;
    }
    .lc-req-card:hover { background: rgba(255,255,255,0.035); border-color: var(--border-hi); border-left-color: var(--blue); }

    .lc-req-top { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 9px; }

    .lc-req-av {
        width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg, var(--blue), var(--teal));
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 700; color: #fff;
    }

    .lc-req-info { flex: 1; min-width: 0; }
    .lc-req-name { font-size: 13px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .lc-req-sub { font-size: 11px; color: var(--text-2); margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .lc-req-time { font-size: 10px; color: var(--text-3); font-family: 'DM Mono', monospace; flex-shrink: 0; padding-top: 2px; }

    .lc-req-reason {
        font-size: 11.5px; color: var(--text-2); font-style: italic;
        background: rgba(255,255,255,0.025); border-left: 2px solid rgba(255,255,255,0.08);
        border-radius: 0 5px 5px 0; padding: 6px 10px; margin-bottom: 10px; line-height: 1.5;
    }

    /* ─── ACTION BUTTONS ─── */
    .lc-req-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        width: 100%;
    }

    .lc-req-btn {
        width: 100%; padding: 8px 0;
        border-radius: 7px; font-size: 12px; font-weight: 600;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        gap: 5px; font-family: 'DM Sans', sans-serif; transition: all 0.18s;
        white-space: nowrap; letter-spacing: 0.02em;
    }
    .lc-req-btn:disabled { opacity: 0.45; cursor: not-allowed; transform: none !important; box-shadow: none !important; }
    .lc-req-btn:active:not(:disabled) { transform: scale(0.96); }

    .lc-req-approve {
        background: rgba(77,191,127,0.1); color: var(--green);
        border: 1px solid rgba(77,191,127,0.22);
    }
    .lc-req-approve:hover:not(:disabled) {
        background: rgba(77,191,127,0.2); border-color: rgba(77,191,127,0.4);
        box-shadow: 0 0 10px rgba(77,191,127,0.12);
    }

    .lc-req-reject {
        background: rgba(224,92,92,0.09); color: var(--red);
        border: 1px solid rgba(224,92,92,0.2);
    }
    .lc-req-reject:hover:not(:disabled) {
        background: rgba(224,92,92,0.18); border-color: rgba(224,92,92,0.38);
        box-shadow: 0 0 10px rgba(224,92,92,0.1);
    }

    .lc-req-status {
        grid-column: 1 / -1; text-align: center; padding: 7px 0;
        font-size: 12px; font-weight: 600; letter-spacing: 0.03em;
        display: flex; align-items: center; justify-content: center; gap: 5px;
    }

    .lc-empty {
        flex: 1; display: flex; flex-direction: column; align-items: center;
        justify-content: center; gap: 10px; padding: 30px 20px;
        color: var(--text-3); text-align: center;
    }
    .lc-empty i { font-size: 28px; opacity: 0.35; }
    .lc-empty p { font-size: 12px; line-height: 1.6; }

    /* ════ BOTTOM BAR ════ */
    .lc-bottom {
        display: flex; flex-shrink: 0;
        border-top: 1px solid var(--border);
    }

    .lc-controls {
        flex: 1; height: var(--ctrl-h);
        background: rgba(5,7,12,0.97); backdrop-filter: blur(20px);
        display: flex; align-items: center; justify-content: center;
        padding: 0 24px; gap: 8px; position: relative;
    }

    .lc-ctrl-btn {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; gap: 4px; width: 52px; height: 46px;
        border: 1px solid var(--border); border-radius: 10px;
        background: rgba(255,255,255,0.025); color: var(--text-2); cursor: pointer;
        transition: all 0.15s; font-family: 'DM Sans', sans-serif; padding: 0;
    }
    .lc-ctrl-btn:hover { background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.12); color: var(--text); }
    .lc-ctrl-btn:active { transform: scale(0.95); }
    .lc-ctrl-btn.active { background: rgba(91,164,212,0.1); border-color: rgba(91,164,212,0.25); color: var(--blue); }
    .lc-ctrl-btn.danger.active,
    .lc-ctrl-btn.danger:hover { background: rgba(224,92,92,0.1); border-color: rgba(224,92,92,0.25); color: var(--red); }
    .lc-ctrl-btn i { font-size: 14px; }
    .lc-ctrl-label { font-size: 9px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; }

    .lc-divider { width: 1px; height: 28px; background: var(--border); margin: 0 4px; }

    .lc-leave {
        position: absolute; right: 18px;
        display: flex; align-items: center; gap: 7px;
        padding: 0 16px; height: 38px;
        background: rgba(217,55,55,0.09); border: 1px solid rgba(217,55,55,0.28);
        border-radius: 9px; color: #e07070; font-family: 'DM Sans', sans-serif;
        font-size: 12px; font-weight: 600; letter-spacing: 0.03em;
        cursor: pointer; transition: all 0.15s;
    }
    .lc-leave:hover { background: rgba(217,55,55,0.18); border-color: rgba(217,55,55,0.5); color: #f09090; }
    .lc-leave:active { transform: scale(0.97); }

    /* Sidebar footer strip */
    .lc-sb-foot {
        width: var(--sidebar-w); flex-shrink: 0; height: var(--ctrl-h);
        background: rgba(5,7,12,0.97); border-left: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
    }

    /* Toast */
    .lc-toast {
        position: fixed; bottom: 76px; left: 50%;
        transform: translateX(-50%) translateY(8px);
        background: rgba(5,8,14,0.94); backdrop-filter: blur(14px);
        border: 1px solid var(--border); border-radius: 8px;
        padding: 8px 18px; font-size: 12.5px; color: var(--text-2);
        z-index: 9999; opacity: 0; transition: opacity 0.2s, transform 0.2s;
        pointer-events: none; white-space: nowrap;
    }
    .lc-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

    /* Rejection Modal */
    .lc-modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.65);
        backdrop-filter: blur(6px); z-index: 9998;
        display: none; align-items: center; justify-content: center;
    }
    .lc-modal-overlay.show { display: flex; }

    .lc-modal {
        background: var(--panel); border: 1px solid var(--border);
        border-radius: 12px; width: 380px; overflow: hidden;
        box-shadow: 0 24px 60px rgba(0,0,0,0.7);
        animation: modalIn .22s ease-out;
    }
    @keyframes modalIn { from { opacity:0; transform:translateY(-12px) scale(.97); } to { opacity:1; transform:none; } }

    .lc-modal-head {
        padding: 16px 18px; border-bottom: 1px solid var(--border);
        background: rgba(255,255,255,0.015);
        display: flex; align-items: center; justify-content: space-between;
    }
    .lc-modal-head h3 { font-size: 14px; font-weight: 600; color: var(--text); }

    .lc-modal-close { background: none; border: none; color: var(--text-2); font-size: 16px; cursor: pointer; padding: 2px 4px; transition: color 0.15s; }
    .lc-modal-close:hover { color: var(--text); }

    .lc-modal-body { padding: 16px 18px; }
    .lc-modal-label { font-size: 11px; font-weight: 600; color: var(--text-2); margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em; }

    .lc-modal-textarea {
        width: 100%; background: rgba(255,255,255,0.04); border: 1px solid var(--border);
        border-radius: 8px; color: var(--text); font-family: 'DM Sans', sans-serif;
        font-size: 13px; padding: 10px 12px; outline: none; resize: none; height: 80px;
        transition: border-color 0.15s;
    }
    .lc-modal-textarea:focus { border-color: var(--border-hi); }
    .lc-modal-textarea::placeholder { color: var(--text-3); }

    .lc-modal-foot { padding: 12px 18px; border-top: 1px solid var(--border); display: flex; gap: 8px; justify-content: flex-end; }

    .lc-modal-btn { padding: 8px 18px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer; font-family: 'DM Sans', sans-serif; border: 1px solid var(--border); transition: all 0.15s; }
    .lc-modal-cancel { background: rgba(255,255,255,0.04); color: var(--text-2); }
    .lc-modal-cancel:hover { background: rgba(255,255,255,0.08); color: var(--text); }
    .lc-modal-confirm { background: rgba(224,92,92,0.12); color: var(--red); border-color: rgba(224,92,92,0.25); }
    .lc-modal-confirm:hover { background: rgba(224,92,92,0.22); border-color: rgba(224,92,92,0.45); }
</style>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- JS route constants — avoids Blade/JS string mixing issues --}}
<script>
    // JS route constants — avoids Blade/JS string mixing issues
    const LC_CLASS_ID  = {{ $liveClass->id }};
    const LC_LEAVE_URL = '{{ route("admin.live-classes.index") }}';
    const LC_POLL_URL  = `/api/live-classes/${LC_CLASS_ID}/requests`;
    const LC_APPROVE   = (id) => `/live-classes/${LC_CLASS_ID}/requests/${id}/approve`;
    const LC_REJECT    = (id) => `/live-classes/${LC_CLASS_ID}/requests/${id}/reject`;
    
    // Debug information - moved after constants are defined
    console.log('=== Live Class Room Debug ===');
    console.log('LC_CLASS_ID:', typeof LC_CLASS_ID, LC_CLASS_ID);
    console.log('LC_POLL_URL:', LC_POLL_URL);
    console.log('Student requests count: {{ is_array($studentRequests) ? count($studentRequests) : $studentRequests->count() }}');
    console.log('Auth checks - Admin: {!! Auth::guard('admin')->check() ? 'true' : 'false' !!}');
    console.log('Auth checks - Teacher: {!! Auth::guard('teacher')->check() ? 'true' : 'false' !!}');
    console.log('Auth checks - Staff: {!! Auth::guard('staff')->check() ? 'true' : 'false' !!}');
</script>

<div class="lc-root">

    <!-- ══ Stage + Sidebar ══ -->
    <div class="lc-body">

        <!-- STAGE — clean, zero overlay on the presentation area -->
        <div class="lc-stage">
            <div class="lc-status-strip">
                <div class="lc-live-pill" id="statusPill">
                    <div class="lc-live-dot"></div>
                    <span id="statusLabel">Live Now</span>
                </div>
                <div class="lc-timer-pill" id="timer">00:00:00</div>
            </div>
            <video id="mainVideo" autoplay muted></video>
        </div>

        <!-- SIDEBAR -->
        <div class="lc-sidebar">

            <div class="lc-sb-head">
                <div class="lc-sb-title">{{ $liveClass->title }}</div>
                <div class="lc-sb-meta">
                    <span><i class="fas fa-clock" style="font-size:10px;opacity:.45"></i> {{ $liveClass->start_time->format('H:i') }}–{{ $liveClass->end_time->format('H:i') }}</span>
                    <span style="color:var(--text-3)">·</span>
                    <span><i class="fas fa-broadcast-tower" style="font-size:10px;color:var(--live-red)"></i> Host</span>
                </div>
            </div>

            <div class="lc-tabs">
                <button class="lc-tab active" onclick="switchTab('participants')" id="tab-participants">
                    <i class="fas fa-users"></i> People
                    <span id="paxBadge" style="background:rgba(91,164,212,0.14);color:var(--blue);border:1px solid rgba(91,164,212,0.18);border-radius:100px;font-size:9px;padding:1px 6px;font-family:'DM Mono',monospace;">1</span>
                </button>

                @if($liveClass->chat_enabled)
                <button class="lc-tab" onclick="switchTab('chat')" id="tab-chat">
                    <i class="fas fa-comments"></i> Chat
                </button>
                @endif

                @if(Auth::guard('admin')->check() || Auth::guard('teacher')->check() || Auth::guard('staff')->check())
                <button class="lc-tab" onclick="switchTab('requests')" id="tab-requests">
                    <i class="fas fa-user-clock"></i> Requests
                    @if((is_array($studentRequests) ? count($studentRequests) : $studentRequests->count()) > 0)
                    <span class="lc-req-badge" id="reqBadge">{{ is_array($studentRequests) ? count($studentRequests) : $studentRequests->count() }}</span>
                    @else
                    <span class="lc-req-badge" id="reqBadge" style="display:none;">0</span>
                    @endif
                </button>
                @endif
            </div>

            <!-- Participants Panel -->
            <div class="lc-tab-panel active" id="panel-participants">
                <div class="lc-pax-scroll">
                    <div class="lc-pax-row">
                        <div class="lc-pax-av">Y</div>
                        <div class="lc-pax-info">
                            <div class="lc-pax-name">You</div>
                            <span class="lc-host-tag">Host</span>
                        </div>
                        <div style="display:flex;gap:4px;">
                            <div class="lc-pax-ind on" id="hostMicInd" title="Mic"><i class="fas fa-microphone"></i></div>
                            <div class="lc-pax-ind on" id="hostCamInd" title="Camera"><i class="fas fa-video"></i></div>
                        </div>
                    </div>
                    <div id="participantsList"></div>
                </div>
            </div>

            <!-- Chat Panel -->
            @if($liveClass->chat_enabled)
            <div class="lc-tab-panel" id="panel-chat">
                <div class="lc-chat-feed" id="chatMessages"></div>
                <div class="lc-chat-foot">
                    <input type="text" id="chatInput" class="lc-chat-inp" placeholder="Type a message…">
                    <button class="lc-chat-send" onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
            @endif

            <!-- Requests Panel -->
            @if(Auth::guard('admin')->check() || Auth::guard('teacher')->check() || Auth::guard('staff')->check())
            <div class="lc-tab-panel" id="panel-requests">
                <div class="lc-req-scroll" id="requestsList">
                    @forelse($studentRequests as $req)
                    <div class="lc-req-card" id="req-{{ $req->id }}">
                        <div class="lc-req-top">
                            <div class="lc-req-av">{{ strtoupper(substr($req->user->full_name ?? 'S', 0, 1)) }}</div>
                            <div class="lc-req-info">
                                <div class="lc-req-name">{{ $req->user->full_name ?? 'Unknown Student' }}</div>
                                <div class="lc-req-sub">{{ $req->user->course?->course_name ?? 'No Course' }}</div>
                            </div>
                            <div class="lc-req-time">{{ \Carbon\Carbon::parse($req->created_at)->diffForHumans() }}</div>
                        </div>
                        @if($req->reason)
                        <div class="lc-req-reason">"{{ Str::limit($req->reason, 90) }}"</div>
                        @endif
                        <div class="lc-req-actions" id="req-actions-{{ $req->id }}">
                            <button type="button" class="lc-req-btn lc-req-approve"
                                onclick="approveRequest({{ $req->id }}, this)">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button type="button" class="lc-req-btn lc-req-reject"
                                onclick="openRejectModal({{ $req->id }})">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="lc-empty" id="emptyState">
                        <i class="fas fa-user-check"></i>
                        <p>No pending requests.<br>New ones will appear here automatically.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

        </div><!-- /sidebar -->
    </div><!-- /body -->

    <!-- ══ Bottom bar: controls (stage width) + sidebar footer ══ -->
    <div class="lc-bottom">
        <div class="lc-controls">
            <button class="lc-ctrl-btn" id="micBtn" onclick="toggleMicrophone()">
                <i class="fas fa-microphone"></i>
                <span class="lc-ctrl-label">Audio</span>
            </button>
            <button class="lc-ctrl-btn" id="videoBtn" onclick="toggleCamera()">
                <i class="fas fa-video"></i>
                <span class="lc-ctrl-label">Video</span>
            </button>
            @if($liveClass->screen_share_enabled)
            <div class="lc-divider"></div>
            <button class="lc-ctrl-btn" id="screenBtn" onclick="toggleScreenShare()">
                <i class="fas fa-desktop"></i>
                <span class="lc-ctrl-label">Share</span>
            </button>
            @endif
            @if($liveClass->recording_enabled)
            <button class="lc-ctrl-btn danger" id="recordBtn" onclick="toggleRecording()">
                <i class="fas fa-circle"></i>
                <span class="lc-ctrl-label">Record</span>
            </button>
            @endif
            <button class="lc-leave" onclick="leaveClass()">
                <i class="fas fa-phone-slash"></i> Leave Class
            </button>
        </div>

        <div class="lc-sb-foot">
            <span style="font-size:10px;color:var(--text-3);letter-spacing:0.05em;text-transform:uppercase;font-weight:600;">
                <i class="fas fa-lock" style="margin-right:5px;font-size:9px;"></i>Secure Session
            </span>
        </div>
    </div>

</div>

<!-- Toast -->
<div class="lc-toast" id="toast"></div>

<!-- Reject Modal — replaces disruptive browser prompt() -->
<div class="lc-modal-overlay" id="rejectModal">
    <div class="lc-modal">
        <div class="lc-modal-head">
            <h3><i class="fas fa-times-circle" style="color:var(--red);margin-right:7px;"></i>Reject Request</h3>
            <button type="button" class="lc-modal-close" onclick="closeRejectModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="lc-modal-body">
            <div class="lc-modal-label">Reason (optional)</div>
            <textarea class="lc-modal-textarea" id="rejectReason" placeholder="Provide a reason for rejection…"></textarea>
        </div>
        <div class="lc-modal-foot">
            <button type="button" class="lc-modal-btn lc-modal-cancel" onclick="closeRejectModal()">Cancel</button>
            <button type="button" class="lc-modal-btn lc-modal-confirm" onclick="confirmReject()">
                <i class="fas fa-times"></i> Reject Request
            </button>
        </div>
    </div>
</div>

<script>
/* ════ STATE ════ */
let localStream  = null;
let isRecording  = false, mediaRecorder = null, recordedChunks = [];
let timerInterval = null, startTime = Date.now();
let micEnabled   = true, camEnabled = true;
let activeTab    = 'participants';
let pendingRejectId = null;
const CSRF = () => document.querySelector('meta[name="csrf-token"]').content;

/* ════ TOAST ════ */
function showToast(msg, ms = 2600) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(t._t);
    t._t = setTimeout(() => t.classList.remove('show'), ms);
}

/* ════ TABS ════ */
function switchTab(name) {
    document.querySelectorAll('.lc-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.lc-tab-panel').forEach(p => p.classList.remove('active'));
    const tab   = document.getElementById('tab-'   + name);
    const panel = document.getElementById('panel-' + name);
    if (tab)   tab.classList.add('active');
    if (panel) panel.classList.add('active');
    activeTab = name;
    if (name === 'requests') {
        const b = document.getElementById('reqBadge');
        if (b) b.style.display = 'none';
    }
}

/* ════ WEBRTC ════ */
async function initializeWebRTC() {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    } catch (err) {
        console.warn('Media access denied:', err.message);
        showToast('⚠ Camera/mic unavailable');
    }
    startTimer();
    simulateRemoteConnection();
}

function simulateRemoteConnection() {
    const canvas = document.createElement('canvas');
    canvas.width = 1280; canvas.height = 720;
    const ctx = canvas.getContext('2d');
    let f = 0;
    setInterval(() => {
        ctx.fillStyle = '#06090e';
        ctx.fillRect(0, 0, 1280, 720);
        const g = ctx.createRadialGradient(
            640 + Math.sin(f * 0.018) * 220,
            360 + Math.cos(f * 0.013) * 100,
            8, 640, 360, 600
        );
        g.addColorStop(0, 'rgba(91,164,212,0.09)');
        g.addColorStop(1, 'transparent');
        ctx.fillStyle = g;
        ctx.fillRect(0, 0, 1280, 720);
        ctx.font = '400 13px "DM Sans", sans-serif';
        ctx.fillStyle = 'rgba(110,126,148,0.4)';
        ctx.textAlign = 'center';
        ctx.fillText('Waiting for remote stream…', 640, 360);
        f++;
    }, 33);
    document.getElementById('mainVideo').srcObject = canvas.captureStream(30);
}

/* ════ CONTROLS ════ */
function toggleMicrophone() {
    micEnabled = !micEnabled;
    if (localStream) localStream.getAudioTracks().forEach(t => t.enabled = micEnabled);
    const btn = document.getElementById('micBtn');
    btn.classList.toggle('active', !micEnabled);
    btn.querySelector('i').className = micEnabled ? 'fas fa-microphone' : 'fas fa-microphone-slash';
    const ind = document.getElementById('hostMicInd');
    if (ind) ind.classList.toggle('on', micEnabled);
    showToast(micEnabled ? '🎙 Microphone on' : '🔇 Microphone muted');
}

function toggleCamera() {
    camEnabled = !camEnabled;
    if (localStream) localStream.getVideoTracks().forEach(t => t.enabled = camEnabled);
    const btn = document.getElementById('videoBtn');
    btn.classList.toggle('active', !camEnabled);
    btn.querySelector('i').className = camEnabled ? 'fas fa-video' : 'fas fa-video-slash';
    const ind = document.getElementById('hostCamInd');
    if (ind) ind.classList.toggle('on', camEnabled);
    showToast(camEnabled ? '📷 Camera on' : '📷 Camera off');
}

function toggleScreenShare() {
    const btn = document.getElementById('screenBtn');
    if (!btn) return;
    if (btn.classList.contains('active')) { stopScreenShare(); return; }
    navigator.mediaDevices.getDisplayMedia({ video: true })
        .then(stream => {
            document.getElementById('mainVideo').srcObject = stream;
            btn.classList.add('active');
            btn.querySelector('.lc-ctrl-label').textContent = 'Stop';
            stream.getVideoTracks()[0].addEventListener('ended', stopScreenShare);
            showToast('🖥 Screen sharing active');
        })
        .catch(() => showToast('Screen share cancelled'));
}

function stopScreenShare() {
    const btn = document.getElementById('screenBtn');
    if (btn) { btn.classList.remove('active'); btn.querySelector('.lc-ctrl-label').textContent = 'Share'; }
    simulateRemoteConnection();
    showToast('Screen share ended');
}

function toggleRecording() {
    const btn   = document.getElementById('recordBtn');
    const pill  = document.getElementById('statusPill');
    const label = document.getElementById('statusLabel');
    if (!isRecording) {
        recordedChunks = [];
        if (localStream) {
            mediaRecorder = new MediaRecorder(localStream);
            mediaRecorder.ondataavailable = e => { if (e.data.size > 0) recordedChunks.push(e.data); };
            mediaRecorder.onstop = () => {
                const blob = new Blob(recordedChunks, { type: 'video/webm' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = `live-class-${LC_CLASS_ID}-${Date.now()}.webm`;
                a.click();
            };
            mediaRecorder.start();
        }
        isRecording = true;
        btn.classList.add('active');
        btn.querySelector('i').className = 'fas fa-stop';
        btn.querySelector('.lc-ctrl-label').textContent = 'Stop';
        if (pill) pill.style.borderColor = 'rgba(224,92,92,0.35)';
        if (label) label.textContent = 'Recording';
        showToast('⏺ Recording started');
    } else {
        if (mediaRecorder) mediaRecorder.stop();
        isRecording = false;
        btn.classList.remove('active');
        btn.querySelector('i').className = 'fas fa-circle';
        btn.querySelector('.lc-ctrl-label').textContent = 'Record';
        if (pill) pill.style.borderColor = '';
        if (label) label.textContent = 'Live Now';
        showToast('Recording saved to downloads');
    }
}

/* ════ TIMER ════ */
function startTimer() {
    timerInterval = setInterval(() => {
        const e = Date.now() - startTime;
        const h = Math.floor(e / 3600000);
        const m = Math.floor((e % 3600000) / 60000);
        const s = Math.floor((e % 60000) / 1000);
        const el = document.getElementById('timer');
        if (el) el.textContent =
            `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    }, 1000);
}

/* ════ CHAT ════ */
function sendMessage() {
    const inp = document.getElementById('chatInput');
    if (!inp) return;
    const msg = inp.value.trim();
    if (!msg) return;
    const feed = document.getElementById('chatMessages');
    const el = document.createElement('div');
    el.className = 'lc-msg';
    el.innerHTML = `
        <div class="lc-msg-av">Y</div>
        <div>
            <div class="lc-msg-name">You</div>
            <div class="lc-msg-text">${msg.replace(/</g,'&lt;')}</div>
        </div>`;
    feed.appendChild(el);
    feed.scrollTop = feed.scrollHeight;
    inp.value = '';
}
document.getElementById('chatInput')?.addEventListener('keypress', e => {
    if (e.key === 'Enter') sendMessage();
});

/* ════ LEAVE ════ */
function leaveClass() {
    if (!confirm('Leave this live class?')) return;
    if (localStream) localStream.getTracks().forEach(t => t.stop());
    if (isRecording && mediaRecorder) mediaRecorder.stop();
    clearInterval(timerInterval);
    window.location.href = LC_LEAVE_URL;
}

/* ════ REQUESTS — APPROVE ════ */
function approveRequest(id, btnEl) {
    setReqBtns(id, true);   // disable while fetching

    fetch(LC_APPROVE(id), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF(),
            'Accept': 'application/json'
        }
    })
    .then(r => {
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return r.json();
    })
    .then(data => {
        if (data.success || data.message === 'approved') {
            markReqDone(id, 'approved');
            showToast('✓ Request approved successfully');
            decrementBadge();
        } else {
            setReqBtns(id, false);
            showToast('✗ ' + (data.message || 'Could not approve request'));
        }
    })
    .catch(err => {
        console.error('Approve failed:', err);
        setReqBtns(id, false);
        showToast('✗ Connection error — please try again');
    });
}

/* ════ REQUESTS — REJECT MODAL ════ */
function openRejectModal(id) {
    pendingRejectId = id;
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectModal').classList.add('show');
    setTimeout(() => document.getElementById('rejectReason').focus(), 80);
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('show');
    pendingRejectId = null;
}

function confirmReject() {
    if (!pendingRejectId) return;
    const id     = pendingRejectId;
    const reason = document.getElementById('rejectReason').value.trim();
    closeRejectModal();
    setReqBtns(id, true);

    fetch(LC_REJECT(id), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF(),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ reason })
    })
    .then(r => {
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return r.json();
    })
    .then(data => {
        if (data.success || data.message === 'rejected') {
            markReqDone(id, 'rejected');
            showToast('Request rejected');
            decrementBadge();
        } else {
            setReqBtns(id, false);
            showToast('✗ ' + (data.message || 'Could not reject request'));
        }
    })
    .catch(err => {
        console.error('Reject failed:', err);
        setReqBtns(id, false);
        showToast('✗ Connection error — please try again');
    });
}

/* Helpers */
function setReqBtns(id, disabled) {
    const wrap = document.getElementById('req-actions-' + id);
    if (!wrap) return;
    wrap.querySelectorAll('button').forEach(b => {
        b.disabled = disabled;
    });
}

function markReqDone(id, status) {
    const card    = document.getElementById('req-' + id);
    const actions = document.getElementById('req-actions-' + id);
    if (!card || !actions) return;
    const isApproved = status === 'approved';
    const color  = isApproved ? 'var(--green)' : 'var(--red)';
    const icon   = isApproved ? 'fa-check-circle' : 'fa-times-circle';
    const label  = isApproved ? 'Approved' : 'Rejected';
    actions.innerHTML = `<div class="lc-req-status" style="color:${color};"><i class="fas ${icon}"></i> ${label}</div>`;
    setTimeout(() => {
        card.style.opacity = '0';
        card.style.transform = 'translateX(12px)';
        setTimeout(() => {
            card.remove();
            // If no more cards, show empty state
            const scroll = document.getElementById('requestsList');
            if (scroll && !scroll.querySelector('.lc-req-card')) {
                scroll.innerHTML = `
                    <div class="lc-empty">
                        <i class="fas fa-user-check"></i>
                        <p>All requests handled.<br>New ones appear here automatically.</p>
                    </div>`;
            }
        }, 320);
    }, 1000);
}

function decrementBadge() {
    const b = document.getElementById('reqBadge');
    if (!b) return;
    const n = Math.max(0, parseInt(b.textContent || '0') - 1);
    if (n <= 0) b.style.display = 'none';
    else b.textContent = n;
}

/* ════ POLL for new requests ════ */
function pollRequests() {
    console.log('Polling requests from:', LC_POLL_URL);
    fetch(LC_POLL_URL, {
        headers: { 'X-CSRF-TOKEN': CSRF(), 'Accept': 'application/json' }
    })
    .then(r => {
        console.log('Poll response status:', r.status);
        return r.json();
    })
    .then(data => {
        console.log('Poll response data:', data);
        if (!data.requests) return;
        
        const count = data.requests.length;
        const b = document.getElementById('reqBadge');
        if (count > 0 && b) {
            b.textContent = count;
            b.style.display = 'inline-flex';
            if (activeTab !== 'requests') {
                showToast(`🔔 ${count} new student request${count > 1 ? 's' : ''}`);
            }
        }
        
        // Update the requests list with new data
        updateRequestsList(data.requests);
    })
    .catch(err => {
        console.error('Poll error:', err);
    });
}

function updateRequestsList(requests) {
    const scroll = document.getElementById('requestsList');
    if (!scroll) return;
    
    if (requests.length === 0) {
        scroll.innerHTML = `
            <div class="lc-empty" id="emptyState">
                <i class="fas fa-user-check"></i>
                <p>No pending requests.<br>New ones will appear here automatically.</p>
            </div>`;
        return;
    }
    
    scroll.innerHTML = '';
    requests.forEach(req => {
        const card = document.createElement('div');
        card.className = 'lc-req-card';
        card.id = 'req-' + req.id;
        card.innerHTML = `
            <div class="lc-req-top">
                <div class="lc-req-av">${req.user ? req.user.full_name.charAt(0).toUpperCase() : 'S'}</div>
                <div class="lc-req-info">
                    <div class="lc-req-name">${req.user ? req.user.full_name : 'Unknown Student'}</div>
                    <div class="lc-req-sub">Course ${req.user ? req.user.course_id : 'N/A'}</div>
                </div>
                <div class="lc-req-time">${new Date(req.created_at).toLocaleTimeString()}</div>
            </div>
            ${req.reason ? `<div class="lc-req-reason">"${req.reason.substring(0, 90)}${req.reason.length > 90 ? '...' : ''}"</div>` : ''}
            <div class="lc-req-actions" id="req-actions-${req.id}">
                <button type="button" class="lc-req-btn lc-req-approve"
                    onclick="approveRequest(${req.id}, this)">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button type="button" class="lc-req-btn lc-req-reject"
                    onclick="openRejectModal(${req.id})">
                    <i class="fas fa-times"></i> Reject
                </button>
            </div>
        `;
        scroll.appendChild(card);
    });
}

/* Close modal on overlay click or Escape */
document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeRejectModal();
});

window.addEventListener('beforeunload', () => {
    if (localStream) localStream.getTracks().forEach(t => t.stop());
});

/* ════ INIT ════ */
document.addEventListener('DOMContentLoaded', () => {
    initializeWebRTC();

    @if((is_array($studentRequests) ? count($studentRequests) : $studentRequests->count()) > 0)
    switchTab('requests');   // open requests tab automatically if there are pending ones
    @endif

    // Initial poll for requests
    setTimeout(pollRequests, 1000);
    
    setInterval(pollRequests, 8000);
});
</script>
@endsection