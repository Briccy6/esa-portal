{{-- resources/views/teacher/live-classes/room.blade.php --}}
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
        :root {
            --bg:#0b0f1a; --surface:#111827; --s2:#1a2235; --s3:#1e2d45;
            --border:rgba(255,255,255,0.07); --accent:#3b82f6; --cyan:#22d3ee;
            --green:#10b981; --red:#ef4444; --amber:#f59e0b;
            --text:#f1f5f9; --muted:#64748b; --r:12px; --rsm:8px;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html,body{height:100%;font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);overflow:hidden}
        .app{display:grid;grid-template-rows:60px 1fr;grid-template-columns:1fr 320px;height:100vh}

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
        .teacher-pill{display:flex;align-items:center;gap:9px;background:var(--s2);border:1px solid var(--border);border-radius:40px;padding:5px 14px 5px 5px}
        .av{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--cyan));display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-size:12px;font-weight:700;flex-shrink:0;overflow:hidden}
        .av img{width:100%;height:100%;object-fit:cover}
        .tname{font-size:12px;font-weight:600}
        .trole{font-size:10px;color:var(--muted)}
        .btn-end{display:flex;align-items:center;gap:7px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.28);color:var(--red);border-radius:50px;padding:8px 18px;font-size:13px;font-weight:600;cursor:pointer;transition:all .18s}
        .btn-end:hover{background:rgba(239,68,68,.22);transform:translateY(-1px)}

        /* VIDEO COL */
        .vcol{display:flex;flex-direction:column;padding:16px 16px 0;gap:12px;overflow:hidden;background:var(--bg)}
        .stage{flex:1;position:relative;background:var(--surface);border-radius:var(--r);border:1px solid var(--border);overflow:hidden;display:flex;align-items:center;justify-content:center}
        .stage::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(59,130,246,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.04) 1px,transparent 1px);background-size:32px 32px;pointer-events:none}
        #localVideo{width:100%;height:100%;object-fit:cover;border-radius:var(--r);display:none}
        .cam-off{display:flex;flex-direction:column;align-items:center;gap:14px;z-index:1;text-align:center}
        .cam-off-icon{width:88px;height:88px;border-radius:50%;background:rgba(59,130,246,.1);border:2px dashed rgba(59,130,246,.25);display:flex;align-items:center;justify-content:center;font-size:32px;color:var(--accent);animation:float 3s ease-in-out infinite}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
        .cam-off h2{font-family:'Syne',sans-serif;font-size:17px;font-weight:700}
        .cam-off p{font-size:12px;color:var(--muted);max-width:220px;line-height:1.5}
        .btn-start{display:flex;align-items:center;gap:7px;background:linear-gradient(135deg,var(--accent),var(--cyan));color:#fff;border:none;border-radius:50px;padding:11px 26px;font-size:13px;font-weight:600;cursor:pointer;transition:all .18s;box-shadow:0 4px 20px rgba(59,130,246,.35);margin-top:4px}
        .btn-start:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(59,130,246,.5)}
        .self-view{position:absolute;bottom:14px;right:14px;width:150px;height:94px;background:var(--s2);border-radius:8px;border:2px solid var(--border);overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--muted)}
        .screen-on-banner{position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);color:var(--green);border-radius:20px;padding:5px 16px;font-size:11px;font-weight:600;display:none;align-items:center;gap:6px;z-index:5}
        .screen-on-banner.show{display:flex}

        /* THUMBS */
        .thumbs{display:flex;gap:8px;overflow-x:auto;padding-bottom:4px}
        .thumbs::-webkit-scrollbar{height:3px}
        .thumbs::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
        .thumb{width:100px;height:62px;background:var(--s2);border-radius:8px;border:1px solid var(--border);flex-shrink:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;position:relative;font-size:10px;color:var(--muted)}
        .thumb-av{width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700}
        .thumb-name{font-size:10px;font-weight:500;color:var(--text);max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

        /* CONTROLS */
        .controls{display:flex;align-items:center;justify-content:center;gap:10px;padding:0 0 14px}
        .ctrl{display:flex;flex-direction:column;align-items:center;gap:4px;background:var(--s2);border:1px solid var(--border);border-radius:10px;padding:10px 15px;cursor:pointer;font-size:10px;font-weight:500;color:var(--text);transition:all .17s;min-width:60px}
        .ctrl i{font-size:17px}
        .ctrl:hover{background:var(--s3);border-color:rgba(59,130,246,.35);transform:translateY(-2px)}
        .ctrl.on{background:rgba(59,130,246,.15);border-color:rgba(59,130,246,.4);color:var(--accent)}
        .ctrl.danger{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.3);color:var(--red)}

        /* SIDEBAR */
        .sidebar{background:var(--surface);border-left:1px solid var(--border);display:flex;flex-direction:column;overflow:hidden}
        .tabs{display:flex;border-bottom:1px solid var(--border);flex-shrink:0}
        .tab{flex:1;background:transparent;border:none;color:var(--muted);font-size:10px;font-weight:600;letter-spacing:.6px;text-transform:uppercase;padding:13px 4px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:4px;position:relative;transition:color .2s}
        .tab.active{color:var(--accent)}
        .tab.active::after{content:'';position:absolute;bottom:0;left:15%;right:15%;height:2px;background:var(--accent);border-radius:2px 2px 0 0}
        .tbadge{background:var(--red);color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:10px;line-height:1.4}
        .panel{display:none;flex:1;flex-direction:column;overflow:hidden}
        .panel.active{display:flex}

        /* PEOPLE */
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
        .ptag.host{background:rgba(59,130,246,.15);color:var(--accent)}
        .ptag.student{background:rgba(16,185,129,.12);color:var(--green)}

        /* CHAT */
        .chat-msgs{flex:1;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:10px}
        .chat-msgs::-webkit-scrollbar{width:3px}
        .chat-msgs::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
        .msg{display:flex;gap:8px;align-items:flex-start}
        .mav2{width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0}
        .mbody{flex:1}
        .mmeta{display:flex;align-items:baseline;gap:5px;margin-bottom:2px}
        .mname{font-size:11px;font-weight:600}
        .mtime{font-size:10px;color:var(--muted)}
        .mtext{background:var(--s2);border:1px solid var(--border);border-radius:0 8px 8px 8px;padding:7px 11px;font-size:12px;line-height:1.5}
        .input-area{padding:10px;border-top:1px solid var(--border);display:flex;gap:7px;align-items:center;flex-shrink:0}
        .msg-input{flex:1;background:var(--s2);border:1px solid var(--border);border-radius:20px;padding:8px 14px;color:var(--text);font-family:'DM Sans',sans-serif;font-size:12px;outline:none;transition:border-color .2s}
        .msg-input::placeholder{color:var(--muted)}
        .msg-input:focus{border-color:rgba(59,130,246,.4)}
        .send-btn{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--cyan));border:none;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;flex-shrink:0;transition:transform .15s}
        .send-btn:hover{transform:scale(1.1)}
        .empty-state{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:var(--muted);text-align:center;padding:20px}
        .empty-icon{font-size:30px;opacity:.35}
        .empty-state p{font-size:12px;line-height:1.5}

        /* TOAST */
        .toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:var(--s3);border:1px solid var(--border);border-radius:10px;padding:9px 18px;font-size:12px;display:flex;align-items:center;gap:7px;z-index:999;opacity:0;transition:opacity .3s,bottom .3s;pointer-events:none}
        .toast.show{opacity:1;bottom:28px}
        .toast i{color:var(--green)}

        /* HAND RAISE notification */
        .hand-notif{position:fixed;top:76px;right:24px;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.35);color:var(--amber);border-radius:10px;padding:10px 16px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:8px;z-index:50;opacity:0;transform:translateX(20px);transition:all .3s}
        .hand-notif.show{opacity:1;transform:translateX(0)}
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
            <div class="chip"><i class="fas fa-users"></i><b id="pcount">0</b><span style="color:var(--muted)">viewers</span></div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <div class="teacher-pill">
                <div class="av">
                    @if(auth()->guard('teacher')->user()->photo ?? false)
                        <img src="{{ asset('storage/'.auth()->guard('teacher')->user()->photo) }}" alt="">
                    @else
                        {{ strtoupper(substr(auth()->guard('teacher')->user()->name ?? 'T', 0, 1)) }}
                    @endif
                </div>
                <div><div class="tname">{{ auth()->guard('teacher')->user()->name ?? 'Teacher' }}</div><div class="trole">Host</div></div>
            </div>
            <button class="btn-end" id="endBtn"><i class="fas fa-phone-slash"></i> End Class</button>
        </div>
    </header>

    {{-- VIDEO COL --}}
    <div class="vcol">
        <div class="stage" id="stage">
            <video id="localVideo" autoplay muted playsinline></video>
            <div class="cam-off" id="camOff">
                <div class="cam-off-icon"><i class="fas fa-video-slash"></i></div>
                <h2>Camera is Off</h2>
                <p>Turn on your camera or share your screen to start broadcasting.</p>
                <button class="btn-start" id="startCamBtn"><i class="fas fa-video"></i> Start Camera</button>
            </div>
            <div class="self-view" id="selfView"><span>Preview</span></div>
            <div class="screen-on-banner" id="screenBanner"><i class="fas fa-desktop"></i> Sharing screen</div>
        </div>

        <div class="thumbs" id="thumbsRow">
            <div class="thumb">
                <div class="thumb-av" style="background:rgba(59,130,246,.2);color:var(--accent)">{{ strtoupper(substr(auth()->guard('teacher')->user()->name ?? 'T', 0, 1)) }}</div>
                <div class="thumb-name">You (Host)</div>
            </div>
        </div>

        <div class="controls">
            <button class="ctrl" id="micCtrl"><i class="fas fa-microphone-slash"></i><span>Muted</span></button>
            <button class="ctrl" id="camCtrl"><i class="fas fa-video"></i><span>Camera</span></button>
            <button class="ctrl" id="screenCtrl"><i class="fas fa-desktop"></i><span>Share</span></button>
            <button class="ctrl" id="recordCtrl"><i class="fas fa-circle" style="color:var(--red);font-size:11px"></i><span>Record</span></button>
            <button class="ctrl" id="fsCtrl"><i class="fas fa-expand-arrows-alt"></i><span>Full</span></button>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="tabs">
            <button class="tab active" data-panel="people"><i class="fas fa-users"></i> People</button>
            <button class="tab" data-panel="requests"><i class="fas fa-hand-paper"></i> Hands <span class="tbadge" id="hand-badge" style="display:none">0</span></button>
            <button class="tab" data-panel="chat"><i class="fas fa-comment"></i> Chat <span class="tbadge" id="chat-badge" style="display:none">0</span></button>
        </div>

        {{-- PEOPLE --}}
        <div class="panel active" id="panel-people">
            <div class="ppl-head"><h3>Participants</h3><span class="ppl-cnt" id="ppl-cnt">1</span></div>
            <div class="ppl-list" id="pplList">
                <div class="person">
                    <div class="pav" style="background:rgba(59,130,246,.15);color:var(--accent)">{{ strtoupper(substr(auth()->guard('teacher')->user()->name ?? 'T', 0, 1)) }}</div>
                    <div class="pinfo"><div class="pname">{{ auth()->guard('teacher')->user()->name ?? 'Teacher' }} (You)</div><div class="prole">Host · Broadcasting</div></div>
                    <span class="ptag host">Host</span>
                </div>
            </div>
        </div>

        {{-- HANDS RAISED --}}
        <div class="panel" id="panel-requests">
            <div class="empty-state" id="handsEmpty"><div class="empty-icon"><i class="fas fa-hand-paper"></i></div><p>No raised hands yet.</p></div>
            <div id="handsList" style="padding:12px;display:flex;flex-direction:column;gap:8px"></div>
        </div>

        {{-- CHAT --}}
        <div class="panel" id="panel-chat">
            <div class="chat-msgs" id="chatMsgs">
                <div class="empty-state" id="chatEmpty"><div class="empty-icon"><i class="fas fa-comments"></i></div><p>No messages yet.</p></div>
            </div>
            <div class="input-area">
                <input class="msg-input" id="chatInput" type="text" placeholder="Message participants…" autocomplete="off">
                <button class="send-btn" id="sendBtn"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </aside>
</div>

<div class="toast" id="toast"><i class="fas fa-check-circle"></i><span id="toastMsg"></span></div>
<div class="hand-notif" id="handNotif"><i class="fas fa-hand-paper"></i><span id="handName"></span> raised their hand</div>

<script>
// ═══════════════════════════════════════════════════════════
// CONFIG
// ═══════════════════════════════════════════════════════════
const ROOM_ID   = 'class-{{ $liveClass->id ?? 1 }}';
const CSRF      = document.querySelector('meta[name=csrf-token]').content;
const SELF_NAME = '{{ addslashes(auth()->guard("teacher")->user()->name ?? "Teacher") }}';
const SELF_INIT = '{{ strtoupper(substr(auth()->guard("teacher")->user()->name ?? "T", 0, 1)) }}';
const SESSION_ID = '{{ session()->getId() }}';

const ICE_SERVERS = { iceServers: [
    { urls: 'stun:stun.l.google.com:19302' },
    { urls: 'stun:stun1.l.google.com:19302' }
]};

// ═══════════════════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════════════════
let localStream   = null;
let micOn         = false;
let camOn         = false;
let sharing       = false;
let peers         = {};       // sessionId → RTCPeerConnection
let participants  = {};       // sessionId → {name}
let chatStarted   = false;
let handCount     = 0;
let chatUnread    = 0;
let secs          = 0;
let lastChatTime  = '';

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
            const b = document.getElementById('chat-badge');
            b.style.display = 'none';
        }
    });
});

// ═══════════════════════════════════════════════════════════
// GET / CREATE LOCAL MEDIA STREAM
// ═══════════════════════════════════════════════════════════
async function ensureStream(video = true, audio = true) {
    if (localStream) return localStream;
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video, audio });
    } catch(e) {
        toast('Media access denied: ' + e.message);
        throw e;
    }
    return localStream;
}

// ═══════════════════════════════════════════════════════════
// PEER CONNECTION — create one per viewer
// ═══════════════════════════════════════════════════════════
async function createPeerForViewer(viewerSessionId) {
    if (peers[viewerSessionId]) {
        console.log('🔄 Peer already exists for:', viewerSessionId);
        return peers[viewerSessionId];
    }

    console.log('🎬 Creating peer for viewer:', viewerSessionId);
    const pc = new RTCPeerConnection(ICE_SERVERS);
    peers[viewerSessionId] = pc;

    // Add all existing local tracks
    if (localStream) {
        localStream.getTracks().forEach(t => pc.addTrack(t, localStream));
        console.log('📹 Added local tracks to peer for:', viewerSessionId);
    }

    pc.onicecandidate = (e) => {
        if (e.candidate) {
            postSignal({
                type:      'ice',
                candidate: e.candidate,
                role:      'host',
                sessionId: SESSION_ID,
                forSession: viewerSessionId,
            });
            console.log('🧊 Sent ICE candidate to:', viewerSessionId);
        }
    };

    pc.onconnectionstatechange = () => {
        console.log('🔗 Peer connection state for', viewerSessionId, ':', pc.connectionState);
        if (['disconnected','failed','closed'].includes(pc.connectionState)) {
            delete peers[viewerSessionId];
            removeParticipant(viewerSessionId);
        }
    };

    // Create and send offer
    try {
        const offer = await pc.createOffer();
        await pc.setLocalDescription(offer);
        
        // Debug: Log the SDP to check for parsing issues
        console.log('📋 Generated SDP:', offer.sdp);
        
        postSignal({
            type:       'offer',
            sdp:        offer.sdp,
            role:       'host',
            sessionId:  SESSION_ID,
            forSession: viewerSessionId,
        });
        console.log('📤 Sent offer to viewer:', viewerSessionId);
    } catch(e) {
        console.error('❌ Failed to create/send offer:', e);
    }

    return pc;
}

// ═══════════════════════════════════════════════════════════
// SIGNAL HELPERS
// ═══════════════════════════════════════════════════════════
function postSignal(data) {
    fetch('/webrtc/signaling/' + ROOM_ID, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(data)
    }).catch(console.warn);
}

// Alternative function to try teacher-specific routes
function postSignalTeacher(data) {
    fetch('/webrtc/signaling/' + ROOM_ID, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(data)
    }).catch(error => {
        console.error('❌ Teacher signaling failed, trying fallback:', error);
        // Fallback to generic route
        fetch('/webrtc/signaling/' + ROOM_ID, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify(data)
        }).catch(console.warn);
    });
}

// ═══════════════════════════════════════════════════════════
// POLL SIGNALS (host reads from host queue)
// ═══════════════════════════════════════════════════════════
async function pollSignals() {
    try {
        const r = await fetch(`/webrtc/signaling/${ROOM_ID}?role=host&sessionId=${SESSION_ID}`, {
            headers: { 'X-CSRF-TOKEN': CSRF }
        });
        if (!r.ok) {
            console.error('🔍 Teacher poll failed:', r.status, r.statusText);
            return;
        }
        const signals = await r.json();
        
        console.log('🔍 Teacher poll: received', signals.length, 'signals:', signals);
        
        for (const sig of (signals || [])) {
            await handleSignal(sig);
        }
    } catch(e) {
        console.error('🔍 Teacher poll error:', e);
    }
}

async function handleSignal(sig) {
    const from = sig.fromSession || sig.sessionId;
    console.log('🎯 Teacher handling signal:', sig.type, 'from:', from);

    if (sig.type === 'viewer_ready' || sig.type === 'participant_joined') {
        // New viewer arrived — create peer & send offer
        console.log('👋 New viewer detected, creating peer for:', from);
        if (!peers[from]) {
            await createPeerForViewer(from);
        }
        addParticipant(from, sig.name || 'Student');
        return;
    }

    if (sig.type === 'participant_left') {
        console.log('👋 Participant left:', from);
        removeParticipant(from);
        delete peers[from];
        return;
    }

    if (sig.type === 'raise_hand') {
        handleRaisedHand(sig.name, sig.raised, from);
        return;
    }

    if (sig.type === 'chat') {
        appendChat(sig.sender, sig.message, false);
        return;
    }

    // WebRTC answer / ICE from a viewer
    const pc = peers[from];
    if (!pc) {
        console.warn('⚠️ No peer connection found for:', from, 'available peers:', Object.keys(peers));
        return;
    }

    if (sig.type === 'answer') {
        console.log('📞 Received answer from:', from);
        if (pc.signalingState === 'have-local-offer') {
            await pc.setRemoteDescription({ type: 'answer', sdp: sig.sdp }).catch(console.warn);
        } else {
            console.warn('⚠️ Peer not in have-local-offer state:', pc.signalingState);
        }
    } else if (sig.type === 'ice' && sig.candidate) {
        console.log('🧊 Received ICE candidate from:', from);
        await pc.addIceCandidate(new RTCIceCandidate(sig.candidate)).catch(() => {});
    }
}

setInterval(pollSignals, 2000);

// ═══════════════════════════════════════════════════════════
// CAMERA
// ═══════════════════════════════════════════════════════════
const localVideo = document.getElementById('localVideo');
const camOff     = document.getElementById('camOff');
const camCtrl    = document.getElementById('camCtrl');
const micCtrl    = document.getElementById('micCtrl');

document.getElementById('startCamBtn').addEventListener('click', startCamera);
camCtrl.addEventListener('click', toggleCamera);
micCtrl.addEventListener('click', toggleMic);

async function startCamera() {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        localVideo.srcObject = localStream;
        localVideo.style.display = 'block';
        camOff.style.display = 'none';
        camOn = true; micOn = true;
        camCtrl.classList.add('on');
        camCtrl.querySelector('span').textContent = 'Camera On';
        micCtrl.classList.add('on');
        micCtrl.querySelector('i').className = 'fas fa-microphone';
        micCtrl.querySelector('span').textContent = 'Mic On';
        // Add tracks to all existing peers
        Object.values(peers).forEach(pc => {
            localStream.getTracks().forEach(t => {
                try { pc.addTrack(t, localStream); } catch(e) {}
            });
        });
        toast('Camera & mic started');
    } catch(e) { toast('Camera access denied'); }
}

function toggleCamera() {
    if (!localStream) { startCamera(); return; }
    const vt = localStream.getVideoTracks()[0];
    if (!vt) return;
    camOn = !camOn;
    vt.enabled = camOn;
    localVideo.style.display = camOn ? 'block' : 'none';
    camOff.style.display      = camOn ? 'none'  : 'flex';
    camCtrl.classList.toggle('on', camOn);
    camCtrl.querySelector('span').textContent = camOn ? 'Camera On' : 'Camera';
    toast(camOn ? 'Camera on' : 'Camera off');
}

function toggleMic() {
    if (!localStream) return;
    const at = localStream.getAudioTracks()[0];
    if (!at) return;
    micOn = !micOn;
    at.enabled = micOn;
    micCtrl.classList.toggle('on', micOn);
    micCtrl.querySelector('i').className = micOn ? 'fas fa-microphone' : 'fas fa-microphone-slash';
    micCtrl.querySelector('span').textContent = micOn ? 'Mic On' : 'Muted';
    toast(micOn ? 'Microphone on' : 'Muted');
}

// ═══════════════════════════════════════════════════════════
// SCREEN SHARE
// ═══════════════════════════════════════════════════════════
const screenCtrl   = document.getElementById('screenCtrl');
const screenBanner = document.getElementById('screenBanner');

screenCtrl.addEventListener('click', async () => {
    if (sharing) {
        // Stop screen share — revert to camera
        localStream?.getVideoTracks().forEach(t => t.stop());
        sharing = false;
        screenCtrl.classList.remove('on');
        screenCtrl.querySelector('span').textContent = 'Share';
        screenBanner.classList.remove('show');
        localVideo.style.display = 'none';
        camOff.style.display = 'flex';
        toast('Screen sharing stopped');
        return;
    }
    try {
        const ss = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: true });
        // Replace video track in all peers
        const senderReplacement = ss.getVideoTracks()[0];
        Object.values(peers).forEach(pc => {
            const sender = pc.getSenders().find(s => s.track?.kind === 'video');
            if (sender) sender.replaceTrack(senderReplacement).catch(() => {});
            else pc.addTrack(senderReplacement, ss);
        });
        localVideo.srcObject = ss;
        localVideo.style.display = 'block';
        camOff.style.display = 'none';
        sharing = true;
        screenCtrl.classList.add('on');
        screenCtrl.querySelector('span').textContent = 'Sharing';
        screenBanner.classList.add('show');
        ss.getVideoTracks()[0].addEventListener('ended', () => {
            sharing = false;
            screenCtrl.classList.remove('on');
            screenCtrl.querySelector('span').textContent = 'Share';
            screenBanner.classList.remove('show');
            localVideo.style.display = 'none';
            camOff.style.display = 'flex';
        });
        toast('Screen sharing started');
    } catch(e) { toast('Screen share cancelled'); }
});

// ═══════════════════════════════════════════════════════════
// PARTICIPANTS
// ═══════════════════════════════════════════════════════════
function addParticipant(sessionId, name) {
    if (participants[sessionId]) return;
    participants[sessionId] = { name };
    updateCount();
    // Add thumb
    const initial = name.charAt(0).toUpperCase();
    const colors  = ['rgba(16,185,129,.2)', 'rgba(245,158,11,.2)', 'rgba(239,68,68,.2)', 'rgba(99,102,241,.2)'];
    const fcolors = ['#10b981','#f59e0b','#ef4444','#6366f1'];
    const ci      = Object.keys(participants).length % colors.length;
    document.getElementById('thumbsRow').insertAdjacentHTML('beforeend', `
        <div class="thumb" id="thumb-${sessionId}">
            <div class="thumb-av" style="background:${colors[ci]};color:${fcolors[ci]}">${initial}</div>
            <div class="thumb-name">${name}</div>
        </div>`);
    // Add to people panel
    document.getElementById('pplList').insertAdjacentHTML('beforeend', `
        <div class="person" id="person-${sessionId}">
            <div class="pav" style="background:rgba(16,185,129,.15);color:var(--green)">${initial}</div>
            <div class="pinfo"><div class="pname">${name}</div><div class="prole">Student · Viewing</div></div>
            <span class="ptag student">Student</span>
        </div>`);
    toast(`${name} joined`);
}

function removeParticipant(sessionId) {
    if (!participants[sessionId]) return;
    const name = participants[sessionId].name;
    delete participants[sessionId];
    document.getElementById('thumb-' + sessionId)?.remove();
    document.getElementById('person-' + sessionId)?.remove();
    updateCount();
    toast(`${name} left`);
}

function updateCount() {
    const n = Object.keys(participants).length;
    document.getElementById('pcount').textContent = n;
    document.getElementById('ppl-cnt').textContent = (n + 1) + ' people';
}

// ═══════════════════════════════════════════════════════════
// CHAT
// ═══════════════════════════════════════════════════════════
const chatMsgs  = document.getElementById('chatMsgs');
const chatEmpty = document.getElementById('chatEmpty');
const chatInput = document.getElementById('chatInput');

function appendChat(sender, text, isSelf) {
    if (!chatStarted) { chatEmpty.style.display = 'none'; chatStarted = true; }
    const t    = new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' });
    const init = sender.charAt(0).toUpperCase();
    const bg   = isSelf ? 'rgba(59,130,246,.18)' : 'rgba(255,255,255,.08)';
    const fc   = isSelf ? 'var(--accent)' : 'var(--muted)';
    chatMsgs.insertAdjacentHTML('beforeend', `
        <div class="msg">
            <div class="mav2" style="background:${bg};color:${fc}">${init}</div>
            <div class="mbody">
                <div class="mmeta"><span class="mname">${sender}</span><span class="mtime">${t}</span></div>
                <div class="mtext">${text}</div>
            </div>
        </div>`);
    chatMsgs.scrollTop = chatMsgs.scrollHeight;
    if (!isSelf && !document.getElementById('panel-chat').classList.contains('active')) {
        chatUnread++;
        const b = document.getElementById('chat-badge');
        b.textContent  = chatUnread;
        b.style.display = 'inline-block';
    }
}

function sendChat() {
    const txt = chatInput.value.trim(); if (!txt) return;
    appendChat(SELF_NAME + ' (You)', txt, true);
    chatInput.value = '';
    fetch('/webrtc/chat/' + ROOM_ID, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ message: txt, role: 'host', sessionId: SESSION_ID })
    }).catch(console.warn);
}

document.getElementById('sendBtn').addEventListener('click', sendChat);
chatInput.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendChat(); } });

// ═══════════════════════════════════════════════════════════
// RAISE HAND
// ═══════════════════════════════════════════════════════════
function handleRaisedHand(name, raised, sessionId) {
    if (raised) {
        handCount++;
        document.getElementById('handsEmpty').style.display = 'none';
        document.getElementById('handsList').insertAdjacentHTML('beforeend', `
            <div id="hand-${sessionId}" style="display:flex;align-items:center;gap:10px;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);border-radius:8px;padding:8px 12px;">
                <span style="font-size:16px">✋</span>
                <span style="font-size:12px;font-weight:600">${name}</span>
                <button onclick="lowerHand('${sessionId}')" style="margin-left:auto;background:transparent;border:none;color:var(--muted);cursor:pointer;font-size:10px">Dismiss</button>
            </div>`);
        const b = document.getElementById('hand-badge');
        b.textContent  = handCount;
        b.style.display = 'inline-block';
        // Show notification
        document.getElementById('handName').textContent = name;
        const n = document.getElementById('handNotif');
        n.classList.add('show');
        setTimeout(() => n.classList.remove('show'), 3500);
    } else {
        document.getElementById('hand-' + sessionId)?.remove();
        handCount = Math.max(0, handCount - 1);
        if (handCount === 0) {
            document.getElementById('handsEmpty').style.display = 'flex';
            document.getElementById('hand-badge').style.display = 'none';
        } else {
            document.getElementById('hand-badge').textContent = handCount;
        }
    }
}

function lowerHand(sessionId) {
    document.getElementById('hand-' + sessionId)?.remove();
    handCount = Math.max(0, handCount - 1);
    if (handCount === 0) {
        document.getElementById('handsEmpty').style.display = 'flex';
        document.getElementById('hand-badge').style.display = 'none';
    } else {
        document.getElementById('hand-badge').textContent = handCount;
    }
}

// ═══════════════════════════════════════════════════════════
// FULLSCREEN
// ═══════════════════════════════════════════════════════════
document.getElementById('fsCtrl').addEventListener('click', () => {
    const stage = document.getElementById('stage');
    if (!document.fullscreenElement) {
        stage.requestFullscreen().catch(() => {});
        document.getElementById('fsCtrl').querySelector('i').className = 'fas fa-compress-arrows-alt';
    } else {
        document.exitFullscreen();
        document.getElementById('fsCtrl').querySelector('i').className = 'fas fa-expand-arrows-alt';
    }
});

// ═══════════════════════════════════════════════════════════
// END CLASS
// ═══════════════════════════════════════════════════════════
document.getElementById('endBtn').addEventListener('click', () => {
    if (!confirm('End class for all participants?')) return;
    localStream?.getTracks().forEach(t => t.stop());
    Object.values(peers).forEach(pc => pc.close());
    fetch('/teacher/live-classes/{{ $liveClass->id ?? 1 }}/end', {
        method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF }
    }).finally(() => window.location.href = '/teacher/live-classes');
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