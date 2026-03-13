# Live Class System Setup Guide

## Overview
This document provides instructions for setting up the custom live streaming system for your ESA portal. The system uses WebRTC for peer-to-peer video streaming and Laravel Echo for real-time signaling.

## Features Implemented
- ✅ Custom WebRTC video streaming (no third-party services)
- ✅ Admin interface for scheduling live classes
- ✅ Student interface for joining live classes
- ✅ Teacher controls (start/stop class, screen sharing, recording)
- ✅ Real-time chat during live classes
- ✅ Participant management
- ✅ Audio/video controls
- ✅ Screen sharing functionality
- ✅ Recording capability (framework ready)

## Database Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Database Tables Created
- `live_classes` - Stores live class information
- Updated with additional fields for WebRTC functionality

## Broadcasting Configuration

### 1. Install Required Packages
```bash
composer require pusher/pusher-php-server
npm install --save-dev laravel-echo pusher-js
```

### 2. Configure Environment Variables
Add these to your `.env` file:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

# Mix settings
MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 3. Enable Broadcasting
Uncomment the `BroadcastServiceProvider` in `config/app.php`:

```php
'providers' => [
    // ...
    App\Providers\BroadcastServiceProvider::class,
],
```

## Pusher Setup (Free Tier Available)

### 1. Create Pusher Account
- Sign up at https://pusher.com/
- Create a new app
- Select "Clustered" (free tier available)
- Copy your app credentials to `.env`

### 2. Configure Pusher Channels
The system uses private channels for secure communication:
- Channel: `private-live-class.{roomId}`
- Event: `live-class.signaling`

## WebRTC Configuration

### 1. STUN/TURN Servers
The system uses Google's free STUN servers by default:
- `stun:stun.l.google.com:19302`
- `stun:stun1.l.google.com:19302`

For production, consider setting up your own TURN server or using a service like Twilio.

### 2. Browser Requirements
- Chrome 60+
- Firefox 55+
- Safari 11+
- Edge 79+

## Usage Instructions

### For Admins
1. **Schedule Live Classes**
   - Go to `/admin/live-classes`
   - Click "Schedule New Live Class"
   - Fill in class details, assign teacher, set time
   - Save the class

2. **Manage Classes**
   - View all scheduled classes
   - Start/stop classes manually
   - View class details and participants

### For Teachers
1. **Start Classes**
   - Go to `/teacher/live-classes`
   - Click "Start Class" when ready
   - Use controls for audio/video/screen sharing
   - Manage recording if needed

2. **Class Controls**
   - Toggle audio/video
   - Share screen
   - Record session
   - Monitor chat

### For Students
1. **View Available Classes**
   - Go to `/student/live-classes`
   - See scheduled classes for their course
   - Join when class is live

2. **Participate**
   - Enable audio/video as needed
   - Use chat for questions
   - View screen shares

## File Structure

### Controllers
- `LiveClassController.php` - Main live class management
- `WebRTCController.php` - WebRTC signaling and chat

### Models
- `LiveClass.php` - Live class data model with relationships

### Views
- `admin/live-classes/` - Admin management interface
- `students/live-classes/` - Student interface
- `live-classes/room.blade.php` - Main video conference room

### Events & Broadcasting
- `LiveClassSignaling.php` - Real-time signaling events
- `channels.php` - Channel authorization

## Security Features

### 1. Authorization
- Only enrolled students can join their course classes
- Teachers can only start their assigned classes
- Admins have full access

### 2. Private Channels
- All communication uses private channels
- Server-side authorization checks

### 3. CSRF Protection
- All API endpoints protected with CSRF tokens

## Performance Considerations

### 1. Peer-to-Peer Limitations
- WebRTC works best with 10-15 participants
- For larger classes, consider a SFU (Selective Forwarding Unit)

### 2. Network Requirements
- Stable internet connection required
- Minimum 1 Mbps upload for video
- Recommended 3+ Mbps for good quality

### 3. Browser Resources
- Video processing uses CPU/GPU
- Consider quality settings for lower-end devices

## Troubleshooting

### Common Issues

1. **Camera/Microphone Not Working**
   - Check browser permissions
   - Ensure HTTPS is used (required for WebRTC)
   - Try different browser

2. **Connection Issues**
   - Check Pusher credentials
   - Verify firewall settings
   - Test STUN server connectivity

3. **Audio/Video Quality**
   - Check internet speed
   - Close other applications using bandwidth
   - Adjust video resolution settings

### Debug Mode
Enable console logging in browser to see WebRTC connection details.

## Future Enhancements

### Planned Features
- [ ] Recording storage and playback
- [ ] Breakout rooms
- [ ] Whiteboard functionality
- [ ] File sharing during class
- [ ] Attendance tracking
- [ ] Polls and quizzes
- [ ] Mobile app support

### Scaling Options
- [ ] SFU server for larger classes
- [ ] CDN integration for recordings
- [ ] Load balancing for high usage

## Support

For technical issues:
1. Check browser console for errors
2. Verify all environment variables are set
3. Ensure database migrations are complete
4. Test Pusher connection in dashboard

## License
This custom live streaming system is proprietary to your ESA portal and should not be redistributed without permission.
