<x-app-layout>
    <x-slot name="title">
        Live Class Room - {{ $liveClass->title }}
    </x-slot>

    <x-slot name="styles">
    <style>
        .video-container {
            position: relative;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .video-element {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .local-video {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 200px;
            height: 150px;
            border-radius: 8px;
            border: 2px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 10;
        }
        
        .controls-panel {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(10px);
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 20;
        }
        
        .control-btn {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
        }
        
        .control-btn:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .control-btn.active {
            background: #2563eb;
            border-color: #2563eb;
        }
        
        .participants-panel {
            position: fixed;
            right: 20px;
            top: 80px;
            width: 300px;
            max-height: 400px;
            background: rgba(255,255,255,0.95);
            border-radius: 8px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 15;
        }
        
        .chat-panel {
            position: fixed;
            left: 20px;
            top: 80px;
            width: 300px;
            max-height: 400px;
            background: rgba(255,255,255,0.95);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 15;
            display: flex;
            flex-direction: column;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }
        
        .chat-input {
            padding: 10px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 8px;
        }
        
        .chat-input input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            outline: none;
        }
        
        .chat-input button {
            padding: 8px 12px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        
        .status-indicator {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.8);
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 14px;
            z-index: 25;
        }
        
        .status-indicator.live {
            background: #dc2626;
        }
        
        .status-indicator.recording {
            background: #dc2626;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
    </x-slot>

    <div class="container-fluid p-0">
        <!-- Status Indicator -->
        <div class="status-indicator" id="statusIndicator">
            <i class="fas fa-circle text-red-500 mr-2"></i>
            Live Now
        </div>

        <!-- Main Video Container -->
        <div class="row g-0">
            <div class="col-12">
                <div class="video-container" style="height: 70vh;">
                    <video id="mainVideo" class="video-element" autoplay muted></video>
                    <video id="localVideo" class="local-video" autoplay muted></video>
                </div>
            </div>
        </div>

        <!-- Side Panels -->
        <div class="row g-0">
            <!-- Chat Panel -->
            @if($liveClass->chat_enabled)
            <div class="col-md-3">
                <div class="chat-panel">
                    <div class="p-3 border-bottom bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-comments mr-2"></i>
                            Chat
                        </h6>
                    </div>
                    <div class="chat-messages" id="chatMessages">
                        <!-- Chat messages will appear here -->
                    </div>
                    <div class="chat-input">
                        <input type="text" id="chatInput" placeholder="Type a message...">
                        <button onclick="sendMessage()">Send</button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Participants Panel -->
            @if($liveClass->screen_share_enabled)
            <div class="col-md-3">
                <div class="participants-panel">
                    <div class="p-3 border-bottom bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-users mr-2"></i>
                            Participants (<span id="participantCount">1</span>)
                        </h6>
                    </div>
                    <div id="participantsList">
                        <div class="p-2 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="w-8 h-8 bg-blue-500 rounded-full d-flex align-items-center justify-center text-white mr-2">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="font-weight-semibold">You</div>
                                    <div class="text-muted small">Host</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Controls Panel -->
        <div class="controls-panel">
            <div class="d-flex align-items-center gap-3">
                <button class="control-btn" id="micBtn" onclick="toggleMicrophone()">
                    <i class="fas fa-microphone"></i>
                </button>
                <button class="control-btn" id="videoBtn" onclick="toggleCamera()">
                    <i class="fas fa-video"></i>
                </button>
                @if($liveClass->screen_share_enabled)
                <button class="control-btn" id="screenBtn" onclick="toggleScreenShare()">
                    <i class="fas fa-desktop"></i>
                </button>
                @endif
                @if($liveClass->recording_enabled)
                <button class="control-btn" id="recordBtn" onclick="toggleRecording()">
                    <i class="fas fa-record-vinyl"></i>
                </button>
                @endif
            </div>
            
            <div class="text-white">
                <span id="timer">00:00:00</span>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <button class="control-btn bg-danger" onclick="leaveClass()">
                    <i class="fas fa-phone-slash mr-2"></i>
                    Leave
                </button>
            </div>
        </div>
    </div>

    <script>
        let localStream = null;
        let remoteStream = null;
        let peerConnection = null;
        let isRecording = false;
        let mediaRecorder = null;
        let recordedChunks = [];
        let timerInterval = null;
        let startTime = Date.now();

        // Initialize WebRTC
        async function initializeWebRTC() {
            try {
                // Get local media
                localStream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: true
                });
                
                // Display local video
                document.getElementById('localVideo').srcObject = localStream;
                
                // Start timer
                startTimer();
                
                // Initialize signaling (simplified for demo)
                initializeSignaling();
                
            } catch (error) {
                console.error('Error accessing media devices:', error);
                alert('Unable to access camera/microphone. Please check permissions.');
            }
        }

        function initializeSignaling() {
            // This would normally connect to your signaling server
            // For demo purposes, we'll simulate a connection
            console.log('WebRTC initialized for class: {{ $liveClass->id }}');
            
            // Simulate remote connection after 2 seconds
            setTimeout(() => {
                simulateRemoteConnection();
            }, 2000);
        }

        function simulateRemoteConnection() {
            // Create a canvas element to simulate remote video
            const canvas = document.createElement('canvas');
            canvas.width = 640;
            canvas.height = 480;
            const ctx = canvas.getContext('2d');
            
            // Generate some video-like content
            let frame = 0;
            setInterval(() => {
                ctx.fillStyle = '#000';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                // Draw some animated content
                ctx.fillStyle = '#2563eb';
                ctx.beginPath();
                ctx.arc(
                    canvas.width/2 + Math.sin(frame * 0.05) * 100,
                    canvas.height/2 + Math.cos(frame * 0.03) * 50,
                    50,
                    0,
                    Math.PI * 2
                );
                ctx.fill();
                
                frame++;
            }, 1000/30);
            
            // Set as remote video
            const remoteVideo = document.getElementById('mainVideo');
            remoteVideo.srcObject = canvas.captureStream(30);
        }

        function toggleMicrophone() {
            const btn = document.getElementById('micBtn');
            const isEnabled = !btn.classList.contains('active');
            
            if (localStream) {
                localStream.getAudioTracks().forEach(track => {
                    track.enabled = isEnabled;
                });
            }
            
            btn.classList.toggle('active', isEnabled);
            btn.innerHTML = isEnabled ? '<i class="fas fa-microphone"></i>' : '<i class="fas fa-microphone-slash"></i>';
        }

        function toggleCamera() {
            const btn = document.getElementById('videoBtn');
            const isEnabled = !btn.classList.contains('active');
            
            if (localStream) {
                localStream.getVideoTracks().forEach(track => {
                    track.enabled = isEnabled;
                });
            }
            
            btn.classList.toggle('active', isEnabled);
            btn.innerHTML = isEnabled ? '<i class="fas fa-video"></i>' : '<i class="fas fa-video-slash"></i>';
        }

        function toggleScreenShare() {
            const btn = document.getElementById('screenBtn');
            const isSharing = !btn.classList.contains('active');
            
            if (isSharing) {
                navigator.mediaDevices.getDisplayMedia({ video: true })
                    .then(stream => {
                        // Add screen share to peer connection
                        const mainVideo = document.getElementById('mainVideo');
                        mainVideo.srcObject = stream;
                        
                        btn.classList.add('active');
                        btn.innerHTML = '<i class="fas fa-desktop"></i>';
                        
                        // Stop sharing when user ends it
                        stream.getVideoTracks()[0].addEventListener('ended', () => {
                            toggleScreenShare();
                        });
                    })
                    .catch(error => {
                        console.error('Error sharing screen:', error);
                    });
            } else {
                // Restore main video
                btn.classList.remove('active');
                // Revert to original video source
            }
        }

        function toggleRecording() {
            const btn = document.getElementById('recordBtn');
            const statusIndicator = document.getElementById('statusIndicator');
            
            if (!isRecording) {
                startRecording();
                btn.classList.add('active');
                btn.innerHTML = '<i class="fas fa-stop"></i>';
                statusIndicator.classList.add('recording');
                statusIndicator.innerHTML = '<i class="fas fa-circle mr-2"></i> Recording';
            } else {
                stopRecording();
                btn.classList.remove('active');
                btn.innerHTML = '<i class="fas fa-record-vinyl"></i>';
                statusIndicator.classList.remove('recording');
                statusIndicator.innerHTML = '<i class="fas fa-circle mr-2"></i> Live Now';
            }
        }

        function startRecording() {
            if (localStream) {
                recordedChunks = [];
                mediaRecorder = new MediaRecorder(localStream);
                
                mediaRecorder.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        recordedChunks.push(event.data);
                    }
                };
                
                mediaRecorder.onstop = () => {
                    const blob = new Blob(recordedChunks, { type: 'video/webm' });
                    const url = URL.createObjectURL(blob);
                    
                    // Create download link
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `live-class-{{ $liveClass->id }}-${Date.now()}.webm`;
                    a.click();
                };
                
                mediaRecorder.start();
                isRecording = true;
            }
        }

        function stopRecording() {
            if (mediaRecorder && isRecording) {
                mediaRecorder.stop();
                isRecording = false;
            }
        }

        function sendMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();
            
            if (message) {
                const chatMessages = document.getElementById('chatMessages');
                const messageElement = document.createElement('div');
                messageElement.className = 'mb-2';
                messageElement.innerHTML = `
                    <div class="d-flex">
                        <div class="w-8 h-8 bg-blue-500 rounded-full d-flex align-items-center justify-center text-white mr-2">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="font-weight-semibold">You</div>
                            <div class="text-muted small">${message}</div>
                        </div>
                    </div>
                `;
                
                chatMessages.appendChild(messageElement);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                input.value = '';
            }
        }

        function startTimer() {
            timerInterval = setInterval(() => {
                const elapsed = Date.now() - startTime;
                const hours = Math.floor(elapsed / 3600000);
                const minutes = Math.floor((elapsed % 3600000) / 60000);
                const seconds = Math.floor((elapsed % 60000) / 1000);
                
                document.getElementById('timer').textContent = 
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
        }

        function leaveClass() {
            if (confirm('Are you sure you want to leave this live class?')) {
                // Stop all streams
                if (localStream) {
                    localStream.getTracks().forEach(track => track.stop());
                }
                
                // Stop recording
                if (isRecording) {
                    stopRecording();
                }
                
                // Clear timer
                if (timerInterval) {
                    clearInterval(timerInterval);
                }
                
                // Redirect back
                window.location.href = '{{ route("live-classes.index") }}';
            }
        }

        // Handle Enter key in chat
        document.getElementById('chatInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            initializeWebRTC();
        });

        // Cleanup on unload
        window.addEventListener('beforeunload', function() {
            if (localStream) {
                localStream.getTracks().forEach(track => track.stop());
            }
        });
    </script>
</x-app-layout>
        width: 200px;
        height: 150px;
        border: 2px solid #fff;
        border-radius: 8px;
        z-index: 10;
        background: #333;
    }
    
    .controls-panel {
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 15px;
        border-radius: 0 0 8px 8px;
    }
    
    .participant-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    
    .participant-video {
        aspect-ratio: 16/9;
        background: #222;
        border-radius: 8px;
        position: relative;
        overflow: hidden;
    }
    
    .participant-name {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
    }
    
    .chat-panel {
        height: 400px;
        overflow-y: auto;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
    }
    
    .message {
        margin-bottom: 10px;
        padding: 8px 12px;
        border-radius: 8px;
        background: white;
        border-left: 4px solid #007bff;
    }
    
    .message.own {
        background: #e3f2fd;
        border-left-color: #2196f3;
    }
    
    .screen-share-video {
        width: 100%;
        max-height: 500px;
        object-fit: contain;
        background: #000;
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Video Area -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">{{ $liveClass->title }}</h4>
                    <small class="text-muted">{{ $liveClass->course?->course_name }}</small>
                </div>
                <div class="card-body p-0">
                    <!-- Main Video Container -->
                    <div class="video-container" style="height: 500px;">
                        <video id="mainVideo" class="video-element" autoplay playsinline></video>
                        <video id="localVideo" class="local-video" autoplay playsinline muted></video>
                        
                        <!-- Screen Share Video (Hidden by default) -->
                        <video id="screenShareVideo" class="screen-share-video" autoplay playsinline style="display: none;"></video>
                    </div>
                    
                    <!-- Controls Panel -->
                    <div class="controls-panel">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="btn-group">
                                    <button id="toggleAudio" class="btn btn-outline-light">
                                        <i class="fas fa-microphone"></i> Audio
                                    </button>
                                    <button id="toggleVideo" class="btn btn-outline-light">
                                        <i class="fas fa-video"></i> Video
                                    </button>
                                    @if($isTeacher)
                                        <button id="toggleScreenShare" class="btn btn-outline-light">
                                            <i class="fas fa-desktop"></i> Screen Share
                                        </button>
                                        <button id="recordClass" class="btn btn-outline-danger">
                                            <i class="fas fa-record-vinyl"></i> Record
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <span id="connectionStatus" class="badge badge-warning">Connecting...</span>
                                <button id="leaveRoom" class="btn btn-danger ml-2">
                                    <i class="fas fa-sign-out-alt"></i> Leave
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Participants Grid -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Participants</h5>
                </div>
                <div class="card-body">
                    <div id="participantsGrid" class="participant-grid">
                        <!-- Participants will be added here dynamically -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-lg-3">
            <!-- Class Info -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Class Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Teacher:</strong> {{ $liveClass->teacher?->name ?? 'No teacher' }}</p>
                    <p><strong>Duration:</strong> {{ $liveClass->start_time->diffInHours($liveClass->end_time) }} hours</p>
                    <p><strong>Meeting ID:</strong> <code>{{ $liveClass->meeting_id }}</code></p>
                </div>
            </div>

            <!-- Chat Panel -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Chat</h6>
                </div>
                <div class="card-body p-0">
                    <div id="chatMessages" class="chat-panel">
                        <!-- Messages will appear here -->
                    </div>
                    <div class="p-3 border-top">
                        <div class="input-group">
                            <input type="text" id="chatInput" class="form-control" placeholder="Type a message...">
                            <div class="input-group-append">
                                <button id="sendMessage" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden elements for WebRTC -->
<video id="testVideo" style="display: none;"></video>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.min.js"></script>
<script>
// Laravel Echo Configuration
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '{{ config("broadcasting.connections.pusher.key") }}',
    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
    forceTLS: true,
    auth: {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }
});

// Live Class WebRTC Implementation
class LiveClassRoom {
    constructor() {
        this.localStream = null;
        this.remoteStreams = new Map();
        this.peerConnection = null;
        this.isTeacher = {{ $isTeacher ? 'true' : 'false' }};
        this.roomId = '{{ $liveClass->meeting_id }}';
        this.isScreenSharing = false;
        this.isRecording = false;
        this.participants = new Map();
        
        this.init();
    }

    async init() {
        try {
            await this.setupLocalMedia();
            this.setupPeerConnection();
            this.setupEventListeners();
            this.setupBroadcasting();
            this.joinRoom();
        } catch (error) {
            console.error('Failed to initialize live class room:', error);
            this.showError('Failed to join the live class. Please check your camera and microphone permissions.');
        }
    }

    setupBroadcasting() {
        // Listen for WebRTC signaling messages
        window.Echo.private(`live-class.${this.roomId}`)
            .listen('.live-class.signaling', (e) => {
                this.handleSignalingMessage(e.data);
            });
    }

    async setupLocalMedia() {
        try {
            const constraints = {
                video: {
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: true
            };
            
            this.localStream = await navigator.mediaDevices.getUserMedia(constraints);
            
            const localVideo = document.getElementById('localVideo');
            localVideo.srcObject = this.localStream;
            
            // Also show in main video initially
            const mainVideo = document.getElementById('mainVideo');
            mainVideo.srcObject = this.localStream;
            
        } catch (error) {
            console.error('Failed to get local media:', error);
            throw error;
        }
    }

    setupPeerConnection() {
        const configuration = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' }
            ]
        };
        
        this.peerConnection = new RTCPeerConnection(configuration);
        
        // Add local stream to peer connection
        this.localStream.getTracks().forEach(track => {
            this.peerConnection.addTrack(track, this.localStream);
        });
        
        // Handle remote streams
        this.peerConnection.ontrack = (event) => {
            const [remoteStream] = event.streams;
            this.handleRemoteStream(remoteStream);
        };
        
        // Handle ICE candidates
        this.peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                this.sendSignalingMessage({
                    type: 'ice-candidate',
                    candidate: event.candidate
                });
            }
        };
        
        // Handle connection state changes
        this.peerConnection.onconnectionstatechange = () => {
            console.log('Connection state:', this.peerConnection.connectionState);
            this.updateStatus(this.getConnectionStatusText(), this.getConnectionStatusType());
        };
    }

    setupEventListeners() {
        // Audio toggle
        document.getElementById('toggleAudio').addEventListener('click', () => {
            this.toggleAudio();
        });
        
        // Video toggle
        document.getElementById('toggleVideo').addEventListener('click', () => {
            this.toggleVideo();
        });
        
        // Screen share (teacher only)
        if (this.isTeacher) {
            document.getElementById('toggleScreenShare').addEventListener('click', () => {
                this.toggleScreenShare();
            });
            
            document.getElementById('recordClass').addEventListener('click', () => {
                this.toggleRecording();
            });
        }
        
        // Leave room
        document.getElementById('leaveRoom').addEventListener('click', () => {
            this.leaveRoom();
        });
        
        // Chat
        document.getElementById('sendMessage').addEventListener('click', () => {
            this.sendChatMessage();
        });
        
        document.getElementById('chatInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendChatMessage();
            }
        });
    }

    async toggleAudio() {
        const audioTracks = this.localStream.getAudioTracks();
        const isEnabled = audioTracks[0].enabled;
        
        audioTracks.forEach(track => {
            track.enabled = !isEnabled;
        });
        
        const button = document.getElementById('toggleAudio');
        button.innerHTML = isEnabled ? 
            '<i class="fas fa-microphone-slash"></i> Audio' : 
            '<i class="fas fa-microphone"></i> Audio';
        button.classList.toggle('btn-outline-light');
        button.classList.toggle('btn-outline-danger');
    }

    async toggleVideo() {
        const videoTracks = this.localStream.getVideoTracks();
        const isEnabled = videoTracks[0].enabled;
        
        videoTracks.forEach(track => {
            track.enabled = !isEnabled;
        });
        
        const button = document.getElementById('toggleVideo');
        button.innerHTML = isEnabled ? 
            '<i class="fas fa-video-slash"></i> Video' : 
            '<i class="fas fa-video"></i> Video';
        button.classList.toggle('btn-outline-light');
        button.classList.toggle('btn-outline-danger');
    }

    async toggleScreenShare() {
        if (this.isScreenSharing) {
            this.stopScreenShare();
        } else {
            await this.startScreenShare();
        }
    }

    async startScreenShare() {
        try {
            const screenStream = await navigator.mediaDevices.getDisplayMedia({
                video: true,
                audio: true
            });
            
            const screenVideoTrack = screenStream.getVideoTracks()[0];
            
            // Replace video track
            const sender = this.peerConnection.getSenders().find(
                s => s.track && s.track.kind === 'video'
            );
            
            if (sender) {
                await sender.replaceTrack(screenVideoTrack);
            }
            
            // Show screen share video
            const screenShareVideo = document.getElementById('screenShareVideo');
            screenShareVideo.srcObject = screenStream;
            screenShareVideo.style.display = 'block';
            
            // Hide main video
            document.getElementById('mainVideo').style.display = 'none';
            
            this.isScreenSharing = true;
            
            const button = document.getElementById('toggleScreenShare');
            button.innerHTML = '<i class="fas fa-stop"></i> Stop Share';
            button.classList.remove('btn-outline-light');
            button.classList.add('btn-outline-danger');
            
            // Handle screen share end
            screenVideoTrack.onended = () => {
                this.stopScreenShare();
            };
            
        } catch (error) {
            console.error('Failed to start screen share:', error);
            this.showError('Failed to share screen');
        }
    }

    stopScreenShare() {
        // Restore camera video
        const videoTrack = this.localStream.getVideoTracks()[0];
        const sender = this.peerConnection.getSenders().find(
            s => s.track && s.track.kind === 'video'
        );
        
        if (sender) {
            sender.replaceTrack(videoTrack);
        }
        
        // Hide screen share video
        document.getElementById('screenShareVideo').style.display = 'none';
        document.getElementById('mainVideo').style.display = 'block';
        
        this.isScreenSharing = false;
        
        const button = document.getElementById('toggleScreenShare');
        button.innerHTML = '<i class="fas fa-desktop"></i> Screen Share';
        button.classList.remove('btn-outline-danger');
        button.classList.add('btn-outline-light');
    }

    toggleRecording() {
        this.isRecording = !this.isRecording;
        
        const button = document.getElementById('recordClass');
        if (this.isRecording) {
            button.innerHTML = '<i class="fas fa-stop"></i> Stop Recording';
            button.classList.remove('btn-outline-danger');
            button.classList.add('btn-danger');
            this.startRecording();
        } else {
            button.innerHTML = '<i class="fas fa-record-vinyl"></i> Record';
            button.classList.remove('btn-danger');
            button.classList.add('btn-outline-danger');
            this.stopRecording();
        }
    }

    startRecording() {
        // Implementation for recording
        console.log('Recording started');
        this.addSystemMessage('Recording started by teacher');
    }

    stopRecording() {
        // Implementation for stopping recording
        console.log('Recording stopped');
        this.addSystemMessage('Recording stopped');
    }

    joinRoom() {
        // Send join message via HTTP API
        fetch(`/webrtc/join/${this.roomId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            console.log('Joined room:', data);
            this.updateStatus('Connected', 'success');
        })
        .catch(error => {
            console.error('Failed to join room:', error);
            this.updateStatus('Connection Error', 'danger');
        });
    }

    leaveRoom() {
        // Send leave message via HTTP API
        fetch(`/webrtc/leave/${this.roomId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        });

        if (this.localStream) {
            this.localStream.getTracks().forEach(track => track.stop());
        }
        
        if (this.peerConnection) {
            this.peerConnection.close();
        }
        
        // Redirect back
        window.location.href = '{{ route("student.live-classes.index") }}';
    }

    sendSignalingMessage(message) {
        fetch(`/webrtc/signaling/${this.roomId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(message)
        })
        .catch(error => {
            console.error('Failed to send signaling message:', error);
        });
    }

    handleSignalingMessage(data) {
        switch (data.type) {
            case 'offer':
                this.handleOffer(data.offer);
                break;
            case 'answer':
                this.handleAnswer(data.answer);
                break;
            case 'ice-candidate':
                this.handleIceCandidate(data.candidate);
                break;
            case 'chat-message':
                this.addChatMessage(data.message, data.sender, data.senderId === this.getCurrentUserId());
                break;
            case 'participant-joined':
                this.addParticipant(data.participant);
                break;
            case 'participant-left':
                this.removeParticipant(data.participantId);
                break;
        }
    }

    async handleOffer(offer) {
        await this.peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
        const answer = await this.peerConnection.createAnswer();
        await this.peerConnection.setLocalDescription(answer);
        
        this.sendSignalingMessage({
            type: 'answer',
            answer: answer
        });
    }

    async handleAnswer(answer) {
        await this.peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
    }

    async handleIceCandidate(candidate) {
        await this.peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
    }

    handleRemoteStream(stream) {
        // Show remote stream in main video if teacher
        if (!this.isTeacher) {
            const mainVideo = document.getElementById('mainVideo');
            mainVideo.srcObject = stream;
        }
        
        // Add to participants grid
        this.addParticipantVideo(stream);
    }

    addParticipantVideo(stream) {
        const grid = document.getElementById('participantsGrid');
        const participantDiv = document.createElement('div');
        participantDiv.className = 'participant-video';
        participantDiv.id = `participant-${stream.id}`;
        
        const video = document.createElement('video');
        video.className = 'video-element';
        video.autoplay = true;
        video.playsinline = true;
        video.srcObject = stream;
        
        const name = document.createElement('div');
        name.className = 'participant-name';
        name.textContent = `Participant ${grid.children.length + 1}`;
        
        participantDiv.appendChild(video);
        participantDiv.appendChild(name);
        grid.appendChild(participantDiv);
    }

    addParticipant(participant) {
        this.participants.set(participant.id, participant);
        this.addSystemMessage(`${participant.name} joined the class`);
    }

    removeParticipant(participantId) {
        const participant = this.participants.get(participantId);
        if (participant) {
            this.addSystemMessage(`${participant.name} left the class`);
            this.participants.delete(participantId);
        }
        
        // Remove video element
        const element = document.querySelector(`[id^="participant-"]`);
        if (element && element.id !== 'localVideo') {
            element.remove();
        }
    }

    sendChatMessage() {
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        
        if (message) {
            fetch(`/webrtc/chat/${this.roomId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            });
            
            input.value = '';
        }
    }

    addChatMessage(message, sender, isOwn = false) {
        const chatMessages = document.getElementById('chatMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isOwn ? 'own' : ''}`;
        
        const time = new Date().toLocaleTimeString();
        messageDiv.innerHTML = `
            <strong>${sender}</strong> <small class="text-muted">[${time}]</small><br>
            ${message}
        `;
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    addSystemMessage(message) {
        const chatMessages = document.getElementById('chatMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'alert alert-info py-2 mb-2';
        messageDiv.innerHTML = `<small><i class="fas fa-info-circle"></i> ${message}</small>`;
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    updateStatus(text, type) {
        const status = document.getElementById('connectionStatus');
        status.textContent = text;
        status.className = `badge badge-${type}`;
    }

    getConnectionStatusText() {
        const state = this.peerConnection.connectionState;
        switch(state) {
            case 'connected': return 'Connected';
            case 'connecting': return 'Connecting...';
            case 'disconnected': return 'Disconnected';
            case 'failed': return 'Connection Failed';
            case 'closed': return 'Connection Closed';
            default: return 'Unknown';
        }
    }

    getConnectionStatusType() {
        const state = this.peerConnection.connectionState;
        switch(state) {
            case 'connected': return 'success';
            case 'connecting': return 'warning';
            case 'disconnected': return 'secondary';
            case 'failed': return 'danger';
            case 'closed': return 'secondary';
            default: return 'secondary';
        }
    }

    getCurrentUserId() {
        // This would be set from the server-side user ID
        return '{{ Auth::id() }}';
    }

    showError(message) {
        alert(message);
    }
}

// Initialize the live class room when page loads
document.addEventListener('DOMContentLoaded', function() {
    new LiveClassRoom();
});
</script>
@endsection
