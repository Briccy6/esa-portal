<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
// use Illuminate\Support\Facades\Http;
use App\Models\TimetableSession;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use App\Models\AcademicYear;

class TimetableController extends Controller
{
    // --- Properties ---

    public function create()
    {
        $courses = Course::all();
        $branches = Branch::all();
        $academicYears = AcademicYear::all();
        $lessons = Lesson::all(); // Make sure to add this line
        $teachers = Teacher::all();

        return view('timetable.create', compact('courses', 'branches', 'academicYears', 'lessons', 'teachers'));
    }
}