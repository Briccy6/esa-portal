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

    /* Stage area: video + controls */
    .lc-stage {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    /* Video container */
    .video-container {
        flex: 1;
        background: #000;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 0;
    }

    /* Control bar */
    .lc-controls {
        height: var(--ctrl-h);
        background: var(--panel);
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        flex-shrink: 0;
    }

    /* Sidebar */
    .lc-sidebar {
        width: var(--sidebar-w);
        background: var(--panel);
        border-left: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    /* Sidebar tabs */
    .sidebar-tabs {
        display: flex;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }

    .sidebar-tab {
        flex: 1;
        padding: 16px;
        background: transparent;
        border: none;
        color: var(--text-2);
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
        font-size: 14px;
    }

    .sidebar-tab.active {
        color: var(--text);
        background: var(--card);
        border-bottom: 2px solid var(--blue);
    }

    .sidebar-tab:hover {
        color: var(--text);
    }

    /* Sidebar content */
    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
    }

    .sidebar-panel {
        display: none;
    }

    .sidebar-panel.active {
        display: block;
    }

    /* Live indicator */
    .live-indicator {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--live-red);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .live-dot {
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Control buttons */
    .control-btn {
        background: var(--card);
        border: 1px solid var(--border);
        color: var(--text);
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .control-btn:hover {
        background: var(--border-hi);
        border-color: var(--blue);
    }

    .control-btn.danger {
        background: var(--red);
        border-color: var(--red);
        color: white;
    }

    .control-btn.danger:hover {
        background: #c04040;
        border-color: #c04040;
    }

    /* Request items */
    .request-item {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.2s;
    }

    .request-item:hover {
        border-color: var(--border-hi);
    }

    .request-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 8px;
    }

    .request-name {
        font-weight: 600;
        color: var(--text);
    }

    .request-time {
        font-size: 12px;
        color: var(--text-2);
    }

    .request-message {
        color: var(--text-2);
        font-size: 14px;
        line-height: 1.4;
        margin-bottom: 12px;
    }

    .request-actions {
        display: flex;
        gap: 8px;
    }

    .request-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }

    .request-btn.approve {
        background: var(--green);
        color: white;
    }

    .request-btn.approve:hover {
        background: #45b570;
    }

    .request-btn.reject {
        background: var(--red);
        color: white;
    }

    .request-btn.reject:hover {
        background: #c04040;
    }

    /* Center placeholder */
    .center-placeholder {
        text-align: center;
        color: var(--text-2);
    }

    .center-placeholder h3 {
        margin-bottom: 8px;
        color: var(--text);
    }

    /* Admin badge */
    .admin-badge {
        background: var(--blue);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .lc-body {
            flex-direction: column;
        }
        
        .lc-sidebar {
            width: 100%;
            height: 300px;
            border-left: none;
            border-top: 1px solid var(--border);
        }
    }
</style>
@endsection

@section('content')
<div class="lc-root">
    <!-- Header -->
    <div style="padding: 20px; background: var(--panel); border-bottom: 1px solid var(--border);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                    <h1 style="font-size: 24px; font-weight: 600;">{{ $liveClass->title }}</h1>
                    <span class="admin-badge">ADMIN</span>
                </div>
                <div style="display: flex; align-items: center; gap: 16px; color: var(--text-2); font-size: 14px;">
                    <span>Teacher: {{ $liveClass->teacher ? $liveClass->teacher->full_name : 'Not assigned' }}</span>
                    <span>•</span>
                    <span>{{ $liveClass->courses->pluck('name')->join(', ') }}</span>
                    <span>•</span>
                    <span>{{ $liveClass->branches->pluck('name')->join(', ') }}</span>
                    <div class="live-indicator">
                        <div class="live-dot"></div>
                        LIVE
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('admin.live-classes.show', $liveClass) }}" class="control-btn">
                    📋 Class Details
                </a>
                <form action="{{ route('admin.live-classes.end', $liveClass) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="control-btn danger" onclick="return confirm('Are you sure you want to end this live class?')">
                        End Class
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main content area -->
    <div class="lc-body">
        <!-- Stage area -->
        <div class="lc-stage">
            <!-- Video container -->
            <div class="video-container">
                <div class="center-placeholder">
                    <h3>Admin Monitor</h3>
                    <p>Live class monitoring interface</p>
                    <p style="margin-top: 16px;">
                        <small>Teacher: {{ $liveClass->teacher ? $liveClass->teacher->full_name : 'No teacher assigned' }}</small>
                    </p>
                    <p style="margin-top: 8px;">
                        <small>WebRTC integration coming soon...</small>
                    </p>
                </div>
            </div>

            <!-- Control bar -->
            <div class="lc-controls">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <button class="control-btn" id="monitorBtn">
                        👁️ Monitor
                    </button>
                    <button class="control-btn" id="recordBtn">
                        ⏺️ Record
                    </button>
                    <button class="control-btn" id="broadcastBtn">
                        📢 Broadcast
                    </button>
                </div>
                
                <div style="display: flex; align-items: center; gap: 12px;">
                    <button class="control-btn" id="chatBtn">
                        💬 Chat
                    </button>
                    <button class="control-btn" id="participantsBtn">
                        👥 Participants
                    </button>
                    <button class="control-btn" id="settingsBtn">
                        ⚙️ Settings
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lc-sidebar">
            <!-- Tabs -->
            <div class="sidebar-tabs">
                <button class="sidebar-tab active" data-tab="requests">
                    Requests ({{ $studentRequests->count() }})
                </button>
                <button class="sidebar-tab" data-tab="participants">
                    Participants
                </button>
                <button class="sidebar-tab" data-tab="chat">
                    Chat
                </button>
            </div>

            <!-- Content -->
            <div class="sidebar-content">
                <!-- Requests Panel -->
                <div class="sidebar-panel active" id="requests-panel">
                    @if($studentRequests->count() > 0)
                        @foreach($studentRequests as $request)
                            <div class="request-item">
                                <div class="request-header">
                                    <div class="request-name">{{ $request->user->full_name }}</div>
                                    <div class="request-time">{{ $request->created_at->diffForHumans() }}</div>
                                </div>
                                @if($request->reason)
                                    <div class="request-message">{{ $request->reason }}</div>
                                @endif
                                <div class="request-actions">
                                    <button class="request-btn approve" onclick="approveRequest({{ $request->id }})">
                                        Approve
                                    </button>
                                    <button class="request-btn reject" onclick="rejectRequest({{ $request->id }})">
                                        Reject
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="center-placeholder">
                            <p>No pending requests</p>
                        </div>
                    @endif
                </div>

                <!-- Participants Panel -->
                <div class="sidebar-panel" id="participants-panel">
                    <div class="center-placeholder">
                        <p>Participants will appear here</p>
                    </div>
                </div>

                <!-- Chat Panel -->
                <div class="sidebar-panel" id="chat-panel">
                    <div class="center-placeholder">
                        <p>Chat messages will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Tab switching
document.querySelectorAll('.sidebar-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        const targetTab = this.dataset.tab;
        
        // Update tab states
        document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        // Update panel states
        document.querySelectorAll('.sidebar-panel').forEach(panel => panel.classList.remove('active'));
        document.getElementById(targetTab + '-panel').classList.add('active');
    });
});

// Request handling
function approveRequest(requestId) {
    if (!confirm('Approve this request?')) return;
    
    fetch(`{{ route('live-classes.requests.approve', [$liveClass->id, '']) }}`.replace('/0', '/' + requestId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to approve request');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to approve request');
    });
}

function rejectRequest(requestId) {
    const reason = prompt('Reason for rejection (optional):');
    
    fetch(`{{ route('live-classes.requests.reject', [$liveClass->id, '']) }}`.replace('/0', '/' + requestId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to reject request');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to reject request');
    });
}

// Control buttons (placeholder functionality)
document.getElementById('monitorBtn').addEventListener('click', function() {
    this.style.opacity = this.style.opacity === '0.5' ? '1' : '0.5';
});

document.getElementById('recordBtn').addEventListener('click', function() {
    this.style.opacity = this.style.opacity === '0.5' ? '1' : '0.5';
    alert('Recording functionality coming soon...');
});

document.getElementById('broadcastBtn').addEventListener('click', function() {
    alert('Broadcast message functionality coming soon...');
});

document.getElementById('chatBtn').addEventListener('click', function() {
    document.querySelector('[data-tab="chat"]').click();
});

document.getElementById('participantsBtn').addEventListener('click', function() {
    document.querySelector('[data-tab="participants"]').click();
});

document.getElementById('settingsBtn').addEventListener('click', function() {
    alert('Admin settings coming soon...');
});

// Auto-refresh requests every 30 seconds
setInterval(() => {
    const activeTab = document.querySelector('.sidebar-tab.active').dataset.tab;
    if (activeTab === 'requests') {
        // Only refresh if requests tab is active
        location.reload();
    }
}, 30000);
</script>
@endsection
