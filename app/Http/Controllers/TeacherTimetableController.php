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

        // Get teacher's branch IDs (from direct branch_id or pivot table)
        $teacherBranchIds = [];
        
        if ($teacher->branch_id) {
            $teacherBranchIds[] = $teacher->branch_id;
        }
        
        // Also get branches from branch_teacher pivot table
        $pivotBranchIds = \DB::table('branch_teacher')
            ->where('teacher_id', $teacher->id)
            ->pluck('branch_id')
            ->toArray();
        
        $teacherBranchIds = array_unique(array_merge($teacherBranchIds, $pivotBranchIds));

        // If still no branches, get all branches as fallback
        if (empty($teacherBranchIds)) {
            $teacherBranchIds = \App\Models\Branch::pluck('id')->toArray();
            \Log::warning('Teacher has no branch assigned, showing all branches', ['teacher_id' => $teacher->id]);
        }

        // Debug: Log teacher info
        \Log::info('Teacher timetable query', [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->teacher_name,
            'teacher_branch_id' => $teacher->branch_id,
            'teacher_branch_ids' => $teacherBranchIds,
        ]);

        // Get lesson IDs assigned to this teacher in their branches
        $assignedLessonIds = \DB::table('lesson_branch_course')
            ->whereIn('branch_id', $teacherBranchIds)
            ->where('teacher_id', $teacher->id)
            ->pluck('lesson_id')
            ->unique()
            ->toArray();

        // Also check direct teacher_id on lessons
        $directLessonIds = \App\Models\Lesson::where('teacher_id', $teacher->id)
            ->pluck('id')
            ->toArray();

        // Merge both sources
        $allLessonIds = array_unique(array_merge($assignedLessonIds, $directLessonIds));

        \Log::info('Teacher assigned lessons', [
            'assigned_lesson_ids' => $assignedLessonIds,
            'direct_lesson_ids' => $directLessonIds,
            'all_lesson_ids' => $allLessonIds,
        ]);

        // Check if teacher has lesson_branch_course assignments
        $lbcAssignments = \DB::table('lesson_branch_course')
            ->where('teacher_id', $teacher->id)
            ->get();
        
        \Log::info('Teacher lesson_branch_course assignments', [
            'count' => $lbcAssignments->count(),
            'assignments' => $lbcAssignments->toArray(),
        ]);

        // Initialize timetables as empty collection
        $timetables = collect();

        if (empty($allLessonIds)) {
            \Log::warning('No lessons assigned to teacher', [
                'teacher_id' => $teacher->id,
                'teacher_name' => $teacher->teacher_name,
                'lbc_assignments_count' => $lbcAssignments->count(),
            ]);
            return view('teacher.timetables.index', compact('timetables'));
        }

        // Get timetables that have sessions with the teacher's assigned lessons
        $timetables = Timetable::published()
            ->whereDate('valid_from', '<=', now())
            ->whereDate('valid_until', '>=', now())
            ->whereIn('branch_id', $teacherBranchIds)
            ->whereHas('sessions', function($query) use ($allLessonIds) {
                $query->whereIn('lesson_id', $allLessonIds);
            })
            ->with(['branch', 'academicYear', 'term', 'sessions.lesson.teacher', 'sessions.lesson.teachers', 'sessions.courses'])
            ->get();

        // Filter sessions to only show those with the teacher's assigned lessons
        foreach ($timetables as $timetable) {
            $timetable->sessions = $timetable->sessions->filter(function($session) use ($allLessonIds) {
                return in_array($session->lesson_id, $allLessonIds);
            });
        }

        // Remove timetables with no matching sessions
        $filteredTimetables = $timetables->filter(function($timetable) {
            return $timetable->sessions->isNotEmpty();
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

        // Get teacher's branch IDs (from direct branch_id or pivot table)
        $teacherBranchIds = [];
        
        if ($teacher->branch_id) {
            $teacherBranchIds[] = $teacher->branch_id;
        }
        
        // Also get branches from branch_teacher pivot table
        $pivotBranchIds = \DB::table('branch_teacher')
            ->where('teacher_id', $teacher->id)
            ->pluck('branch_id')
            ->toArray();
        
        $teacherBranchIds = array_unique(array_merge($teacherBranchIds, $pivotBranchIds));

        // If still no branches, get all branches as fallback
        if (empty($teacherBranchIds)) {
            $teacherBranchIds = \App\Models\Branch::pluck('id')->toArray();
        }

        // Get lesson IDs assigned to this teacher in their branches
        $assignedLessonIds = \DB::table('lesson_branch_course')
            ->whereIn('branch_id', $teacherBranchIds)
            ->where('teacher_id', $teacher->id)
            ->pluck('lesson_id')
            ->unique()
            ->toArray();

        // Also check direct teacher_id on lessons
        $directLessonIds = \App\Models\Lesson::where('teacher_id', $teacher->id)
            ->pluck('id')
            ->toArray();

        // Merge both sources
        $allLessonIds = array_unique(array_merge($assignedLessonIds, $directLessonIds));

        $timetable->load(['branch', 'academicYear', 'term', 'sessions.lesson.teacher', 'sessions.lesson.teachers', 'sessions.courses']);

        // Filter sessions to only show those with the teacher's assigned lessons
        $teacherSessions = $timetable->sessions->filter(function ($session) use ($allLessonIds) {
            return in_array($session->lesson_id, $allLessonIds);
        })->values();

        if ($teacherSessions->isEmpty()) {
            abort(403, 'You are not authorized to view this timetable.');
        }

        return view('teacher.timetables.show', compact('timetable', 'teacherSessions'));
    }
}
