/**
 * ESA Live Class - WebRTC Client
 * Handles peer-to-peer connections for screen sharing and audio streaming
 */

class ESALiveClassClient {
    constructor(options = {}) {
        this.socket = null;
        this.localStream = null;
        this.remoteStreams = new Map();
        this.peerConnections = new Map();
        this.roomId = null;
        this.userName = null;
        this.userRole = null;
        this.isTeacher = false;
        
        // Configuration
        this.config = {
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' },
                { urls: 'stun:stun1.l.google.com:19302' }
            ],
            ...options
        };

        // Event callbacks
        this.callbacks = {
            onRoomJoined: options.onRoomJoined || (() => {}),
            onParticipantJoined: options.onParticipantJoined || (() => {}),
            onParticipantLeft: options.onParticipantLeft || (() => {}),
            onMessageReceived: options.onMessageReceived || (() => {}),
            onScreenShareStarted: options.onScreenShareStarted || (() => {}),
            onScreenShareStopped: options.onScreenShareStopped || (() => {}),
            onError: options.onError || (() => {}),
            onHandRaised: options.onHandRaised || (() => {}),
            onHandLowered: options.onHandLowered || (() => {})
        };
    }

    /**
     * Initialize connection to Socket.IO server
     */
    async connect(token, liveClassId, userName, userRole) {
        try {
            // Connect to Node.js server
            this.socket = io('http://localhost:3001', {
                auth: {
                    token: token
                }
            });

            this.roomId = liveClassId;
            this.userName = userName;
            this.userRole = userRole;
            this.isTeacher = userRole === 'teacher';

            // Set up event listeners
            this.setupSocketListeners();

            // Join room
            return new Promise((resolve, reject) => {
                this.socket.emit('join-room', {
                    token: token,
                    liveClassId: liveClassId,
                    userName: userName,
                    userRole: userRole
                });

                this.socket.once('room-joined', (data) => {
                    this.callbacks.onRoomJoined(data);
                    resolve(data);
                });

                this.socket.once('error', (error) => {
                    this.callbacks.onError(error);
                    reject(error);
                });
            });

        } catch (error) {
            this.callbacks.onError(error);
            throw error;
        }
    }

    /**
     * Set up Socket.IO event listeners
     */
    setupSocketListeners() {
        // Participant management
        this.socket.on('participant-joined', (data) => {
            this.callbacks.onParticipantJoined(data);
        });

        this.socket.on('participant-left', (data) => {
            this.callbacks.onParticipantLeft(data);
            // Clean up peer connection
            this.cleanupPeerConnection(data.socketId);
        });

        // WebRTC signaling
        this.socket.on('webrtc-offer', async (data) => {
            await this.handleOffer(data);
        });

        this.socket.on('webrtc-answer', async (data) => {
            await this.handleAnswer(data);
        });

        this.socket.on('webrtc-ice-candidate', async (data) => {
            await this.handleIceCandidate(data);
        });

        // Screen sharing
        this.socket.on('screen-share-started', (data) => {
            this.callbacks.onScreenShareStarted(data);
        });

        this.socket.on('screen-share-stopped', () => {
            this.callbacks.onScreenShareStopped();
        });

        // Chat
        this.socket.on('new-message', (message) => {
            this.callbacks.onMessageReceived(message);
        });

        // Hand raise
        this.socket.on('hand-toggled', (data) => {
            if (data.isHandRaised) {
                this.callbacks.onHandRaised(data);
            } else {
                this.callbacks.onHandLowered(data);
            }
        });

        // Audio updates
        this.socket.on('audio-toggled', (data) => {
            this.updateParticipantAudioState(data.socketId, data.isMuted);
        });

        // Connection errors
        this.socket.on('connect_error', (error) => {
            this.callbacks.onError({ message: 'Connection failed: ' + error.message });
        });

        this.socket.on('disconnect', () => {
            console.log('Disconnected from server');
        });
    }

    /**
     * Start screen sharing (teacher only)
     */
    async startScreenShare() {
        if (!this.isTeacher) {
            throw new Error('Only teachers can share screen');
        }

        try {
            const screenStream = await navigator.mediaDevices.getDisplayMedia({
                video: {
                    cursor: 'always'
                },
                audio: true
            });

            this.localStream = screenStream;

            // Notify server
            this.socket.emit('start-screen-share');

            // Create peer connections for all participants
            const roomElement = document.querySelector('[data-room-id]');
            if (roomElement) {
                const participants = JSON.parse(roomElement.dataset.participants || '[]');
                for (const participant of participants) {
                    if (participant.socketId !== this.socket.id) {
                        await this.createPeerConnection(participant.socketId, true);
                    }
                }
            }

            // Handle screen share end
            screenStream.getVideoTracks()[0].addEventListener('ended', () => {
                this.stopScreenShare();
            });

            return screenStream;

        } catch (error) {
            this.callbacks.onError({ message: 'Failed to start screen share: ' + error.message });
            throw error;
        }
    }

    /**
     * Stop screen sharing
     */
    stopScreenShare() {
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => track.stop());
            this.localStream = null;
        }

        // Close all peer connections
        this.peerConnections.forEach((pc, socketId) => {
            pc.close();
        });
        this.peerConnections.clear();

        // Notify server
        this.socket.emit('stop-screen-share');
    }

    /**
     * Create peer connection
     */
    async createPeerConnection(socketId, isInitiator = false) {
        try {
            const pc = new RTCPeerConnection(this.config);

            // Add local stream if available
            if (this.localStream) {
                this.localStream.getTracks().forEach(track => {
                    pc.addTrack(track, this.localStream);
                });
            }

            // Handle remote stream
            pc.ontrack = (event) => {
                this.handleRemoteStream(socketId, event.streams[0]);
            };

            // Handle ICE candidates
            pc.onicecandidate = (event) => {
                if (event.candidate) {
                    this.socket.emit('webrtc-ice-candidate', {
                        targetSocketId: socketId,
                        candidate: event.candidate
                    });
                }
            };

            // Store peer connection
            this.peerConnections.set(socketId, pc);

            // Create offer if initiator
            if (isInitiator && this.localStream) {
                const offer = await pc.createOffer();
                await pc.setLocalDescription(offer);

                this.socket.emit('webrtc-offer', {
                    targetSocketId: socketId,
                    offer: offer
                });
            }

            return pc;

        } catch (error) {
            this.callbacks.onError({ message: 'Failed to create peer connection: ' + error.message });
            throw error;
        }
    }

    /**
     * Handle WebRTC offer
     */
    async handleOffer(data) {
        try {
            let pc = this.peerConnections.get(data.fromSocketId);
            if (!pc) {
                pc = await this.createPeerConnection(data.fromSocketId, false);
            }

            await pc.setRemoteDescription(new RTCSessionDescription(data.offer));
            const answer = await pc.createAnswer();
            await pc.setLocalDescription(answer);

            this.socket.emit('webrtc-answer', {
                targetSocketId: data.fromSocketId,
                answer: answer
            });

        } catch (error) {
            this.callbacks.onError({ message: 'Failed to handle offer: ' + error.message });
        }
    }

    /**
     * Handle WebRTC answer
     */
    async handleAnswer(data) {
        try {
            const pc = this.peerConnections.get(data.fromSocketId);
            if (pc) {
                await pc.setRemoteDescription(new RTCSessionDescription(data.answer));
            }
        } catch (error) {
            this.callbacks.onError({ message: 'Failed to handle answer: ' + error.message });
        }
    }

    /**
     * Handle ICE candidate
     */
    async handleIceCandidate(data) {
        try {
            const pc = this.peerConnections.get(data.fromSocketId);
            if (pc) {
                await pc.addIceCandidate(new RTCIceCandidate(data.candidate));
            }
        } catch (error) {
            console.error('Failed to handle ICE candidate:', error);
        }
    }

    /**
     * Handle remote stream
     */
    handleRemoteStream(socketId, stream) {
        this.remoteStreams.set(socketId, stream);
        
        // Create video element for remote stream
        const videoContainer = document.getElementById('remote-videos');
        if (videoContainer) {
            const videoElement = document.createElement('video');
            videoElement.id = `remote-video-${socketId}`;
            videoElement.autoplay = true;
            videoElement.playsinline = true;
            videoElement.srcObject = stream;
            videoContainer.appendChild(videoElement);
        }
    }

    /**
     * Clean up peer connection
     */
    cleanupPeerConnection(socketId) {
        const pc = this.peerConnections.get(socketId);
        if (pc) {
            pc.close();
            this.peerConnections.delete(socketId);
        }

        const stream = this.remoteStreams.get(socketId);
        if (stream) {
            this.remoteStreams.delete(socketId);
        }

        // Remove video element
        const videoElement = document.getElementById(`remote-video-${socketId}`);
        if (videoElement) {
            videoElement.remove();
        }
    }

    /**
     * Send chat message
     */
    sendMessage(message) {
        if (this.socket && this.socket.connected) {
            this.socket.emit('send-message', { message });
        }
    }

    /**
     * Toggle hand raise
     */
    toggleHand() {
        if (this.socket && this.socket.connected) {
            this.socket.emit('toggle-hand');
        }
    }

    /**
     * Toggle audio (for teachers)
     */
    toggleAudio() {
        if (this.socket && this.socket.connected) {
            this.socket.emit('toggle-audio');
        }
    }

    /**
     * Update participant audio state
     */
    updateParticipantAudioState(socketId, isMuted) {
        // Update UI to show participant's audio state
        const participantElement = document.querySelector(`[data-participant-id="${socketId}"]`);
        if (participantElement) {
            const audioIndicator = participantElement.querySelector('.audio-indicator');
            if (audioIndicator) {
                audioIndicator.textContent = isMuted ? '🔇' : '🎤';
                audioIndicator.title = isMuted ? 'Muted' : 'Unmuted';
            }
        }
    }

    /**
     * Disconnect from room
     */
    disconnect() {
        this.stopScreenShare();
        
        if (this.socket) {
            this.socket.disconnect();
            this.socket = null;
        }

        // Clean up all peer connections
        this.peerConnections.forEach((pc) => pc.close());
        this.peerConnections.clear();
        this.remoteStreams.clear();
    }

    /**
     * Get connection status
     */
    isConnected() {
        return this.socket && this.socket.connected;
    }

    /**
     * Get room info
     */
    getRoomInfo() {
        return {
            roomId: this.roomId,
            userName: this.userName,
            userRole: this.userRole,
            isTeacher: this.isTeacher,
            connected: this.isConnected(),
            participantCount: this.peerConnections.size
        };
    }
}

// Export for use in browser
window.ESALiveClassClient = ESALiveClassClient;
