<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('system.updates', function ($user) {
    return $user !== null;
});

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('live-class.{roomId}', function ($user, $roomId) {
    // Check if user has access to this live class room
    $liveClass = \App\Models\LiveClass::where('meeting_id', $roomId)->first();
    
    if (!$liveClass) {
        return false;
    }

    // Admin has access to all classes
    if (auth('admin')->check()) {
        return true;
    }

    // Staff has access to all classes
    if (auth('staff')->check()) {
        return true;
    }

    // Teacher has access if they're assigned to the class
    if (auth('teacher')->check()) {
        return $liveClass->teacher_id == $user->id;
    }

    // Student has access if they're enrolled in the course
    if (auth('student')->check()) {
        return $liveClass->course_id == $user->course_id;
    }

    return false;
});
