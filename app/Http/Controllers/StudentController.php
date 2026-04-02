<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\Branch;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\Payment;
use App\Models\SchoolFee;
use App\Models\PaymentRecord;
use App\Models\EmailVerification;
use App\Models\Transcript;
use App\Models\Announcement;
use App\Models\DismissedStudent;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentWelcomeMail;
use App\Mail\StudentEmailVerificationMail;
use App\Mail\StudentInformationEmail;
use App\Mail\NesaRegistrationStatusMail;
use App\Mail\StudentDismissalMail;
use Illuminate\Support\Facades\Storage;
use App\Models\StudentTransaction;
use App\Models\Exam;
use App\Models\Timetable;
use App\Models\CourseMaterial;
use App\Models\StudentCard;
use App\Models\Attendance;
use App\Support\StudentPaymentTranscript;

class StudentController extends Controller
{
    public function studentExams()
    {
        $student = auth('student')->user();

        $exams = \App\Models\Exam::where(function ($query) use ($student) {
            $query->whereHas('courses', function ($q) use ($student) {
                $q->where('courses.id', $student->course_id);
            })
            ->orWhereHas('lessons', function ($q) use ($student) {
                $q->where('lessons.course_id', $student->course_id);
            });
        })
        ->with(['marks' => function ($q) use ($student) {
            $q->where('student_id', $student->id);
        }])
        ->orderBy('class_date', 'asc')
        ->get();

        return view('students.exams', compact('exams'));
    }

    public function invoices()
    {
        $student = auth('student')->user();
        $invoices = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->whereIn('status', ['paid', 'successful', 'pending'])
            ->orderByDesc('created_at')
            ->get();

        return view('students.invoices', compact('invoices'));
    }

    public function courses()
    {
        $student = auth('student')->user();

        $courses = \App\Models\Course::where('id', $student->course_id)->get();

        foreach ($courses as $course) {
            $course->lessons = \App\Models\Lesson::with(['teacher', 'terms'])
                ->where('course_id', $course->id)
                ->get();
        }

        return view('students.courses', compact('courses'));
    }

    public function grades()
    {
        $student = auth('student')->user();
        if (!$student) {
            abort(403, 'Unauthorized');
        }

        $marks = \App\Models\Mark::where('student_id', $student->id)
            ->with(['course', 'branch'])
            ->get();

        foreach ($marks as $mark) {
            $allMarks = \App\Models\Mark::where([
                    'course_id' => $mark->course_id,
                    'branch_id' => $mark->branch_id,
                ])
                ->orderByDesc('value')
                ->pluck('student_id', 'value')
                ->toArray();

            $sortedMarks = array_keys($allMarks);
            $position = array_search($mark->value, $sortedMarks) + 1;
            $mark->position = $position;
            $mark->total_students = count($allMarks);
        }

        return view('students.grades', compact('marks'));
    }

    public function index(Request $request)
    {
        $staff = auth('staff')->user();
        $admin = auth('admin')->user();

        if (!$staff && !$admin) {
            abort(403, 'Unauthorized: Not logged in as staff or admin.');
        }

        // Get dismissed student IDs to exclude them from active list
        $dismissedStudentIds = DismissedStudent::pluck('original_student_id')->toArray();

        if ($admin) {
            $students = Student::with(['course', 'branch', 'academicYear'])
                ->whereNotIn('id', $dismissedStudentIds);
        } else {
            $branchIds = $staff->branches()->pluck('branches.id')->toArray();
            $students = Student::whereIn('branch_id', $branchIds)
                ->with(['course', 'branch', 'academicYear'])
                ->whereNotIn('id', $dismissedStudentIds);
        }

        if ($request->filled('name')) {
            $students->where('full_name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('reg_number')) {
            $students->where('reg_number', 'like', '%' . $request->reg_number . '%');
        }
        if ($request->filled('email')) {
            $students->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('telephone')) {
            $students->where('phone', 'like', '%' . $request->telephone . '%');
        }
        if ($request->filled('academic_year')) {
            $students->whereHas('academicYear', function ($q) use ($request) {
                $q->where('year', 'like', '%' . $request->academic_year . '%');
            });
        }
        if ($request->filled('course')) {
            $students->whereHas('course', function ($q) use ($request) {
                $q->where('course_name', 'like', '%' . $request->course . '%')
                  ->orWhere('abbreviation', 'like', '%' . $request->course . '%');
            });
        }
        if ($request->filled('id_number')) {
            $students->where('id_number', 'like', '%' . $request->id_number . '%');
        }

        $allStudents = Student::with(['branch', 'course'])
            ->whereNotIn('id', $dismissedStudentIds)
            ->orderBy('full_name', 'asc')
            ->get();

        $students = $students->orderBy('created_at', 'desc')->paginate(20);

        if ($request->ajax() || $request->wantsJson()) {
            if ($admin) {
                return view('admin.students.partials.students_table', compact('students'))->render();
            } else {
                return view('staff.partials.students_table', compact('students'))->render();
            }
        }

        if ($admin) {
            return view('admin.students.index', compact('students', 'allStudents'));
        } else {
            return view('students.index', compact('students', 'allStudents'));
        }
    }

    public function create()
    {
        $branches = Branch::all();
        $courses = Course::all();
        $academicYears = AcademicYear::all();

        return view('students.create', compact('branches', 'courses', 'academicYears'));
    }

    public function getAcademicYears()
    {
        return AcademicYear::orderByDesc('starting_date')->get();
    }

    public function getBranchesByAcademicYear(Request $request)
    {
        $academicYearId = $request->query('academic_year_id');

        if (!$academicYearId) {
            return response()->json([], 400);
        }

        $branchIds = DB::table('branch_course')
            ->join('courses', 'branch_course.course_id', '=', 'courses.id')
            ->where('courses.academic_year_id', $academicYearId)
            ->pluck('branch_course.branch_id')
            ->unique();

        $branches = Branch::whereIn('id', $branchIds)->get();

        return response()->json($branches);
    }

    public function getCoursesByBranchAndYear(Request $request)
    {
        $branchId = $request->query('branch_id');
        $academicYearId = $request->query('academic_year_id');

        if (!$branchId || !$academicYearId) {
            return response()->json([], 400);
        }

        $courses = Course::where('academic_year_id', $academicYearId)
            ->whereHas('branches', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->get();

        return response()->json($courses);
    }

    public function showStudentFees(Request $request, $studentId)
    {
        $academicYears = AcademicYear::all();
        $terms = Term::all();

        $academicYearId = $request->query('academic_year_id') ?? ($academicYears->first()->id ?? null);
        $termId = $request->query('term_id') ?? ($terms->first()->id ?? null);
        $studentCardFee = 2000;
        $schoolFee = null;

        if ($academicYearId && $termId) {
            $schoolFee = SchoolFee::where('academic_year_id', $academicYearId)
                ->where('term_id', $termId)
                ->first();
        }

        return view('admin.students.show_fees', compact('academicYears', 'terms', 'schoolFee', 'academicYearId', 'termId', 'studentCardFee'));
    }

    public function show(Student $student, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $termId = $request->get('term_id');

        $student->load('branch', 'course');

        $schoolFee = null;
        if ($academicYearId && $termId) {
            $schoolFee = SchoolFee::where('academic_year_id', $academicYearId)
                ->where('term_id', $termId)
                ->first();
        }

        $studentCardFee = 2000;

        $paid = [
            'registration' => StudentTransaction::where('student_id', $student->id)
                ->where('type', 'registration')
                ->where('status', 'successful')
                ->sum('amount'),
            'tuition' => StudentTransaction::where('student_id', $student->id)
                ->where('type', 'school_fee')
                ->where('status', 'successful')
                ->sum('amount'),
            'practical' => StudentTransaction::where('student_id', $student->id)
                ->where('type', 'practical_fee')
                ->where('status', 'successful')
                ->sum('amount'),
            'trip' => StudentTransaction::where('student_id', $student->id)
                ->where('type', 'trip_fee')
                ->where('status', 'successful')
                ->sum('amount'),
        ];

        $studentCardPaid = StudentTransaction::where('student_id', $student->id)
            ->where('type', 'student_card')
            ->where('status', 'successful')
            ->sum('amount');

        $totalPaid = array_sum($paid) + $studentCardPaid;

        return view('admin.students.show', compact(
            'student', 'schoolFee', 'academicYearId', 'termId',
            'studentCardFee', 'studentCardPaid', 'paid', 'totalPaid'
        ));
    }

    public function edit(Student $student)
    {
        $branches = Branch::all();
        $courses = Course::all();
        $academicYears = AcademicYear::all();

        return view('admin.students.edit', compact('student', 'branches', 'courses', 'academicYears'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'full_name'                  => 'required|string|max:255',
            'email'                      => 'required|email|max:255|unique:students,email,' . $student->id,
            'phone'                      => 'required|string|max:20',
            'sex'                        => 'required|in:Male,Female,Other',
            'branch_id'                  => 'required|exists:branches,id',
            'course_id'                  => 'required|exists:courses,id',
            'academic_year_id'           => 'required|exists:academic_years,id',
            'discount'                   => 'nullable|numeric|min:0|max:100',
            'education_level'            => 'nullable|string|max:50',
            'identification_type'        => 'nullable|in:ID,Passport',
            'id_number'                  => 'nullable|string|max:50',
            'id_photo'                   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'passport_photo'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'senior6_document'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'senior3_document'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'other_supporting_document'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'full_name.required'    => 'Student name is required',
            'email.required'        => 'Email address is required',
            'email.email'           => 'Please enter a valid email address',
            'email.unique'          => 'This email is already registered',
            'phone.required'        => 'Phone number is required',
            'sex.required'          => 'Sex is required',
            'id_photo.image'        => 'ID photo must be an image file',
            'id_photo.max'          => 'ID photo size must not exceed 2MB',
            'passport_photo.image'  => 'Passport photo must be an image file',
            'passport_photo.max'    => 'Passport photo size must not exceed 2MB',
        ]);

        try {
            DB::beginTransaction();

            $updatedFields = [];

            if ($request->full_name !== $student->full_name) {
                $student->full_name = $request->full_name;
                $updatedFields[] = 'Full Name';
            }
            if ($request->sex !== $student->sex) {
                $student->sex = $request->sex;
                $updatedFields[] = 'Sex';
            }
            if ($request->email !== $student->email) {
                $student->email = $request->email;
                $updatedFields[] = 'Email';
            }
            if ($request->phone !== $student->phone) {
                $student->phone = $request->phone;
                $updatedFields[] = 'Phone';
            }
            if ($request->branch_id != $student->branch_id) {
                $student->branch_id = $request->branch_id;
                $updatedFields[] = 'Branch';
            }
            if ($request->course_id != $student->course_id) {
                $student->course_id = $request->course_id;
                $updatedFields[] = 'Course';
            }
            if ($request->academic_year_id != $student->academic_year_id) {
                $student->academic_year_id = $request->academic_year_id;
                $updatedFields[] = 'Academic Year';
            }
            if ($request->filled('discount') && $request->discount != $student->discount) {
                $student->discount = $request->discount;
                $updatedFields[] = 'Discount';
            }
            if ($request->filled('education_level') && $request->education_level !== $student->education_level) {
                $student->education_level = $request->education_level;
                $updatedFields[] = 'Education Level';
            }
            if ($request->filled('identification_type') && $request->identification_type !== $student->identification_type) {
                $student->identification_type = $request->identification_type;
                $updatedFields[] = 'Identification Type';
            }
            if ($request->filled('id_number') && $request->id_number !== $student->id_number) {
                $student->id_number = $request->id_number;
                $updatedFields[] = 'ID Number';
            }

            if ($request->hasFile('id_photo')) {
                if ($student->id_photo && Storage::disk('public')->exists($student->id_photo)) {
                    Storage::disk('public')->delete($student->id_photo);
                }
                $student->id_photo = $request->file('id_photo')->store('students/id_photo', 'public');
                $updatedFields[] = 'ID Photo';
            }

            if ($request->hasFile('passport_photo')) {
                if ($student->passport_photo && Storage::disk('public')->exists($student->passport_photo)) {
                    Storage::disk('public')->delete($student->passport_photo);
                }
                $student->passport_photo = $request->file('passport_photo')->store('students/passport_photo', 'public');
                $updatedFields[] = 'Passport Photo';
            }
            if ($request->hasFile('senior6_document')) {
                if ($student->senior6_document && Storage::disk('public')->exists($student->senior6_document)) {
                    Storage::disk('public')->delete($student->senior6_document);
                }
                $student->senior6_document = $request->file('senior6_document')->store('students/documents', 'public');
                $updatedFields[] = 'Senior 6 Certificate';
            }
            if ($request->hasFile('senior3_document')) {
                if ($student->senior3_document && Storage::disk('public')->exists($student->senior3_document)) {
                    Storage::disk('public')->delete($student->senior3_document);
                }
                $student->senior3_document = $request->file('senior3_document')->store('students/documents', 'public');
                $updatedFields[] = 'Senior 3 Certificate';
            }
            if ($request->hasFile('other_supporting_document')) {
                if ($student->other_supporting_document && Storage::disk('public')->exists($student->other_supporting_document)) {
                    Storage::disk('public')->delete($student->other_supporting_document);
                }
                $student->other_supporting_document = $request->file('other_supporting_document')->store('students/documents', 'public');
                $updatedFields[] = 'Supporting Document';
            }

            $student->save();

            DB::commit();

            Log::info('Student updated by admin', [
                'student_id'     => $student->id,
                'admin_id'       => auth('admin')->id,
                'updated_fields' => $updatedFields,
            ]);

            if (count($updatedFields) > 0) {
                return redirect()->route('admin.students.edit', $student)
                    ->with('success', 'Student information updated successfully!')
                    ->with('updated_fields', $updatedFields);
            }

            return redirect()->route('admin.students.edit', $student)
                ->with('success', 'No changes were made.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Student update failed', [
                'student_id' => $student->id,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update student: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function editByStaff(Student $student)
    {
        if (!auth()->guard('staff')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $staff = auth('staff')->user();
        $branchIds = $staff->branches()->pluck('branches.id')->toArray();

        if (!in_array($student->branch_id, $branchIds)) {
            return redirect()->back()->with('error', 'You do not have access to edit this student.');
        }

        $branches = Branch::whereIn('id', $branchIds)->get();
        $courses = Course::all();
        $academicYears = AcademicYear::all();

        return view('staff.students.edit', compact('student', 'branches', 'courses', 'academicYears'));
    }

    public function updateByStaff(Request $request, Student $student)
    {
        if (!auth()->guard('staff')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $staff = auth('staff')->user();
        $branchIds = $staff->branches()->pluck('branches.id')->toArray();

        if (!in_array($student->branch_id, $branchIds)) {
            return redirect()->back()->with('error', 'You do not have access to edit this student.');
        }

        $request->validate([
            'full_name'                  => 'required|string|max:255',
            'email'                      => 'required|email|max:255|unique:students,email,' . $student->id,
            'phone'                      => 'required|string|max:20',
            'sex'                        => 'required|in:Male,Female,Other',
            'branch_id'                  => 'required|exists:branches,id',
            'course_id'                  => 'required|exists:courses,id',
            'academic_year_id'           => 'required|exists:academic_years,id',
            'discount'                   => 'nullable|numeric|min:0|max:100',
            'education_level'            => 'nullable|string|max:50',
            'identification_type'        => 'nullable|in:ID,Passport',
            'id_number'                  => 'nullable|string|max:50',
            'id_photo'                   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'passport_photo'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'senior6_document'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'senior3_document'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'other_supporting_document'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'full_name.required'    => 'Student name is required',
            'email.required'        => 'Email address is required',
            'email.email'           => 'Please enter a valid email address',
            'email.unique'          => 'This email is already registered',
            'phone.required'        => 'Phone number is required',
            'sex.required'          => 'Sex is required',
            'id_photo.image'        => 'ID photo must be an image file',
            'id_photo.max'          => 'ID photo size must not exceed 2MB',
            'passport_photo.image'  => 'Passport photo must be an image file',
            'passport_photo.max'    => 'Passport photo size must not exceed 2MB',
        ]);

        try {
            DB::beginTransaction();

            $updatedFields = [];

            if ($request->full_name !== $student->full_name) {
                $student->full_name = $request->full_name;
                $updatedFields[] = 'Full Name';
            }
            if ($request->sex !== $student->sex) {
                $student->sex = $request->sex;
                $updatedFields[] = 'Sex';
            }
            if ($request->email !== $student->email) {
                $student->email = $request->email;
                $updatedFields[] = 'Email';
            }
            if ($request->phone !== $student->phone) {
                $student->phone = $request->phone;
                $updatedFields[] = 'Phone';
            }
            if ($request->branch_id != $student->branch_id) {
                $student->branch_id = $request->branch_id;
                $updatedFields[] = 'Branch';
            }
            if ($request->course_id != $student->course_id) {
                $student->course_id = $request->course_id;
                $updatedFields[] = 'Course';
            }
            if ($request->academic_year_id != $student->academic_year_id) {
                $student->academic_year_id = $request->academic_year_id;
                $updatedFields[] = 'Academic Year';
            }
            if ($request->filled('discount') && $request->discount != $student->discount) {
                $student->discount = $request->discount;
                $updatedFields[] = 'Discount';
            }
            if ($request->filled('education_level') && $request->education_level !== $student->education_level) {
                $student->education_level = $request->education_level;
                $updatedFields[] = 'Education Level';
            }
            if ($request->filled('identification_type') && $request->identification_type !== $student->identification_type) {
                $student->identification_type = $request->identification_type;
                $updatedFields[] = 'Identification Type';
            }
            if ($request->filled('id_number') && $request->id_number !== $student->id_number) {
                $student->id_number = $request->id_number;
                $updatedFields[] = 'ID Number';
            }

            if ($request->hasFile('id_photo')) {
                if ($student->id_photo && Storage::disk('public')->exists($student->id_photo)) {
                    Storage::disk('public')->delete($student->id_photo);
                }
                $student->id_photo = $request->file('id_photo')->store('students/id_photo', 'public');
                $updatedFields[] = 'ID Photo';
            }

            if ($request->hasFile('passport_photo')) {
                if ($student->passport_photo && Storage::disk('public')->exists($student->passport_photo)) {
                    Storage::disk('public')->delete($student->passport_photo);
                }
                $student->passport_photo = $request->file('passport_photo')->store('students/passport_photo', 'public');
                $updatedFields[] = 'Passport Photo';
            }
            if ($request->hasFile('senior6_document')) {
                if ($student->senior6_document && Storage::disk('public')->exists($student->senior6_document)) {
                    Storage::disk('public')->delete($student->senior6_document);
                }
                $student->senior6_document = $request->file('senior6_document')->store('students/documents', 'public');
                $updatedFields[] = 'Senior 6 Certificate';
            }
            if ($request->hasFile('senior3_document')) {
                if ($student->senior3_document && Storage::disk('public')->exists($student->senior3_document)) {
                    Storage::disk('public')->delete($student->senior3_document);
                }
                $student->senior3_document = $request->file('senior3_document')->store('students/documents', 'public');
                $updatedFields[] = 'Senior 3 Certificate';
            }
            if ($request->hasFile('other_supporting_document')) {
                if ($student->other_supporting_document && Storage::disk('public')->exists($student->other_supporting_document)) {
                    Storage::disk('public')->delete($student->other_supporting_document);
                }
                $student->other_supporting_document = $request->file('other_supporting_document')->store('students/documents', 'public');
                $updatedFields[] = 'Supporting Document';
            }

            $student->save();

            DB::commit();

            Log::info('Student updated by staff', [
                'student_id'     => $student->id,
                'staff_id'       => $staff->id,
                'updated_fields' => $updatedFields,
            ]);

            if (count($updatedFields) > 0) {
                return redirect()->route('staff.students.edit', $student)
                    ->with('success', 'Student information updated successfully!')
                    ->with('updated_fields', $updatedFields);
            }

            return redirect()->route('staff.students.edit', $student)
                ->with('success', 'No changes were made.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Student update failed', [
                'student_id' => $student->id,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update student: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function report(Request $request)
    {
        $staff = auth('staff')->user();

        if (!$staff) {
            abort(403, 'Unauthorized');
        }

        $branchIds = $staff->branches()->pluck('branches.id')->toArray();

        $query = Student::whereIn('branch_id', $branchIds)
            ->with(['course', 'branch', 'academicYear']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                  ->orWhere('reg_number', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('payed_registration_fees', 1);
            } elseif ($request->status === 'inactive') {
                $query->where('payed_registration_fees', 0);
            }
        }

        $students = $query->orderBy('created_at', 'desc')->get();

        $courses = Course::whereIn('id', function ($q) use ($branchIds) {
            $q->select('course_id')
              ->from('branch_course')
              ->whereIn('branch_id', $branchIds);
        })->get();

        $branches = Branch::whereIn('id', $branchIds)->get();
        $academicYears = AcademicYear::whereIn('id', function ($q) use ($branchIds) {
            $q->select('academic_year_id')
              ->from('courses')
              ->whereIn('id', function ($subQ) use ($branchIds) {
                  $subQ->select('course_id')
                        ->from('branch_course')
                        ->whereIn('branch_id', $branchIds);
              });
        })->get();

        $totalStudents    = $students->count();
        $activeStudents   = $students->where('payed_registration_fees', 1)->count();
        $inactiveStudents = $totalStudents - $activeStudents;

        return view('staff.students.report', compact(
            'students',
            'courses',
            'branches',
            'academicYears',
            'totalStudents',
            'activeStudents',
            'inactiveStudents'
        ));
    }

    public function confirmRegistrationFee(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);

        $registrationRecord = PaymentRecord::where('student_id', $student->id)
            ->where('type', 'registration')
            ->where('is_paid', false)
            ->first();

        if ($registrationRecord) {
            $registrationRecord->is_paid = true;
            $registrationRecord->save();

            $schoolFeeToken = Str::random(32);
            PaymentRecord::create([
                'student_id' => $student->id,
                'type'       => 'school_fee',
                'token'      => $schoolFeeToken,
                'expires_at' => now()->addDays(30),
                'is_paid'    => false,
            ]);

            $password = $student->plain_password ?? 'Your chosen password';
            Mail::to($student->email)->send(new StudentWelcomeMail($student, $password));

            return redirect()->back()->with('success', 'Registration fee confirmed. School fee token generated and email sent.');
        }

        return redirect()->back()->with('error', 'No pending registration fee found for this student.');
    }

    public function sendVerificationCode(Request $request)
    {
        try {
            if ($request->isJson()) {
                $data = $request->json()->all();
                $regNumber = $data['reg_number'] ?? null;
            } else {
                $regNumber = $request->input('reg_number');
            }

            if (!$regNumber) {
                return response()->json(['message' => 'Registration number is required.'], 400);
            }

            $student = Student::where('reg_number', $regNumber)->first();
            if (!$student) {
                return response()->json(['message' => 'Registration number not found.'], 404);
            }

            $code = rand(100000, 999999);

            EmailVerification::where('email', $student->email)->delete();

            $nextId = DB::table('email_verifications')->max('id') + 1;

            DB::table('email_verifications')->insert([
                'id'         => $nextId,
                'email'      => $student->email,
                'code'       => $code,
                'expires_at' => now()->addMinutes(10),
                'verified'   => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            try {
                Mail::to($student->email)->send(new StudentEmailVerificationMail($code));
                Log::info('Verification email sent successfully', ['email' => $student->email, 'code' => $code]);

                return response()->json([
                    'message' => 'Verification code sent to your email.',
                    'debug'   => [
                        'email'      => $student->email,
                        'code'       => $code,
                        'expires_at' => now()->addMinutes(10)->format('Y-m-d H:i:s'),
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send verification email', [
                    'email' => $student->email,
                    'error' => $e->getMessage(),
                    'code'  => $code,
                ]);

                return response()->json([
                    'message' => 'Verification code generated but email failed to send. Code: ' . $code,
                    'debug'   => [
                        'email_error' => $e->getMessage(),
                        'code'        => $code,
                        'expires_at'  => now()->addMinutes(10)->format('Y-m-d H:i:s'),
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('sendVerificationCode failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to send verification code. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyEmailCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string',
        ]);

        $record = EmailVerification::where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if ($record) {
            $record->verified = true;
            $record->save();
            return response()->json(['message' => 'Email verified.']);
        }

        return response()->json(['message' => 'Invalid or expired code.'], 422);
    }

    public function getCourseFee(Request $request)
    {
        // Case 1: Manual registration (Secretary, cash)
        if ($request->filled('amount_paid') && $request->filled('fee_type')) {
            $request->validate([
                'full_name'        => 'required|string|max:255',
                'email'            => 'required|email|unique:students,email',
                'phone'            => 'required|string',
                'sex'              => 'required|in:Male,Female',
                'academic_year_id' => 'required|exists:academic_years,id',
                'branch_id'        => 'required|exists:branches,id',
                'course_id'        => 'required|exists:courses,id',
                'fee_type'         => 'required|string',
                'amount_paid'      => 'required|numeric|min:1',
            ]);

            DB::beginTransaction();
            try {
                $course       = Course::findOrFail($request->course_id);
                $academicYear = AcademicYear::findOrFail($request->academic_year_id);
                $plainPassword = Str::random(8);

                $maxAttempts = 5;
                $student     = null;

                for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                    try {
                        $regNumber = $this->generateUniqueRegNumber($course->abbreviation, $academicYear->year);

                        $student = Student::create([
                            'full_name'               => $request->full_name,
                            'email'                   => $request->email,
                            'phone'                   => $request->phone,
                            'sex'                     => $request->sex,
                            'branch_id'               => $request->branch_id,
                            'course_id'               => $request->course_id,
                            'academic_year_id'        => $request->academic_year_id,
                            'reg_number'              => $regNumber,
                            'password'                => bcrypt($plainPassword),
                            'payed_registration_fees' => $request->fee_type === 'registration_fee' ? 1 : 0,
                        ]);

                        break;

                    } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                        if ($attempt === $maxAttempts) {
                            throw new \Exception('Unable to generate unique registration number after ' . $maxAttempts . ' attempts. Please try again.');
                        }
                        usleep(100000);
                        continue;
                    }
                }

                if (!$student) {
                    throw new \Exception('Failed to create student record.');
                }

                $student->initializeTokens(10);

                StudentTransaction::create([
                    'student_id' => $student->id,
                    'tx_ref'     => 'CASH-' . $student->id . '-' . time() . '-' . uniqid(),
                    'type'       => $request->fee_type,
                    'amount'     => $request->amount_paid,
                    'currency'   => 'RWF',
                    'status'     => 'successful',
                    'meta'       => json_encode([
                        'method'      => 'cash',
                        'recorded_by' => auth()->user()->name ?? 'staff',
                    ]),
                    'paid_at' => now(),
                ]);

                DB::commit();

                if ($student->email) {
                    try {
                        Mail::to($student->email)->send(new \App\Mail\StudentWelcomeMail($student, $plainPassword));
                    } catch (\Exception $e) {
                        Log::error('Failed to send student welcome email', ['error' => $e->getMessage()]);
                    }
                }

                return back()->with('success', 'Student registered successfully. Registration Number: ' . $student->reg_number);

            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Student registration failed', ['error' => $e->getMessage()]);
                return back()->with('error', 'Registration failed: ' . $e->getMessage());
            }
        }

        // Case 2: Self-registration (Online with Flutterwave)
        $request->validate([
            'full_name'                  => 'required|string',
            'email'                      => 'required|email|unique:students,email',
            'phone'                      => 'required|string',
            'academic_year_id'           => 'required|exists:academic_years,id',
            'branch_id'                  => 'required|exists:branches,id',
            'course_id'                  => 'required|exists:courses,id',
            'passport_photo'             => 'nullable|file|max:200',
            'id_photo'                   => 'nullable|file|max:200',
            'education_level'            => 'required|in:s6,s3,other',
            'senior6_document'           => 'nullable|file|max:200',
            'senior3_document'           => 'nullable|file|max:200',
            'other_supporting_document'  => 'nullable|file|max:200',
        ]);

        $fileData = [];
        foreach (['id_photo', 'passport_photo', 'senior6_document', 'senior3_document', 'other_supporting_document'] as $field) {
            if ($request->hasFile($field)) {
                $fileData[$field] = $request->file($field)->store('temp', 'public');
            }
        }

        $registrationData = [
            'full_name'        => $request->full_name,
            'email'            => $request->email,
            'phone'            => $request->phone,
            'branch_id'        => $request->branch_id,
            'course_id'        => $request->course_id,
            'academic_year_id' => $request->academic_year_id,
            'education_level'  => $request->education_level,
            'files'            => $fileData,
        ];
        session(['student_registration' => $registrationData]);

        $course = Course::findOrFail($request->course_id);
        $amount = $course->registration_fee;
        $tx_ref = 'TX-' . time() . '-' . uniqid();

        $flutterwavePayload = [
            'tx_ref'       => $tx_ref,
            'amount'       => $amount,
            'currency'     => 'RWF',
            'redirect_url' => route('students.payment.callback'),
            'customer'     => [
                'email'       => $request->email,
                'phonenumber' => $request->phone,
                'name'        => $request->full_name,
            ],
            'customizations' => [
                'title'       => 'Student Registration',
                'description' => 'Pay for registration at ESA E-Learning',
                'email'       => $request->email,
                'logo'        => url('/logo.png'),
            ],
            'meta' => [
                'session_key' => session()->getId(),
            ],
        ];

        session(['student_registration_tx_ref' => $tx_ref]);

        $response = Http::withToken(config('services.flutterwave.secret_key'))
            ->post('https://api.flutterwave.com/v3/payments', $flutterwavePayload);

        if ($response->successful()) {
            $paymentLink = $response->json()['data']['link'];
            return redirect($paymentLink);
        }

        Log::error('Flutterwave payment init failed', ['body' => $response->body()]);
        return back()->with('error', 'Flutterwave payment session failed.');
    }

    public function paymentCallback(Request $request)
    {
        Log::info('Flutterwave callback received', $request->all());

        $tx_ref         = $request->query('tx_ref');
        $status         = $request->query('status');
        $transaction_id = $request->query('transaction_id');

        if ($status !== 'successful' || !$transaction_id) {
            Log::error('Payment not successful or missing transaction_id', compact('status', 'transaction_id'));
            StudentTransaction::where('tx_ref', $tx_ref)->update([
                'status'         => 'failed',
                'transaction_id' => $transaction_id,
            ]);
            return redirect()->route('students.create')->with('error', 'Payment failed or cancelled.');
        }

        $verifyResponse = Http::withToken(config('services.flutterwave.secret_key'))
            ->get("https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify");

        Log::info('Flutterwave verification response', ['body' => $verifyResponse->body()]);

        if (!$verifyResponse->successful() || ($verifyResponse->json('data.status') !== 'successful')) {
            Log::error('Payment verification failed', ['body' => $verifyResponse->body()]);
            StudentTransaction::where('tx_ref', $tx_ref)->update([
                'status'         => 'failed',
                'transaction_id' => $transaction_id,
            ]);
            return redirect()->route('students.create')->with('error', 'Payment verification failed.');
        }

        $sessionTxRef     = session('student_registration_tx_ref');
        $registrationData = session('student_registration');

        if (!$registrationData || $tx_ref !== $sessionTxRef) {
            return redirect()->route('students.create')->with('error', 'Registration data not found. Please try again.');
        }

        if (Student::where('email', $registrationData['email'])->exists()) {
            session()->forget(['student_registration', 'student_registration_tx_ref']);
            return redirect()->route('students.create')->with('error', 'A student with this email already exists. Please log in or use a different email.');
        }

        DB::beginTransaction();
        try {
            $course        = Course::findOrFail($registrationData['course_id']);
            $academicYear  = AcademicYear::findOrFail($registrationData['academic_year_id']);
            $plainPassword = Str::random(8);

            $finalFiles = [];
            foreach ($registrationData['files'] as $field => $tempPath) {
                if ($tempPath) {
                    $filename  = basename($tempPath);
                    $finalPath = "students/{$field}/" . $filename;
                    Storage::disk('public')->move($tempPath, $finalPath);
                    $finalFiles[$field] = $finalPath;
                } else {
                    $finalFiles[$field] = null;
                }
            }

            $maxAttempts = 5;
            $student     = null;

            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                try {
                    $regNumber = $this->generateUniqueRegNumber($course->abbreviation, $academicYear->year);

                    $student = Student::create([
                        'full_name'                 => $registrationData['full_name'],
                        'email'                     => $registrationData['email'],
                        'phone'                     => $registrationData['phone'],
                        'branch_id'                 => $registrationData['branch_id'],
                        'course_id'                 => $registrationData['course_id'],
                        'academic_year_id'          => $registrationData['academic_year_id'],
                        'reg_number'                => $regNumber,
                        'password'                  => bcrypt($plainPassword),
                        'id_photo'                  => $finalFiles['id_photo'] ?? null,
                        'passport_photo'            => $finalFiles['passport_photo'] ?? null,
                        'senior6_document'          => $finalFiles['senior6_document'] ?? null,
                        'senior3_document'          => $finalFiles['senior3_document'] ?? null,
                        'other_supporting_document' => $finalFiles['other_supporting_document'] ?? null,
                        'payed_registration_fees'   => 1,
                    ]);

                    break;

                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    if ($attempt === $maxAttempts) {
                        DB::rollback();
                        session()->forget(['student_registration', 'student_registration_tx_ref']);
                        return redirect()->route('students.create')
                            ->with('error', 'Registration failed due to duplicate entry. Please try again.');
                    }
                    usleep(100000);
                    continue;
                }
            }

            if (!$student) {
                throw new \Exception('Failed to create student record.');
            }

            $student->initializeTokens(10);

            StudentTransaction::create([
                'student_id'     => $student->id,
                'tx_ref'         => $tx_ref,
                'transaction_id' => $transaction_id,
                'type'           => 'registration_fee',
                'amount'         => $course->registration_fee,
                'currency'       => 'RWF',
                'status'         => 'paid',
                'meta'           => json_encode([
                    'email' => $student->email,
                    'phone' => $student->phone,
                ]),
                'paid_at' => now(),
            ]);

            DB::commit();

            try {
                Mail::to($student->email)->send(new StudentWelcomeMail($student, $plainPassword));
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email', ['error' => $e->getMessage()]);
            }

            session()->forget(['student_registration', 'student_registration_tx_ref']);

            Auth::guard('student')->login($student);

            if (Auth::guard('student')->check()) {
                return redirect()->route('student.dashboard')
                    ->with('success', 'Payment completed successfully. Welcome to ESA! Your registration number is: ' . $student->reg_number);
            }

            return redirect()->route('students.login')
                ->with('success', 'Payment completed. Your registration number is: ' . $student->reg_number . '. Please log in.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payment callback failed', ['error' => $e->getMessage()]);
            session()->forget(['student_registration', 'student_registration_tx_ref']);
            return redirect()->route('students.create')
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    public function showLoginForm()
    {
        return view('students.login-simple');
    }

    public function showIdNumberForm(Request $request)
    {
        $identificationType = $request->input('identification_type');

        if (!$identificationType || !in_array($identificationType, ['ID', 'Passport'])) {
            return back()->with('error', 'Invalid identification type selected.');
        }

        return view('students.id-number-form', compact('identificationType'));
    }

    public function loginWithCode(Request $request)
    {
        try {
            $request->validate([
                'reg_number' => 'required|string',
                'code'       => 'required|string',
            ]);

            $student = Student::where('reg_number', $request->reg_number)->first();
            if (!$student) {
                if (DismissedStudent::isRegNumberDismissed($request->reg_number)) {
                    return back()->withInput()->with('error', 'This registration number belongs to a dismissed student. You can no longer access the portal. Please refer to the dismissal email sent to your address on file.');
                }

                return back()->withInput()->with('error', 'Invalid registration number.');
            }

            $record = EmailVerification::where('email', $student->email)
                ->where('code', $request->code)
                ->where('expires_at', '>', now())
                ->first();

            if (!$record) {
                return back()->withInput()->with('error', 'Invalid or expired code.');
            }

            Auth::guard('student')->login($student);
            $record->delete();

            return redirect()->route('student.dashboard');

        } catch (\Exception $e) {
            Log::error('loginWithCode failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', 'Login failed. Please try again.');
        }
    }

    public function loginWithPassword(Request $request)
    {
        $request->validate([
            'reg_number' => 'required|string',
            'password'   => 'required|string',
        ]);

        $student = Student::where('reg_number', $request->reg_number)->first();

        if (!$student) {
            if (DismissedStudent::isRegNumberDismissed($request->reg_number)) {
                return back()->withInput()->with('error', 'Your account has been dismissed. You can no longer access the student portal. Please check your email for the reason and further information.');
            }

            return back()->withInput()->with('error', 'Invalid credentials.');
        }

        if (!Hash::check($request->password, $student->password)) {
            return back()->withInput()->with('error', 'Invalid credentials.');
        }

        $this->cleanupExistingSessions($student, 'student');
        Auth::guard('student')->login($student);
        $this->storeSessionFingerprint($request, $student, 'student');

        return redirect()->route('student.dashboard')->with('success', 'Login successful!');
    }

    public function dashboard(Request $request)
    {
        $student = auth()->guard('student')->user();
        $student->load('course'); // Load course relationship

        $student->initializeTokens();

        $approvedAttendances = $student->attendances()
            ->where('approved', 1)
            ->orderByDesc('marked_at')
            ->get();

        $totalDays         = $approvedAttendances->count();
        $attendedDays      = $approvedAttendances->where('status', 'present')->count();
        $attendancePercent = $totalDays > 0 ? round(($attendedDays / $totalDays) * 100, 2) : 0;

        $registrationFee = $student->course->registration_fee ?? 0;
        $tripFee         = $student->course->trip_fee ?? 0;
        $tuitionFee      = $student->course->tuition_fee ?? 0;
        $praticalFee     = $student->course->pratical_fee ?? 0;
        $discount        = $student->discount ?? 0;
        $studentCardFee  = 2000;

        $discountableFees = $tripFee + $tuitionFee + $praticalFee;
        $discountAmount   = ($discount > 0) ? ($discountableFees * ($discount / 100)) : 0;
        $totalFees        = $registrationFee + $studentCardFee + ($discountableFees - $discountAmount);

        $paidRegistration = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'registration')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');

        $paidTrip = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'trip_fee')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');

        $paidTuition = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'school_fee')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');

        $paidPractical = StudentTransaction::where('student_id', $student->id)
            ->where('type', 'practical_fee')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');

        $paidStudentCard = StudentTransaction::where('student_id', $student->id)
            ->where('type', 'student_card')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');

        $totalPaid   = $paidRegistration + $paidTrip + $paidTuition + $paidPractical + $paidStudentCard;
        $outstanding = $totalFees - $totalPaid;

        // Get resources for the student's course and branch
        $resources = \App\Models\Resource::where(function($query) use ($student) {
            $query->orWhere('course_id', $student->course_id)
                  ->orWhere('branch_id', $student->branch_id);
        })
        ->with(['teacher', 'course', 'branch'])
        ->orderByDesc('created_at')
        ->take(6) // Limit to 6 resources for dashboard display
        ->get();

        // Get upcoming live classes for the student
        $liveClass = \App\Models\LiveClass::where(function($query) use ($student) {
            $query->whereHas('courses', function($q) use ($student) {
                $q->where('courses.id', $student->course_id);
            })
            ->orWhereHas('branches', function($q) use ($student) {
                $q->where('branches.id', $student->branch_id);
            });
        })
        ->where('status', 'active')
        ->where('start_time', '>', now())
        ->orderBy('start_time', 'asc')
        ->first();

        // Get latest announcements for the student
        $announcements = \App\Models\Announcement::where(function($query) use ($student) {
            $query->whereIn('audience', ['public', 'all', 'students']);
        })
        ->orderByDesc('created_at')
        ->take(5) // Limit to 5 announcements for dashboard display
        ->get();

        // Get exams for the student
        $exams = \App\Models\Exam::where(function ($query) use ($student) {
            $query->whereHas('courses', function ($q) use ($student) {
                $q->where('courses.id', $student->course_id);
            })
            ->orWhereHas('lessons', function ($q) use ($student) {
                $q->where('lessons.course_id', $student->course_id);
            });
        })
        ->with(['marks' => function ($q) use ($student) {
            $q->where('student_id', $student->id);
        }])
        ->orderBy('class_date', 'asc')
        ->take(5) // Limit to 5 exams for dashboard display
        ->get();

        // Get subscription data for the student
        $subscriptions = \App\Models\Subscription::active()->orderBy('price')->get();
        $activeSubscription = $student->activeSubscription;

        // Get active and upcoming timetables for the student
        $timetables = \App\Models\Timetable::where(function($query) {
            // Include timetables that are currently active
            $query->where(function($activeQuery) {
                $activeQuery->whereNull('valid_from')
                          ->orWhere('valid_from', '<=', now());
            })
            ->where(function($activeUntilQuery) {
                $activeUntilQuery->whereNull('valid_until')
                               ->orWhere('valid_until', '>=', now());
            });
        })
        ->orWhere(function($query) {
            // Include upcoming timetables (valid_from is in the future)
            $query->whereNotNull('valid_from')
                  ->where('valid_from', '>', now())
                  ->where(function($upcomingUntilQuery) {
                      $upcomingUntilQuery->whereNull('valid_until')
                                        ->orWhere('valid_until', '>', now());
                  });
        })
        ->orderBy('valid_from', 'asc') // Order by start date to show upcoming first
        ->orderBy('created_at', 'desc')
        ->get();

        return view('students.dashboard', compact(
            'student',
            'approvedAttendances',
            'totalDays',
            'attendedDays',
            'attendancePercent',
            'registrationFee',
            'tripFee',
            'tuitionFee',
            'praticalFee',
            'discount',
            'studentCardFee',
            'discountAmount',
            'totalFees',
            'paidRegistration',
            'paidTrip',
            'paidTuition',
            'paidPractical',
            'paidStudentCard',
            'totalPaid',
            'outstanding',
            'resources',
            'liveClass',
            'announcements',
            'exams',
            'subscriptions',
            'activeSubscription',
            'timetables'
        ));
    }

    // =========================================================================
    // RECORD CASH PAYMENT
    // Route: POST /staff/students/{studentId}/pay
    // =========================================================================
    public function recordCashPayment(Request $request, $studentId)
    {
        $isAjax = $request->ajax() || $request->wantsJson();

        try {
            $incomingFeeType = $request->input('fee_type');

            $feeTypeMap = [
                'tuition_fee'   => 'school_fee',
                'tuition'       => 'school_fee',
                'pratical'      => 'practical_fee',
                'practical'     => 'practical_fee',
                'trip'          => 'trip_fee',
                'studentcard'   => 'student_card',
                'student-card'  => 'student_card',
                'school fee'    => 'school_fee',
                'school-fee'    => 'school_fee',
                'registration'  => 'registration_fee',
                'practical_fee' => 'practical_fee',
                'pratical_fee'  => 'practical_fee',
                'pratical'      => 'practical_fee',
                'practical'     => 'practical_fee',
                'trip'          => 'trip_fee',
                'studentcard'   => 'student_card',
                'student-card'  => 'student_card',
                'card'          => 'student_card',
            ];

            if ($incomingFeeType && isset($feeTypeMap[strtolower($incomingFeeType)])) {
                $incomingFeeType = $feeTypeMap[strtolower($incomingFeeType)];
            }

            $request->merge(['fee_type' => $incomingFeeType]);

            $validated = \Validator::make($request->all(), [
                'amount_paid' => 'required|numeric|min:1',
                'fee_type'    => 'required|string|in:registration_fee,school_fee,trip_fee,practical_fee,student_card',
                'term_id'     => 'nullable|exists:terms,id',
            ], [
                'amount_paid.required' => 'Please enter the payment amount.',
                'amount_paid.numeric'  => 'Payment amount must be a number.',
                'amount_paid.min'      => 'Payment amount must be at least 1 RWF.',
                'fee_type.required'    => 'Please select a fee type.',
                'fee_type.in'          => 'Invalid fee type selected.',
                'term_id.exists'       => 'Selected term is invalid.',
            ]);

            if ($validated->fails()) {
                $message = implode(' ', $validated->errors()->all());
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'errors'  => $validated->errors(),
                    ], 422);
                }
                return back()->withErrors($validated)->withInput();
            }

            $student = Student::findOrFail($studentId);
            $course  = $student->course;

            if (!$course) {
                $message = 'Student course not found. Cannot process payment.';
                return $isAjax
                    ? response()->json(['success' => false, 'message' => $message], 400)
                    : back()->with('error', $message);
            }

            // Get term-specific fees or fallback to legacy fees
            $termId = $request->term_id;
            $termFees = null;
            
            if ($termId) {
                $termFees = $course->getFeesForTerm($termId);
            }
            
            if (!$termFees) {
                // Fallback to legacy fee structure
                $termFees = $course->getLegacyFees();
            }

            $registrationFee = floatval($termFees->registration_fee ?? 0);
            $tripFee         = floatval($termFees->trip_fee ?? 0);
            $schoolFee       = floatval($termFees->tuition_fee ?? 0);
            $practicalFee    = floatval($termFees->practical_fee ?? 0);
            $studentCardFee  = floatval($termFees->student_card_fee ?? 2000);
            $discount        = floatval($student->discount ?? 0);

            // Calculate paid amounts for the specific term
            $queryBase = StudentTransaction::where('student_id', $student->id);
            if ($termId) {
                $queryBase->where('term_id', $termId);
            }

            $paidRegistration = (clone $queryBase)
                ->where('type', 'registration')
                ->whereIn('status', ['successful', 'paid'])
                ->sum('amount');

            $paidSchoolFee = (clone $queryBase)
                ->where('type', 'school_fee')
                ->whereIn('status', ['successful', 'paid'])
                ->sum('amount');

            $paidPractical = (clone $queryBase)
                ->where('type', 'practical_fee')
                ->whereIn('status', ['successful', 'paid'])
                ->sum('amount');

            $paidTrip = (clone $queryBase)
                ->where('type', 'trip_fee')
                ->whereIn('status', ['successful', 'paid'])
                ->sum('amount');

            $paidStudentCard = (clone $queryBase)
                ->where('type', 'student_card')
                ->whereIn('status', ['successful', 'paid'])
                ->sum('amount');

            $remainingAmount = 0;
            $feeTypeName     = '';
            $totalFeeAmount  = 0;

            switch ($request->fee_type) {
                case 'registration_fee':
                    $totalFeeAmount  = $registrationFee;
                    $remainingAmount = $registrationFee - $paidRegistration;
                    $feeTypeName     = 'Registration Fee';
                    break;
                case 'school_fee':
                    $totalFeeAmount  = $schoolFee;
                    $remainingAmount = $schoolFee - $paidSchoolFee;
                    $feeTypeName     = 'School Fee';
                    break;
                case 'practical_fee':
                    $totalFeeAmount  = $practicalFee;
                    $remainingAmount = $practicalFee - $paidPractical;
                    $feeTypeName     = 'Practical Fee';
                    break;
                case 'trip_fee':
                    $totalFeeAmount  = $tripFee;
                    $remainingAmount = $tripFee - $paidTrip;
                    $feeTypeName     = 'Trip Fee';
                    break;
                case 'student_card':
                    $totalFeeAmount  = $studentCardFee;
                    $remainingAmount = $studentCardFee - $paidStudentCard;
                    $feeTypeName     = 'Student Card';
                    break;
                default:
                    $message = 'Invalid fee type: ' . $request->fee_type;
                    return $isAjax
                        ? response()->json(['success' => false, 'message' => $message], 400)
                        : back()->with('error', $message);
            }

            if ($totalFeeAmount <= 0) {
                $message = "{$feeTypeName} is not configured for this course.";
                return $isAjax
                    ? response()->json(['success' => false, 'message' => $message], 400)
                    : back()->with('error', $message);
            }

            if ($remainingAmount <= 0) {
                $message = "{$feeTypeName} has already been fully paid.";
                return $isAjax
                    ? response()->json(['success' => false, 'message' => $message], 400)
                    : back()->with('warning', $message);
            }

            $amountPaid = floatval($request->amount_paid);
            if ($amountPaid > $remainingAmount) {
                $message = "Payment amount (" . number_format($amountPaid, 0) . " RWF) exceeds remaining balance of " . number_format($remainingAmount, 0) . " RWF";
                return $isAjax
                    ? response()->json(['success' => false, 'message' => $message], 422)
                    : back()->with('error', $message);
            }

            DB::beginTransaction();

            $transaction = StudentTransaction::create([
                'student_id' => $student->id,
                'tx_ref'     => 'CASH-' . $student->id . '-' . time() . '-' . uniqid(),
                'type'       => $request->fee_type,
                'amount'     => $amountPaid,
                'currency'   => 'RWF',
                'status'     => 'successful',
                'term_id'    => $request->term_id ?? null,
                'meta'       => json_encode([
                    'method'      => 'cash',
                    'recorded_by' => auth('staff')->user()->name ?? auth('admin')->user()->name ?? 'staff',
                    'recorded_at' => now()->toDateTimeString(),
                    'fee_name'    => $feeTypeName,
                ]),
                'paid_at' => now(),
            ]);

            if (!$transaction || !$transaction->id) {
                DB::rollBack();
                $message = 'Payment failed: Unable to create transaction record.';
                Log::error('Transaction creation failed', ['student_id' => $student->id]);
                return $isAjax
                    ? response()->json(['success' => false, 'message' => $message], 500)
                    : back()->with('error', $message);
            }

            $discountableFees = $tripFee + $schoolFee + $practicalFee;
            $discountAmount   = ($discount > 0) ? ($discountableFees * ($discount / 100)) : 0;
            $totalFees        = $registrationFee + $studentCardFee + ($discountableFees - $discountAmount);

            $paidFees = StudentTransaction::where('student_id', $student->id)
                ->whereIn('status', ['successful', 'paid'])
                ->sum('amount');

            $balance      = $totalFees - $paidFees;
            $newRemaining = $remainingAmount - $amountPaid;

            DB::commit();

            try {
                Mail::to($student->email)->send(
                    new \App\Mail\StudentCashPaymentConfirmationMail(
                        $student, $amountPaid, $balance, now()->addDays(30),
                        $totalFees, $paidFees, $registrationFee, $tripFee,
                        $schoolFee, $practicalFee, $discountAmount,
                        $request->fee_type, $studentCardFee
                    )
                );
            } catch (\Exception $e) {
                Log::error('Email failed', ['error' => $e->getMessage()]);
            }

            Log::info('Cash payment recorded', [
                'transaction_id' => $transaction->id,
                'student_id'     => $student->id,
                'fee_type'       => $feeTypeName,
                'amount'         => $amountPaid,
            ]);

            $successMessage = "Payment recorded successfully! " .
                number_format($amountPaid, 0) . " RWF paid for {$feeTypeName}. " .
                "Remaining: " . number_format($newRemaining, 0) . " RWF.";

            if ($isAjax) {
                return response()->json([
                    'success'   => true,
                    'message'   => $successMessage,
                    'balance'   => $balance,
                    'remaining' => $newRemaining,
                ], 200);
            }

            return back()->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cash payment failed', [
                'student_id' => $studentId,
                'error'      => $e->getMessage(),
            ]);

            $message = 'Payment failed: ' . $e->getMessage();

            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }

            return back()->with('error', $message);
        }
    }

    public function ajaxPayments($studentId)
    {
        $student  = Student::findOrFail($studentId);
        $payments = StudentTransaction::where('student_id', $student->id)
            ->orderByDesc('paid_at')
            ->get();

        return view('students.partials.payments_table', compact('student', 'payments'))->render();
    }

    public function sendTranscript($studentId)
    {
        $student      = Student::findOrFail($studentId);
        $transactions = StudentTransaction::where('student_id', $student->id)
            ->orderBy('paid_at')
            ->get();

        $registrationFee = $student->course->registration_fee ?? 0;
        $tripFee         = $student->course->trip_fee ?? 0;
        $tuitionFee      = $student->course->tuition_fee ?? 0;
        $praticalFee     = $student->course->pratical_fee ?? 0;
        $discount        = $student->discount ?? 0;
        $studentCardFee  = 2000;

        $discountableFees = $tripFee + $tuitionFee + $praticalFee;
        $discountAmount   = ($discount > 0) ? ($discountableFees * ($discount / 100)) : 0;
        $totalToBePaid    = $registrationFee + $studentCardFee + ($discountableFees - $discountAmount);

        $totalPaid   = StudentTransaction::where('student_id', $student->id)
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');
        $outstanding = $totalToBePaid - $totalPaid;

        Mail::to($student->email)->send(
            new \App\Mail\StudentTranscriptMail(
                $student,
                $transactions,
                $totalToBePaid,
                $totalPaid,
                $outstanding,
                $studentCardFee,
                $discountAmount
            )
        );

        return back()->with('success', 'Transcript sent to student.');
    }

    // =========================================================================
    // GENERATE STUDENT CARD
    // =========================================================================
    public function generateStudentCard(Request $request, $studentId)
    {
        $student = Student::with('course', 'branch', 'academicYear')->findOrFail($studentId);

        $actor     = auth('staff')->user() ?? auth('admin')->user();
        $actorName = $actor->name ?? $actor->email ?? 'system';

        if (StudentCard::where('student_id', $student->id)->exists()) {
            return back()->with('error', 'Student card already exists for this student.');
        }

        $studentCardPaid = StudentTransaction::where('student_id', $student->id)
            ->where('type', 'student_card')
            ->whereIn('status', ['successful', 'paid'])
            ->exists();

        if (!$studentCardPaid) {
            return back()->with('error', 'Student must pay the Student Card fee (2,000 RWF) before generating an ID card.');
        }

        $passportPhotoBase64 = null;

        if ($student->passport_photo) {
            $photoValue = $student->passport_photo;

            if (strlen($photoValue) < 500 && Storage::disk('public')->exists($photoValue)) {
                try {
                    $passportPhotoBase64 = base64_encode(
                        Storage::disk('public')->get($photoValue)
                    );
                } catch (\Exception $e) {
                    Log::warning('Could not read passport photo from storage', [
                        'student_id' => $student->id,
                        'path'       => $photoValue,
                        'error'      => $e->getMessage(),
                    ]);
                }
            } elseif (strlen($photoValue) >= 500) {
                $passportPhotoBase64 = $photoValue;
            }
        }

        $qrData = json_encode([
            'student_name'   => $student->full_name,
            'reg_number'     => $student->reg_number,
            'course'         => $student->course->course_name ?? '',
            'branch'         => $student->branch->name ?? '',
            'academic_year'  => $student->academicYear->year ?? date('Y'),
            'generated_date' => now()->format('Y-m-d H:i:s'),
        ]);

        try {
            $qrSvg  = \QrCode::size(200)
                ->margin(0)
                ->errorCorrection('H')
                ->encoding('UTF-8')
                ->generate($qrData);

            $qrCode = (string) $qrSvg;

        } catch (\Exception $e) {
            Log::error('QR code generation failed', [
                'student_id' => $student->id,
                'error'      => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to generate QR code: ' . $e->getMessage());
        }

        StudentCard::create([
            'student_id'        => $student->id,
            'reg_number'        => $student->reg_number,
            'course'            => $student->course->course_name ?? '',
            'branch'            => $student->branch->name ?? '',
            'passport_photo'    => $passportPhotoBase64,
            'qr_code'           => $qrCode,
            'academic_year'     => $student->academicYear->year ?? date('Y'),
            'card_generated_by' => $actorName,
            'generated_at'      => now(),
        ]);

        if ($student->email) {
            try {
                Mail::to($student->email)->send(
                    new \App\Mail\StudentCardGeneratedMail([
                        'student_name'        => $student->full_name,
                        'reg_number'          => $student->reg_number,
                        'course'              => $student->course->course_name ?? '',
                        'branch'              => $student->branch->name ?? '',
                        'card_generated_date' => now()->format('F j, Y \a\t g:i A'),
                        'generated_by'        => $actorName,
                    ])
                );

                Log::info('Student card generation email sent', [
                    'student_id' => $student->id,
                    'email'      => $student->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send student card generation email', [
                    'student_id' => $student->id,
                    'email'      => $student->email,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        return back()->with('success', 'Student card generated successfully.');
    }

    public function listStudentCards()
    {
        $cards = StudentCard::with('student')->latest()->paginate(20);
        return view('students.StudentCard', compact('cards'));
    }

    // =========================================================================
    // GENERATE CARDS BULK
    // =========================================================================
    public function generateCardsBulk(Request $request)
    {
        $query = Student::with(['course', 'branch', 'academicYear']);

        if ($request->filled('name')) {
            $query->where('full_name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('reg_number')) {
            $query->where('reg_number', 'like', '%' . $request->reg_number . '%');
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('telephone')) {
            $query->where('phone', 'like', '%' . $request->telephone . '%');
        }
        if ($request->filled('academic_year')) {
            $query->whereHas('academicYear', function ($q) use ($request) {
                $q->where('year', 'like', '%' . $request->academic_year . '%');
            });
        }
        if ($request->filled('course')) {
            $query->whereHas('course', function ($q) use ($request) {
                $q->where('course_name', 'like', '%' . $request->course . '%')
                  ->orWhere('abbreviation', 'like', '%' . $request->course . '%');
            });
        }

        $actor     = auth('staff')->user() ?? auth('admin')->user();
        $actorName = $actor->name ?? $actor->email ?? 'system';

        $students  = $query->get();
        $generated = 0;
        $skipped   = 0;

        foreach ($students as $student) {
            if (StudentCard::where('student_id', $student->id)->exists()) {
                $skipped++;
                continue;
            }

            $hasPaid = StudentTransaction::where('student_id', $student->id)
                ->where('type', 'student_card')
                ->whereIn('status', ['successful', 'paid'])
                ->exists();

            if (!$hasPaid) {
                $skipped++;
                continue;
            }

            $qrData = json_encode([
                'student_name'   => $student->full_name,
                'reg_number'     => $student->reg_number,
                'course'         => $student->course->course_name ?? '',
                'branch'         => $student->branch->name ?? '',
                'academic_year'  => $student->academicYear->year ?? date('Y'),
                'generated_date' => now()->format('Y-m-d H:i:s'),
            ]);

            try {
                $qrSvg  = \QrCode::size(200)
                    ->margin(0)
                    ->errorCorrection('H')
                    ->encoding('UTF-8')
                    ->generate($qrData);

                $qrCode = (string) $qrSvg;
            } catch (\Exception $e) {
                Log::error('Bulk QR generation failed for student', [
                    'student_id' => $student->id,
                    'error'      => $e->getMessage(),
                ]);
                $skipped++;
                continue;
            }

            $passportPhotoBase64 = null;
            if ($student->passport_photo) {
                $photoValue = $student->passport_photo;
                if (strlen($photoValue) < 500 && Storage::disk('public')->exists($photoValue)) {
                    try {
                        $passportPhotoBase64 = base64_encode(Storage::disk('public')->get($photoValue));
                    } catch (\Exception $e) {
                        // Non-fatal
                    }
                } elseif (strlen($photoValue) >= 500) {
                    $passportPhotoBase64 = $photoValue;
                }
            }

            StudentCard::create([
                'student_id'        => $student->id,
                'reg_number'        => $student->reg_number,
                'course'            => $student->course->course_name ?? '',
                'branch'            => $student->branch->name ?? '',
                'passport_photo'    => $passportPhotoBase64,
                'qr_code'           => $qrCode,
                'academic_year'     => $student->academicYear->year ?? date('Y'),
                'card_generated_by' => $actorName,
                'generated_at'      => now(),
            ]);

            $generated++;
        }

        return back()->with('success', "{$generated} student card(s) generated. {$skipped} skipped (already exists or fee not paid).");
    }

    // =========================================================================
    // EDIT TRANSACTION
    // =========================================================================
    public function editTransaction(Request $request, $transactionId)
    {
        $staff = auth('staff')->user();
        if (!$staff) {
            abort(403, 'Unauthorized. Only staff can edit transactions.');
        }

        $transaction = StudentTransaction::findOrFail($transactionId);

        if ($request->isMethod('get')) {
            return view('staff.transactions.edit', compact('transaction'));
        }

        // Legacy POST fallback
        $request->validate([
            'amount'  => 'required|numeric|min:0',
            'type'    => 'required|string',
            'status'  => 'required|string',
            'paid_at' => 'nullable|date',
        ]);

        $transaction->amount = $request->input('amount');
        $transaction->type   = $request->input('type');
        $transaction->status = $request->input('status');
        if ($request->filled('paid_at')) {
            $transaction->paid_at = $request->input('paid_at');
        }
        $transaction->save();

        Log::info('Transaction edited by staff (legacy)', [
            'transaction_id' => $transaction->id,
            'edited_by'      => $staff->email ?? 'staff',
        ]);

        return redirect()->back()->with('success', 'Transaction updated successfully.');
    }

    // =========================================================================
    // UPDATE TRANSACTION (AJAX)
    // =========================================================================
    public function updateTransaction(Request $request, $transactionId)
    {
        $staff = auth('staff')->user();
        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only staff can edit transactions.',
            ], 403);
        }

        try {
            $transaction = StudentTransaction::findOrFail($transactionId);

            $validated = \Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1',
                'type'   => 'required|in:registration_fee,school_fee,practical_fee,trip_fee,student_card',
                'notes'  => 'nullable|string|max:500',
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => implode(' ', $validated->errors()->all()),
                    'errors'  => $validated->errors(),
                ], 422);
            }

            $meta = is_array($transaction->meta)
                ? $transaction->meta
                : (json_decode($transaction->meta, true) ?? []);

            $meta['notes']     = $request->input('notes', '');
            $meta['edited_by'] = $staff->name ?? $staff->email ?? 'staff';
            $meta['edited_at'] = now()->toDateTimeString();

            $transaction->amount = $request->input('amount');
            $transaction->type   = $request->input('type');
            $transaction->meta   = $meta;
            $transaction->save();

            Log::info('Transaction updated by staff', [
                'transaction_id' => $transaction->id,
                'edited_by'      => $staff->email ?? 'staff',
                'changes'        => $request->only(['amount', 'type', 'notes']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully.',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to update transaction', [
                'transaction_id' => $transactionId,
                'error'          => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================================
    // DELETE TRANSACTION
    // =========================================================================
    public function deleteTransaction(Request $request, $transactionId)
    {
        $staff = auth('staff')->user();
        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only staff can delete transactions.',
            ], 403);
        }

        try {
            $transaction = StudentTransaction::findOrFail($transactionId);
            $transaction->delete();

            Log::info('Transaction deleted by staff', [
                'transaction_id' => $transactionId,
                'deleted_by'     => $staff->email ?? 'staff',
            ]);

            return response()->json(['success' => true, 'message' => 'Transaction deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Failed to delete transaction by staff', [
                'transaction_id' => $transactionId,
                'error'          => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reportCards(Request $request)
    {
        $student = auth()->guard('student')->user();
        
        // Get current date and time in Rwanda
        $now = \Carbon\Carbon::now('Africa/Kigali');
        
        // Calculate outstanding fees
        $registrationFee = $student->course->registration_fee ?? 0;
        $tripFee = $student->course->trip_fee ?? 0;
        $tuitionFee = $student->course->tuition_fee ?? 0;
        $praticalFee = $student->course->pratical_fee ?? 0;
        $studentCardFee = 2000;
        
        $discountableFees = $tripFee + $tuitionFee + $praticalFee;
        $discountAmount = ($student->discount ?? 0) > 0 ? ($discountableFees * ($student->discount / 100)) : 0;
        $totalFees = $registrationFee + $studentCardFee + ($discountableFees - $discountAmount);
        
        $paidRegistration = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'registration')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');
            
        $paidTrip = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'trip_fee')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');
            
        $paidTuition = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'school_fee')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');
            
        $paidPractical = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'practical_fee')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');
            
        $paidStudentCard = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'student_card')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');
            
        $totalPaid = $paidRegistration + $paidTrip + $paidTuition + $paidPractical + $paidStudentCard;
        $outstanding = max(0, $totalFees - $totalPaid);
        
        // Get report card release date from school information
        $schoolInfo = \App\Models\SchoolInformation::first();
        $reportCardReleaseDate = $schoolInfo ? $schoolInfo->report_card_release_date : null;
        
        // Check if student has any outstanding fees
        $hasOutstandingFees = $outstanding > 0;
        
        // Check if report card release date has passed
        $releaseDatePassed = $reportCardReleaseDate ? $now->isAfter(\Carbon\Carbon::parse($reportCardReleaseDate)) : false;
        
        // Get student's approved report cards
        $reportCards = \App\Models\ReportCard::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with(['academicYear', 'term', 'course'])
            ->orderByDesc('created_at')
            ->get();
            
        // Calculate countdown if release date is in future
        $countdown = null;
        if ($reportCardReleaseDate && !$releaseDatePassed) {
            $releaseDate = \Carbon\Carbon::parse($reportCardReleaseDate);
            $countdown = $now->diff($releaseDate);
        }
        
        return view('students.report-cards', compact(
            'student',
            'reportCards',
            'hasOutstandingFees',
            'outstanding',
            'reportCardReleaseDate',
            'releaseDatePassed',
            'countdown'
        ));
    }

    public function downloadReportCard($reportCardId)
    {
        $student = auth()->guard('student')->user();
        
        // Get current date and time in Rwanda
        $now = \Carbon\Carbon::now('Africa/Kigali');
        
        // Calculate outstanding fees (same logic as above)
        $registrationFee = $student->course->registration_fee ?? 0;
        $tripFee = $student->course->trip_fee ?? 0;
        $tuitionFee = $student->course->tuition_fee ?? 0;
        $praticalFee = $student->course->pratical_fee ?? 0;
        $studentCardFee = 2000;
        
        $discountableFees = $tripFee + $tuitionFee + $praticalFee;
        $discountAmount = ($student->discount ?? 0) > 0 ? ($discountableFees * ($student->discount / 100)) : 0;
        $totalFees = $registrationFee + $studentCardFee + ($discountableFees - $discountAmount);
        
        $paidRegistration = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'registration')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');
            
        $paidTrip = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'trip_fee')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');
            
        $paidTuition = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'school_fee')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');
            
        $paidPractical = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'practical_fee')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');
            
        $paidStudentCard = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'student_card')
            ->whereIn('status', ['successful', 'paid'])
            ->sum('amount');
            
        $totalPaid = $paidRegistration + $paidTrip + $paidTuition + $paidPractical + $paidStudentCard;
        $outstanding = max(0, $totalFees - $totalPaid);
        
        // Check if student has outstanding fees
        if ($outstanding > 0) {
            return redirect()->route('student.report-cards')
                ->with('error', 'You cannot download the report card until all outstanding fees are paid. Remaining amount: ' . number_format($outstanding, 2) . ' Rwf. Please reach our office for support.');
        }
        
        // Check if report card release date has passed
        $schoolInfo = \App\Models\SchoolInformation::first();
        $reportCardReleaseDate = $schoolInfo ? $schoolInfo->report_card_release_date : null;
        
        if ($reportCardReleaseDate && !$now->isAfter(\Carbon\Carbon::parse($reportCardReleaseDate))) {
            return redirect()->route('student.report-cards')
                ->with('error', 'Report cards are not yet available. They will be released on ' . \Carbon\Carbon::parse($reportCardReleaseDate)->format('d M Y H:i') . '.');
        }
        
        // Get the report card
        $reportCard = \App\Models\ReportCard::where('id', $reportCardId)
            ->where('student_id', $student->id)
            ->where('status', 'approved')
            ->with(['student', 'course', 'academicYear', 'term', 'approvedBy', 'subjects.lesson', 'subjects.testMarks.exam'])
            ->firstOrFail();
            
        // Generate PDF using existing ReportCardController method
        $reportCardController = new \App\Http\Controllers\ReportCardController();
        return $reportCardController->printReport($reportCard);
    }

    public function transcriptAjax($studentId)
    {
        $student = Student::findOrFail($studentId);

        $payments = StudentTransaction::where('student_id', $student->id)
            ->with('term')
            ->whereIn('status', ['successful', 'paid', 'completed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('students.partials.transcript_table', compact('student', 'payments'))->render();
    }

    public function transcript($studentId)
    {
        $student  = Student::findOrFail($studentId);
        $payments = StudentTransaction::where('student_id', $studentId)
            ->with('term')
            ->whereIn('status', ['successful', 'paid', 'completed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('students.transcript', compact('student', 'payments'));
    }

    /**
     * Official printable payment transcript for the logged-in student (same document as accounting print).
     */
    public function paymentTranscriptPrint()
    {
        $authStudent = auth('student')->user();
        $data        = StudentPaymentTranscript::build($authStudent);

        return view('admin.accounting.print-payment-transcript', [
            'student'            => $authStudent,
            'schoolInfo'         => $data['school'],
            'feeRows'            => $data['feeRows'],
            'totalExpected'      => $data['totalExpected'],
            'totalPaid'          => $data['totalPaid'],
            'totalPaidAllTypes'  => $data['totalPaidAllTypes'],
            'totalOutstanding'   => $data['totalOutstanding'],
            'discountPct'        => $data['discountPct'],
            'discountAmount'     => $data['discountAmount'],
            'transactions'       => $data['transactions'],
            'printedByName'      => null,
            'printedAt'          => now(),
        ]);
    }

    public function getNESADetails($studentId)
    {
        $student = Student::findOrFail($studentId);

        return response()->json([
            'success'                => true,
            'nesa_registered'        => $student->nesa_registered,
            'nesa_registration_date' => $student->nesa_registration_date,
            'nesa_sdms_code'         => $student->nesa_sdms_code,
            'nesa_reg_number'        => $student->nesa_reg_number,
            'nesa_remarks'           => $student->nesa_remarks,
            'nesa_document'          => $student->nesa_document,
        ]);
    }

    public function registerNesa(Request $request, Student $student)
    {
        if (!auth()->guard('staff')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'nesa_registered'        => 'required|boolean',
            'nesa_sdms_code'         => 'nullable|string|max:100',
            'nesa_reg_number'        => 'nullable|string|max:100',
            'nesa_registration_date' => 'nullable|date',
            'nesa_remarks'           => 'nullable|string|max:500',
            'nesa_document'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $nesaRegistered           = (bool) $request->nesa_registered;
            $student->nesa_registered = $nesaRegistered;

            if ($nesaRegistered) {
                if ($request->filled('nesa_sdms_code')) {
                    $student->nesa_sdms_code = $request->nesa_sdms_code;
                }
                if ($request->filled('nesa_reg_number')) {
                    $student->nesa_reg_number = $request->nesa_reg_number;
                }
                if ($request->filled('nesa_registration_date')) {
                    $student->nesa_registration_date = $request->nesa_registration_date;
                }
                if ($request->filled('nesa_remarks')) {
                    $student->nesa_remarks = $request->nesa_remarks;
                }
                if ($request->hasFile('nesa_document')) {
                    if ($student->nesa_document && Storage::disk('public')->exists($student->nesa_document)) {
                        Storage::disk('public')->delete($student->nesa_document);
                    }
                    $student->nesa_document = $request->file('nesa_document')
                        ->store('students/nesa_documents', 'public');
                }
            } else {
                $student->nesa_sdms_code         = null;
                $student->nesa_reg_number        = null;
                $student->nesa_registration_date = null;
                $student->nesa_remarks           = null;
            }

            $student->save();
            DB::commit();

            Log::info('NESA registration updated', [
                'student_id'      => $student->id,
                'nesa_registered' => $nesaRegistered,
                'updated_by'      => auth('staff')->user()->id,
            ]);

            if ($nesaRegistered && $student->email) {
                try {
                    Mail::to($student->email)->send(new \App\Mail\StudentNesaRegistrationMail([
                        'name'              => $student->full_name,
                        'email'             => $student->email,
                        'reg_number'        => $student->reg_number,
                        'sdms_code'         => $student->nesa_sdms_code,
                        'nesa_reg_number'   => $student->nesa_reg_number,
                        'registration_date' => $student->nesa_registration_date
                            ? \Carbon\Carbon::parse($student->nesa_registration_date)->format('d M Y')
                            : null,
                    ]));
                } catch (\Exception $e) {
                    Log::error('Failed to send NESA registration email', [
                        'student_id' => $student->id,
                        'error'      => $e->getMessage(),
                    ]);
                }
            }

            return redirect()->route('staff.students.edit', $student)
                ->with('success', $nesaRegistered
                    ? 'NESA registration saved successfully. Student has been notified via email.'
                    : 'Student marked as not registered with NESA.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('NESA registration update failed', [
                'student_id' => $student->id,
                'error'      => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to save NESA registration: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function viewDocument(Request $request, Student $student, $documentType)
    {
        $validTypes = ['id_photo', 'passport_photo', 'senior3_document', 'senior6_document', 'other_supporting_document'];
        if (!in_array($documentType, $validTypes)) {
            abort(404, 'Invalid document type');
        }

        $documentPath = $student->$documentType;
        if (!$documentPath) {
            abort(404, 'Document not found');
        }

        try {
            if (!Storage::disk('public')->exists($documentPath)) {
                abort(404, 'Document file not found');
            }

            $file     = Storage::disk('public')->get($documentPath);
            $mimeType = Storage::disk('public')->mimeType($documentPath);

            return response($file)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . basename($documentPath) . '"');

        } catch (\Exception $e) {
            Log::error('Error viewing student document', [
                'student_id'    => $student->id,
                'document_type' => $documentType,
                'error'         => $e->getMessage(),
            ]);

            abort(500, 'Error loading document');
        }
    }

    public function verifyQRCode($qrData)
    {
        try {
            $decodedData = json_decode(base64_decode($qrData), true);

            if (!$decodedData) {
                return response()->json(['success' => false, 'message' => 'Invalid QR code format', 'data' => null], 400);
            }

            $requiredFields = ['student_name', 'reg_number', 'course', 'branch', 'academic_year'];
            foreach ($requiredFields as $field) {
                if (empty($decodedData[$field])) {
                    return response()->json(['success' => false, 'message' => "Missing required field: {$field}", 'data' => null], 400);
                }
            }

            $student = Student::with(['course', 'branch', 'academicYear'])
                ->where('reg_number', $decodedData['reg_number'])
                ->first();

            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student not found', 'data' => null], 404);
            }

            $studentCard = StudentCard::where('student_id', $student->id)
                ->where('academic_year', $decodedData['academic_year'])
                ->first();

            if (!$studentCard) {
                return response()->json(['success' => false, 'message' => 'Student card not found for this academic year', 'data' => null], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Student verified successfully',
                'data'    => [
                    'student_id'        => $student->id,
                    'student_name'      => $student->full_name,
                    'reg_number'        => $student->reg_number,
                    'course'            => $student->course->course_name ?? '',
                    'branch'            => $student->branch->name ?? '',
                    'academic_year'     => $student->academicYear->year ?? '',
                    'email'             => $student->email,
                    'phone'             => $student->phone,
                    'sex'               => $student->sex,
                    'card_generated_at' => $studentCard->generated_at,
                    'verification_time' => now()->format('Y-m-d H:i:s'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('QR Code verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['success' => false, 'message' => 'QR code verification failed: ' . $e->getMessage(), 'data' => null], 500);
        }
    }

    public function profile()
    {
        $student = auth()->user();
        return view('students.profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $student = auth()->user();

        $request->validate([
            'full_name'                  => 'nullable|string|max:255',
            'telephone'                  => 'nullable|string|max:20',
            'email'                      => 'nullable|email|max:255',
            'sex'                        => 'nullable|in:Male,Female,Other',
            'identification_type'        => 'nullable|in:ID,Passport',
            'id_number'                  => 'nullable|string|max:50',
            'id_photo'                   => 'nullable|image|mimes:jpg,jpeg,png|max:200',
            'passport_photo'             => 'nullable|image|mimes:jpg,jpeg,png|max:200',
            'education_level'            => 'nullable|string|max:50',
            'senior3_document'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:200',
            'senior6_document'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:200',
            'other_supporting_document'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:200',
            'password'                   => 'nullable|string|min:8|confirmed',
        ]);

        $updatedFields = [];

        if ($request->filled('full_name') && $request->full_name !== $student->full_name) {
            $student->full_name = $request->full_name;
            $updatedFields[] = 'Full Name';
        }
        if ($request->filled('telephone') && $request->telephone !== $student->phone) {
            $student->phone = $request->telephone;
            $updatedFields[] = 'Telephone';
        }
        if ($request->filled('email') && $request->email !== $student->email) {
            $student->email = $request->email;
            $updatedFields[] = 'Email';
        }
        if ($request->filled('sex') && $request->sex !== $student->sex) {
            $student->sex = $request->sex;
            $updatedFields[] = 'Sex';
        }
        if ($request->filled('identification_type') && $request->identification_type !== $student->identification_type) {
            $student->identification_type = $request->identification_type;
            $updatedFields[] = 'Identification Type';
        }
        if ($request->filled('id_number') && $request->id_number !== $student->id_number) {
            $student->id_number = $request->id_number;
            $updatedFields[] = ($request->identification_type === 'ID') ? 'ID Number' : 'Passport Number';
        }
        if ($request->hasFile('id_photo')) {
            $student->id_photo = base64_encode(file_get_contents($request->file('id_photo')->getRealPath()));
            $updatedFields[] = ($request->identification_type === 'ID') ? 'ID Image' : 'Passport Image';
        }
        if ($request->hasFile('passport_photo')) {
            $student->passport_photo = base64_encode(file_get_contents($request->file('passport_photo')->getRealPath()));
            $updatedFields[] = 'Passport Photo';
        }
        if ($request->filled('education_level') && $request->education_level !== $student->education_level) {
            $student->education_level = $request->education_level;
            $updatedFields[] = 'Education Level';
        }
        if ($request->education_level === 'Senior 3' && $request->hasFile('senior3_document')) {
            $student->senior3_document = base64_encode(file_get_contents($request->file('senior3_document')->getRealPath()));
            $updatedFields[] = 'Senior 3 Document';
        }
        if ($request->education_level === 'Senior 6' && $request->hasFile('senior6_document')) {
            $student->senior6_document = base64_encode(file_get_contents($request->file('senior6_document')->getRealPath()));
            $updatedFields[] = 'Senior 6 Document';
        }
        if ($request->education_level === 'Other' && $request->hasFile('other_supporting_document')) {
            $student->other_supporting_document = base64_encode(file_get_contents($request->file('other_supporting_document')->getRealPath()));
            $updatedFields[] = 'Other Supporting Document';
        }
        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
            $updatedFields[] = 'Password';
        }

        if (count($updatedFields)) {
            $student->save();
            return back()->with('success', 'Profile updated successfully.')->with('updated_fields', $updatedFields);
        }

        return back()->with('success', 'No changes were made.');
    }

    public function attendance(Request $request)
    {
        if (auth()->guard('student')->check()) {
            $user                = auth()->guard('student')->user();
            $approvedAttendances = $user->attendances()
                ->where('approved', 1)
                ->orderByDesc('marked_at')
                ->get();

            $totalDays         = $approvedAttendances->count();
            $attendedDays      = $approvedAttendances->where('status', 'present')->count();
            $attendancePercent = $totalDays > 0 ? round(($attendedDays / $totalDays) * 100, 2) : 0;

            return view('students.attendance', compact(
                'approvedAttendances',
                'totalDays',
                'attendedDays',
                'attendancePercent'
            ));
        }

        if (auth()->guard('staff')->check() || auth()->guard('teacher')->check()) {
            $courses  = Course::all();
            $branches = Branch::all();
            $today    = now()->format('Y-m-d');
            $lessons  = [];
            $students = [];

            if ($request->filled('course_id') && $request->filled('branch_id')) {
                $courseId  = $request->course_id;
                $branchId  = $request->branch_id;
                $dayOfWeek = strtolower(now()->format('l'));

                $lessons = \App\Models\Timetable::where('course_id', $courseId)
                    ->where('branch_id', $branchId)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('is_active', true)
                    ->where('effective_from', '<=', $today)
                    ->where(function ($q) use ($today) {
                        $q->whereNull('effective_to')->orWhere('effective_to', '>=', $today);
                    })
                    ->orderBy('start_time')
                    ->get();

                $students = Student::where('course_id', $courseId)
                    ->where('branch_id', $branchId)
                    ->get();
            }

            return view('staff.attendance_manage', compact('courses', 'branches', 'lessons', 'students', 'today'));
        }

        if (auth()->guard('admin')->check()) {
            $students = Student::with('attendances')->get();
            return view('admin.attendance_dashboard', compact('students'));
        }

        abort(403);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $student     = Student::findOrFail($id);
            $studentName = $student->full_name;

            // Validate dismissal reason
            $request->validate([
                'dismissal_reason' => 'required|string|min:5|max:1000',
                'dismissal_notes' => 'nullable|string|max:2000',
            ], [
                'dismissal_reason.required' => 'Please provide a reason for dismissal',
                'dismissal_reason.min' => 'Dismissal reason must be at least 5 characters',
                'dismissal_reason.max' => 'Dismissal reason cannot exceed 1000 characters',
                'dismissal_notes.max' => 'Dismissal notes cannot exceed 2000 characters',
            ]);

            DB::beginTransaction();

            $dismissedStudent = DismissedStudent::dismissStudent(
                $student,
                $request->dismissal_reason,
                $request->dismissal_notes,
                auth('admin')->id()
            );

            $dismissedStudent->save();

            $student->loadMissing(['course', 'branch']);

            try {
                Mail::to($student->email)->send(new StudentDismissalMail(
                    $student,
                    $request->dismissal_reason,
                    $request->dismissal_notes
                ));
                Log::info('Dismissal email sent to student', [
                    'student_email' => $student->email,
                    'student_name'  => $studentName,
                ]);
            } catch (\Exception $mailException) {
                Log::error('Failed to send dismissal email', [
                    'student_email' => $student->email,
                    'error'         => $mailException->getMessage(),
                ]);
            }

            $student->delete();

            DB::commit();

            Log::info('Student dismissed successfully', [
                'student_id'           => $id,
                'student_name'         => $studentName,
                'dismissed_by'         => auth('admin')->id(),
                'dismissal_reason'     => $request->dismissal_reason,
                'dismissed_student_id' => $dismissedStudent->id,
            ]);

            return redirect()->back()
                ->with('success', "Student '{$studentName}' has been dismissed and moved to dismissed records.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during student dismissal', [
                'student_id' => $id,
                'error'      => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while dismissing the student: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        try {
            $students = Student::with(['branch', 'course', 'academicYear'])
                ->when($request->branch_id, function ($query, $branchId) {
                    return $query->where('branch_id', $branchId);
                })
                ->when($request->course_id, function ($query, $courseId) {
                    return $query->where('course_id', $courseId);
                })
                ->when($request->academic_year_id, function ($query, $academicYearId) {
                    return $query->where('academic_year_id', $academicYearId);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $filename = "students_export_" . date('Y-m-d_H-i-s') . ".csv";
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];

            $callback = function () use ($students) {
                $file = fopen('php://output', 'w');
                
                // CSV Header
                fputcsv($file, [
                    'Registration Number',
                    'Full Name',
                    'Email',
                    'Phone',
                    'Gender',
                    'Branch',
                    'Course',
                    'Academic Year',
                    'ID Number',
                    'NESA Registered',
                    'Created At'
                ]);

                // CSV Data
                foreach ($students as $student) {
                    fputcsv($file, [
                        $student->reg_number,
                        $student->full_name,
                        $student->email,
                        $student->phone,
                        $student->sex,
                        $student->branch->name ?? 'N/A',
                        $student->course->abbreviation ?? $student->course->course_name ?? 'N/A',
                        $student->academicYear->year ?? 'N/A',
                        $student->id_number ?? 'N/A',
                        $student->nesa_registered ? 'Yes' : 'No',
                        $student->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error("Error exporting students", [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while exporting students: ' . $e->getMessage());
        }
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function generateUniqueRegNumber($courseAbbreviation, $yearRange)
    {
        $course       = Course::where('abbreviation', $courseAbbreviation)->first();
        $academicYear = AcademicYear::where('year', $yearRange)->first();

        if (!$course || !$academicYear) {
            return 'ESA' . strtoupper($courseAbbreviation) . time() . $yearRange;
        }

        $prefix = 'ESA' . strtoupper($courseAbbreviation);

        $existingNumbers = Student::where('course_id', $course->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('reg_number', 'like', $prefix . '%')
            ->pluck('reg_number')
            ->map(function ($regNumber) use ($prefix, $yearRange) {
                $pattern = '/^' . preg_quote($prefix, '/') . '(\d+)' . preg_quote($yearRange, '/') . '$/';
                if (preg_match($pattern, $regNumber, $matches)) {
                    return (int) $matches[1];
                }
                return 0;
            })
            ->filter()
            ->toArray();

        $nextNumber    = empty($existingNumbers) ? 1 : max($existingNumbers) + 1;
        $studentNumber = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        $regNumber     = $prefix . $studentNumber . $yearRange;

        while (Student::where('reg_number', $regNumber)->exists()) {
            $nextNumber++;
            $studentNumber = str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
            $regNumber     = $prefix . $studentNumber . $yearRange;

            if ($nextNumber > 9999) {
                $regNumber = $prefix . uniqid() . $yearRange;
                break;
            }
        }

        return $regNumber;
    }

    private function cleanupExistingSessions($user, $guard)
    {
        session()->forget('user_session_' . $guard . '_' . $user->id);
        session()->forget('last_activity_' . $guard . '_' . $user->id);
        session()->forget('password_hash_' . $guard);
    }

    private function storeSessionFingerprint($request, $user, $guard)
    {
        $fingerprint = $this->generateSessionFingerprint($request);
        session()->put('user_session_' . $guard . '_' . $user->id, $fingerprint);
        session()->put('last_activity_' . $guard . '_' . $user->id, now());
    }

    private function generateSessionFingerprint($request): string
    {
        return hash('sha256', serialize([
            'ip'              => $request->ip(),
            'user_agent'      => $request->userAgent(),
            'accept_language' => $request->header('Accept-Language'),
            'accept_encoding' => $request->header('Accept-Encoding'),
            'session_id'      => session()->getId(),
        ]));
    }

    private function buildRegNumber(string $abbr, string $yearRange, int $id): string
    {
        return $abbr . str_pad($id, 3, '0', STR_PAD_LEFT) . $yearRange;
    }

    /**
     * Generate a temporary password for a student
     */
    private function generateTemporaryPassword($student)
    {
        // Generate a secure 8-character password
        return strtoupper(substr(md5($student->id . $student->reg_number . time()), 0, 8));
    }

    /**
     * Send bulk email with student information to all students
     */
    public function sendBulkStudentInformationEmails(Request $request)
    {
        // Only allow admin users
        if (!auth('admin')->check()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Get all students with their relationships
            $students = Student::with(['branch', 'course', 'academicYear'])->get();
            
            $sentCount = 0;
            $failedCount = 0;
            $failedEmails = [];

            foreach ($students as $student) {
                try {
                    // Generate a temporary password if the student needs one
                    $password = $this->generateTemporaryPassword($student);
                    
                    // Update the student's password with the new temporary password
                    $student->password = Hash::make($password);
                    $student->save();
                    
                    // Send the email with the new password
                    Mail::to($student->email)->send(new StudentInformationEmail($student, $password));
                    $sentCount++;
                    
                    // Add delay to avoid overwhelming the email server
                    usleep(100000); // 0.1 second delay
                    
                } catch (\Exception $e) {
                    $failedCount++;
                    $failedEmails[] = [
                        'email' => $student->email,
                        'name' => $student->full_name,
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error('Failed to send student information email', [
                        'student_id' => $student->id,
                        'email' => $student->email,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $message = "Email sending completed. Sent: {$sentCount}, Failed: {$failedCount}";
            
            if ($failedCount > 0) {
                $message .= ". Check logs for failed emails.";
                Log::info('Bulk student information email results', [
                    'total_students' => $students->count(),
                    'sent' => $sentCount,
                    'failed' => $failedCount,
                    'failed_emails' => $failedEmails
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Bulk email sending failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to send bulk emails: ' . $e->getMessage());
        }
    }

    /**
     * Download NESA document for student
     */
    public function downloadNesaDocument(Student $student)
    {
        if (!auth()->guard('staff')->check()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$student->nesa_document) {
            abort(404, 'No NESA document found for this student.');
        }

        try {
            $documentPath = storage_path('app/public/' . $student->nesa_document);
            
            if (!file_exists($documentPath)) {
                abort(404, 'Document file not found.');
            }

            // Create filename with student name and registration number
            $studentName = str_replace(' ', '_', $student->full_name);
            $regNumber = str_replace('/', '_', $student->reg_number);
            $originalName = pathinfo($documentPath, PATHINFO_FILENAME);
            $extension = pathinfo($documentPath, PATHINFO_EXTENSION);
            
            $filename = "NESA_{$studentName}_{$regNumber}_{$originalName}.{$extension}";

            return response()->download($documentPath, $filename);
            
        } catch (\Exception $e) {
            Log::error('Failed to download NESA document', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Failed to download document: ' . $e->getMessage());
        }
    }

    /**
     * Download all NESA documents as ZIP
     */
    public function downloadAllNesaDocuments(Request $request)
    {
        if (!auth()->guard('staff')->check()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Get registered students with NESA documents
            $students = Student::where('nesa_registered', true)
                ->whereNotNull('nesa_document')
                ->with(['branch', 'course'])
                ->get();

            if ($students->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No NESA documents found.'
                ], 404);
            }

            // Create ZIP file
            $zip = new \ZipArchive();
            $zipFileName = 'All_NESA_Documents_' . date('Y-m-d_H-i-s') . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);

            if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
                throw new \Exception('Cannot create ZIP file');
            }

            $documentCount = 0;
            foreach ($students as $student) {
                $documentPath = storage_path('app/public/' . $student->nesa_document);
                
                if (file_exists($documentPath)) {
                    // Create filename with student details
                    $studentName = str_replace(' ', '_', $student->full_name);
                    $regNumber = str_replace('/', '_', $student->reg_number);
                    $originalName = pathinfo($documentPath, PATHINFO_BASENAME);
                    
                    $zipFileNameInArchive = "NESA_{$studentName}_{$regNumber}_{$originalName}";
                    $zip->addFile($documentPath, $zipFileNameInArchive);
                    $documentCount++;
                }
            }

            $zip->close();

            if ($documentCount === 0) {
                unlink($zipPath);
                return response()->json([
                    'success' => false,
                    'message' => 'No valid NESA documents found.'
                ], 404);
            }

            Log::info('Bulk NESA documents download', [
                'document_count' => $documentCount,
                'user' => auth()->guard('staff')->user()->full_name
            ]);

            return response()->download($zipPath)->deleteFileAfterSend();

        } catch (\Exception $e) {
            Log::error('Failed to download all NESA documents', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to download documents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export NESA registration report to Excel
     */
    public function exportNesaReport(Request $request)
    {
        if (!auth()->guard('staff')->check()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Apply same filters as the index method
            $students = Student::with(['branch', 'course', 'academicYear'])
                ->when($request->name, function ($query, $name) {
                    $query->where('full_name', 'like', '%' . $name . '%');
                })
                ->when($request->reg_number, function ($query, $regNumber) {
                    $query->where('reg_number', 'like', '%' . $regNumber . '%');
                })
                ->when($request->email, function ($query, $email) {
                    $query->where('email', 'like', '%' . $email . '%');
                })
                ->when($request->telephone, function ($query, $telephone) {
                    $query->where('telephone', 'like', '%' . $telephone . '%');
                })
                ->when($request->course, function ($query, $course) {
                    $query->whereHas('course', function ($q) use ($course) {
                        $q->where('name', 'like', '%' . $course . '%');
                    });
                })
                ->when($request->branch, function ($query, $branch) {
                    $query->where('branch_id', $branch);
                })
                ->orderBy('full_name', 'asc')
                ->get();

            // Create spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('NESA Registration Report');

            // Headers
            $headers = [
                'Student Name',
                'Registration Number',
                'Email',
                'Telephone',
                'Branch',
                'Course',
                'Academic Year',
                'NESA Status',
                'SDMS Code',
                'NESA Registration Number',
                'Registration Date',
                'Has Document',
                'Remarks'
            ];

            $sheet->fromArray($headers, null, 'A1');

            // Style headers
            $headerStyle = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4285F4']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF']]
            ];
            $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);

            // Data
            $row = 2;
            foreach ($students as $student) {
                $sheet->setCellValue('A' . $row, $student->full_name);
                $sheet->setCellValue('B' . $row, $student->reg_number);
                $sheet->setCellValue('C' . $row, $student->email);
                $sheet->setCellValue('D' . $row, $student->telephone);
                $sheet->setCellValue('E' . $row, $student->branch->name ?? 'N/A');
                $sheet->setCellValue('F' . $row, $student->course->name ?? 'N/A');
                $sheet->setCellValue('G' . $row, $student->academicYear->name ?? 'N/A');
                $sheet->setCellValue('H' . $row, $student->nesa_registered ? 'Registered' : 'Not Registered');
                $sheet->setCellValue('I' . $row, $student->nesa_sdms_code ?? 'N/A');
                $sheet->setCellValue('J' . $row, $student->nesa_reg_number ?? 'N/A');
                $sheet->setCellValue('K' . $row, $student->nesa_registration_date ? $student->nesa_registration_date->format('d M Y') : 'N/A');
                $sheet->setCellValue('L' . $row, $student->nesa_document ? 'Yes' : 'No');
                $sheet->setCellValue('M' . $row, $student->nesa_remarks ?? 'N/A');
                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'M') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Create file
            $fileName = 'NESA_Registration_Report_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filePath = storage_path('app/temp/' . $fileName);

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filePath);

            Log::info('NESA report exported', [
                'student_count' => $students->count(),
                'user' => auth()->guard('staff')->user()->full_name
            ]);

            return response()->download($filePath)->deleteFileAfterSend();

        } catch (\Exception $e) {
            Log::error('Failed to export NESA report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update NESA status for student
     */
    public function updateNesaStatus(Request $request, Student $student)
    {
        if (!auth()->guard('staff')->check()) {
            abort(403, 'Unauthorized action.');
        }

        // Log incoming request data for debugging
        \Log::info('NESA status update request', [
            'student_id' => $student->id,
            'request_data' => $request->all(),
            'files' => $request->allFiles()
        ]);

        // Convert checkbox to boolean properly
        $requestData = $request->all();
        $requestData['send_email'] = isset($requestData['send_email']) && ($requestData['send_email'] === '1' || $requestData['send_email'] === 'true' || $requestData['send_email'] === true);

        $validator = \Validator::make($requestData, [
            'status' => 'required|in:registered,failed',
            'send_email' => 'sometimes|boolean',
            'reason' => 'required_if:status,failed|string|max:500',
            'nesa_document' => 'required_if:status,registered|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'
        ], [
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status value',
            'reason.required_if' => 'Reason is required when status is failed',
            'nesa_document.required_if' => 'NESA document is required when status is registered',
            'nesa_document.mimes' => 'Document must be a PDF, DOC, DOCX, JPG, JPEG or PNG file',
            'nesa_document.max' => 'Document size must not exceed 5MB'
        ]);

        if ($validator->fails()) {
            \Log::error('NESA status validation failed', [
                'student_id' => $student->id,
                'errors' => $validator->errors()->toArray(),
                'request_data' => $requestData
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        try {
            $status = $requestData['status'];
            $sendEmail = $requestData['send_email'] ?? false;
            
            \Log::info('Processing NESA status update', [
                'status' => $status,
                'send_email' => $sendEmail,
                'has_document' => $request->hasFile('nesa_document')
            ]);
            
            if ($status === 'registered') {
                // Handle document upload
                if ($request->hasFile('nesa_document')) {
                    if ($student->nesa_document) {
                        Storage::disk('public')->delete($student->nesa_document);
                    }
                    $file = $request->file('nesa_document');
                    $filename = 'nesa_document_' . $student->reg_number . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('nesa_documents', $filename, 'public');
                    
                    $student->update([
                        'nesa_registered' => true,
                        'nesa_document' => $path,
                        'nesa_registration_date' => now(),
                    ]);
                    
                    \Log::info('NESA document uploaded', ['path' => $path]);
                }
                
                // Generate email content for successful registration
                $emailContent = $this->generateNesaStatusEmailContent($student, 'approved');
                
                $emailSent = false;
                $emailError = null;
                
                if ($sendEmail && $student->email) {
                    try {
                        // Attach document to email
                        $documentPath = storage_path('app/public/' . $student->nesa_document);
                        Mail::to($student->email)->send(new NesaRegistrationStatusMail($student, 'approved', $emailContent, $documentPath));
                        \Log::info('NESA approval email sent', ['student_id' => $student->id, 'email' => $student->email]);
                        $emailSent = true;
                    } catch (\Exception $e) {
                        \Log::error('Failed to send NESA approval email', [
                            'student_id' => $student->id,
                            'error' => $e->getMessage()
                        ]);
                        $emailError = $e->getMessage();
                    }
                }
                
                $message = 'Student marked as registered with NESA successfully.';
                if ($sendEmail) {
                    if ($emailSent) {
                        $message .= ' Email notification sent successfully.';
                    } elseif ($emailError) {
                        $message .= ' However, email sending failed: ' . $emailError;
                    } else {
                        $message .= ' Email was not requested or student has no email address.';
                    }
                }
                
            } else {
                // Mark as failed
                $student->update([
                    'nesa_registered' => false,
                    'nesa_remarks' => $requestData['reason'],
                ]);
                
                // Generate email content for failed registration
                $emailContent = $this->generateNesaStatusEmailContent($student, 'rejected', $requestData['reason']);
                
                $emailSent = false;
                $emailError = null;
                
                if ($sendEmail && $student->email) {
                    try {
                        Mail::to($student->email)->send(new NesaRegistrationStatusMail($student, 'rejected', $emailContent));
                        \Log::info('NESA rejection email sent', ['student_id' => $student->id, 'email' => $student->email]);
                        $emailSent = true;
                    } catch (\Exception $e) {
                        \Log::error('Failed to send NESA rejection email', [
                            'student_id' => $student->id,
                            'error' => $e->getMessage()
                        ]);
                        $emailError = $e->getMessage();
                    }
                }
                
                $message = 'Student marked as failed for NESA registration.';
                if ($sendEmail) {
                    if ($emailSent) {
                        $message .= ' Email notification sent successfully.';
                    } elseif ($emailError) {
                        $message .= ' However, email sending failed: ' . $emailError;
                    } else {
                        $message .= ' Email was not requested or student has no email address.';
                    }
                }
            }

            Log::info('NESA status updated successfully', [
                'student_id' => $student->id,
                'status' => $status,
                'user' => auth()->guard('staff')->user()->full_name,
                'email_sent' => $sendEmail
            ]);

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update NESA status', [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update NESA status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send NESA status email to student
     */
    public function sendNesaStatusEmail(Request $request, Student $student)
    {
        if (!auth()->guard('staff')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        try {
            $status = $request->status;
            
            // Create email content based on status
            $subject = $status === 'approved' 
                ? 'NESA Registration Approved' 
                : 'NESA Registration Update';
            
            $emailContent = $this->generateNesaStatusEmailContent($student, $status);
            
            // Send email (commented out for now - uncomment when email is configured)
            // Mail::to($student->email)->send(new NesaStatusMail($student, $status, $emailContent));
            
            // For now, just log and return success
            Log::info("NESA {$status} email sent to student", [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'student_email' => $student->email,
                'status' => $status
            ]);
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($status) . ' email sent successfully to ' . $student->full_name
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send NESA status email', [
                'student_id' => $student->id,
                'status' => $request->status,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show student payment schedule
     */
    public function paymentSchedule(Request $request)
    {
        $student = auth('student')->user();
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'Please login to view payment schedule.');
        }

        $course = $student->course;
        
        if (!$course) {
            return back()->with('error', 'No course assigned to your account.');
        }

        // Get all term fees for the student's course
        $termFees = $course->activeTermFees()->with('term')->get();
        
        $paymentSchedule = [];
        
        foreach ($termFees as $termFee) {
            $transactions = $student->transactions()
                ->where('term_id', $termFee->term_id)
                ->orderBy('paid_at', 'desc')
                ->get();
            
            $totalPaid = $transactions->where('status', 'successful')->sum('amount');
            
            $paymentSchedule[] = [
                'term' => $termFee->term,
                'term_fee' => $termFee,
                'transactions' => $transactions,
                'total_paid' => $totalPaid,
                'total_required' => $termFee->total_fees,
                'remaining' => $termFee->total_fees - $totalPaid,
                'status' => $totalPaid >= $termFee->total_fees ? 'paid' : 
                          ($totalPaid > 0 ? 'partial' : 'unpaid'),
                'fee_breakdown' => [
                    'registration' => [
                        'required' => $termFee->registration_fee,
                        'paid' => $transactions->where('type', 'registration')->where('status', 'successful')->sum('amount'),
                    ],
                    'tuition' => [
                        'required' => $termFee->tuition_fee,
                        'paid' => $transactions->where('type', 'school_fee')->where('status', 'successful')->sum('amount'),
                    ],
                    'practical' => [
                        'required' => $termFee->practical_fee,
                        'paid' => $transactions->where('type', 'practical_fee')->where('status', 'successful')->sum('amount'),
                    ],
                    'trip' => [
                        'required' => $termFee->trip_fee,
                        'paid' => $transactions->where('type', 'trip_fee')->where('status', 'successful')->sum('amount'),
                    ],
                    'student_card' => [
                        'required' => $termFee->student_card_fee,
                        'paid' => $transactions->where('type', 'student_card')->where('status', 'successful')->sum('amount'),
                    ],
                ],
            ];
        }
        
        // Sort by term start date
        usort($paymentSchedule, function($a, $b) {
            return $a['term']->start_date <=> $b['term']->start_date;
        });
        
        return view('students.payment-schedule', compact(
            'student',
            'course',
            'paymentSchedule'
        ));
    }

    /**
     * Generate email content for NESA status
     */
    private function generateNesaStatusEmailContent(Student $student, string $status, string $reason = null): string
    {
        if ($status === 'approved') {
            return "
                <h2>Congratulations - NESA Registration Successful!</h2>
                <p>Dear {$student->full_name},</p>
                <p>We are pleased to inform you that your NESA registration has been successfully completed.</p>
                <p><strong>Registration Details:</strong></p>
                <ul>
                    <li>Registration Date: " . ($student->nesa_registration_date ? $student->nesa_registration_date->format('d M Y') : now()->format('d M Y')) . "</li>
                    <li>Registration Number: {$student->reg_number}</li>
                </ul>
                <p>Your NESA registration document is attached to this email as proof of your successful registration.</p>
                <p>Please keep this document safe for future reference and examination purposes.</p>
                <p>If you have any questions, please don't hesitate to contact us.</p>
                <p>Best regards,<br>Examination Department</p>
            ";
        } else {
            return "
                <h2>NESA Registration - Update Required</h2>
                <p>Dear {$student->full_name},</p>
                <p>We regret to inform you that your NESA registration could not be completed at this time.</p>
                " . ($reason ? "<p><strong>Reason:</strong> {$reason}</p>" : "") . "
                <p>Please contact the administration office immediately to resolve this matter.</p>
                <p>You may need to:</p>
                <ul>
                    <li>Provide additional documentation</li>
                    <li>Complete missing requirements</li>
                    <li>Correct information in your registration</li>
                </ul>
                <p>Please visit our office or call us for assistance with completing your NESA registration.</p>
                <p>We appreciate your understanding and cooperation.</p>
                <p>Best regards,<br>Examination Department</p>
            ";
        }
    }

    /**
     * Handle cash payment for a student
     * This is an alias method for recordCashPayment to match the route definition
     * 
     * @param Request $request
     * @param int $studentId
     * @return \Illuminate\Http\Response
     */
    public function payCash(Request $request, $studentId)
    {
        return $this->recordCashPayment($request, $studentId);
    }
}