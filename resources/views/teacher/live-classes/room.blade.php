{{-- resources/views/teacher/live-classes/room.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Class — {{ $liveClass->title ?? 'testing live class' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --bg:        #0b0f1a;
            --surface:   #111827;
            --surface2:  #1a2235;
            --border:    rgba(255,255,255,0.07);
            --accent:    #3b82f6;
            --accent2:   #06b6d4;
            --danger:    #ef4444;
            --success:   #10b981;
            --warn:      #f59e0b;
            --text:      #f1f5f9;
            --muted:     #64748b;
            --live:      #ef4444;
            --radius:    14px;
            --radius-sm: 8px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ── TOP BAR ─────────────────────────────────── */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            height: 64px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
            z-index: 10;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo-mark {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 18px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .divider-v { width: 1px; height: 24px; background: var(--border); }

        .class-title {
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            max-width: 260px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .live-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.3);
            color: var(--live);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.2px;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .live-dot {
            width: 7px; height: 7px;
            background: var(--live);
            border-radius: 50%;
            animation: pulse-dot 1.4s infinite;
        }

        @keyframes pulse-dot {
            0%,100% { opacity: 1; transform: scale(1); }
            50%      { opacity: 0.5; transform: scale(0.7); }
        }

        .topbar-center {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 500;
        }

        .stat-chip i { font-size: 12px; color: var(--accent2); }

        .timer { font-family: 'Syne', sans-serif; font-size: 14px; font-weight: 700; color: var(--accent2); }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .teacher-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 40px;
            padding: 6px 14px 6px 6px;
        }

        .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-size: 13px; font-weight: 700;
            flex-shrink: 0;
            overflow: hidden;
        }

        .avatar img { width: 100%; height: 100%; object-fit: cover; }

        .teacher-info { line-height: 1.2; }
        .teacher-name { font-size: 13px; font-weight: 600; }
        .teacher-dept { font-size: 11px; color: var(--muted); }

        /* ── MAIN LAYOUT ─────────────────────────────── */
        .main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* ── VIDEO AREA ─────────────────────────────── */
        .video-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0;
            padding: 20px;
            overflow: hidden;
        }

        .video-stage {
            flex: 1;
            position: relative;
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Subtle grid overlay for the empty video state */
        .video-stage::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(59,130,246,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59,130,246,0.04) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
        }

        .camera-off-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            z-index: 1;
        }

        .camera-off-icon {
            width: 88px; height: 88px;
            border-radius: 50%;
            background: rgba(59,130,246,0.1);
            border: 2px dashed rgba(59,130,246,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            color: var(--accent);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }

        .camera-off-label {
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }

        .camera-off-sub {
            font-size: 13px;
            color: var(--muted);
            text-align: center;
            max-width: 240px;
            line-height: 1.5;
        }

        .btn-start-camera {
            display: flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 11px 28px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 24px rgba(59,130,246,0.35);
            margin-top: 4px;
        }

        .btn-start-camera:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(59,130,246,0.5);
        }

        /* Self-view thumbnail (bottom-right corner of stage) */
        .self-view {
            position: absolute;
            bottom: 16px; right: 16px;
            width: 160px; height: 100px;
            background: var(--surface2);
            border-radius: 10px;
            border: 2px solid var(--border);
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; color: var(--muted);
        }

        /* ── CONTROL BAR ─────────────────────────────── */
        .control-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px 0 0;
        }

        .ctrl-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 18px;
            cursor: pointer;
            transition: all 0.18s;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 11px;
            font-weight: 500;
            min-width: 64px;
        }

        .ctrl-btn i { font-size: 18px; }

        .ctrl-btn:hover {
            background: var(--surface2);
            border-color: rgba(59,130,246,0.3);
            transform: translateY(-2px);
        }

        .ctrl-btn.active {
            background: rgba(59,130,246,0.15);
            border-color: rgba(59,130,246,0.4);
            color: var(--accent);
        }

        .ctrl-btn.danger {
            background: rgba(239,68,68,0.1);
            border-color: rgba(239,68,68,0.3);
            color: var(--danger);
        }

        .ctrl-btn.danger:hover {
            background: rgba(239,68,68,0.2);
            border-color: rgba(239,68,68,0.5);
        }

        /* ── SIDEBAR ─────────────────────────────────── */
        .sidebar {
            width: 320px;
            flex-shrink: 0;
            background: var(--surface);
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-tabs {
            display: flex;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .tab-btn {
            flex: 1;
            padding: 14px;
            background: transparent;
            border: none;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-transform: uppercase;
            position: relative;
        }

        .tab-btn.active {
            color: var(--accent);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: 0; left: 20%; right: 20%;
            height: 2px;
            background: var(--accent);
            border-radius: 2px 2px 0 0;
        }

        .tab-badge {
            background: var(--danger);
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 5px;
            border-radius: 10px;
            line-height: 1;
        }

        .tab-panel { display: none; flex: 1; flex-direction: column; overflow: hidden; }
        .tab-panel.active { display: flex; }

        /* ── PEOPLE PANEL ────────────────────────────── */
        .people-header {
            padding: 16px 18px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .people-header h3 {
            font-family: 'Syne', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
        }

        .people-count {
            font-size: 11px;
            color: var(--muted);
            background: var(--surface2);
            padding: 3px 9px;
            border-radius: 20px;
        }

        .people-list {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .people-list::-webkit-scrollbar { width: 4px; }
        .people-list::-webkit-scrollbar-track { background: transparent; }
        .people-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        .person-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            transition: background 0.15s;
        }

        .person-card:hover { background: var(--surface2); }

        .person-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-size: 13px; font-weight: 700;
            flex-shrink: 0;
        }

        .person-info { flex: 1; line-height: 1.3; }
        .person-name { font-size: 13px; font-weight: 500; }
        .person-role { font-size: 11px; color: var(--muted); }

        .role-tag {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-tag.teacher { background: rgba(59,130,246,0.15); color: var(--accent); }
        .role-tag.student { background: rgba(16,185,129,0.12); color: var(--success); }
        .role-tag.guest   { background: rgba(245,158,11,0.12); color: var(--warn); }

        /* ── REQUESTS PANEL ──────────────────────────── */
        .empty-state {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--muted);
            padding: 24px;
            text-align: center;
        }

        .empty-icon { font-size: 36px; opacity: 0.4; }

        .empty-state p { font-size: 13px; line-height: 1.5; }

        /* ── CHAT PANEL ──────────────────────────────── */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .chat-messages::-webkit-scrollbar { width: 4px; }
        .chat-messages::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        .msg { display: flex; gap: 10px; align-items: flex-start; }

        .msg-avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700;
            flex-shrink: 0;
        }

        .msg-body { flex: 1; }
        .msg-meta { display: flex; align-items: baseline; gap: 6px; margin-bottom: 3px; }
        .msg-name { font-size: 12px; font-weight: 600; }
        .msg-time { font-size: 10px; color: var(--muted); }

        .msg-text {
            background: var(--surface2);
            border-radius: 0 10px 10px 10px;
            padding: 8px 12px;
            font-size: 13px;
            line-height: 1.5;
            border: 1px solid var(--border);
        }

        .chat-input-wrap {
            padding: 12px;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 8px;
            align-items: center;
            flex-shrink: 0;
        }

        .chat-input {
            flex: 1;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 9px 16px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
        }

        .chat-input::placeholder { color: var(--muted); }
        .chat-input:focus { border-color: rgba(59,130,246,0.4); }

        .btn-send {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border: none;
            color: #fff;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            transition: transform 0.15s, box-shadow 0.15s;
            flex-shrink: 0;
        }

        .btn-send:hover { transform: scale(1.1); box-shadow: 0 4px 16px rgba(59,130,246,0.4); }

        /* ── END CLASS BUTTON ────────────────────────── */
        .btn-end {
            display: flex; align-items: center; gap: 8px;
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.35);
            color: var(--danger);
            border-radius: 50px;
            padding: 9px 20px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.18s;
        }

        .btn-end:hover {
            background: rgba(239,68,68,0.25);
            transform: translateY(-1px);
        }

        /* Class Daily button */
        .btn-daily {
            display: flex; align-items: center; gap: 8px;
            background: rgba(6,182,212,0.12);
            border: 1px solid rgba(6,182,212,0.3);
            color: var(--accent2);
            border-radius: 50px;
            padding: 9px 18px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
        }

        .btn-daily:hover {
            background: rgba(6,182,212,0.2);
            transform: translateY(-1px);
        }

        /* ── PARTICIPANTS GRID (thumbnails) ──────────── */
        .thumbs-row {
            display: flex;
            gap: 8px;
            padding: 10px 0 0;
            overflow-x: auto;
        }

        .thumb {
            width: 100px; height: 64px;
            background: var(--surface2);
            border-radius: 8px;
            border: 1px solid var(--border);
            flex-shrink: 0;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 4px;
            font-size: 10px;
            color: var(--muted);
            position: relative;
            overflow: hidden;
        }

        .thumb-avatar {
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700;
        }

        .thumb-name { font-size: 10px; font-weight: 500; color: var(--text); }

        /* Connection quality indicator */
        .quality {
            position: absolute;
            top: 5px; right: 5px;
            display: flex; gap: 1.5px; align-items: flex-end; height: 10px;
        }

        .quality span {
            width: 3px;
            background: var(--success);
            border-radius: 1px;
        }

        .quality span:nth-child(1) { height: 30%; }
        .quality span:nth-child(2) { height: 60%; }
        .quality span:nth-child(3) { height: 100%; }

        /* Announcement strip */
        .announcement-strip {
            margin: 0 20px 0;
            background: rgba(59,130,246,0.08);
            border: 1px solid rgba(59,130,246,0.18);
            border-radius: var(--radius-sm);
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: var(--muted);
        }

        .announcement-strip i { color: var(--accent); }

        textarea.announcement-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            color: var(--text);
            resize: none;
            line-height: 1.4;
        }

        .announcement-input::placeholder { color: var(--muted); }

        .btn-announce {
            background: var(--accent);
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 5px 12px;
            cursor: pointer;
            transition: opacity 0.15s;
            white-space: nowrap;
        }

        .btn-announce:hover { opacity: 0.85; }

        /* ── RESPONSIVE ──────────────────────────────── */
        @media (max-width: 900px) {
            .sidebar { width: 260px; }
            .topbar-center { display: none; }
        }
    </style>
</head>
<body>

    {{-- ── TOP BAR ─────────────────────────────────────── --}}
    <header class="topbar">
        <div class="topbar-left">
            <span class="logo-mark">ESA</span>
            <div class="divider-v"></div>
            <span class="class-title">{{ $liveClass->title ?? 'testing live class' }}</span>
            <div class="live-badge">
                <span class="live-dot"></span>
                Live
            </div>
        </div>

        <div class="topbar-center">
            <div class="stat-chip">
                <i class="fas fa-clock"></i>
                <span class="timer" id="timer">00:00:10</span>
            </div>
            <div class="stat-chip">
                <i class="fas fa-users"></i>
                <span id="participant-count">2</span> participants
            </div>
        </div>

        <div class="topbar-right">
            <a href="#" class="btn-daily">
                <i class="fas fa-clipboard-list"></i>
                Class Daily
            </a>

            <div class="teacher-pill">
                <div class="avatar">
                    @if(auth()->guard('teacher')->user()->photo ?? false)
                        <img src="{{ asset('storage/' . auth()->guard('teacher')->user()->photo) }}" alt="">
                    @else
                        {{ strtoupper(substr(auth()->guard('teacher')->user()->name ?? 'T', 0, 1)) }}
                    @endif
                </div>
                <div class="teacher-info">
                    <div class="teacher-name">{{ auth()->guard('teacher')->user()->name ?? 'Hirwa Alain Brian' }}</div>
                    <div class="teacher-dept">Teacher · Kigali</div>
                </div>
            </div>

            <button class="btn-end" id="end-class-btn">
                <i class="fas fa-phone-slash"></i>
                End Class
            </button>
        </div>
    </header>

    {{-- ── MAIN ────────────────────────────────────────── --}}
    <div class="main">

        {{-- Video Area --}}
        <div class="video-area">

            {{-- Main Stage --}}
            <div class="video-stage" id="video-stage">
                <video id="localVideo" autoplay muted playsinline style="display:none; width:100%; height:100%; object-fit:cover; border-radius:14px;"></video>
                <div class="camera-off-state" id="camera-off-state">
                    <div class="camera-off-icon">
                        <i class="fas fa-video-slash"></i>
                    </div>
                    <div class="camera-off-label">Camera is Off</div>
                    <div class="camera-off-sub">Turn on your camera or share your screen to start broadcasting to students.</div>
                    <button class="btn-start-camera" id="startCameraBtn">
                        <i class="fas fa-video"></i>
                        Start Camera
                    </button>
                </div>

                {{-- Self-view --}}
                <div class="self-view" id="selfViewBox">
                    <span style="font-size:11px">Your preview</span>
                </div>
            </div>

            {{-- Participant thumbnails --}}
            <div class="thumbs-row" id="thumbs-row">
                <div class="thumb">
                    <div class="thumb-avatar" style="background:rgba(59,130,246,0.2);color:#3b82f6;">T</div>
                    <div class="thumb-name">Teacher (You)</div>
                    <div class="quality"><span></span><span></span><span></span></div>
                </div>
                <div class="thumb">
                    <div class="thumb-avatar" style="background:rgba(16,185,129,0.2);color:#10b981;">G</div>
                    <div class="thumb-name">Guest_a45d</div>
                    <div class="quality"><span></span><span></span><span style="background:var(--warn)"></span></div>
                </div>
                <div class="thumb" style="border-style:dashed;opacity:0.5;">
                    <i class="fas fa-user-plus" style="font-size:16px;color:var(--muted)"></i>
                    <div class="thumb-name" style="color:var(--muted)">Waiting...</div>
                </div>
            </div>

            {{-- Announcement strip --}}
            <div class="announcement-strip" style="margin-top:12px;">
                <i class="fas fa-bullhorn"></i>
                <textarea class="announcement-input" rows="1" placeholder="Type an announcement to broadcast to all participants…"></textarea>
                <button class="btn-announce"><i class="fas fa-paper-plane"></i> Send</button>
            </div>

            {{-- Control bar --}}
            <div class="control-bar">
                <button class="ctrl-btn" id="micBtn" title="Toggle Mic">
                    <i class="fas fa-microphone"></i>
                    <span>Mic</span>
                </button>
                <button class="ctrl-btn" id="cameraBtn" title="Toggle Camera">
                    <i class="fas fa-video"></i>
                    <span>Camera</span>
                </button>
                <button class="ctrl-btn" id="screenBtn" title="Share Screen">
                    <i class="fas fa-desktop"></i>
                    <span>Screen</span>
                </button>
                <button class="ctrl-btn" id="raiseBtn" title="Raise Hand">
                    <i class="fas fa-hand-paper"></i>
                    <span>Raise</span>
                </button>
                <button class="ctrl-btn" id="recordBtn" title="Record">
                    <i class="fas fa-circle" style="color:var(--danger);font-size:12px;"></i>
                    <span>Record</span>
                </button>
                <button class="ctrl-btn" id="settingsBtn" title="Settings">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </button>
            </div>
        </div>

        {{-- ── SIDEBAR ──────────────────────────────────── --}}
        <aside class="sidebar">

            {{-- Tabs --}}
            <div class="sidebar-tabs">
                <button class="tab-btn active" data-tab="people">
                    <i class="fas fa-users"></i> People
                </button>
                <button class="tab-btn" data-tab="requests">
                    <i class="fas fa-hand-paper"></i> Requests
                    <span class="tab-badge" id="req-badge">0</span>
                </button>
                <button class="tab-btn" data-tab="chat">
                    <i class="fas fa-comment"></i> Chat
                </button>
            </div>

            {{-- People Panel --}}
            <div class="tab-panel active" id="panel-people">
                <div class="people-header">
                    <h3>In this session</h3>
                    <span class="people-count" id="ppl-count">2 people</span>
                </div>
                <div class="people-list">
                    <div class="person-card">
                        <div class="person-avatar" style="background:rgba(59,130,246,0.15);color:var(--accent);">T</div>
                        <div class="person-info">
                            <div class="person-name">{{ auth()->guard('teacher')->user()->name ?? 'Hirwa Alain Brian' }}</div>
                            <div class="person-role">Host · REB Combinations</div>
                        </div>
                        <span class="role-tag teacher">Host</span>
                    </div>

                    <div class="person-card">
                        <div class="person-avatar" style="background:rgba(245,158,11,0.15);color:var(--warn);">G</div>
                        <div class="person-info">
                            <div class="person-name">Guest_a45d</div>
                            <div class="person-role">Guest participant</div>
                        </div>
                        <span class="role-tag guest">Guest</span>
                    </div>

                    {{-- More participants loaded dynamically --}}
                    <div id="dynamic-participants"></div>
                </div>
            </div>

            {{-- Requests Panel --}}
            <div class="tab-panel" id="panel-requests">
                <div class="empty-state" id="requests-empty">
                    <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                    <p>No pending join requests.<br>Requests will appear here when students ask to join.</p>
                </div>
                <div id="requests-list" style="padding:12px;display:flex;flex-direction:column;gap:8px;"></div>
            </div>

            {{-- Chat Panel --}}
            <div class="tab-panel" id="panel-chat">
                <div class="chat-messages" id="chat-messages">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-comments"></i></div>
                        <p>No messages yet.<br>Send a message to your participants.</p>
                    </div>
                </div>
                <div class="chat-input-wrap">
                    <input type="text" class="chat-input" id="chatInput" placeholder="Send a message…">
                    <button class="btn-send" id="sendBtn"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>

        </aside>
    </div>

    <script>
    // ── TIMER ──────────────────────────────────────────
    let seconds = 10;
    const timerEl = document.getElementById('timer');
    setInterval(() => {
        seconds++;
        const h = String(Math.floor(seconds/3600)).padStart(2,'0');
        const m = String(Math.floor((seconds%3600)/60)).padStart(2,'0');
        const s = String(seconds%60).padStart(2,'0');
        timerEl.textContent = `${h}:${m}:${s}`;
    }, 1000);

    // ── TABS ────────────────────────────────────────────
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('panel-' + btn.dataset.tab).classList.add('active');
        });
    });

    // ── CAMERA ─────────────────────────────────────────
    let localStream = null;
    let cameraOn = false;
    let micOn = true;

    const localVideo   = document.getElementById('localVideo');
    const cameraOffEl  = document.getElementById('camera-off-state');
    const cameraBtn    = document.getElementById('cameraBtn');
    const startCamBtn  = document.getElementById('startCameraBtn');
    const micBtn       = document.getElementById('micBtn');

    async function startCamera() {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            localVideo.srcObject = localStream;
            localVideo.style.display = 'block';
            cameraOffEl.style.display = 'none';
            cameraOn = true;
            cameraBtn.classList.add('active');
            cameraBtn.querySelector('span').textContent = 'Camera On';
        } catch(e) {
            console.warn('Camera access denied', e);
        }
    }

    function toggleCamera() {
        if (!localStream) { startCamera(); return; }
        const vTrack = localStream.getVideoTracks()[0];
        if (vTrack) {
            cameraOn = !cameraOn;
            vTrack.enabled = cameraOn;
            localVideo.style.display = cameraOn ? 'block' : 'none';
            cameraOffEl.style.display = cameraOn ? 'none' : 'flex';
            cameraBtn.classList.toggle('active', cameraOn);
            cameraBtn.querySelector('span').textContent = cameraOn ? 'Camera On' : 'Camera';
        }
    }

    function toggleMic() {
        if (!localStream) return;
        const aTrack = localStream.getAudioTracks()[0];
        if (aTrack) {
            micOn = !micOn;
            aTrack.enabled = micOn;
            micBtn.classList.toggle('active', micOn);
            micBtn.querySelector('i').className = micOn ? 'fas fa-microphone' : 'fas fa-microphone-slash';
            micBtn.querySelector('span').textContent = micOn ? 'Mic' : 'Muted';
        }
    }

    startCamBtn.addEventListener('click', startCamera);
    cameraBtn.addEventListener('click', toggleCamera);
    micBtn.addEventListener('click', toggleMic);

    // ── SCREEN SHARE ───────────────────────────────────
    document.getElementById('screenBtn').addEventListener('click', async () => {
        try {
            const screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true });
            localVideo.srcObject = screenStream;
            localVideo.style.display = 'block';
            cameraOffEl.style.display = 'none';
            screenStream.getVideoTracks()[0].addEventListener('ended', () => {
                cameraOffEl.style.display = 'flex';
                localVideo.style.display = 'none';
            });
        } catch(e) { console.warn('Screen share cancelled', e); }
    });

    // ── CHAT ───────────────────────────────────────────
    const chatMessages = document.getElementById('chat-messages');
    const chatInput    = document.getElementById('chatInput');
    const sendBtn      = document.getElementById('sendBtn');
    let hasChat = false;

    function sendMessage() {
        const text = chatInput.value.trim();
        if (!text) return;
        if (!hasChat) { chatMessages.innerHTML = ''; hasChat = true; }
        const initial = '{{ strtoupper(substr(auth()->guard("teacher")->user()->name ?? "T", 0, 1)) }}';
        const name    = '{{ auth()->guard("teacher")->user()->name ?? "Teacher" }}';
        const now     = new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
        chatMessages.insertAdjacentHTML('beforeend', `
            <div class="msg">
                <div class="msg-avatar" style="background:rgba(59,130,246,0.18);color:var(--accent);">${initial}</div>
                <div class="msg-body">
                    <div class="msg-meta">
                        <span class="msg-name">${name}</span>
                        <span class="msg-time">${now}</span>
                    </div>
                    <div class="msg-text">${text}</div>
                </div>
            </div>
        `);
        chatInput.value = '';
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); } });

    // ── END CLASS ──────────────────────────────────────
    document.getElementById('end-class-btn').addEventListener('click', () => {
        if (confirm('Are you sure you want to end this class for all participants?')) {
            if (localStream) localStream.getTracks().forEach(t => t.stop());
            // POST to end route
            fetch(`/teacher/live-classes/{{ $liveClass->id ?? 1 }}/end`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
            }).then(() => window.location.href = '/teacher/live-classes').catch(() => {});
        }
    });

    // ── CONTROL TOGGLES ────────────────────────────────
    ['raiseBtn','recordBtn','settingsBtn'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    });
    </script>

</body>
</html>