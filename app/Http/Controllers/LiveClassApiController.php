<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiveClass;
use Illuminate\Support\Facades\Auth;

class LiveClassApiController extends Controller
{
    /**
     * Validate user session for Node.js server
     */
    public function validateSession(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'userId' => 'required|string'
        ]);

        // For development, we'll be more permissive
        // In production, implement proper CSRF token validation
        $isValidToken = $this->validateCsrfToken($request->token);
        
        if (!$isValidToken) {
            return response()->json(['valid' => false, 'error' => 'Invalid CSRF token']);
        }

        // Get user by ID from appropriate guard
        $user = $this->getUserById($request->userId);
        
        if (!$user) {
            return response()->json(['valid' => false, 'error' => 'User not found']);
        }

        return response()->json([
            'valid' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name ?? $user->full_name ?? 'Unknown User',
                'role' => $this->getUserRole($user),
                'email' => $user->email ?? null
            ]
        ]);
    }

    /**
     * Get live class details by meeting ID
     */
    public function getLiveClass($meetingId)
    {
        $liveClass = LiveClass::where('meeting_id', $meetingId)
            ->with(['teacher', 'courses', 'branches'])
            ->first();

        if (!$liveClass) {
            return response()->json(['error' => 'Live class not found'], 404);
        }

        // Check if user has access to this live class
        if (!$this->hasAccessToLiveClass($liveClass)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $liveClass->id,
                'title' => $liveClass->title,
                'meeting_id' => $liveClass->meeting_id,
                'description' => $liveClass->description,
                'status' => $liveClass->status,
                'teacher' => [
                    'id' => $liveClass->teacher->id,
                    'name' => $liveClass->teacher->name ?? $liveClass->teacher->full_name,
                    'email' => $liveClass->teacher->email
                ],
                'start_time' => $liveClass->start_time,
                'end_time' => $liveClass->end_time,
                'chat_enabled' => $liveClass->chat_enabled ?? true,
                'screen_share_enabled' => $liveClass->screen_share_enabled ?? true,
                'max_participants' => $liveClass->max_participants ?? 50,
                'courses' => $liveClass->courses->map(function($course) {
                    return [
                        'id' => $course->id,
                        'name' => $course->name,
                        'code' => $course->code
                    ];
                }),
                'branches' => $liveClass->branches->map(function($branch) {
                    return [
                        'id' => $branch->id,
                        'name' => $branch->name
                    ];
                })
            ]
        ]);
    }

    /**
     * Update live class status
     */
    public function updateStatus(Request $request, $meetingId)
    {
        $request->validate(['status' => 'required|in:scheduled,active,ended']);

        $liveClass = LiveClass::where('meeting_id', $meetingId)->first();
        
        if (!$liveClass) {
            return response()->json(['error' => 'Live class not found'], 404);
        }

        // Only teacher or admin can update status
        $currentUser = $this->getCurrentUser();
        if (!$this->canUpdateStatus($liveClass, $currentUser)) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        $liveClass->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'updated_at' => now()->toISOString()
        ]);
    }

    /**
     * Get live class participants
     */
    public function getParticipants($meetingId)
    {
        $liveClass = LiveClass::where('meeting_id', $meetingId)->first();
        
        if (!$liveClass) {
            return response()->json(['error' => 'Live class not found'], 404);
        }

        // This would typically come from the Node.js server
        // For now, return enrolled students
        $participants = [];
        
        if ($liveClass->courses) {
            foreach ($liveClass->courses as $course) {
                $students = $course->students ?? [];
                foreach ($students as $student) {
                    $participants[] = [
                        'id' => $student->id,
                        'name' => $student->name ?? $student->full_name,
                        'role' => 'student',
                        'joined_at' => null
                    ];
                }
            }
        }

        // Add teacher
        if ($liveClass->teacher) {
            array_unshift($participants, [
                'id' => $liveClass->teacher->id,
                'name' => $liveClass->teacher->name ?? $liveClass->teacher->full_name,
                'role' => 'teacher',
                'joined_at' => $liveClass->start_time
            ]);
        }

        return response()->json([
            'success' => true,
            'participants' => $participants,
            'total_count' => count($participants)
        ]);
    }

    /**
     * Validate CSRF token (simplified for development)
     */
    private function validateCsrfToken($token)
    {
        // In development, be more permissive
        if (app()->environment('local', 'testing')) {
            return true;
        }
        
        // In production, validate against session
        return hash_equals($token, csrf_token());
    }

    /**
     * Get user by ID across different guards
     */
    private function getUserById($userId)
    {
        $guards = ['admin', 'teacher', 'staff', 'student'];
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                if ((string) $user->id === $userId) {
                    return $user;
                }
            }
        }
        
        // Try to find user directly if not authenticated
        foreach ($guards as $guard) {
            $model = $this->getModelForGuard($guard);
            if ($model) {
                $user = $model::find($userId);
                if ($user) {
                    return $user;
                }
            }
        }
        
        return null;
    }

    /**
     * Get current authenticated user
     */
    private function getCurrentUser()
    {
        $guards = ['admin', 'teacher', 'staff', 'student'];
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return Auth::guard($guard)->user();
            }
        }
        
        return null;
    }

    /**
     * Get user role
     */
    private function getUserRole($user)
    {
        if ($user instanceof \App\Models\Admin) return 'admin';
        if ($user instanceof \App\Models\Teacher) return 'teacher';
        if ($user instanceof \App\Models\Staff) return 'staff';
        if ($user instanceof \App\Models\Student) return 'student';
        
        return 'unknown';
    }

    /**
     * Get model class for guard
     */
    private function getModelForGuard($guard)
    {
        $models = [
            'admin' => \App\Models\Admin::class,
            'teacher' => \App\Models\Teacher::class,
            'staff' => \App\Models\Staff::class,
            'student' => \App\Models\Student::class
        ];
        
        return $models[$guard] ?? null;
    }

    /**
     * Check if user has access to live class
     */
    private function hasAccessToLiveClass($liveClass)
    {
        $currentUser = $this->getCurrentUser();
        
        if (!$currentUser) {
            return false;
        }

        // Admin and teacher have full access
        if ($currentUser instanceof \App\Models\Admin || 
            $currentUser instanceof \App\Models\Teacher) {
            return true;
        }

        // Staff have access if they manage related branches/courses
        if ($currentUser instanceof \App\Models\Staff) {
            return true; // Simplified for development
        }

        // Students must be enrolled in related courses
        if ($currentUser instanceof \App\Models\Student) {
            if ($liveClass->courses) {
                foreach ($liveClass->courses as $course) {
                    if ($course->students && $course->students->contains('id', $currentUser->id)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check if user can update live class status
     */
    private function canUpdateStatus($liveClass, $currentUser)
    {
        if (!$currentUser) {
            return false;
        }

        // Admin can update any live class
        if ($currentUser instanceof \App\Models\Admin) {
            return true;
        }

        // Teacher can update their own live classes
        if ($currentUser instanceof \App\Models\Teacher) {
            return $liveClass->teacher_id === $currentUser->id;
        }

        return false;
    }
}
