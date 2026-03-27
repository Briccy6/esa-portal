# ESA Live Class Node.js Server

A Node.js server with Express and Socket.IO for handling real-time WebRTC communication for ESA Live Classes.

## Features

- **Real-time WebRTC Signaling**: Handle offer/answer/ICE candidate exchange
- **Room Management**: Create and manage live class rooms
- **Chat System**: Real-time messaging between participants
- **Screen Sharing**: Support for teacher screen sharing
- **Audio/Video Controls**: Toggle microphone and camera
- **Hand Raise**: Student hand raising functionality
- **Session Validation**: Integration with Laravel authentication
- **Participant Management**: Track users in rooms

## Installation

1. Install dependencies:
```bash
cd nodejs-live-server
npm install
```

2. Start the server:
```bash
# Development mode with auto-restart
npm run dev

# Production mode
npm start
```

The server will start on port 3001 by default.

## API Endpoints

### Health Check
- `GET /health` - Check if server is running

### Session Validation
- `POST /validate-session` - Validate user session with Laravel
  ```json
  {
    "token": "csrf_token",
    "userId": "user_id"
  }
  ```

### Live Class Details
- `GET /live-class/:meetingId` - Get live class information

### Update Live Class Status
- `POST /live-class/:meetingId/status` - Update live class status
  ```json
  {
    "status": "active|ended"
  }
  ```

## Socket.IO Events

### Client to Server

#### Room Management
- `join-room` - Join a live class room
  ```json
  {
    "roomId": "meeting_id",
    "user": {
      "id": "user_id",
      "name": "User Name",
      "role": "teacher|student"
    }
  }
  ```

#### WebRTC Signaling
- `signal` - Send WebRTC signal to specific user
  ```json
  {
    "roomId": "meeting_id",
    "targetUserId": "target_user_id",
    "signal": {
      "type": "offer|answer|ice-candidate",
      "sdp": "session_description",
      "candidate": "ice_candidate"
    }
  }
  ```

#### Chat
- `chat-message` - Send chat message
  ```json
  {
    "roomId": "meeting_id",
    "message": "Hello everyone!",
    "sender": "Teacher Name",
    "senderId": "teacher_id"
  }
  ```

#### Media Controls
- `start-screen-share` - Start screen sharing
- `stop-screen-share` - Stop screen sharing
- `audio-toggle` - Toggle microphone
- `video-toggle` - Toggle camera
- `hand-raise` - Raise/lower hand

### Server to Client

#### Room Events
- `room-joined` - Successfully joined room
- `user-joined` - New user joined room
- `user-left` - User left room
- `participants-update` - Updated participants list

#### WebRTC
- `signal` - Receive WebRTC signal from another user

#### Chat
- `chat-message` - Receive chat message

#### Media
- `screen-share-started` - Someone started screen sharing
- `screen-share-stopped` - Someone stopped screen sharing
- `audio-changed` - User toggled microphone
- `video-changed` - User toggled camera
- `hand-raised` - User raised/lowered hand

#### Error Handling
- `error` - General error message

## Laravel Integration

The server integrates with Laravel for:

1. **Session Validation**: Validates user sessions before allowing room access
2. **Live Class Data**: Retrieves live class details from Laravel database
3. **Status Updates**: Updates live class status in Laravel

### Required Laravel Routes

Add these routes to your Laravel `routes/api.php`:

```php
// Session validation
Route::post('/live-class/validate-session', [LiveClassApiController::class, 'validateSession']);

// Live class details
Route::get('/live-class/{meetingId}', [LiveClassApiController::class, 'getLiveClass']);

// Status updates
Route::post('/live-class/{meetingId}/status', [LiveClassApiController::class, 'updateStatus']);
```

### Required Laravel Controller

Create `app/Http/Controllers/LiveClassApiController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiveClass;
use Illuminate\Support\Facades\Auth;

class LiveClassApiController extends Controller
{
    public function validateSession(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'userId' => 'required|string'
        ]);

        // Validate CSRF token
        if (!hash_equals($request->token, csrf_token())) {
            return response()->json(['valid' => false]);
        }

        // Validate user session
        $user = $this->getUserById($request->userId);
        
        return response()->json([
            'valid' => !!$user,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name ?? $user->full_name,
                'role' => $this->getUserRole($user)
            ] : null
        ]);
    }

    public function getLiveClass($meetingId)
    {
        $liveClass = LiveClass::where('meeting_id', $meetingId)
            ->with(['teacher', 'courses', 'branches'])
            ->first();

        if (!$liveClass) {
            return response()->json(['error' => 'Live class not found'], 404);
        }

        return response()->json([
            'id' => $liveClass->id,
            'title' => $liveClass->title,
            'meeting_id' => $liveClass->meeting_id,
            'status' => $liveClass->status,
            'teacher' => $liveClass->teacher,
            'start_time' => $liveClass->start_time,
            'end_time' => $liveClass->end_time,
            'chat_enabled' => $liveClass->chat_enabled,
            'screen_share_enabled' => $liveClass->screen_share_enabled
        ]);
    }

    public function updateStatus(Request $request, $meetingId)
    {
        $request->validate(['status' => 'required|in:scheduled,active,ended']);

        $liveClass = LiveClass::where('meeting_id', $meetingId)->first();
        
        if (!$liveClass) {
            return response()->json(['error' => 'Live class not found'], 404);
        }

        $liveClass->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    private function getUserById($userId)
    {
        // Check different user guards
        $guards = ['admin', 'teacher', 'staff', 'student'];
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                if ((string) $user->id === $userId) {
                    return $user;
                }
            }
        }
        
        return null;
    }

    private function getUserRole($user)
    {
        // Determine user role based on model type
        if ($user instanceof \App\Models\Admin) return 'admin';
        if ($user instanceof \App\Models\Teacher) return 'teacher';
        if ($user instanceof \App\Models\Staff) return 'staff';
        if ($user instanceof \App\Models\Student) return 'student';
        
        return 'unknown';
    }
}
```

## Configuration

### Environment Variables

Create a `.env` file in the `nodejs-live-server` directory:

```env
NODE_ENV=development
PORT=3001
LARAVEL_URL=http://localhost:8000
CORS_ORIGIN=http://localhost:8000
```

### CORS Configuration

The server is configured to allow requests from:
- `http://localhost:8000` (Laravel development server)
- `http://127.0.0.1:8000`

Update the CORS configuration in `server.js` for your production environment.

## Security Considerations

1. **Session Validation**: All room joins require valid Laravel session
2. **CSRF Protection**: CSRF tokens are validated
3. **Room Access**: Users can only join rooms they have access to
4. **Rate Limiting**: Consider implementing rate limiting for production

## Monitoring

The server provides:
- Console logging for all events
- Health check endpoint
- Participant count tracking
- Error logging

## Deployment

For production deployment:

1. Set `NODE_ENV=production`
2. Use a process manager like PM2
3. Configure reverse proxy (nginx/Apache)
4. Enable HTTPS/WSS for secure connections
5. Set up proper logging

Example PM2 configuration:

```json
{
  "name": "esa-live-server",
  "script": "server.js",
  "instances": 1,
  "exec_mode": "fork",
  "env": {
    "NODE_ENV": "production",
    "PORT": 3001
  }
}
```

## Troubleshooting

### Common Issues

1. **CORS Errors**: Check CORS configuration and allowed origins
2. **Session Validation**: Ensure Laravel routes are accessible
3. **Socket Connection**: Check if port 3001 is available
4. **WebRTC Issues**: Verify STUN/TURN server configuration

### Debug Mode

Run with debug logging:

```bash
DEBUG=* npm run dev
```

## License

MIT License
