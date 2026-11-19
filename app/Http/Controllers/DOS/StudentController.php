<?php

namespace App\Http\Controllers\DOS;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Branch;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->filled('branch')) {
            $query->where('branch_id', $request->input('branch'));
        }

        if ($request->filled('course')) {
            $query->where('course_id', $request->input('course'));
        }

        if ($request->input('status') === 'active') {
            $query->where('payed_registration_fees', 1);
        } elseif ($request->input('status') === 'inactive') {
            $query->where('payed_registration_fees', 0);
        }

        $studentStats = (clone $query)
            ->selectRaw(
                'COUNT(*) as total, ' .
                'COALESCE(SUM(CASE WHEN payed_registration_fees = 1 THEN 1 ELSE 0 END), 0) as active, ' .
                'COALESCE(SUM(CASE WHEN payed_registration_fees = 0 THEN 1 ELSE 0 END), 0) as inactive'
            )
            ->first();

        $students = $query
            ->select([
                'id',
                'full_name',
                'student_id',
                'email',
                'phone',
                'branch_id',
                'course_id',
                'academic_year_id',
                'payed_registration_fees',
            ])
            ->with([
                'branch:id,name',
                'course:id,course_name',
                'academicYear:id,year',
            ])
            ->orderBy('full_name')
            ->paginate(20)
            ->withQueryString();

        $branches = Branch::query()->select('id', 'name')->get();
        $courses = Course::query()->select('id', 'course_name')->get();
        $academicYears = AcademicYear::query()->select('id', 'year')->get();
        
        return view('dos.students.index', compact(
            'students',
            'studentStats',
            'branches',
            'courses',
            'academicYears'
        ));
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

    public function markAttendance(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,late,absent'
        ]);

        try {
            DB::beginTransaction();
            
            $markedBy = Auth::guard('staff')->user()->id;
            $markedAt = now();
            
            foreach ($request->attendance as $attendanceData) {
                // Check if attendance already exists for this student on this date
                $existingAttendance = Attendance::where('student_id', $attendanceData['student_id'])
                    ->where('date', $request->date)
                    ->first();
                
                if ($existingAttendance) {
                    // Update existing attendance
                    $existingAttendance->update([
                        'status' => $attendanceData['status'],
                        'marked_by' => $markedBy,
                        'marked_at' => $markedAt,
                        'course_id' => $request->course_id,
                        'lesson_id' => $request->lesson_id,
                    ]);
                } else {
                    // Create new attendance record
                    Attendance::create([
                        'student_id' => $attendanceData['student_id'],
                        'course_id' => $request->course_id,
                        'lesson_id' => $request->lesson_id,
                        'date' => $request->date,
                        'status' => $attendanceData['status'],
                        'marked_by' => $markedBy,
                        'marked_at' => $markedAt,
                    ]);
                }
            }
            
            DB::commit();
            
            // Check if request expects JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance marked successfully!'
                ]);
            }
            
            return redirect()->route('dos.students.attendance')
                ->with('success', 'Attendance marked successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Check if request expects JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error marking attendance: ' . $e->getMessage()
                ], 500);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Error marking attendance: ' . $e->getMessage());
        }
    }

    public function getStudentsForAttendance(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $students = Student::where('branch_id', $request->branch_id)
            ->where('course_id', $request->course_id)
            ->with(['branch', 'course'])
            ->orderBy('full_name')
            ->get();

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
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
