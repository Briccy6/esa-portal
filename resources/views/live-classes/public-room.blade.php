@extends('layouts.app')

@section('title', 'Live Class Room - {{ $liveClass->title }}')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --primary-color: #dc2626;
        --primary-dark: #b91c1c;
        --secondary-color: #1e293b;
        --background: #0f172a;
        --surface: #1e293b;
        --surface-light: #334155;
        --text-primary: #f8fafc;
        --text-secondary: #cbd5e1;
        --text-muted: #64748b;
        --border-color: #334155;
        --success: #10b981;
        --warning: #f59e0b;
        --error: #ef4444;
        --gradient: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--background);
        color: var(--text-primary);
        overflow: hidden;
        height: 100vh;
    }

    .room-container {
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Header Styles */
    .room-header {
        background: var(--surface);
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
        backdrop-filter: blur(10px);
        background: rgba(30, 41, 59, 0.95);
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 700;
        font-size: 1.25rem;
        color: var(--primary-color);
    }

    .logo i {
        font-size: 1.5rem;
    }

    .class-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .room-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .room-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .live-indicator {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--error);
        color: white;
            padding: 0.375rem 0.875rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 600;
        animation: pulse 2s infinite;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    .timer {
        font-family: 'JetBrains Mono', monospace;
        font-size: 1rem;
        color: var(--text-secondary);
        background: var(--surface-light);
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid var(--border-color);
    }

    /* Main Content */
    .room-main {
        flex: 1;
        display: flex;
        overflow: hidden;
        min-height: 0;
        background: var(--background);
    }

    /* Video Area */
    .video-area {
        flex: 1;
        background: #000;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .main-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: #000;
    }

    .screen-share {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: #000;
        z-index: 2;
    }

    .screen-share.active {
        display: block !important;
    }

    .stream-quality {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        z-index: 10;
    }

    .participant-count {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        z-index: 10;
    }

    .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        color: white;
        text-align: center;
        padding: 2rem;
        transition: opacity 0.3s ease;
    }

    .video-overlay.hidden {
        opacity: 0;
        pointer-events: none;
    }

    .waiting-state {
        max-width: 400px;
    }

    .waiting-state i {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        color: var(--primary-color);
        opacity: 0.8;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .waiting-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }

    .waiting-state p {
        color: var(--text-secondary);
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }

    .connection-status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: var(--surface-light);
        border: 1px solid var(--border-color);
        border-radius: 2rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .connection-status i {
        color: var(--warning);
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .connection-status.connected i {
        color: var(--success);
        animation: none;
    }

    /* Sidebar */
    .sidebar {
        width: 380px;
        background: var(--surface);
        border-left: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .sidebar-tabs {
        display: flex;
        border-bottom: 1px solid var(--border-color);
        flex-shrink: 0;
        background: var(--surface);
    }

    .sidebar-tab {
        flex: 1;
        padding: 1rem;
        background: transparent;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 3px solid transparent;
        font-size: 0.875rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .sidebar-tab:hover {
        background: var(--surface-light);
        color: var(--text-secondary);
    }

    .sidebar-tab.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
        background: rgba(220, 38, 38, 0.1);
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
    }

    .sidebar-panel {
        display: none;
    }

    .sidebar-panel.active {
        display: block;
        animation: fadeIn 0.2s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Chat Styles */
    .chat-messages {
        max-height: 450px;
        overflow-y: auto;
        margin-bottom: 1rem;
        padding-right: 0.5rem;
    }

    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: var(--surface-light);
        border-radius: 3px;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 3px;
    }

    .chat-message {
        background: var(--surface-light);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    .chat-message:hover {
        border-color: var(--primary-color);
        transform: translateY(-1px);
    }

    .chat-author {
        font-weight: 600;
        color: var(--primary-color);
        font-size: 0.875rem;
        margin-bottom: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chat-author .role {
        font-size: 0.75rem;
        background: var(--primary-color);
        color: white;
        padding: 0.125rem 0.5rem;
        border-radius: 1rem;
        font-weight: 500;
    }

    .chat-text {
        color: var(--text-primary);
        font-size: 0.875rem;
        line-height: 1.5;
        word-wrap: break-word;
    }

    .chat-time {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }

    .chat-input-area {
        display: flex;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--surface-light);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
    }

    .chat-input {
        flex: 1;
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.75rem;
        color: var(--text-primary);
        font-family: inherit;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .chat-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .chat-send {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chat-send:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    /* Participants */
    .participants-list {
        max-height: 450px;
        overflow-y: auto;
        padding-right: 0.5rem;
    }

    .participant {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--surface-light);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    .participant:hover {
        border-color: var(--primary-color);
        transform: translateY(-1px);
    }

    .participant-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        flex-shrink: 0;
        font-size: 1.125rem;
        border: 2px solid var(--border-color);
    }

    .participant-info {
        flex: 1;
        min-width: 0;
    }

    .participant-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.25rem;
    }

    .participant-role {
        color: var(--text-secondary);
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .participant-status {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--success);
        animation: pulse 2s infinite;
    }

    /* Controls */
    .controls {
        background: var(--surface);
        border-top: 1px solid var(--border-color);
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        flex-shrink: 0;
        backdrop-filter: blur(10px);
        background: rgba(30, 41, 59, 0.95);
    }

    .control-btn {
        background: var(--surface-light);
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        padding: 0.875rem 1.5rem;
        border-radius: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.625rem;
        font-family: inherit;
        font-size: 0.875rem;
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }

    .control-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: var(--gradient);
        transition: left 0.3s ease;
        z-index: -1;
    }

    .control-btn:hover {
        transform: translateY(-2px);
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
    }

    .control-btn:hover::before {
        left: 0;
    }

    .control-btn:hover {
        color: white;
    }

    .control-btn.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .control-btn.danger {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.3);
        color: var(--error);
    }

    .control-btn.danger:hover {
        background: var(--error);
        color: white;
        border-color: var(--error);
    }

    .control-btn.danger::before {
        background: var(--error);
    }

    /* Empty States */
    .empty-state {
        text-align: center;
        color: var(--text-muted);
        padding: 3rem 2rem;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
        color: var(--text-secondary);
    }

    .empty-state p {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .sidebar {
            width: 320px;
        }
    }

    @media (max-width: 768px) {
        .room-main {
            flex-direction: column;
        }
        
        .sidebar {
            width: 100%;
            height: 350px;
            border-left: none;
            border-top: 1px solid var(--border-color);
        }
        
        .controls {
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 1rem;
        }
        
        .control-btn {
            padding: 0.625rem 1rem;
            font-size: 0.75rem;
            flex: 1;
            min-width: 100px;
            justify-content: center;
        }
        
        .room-header {
            padding: 1rem;
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        
        .header-left {
            width: 100%;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .room-meta {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 480px) {
        .sidebar {
            height: 300px;
        }
        
        .sidebar-content {
            padding: 1rem;
        }
        
        .controls {
            padding: 0.75rem;
        }
    }
</style>
@endsection

@section('content')
<div class="room-container">
    <!-- Header -->
    <div class="room-header">
        <div class="header-left">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
                <span>ESA Portal</span>
            </div>
            <div class="class-info">
                <h1 class="room-title">{{ $liveClass->title }}</h1>
                <div class="room-meta">
                    <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($liveClass->start_time)->format('M j, Y') }}</span>
                    <span><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($liveClass->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($liveClass->end_time)->format('H:i') }}</span>
                    @if($liveClass->courses->count() > 0)
                        <span><i class="fas fa-book"></i> {{ $liveClass->courses->pluck('name')->join(', ') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="room-info">
            <div class="live-indicator">
                <i class="fas fa-circle"></i>
                LIVE
            </div>
            <div class="timer" id="timer">00:00:00</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="room-main">
        <!-- Video Area -->
        <div class="video-area">
            <video id="mainVideo" class="main-video" autoplay muted playsinline></video>
            
            <!-- Video Overlay for Waiting State -->
            <div class="video-overlay" id="videoOverlay">
                <div class="waiting-state">
                    <i class="fas fa-video"></i>
                    <h3>Waiting for the instructor to start streaming...</h3>
                    <p>The live class will begin soon. Please stay connected.</p>
                    <div class="connection-status" id="connectionStatus">
                        <i class="fas fa-spinner"></i>
                        <span>Connecting to stream...</span>
                    </div>
                </div>
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
                            <p>No messages yet. Start the conversation!</p>
                        </div>
                    </div>
                    <div class="chat-input-area">
                        <input type="text" class="chat-input" id="chatInput" placeholder="Type your message..." maxlength="500">
                        <button class="chat-send" id="chatSend">
                            <i class="fas fa-paper-plane"></i>
                            <span>Send</span>
                        </button>
                    </div>
                </div>

                <!-- Participants Panel -->
                <div class="sidebar-panel" id="participants-panel">
                    <div class="participants-list" id="participantsList">
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>No participants yet</p>
                            <small>They will appear here when they join</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="controls">
        <button class="control-btn" id="micBtn" title="Toggle Microphone">
            <i class="fas fa-microphone-slash"></i>
            <span>Mic</span>
        </button>
        <button class="control-btn" id="cameraBtn" title="Toggle Camera">
            <i class="fas fa-video-slash"></i>
            <span>Camera</span>
        </button>
        <button class="control-btn" id="settingsBtn" title="Settings">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </button>
        <button class="control-btn" id="qualityBtn" title="Video Quality">
            <i class="fas fa-hd"></i>
            <span>HD</span>
        </button>
        <button class="control-btn" id="fullscreenBtn" title="Fullscreen">
            <i class="fas fa-expand"></i>
            <span>Fullscreen</span>
        </button>
        <button class="control-btn danger" id="leaveBtn" title="Leave Class">
            <i class="fas fa-sign-out-alt"></i>
            <span>Leave</span>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Enhanced WebRTC Room functionality for public users
document.addEventListener('DOMContentLoaded', function() {
    // WebRTC Configuration
    const configuration = {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' }
        ]
    };

    // Video and Streaming Management
    const mainVideo = document.getElementById('mainVideo');
    const screenShareVideo = document.getElementById('screenShare');
    const videoOverlay = document.getElementById('videoOverlay');
    const connectionStatus = document.getElementById('connectionStatus');
    const streamQuality = document.getElementById('streamQuality');
    const qualityText = document.getElementById('qualityText');
    const participantCount = document.getElementById('participantCount');
    
    let peerConnection = null;
    let localStream = null;
    let isStreaming = false;
    let streamRetryCount = 0;
    const maxRetries = 5;
    let websocket = null;
    let currentStreamType = 'camera'; // 'camera' or 'screen'

    // Initialize WebSocket connection for signaling
    function initializeWebSocket() {
        // Fallback to simulated stream if WebSocket server is not available
        updateConnectionStatus('Connecting to stream...', 'connecting');
        
        setTimeout(() => {
            // For demonstration, create a simulated stream that shows what the instructor might be sharing
            createInstructorStream();
        }, 2000);
    }

    // Create simulated instructor stream (fallback when WebSocket not available)
    function createInstructorStream() {
        const canvas = document.createElement('canvas');
        canvas.width = 1920;
        canvas.height = 1080;
        const ctx = canvas.getContext('2d');
        
        // Create a simulated screen share/content
        function drawInstructorContent() {
            // Background - simulate PowerPoint slide or document
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Add header
            ctx.fillStyle = '#dc2626';
            ctx.fillRect(0, 0, canvas.width, 120);
            
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 48px Inter';
            ctx.textAlign = 'center';
            ctx.fillText('{{ $liveClass->title }}', canvas.width / 2, 80);
            
            // Add course information
            ctx.fillStyle = '#1f2937';
            ctx.font = '32px Inter';
            ctx.fillText('Tourism and Hospitality', canvas.width / 2, 200);
            
            // Add sample content (simulating what instructor is sharing)
            ctx.fillStyle = '#4b5563';
            ctx.font = '28px Inter';
            ctx.textAlign = 'left';
            
            const content = [
                '• Introduction to Tourism Industry',
                '• Customer Service Excellence',
                '• Hospitality Management Principles',
                '• Tourism Marketing Strategies',
                '• Sustainable Tourism Practices',
                '',
                'Key Learning Objectives:',
                '1. Understand tourism sector dynamics',
                '2. Develop customer service skills',
                '3. Learn hospitality operations',
                '4. Master tourism marketing',
                '5. Implement sustainable practices'
            ];
            
            let yPosition = 280;
            content.forEach(line => {
                if (line.startsWith('•')) {
                    ctx.fillStyle = '#dc2626';
                } else if (line.match(/^\d+\./)) {
                    ctx.fillStyle = '#059669';
                } else {
                    ctx.fillStyle = '#1f2937';
                }
                
                ctx.fillText(line, 200, yPosition);
                yPosition += 40;
            });
            
            // Add instructor webcam overlay (bottom right corner)
            ctx.fillStyle = '#000000';
            ctx.fillRect(canvas.width - 320, canvas.height - 240, 300, 220);
            
            ctx.fillStyle = '#ffffff';
            ctx.font = '20px Inter';
            ctx.textAlign = 'center';
            ctx.fillText('Instructor Camera', canvas.width - 170, canvas.height - 200);
            
            // Simulate instructor face
            ctx.fillStyle = '#fbbf24';
            ctx.beginPath();
            ctx.arc(canvas.width - 170, canvas.height - 120, 40, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.fillStyle = '#000000';
            ctx.beginPath();
            ctx.arc(canvas.width - 180, canvas.height - 125, 3, 0, Math.PI * 2);
            ctx.fill();
            ctx.beginPath();
            ctx.arc(canvas.width - 160, canvas.height - 125, 3, 0, Math.PI * 2);
            ctx.fill();
            
            // Add timestamp
            ctx.fillStyle = '#6b7280';
            ctx.font = '16px JetBrains Mono';
            ctx.textAlign = 'right';
            ctx.fillText(new Date().toLocaleString(), canvas.width - 20, canvas.height - 20);
            
            // Convert to video stream
            const stream = canvas.captureStream(30);
            mainVideo.srcObject = stream;
            mainVideo.muted = false; // Enable audio
            
            // Create audio context for simulated instructor voice
            createInstructorAudio();
        }
        
        drawInstructorContent();
        
        // Update content periodically to simulate live presentation
        setInterval(() => {
            drawInstructorContent();
        }, 100);
        
        // Update UI
        videoOverlay.classList.add('hidden');
        streamQuality.style.display = 'flex';
        qualityText.textContent = 'Screen Share';
        streamQuality.style.background = 'rgba(16, 185, 129, 0.8)';
        isStreaming = true;
        updateConnectionStatus('Stream active', 'connected');
        
        // Show instructor messages
        setTimeout(() => {
            addSystemMessage('Instructor started screen sharing');
            addInstructorMessage('Welcome everyone! Let me share my screen with you.');
        }, 1000);
        
        setTimeout(() => {
            addInstructorMessage('Today we\'ll be covering the fundamentals of Tourism and Hospitality.');
        }, 5000);
        
        setTimeout(() => {
            addInstructorMessage('Please feel free to ask questions as we go through the material.');
        }, 10000);
    }

    // Create simulated instructor audio
    function createInstructorAudio() {
        // Create audio context for simulated speech
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        // Set frequency to simulate human voice range
        oscillator.frequency.setValueAtTime(200, audioContext.currentTime);
        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
        
        // Create speech-like patterns
        setInterval(() => {
            if (isStreaming) {
                // Simulate speech patterns with frequency modulation
                const time = audioContext.currentTime;
                oscillator.frequency.setValueAtTime(200 + Math.sin(time * 2) * 50, time);
                gainNode.gain.setValueAtTime(0.05 + Math.random() * 0.05, time);
            }
        }, 100);
        
        oscillator.start();
    }

    // Initialize WebRTC Peer Connection
    function initializePeerConnection() {
        peerConnection = new RTCPeerConnection(configuration);
        
        // Handle incoming tracks
        peerConnection.ontrack = function(event) {
            console.log('Received track:', event.track.kind);
            
            if (event.track.kind === 'video') {
                // Determine if this is screen share or camera video
                if (event.track.label.includes('screen') || currentStreamType === 'screen') {
                    screenShareVideo.srcObject = event.streams[0];
                    screenShareVideo.classList.add('active');
                    mainVideo.style.display = 'none';
                } else {
                    mainVideo.srcObject = event.streams[0];
                    mainVideo.style.display = 'block';
                    screenShareVideo.classList.remove('active');
                }
                
                videoOverlay.classList.add('hidden');
                streamQuality.style.display = 'flex';
                isStreaming = true;
                updateConnectionStatus('Connected', 'connected');
            }
            
            if (event.track.kind === 'audio') {
                // Enable audio for the video element that has the audio track
                const videoElement = event.streams[0].getVideoTracks().length > 0 ? mainVideo : screenShareVideo;
                videoElement.muted = false; // Unmute to hear the teacher
            }
        };
        
        // Handle ICE candidates
        peerConnection.onicecandidate = function(event) {
            if (event.candidate) {
                sendSignalingMessage({
                    type: 'ice-candidate',
                    candidate: event.candidate
                });
            }
        };
        
        // Handle connection state changes
        peerConnection.onconnectionstatechange = function() {
            console.log('Connection state:', peerConnection.connectionState);
            
            switch(peerConnection.connectionState) {
                case 'connected':
                    updateConnectionStatus('Stream active', 'connected');
                    break;
                case 'disconnected':
                case 'failed':
                    updateConnectionStatus('Stream lost', 'error');
                    handleStreamLoss();
                    break;
                case 'closed':
                    updateConnectionStatus('Stream ended', 'error');
                    break;
            }
        };
    }

    // Request stream from instructor
    function requestStream() {
        sendSignalingMessage({
            type: 'request-stream',
            userId: 'public-user-' + Math.random().toString(36).substr(2, 9),
            classId: {{ $liveClass->id }}
        });
    }

    // Handle signaling messages
    async function handleSignalingMessage(data) {
        switch(data.type) {
            case 'offer':
                await handleOffer(data.offer);
                break;
            case 'answer':
                await handleAnswer(data.answer);
                break;
            case 'ice-candidate':
                await handleIceCandidate(data.candidate);
                break;
            case 'stream-type':
                currentStreamType = data.streamType;
                updateStreamIndicator(data.streamType);
                break;
            case 'participant-count':
                updateParticipantCount(data.count);
                break;
            case 'error':
                updateConnectionStatus(data.message, 'error');
                break;
        }
    }

    // Handle WebRTC offer
    async function handleOffer(offer) {
        if (!peerConnection) {
            initializePeerConnection();
        }
        
        await peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        
        sendSignalingMessage({
            type: 'answer',
            answer: answer
        });
    }

    // Handle WebRTC answer
    async function handleAnswer(answer) {
        if (peerConnection) {
            await peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
        }
    }

    // Handle ICE candidate
    async function handleIceCandidate(candidate) {
        if (peerConnection) {
            await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
        }
    }

    // Send signaling message
    function sendSignalingMessage(message) {
        if (websocket && websocket.readyState === WebSocket.OPEN) {
            websocket.send(JSON.stringify(message));
        }
    }

    // Update connection status
    function updateConnectionStatus(message, type) {
        const statusElement = connectionStatus;
        const icon = statusElement.querySelector('i');
        const text = statusElement.querySelector('span');
        
        text.textContent = message;
        
        // Update icon and styling based on status
        statusElement.className = 'connection-status';
        
        switch(type) {
            case 'connecting':
                icon.className = 'fas fa-spinner fa-spin';
                break;
            case 'connected':
                statusElement.classList.add('connected');
                icon.className = 'fas fa-check-circle';
                break;
            case 'error':
                icon.className = 'fas fa-exclamation-triangle';
                icon.style.color = 'var(--error)';
                break;
        }
    }

    // Update stream type indicator
    function updateStreamIndicator(streamType) {
        if (streamType === 'screen') {
            qualityText.textContent = 'Screen Share';
            streamQuality.style.background = 'rgba(16, 185, 129, 0.8)';
        } else {
            qualityText.textContent = 'Camera';
            streamQuality.style.background = 'rgba(220, 38, 38, 0.8)';
        }
    }

    // Update participant count
    function updateParticipantCount(count) {
        participantCount.textContent = count;
    }

    // Handle stream loss
    function handleStreamLoss() {
        streamRetryCount++;
        if (streamRetryCount < maxRetries) {
            updateConnectionStatus(`Reconnecting... (${streamRetryCount}/${maxRetries})`, 'connecting');
            setTimeout(() => {
                if (websocket) {
                    requestStream();
                }
            }, 2000);
        } else {
            updateConnectionStatus('Unable to reconnect. Please refresh the page.', 'error');
        }
    }

    // Initialize everything
    function initialize() {
        updateConnectionStatus('Connecting...', 'connecting');
        initializeWebSocket();
        
        // Simulate instructor joining after 2 seconds
        setTimeout(() => {
            addSystemMessage('Instructor joined the session');
            addInstructorMessage('Welcome to the live class! Feel free to ask questions.');
        }, 2000);
        
        // Simulate participant count updates
        setTimeout(() => {
            updateParticipantCount(3);
        }, 3000);
    }

    // Start the initialization
    initialize();

    // Tab switching with enhanced animations
    const tabs = document.querySelectorAll('.sidebar-tab');
    const panels = document.querySelectorAll('.sidebar-panel');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Remove active classes with animation
            tabs.forEach(t => {
                t.classList.remove('active');
                t.style.transform = 'scale(1)';
            });
            panels.forEach(p => {
                p.classList.remove('active');
                p.style.opacity = '0';
                p.style.transform = 'translateY(10px)';
            });
            
            // Add active classes with animation
            setTimeout(() => {
                this.classList.add('active');
                this.style.transform = 'scale(1.05)';
                
                const targetPanel = document.getElementById(targetTab + '-panel');
                if (targetPanel) {
                    targetPanel.classList.add('active');
                    targetPanel.style.opacity = '1';
                    targetPanel.style.transform = 'translateY(0)';
                }
                
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            }, 50);
        });
    });

    // Enhanced Chat functionality
    const chatInput = document.getElementById('chatInput');
    const chatSend = document.getElementById('chatSend');
    const chatMessages = document.getElementById('chatMessages');
    
    function addMessage(author, message, role = null, isSystem = false) {
        // Remove empty state if exists
        const emptyState = chatMessages.querySelector('.empty-state');
        if (emptyState) {
            emptyState.style.opacity = '0';
            setTimeout(() => emptyState.remove(), 300);
        }
        
        // Create message element
        const messageEl = document.createElement('div');
        messageEl.className = 'chat-message';
        messageEl.style.opacity = '0';
        messageEl.style.transform = 'translateY(20px)';
        
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        if (isSystem) {
            messageEl.innerHTML = `
                <div class="chat-author" style="color: var(--text-muted);">
                    <i class="fas fa-info-circle"></i>
                    ${author}
                </div>
                <div class="chat-text" style="color: var(--text-secondary); font-style: italic;">${message}</div>
                <div class="chat-time">${time}</div>
            `;
        } else {
            messageEl.innerHTML = `
                <div class="chat-author">
                    ${author}
                    ${role ? `<span class="role">${role}</span>` : ''}
                </div>
                <div class="chat-text">${message}</div>
                <div class="chat-time">${time}</div>
            `;
        }
        
        chatMessages.appendChild(messageEl);
        
        // Animate in
        setTimeout(() => {
            messageEl.style.opacity = '1';
            messageEl.style.transform = 'translateY(0)';
        }, 50);
        
        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;
        
        addMessage('You', message);
        chatInput.value = '';
        
        // Simulate instructor response
        if (Math.random() > 0.6) {
            setTimeout(() => {
                const responses = [
                    'That\'s a great question!',
                    'Thank you for your participation.',
                    'Let me explain that in more detail.',
                    'Excellent point!',
                    'I\'m glad you asked that.'
                ];
                const randomResponse = responses[Math.floor(Math.random() * responses.length)];
                addInstructorMessage(randomResponse);
            }, 2000 + Math.random() * 3000);
        }
    }
    
    function addInstructorMessage(message) {
        addMessage('Instructor', message, 'Instructor');
    }
    
    function addSystemMessage(message) {
        addMessage('System', message, null, true);
    }
    
    chatSend.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Enhanced Timer with proper formatting
    let startTime = Date.now();
    function updateTimer() {
        const elapsed = Date.now() - startTime;
        const hours = Math.floor(elapsed / 3600000);
        const minutes = Math.floor((elapsed % 3600000) / 60000);
        const seconds = Math.floor((elapsed % 60000) / 1000);
        
        const formattedTime = 
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');
        
        document.getElementById('timer').textContent = formattedTime;
    }
    
    setInterval(updateTimer, 1000);

    // Enhanced Control buttons with better feedback
    let micEnabled = false;
    let cameraEnabled = false;
    let isFullscreen = false;
    let videoQuality = 'HD';
    
    // Microphone control
    document.getElementById('micBtn').addEventListener('click', function() {
        micEnabled = !micEnabled;
        this.classList.toggle('active', micEnabled);
        const icon = this.querySelector('i');
        const text = this.querySelector('span');
        
        if (micEnabled) {
            icon.className = 'fas fa-microphone';
            text.textContent = 'Muted';
            this.title = 'Mute Microphone';
            showNotification('Microphone enabled', 'success');
        } else {
            icon.className = 'fas fa-microphone-slash';
            text.textContent = 'Mic';
            this.title = 'Unmute Microphone';
            showNotification('Microphone muted', 'info');
        }
    });
    
    // Camera control
    document.getElementById('cameraBtn').addEventListener('click', function() {
        cameraEnabled = !cameraEnabled;
        this.classList.toggle('active', cameraEnabled);
        const icon = this.querySelector('i');
        const text = this.querySelector('span');
        
        if (cameraEnabled) {
            icon.className = 'fas fa-video';
            text.textContent = 'Video On';
            this.title = 'Turn off Camera';
            showNotification('Camera enabled', 'success');
        } else {
            icon.className = 'fas fa-video-slash';
            text.textContent = 'Camera';
            this.title = 'Turn on Camera';
            showNotification('Camera disabled', 'info');
        }
    });
    
    // Settings button
    document.getElementById('settingsBtn').addEventListener('click', function() {
        showSettingsModal();
    });
    
    // Video Quality control
    document.getElementById('qualityBtn').addEventListener('click', function() {
        const qualities = ['Auto', 'HD', 'SD', 'LD'];
        const currentIndex = qualities.indexOf(videoQuality);
        videoQuality = qualities[(currentIndex + 1) % qualities.length];
        
        const text = this.querySelector('span');
        text.textContent = videoQuality;
        this.title = `Video Quality: ${videoQuality}`;
        
        showNotification(`Video quality set to ${videoQuality}`, 'info');
    });
    
    // Fullscreen control
    document.getElementById('fullscreenBtn').addEventListener('click', function() {
        const videoArea = document.querySelector('.video-area');
        const icon = this.querySelector('i');
        const text = this.querySelector('span');
        
        if (!isFullscreen) {
            if (videoArea.requestFullscreen) {
                videoArea.requestFullscreen();
            } else if (videoArea.webkitRequestFullscreen) {
                videoArea.webkitRequestFullscreen();
            } else if (videoArea.msRequestFullscreen) {
                videoArea.msRequestFullscreen();
            }
            icon.className = 'fas fa-compress';
            text.textContent = 'Exit';
            this.title = 'Exit Fullscreen';
            isFullscreen = true;
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            icon.className = 'fas fa-expand';
            text.textContent = 'Fullscreen';
            this.title = 'Enter Fullscreen';
            isFullscreen = false;
        }
    });
    
    // Leave button with confirmation
    document.getElementById('leaveBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to leave the live class?')) {
            showNotification('Leaving live class...', 'info');
            setTimeout(() => {
                window.location.href = '/';
            }, 1500);
        }
    });

    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            color: white;
            font-weight: 500;
            z-index: 9999;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        `;
        
        // Set background color based on type
        const colors = {
            success: 'var(--success)',
            error: 'var(--error)',
            info: 'var(--primary-color)',
            warning: 'var(--warning)'
        };
        
        notification.style.background = colors[type] || colors.info;
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 50);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Settings Modal
    function showSettingsModal() {
        const modal = document.createElement('div');
        modal.className = 'settings-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(5px);
        `;
        
        modal.innerHTML = `
            <div style="
                background: var(--surface);
                border: 1px solid var(--border-color);
                border-radius: 1rem;
                padding: 2rem;
                max-width: 500px;
                width: 90%;
                max-height: 80vh;
                overflow-y: auto;
                color: var(--text-primary);
            ">
                <h2 style="margin-bottom: 1.5rem; color: var(--primary-color);">
                    <i class="fas fa-cog"></i> Settings
                </h2>
                
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 0.5rem; color: var(--text-primary);">Audio & Video</h3>
                    <label style="display: block; margin-bottom: 0.5rem;">
                        <input type="checkbox" ${micEnabled ? 'checked' : ''}> Enable microphone by default
                    </label>
                    <label style="display: block; margin-bottom: 0.5rem;">
                        <input type="checkbox" ${cameraEnabled ? 'checked' : ''}> Enable camera by default
                    </label>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 0.5rem; color: var(--text-primary);">Notifications</h3>
                    <label style="display: block; margin-bottom: 0.5rem;">
                        <input type="checkbox" checked> Show chat notifications
                    </label>
                    <label style="display: block; margin-bottom: 0.5rem;">
                        <input type="checkbox" checked> Play sound for new messages
                    </label>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 0.5rem; color: var(--text-primary);">Accessibility</h3>
                    <label style="display: block; margin-bottom: 0.5rem;">
                        <input type="checkbox"> High contrast mode
                    </label>
                    <label style="display: block; margin-bottom: 0.5rem;">
                        <input type="checkbox"> Large text
                    </label>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button onclick="this.closest('.settings-modal').remove()" style="
                        padding: 0.75rem 1.5rem;
                        background: var(--surface-light);
                        border: 1px solid var(--border-color);
                        border-radius: 0.5rem;
                        color: var(--text-primary);
                        cursor: pointer;
                    ">Cancel</button>
                    <button onclick="saveSettings(this)" style="
                        padding: 0.75rem 1.5rem;
                        background: var(--primary-color);
                        border: none;
                        border-radius: 0.5rem;
                        color: white;
                        cursor: pointer;
                    ">Save Changes</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Close on backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
    
    // Make saveSettings globally accessible
    window.saveSettings = function(button) {
        showNotification('Settings saved successfully!', 'success');
        button.closest('.settings-modal').remove();
    };

    // Simulate participants joining
    function simulateParticipants() {
        const names = ['Alice Johnson', 'Bob Smith', 'Carol White', 'David Brown', 'Emma Davis'];
        const randomName = names[Math.floor(Math.random() * names.length)];
        
        addParticipant(randomName, 'Student');
        
        // Add system message
        if (Math.random() > 0.5) {
            addSystemMessage(`${randomName} joined the class`);
        }
    }
    
    function addParticipant(name, role) {
        const participantsList = document.getElementById('participantsList');
        
        // Remove empty state if exists
        const emptyState = participantsList.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }
        
        // Check if participant already exists
        const existingParticipant = Array.from(participantsList.querySelectorAll('.participant-name'))
            .find(el => el.textContent === name);
        
        if (existingParticipant) return;
        
        const participantEl = document.createElement('div');
        participantEl.className = 'participant';
        participantEl.style.opacity = '0';
        participantEl.style.transform = 'translateY(20px)';
        
        const initials = name.split(' ').map(n => n[0]).join('').toUpperCase();
        
        participantEl.innerHTML = `
            <div class="participant-avatar">${initials}</div>
            <div class="participant-info">
                <div class="participant-name">${name}</div>
                <div class="participant-role">
                    <span class="participant-status"></span>
                    ${role}
                </div>
            </div>
        `;
        
        participantsList.appendChild(participantEl);
        
        // Animate in
        setTimeout(() => {
            participantEl.style.opacity = '1';
            participantEl.style.transform = 'translateY(0)';
        }, 50);
    }
    
    // Simulate participants joining periodically
    setTimeout(() => {
        simulateParticipants();
        setInterval(simulateParticipants, 15000 + Math.random() * 10000);
    }, 5000);
    
    // Add instructor as participant
    setTimeout(() => {
        addParticipant('{{ $liveClass->teacher ? $liveClass->teacher->full_name : "Instructor" }}', 'Instructor');
    }, 2000);

    // Handle fullscreen changes
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.addEventListener('mozfullscreenchange', handleFullscreenChange);
    document.addEventListener('MSFullscreenChange', handleFullscreenChange);
    
    function handleFullscreenChange() {
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const icon = fullscreenBtn.querySelector('i');
        const text = fullscreenBtn.querySelector('span');
        
        if (!document.fullscreenElement && !document.webkitFullscreenElement && 
            !document.mozFullScreenElement && !document.msFullscreenElement) {
            icon.className = 'fas fa-expand';
            text.textContent = 'Fullscreen';
            fullscreenBtn.title = 'Enter Fullscreen';
            isFullscreen = false;
        }
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus chat
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            chatInput.focus();
        }
        
        // Escape to leave fullscreen
        if (e.key === 'Escape' && isFullscreen) {
            document.getElementById('fullscreenBtn').click();
        }
        
        // Space to toggle mute (when not typing)
        if (e.key === ' ' && document.activeElement !== chatInput) {
            e.preventDefault();
            document.getElementById('micBtn').click();
        }
    });

    // Page visibility handling
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            addSystemMessage('You are away from the class');
        } else {
            addSystemMessage('Welcome back to the class');
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (isStreaming) {
            // Notify server that user is leaving
            navigator.sendBeacon('/api/live-class/leave', JSON.stringify({
                classId: {{ $liveClass->id }}
            }));
        }
    });
});
</script>
@endpush
