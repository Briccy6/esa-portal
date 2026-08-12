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

        $timetables = Timetable::published()
            ->whereHas('sessions.lesson', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->whereDate('valid_from', '<=', now())
            ->whereDate('valid_until', '>=', now())
            ->with(['branch', 'academicYear', 'sessions.lesson.teacher', 'sessions.courses'])
            ->get()
            ->filter(function ($timetable) use ($teacher) {
                return $timetable->sessions->contains(function ($session) use ($teacher) {
                    return $session->lesson && $session->lesson->teacher_id === $teacher->id;
                });
            })
            ->values();

        return view('teacher.timetables.index', compact('timetables'));
    }

    public function show(Timetable $timetable)
    {
        $teacher = Auth::guard('teacher')->user();

        $timetable->load(['branch', 'academicYear', 'sessions.lesson.teacher', 'sessions.courses']);

        $teacherSessions = $timetable->sessions->filter(function ($session) use ($teacher) {
            return $session->lesson && $session->lesson->teacher_id === $teacher->id;
        })->values();

        if ($teacherSessions->isEmpty()) {
            abort(403, 'You are not authorized to view this timetable.');
        }

        return view('teacher.timetables.show', compact('timetable', 'teacherSessions'));
    }
}
