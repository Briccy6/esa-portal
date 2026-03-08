@extends('layouts.app')

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
        --orange: #ff9f43;
        --purple: #8b5cf6;
    }

    body {
        background: var(--void);
        color: var(--text);
        font-family: 'DM Sans', sans-serif;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    .room-container {
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .room-header {
        background: var(--panel);
        padding: 1rem 2rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .room-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text);
        margin: 0;
    }

    .room-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: var(--text-2);
        font-size: 0.875rem;
    }

    .live-indicator {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--red);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .room-main {
        flex: 1;
        display: flex;
        overflow: hidden;
        min-height: 0;
    }

    .video-area {
        flex: 1;
        background: #000;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .main-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-video {
        text-align: center;
        color: var(--text-3);
    }

    .no-video i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .sidebar {
        width: 350px;
        background: var(--panel);
        border-left: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .sidebar-tabs {
        display: flex;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }

    .sidebar-tab {
        flex: 1;
        padding: 1rem;
        background: transparent;
        border: none;
        color: var(--text-2);
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 2px solid transparent;
    }

    .sidebar-tab.active {
        color: var(--text);
        border-bottom-color: var(--blue);
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }

    .sidebar-panel {
        display: none;
    }

    .sidebar-panel.active {
        display: block;
    }

    .chat-messages {
        max-height: 400px;
        overflow-y: auto;
        margin-bottom: 1rem;
    }

    .chat-message {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .chat-author {
        font-weight: 600;
        color: var(--blue);
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .chat-text {
        color: var(--text);
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .chat-input-area {
        display: flex;
        gap: 0.5rem;
    }

    .chat-input {
        flex: 1;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 0.375rem;
        padding: 0.5rem;
        color: var(--text);
        font-family: inherit;
    }

    .chat-send {
        background: var(--blue);
        color: white;
        border: none;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .chat-send:hover {
        background: #4a8fd4;
    }

    .participants-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .participant {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .participant-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--blue), var(--purple));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        flex-shrink: 0;
    }

    .participant-info {
        flex: 1;
        min-width: 0;
    }

    .participant-name {
        font-weight: 600;
        color: var(--text);
        font-size: 0.875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .participant-role {
        color: var(--text-2);
        font-size: 0.75rem;
    }

    .controls {
        background: var(--panel);
        padding: 1rem 2rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-shrink: 0;
    }

    .control-btn {
        background: var(--card);
        border: 1px solid var(--border);
        color: var(--text);
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-family: inherit;
        font-size: 0.875rem;
    }

    .control-btn:hover {
        background: var(--border);
        border-color: var(--border-hi);
    }

    .control-btn.active {
        background: var(--blue);
        color: white;
        border-color: var(--blue);
    }

    .control-btn.danger {
        background: rgba(224, 92, 92, 0.2);
        border-color: rgba(224, 92, 92, 0.3);
        color: var(--red);
    }

    .control-btn.danger:hover {
        background: var(--red);
        color: white;
        border-color: var(--red);
    }

    .empty-state {
        text-align: center;
        color: var(--text-3);
        padding: 2rem;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .room-main {
            flex-direction: column;
        }
        
        .sidebar {
            width: 100%;
            height: 300px;
            border-left: none;
            border-top: 1px solid var(--border);
        }
        
        .controls {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .control-btn {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
        }
    }
</style>
@endsection

@section('content')
<div class="room-container">
    <!-- Header -->
    <div class="room-header">
        <div>
            <h1 class="room-title">{{ $liveClass->title }}</h1>
            <div class="room-info">
                <span>{{ \Carbon\Carbon::parse($liveClass->start_time)->format('M j, Y H:i') }}</span>
                @if($liveClass->courses->count() > 0)
                    <span>•</span>
                    <span>{{ $liveClass->courses->pluck('name')->join(', ') }}</span>
                @endif
            </div>
        </div>
        <div class="room-info">
            <div class="live-indicator">
                <i class="fas fa-circle"></i>
                LIVE
            </div>
            <span id="timer">00:00:00</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="room-main">
        <!-- Video Area -->
        <div class="video-area">
            <video id="mainVideo" class="main-video" autoplay muted playsinline></video>
            
            <!-- No Video State -->
            <div class="no-video" id="noVideo">
                <i class="fas fa-video-slash"></i>
                <h3>Waiting for the instructor to start streaming...</h3>
                <p>The live class will begin soon</p>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Tabs -->
            <div class="sidebar-tabs">
                <button class="sidebar-tab active" data-tab="chat">
                    <i class="fas fa-comments"></i> Chat
                </button>
                <button class="sidebar-tab" data-tab="participants">
                    <i class="fas fa-users"></i> People
                </button>
            </div>

            <!-- Content -->
            <div class="sidebar-content">
                <!-- Chat Panel -->
                <div class="sidebar-panel active" id="chat-panel">
                    <div class="chat-messages" id="chatMessages">
                        <div class="empty-state">
                            <i class="fas fa-comments"></i>
                            <p>No messages yet. Start a conversation!</p>
                        </div>
                    </div>
                    <div class="chat-input-area">
                        <input type="text" class="chat-input" id="chatInput" placeholder="Type a message...">
                        <button class="chat-send" id="chatSend">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>

                <!-- Participants Panel -->
                <div class="sidebar-panel" id="participants-panel">
                    <div class="participants-list" id="participantsList">
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>No participants yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="controls">
        <button class="control-btn" id="micBtn">
            <i class="fas fa-microphone"></i>
            <span>Mic</span>
        </button>
        <button class="control-btn" id="cameraBtn">
            <i class="fas fa-video"></i>
            <span>Camera</span>
        </button>
        <button class="control-btn" id="settingsBtn">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </button>
        <button class="control-btn danger" id="leaveBtn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Leave</span>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Room functionality for public users
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabs = document.querySelectorAll('.sidebar-tab');
    const panels = document.querySelectorAll('.sidebar-panel');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(targetTab + '-panel').classList.add('active');
        });
    });

    // Chat functionality
    const chatInput = document.getElementById('chatInput');
    const chatSend = document.getElementById('chatSend');
    const chatMessages = document.getElementById('chatMessages');
    
    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;
        
        // Remove empty state if exists
        const emptyState = chatMessages.querySelector('.empty-state');
        if (emptyState) emptyState.remove();
        
        // Add message
        const messageEl = document.createElement('div');
        messageEl.className = 'chat-message';
        messageEl.innerHTML = `
            <div class="chat-author">You</div>
            <div class="chat-text">${message}</div>
        `;
        chatMessages.appendChild(messageEl);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        chatInput.value = '';
        
        // Here you would send the message to the server
        // For now, it's just added locally
    }
    
    chatSend.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });

    // Timer
    let startTime = Date.now();
    setInterval(function() {
        const elapsed = Date.now() - startTime;
        const hours = Math.floor(elapsed / 3600000);
        const minutes = Math.floor((elapsed % 3600000) / 60000);
        const seconds = Math.floor((elapsed % 60000) / 1000);
        
        document.getElementById('timer').textContent = 
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');
    }, 1000);

    // Control buttons
    let micEnabled = false;
    let cameraEnabled = false;
    
    document.getElementById('micBtn').addEventListener('click', function() {
        micEnabled = !micEnabled;
        this.classList.toggle('active', micEnabled);
        this.querySelector('i').className = micEnabled ? 'fas fa-microphone' : 'fas fa-microphone-slash';
    });
    
    document.getElementById('cameraBtn').addEventListener('click', function() {
        cameraEnabled = !cameraEnabled;
        this.classList.toggle('active', cameraEnabled);
        this.querySelector('i').className = cameraEnabled ? 'fas fa-video' : 'fas fa-video-slash';
    });
    
    document.getElementById('settingsBtn').addEventListener('click', function() {
        alert('Settings coming soon!');
    });
    
    document.getElementById('leaveBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to leave the live class?')) {
            window.location.href = '/';
        }
    });

    // Simulate receiving messages (for demo)
    setTimeout(function() {
        const emptyState = chatMessages.querySelector('.empty-state');
        if (emptyState) emptyState.remove();
        
        const messageEl = document.createElement('div');
        messageEl.className = 'chat-message';
        messageEl.innerHTML = `
            <div class="chat-author">Instructor</div>
            <div class="chat-text">Welcome to the live class! Feel free to ask questions.</div>
        `;
        chatMessages.appendChild(messageEl);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }, 3000);
});
</script>
@endpush
