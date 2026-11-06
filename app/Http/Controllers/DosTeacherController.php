<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\LessonBranchCourse;

class DosTeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with(['courses', 'branches'])
            ->orderBy('teacher_name')
            ->get();

        // Get lesson assignments for each teacher
        foreach ($teachers as $teacher) {
            $lessonAssignments = LessonBranchCourse::where('teacher_id', $teacher->id)
                ->with(['lesson', 'course', 'branch'])
                ->get()
                ->groupBy('lesson_id');

            $teacher->lesson_assignments = $lessonAssignments;
        }

        return view('dos.teachers.index', compact('teachers'));
    }
}
