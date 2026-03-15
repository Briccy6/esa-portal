<?php

namespace App\Http\Controllers\DOS;

use App\Http\Controllers\Controller;
use App\Models\LiveClass;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LiveClassController extends Controller
{
    public function index()
    {
        $liveClasses = LiveClass::with(['branches', 'courses', 'lessons', 'teacher'])
            ->orderBy('start_time', 'desc')
            ->paginate(10);
        
        $branches = Branch::all();
        $courses = Course::all();
        $teachers = Teacher::all();
        
        return view('dos.live-classes.index', compact('liveClasses', 'branches', 'courses', 'teachers'));
    }

    public function create()
    {
        $branches = Branch::all();
        $courses = Course::all();
        $teachers = Teacher::all();
        $lessons = Lesson::all();
        
        return view('dos.live-classes.create', compact('branches', 'courses', 'teachers', 'lessons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:teachers,id',
            'course_id' => 'required|exists:courses,id',
            'branch_id' => 'required|exists:branches,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'start_time' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:480',
            'timezone' => 'nullable|string|max:50',
            'meeting_link' => 'nullable|url',
            'max_participants' => 'nullable|integer|min:1|max:1000',
            'is_recurring' => 'boolean',
            'recurring_pattern' => 'nullable|string',
        ]);

        try {
            $liveClass = LiveClass::create([
                'title' => $request->title,
                'description' => $request->description,
                'teacher_id' => $request->teacher_id,
                'course_id' => $request->course_id,
                'branch_id' => $request->branch_id,
                'lesson_id' => $request->lesson_id,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'timezone' => $request->timezone ?? 'Africa/Kigali',
                'meeting_link' => $request->meeting_link,
                'max_participants' => $request->max_participants ?? 50,
                'status' => 'scheduled',
                'is_recurring' => $request->boolean('is_recurring'),
                'recurring_pattern' => $request->recurring_pattern,
            ]);

            return redirect()->route('dos.live-classes.index')
                ->with('success', 'Live class scheduled successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error scheduling live class: ' . $e->getMessage());
        }
    }

    public function show(LiveClass $liveClass)
    {
        $liveClass->load(['branches', 'courses', 'lessons', 'teacher', 'participants']);
        
        return view('dos.live-classes.show', compact('liveClass'));
    }

    public function edit(LiveClass $liveClass)
    {
        $branches = Branch::all();
        $courses = Course::all();
        $teachers = Teacher::all();
        $lessons = Lesson::all();
        
        return view('dos.live-classes.edit', compact('liveClass', 'branches', 'courses', 'teachers', 'lessons'));
    }

    public function update(Request $request, LiveClass $liveClass)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:teachers,id',
            'course_id' => 'required|exists:courses,id',
            'branch_id' => 'required|exists:branches,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'start_time' => 'required|date',
            'duration' => 'required|integer|min:15|max:480',
            'timezone' => 'nullable|string|max:50',
            'meeting_link' => 'nullable|url',
            'max_participants' => 'nullable|integer|min:1|max:1000',
        ]);

        try {
            $liveClass->update([
                'title' => $request->title,
                'description' => $request->description,
                'teacher_id' => $request->teacher_id,
                'course_id' => $request->course_id,
                'branch_id' => $request->branch_id,
                'lesson_id' => $request->lesson_id,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'timezone' => $request->timezone ?? 'Africa/Kigali',
                'meeting_link' => $request->meeting_link,
                'max_participants' => $request->max_participants ?? 50,
            ]);

            return redirect()->route('dos.live-classes.index')
                ->with('success', 'Live class updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating live class: ' . $e->getMessage());
        }
    }

    public function destroy(LiveClass $liveClass)
    {
        try {
            $liveClass->delete();
            
            return redirect()->route('dos.live-classes.index')
                ->with('success', 'Live class deleted successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error deleting live class: ' . $e->getMessage());
        }
    }

    public function start(LiveClass $liveClass)
    {
        try {
            $liveClass->update([
                'status' => 'live',
                'started_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Live class started successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error starting live class: ' . $e->getMessage()
            ]);
        }
    }

    public function end(LiveClass $liveClass)
    {
        try {
            $liveClass->update([
                'status' => 'completed',
                'ended_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Live class ended successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error ending live class: ' . $e->getMessage()
            ]);
        }
    }

    public function room(LiveClass $liveClass)
    {
        $liveClass->load(['branches', 'courses', 'lessons', 'teacher']);
        
        return view('dos.live-classes.room', compact('liveClass'));
    }

    public function manageRequests(LiveClass $liveClass)
    {
        $liveClass->load(['participants', 'requests']);
        
        return view('dos.live-classes.requests', compact('liveClass'));
    }
}
