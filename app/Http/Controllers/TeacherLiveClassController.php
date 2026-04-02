<?php

namespace App\Http\Controllers;

use App\Models\LiveClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherLiveClassController extends Controller
{
    public function index()
    {
        $teacher = Auth::guard('teacher')->user();
        
        // Get announcements for teacher layout
        $announcements = \App\Models\Announcement::latest()->take(5)->get();
        
        // Get only live classes assigned to this teacher
        $liveClasses = LiveClass::where('teacher_id', $teacher->id)
            ->with(['branches', 'courses', 'lessons'])
            ->orderBy('start_time', 'desc')
            ->paginate(10);
        
        return view('teacher.live-classes.index', compact('liveClasses', 'announcements'));
    }

    public function show(LiveClass $liveClass)
    {
        $teacher = Auth::guard('teacher')->user();
        
        // Ensure teacher is assigned to this class
        if ($liveClass->teacher_id !== $teacher->id) {
            abort(403, 'You are not authorized to view this live class');
        }
        
        // Get announcements for teacher layout
        $announcements = \App\Models\Announcement::latest()->take(5)->get();

        $liveClass->load(['branches', 'courses', 'lessons', 'participants' => function($query) {
            $query->where('is_active', true)->latest();
        }]);
        
        return view('teacher.live-classes.show', compact('liveClass', 'announcements'));
    }

    public function start(LiveClass $liveClass)
    {
        $teacher = Auth::guard('teacher')->user();
        
        // Ensure teacher is assigned to this class
        if ($liveClass->teacher_id !== $teacher->id) {
            abort(403, 'You are not authorized to start this live class');
        }

        $liveClass->update([
            'status' => 'active',
            'start_time' => now(),
        ]);

        return redirect()->route('teacher.live-classes.room', $liveClass)
            ->with('success', 'Live class started successfully!');
    }

    public function room(LiveClass $liveClass)
    {
        $teacher = Auth::guard('teacher')->user();
        
        // Ensure teacher is assigned to this class
        if ($liveClass->teacher_id !== $teacher->id) {
            abort(403, 'You are not authorized to access this live class room');
        }

        $liveClass->load(['branches', 'courses', 'lessons', 'teacher']);
        
        // Get announcements for teacher layout
        $announcements = \App\Models\Announcement::latest()->take(5)->get();
        
        // Load student requests for teachers
        $studentRequests = $liveClass->requests()
            ->with(['user' => function($query) {
                $query->select('id', 'full_name', 'course_id');
            }])
            ->where('user_type', 'student')
            ->where('status', 'pending')
            ->latest()
            ->get();
        
        return view('teacher.live-classes.room', compact('liveClass', 'studentRequests', 'announcements'));
    }

    public function end(LiveClass $liveClass)
    {
        $teacher = Auth::guard('teacher')->user();
        
        // Ensure teacher is assigned to this class
        if ($liveClass->teacher_id !== $teacher->id) {
            abort(403, 'You are not authorized to end this live class');
        }

        $liveClass->update([
            'status' => 'ended',
            'end_time' => now(),
        ]);

        return redirect()->route('teacher.live-classes.index')
            ->with('success', 'Live class ended successfully!');
    }

    public function manageRequests(LiveClass $liveClass)
    {
        $teacher = Auth::guard('teacher')->user();
        
        // Ensure teacher is assigned to this class
        if ($liveClass->teacher_id !== $teacher->id) {
            abort(403, 'You are not authorized to manage requests for this live class');
        }
        
        // Get announcements for teacher layout
        $announcements = \App\Models\Announcement::latest()->take(5)->get();

        $requests = $liveClass->requests()
            ->with('user')
            ->latest()
            ->get();
        
        return view('teacher.live-classes.requests', compact('liveClass', 'requests', 'announcements'));
    }

    public function approveRequest(Request $request, LiveClass $liveClass, $requestId)
    {
        $teacher = Auth::guard('teacher')->user();
        
        // Ensure teacher is assigned to this class
        if ($liveClass->teacher_id !== $teacher->id) {
            abort(403, 'You are not authorized to approve requests for this live class');
        }

        $classRequest = $liveClass->requests()->findOrFail($requestId);
        $classRequest->approve($teacher->id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Request approved successfully']);
        }

        return back()->with('success', 'Request approved successfully');
    }

    public function rejectRequest(Request $request, LiveClass $liveClass, $requestId)
    {
        $teacher = Auth::guard('teacher')->user();
        
        // Ensure teacher is assigned to this class
        if ($liveClass->teacher_id !== $teacher->id) {
            abort(403, 'You are not authorized to reject requests for this live class');
        }

        if ($request->ajax() || $request->wantsJson()) {
            $data = json_decode($request->getContent(), true) ?? [];
            $reason = $data['reason'] ?? null;
        } else {
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);
            $reason = $request->reason;
        }

        $classRequest = $liveClass->requests()->findOrFail($requestId);
        $classRequest->reject($reason, $teacher->id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Request rejected']);
        }

        return response()->json(['success' => true, 'message' => 'Request rejected']);
    }
}
