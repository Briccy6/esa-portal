const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const cors = require('cors');
const axios = require('axios');

class LiveClassServer {
    constructor() {
        this.app = express();
        this.server = http.createServer(this.app);
        this.io = socketIo(this.server, {
            cors: {
                origin: ["http://localhost:8000", "http://127.0.0.1:8000"],
                methods: ["GET", "POST"]
            }
        });
        
        this.rooms = new Map(); // roomId -> room data
        this.users = new Map();  // socketId -> user data
        this.laravelUrl = 'http://localhost:8000';
        
        this.setupMiddleware();
        this.setupRoutes();
        this.setupSocketHandlers();
    }
    
    setupMiddleware() {
        this.app.use(cors());
        this.app.use(express.json());
        this.app.use(express.urlencoded({ extended: true }));
    }
    
    setupRoutes() {
        // Health check
        this.app.get('/health', (req, res) => {
            res.json({ status: 'ok', timestamp: new Date().toISOString() });
        });
        
        // Validate session with Laravel
        this.app.post('/validate-session', async (req, res) => {
            try {
                const { token, userId } = req.body;
                
                // Make request to Laravel to validate session
                const response = await axios.post(`${this.laravelUrl}/api/live-class/validate-session`, {
                    token: token,
                    userId: userId
                }, {
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });
                
                res.json(response.data);
            } catch (error) {
                console.error('Session validation error:', error.message);
                res.status(401).json({ valid: false, error: 'Invalid session' });
            }
        });
        
        // Get live class details
        this.app.get('/live-class/:meetingId', async (req, res) => {
            try {
                const meetingId = req.params.meetingId;
                
                // Make request to Laravel to get live class details
                const response = await axios.get(`${this.laravelUrl}/api/live-class/${meetingId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                res.json(response.data);
            } catch (error) {
                console.error('Get live class error:', error.message);
                res.status(404).json({ error: 'Live class not found' });
            }
        });
        
        // Update live class status
        this.app.post('/live-class/:meetingId/status', async (req, res) => {
            try {
                const meetingId = req.params.meetingId;
                const { status } = req.body;
                
                // Make request to Laravel to update status
                const response = await axios.post(`${this.laravelUrl}/api/live-class/${meetingId}/status`, {
                    status: status
                }, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                res.json(response.data);
            } catch (error) {
                console.error('Update status error:', error.message);
                res.status(500).json({ error: 'Failed to update status' });
            }
        });
    }
    
    setupSocketHandlers() {
        this.io.on('connection', (socket) => {
            console.log(`User connected: ${socket.id}`);
            
            // Handle joining a room
            socket.on('join-room', async (data) => {
                try {
                    const { roomId, user } = data;
                    
                    // Validate session with Laravel
                    const validation = await this.validateSession(data.token || 'csrf-token', user.id);
                    if (!validation.valid) {
                        socket.emit('error', { message: 'Invalid session' });
                        socket.disconnect();
                        return;
                    }
                    
                    // Get room details
                    const roomDetails = await this.getLiveClassDetails(roomId);
                    if (!roomDetails.success) {
                        socket.emit('error', { message: 'Invalid room' });
                        return;
                    }
                    
                    // Join room
                    socket.join(roomId);
                    
                    // Store user data
                    this.users.set(socket.id, {
                        ...user,
                        socketId: socket.id,
                        roomId: roomId,
                        joinedAt: new Date()
                    });
                    
                    // Initialize room if not exists
                    if (!this.rooms.has(roomId)) {
                        this.rooms.set(roomId, {
                            id: roomId,
                            participants: new Map(),
                            messages: [],
                            createdAt: new Date(),
                            details: roomDetails.data
                        });
                    }
                    
                    const room = this.rooms.get(roomId);
                    room.participants.set(socket.id, user);
                    
                    console.log(`User ${user.name} joined room ${roomId}`);
                    
                    // Notify others in room
                    socket.to(roomId).emit('user-joined', {
                        user: user,
                        participants: Array.from(room.participants.values())
                    });
                    
                    // Send current room state to new user
                    socket.emit('room-joined', {
                        room: {
                            id: roomId,
                            participants: Array.from(room.participants.values()),
                            messages: room.messages.slice(-50), // Last 50 messages
                            details: room.details
                        }
                    });
                    
                    // Update participant count
                    this.updateParticipantsCount(roomId);
                    
                } catch (error) {
                    console.error('Join room error:', error);
                    socket.emit('error', { message: 'Failed to join room' });
                }
            });
            
            // Handle WebRTC signaling
            socket.on('signal', (data) => {
                try {
                    const { roomId, targetUserId, signal } = data;
                    
                    // Find target user's socket
                    const targetSocket = Array.from(this.users.values())
                        .find(u => u.id === targetUserId && u.roomId === roomId);
                    
                    if (targetSocket) {
                        this.io.to(targetSocket.socketId).emit('signal', {
                            fromUserId: data.fromUserId,
                            signal: signal
                        });
                    }
                } catch (error) {
                    console.error('Signal error:', error);
                }
            });
            
            // Handle chat messages
            socket.on('chat-message', (data) => {
                try {
                    const { roomId, message, sender, senderId } = data;
                    
                    const room = this.rooms.get(roomId);
                    if (!room) return;
                    
                    const messageData = {
                        id: Date.now().toString(),
                        message: message,
                        sender: sender,
                        senderId: senderId,
                        timestamp: new Date().toISOString()
                    };
                    
                    // Store message
                    room.messages.push(messageData);
                    
                    // Keep only last 100 messages
                    if (room.messages.length > 100) {
                        room.messages = room.messages.slice(-100);
                    }
                    
                    // Broadcast to all in room
                    this.io.to(roomId).emit('chat-message', messageData);
                    
                    console.log(`Chat message in ${roomId} from ${sender}: ${message}`);
                    
                } catch (error) {
                    console.error('Chat message error:', error);
                }
            });
            
            // Handle screen sharing
            socket.on('start-screen-share', (data) => {
                try {
                    const { roomId } = data;
                    socket.to(roomId).emit('screen-share-started', {
                        userId: data.userId,
                        userName: data.userName
                    });
                } catch (error) {
                    console.error('Screen share error:', error);
                }
            });
            
            socket.on('stop-screen-share', (data) => {
                try {
                    const { roomId } = data;
                    socket.to(roomId).emit('screen-share-stopped', {
                        userId: data.userId
                    });
                } catch (error) {
                    console.error('Stop screen share error:', error);
                }
            });
            
            // Handle audio toggle
            socket.on('audio-toggle', (data) => {
                try {
                    const { roomId, enabled, userId } = data;
                    socket.to(roomId).emit('audio-changed', {
                        userId: userId,
                        enabled: enabled
                    });
                } catch (error) {
                    console.error('Audio toggle error:', error);
                }
            });
            
            // Handle video toggle
            socket.on('video-toggle', (data) => {
                try {
                    const { roomId, enabled, userId } = data;
                    socket.to(roomId).emit('video-changed', {
                        userId: userId,
                        enabled: enabled
                    });
                } catch (error) {
                    console.error('Video toggle error:', error);
                }
            });
            
            // Handle hand raise
            socket.on('hand-raise', (data) => {
                try {
                    const { roomId, raised, userId } = data;
                    socket.to(roomId).emit('hand-raised', {
                        userId: userId,
                        raised: raised
                    });
                } catch (error) {
                    console.error('Hand raise error:', error);
                }
            });
            
            // Handle disconnect
            socket.on('disconnect', () => {
                try {
                    const user = this.users.get(socket.id);
                    if (user && user.roomId) {
                        const room = this.rooms.get(user.roomId);
                        if (room) {
                            // Remove from participants
                            room.participants.delete(socket.id);
                            
                            // Notify others
                            socket.to(user.roomId).emit('user-left', {
                                user: user,
                                participants: Array.from(room.participants.values())
                            });
                            
                            // Update participant count
                            this.updateParticipantsCount(user.roomId);
                            
                            // Clean up empty rooms
                            if (room.participants.size === 0) {
                                this.rooms.delete(user.roomId);
                            }
                        }
                    }
                    
                    this.users.delete(socket.id);
                    console.log(`User disconnected: ${socket.id}`);
                    
                } catch (error) {
                    console.error('Disconnect error:', error);
                }
            });
        });
    }
    
    async validateSession(token, userId) {
        try {
            const response = await axios.post(`${this.laravelUrl}/api/live-class/validate-session`, {
                token: token,
                userId: userId
            }, {
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            });
            return response.data;
        } catch (error) {
            return { valid: false };
        }
    }
    
    async getLiveClassDetails(meetingId) {
        try {
            const response = await axios.get(`${this.laravelUrl}/api/live-class/${meetingId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, error: error.message };
        }
    }
    
    updateParticipantsCount(roomId) {
        const room = this.rooms.get(roomId);
        if (room) {
            const count = room.participants.size;
            this.io.to(roomId).emit('participants-update', {
                count: count,
                participants: Array.from(room.participants.values())
            });
        }
    }
    
    start(port = 3001) {
        this.server.listen(port, () => {
            console.log(`🚀 ESA Live Class Server running on port ${port}`);
            console.log(`📡 Socket.IO server ready for connections`);
            console.log(`🔗 Laravel integration: ${this.laravelUrl}`);
        });
    }
}

// Start the server
const server = new LiveClassServer();
server.start();

// Graceful shutdown
process.on('SIGTERM', () => {
    console.log('SIGTERM received, shutting down gracefully');
    server.server.close(() => {
        console.log('Server closed');
        process.exit(0);
    });
});

process.on('SIGINT', () => {
    console.log('SIGINT received, shutting down gracefully');
    server.server.close(() => {
        console.log('Server closed');
        process.exit(0);
    });
});
