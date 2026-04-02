{{-- resources/views/live-classes/room.blade.php  (student / guest view) --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Class — {{ $liveClass->title ?? 'Live Class' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root{--bg:#080d18;--surface:#0f1724;--s2:#161f30;--s3:#1c2740;--border:rgba(255,255,255,0.07);--accent:#6366f1;--cyan:#22d3ee;--green:#10b981;--red:#ef4444;--amber:#f59e0b;--text:#f1f5f9;--muted:#64748b;--r:12px;--rsm:8px}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html,body{height:100%;font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);overflow:hidden}
        .app{display:grid;grid-template-rows:58px 1fr;grid-template-columns:1fr 330px;height:100vh}

        /* TOPBAR */
        .topbar{grid-column:1/-1;display:flex;align-items:center;justify-content:space-between;padding:0 20px;background:var(--surface);border-bottom:1px solid var(--border);z-index:20}
        .logo{font-family:'Syne',sans-serif;font-weight:800;font-size:17px;background:linear-gradient(135deg,var(--accent),var(--cyan));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .sep{width:1px;height:22px;background:var(--border)}
        .class-name{font-family:'Syne',sans-serif;font-size:14px;font-weight:600;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .live-pill{display:flex;align-items:center;gap:5px;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.28);color:var(--red);font-size:10px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;padding:3px 9px;border-radius:20px}
        .pulse{width:6px;height:6px;border-radius:50%;background:var(--red);animation:blink 1.3s infinite}
        @keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
        .chip{display:flex;align-items:center;gap:7px;background:var(--s2);border:1px solid var(--border);border-radius:20px;padding:5px 13px;font-size:12px}
        .chip i{color:var(--cyan);font-size:11px}
        .chip b{font-family:'Syne',sans-serif;font-weight:700}
        .viewer-pill{display:flex;align-items:center;gap:9px;background:var(--s2);border:1px solid var(--border);border-radius:40px;padding:5px 14px 5px 5px}
        .av{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--cyan));display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:12px;font-weight:700;flex-shrink:0}
        .vname{font-size:12px;font-weight:600}
        .vrole{font-size:10px;color:var(--muted)}
        .btn-leave{display:flex;align-items:center;gap:7px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.28);color:var(--red);border-radius:50px;padding:7px 16px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .18s}
        .btn-leave:hover{background:rgba(239,68,68,.22);transform:translateY(-1px)}

        /* VIDEO COL */
        .vcol{display:flex;flex-direction:column;padding:14px 14px 0;gap:10px;overflow:hidden;background:var(--bg)}

        /* STAGE — children must stack; flex row placed video + waiting side‑by‑side and hid the stream */
        .stage{flex:1;position:relative;background:var(--surface);border-radius:var(--r);border:1px solid var(--border);overflow:hidden;min-height:220px}
        .stage::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(99,102,241,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(99,102,241,.04) 1px,transparent 1px);background-size:28px 28px;pointer-events:none;z-index:0}

        #remoteVideo{position:absolute;inset:0;width:100%;height:100%;object-fit:contain;display:none;border-radius:var(--r);z-index:1;background:#000}

        /* WAITING STATE */
        .waiting{position:absolute;inset:0;display:none;flex-direction:column;align-items:center;justify-content:center;gap:14px;z-index:2;text-align:center;padding:16px;background:rgba(15,23,36,.35)}
        .waiting-icon{width:88px;height:88px;border-radius:50%;background:rgba(99,102,241,.1);border:2px dashed rgba(99,102,241,.25);display:flex;align-items:center;justify-content:center;font-size:34px;color:var(--accent);animation:float 3s ease-in-out infinite}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-9px)}}
        .waiting h2{font-family:'Syne',sans-serif;font-size:18px;font-weight:700}
        .waiting p{font-size:12px;color:var(--muted);max-width:250px;line-height:1.6}
        .conn-status{display:flex;align-items:center;gap:6px;font-size:11px;color:var(--amber);background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);border-radius:20px;padding:4px 12px}
        .conn-dot{width:6px;height:6px;border-radius:50%;background:var(--amber);animation:blink 1s infinite}

        /* OVERLAYS */
        .screen-banner{position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);color:var(--green);border-radius:20px;padding:5px 16px;font-size:11px;font-weight:600;display:none;align-items:center;gap:6px;z-index:5}
        .screen-banner.show{display:flex}
        .instructor-tag{position:absolute;bottom:14px;left:14px;background:rgba(0,0,0,.65);backdrop-filter:blur(6px);border-radius:var(--rsm);padding:6px 12px;font-size:12px;font-weight:600;display:none;align-items:center;gap:7px}
        .instructor-tag.show{display:flex}
        .self-preview{position:absolute;bottom:14px;right:14px;width:150px;height:94px;background:var(--s2);border-radius:8px;border:2px solid var(--border);overflow:hidden;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:4px;font-size:11px;color:var(--muted);z-index:6}
        #localPreview{width:100%;height:100%;object-fit:cover;display:none}
        .fs-btn{position:absolute;top:12px;right:12px;background:rgba(0,0,0,.5);border:1px solid var(--border);color:var(--text);border-radius:var(--rsm);width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:13px;cursor:pointer;z-index:5;transition:background .15s}
        .fs-btn:hover{background:rgba(0,0,0,.8)}

        /* CONNECTING OVERLAY */
        .conn-overlay{position:absolute;inset:0;background:rgba(8,13,24,.92);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:16px;z-index:10;border-radius:var(--r)}
        .spin{width:46px;height:46px;border-radius:50%;border:3px solid var(--s3);border-top-color:var(--accent);animation:spin 1s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}
        .conn-overlay h3{font-family:'Syne',sans-serif;font-size:16px;font-weight:700}
        .conn-overlay p{font-size:12px;color:var(--muted)}

        /* CONTROLS */
        .controls{display:flex;align-items:center;justify-content:center;gap:9px;padding:0 0 12px}
        .ctrl{display:flex;flex-direction:column;align-items:center;gap:4px;background:var(--s2);border:1px solid var(--border);border-radius:10px;padding:9px 14px;cursor:pointer;font-size:10px;font-weight:500;color:var(--text);transition:all .17s;min-width:58px}
        .ctrl i{font-size:16px}
        .ctrl:hover{background:var(--s3);border-color:rgba(99,102,241,.35);transform:translateY(-2px)}
        .ctrl.on{background:rgba(99,102,241,.15);border-color:rgba(99,102,241,.4);color:var(--accent)}
        .ctrl.warn{background:rgba(245,158,11,.1);border-color:rgba(245,158,11,.3);color:var(--amber)}
        .vol-wrap{display:flex;align-items:center;gap:7px;background:var(--s2);border:1px solid var(--border);border-radius:10px;padding:9px 13px}
        .vol-wrap i{color:var(--cyan);font-size:14px}
        input[type=range]{-webkit-appearance:none;width:75px;height:3px;background:var(--s3);border-radius:3px;outline:none;cursor:pointer}
        input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;width:13px;height:13px;border-radius:50%;background:var(--accent);cursor:pointer}

        /* SIDEBAR */
        .sidebar{background:var(--surface);border-left:1px solid var(--border);display:flex;flex-direction:column;overflow:hidden}
        .tabs{display:flex;border-bottom:1px solid var(--border);flex-shrink:0}
        .tab{flex:1;background:transparent;border:none;color:var(--muted);font-size:10px;font-weight:600;letter-spacing:.6px;text-transform:uppercase;padding:13px 4px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:4px;position:relative;transition:color .2s}
        .tab.active{color:var(--accent)}
        .tab.active::after{content:'';position:absolute;bottom:0;left:15%;right:15%;height:2px;background:var(--accent);border-radius:2px 2px 0 0}
        .tbadge{background:var(--red);color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:10px;line-height:1.4}
        .panel{display:none;flex:1;flex-direction:column;overflow:hidden}
        .panel.active{display:flex}
        .ppl-head{padding:14px 16px 10px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);flex-shrink:0}
        .ppl-head h3{font-family:'Syne',sans-serif;font-size:12px;font-weight:700}
        .ppl-cnt{font-size:10px;color:var(--muted);background:var(--s2);padding:2px 8px;border-radius:20px}
        .ppl-list{flex:1;overflow-y:auto;padding:10px;display:flex;flex-direction:column;gap:4px}
        .ppl-list::-webkit-scrollbar{width:3px}
        .ppl-list::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
        .person{display:flex;align-items:center;gap:9px;padding:9px 10px;border-radius:var(--rsm);transition:background .15s}
        .person:hover{background:var(--s2)}
        .pav{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:12px;font-weight:700;flex-shrink:0}
        .pinfo{flex:1;line-height:1.25}
        .pname{font-size:12px;font-weight:500}
        .prole{font-size:10px;color:var(--muted)}
        .ptag{font-size:9px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;padding:2px 7px;border-radius:10px}
        .ptag.host{background:rgba(99,102,241,.15);color:var(--accent)}
        .ptag.you{background:rgba(34,211,238,.12);color:var(--cyan)}
        .chat-msgs{flex:1;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:10px}
        .chat-msgs::-webkit-scrollbar{width:3px}
        .chat-msgs::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
        .empty-state{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:var(--muted);text-align:center;padding:20px}
        .empty-icon{font-size:30px;opacity:.35}
        .empty-state p{font-size:12px;line-height:1.5}
        .msg{display:flex;gap:8px;align-items:flex-start}
        .mav2{width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0}
        .mbody{flex:1}
        .mmeta{display:flex;align-items:baseline;gap:5px;margin-bottom:2px}
        .mname{font-size:11px;font-weight:600}
        .mtime{font-size:10px;color:var(--muted)}
        .mtext{background:var(--s2);border:1px solid var(--border);border-radius:0 8px 8px 8px;padding:7px 11px;font-size:12px;line-height:1.5}
        .mtext.host{background:rgba(99,102,241,.1);border-color:rgba(99,102,241,.2)}
        .mtext.qa{background:rgba(245,158,11,.08);border-color:rgba(245,158,11,.2)}
        .input-area{padding:10px;border-top:1px solid var(--border);display:flex;gap:7px;align-items:center;flex-shrink:0}
        .msg-input{flex:1;background:var(--s2);border:1px solid var(--border);border-radius:20px;padding:8px 14px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:12px;outline:none;transition:border-color .2s}
        .msg-input::placeholder{color:var(--muted)}
        .msg-input:focus{border-color:rgba(99,102,241,.4)}
        .send-btn{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--cyan));border:none;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;flex-shrink:0;transition:transform .15s}
        .send-btn:hover{transform:scale(1.1)}
        .toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:var(--s3);border:1px solid var(--border);border-radius:10px;padding:9px 18px;font-size:12px;display:flex;align-items:center;gap:7px;z-index:999;opacity:0;transition:opacity .3s,bottom .3s;pointer-events:none}
        .toast.show{opacity:1;bottom:28px}
        .toast i{color:var(--green)}
    </style>
</head>
<body>
<div class="app">

    {{-- TOPBAR --}}
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:14px">
            <span class="logo">ESA</span>
            <div class="sep"></div>
            <span class="class-name">{{ $liveClass->title ?? 'Live Class' }}</span>
            <div class="live-pill"><span class="pulse"></span>Live</div>
        </div>
        <div style="display:flex;align-items:center;gap:12px">
            <div class="chip"><i class="fas fa-clock"></i><b id="timer">00:00:00</b></div>
            <div class="chip"><i class="fas fa-users"></i><b id="pcount">1</b><span style="color:var(--muted)">in class</span></div>
            <div class="chip" id="qualChip" style="display:none"><i class="fas fa-wifi"></i><span id="qualLabel">Good</span></div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <div class="viewer-pill">
                <div class="av">{{ strtoupper(substr($participantName ?? 'G', 0, 1)) }}</div>
                <div><div class="vname">{{ $participantName ?? 'Guest' }}</div><div class="vrole">Student · Viewer</div></div>
            </div>
            <button class="btn-leave" id="leaveBtn"><i class="fas fa-sign-out-alt"></i> Leave</button>
        </div>
    </header>

    {{-- VIDEO COLUMN --}}
    <div class="vcol">
        <div class="stage" id="stage">

            {{-- Connecting overlay --}}
            <div class="conn-overlay" id="connOverlay">
                <div class="spin"></div>
                <h3>Joining class…</h3>
                <p>Connecting to {{ $liveClass->teacher->name ?? 'the instructor' }}</p>
            </div>

            {{-- Remote stream --}}
            {{-- muted + JS volume: unblocks autoplay; audio follows vol slider (muted only at 0) --}}
            <video id="remoteVideo" autoplay playsinline muted></video>

            {{-- Waiting state --}}
            <div class="waiting" id="waitingState" style="display:none">
                <div class="waiting-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <h2>Waiting for instructor…</h2>
                <p>{{ $liveClass->teacher->name ?? 'The instructor' }} hasn't started broadcasting yet. Stay connected.</p>
                <div class="conn-status"><span class="conn-dot"></span> Connected — waiting for stream</div>
            </div>

            <button class="fs-btn" id="fsBtn"><i class="fas fa-expand"></i></button>
            <div class="screen-banner" id="screenBanner"><i class="fas fa-desktop"></i> Instructor is sharing their screen</div>
            <div class="instructor-tag" id="instrTag"><i class="fas fa-microphone" style="color:var(--green)"></i> {{ $liveClass->teacher->name ?? 'Instructor' }}</div>
            <div class="self-preview"><video id="localPreview" autoplay muted playsinline></video><i class="fas fa-video-slash" id="prevIcon" style="font-size:15px;color:var(--muted)"></i><span id="prevLabel" style="font-size:10px;color:var(--muted)">Your cam</span></div>
        </div>

        <div class="controls">
            <button class="ctrl" id="micCtrl"><i class="fas fa-microphone-slash"></i><span>Muted</span></button>
            <button class="ctrl" id="camCtrl"><i class="fas fa-video-slash"></i><span>Camera</span></button>
            <button class="ctrl" id="screenCtrl"><i class="fas fa-desktop"></i><span>Share</span></button>
            <button class="ctrl" id="raiseCtrl"><i class="fas fa-hand-paper"></i><span>Raise</span></button>
            <div class="vol-wrap">
                <i class="fas fa-volume-up" id="volIcon"></i>
                <input type="range" id="volSlider" min="0" max="1" step="0.05" value="1">
            </div>
            <button class="ctrl" id="fsCtrl2"><i class="fas fa-expand-arrows-alt"></i><span>Full</span></button>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="tabs">
            <button class="tab active" data-panel="chat"><i class="fas fa-comment-alt"></i> Chat <span class="tbadge" id="chatBadge" style="display:none">0</span></button>
            <button class="tab" data-panel="qa"><i class="fas fa-question-circle"></i> Q&amp;A</button>
            <button class="tab" data-panel="people"><i class="fas fa-users"></i> People</button>
        </div>

        {{-- CHAT --}}
        <div class="panel active" id="panel-chat">
            <div class="chat-msgs" id="chatMsgs">
                <div class="empty-state" id="chatEmpty"><div class="empty-icon"><i class="fas fa-comments"></i></div><p>No messages yet.<br>Say hello!</p></div>
            </div>
            <div class="input-area">
                <input class="msg-input" id="chatInput" type="text" placeholder="Send a message…" autocomplete="off">
                <button class="send-btn" id="chatSend"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>

        {{-- Q&A --}}
        <div class="panel" id="panel-qa">
            <div class="chat-msgs" id="qaMsgs">
                <div class="empty-state"><div class="empty-icon"><i class="fas fa-question"></i></div><p>No questions yet.<br>Ask the instructor anything!</p></div>
            </div>
            <div class="input-area">
                <input class="msg-input" id="qaInput" type="text" placeholder="Ask a question…" autocomplete="off">
                <button class="send-btn" id="qaSend" style="background:linear-gradient(135deg,var(--amber),#f97316)"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>

        {{-- PEOPLE --}}
        <div class="panel" id="panel-people">
            <div class="ppl-head"><h3>Participants</h3><span class="ppl-cnt" id="pplCnt">1</span></div>
            <div class="ppl-list" id="pplList">
                <div class="person">
                    <div class="pav" style="background:rgba(99,102,241,.18);color:var(--accent)">{{ strtoupper(substr($liveClass->teacher->name ?? 'I', 0, 1)) }}</div>
                    <div class="pinfo"><div class="pname">{{ $liveClass->teacher->name ?? 'Instructor' }}</div><div class="prole">Host · Broadcasting</div></div>
                    <span class="ptag host">Host</span>
                </div>
                <div class="person">
                    <div class="pav" style="background:rgba(34,211,238,.15);color:var(--cyan)">{{ strtoupper(substr($participantName ?? 'G', 0, 1)) }}</div>
                    <div class="pinfo"><div class="pname">{{ $participantName ?? 'Guest' }} (You)</div><div class="prole">Student · Viewing</div></div>
                    <span class="ptag you">You</span>
                </div>
            </div>
        </div>
    </aside>
</div>

<div class="toast" id="toast"><i class="fas fa-check-circle"></i><span id="toastMsg"></span></div>

<script>
// ═══════════════════════════════════════════════════════════
// CONFIG
// ═══════════════════════════════════════════════════════════
const ROOM_ID       = 'class-{{ $liveClass->id ?? 1 }}';
const REFERRER_URL  = document.referrer || '/live-classes';
const CSRF          = document.querySelector('meta[name=csrf-token]').content;
const SELF_NAME     = '{{ addslashes($participantName ?? "Guest") }}';
const SELF_INIT     = '{{ strtoupper(substr($participantName ?? "G", 0, 1)) }}';
const HOST_NAME     = '{{ addslashes($liveClass->teacher->name ?? "Instructor") }}';

// ── KEY FIX: Capture the PHP session ID exactly once at page render time.
// This value is used as our stable identifier for ALL signaling operations.
// The server uses session()->getId() in Blade, so this will always match
// what the server uses when it stores/reads from the cache.
const SESSION_ID = '{{ session()->getId() }}';
const VIEWER_PARTICIPANT_DB_ID = @json(isset($viewerParticipantId) ? (int) $viewerParticipantId : null);

console.log('🆔 Student SESSION_ID:', SESSION_ID);

const ICE_SERVERS = { iceServers: [
    { urls: 'stun:stun.l.google.com:19302' },
    { urls: 'stun:stun1.l.google.com:19302' },
]};

// ═══════════════════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════════════════
let pc          = null;
let localStream = null;
/** Single stream for all instructor tracks — never assign e.streams[0] per ontrack or a later audio-only stream replaces video (black screen). */
let remoteInstructorStream = null;
let micOn       = false;
let camOn       = false;
let sharing     = false;
let handRaised  = false;
let connected   = false;
let chatStarted = false;
let qaStarted   = false;
let lastChatTS  = '';
let chatUnread  = 0;
let secs        = 0;
let pendingIceCandidates = [];
const handledSignalIds = new Set();

/**
 * Poll queue can contain many obsolete offers (old renegotiation). Applying them in order mixes ICE
 * ufrags → failed. Only the newest offer in this batch counts; drop ICE until after that offer.
 */
async function dispatchPolledSignals(raw) {
    if (!raw || raw.length === 0) return;
    const sorted = raw.slice().sort(function (a, b) { return (a.id || 0) - (b.id || 0); });
    const offerIds = sorted.filter(function (s) { return s.type === 'offer' && s.id != null; }).map(function (s) { return s.id; });
    const maxOfferId = offerIds.length ? Math.max.apply(null, offerIds) : null;
    let pastWinningOffer = maxOfferId == null;

    for (let i = 0; i < sorted.length; i++) {
        const sig = sorted[i];
        if (sig.type === 'chat') {
            await handleSignal(sig);
            continue;
        }
        if (sig.type === 'offer' && maxOfferId != null && sig.id !== maxOfferId) {
            if (sig.id != null) handledSignalIds.add(sig.id);
            continue;
        }
        if (sig.type === 'offer' && maxOfferId != null && sig.id === maxOfferId) {
            pendingIceCandidates = [];
            await handleSignal(sig);
            pastWinningOffer = true;
            continue;
        }
        if (sig.type === 'ice' && maxOfferId != null && !pastWinningOffer) {
            continue;
        }
        await handleSignal(sig);
    }
}

function postWebRtcResync() {
    console.log('🔁 Requesting host WebRTC resync (fresh offer/ICE)');
    return fetch('/live-signaling/' + ROOM_ID + '/send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({
            from_peer: SESSION_ID,
            to_peer: 'teacher',
            type: 'webrtc_resync',
            payload: { name: SELF_NAME }
        })
    }).catch(console.warn);
}

function sanitizeRemoteOfferSdp(raw) {
    if (!raw || typeof raw !== 'string') return raw;
    // Literal "\n" from double-encoded JSON
    let s = raw.replace(/^\uFEFF/, '').replace(/\\n/g, '\n').replace(/\\r\\n/g, '\n');
    const lines = s.split(/\r?\n/);
    const out = [];
    for (let i = 0; i < lines.length; i++) {
        const line = lines[i].replace(/\r$/, '');
        const t = line.trim();
        // Only drop Plan-B SSRC lines (malformed msid after storage). Do not strip rtpmap / fmtp / candidate.
        if (t.startsWith('a=ssrc-group:')) continue;
        if (t.startsWith('a=ssrc:')) continue;
        out.push(line);
    }
    let sdp = out.join('\r\n');
    if (sdp.length > 0 && !sdp.endsWith('\r\n')) {
        sdp += '\r\n';
    }
    return sdp;
}

// ═══════════════════════════════════════════════════════════
// TIMER
// ═══════════════════════════════════════════════════════════
setInterval(() => {
    secs++;
    document.getElementById('timer').textContent =
        [Math.floor(secs/3600), Math.floor((secs%3600)/60), secs%60]
        .map(n => String(n).padStart(2,'0')).join(':');
}, 1000);

// ═══════════════════════════════════════════════════════════
// TABS
// ═══════════════════════════════════════════════════════════
document.querySelectorAll('.tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('panel-' + btn.dataset.panel).classList.add('active');
        if (btn.dataset.panel === 'chat') {
            chatUnread = 0;
            document.getElementById('chatBadge').style.display = 'none';
        }
    });
});

// ═══════════════════════════════════════════════════════════
// JOIN — registers the student with the server.
// The server pushes viewer_ready to the host queue.
// We do NOT re-send viewer_ready from JS — the server handles it.
// ═══════════════════════════════════════════════════════════
// Track the highest signal ID seen
let lastSignalId = 0;

async function joinRoom() {
    try {
        console.log('🚪 Joining room:', ROOM_ID, 'sessionId:', SESSION_ID);
        document.getElementById('connOverlay').style.display = 'none';
        if (!connected) document.getElementById('waitingState').style.display = 'flex';

        console.log('📤 Student sending join signal to teacher, from_peer:', SESSION_ID, 'to_peer: teacher');
        try {
            const joinResponse = await fetch(`/live-signaling/${ROOM_ID}/send`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({
                    from_peer: SESSION_ID,
                    to_peer: 'teacher',
                    type: 'join',
                    payload: {
                        sessionId: SESSION_ID,
                        name: SELF_NAME
                    }
                })
            });
            if (!joinResponse.ok) {
                console.error('❌ Join signal failed:', joinResponse.status, joinResponse.statusText);
            } else {
                console.log('✅ Join signal sent successfully');
            }
        } catch(e) {
            console.error('❌ Join signal error:', e);
        }

        console.log('📤 Student sending viewer_ready to teacher, from_peer:', SESSION_ID, 'to_peer: teacher');
        fetch(`/live-signaling/${ROOM_ID}/send`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                from_peer: SESSION_ID,
                to_peer: 'teacher',
                type: 'viewer_ready',
                payload: { name: SELF_NAME }
            })
        }).catch(console.warn);
    } catch(e) {
        console.error('❌ Join error:', e);
    }
}

// ═══════════════════════════════════════════════════════════
// CREATE PEER CONNECTION
// ═══════════════════════════════════════════════════════════
function createPC() {
    if (pc) { pc.close(); }
    console.log('🔧 Creating peer connection');
    remoteInstructorStream = new MediaStream();
    const remoteVideo = document.getElementById('remoteVideo');
    remoteVideo.srcObject = remoteInstructorStream;
    pc = new RTCPeerConnection(ICE_SERVERS);

    function showRemotePlaying() {
        const remoteVideo = document.getElementById('remoteVideo');
        remoteVideo.style.display = 'block';
        document.getElementById('waitingState').style.display = 'none';
        document.getElementById('connOverlay').style.display = 'none';
        document.getElementById('instrTag').classList.add('show');
        document.getElementById('qualChip').style.display = 'flex';
        connected = true;
        const v = parseFloat(document.getElementById('volSlider').value);
        remoteVideo.volume = v;
        // Fix: Don't mute video by default - only mute if volume is 0
        remoteVideo.muted = (v === 0);
        remoteVideo.play().then(function () {
            // Video should play regardless of volume setting
            console.log('✅ Remote video playing successfully');
        }).catch(function (err) {
            console.warn('⚠️ Video play failed, trying again:', err);
            remoteVideo.play().catch(function (e) {
                console.warn('⚠️ Video play failed again:', e);
            });
        });
    }

    pc.ontrack = (e) => {
        const track = e.track;
        console.log('🎥 Received track:', track.kind, 'muted=', track.muted);
        const rv = document.getElementById('remoteVideo');

        // Replace prior track of same kind (screen share / camera swap) so we do not keep a dead video track on top.
        if (track.kind === 'video') {
            remoteInstructorStream.getVideoTracks().forEach(function (oldT) {
                if (oldT.id !== track.id) {
                    remoteInstructorStream.removeTrack(oldT);
                }
            });
        } else if (track.kind === 'audio') {
            remoteInstructorStream.getAudioTracks().forEach(function (oldT) {
                if (oldT.id !== track.id) {
                    remoteInstructorStream.removeTrack(oldT);
                }
            });
        }

        if (!remoteInstructorStream.getTracks().some(function (t) { return t.id === track.id; })) {
            remoteInstructorStream.addTrack(track);
        }
        rv.srcObject = remoteInstructorStream;

        track.addEventListener('ended', function () {
            try {
                remoteInstructorStream.removeTrack(track);
            } catch (_) {}
            if (track.kind === 'video') {
                document.getElementById('screenBanner').classList.remove('show');
            }
        });

        // Always show the video element when a track is received, even if muted
        showRemotePlaying();
        
        // If track is muted, listen for unmute event and show video again
        if (track.muted) {
            console.log('⏸️ Track is initially muted:', track.kind, 'waiting for unmute...');
            track.addEventListener('unmute', function () { 
                console.log('🔊 Track unmuted:', track.kind, 'showing remote video');
                showRemotePlaying(); 
            }, { once: true });
        } else {
            console.log('▶️ Track is not muted:', track.kind);
        }
        console.log('✅ Remote track attached');

        if (track.kind === 'video') {
            const checkScreenShare = function () {
                try {
                    const s = track.getSettings();
                    if (s.width > 1400) document.getElementById('screenBanner').classList.add('show');
                    else document.getElementById('screenBanner').classList.remove('show');
                } catch (_) {}
            };
            track.addEventListener('unmute', function () {
                showRemotePlaying();
                setTimeout(checkScreenShare, 400);
            });
        }
    };

    pc.onicecandidate = (e) => {
        if (e.candidate) {
            postSignal({ type: 'ice', candidate: e.candidate, role: 'client', sessionId: SESSION_ID });
        }
    };

    pc.onconnectionstatechange = () => {
        const s = pc.connectionState;
        console.log('🔗 Connection state:', s);
        document.getElementById('qualLabel').textContent =
            s === 'connected' ? 'Good' : s === 'connecting' ? 'Connecting…' : s;

        if (['disconnected', 'failed'].includes(s)) {
            document.getElementById('remoteVideo').style.display  = 'none';
            document.getElementById('waitingState').style.display = 'flex';
            document.getElementById('instrTag').classList.remove('show');
            document.getElementById('screenBanner').classList.remove('show');
            connected = false;
            setTimeout(reconnect, 3000);
        }
    };

    return pc;
}

// ═══════════════════════════════════════════════════════════
// SIGNAL HELPERS
// ═══════════════════════════════════════════════════════════
function postSignal(data) {
    fetch(`/live-signaling/${ROOM_ID}/send`, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify({
            from_peer: SESSION_ID,
            to_peer: 'teacher',
            type: data.type,
            payload: {
                sdp: data.sdp,
                candidate: data.candidate,
                role: data.role,
                sessionId: data.sessionId
            }
        }),
    }).catch(console.warn);
}

// ═══════════════════════════════════════════════════════════
// POLL SIGNALS — client queue, every 2s
// ═══════════════════════════════════════════════════════════
let pollActive = true;

async function pollSignals() {
    if (!pollActive) return;
    try {
        console.log('📨 Student polling with peer:', SESSION_ID, 'since:', lastSignalId);
        const r = await fetch(
            `/live-signaling/${ROOM_ID}/poll?peer=${encodeURIComponent(SESSION_ID)}&since=${lastSignalId}`,
            { headers: { 'X-CSRF-TOKEN': CSRF } }
        );
        if (!r.ok) { console.error('Poll failed:', r.status); return; }
        const response = await r.json();
        const signals = response.signals || [];
        
        // BUG 2 FIX: Update the last signal ID properly
        console.log('📊 Poll response:', response);
        if (response.signals && response.signals.length > 0) {
            const signalIds = response.signals.map(s => s.id);
            const maxId = Math.max(...signalIds);
            console.log('📊 Signal IDs received:', signalIds, 'maxId:', maxId, 'current lastSignalId:', lastSignalId);
            if (maxId > lastSignalId) {
                lastSignalId = maxId;
                console.log('📈 Updated lastSignalId to:', lastSignalId);
            } else {
                console.log('📊 No update needed, maxId <= lastSignalId');
            }
        } else {
            console.log('📊 No signals in response');
        }
        
        if (signals.length) console.log('📨 Student received', signals.length, 'signals');
        await dispatchPolledSignals(signals);
    } catch(e) {
        console.error('Poll error:', e);
    }
}

async function handleSignal(sig) {
    if (sig.id != null && handledSignalIds.has(sig.id)) {
        return;
    }

    console.log('🎯 Handling signal:', sig.type, sig.id != null ? '#' + sig.id : '');

    if (sig.type === 'chat') {
        appendChat(sig.payload?.sender, sig.payload?.message, false, sig.from_peer === 'teacher');
        if (sig.id != null) handledSignalIds.add(sig.id);
        return;
    }

    if (!pc) pc = createPC();

    if (sig.type === 'offer') {
        console.log('📥 Received offer (signalingState=', pc.signalingState, ')');
        if (!sig.payload?.sdp || (!sig.payload.sdp.includes('m=video') && !sig.payload.sdp.includes('m=audio'))) {
            console.error('❌ Invalid SDP'); return;
        }

        // Keep existing PC when stable (renegotiation after host replaces screen / camera).
        if (pc.signalingState !== 'stable') {
            console.warn('⚠️ Recreating peer — was in state:', pc.signalingState);
            pc.close();
            pc = createPC();
            pendingIceCandidates = [];
        }

        const sdp = sanitizeRemoteOfferSdp(sig.payload.sdp);

        async function applyOfferAnswer() {
            await pc.setRemoteDescription(new RTCSessionDescription({ type: 'offer', sdp }));
            console.log('✅ Remote description set');
            if (sig.id != null) handledSignalIds.add(sig.id);

            for (const c of pendingIceCandidates) {
                await pc.addIceCandidate(new RTCIceCandidate(c)).catch(() => {});
            }
            pendingIceCandidates = [];

            if (localStream) {
                localStream.getTracks().forEach(t => {
                    if (!pc.getSenders().find(s => s.track === t)) pc.addTrack(t, localStream);
                });
            }

            const answer = await pc.createAnswer();
            await pc.setLocalDescription(answer);
            postSignal({ type: 'answer', sdp: answer.sdp, role: 'client', sessionId: SESSION_ID });
            console.log('📤 Answer sent');
        }

        try {
            await applyOfferAnswer();
        } catch (e) {
            console.error('❌ Offer handling failed:', e.name, e.message);
            const msg = (e && e.message) ? String(e.message) : '';
            const needsHostResync =
                (/m-line|order.*previous offer|doesn't match/i.test(msg) &&
                    (e.name === 'InvalidAccessError' || e.name === 'OperationError')) ||
                e.name === 'InvalidStateError';
            if (needsHostResync) {
                try {
                    pc.close();
                } catch (_) {}
                pc = createPC();
                pendingIceCandidates = [];
                if (sig.id != null) handledSignalIds.add(sig.id);
                postWebRtcResync();
            }
        }

    } else if (sig.type === 'ice' && sig.payload?.candidate) {
        try {
            if (pc.remoteDescription) {
                await pc.addIceCandidate(new RTCIceCandidate(sig.payload.candidate));
            } else {
                pendingIceCandidates.push(sig.payload.candidate);
            }
        } catch(e) { console.warn('ICE error:', e); }
    }
}

function reconnect() {
    if (connected) return;
    console.log('🔄 Reconnecting…');
    pendingIceCandidates = [];
    createPC();
    postWebRtcResync();
}

// Start polling, then join (1s = lower latency than 2s for HTTP signaling)
setInterval(pollSignals, 1000);
joinRoom().then(() => pollSignals());

// ═══════════════════════════════════════════════════════════
// POLL CHAT — persistent chat log, every 3s
// ═══════════════════════════════════════════════════════════
// Track the highest signal ID seen for chat
let lastChatSignalId = 0;

// Poll for chat messages through the signaling endpoint
async function pollChat() {
    try {
        const r = await fetch(`/live-signaling/${ROOM_ID}/poll?peer=${encodeURIComponent(SESSION_ID)}&since=${lastChatSignalId}`, {
            headers: { 'X-CSRF-TOKEN': CSRF }
        });
        if (!r.ok) return;
        const response = await r.json();
        const signals = response.signals || [];
        
        // Filter for chat signals only
        const chatSignals = signals.filter(sig => sig.type === 'chat');
        
        // Update the last signal ID
        if (signals.length > 0) {
            lastChatSignalId = Math.max(...signals.map(s => s.id));
        }
        
        for (const sig of chatSignals) {
            if (sig.from_peer === SESSION_ID) { continue; }
            appendChat(sig.payload?.sender, sig.payload?.message, false, sig.from_peer === 'teacher');
        }
    } catch(e) { /* silent */ }
}
setInterval(pollChat, 3000);

// ═══════════════════════════════════════════════════════════
// POLL PARTICIPANTS — dynamically refresh student list
// ═══════════════════════════════════════════════════════════
async function pollParticipants() {
    try {
        const response = await fetch(`/live-signaling/{{ $liveClass->id }}/participants`, {
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        if (!response.ok) return;
        
        const participants = await response.json();
        // API = active viewer rows; host is shown separately in the list → total heads = 1 + viewers
        const totalInClass = 1 + participants.length;
        
        // Update top bar count
        document.getElementById('pcount').textContent = totalInClass;
        document.getElementById('pplCnt').textContent = totalInClass;
        
        // Re-render participant list elements
        const pplList = document.getElementById('pplList');
        let html = `
            <div class="person">
                <div class="pav" style="background:rgba(99,102,241,.18);color:var(--accent)">{{ strtoupper(substr($liveClass->teacher->name ?? 'I', 0, 1)) }}</div>
                <div class="pinfo"><div class="pname">{{ $liveClass->teacher->name ?? 'Instructor' }}</div><div class="prole">Host · Broadcasting</div></div>
                <span class="ptag host">Host</span>
            </div>`;
            
        participants.forEach(p => {
            const isSelf = (VIEWER_PARTICIPANT_DB_ID !== null && Number(p.socketId) === VIEWER_PARTICIPANT_DB_ID)
                || p.name === SELF_NAME;
            const init = p.name ? p.name.charAt(0).toUpperCase() : '?';
            const bgStr = isSelf ? 'rgba(34,211,238,.15)' : 'rgba(16,185,129,.15)';
            const colorStr = isSelf ? 'var(--cyan)' : 'var(--green)';
            const roleStr = isSelf ? 'Student · Viewing' : 'Student';
            const tagStr = isSelf ? '<span class="ptag you">You</span>' : '<span class="ptag student" style="background:rgba(16,185,129,.15);color:var(--green)">Student</span>';
            const nameStr = isSelf ? `${p.name} (You)` : p.name;
            
            html += `
            <div class="person">
                <div class="pav" style="background:${bgStr};color:${colorStr}">${init}</div>
                <div class="pinfo"><div class="pname">${nameStr}</div><div class="prole">${roleStr}</div></div>
                ${tagStr}
            </div>`;
        });
        
        pplList.innerHTML = html;
        
    } catch(e) { /* silent */ }
}
// Initial fetch, then poll every 5s
pollParticipants();
setInterval(pollParticipants, 5000);

// ═══════════════════════════════════════════════════════════
// MIC
// ═══════════════════════════════════════════════════════════
document.getElementById('micCtrl').addEventListener('click', async function() {
    if (!localStream || !localStream.getAudioTracks().length) {
        try {
            const s = await navigator.mediaDevices.getUserMedia({ audio: true });
            if (!localStream) localStream = s;
            else s.getAudioTracks().forEach(t => { localStream.addTrack(t); pc?.addTrack(t, localStream); });
        } catch { toast('Microphone denied'); return; }
    }
    micOn = !micOn;
    localStream.getAudioTracks().forEach(t => t.enabled = micOn);
    this.classList.toggle('on', micOn);
    this.querySelector('i').className = micOn ? 'fas fa-microphone' : 'fas fa-microphone-slash';
    this.querySelector('span').textContent = micOn ? 'Mic On' : 'Muted';
    toast(micOn ? 'Microphone on' : 'Muted');
});

// ═══════════════════════════════════════════════════════════
// CAMERA
// ═══════════════════════════════════════════════════════════
document.getElementById('camCtrl').addEventListener('click', async function() {
    if (!localStream || !localStream.getVideoTracks().length) {
        try {
            const s = await navigator.mediaDevices.getUserMedia({ video: true });
            if (!localStream) localStream = s;
            else s.getVideoTracks().forEach(t => { localStream.addTrack(t); pc?.addTrack(t, localStream); });
            const prev = document.getElementById('localPreview');
            prev.srcObject = localStream;
            prev.style.display = 'block';
            document.getElementById('prevIcon').style.display = 'none';
            document.getElementById('prevLabel').textContent = 'You';
            camOn = true;
        } catch { toast('Camera denied'); return; }
    } else {
        camOn = !camOn;
        localStream.getVideoTracks().forEach(t => t.enabled = camOn);
        document.getElementById('localPreview').style.display = camOn ? 'block' : 'none';
        document.getElementById('prevIcon').style.display = camOn ? 'none' : '';
        document.getElementById('prevLabel').textContent = camOn ? 'You' : 'Your cam';
    }
    this.classList.toggle('on', camOn);
    this.querySelector('i').className = camOn ? 'fas fa-video' : 'fas fa-video-slash';
    this.querySelector('span').textContent = camOn ? 'Camera On' : 'Camera';
    toast(camOn ? 'Camera on' : 'Camera off');
});

// ═══════════════════════════════════════════════════════════
// SCREEN SHARE
// ═══════════════════════════════════════════════════════════
document.getElementById('screenCtrl').addEventListener('click', async function() {
    if (sharing) {
        localStream?.getVideoTracks().forEach(t => t.stop());
        sharing = false;
        this.classList.remove('on');
        this.querySelector('span').textContent = 'Share';
        document.getElementById('localPreview').style.display = 'none';
        document.getElementById('prevIcon').style.display = '';
        document.getElementById('prevLabel').textContent = 'Your cam';
        toast('Screen sharing stopped');
        return;
    }
    try {
        const ss = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: true });
        const vt = ss.getVideoTracks()[0];
        if (pc) {
            const sender = pc.getSenders().find(s => s.track?.kind === 'video');
            if (sender) await sender.replaceTrack(vt).catch(() => {});
            else pc.addTrack(vt, ss);
        }
        document.getElementById('localPreview').srcObject = ss;
        document.getElementById('localPreview').style.display = 'block';
        document.getElementById('prevIcon').style.display = 'none';
        document.getElementById('prevLabel').textContent = 'Your screen';
        sharing = true;
        this.classList.add('on');
        this.querySelector('span').textContent = 'Sharing';
        vt.addEventListener('ended', () => {
            sharing = false;
            this.classList.remove('on');
            this.querySelector('span').textContent = 'Share';
            document.getElementById('localPreview').style.display = 'none';
        });
        toast('Sharing your screen');
    } catch { toast('Screen share cancelled'); }
});

// ═══════════════════════════════════════════════════════════
// RAISE HAND
// ═══════════════════════════════════════════════════════════
document.getElementById('raiseCtrl').addEventListener('click', function() {
    handRaised = !handRaised;
    this.classList.toggle('warn', handRaised);
    this.querySelector('span').textContent = handRaised ? '✋ Raised' : 'Raise';
    postSignal({ type: 'raise_hand', role: 'client', sessionId: SESSION_ID, name: SELF_NAME, raised: handRaised });
    toast(handRaised ? '✋ Hand raised — instructor notified' : 'Hand lowered');
});

// ═══════════════════════════════════════════════════════════
// VOLUME
// ═══════════════════════════════════════════════════════════
document.getElementById('volSlider').addEventListener('input', function() {
    const rv = document.getElementById('remoteVideo');
    const v = parseFloat(this.value);
    rv.volume = v;
    // Fix: Only mute if volume is 0, otherwise ensure unmuted
    rv.muted = (v === 0);
    document.getElementById('volIcon').className =
        v === 0 ? 'fas fa-volume-mute' : v < 0.5 ? 'fas fa-volume-down' : 'fas fa-volume-up';
    console.log('🔊 Volume changed to:', v, 'muted:', rv.muted);
});

// ═══════════════════════════════════════════════════════════
// FULLSCREEN
// ═══════════════════════════════════════════════════════════
function toggleFullscreen() {
    const stage = document.getElementById('stage');
    if (!document.fullscreenElement) {
        stage.requestFullscreen().catch(() => {});
        document.getElementById('fsBtn').querySelector('i').className = 'fas fa-compress';
        document.getElementById('fsCtrl2').querySelector('i').className = 'fas fa-compress-arrows-alt';
    } else {
        document.exitFullscreen();
        document.getElementById('fsBtn').querySelector('i').className = 'fas fa-expand';
        document.getElementById('fsCtrl2').querySelector('i').className = 'fas fa-expand-arrows-alt';
    }
}
document.getElementById('fsBtn').addEventListener('click', toggleFullscreen);
document.getElementById('fsCtrl2').addEventListener('click', toggleFullscreen);

// ═══════════════════════════════════════════════════════════
// CHAT
// ═══════════════════════════════════════════════════════════
const chatMsgs  = document.getElementById('chatMsgs');
const chatEmpty = document.getElementById('chatEmpty');

function appendChat(sender, text, isSelf, isHost) {
    if (!chatStarted) { chatEmpty.style.display = 'none'; chatStarted = true; }
    const t    = new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' });
    const init = sender.charAt(0).toUpperCase();
    const bg   = isHost ? 'rgba(99,102,241,.18)' : isSelf ? 'rgba(34,211,238,.15)' : 'rgba(255,255,255,.07)';
    const fc   = isHost ? 'var(--accent)' : isSelf ? 'var(--cyan)' : 'var(--muted)';
    const cls  = isHost ? 'mtext host' : 'mtext';
    chatMsgs.insertAdjacentHTML('beforeend', `
        <div class="msg">
            <div class="mav2" style="background:${bg};color:${fc}">${init}</div>
            <div class="mbody">
                <div class="mmeta"><span class="mname">${sender}</span><span class="mtime">${t}</span></div>
                <div class="${cls}">${text}</div>
            </div>
        </div>`);
    chatMsgs.scrollTop = chatMsgs.scrollHeight;
    if (!isSelf && !document.getElementById('panel-chat').classList.contains('active')) {
        chatUnread++;
        const b = document.getElementById('chatBadge');
        b.textContent = chatUnread; b.style.display = 'inline-block';
    }
}

function sendChat() {
    const txt = document.getElementById('chatInput').value.trim(); if (!txt) return;
    appendChat(SELF_NAME + ' (You)', txt, true, false);
    document.getElementById('chatInput').value = '';
    // Send chat message through signaling endpoint
    fetch(`/live-signaling/${ROOM_ID}/send`, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify({
            from_peer: SESSION_ID,
            to_peer: 'all',
            type: 'chat',
            payload: {
                message: txt,
                sender: SELF_NAME
            }
        }),
    }).catch(console.warn);
}

document.getElementById('chatSend').addEventListener('click', sendChat);
document.getElementById('chatInput').addEventListener('keydown', e => { if (e.key==='Enter' && !e.shiftKey){ e.preventDefault(); sendChat(); } });

// ═══════════════════════════════════════════════════════════
// Q&A
// ═══════════════════════════════════════════════════════════
function sendQA() {
    const txt = document.getElementById('qaInput').value.trim(); if (!txt) return;
    if (!qaStarted) { document.getElementById('qaMsgs').innerHTML = ''; qaStarted = true; }
    const t = new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' });
    document.getElementById('qaMsgs').insertAdjacentHTML('beforeend', `
        <div class="msg">
            <div class="mav2" style="background:rgba(245,158,11,.15);color:var(--amber)">${SELF_INIT}</div>
            <div class="mbody">
                <div class="mmeta"><span class="mname" style="color:var(--amber)">${SELF_NAME} (You)</span><span class="mtime">${t}</span></div>
                <div class="mtext qa"><i class="fas fa-question-circle" style="color:var(--amber);margin-right:5px;font-size:10px"></i>${txt}</div>
            </div>
        </div>`);
    document.getElementById('qaMsgs').scrollTop = 9999;
    // Send Q&A message through signaling endpoint
    fetch(`/live-signaling/${ROOM_ID}/send`, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify({
            from_peer: SESSION_ID,
            to_peer: 'all',
            type: 'chat',
            payload: {
                message: `[Q&A] ${txt}`,
                sender: SELF_NAME
            }
        }),
    }).catch(console.warn);
    document.getElementById('qaInput').value = '';
    toast('Question sent to instructor!');
}

document.getElementById('qaSend').addEventListener('click', sendQA);
document.getElementById('qaInput').addEventListener('keydown', e => { if (e.key==='Enter' && !e.shiftKey){ e.preventDefault(); sendQA(); } });

// ═══════════════════════════════════════════════════════════
// LEAVE
// ═══════════════════════════════════════════════════════════
document.getElementById('leaveBtn').addEventListener('click', (e) => {
    e.preventDefault();
    pollActive = false;
    localStream?.getTracks().forEach(t => t.stop());
    if (pc) { pc.close(); pc = null; }
    fetch('/webrtc/leave/' + ROOM_ID, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body:    JSON.stringify({ sessionId: SESSION_ID }),
    }).finally(() => { window.location.href = REFERRER_URL; });
});

// ═══════════════════════════════════════════════════════════
// TOAST
// ═══════════════════════════════════════════════════════════
function toast(msg) {
    document.getElementById('toastMsg').textContent = msg;
    const t = document.getElementById('toast');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
}
</script>
</body>
</html>