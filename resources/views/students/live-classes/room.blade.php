@extends('students.app')

@section('title', 'Live Class Room - {{ $liveClass->title }}')

@push('styles')
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

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0 }

    html, body { overflow: hidden !important }

    /* ROOT fills whatever space the student layout leaves */
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

    /* Stage: video area + controls */
    .lc-stage {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    /* Video area */
    .lc-video-area {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #000;
        position: relative;
        min-height: 0;
    }

    .lc-video {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* Control bar */
    .lc-controls {
        height: var(--ctrl-h);
        background: var(--panel);
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        padding: 0 1.5rem;
        gap: 1rem;
    }

    /* Sidebar */
    .lc-sidebar {
        width: var(--sidebar-w);
        background: var(--panel);
        border-left: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .lc-sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
    }

    .lc-sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
    }

    /* Buttons */
    .lc-btn {
        background: var(--card);
        border: 1px solid var(--border);
        color: var(--text);
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        cursor: pointer;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .lc-btn:hover {
        background: var(--border-hi);
        border-color: var(--blue);
    }

    .lc-btn.primary {
        background: var(--blue);
        border-color: var(--blue);
        color: white;
    }

    .lc-btn.primary:hover {
        background: #4a8bc4;
    }

    .lc-btn.danger {
        background: var(--red);
        border-color: var(--red);
        color: white;
    }

    /* Live indicator */
    .lc-live-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        background: var(--live-red);
        color: white;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .lc-live-dot {
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1 }
        50% { opacity: 0.5 }
    }

    /* Class info */
    .lc-class-info h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text);
    }

    .lc-class-info p {
        color: var(--text-2);
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    /* Participants */
    .lc-participants {
        margin-top: 1.5rem;
    }

    .lc-participants h4 {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--text);
    }

    .lc-participant-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .lc-participant {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--card);
        border-radius: 0.5rem;
        border: 1px solid var(--border);
    }

    .lc-participant-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--blue);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .lc-participant-info {
        flex: 1;
    }

    .lc-participant-name {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text);
    }

    .lc-participant-role {
        font-size: 0.75rem;
        color: var(--text-2);
    }

    /* Chat */
    .lc-chat {
        margin-top: 1.5rem;
        display: flex;
        flex-direction: column;
        height: 300px;
    }

    .lc-chat h4 {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--text);
    }

    .lc-chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .lc-chat-message {
        margin-bottom: 0.75rem;
    }

    .lc-chat-message:last-child {
        margin-bottom: 0;
    }

    .lc-chat-author {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--blue);
        margin-bottom: 0.25rem;
    }

    .lc-chat-text {
        font-size: 0.875rem;
        color: var(--text);
        line-height: 1.4;
    }

    .lc-chat-time {
        font-size: 0.75rem;
        color: var(--text-2);
        margin-top: 0.25rem;
    }

    .lc-chat-input-group {
        display: flex;
        gap: 0.5rem;
    }

    .lc-chat-input {
        flex: 1;
        background: var(--card);
        border: 1px solid var(--border);
        color: var(--text);
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .lc-chat-input:focus {
        outline: none;
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(91, 164, 212, 0.1);
    }

    .lc-chat-send {
        background: var(--blue);
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        cursor: pointer;
        font-size: 0.875rem;
        transition: background 0.2s;
    }

    .lc-chat-send:hover {
        background: #4a8bc4;
    }

    /* Loading state */
    .lc-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 200px;
        color: var(--text-2);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .lc-sidebar {
            width: 280px;
        }
        
        .lc-controls {
            padding: 0 1rem;
            gap: 0.5rem;
        }
        
        .lc-btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.8rem;
        }
    }
</style>
@endpush

@section('content')
<div class="lc-root">
    <!-- Header -->
    <div style="padding: 1rem 1.5rem; background: var(--panel); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div class="lc-live-indicator">
                <div class="lc-live-dot"></div>
                LIVE
            </div>
            <div>
                <h1 style="font-size: 1.25rem; font-weight: 600; color: var(--text); margin-bottom: 0.25rem;">
                    {{ $liveClass->title }}
                </h1>
                <p style="color: var(--text-2); font-size: 0.875rem;">
                    {{ $liveClass->courses->pluck('course_name')->implode(', ') }} • {{ $liveClass->teacher->name ?? 'Instructor' }}
                </p>
            </div>
        </div>
        <button class="lc-btn danger" onclick="leaveClass()">
            <i class="fas fa-sign-out-alt"></i> Leave Class
        </button>
    </div>

    <!-- Body -->
    <div class="lc-body">
        <!-- Stage -->
        <div class="lc-stage">
            <!-- Video Area -->
            <div class="lc-video-area">
                @if($liveClass->meet_link)
                    <iframe 
                        src="{{ $liveClass->meet_link }}" 
                        class="lc-video"
                        frameborder="0"
                        allow="camera; microphone; fullscreen; display-capture"
                        allowfullscreen>
                    </iframe>
                @else
                    <div class="lc-loading">
                        <div style="text-align: center;">
                            <i class="fas fa-video" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                            <p>Waiting for instructor to start the stream...</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Controls -->
            <div class="lc-controls">
                <button class="lc-btn" onclick="toggleAudio()">
                    <i class="fas fa-microphone" id="audioIcon"></i> Audio
                </button>
                <button class="lc-btn" onclick="toggleVideo()">
                    <i class="fas fa-video" id="videoIcon"></i> Video
                </button>
                <button class="lc-btn" onclick="toggleFullscreen()">
                    <i class="fas fa-expand"></i> Fullscreen
                </button>
                <div style="flex: 1;"></div>
                <div style="color: var(--text-2); font-size: 0.875rem;">
                    <i class="fas fa-users"></i> <span id="participantCount">1</span> participants
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lc-sidebar">
            <div class="lc-sidebar-header">
                <div class="lc-class-info">
                    <h3>{{ $liveClass->title }}</h3>
                    <p><i class="fas fa-book"></i> {{ $liveClass->courses->pluck('course_name')->implode(', ') }}</p>
                    <p><i class="fas fa-user"></i> {{ $liveClass->teacher->name ?? 'Instructor' }}</p>
                    <p><i class="fas fa-clock"></i> Started at {{ $liveClass->start_time->format('h:i A') }}</p>
                </div>
            </div>

            <div class="lc-sidebar-content">
                <!-- Participants -->
                <div class="lc-participants">
                    <h4>Participants</h4>
                    <div class="lc-participant-list" id="participantsList">
                        <div class="lc-participant">
                            <div class="lc-participant-avatar">
                                {{ substr(auth()->guard('student')->user()->full_name, 0, 1) }}
                            </div>
                            <div class="lc-participant-info">
                                <div class="lc-participant-name">{{ auth()->guard('student')->user()->full_name }}</div>
                                <div class="lc-participant-role">Student</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat -->
                <div class="lc-chat">
                    <h4>Class Chat</h4>
                    <div class="lc-chat-messages" id="chatMessages">
                        <div class="lc-chat-message">
                            <div class="lc-chat-author">System</div>
                            <div class="lc-chat-text">Welcome to the live class! You can ask questions here.</div>
                            <div class="lc-chat-time">{{ now()->format('h:i A') }}</div>
                        </div>
                    </div>
                    <div class="lc-chat-input-group">
                        <input 
                            type="text" 
                            class="lc-chat-input" 
                            id="chatInput" 
                            placeholder="Type your message..."
                            onkeypress="if(event.key === 'Enter') sendMessage()">
                        <button class="lc-chat-send" onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Live class functionality
let audioEnabled = true;
let videoEnabled = true;
let participantCount = 1;

// WebRTC configuration
const roomId = '{{ $liveClass->id }}';
const signalingUrl = '/live-signaling/{{ $liveClass->id }}';
const chatUrl = '/live-signaling/{{ $liveClass->id }}/poll';
const participantsUrl = '/live-signaling/{{ $liveClass->id }}/participants';

// Toggle audio
function toggleAudio() {
    audioEnabled = !audioEnabled;
    const icon = document.getElementById('audioIcon');
    icon.className = audioEnabled ? 'fas fa-microphone' : 'fas fa-microphone-slash';
    
    // Send signal to other participants
    postSignal({ type: 'audio', enabled: audioEnabled });
}

// Toggle video
function toggleVideo() {
    videoEnabled = !videoEnabled;
    const icon = document.getElementById('videoIcon');
    icon.className = videoEnabled ? 'fas fa-video' : 'fas fa-video-slash';
    
    // Send signal to other participants
    postSignal({ type: 'video', enabled: videoEnabled });
}

// Toggle fullscreen
function toggleFullscreen() {
    const videoArea = document.querySelector('.lc-video-area');
    if (!document.fullscreenElement) {
        videoArea.requestFullscreen().catch(err => {
            console.log('Error attempting to enable fullscreen:', err);
        });
    } else {
        document.exitFullscreen();
    }
}

// Leave class
function leaveClass() {
    if (confirm('Are you sure you want to leave this live class?')) {
        // Mark as left in database before leaving WebRTC room
        fetch(`/student/live-classes/{{ $liveClass->id }}/leave`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Successfully marked as left:', data);
        })
        .catch(err => {
            console.error('Error marking as left:', err);
        })
        .finally(() => {
            // Leave WebRTC room
            fetch(`/live-signaling/{{ $liveClass->id }}/leave`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).finally(() => {
                // Redirect back to student dashboard
                window.location.href = '{{ route('student.live-classes.index') }}';
            });
        });
    }
}

// Send chat message
function sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Add message to local chat immediately
    addChatMessage('{{ auth()->guard('student')->user()->full_name }}', message);
    
    // Send message to server
    fetch(chatUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            message: message,
            user: '{{ auth()->guard('student')->user()->full_name }}'
        })
    });
    
    input.value = '';
}

// Add chat message to UI
function addChatMessage(author, message, time = null) {
    const messagesContainer = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = 'lc-chat-message';
    
    const timeStr = time || new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    messageDiv.innerHTML = `
        <div class="lc-chat-author">${author}</div>
        <div class="lc-chat-text">${message}</div>
        <div class="lc-chat-time">${timeStr}</div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Send WebRTC signal
function postSignal(data) {
    fetch(signalingUrl + '/send', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            from_peer: '{{ auth()->guard('student')->user()->full_name }}',
            to_peer: 'teacher',
            type: data.type,
            payload: {
                sdp: data.sdp,
                candidate: data.candidate,
                role: data.role,
                sessionId: data.sessionId
            }
        })
    }).catch(console.warn);
}

// Poll for new chat messages
async function pollChat() {
    try {
        const r = await fetch(chatUrl + '?peer={{ auth()->guard('student')->user()->full_name }}&since=0', {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        if (!r.ok) return;
        const response = await r.json();
        const signals = response.signals || [];
        
        // Filter for chat signals only
        const chatSignals = signals.filter(sig => sig.type === 'chat');
        
        for (const sig of chatSignals) {
            if (sig.from_peer === '{{ auth()->guard('student')->user()->full_name }}') { continue; }
            addChatMessage(sig.payload?.sender, sig.payload?.message, false, sig.from_peer === 'teacher');
        }
    } catch(e) { /* silent */ }
}

// Poll for participants
function pollParticipants() {
    fetch(participantsUrl, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(participants => {
        participantCount = participants.length;
        document.getElementById('participantCount').textContent = participantCount;
        
        // Update participants list
        const participantsList = document.getElementById('participantsList');
        participantsList.innerHTML = '';
        
        participants.forEach(participant => {
            const participantDiv = document.createElement('div');
            participantDiv.className = 'lc-participant';
            
            const initials = participant.name ? participant.name.substring(0, 1).toUpperCase() : '?';
            
            participantDiv.innerHTML = `
                <div class="lc-participant-avatar">${initials}</div>
                <div class="lc-participant-info">
                    <div class="lc-participant-name">${participant.name || 'Anonymous'}</div>
                    <div class="lc-participant-role">${participant.role || 'Participant'}</div>
                </div>
            `;
            
            participantsList.appendChild(participantDiv);
        });
    })
    .catch(err => console.error('Error polling participants:', err));
}

// Join WebRTC room
function joinRoom() {
    // Start polling immediately instead of separate join endpoint
    fetch(chatUrl + '?peer={{ auth()->guard('student')->user()->full_name }}&since=0', {
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Joined room successfully:', data);
        
        // Send viewer_ready signal to host
        fetch(signalingUrl + '/send', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                from_peer: '{{ auth()->guard('student')->user()->full_name }}',
                to_peer: 'teacher',
                type: 'viewer_ready',
                payload: { name: '{{ auth()->guard('student')->user()->full_name }}' }
            })
        }).catch(console.warn);
    })
    .catch(err => console.error('Error joining room:', err));
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Join WebRTC room
    joinRoom();
    
    // Start polling for chat messages and participants
    setInterval(pollChat, 3000); // Poll every 3 seconds
    setInterval(pollParticipants, 5000); // Poll every 5 seconds
    
    // Initial poll
    pollChat();
    pollParticipants();
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    // Send synchronous request to mark as left when leaving page
    const xhr = new XMLHttpRequest();
    xhr.open('POST', `/student/live-classes/{{ $liveClass->id }}/leave`, false);
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send();
    
    // Leave WebRTC room
    const webrtcXhr = new XMLHttpRequest();
    webrtcXhr.open('POST', `{{ route('webrtc.leave', $liveClass->id) }}`, false);
    webrtcXhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    webrtcXhr.setRequestHeader('Content-Type', 'application/json');
    webrtcXhr.send();
});

// Also handle visibility change (when tab is hidden/shown)
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'hidden') {
        // Mark as left when tab becomes hidden (optional - might be too aggressive)
        // Uncomment if you want to mark as left when switching tabs
        /*
        fetch(`/student/live-classes/{{ $liveClass->id }}/leave`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            keepalive: true
        }).catch(err => console.log('Error marking as left on visibility change:', err));
        */
    }
});
</script>
@endsection
