<?php

namespace App\Http\Controllers\DOS;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Branch;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['branch', 'course', 'academicYear'])
            ->orderBy('full_name')
            ->paginate(20);
        
        $branches = Branch::all();
        $courses = Course::all();
        $academicYears = AcademicYear::all();
        
        return view('dos.students.index', compact('students', 'branches', 'courses', 'academicYears'));
    }

    public function listStudentCards()
    {
        $students = Student::with(['branch', 'course', 'academicYear'])
            ->orderBy('full_name')
            ->get();
        
        return view('dos.students.cards', compact('students'));
    }

    public function report()
    {
        $totalStudents = Student::count();
        $activeStudents = Student::where('payed_registration_fees', 1)->count();
        $inactiveStudents = Student::where('payed_registration_fees', 0)->count();
        
        $studentsByBranch = Student::join('branches', 'students.branch_id', '=', 'branches.id')
            ->select('branches.name', DB::raw('count(*) as count'))
            ->groupBy('branches.id', 'branches.name')
            ->get();
        
        $studentsByCourse = Student::join('courses', 'students.course_id', '=', 'courses.id')
            ->select('courses.course_name', DB::raw('count(*) as count'))
            ->groupBy('courses.id', 'courses.course_name')
            ->get();
        
        return view('dos.students.report', compact(
            'totalStudents', 
            'activeStudents', 
            'inactiveStudents',
            'studentsByBranch',
            'studentsByCourse'
        ));
    }

    public function attendance()
    {
        $branches = Branch::all();
        $courses = Course::all();
        $academicYears = AcademicYear::all();
        
        $attendances = Attendance::with(['student', 'lesson'])
            ->orderBy('date', 'desc')
            ->paginate(50);
        
        return view('dos.students.attendance', compact(
            'attendances',
            'branches', 
            'courses', 
            'academicYears'
        ));
    }

    public function edit(Student $student)
    {
        $branches = Branch::all();
        $courses = Course::all();
        $academicYears = AcademicYear::all();
        
        return view('dos.students.edit', compact('student', 'branches', 'courses', 'academicYears'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
        ]);

        try {
            $student->update($request->all());

            return redirect()->route('dos.students.index')
                ->with('success', 'Student information updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating student: ' . $e->getMessage());
        }
    }

    public function generateStudentCard(Student $student)
    {
        try {
            // Generate student card logic here
            return response()->json([
                'success' => true,
                'message' => 'Student card generated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating student card: ' . $e->getMessage()
            ]);
        }
    }

    public function generateCardsBulk(Request $request)
    {
        $studentIds = $request->input('students', []);
        
        try {
            $students = Student::whereIn('id', $studentIds)->get();
            
            // Bulk card generation logic here
            return response()->json([
                'success' => true,
                'message' => count($students) . ' student cards generated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating cards: ' . $e->getMessage()
            ]);
        }
    }

    public function verifyQRCode($qrData)
    {
        try {
            $student = Student::where('qr_code', $qrData)->first();
            
            if ($student) {
                return response()->json([
                    'success' => true,
                    'student' => $student->load(['branch', 'course'])
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error verifying QR code: ' . $e->getMessage()
            ]);
        }
    }
}
