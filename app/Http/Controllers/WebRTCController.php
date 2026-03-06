<?php

namespace App\Http\Controllers;

use App\Events\LiveClassSignaling;
use App\Models\LiveClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebRTCController extends Controller
{
    public function signaling(Request $request, $roomId)
    {
        $data = $request->all();
        $user = Auth::user();
        
        // Validate that user has access to this live class
        $liveClass = LiveClass::where('meeting_id', $roomId)->first();
        if (!$liveClass) {
            return response()->json(['error' => 'Live class not found'], 404);
        }

        // Check permissions
        $this->validateAccess($liveClass, $user);

        // Broadcast the signaling message
        broadcast(new LiveClassSignaling($roomId, $data, $user->id));

        return response()->json(['success' => true]);
    }

    public function joinRoom(Request $request, $roomId)
    {
        $user = Auth::user();
        
        // Validate that user has access to this live class
        $liveClass = LiveClass::where('meeting_id', $roomId)->first();
        if (!$liveClass) {
            return response()->json(['error' => 'Live class not found'], 404);
        }

        // Check permissions
        $this->validateAccess($liveClass, $user);

        // Broadcast that user joined
        broadcast(new LiveClassSignaling($roomId, [
            'type' => 'participant-joined',
            'participant' => [
                'id' => $user->id,
                'name' => $this->getUserName($user),
                'isTeacher' => $this->isTeacher($user, $liveClass)
            ]
        ]));

        return response()->json([
            'success' => true,
            'roomId' => $roomId,
            'isTeacher' => $this->isTeacher($user, $liveClass)
        ]);
    }

    public function leaveRoom(Request $request, $roomId)
    {
        $user = Auth::user();
        
        // Validate that user has access to this live class
        $liveClass = LiveClass::where('meeting_id', $roomId)->first();
        if (!$liveClass) {
            return response()->json(['error' => 'Live class not found'], 404);
        }

        // Broadcast that user left
        broadcast(new LiveClassSignaling($roomId, [
            'type' => 'participant-left',
            'participantId' => $user->id
        ]));

        return response()->json(['success' => true]);
    }

    public function chatMessage(Request $request, $roomId)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        $user = Auth::user();
        
        // Validate that user has access to this live class
        $liveClass = LiveClass::where('meeting_id', $roomId)->first();
        if (!$liveClass) {
            return response()->json(['error' => 'Live class not found'], 404);
        }

        // Check permissions
        $this->validateAccess($liveClass, $user);

        // Broadcast chat message
        broadcast(new LiveClassSignaling($roomId, [
            'type' => 'chat-message',
            'message' => $request->message,
            'sender' => $this->getUserName($user),
            'senderId' => $user->id,
            'timestamp' => now()->toISOString()
        ]));

        return response()->json(['success' => true]);
    }

    private function validateAccess($liveClass, $user)
    {
        // Admin has access to all classes
        if (Auth::guard('admin')->check()) {
            return true;
        }

        // Staff has access to all classes
        if (Auth::guard('staff')->check()) {
            return true;
        }

        // Teacher has access if they're assigned to the class
        if (Auth::guard('teacher')->check()) {
            return $liveClass->teacher_id == $user->id;
        }

        // Student has access if they're enrolled in the course
        if (Auth::guard('student')->check()) {
            return $liveClass->course_id == $user->course_id;
        }

        return false;
    }

    private function getUserName($user)
    {
        if (Auth::guard('admin')->check()) {
            return $user->name . ' (Admin)';
        }

        if (Auth::guard('staff')->check()) {
            return $user->name . ' (Staff)';
        }

        if (Auth::guard('teacher')->check()) {
            return $user->name . ' (Teacher)';
        }

        if (Auth::guard('student')->check()) {
            return $user->full_name ?? $user->name;
        }

        return 'Unknown User';
    }

    private function isTeacher($user, $liveClass)
    {
        if (Auth::guard('admin')->check() || Auth::guard('staff')->check()) {
            return true;
        }

        if (Auth::guard('teacher')->check()) {
            return $liveClass->teacher_id == $user->id;
        }

        return false;
    }
}
