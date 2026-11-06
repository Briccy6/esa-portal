<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timetable;

class TeacherTimetableController extends Controller
{
    public function index()
    {
        $teacher = Auth::guard('teacher')->user();

        // Debug: Log teacher info
        \Log::info('Teacher timetable query', [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->teacher_name,
        ]);

        $timetables = Timetable::published()
            ->whereDate('valid_from', '<=', now())
            ->whereDate('valid_until', '>=', now())
            ->with(['branch', 'academicYear', 'term', 'sessions.lesson.teacher', 'sessions.lesson.teachers', 'sessions.courses'])
            ->get();

        // Filter timetables to only show those where teacher is assigned to at least one session
        $filteredTimetables = $timetables->filter(function ($timetable) use ($teacher) {
            $hasAssignedSession = false;
            
            foreach ($timetable->sessions as $session) {
                // Check if teacher is assigned to this lesson (either directly or through pivot)
                if ($session->lesson) {
                    // Check direct teacher assignment
                    if ($session->lesson->teacher_id === $teacher->id) {
                        $hasAssignedSession = true;
                        break;
                    }
                    
                    // Check through lesson_branch_course pivot table
                    $isPivotAssigned = $session->lesson->teachers()
                        ->where('teachers.id', $teacher->id)
                        ->where('lesson_branch_course.branch_id', $timetable->branch_id)
                        ->exists();
                    
                    if ($isPivotAssigned) {
                        $hasAssignedSession = true;
                        break;
                    }
                }
            }
            
            return $hasAssignedSession;
        })->values();

        // Debug: Log timetable count
        \Log::info('Teacher timetable results', [
            'total_timetables' => $timetables->count(),
            'filtered_timetables' => $filteredTimetables->count(),
        ]);

        return view('teacher.timetables.index', compact('timetables'))->with('timetables', $filteredTimetables);
    }

    public function show(Timetable $timetable)
    {
        $teacher = Auth::guard('teacher')->user();

        $timetable->load(['branch', 'academicYear', 'term', 'sessions.lesson.teacher', 'sessions.lesson.teachers', 'sessions.courses']);

        // Filter sessions to only show those where teacher is assigned
        $teacherSessions = $timetable->sessions->filter(function ($session) use ($teacher) {
            if (!$session->lesson) {
                return false;
            }
            
            // Check direct teacher assignment
            if ($session->lesson->teacher_id === $teacher->id) {
                return true;
            }
            
            // Check through lesson_branch_course pivot table
            $isPivotAssigned = $session->lesson->teachers()
                ->where('teachers.id', $teacher->id)
                ->where('lesson_branch_course.branch_id', $timetable->branch_id)
                ->exists();
            
            return $isPivotAssigned;
        })->values();

        if ($teacherSessions->isEmpty()) {
            abort(403, 'You are not authorized to view this timetable.');
        }

        return view('teacher.timetables.show', compact('timetable', 'teacherSessions'));
    }
}
