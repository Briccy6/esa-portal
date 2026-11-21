<?php

namespace App\Http\Controllers;

use App\Models\ToWhomLetter;
use App\Models\Student;
use App\Models\SchoolInformation;
use App\Models\User;
use App\Models\CourseTermFee;
use App\Models\StudentTransaction;
use App\Models\Staff;
use App\Models\Teacher;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ToWhomLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isStudent()) {
            // For student users, find their student record by email
            $student = Student::where('email', $user->email)->first();
            
            if (!$student) {
                // Student user without corresponding student record
                $letters = collect(); // Empty collection
            } else {
                // Students can only see their own letters
                $letters = ToWhomLetter::where('student_id', $student->id)
                    ->where('is_active', true)
                    ->with(['student', 'generator'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
            }
                
            return view('to-whom.student-index', compact('letters'));
        } else {
            // Admins and staff can see all letters
            $letters = ToWhomLetter::where('is_active', true)
                ->with(['student', 'generator'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
                
            return view('to-whom.index', compact('letters'));
        }
    }

    /**
     * Show the form for creating a new letter.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $students = collect();
        $employees = collect();
        $letterTypes = ToWhomLetter::getLetterTypes();
        $recipientTitles = ToWhomLetter::getRecipientTitles();

        if ($user->isStudent()) {
            // Students can only create letters for themselves
            $student = Student::where('email', $user->email)->first();
            if ($student) {
                $students = collect([$student]);
            }
            return view('to-whom.student-create', compact('students', 'letterTypes', 'recipientTitles'));
        } else if (method_exists($user, 'isStaff') && $user->isStaff()) {
            // Staff can only see students from their assigned branches
            $branchIds = $user->branches()->pluck('branch_id')->toArray();
            if (empty($branchIds)) {
                // If no branches assigned, show no students
                $students = collect();
            } else {
                $students = Student::with(['course', 'branch', 'academicYear'])
                    ->whereIn('branch_id', $branchIds)
                    ->orderBy('full_name')
                    ->get();
            }
        } else {
            // Admins can see all students
            $students = Student::with(['course', 'branch', 'academicYear'])
                ->orderBy('full_name')
                ->get();
        }

        $employees = Staff::query()->orderBy('name')->get(['id', 'name', 'role'])
            ->map(fn ($employee) => [
                'ref' => 'staff:'.$employee->id,
                'name' => $employee->name,
                'role' => $employee->role,
            ])
            ->concat(Teacher::query()->orderBy('teacher_name')->get(['id', 'teacher_name', 'department'])
                ->map(fn ($employee) => [
                    'ref' => 'teacher:'.$employee->id,
                    'name' => $employee->teacher_name,
                    'role' => 'Teacher - '.$employee->department,
                ]))
            ->sortBy('name')
            ->values();

        return view('to-whom.create', compact('students', 'employees', 'letterTypes', 'recipientTitles'));
    }

    /**
     * Search for students via AJAX (for live autocomplete).
     */
    public function searchStudents(Request $request)
    {
        $query = $request->input('q', '');
        $user = Auth::user();
        
        $studentQuery = Student::with(['course', 'branch', 'academicYear']);
        
        // If staff, filter by their assigned branches
        if ($user && method_exists($user, 'isStaff') && $user->isStaff()) {
            $branchIds = $user->branches()->pluck('branch_id')->toArray();
            if (!empty($branchIds)) {
                $studentQuery->whereIn('branch_id', $branchIds);
            } else {
                // No branches assigned, return empty
                return response()->json([]);
            }
        }
        
        // Search by name or registration number
        if (!empty($query)) {
            $studentQuery->where(function ($q) use ($query) {
                $q->where('full_name', 'like', "%{$query}%")
                  ->orWhere('reg_number', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            });
        }
        
        $students = $studentQuery->orderBy('full_name')
            ->limit(20)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'text' => "{$student->full_name} ({$student->reg_number})",
                    'full_name' => $student->full_name,
                    'reg_number' => $student->reg_number,
                    'branch' => $student->branch?->name ?? 'N/A',
                    'course' => $student->course?->name ?? 'N/A'
                ];
            });
        
        return response()->json($students);
    }

    /**
     * Store a newly created letter.
     */
    public function store(Request $request)
    {
        $recipientType = $request->input('recipient_type', 'student');
        $letterTypes = ToWhomLetter::getLetterTypes();
        if ($recipientType === 'employee') {
            $letterTypes = [ToWhomLetter::TYPE_EMPLOYMENT_CONFIRMATION => $letterTypes[ToWhomLetter::TYPE_EMPLOYMENT_CONFIRMATION]];
        } else {
            unset($letterTypes[ToWhomLetter::TYPE_EMPLOYMENT_CONFIRMATION]);
        }

        $request->validate([
            'recipient_type' => 'required|in:student,employee',
            'student_id' => 'required_if:recipient_type,student|nullable|exists:students,id',
            'employee_ref' => ['required_if:recipient_type,employee', 'nullable', 'regex:/^(staff|teacher):[1-9][0-9]*$/'],
            'letter_type' => 'required|in:' . implode(',', array_keys($letterTypes)),
            'recipient_title' => 'required|in:' . implode(',', array_keys(ToWhomLetter::getRecipientTitles())),
            'purpose' => 'required|string|max:255',
            'letter_date' => 'required|date',
        ]);

        $user = Auth::user();
        if ($user->isStudent() && $recipientType !== 'student') {
            abort(403, 'Students may only request their own documents.');
        }

        $student = null;
        $recipientName = null;
        $recipientRole = null;

        if ($recipientType === 'employee') {
            [$employeeType, $employeeId] = explode(':', $request->employee_ref, 2);
            $employee = $employeeType === 'teacher'
                ? Teacher::findOrFail($employeeId)
                : Staff::findOrFail($employeeId);
            $recipientName = $employeeType === 'teacher' ? $employee->teacher_name : $employee->name;
            if ($employeeType === 'teacher') {
                $recipientRole = 'Teacher - '.$employee->department;
            } else {
                $staffRole = Str::of($employee->role)->replace('_', ' ')->trim()->lower()->toString();
                $recipientRole = in_array($staffRole, ['it', 'information technology'])
                    ? 'IT'
                    : Str::of($staffRole)->title()->toString();
            }
        } else {
            if ($user->isStudent()) {
                $student = Student::with(['course', 'branch', 'academicYear'])
                    ->where('email', $user->email)
                    ->firstOrFail();
                if ((int) $request->student_id !== (int) $student->id) {
                    abort(403, 'Students may only request documents for themselves.');
                }
            } else {
                $student = Student::with(['course', 'branch', 'academicYear'])->findOrFail($request->student_id);
                if (method_exists($user, 'isStaff') && $user->isStaff() && method_exists($user, 'branches')) {
                    $branchIds = $user->branches()->pluck('branch_id')->toArray();
                    if (!in_array($student->branch_id, $branchIds)) {
                        abort(403, 'You may only generate student documents for your assigned branches.');
                    }
                }
            }
            $recipientName = $student->full_name;
        }

        // Check for outstanding fees on financial status and good standing documents
        if ($student && in_array($request->letter_type, [ToWhomLetter::TYPE_FINANCIAL_STATUS, ToWhomLetter::TYPE_GOOD_STANDING])) {
            $outstandingFees = $this->getStudentOutstandingFees($student->id);
            if ($outstandingFees > 0) {
                $error = [
                    'error' => 'Student has outstanding fees',
                    'message' => "This student has outstanding fees of RWF " . number_format($outstandingFees, 2) . ". All fees must be paid before generating a {$request->letter_type} document.",
                    'outstanding_amount' => $outstandingFees
                ];

                if ($request->expectsJson()) {
                    return response()->json($error, 422);
                }

                return back()->withErrors(['student_id' => $error['message']])->withInput();
            }
        }

        // Generate letter content based on type
        $content = $recipientType === 'employee'
            ? "This letter confirms that {$recipientName} is employed by our institution as {$recipientRole}. It is issued upon request for employment verification."
            : $this->generateLetterContent($request->letter_type, $student, $request->purpose);

        // Set generated_by based on user type
        $generatedById = null;
        if ($user && $user instanceof User) {
            // Only set generated_by for User model instances (not Staff)
            // Staff members don't have corresponding user records
            $generatedById = $user->id;
        }

        $verificationToken = Str::random(64);
        $letter = ToWhomLetter::create([
            'student_id' => $student?->id,
            'recipient_type' => $recipientType,
            'recipient_name' => $recipientName,
            'recipient_role' => $recipientRole,
            'generated_by' => $generatedById,
            'letter_type' => $request->letter_type,
            'recipient_title' => $request->recipient_title,
            'purpose' => $request->purpose,
            'content' => $content,
            'letter_date' => $request->letter_date,
            'reference_number' => ToWhomLetter::generateReferenceNumber(),
            'verification_token' => $verificationToken,
            'is_active' => true,
        ]);

        return redirect()->route('to-whom.show', $letter)
            ->with('success', 'Letter generated successfully!');
    }

    /**
     * Delete a generated letter.
     */
    public function destroy(ToWhomLetter $letter)
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->isStudent()) {
            $student = Student::where('email', $user->email)->first();
            if (!$student || $letter->student_id != $student->id) {
                abort(403, 'Unauthorized');
            }
        } else if (method_exists($user, 'isStaff') && $user->isStaff()) {
            // Staff can only delete letters for students in their assigned branches
            $branchIds = $user->branches()->pluck('branch_id')->toArray();
            if ($letter->student && !in_array($letter->student->branch_id, $branchIds)) {
                abort(403, 'Unauthorized');
            }
        }
        // Admins can delete any letter
        
        $letter->delete();
        
        return redirect()->route('to-whom.index')
            ->with('success', 'Letter deleted successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ToWhomLetter $letter)
    {
        $user = auth()->user();
        $canDelete = false;
        
        // Load the student relationship
        $letter->load('student');
        
        // Check permissions and determine if user can delete
        if ($user->isStudent()) {
            $student = Student::where('email', $user->email)->first();
            if (!$student || $letter->student_id != $student->id) {
                abort(403, 'Unauthorized');
            }
            $canDelete = true;
        } else if (method_exists($user, 'isStaff') && $user->isStaff()) {
            // Staff can view letters for students in their assigned branches
            $branchIds = $user->branches()->pluck('branch_id')->toArray();
            if ($letter->student && !in_array($letter->student->branch_id, $branchIds)) {
                abort(403, 'Unauthorized');
            }
            $canDelete = true;
        } else {
            // Admins can delete any letter
            $canDelete = true;
        }

        $schoolInfo = SchoolInformation::getActive();
        
        if ($user->isStudent()) {
            return view('to-whom.student-show', compact('letter', 'schoolInfo', 'canDelete'));
        } else {
            return view('to-whom.show', compact('letter', 'schoolInfo', 'canDelete'));
        }
    }

    /**
     * Generate PDF version of the letter.
     */
    public function download(ToWhomLetter $letter)
    {
        $user = Auth::user();
        
        // Check permissions
        if ($user->isStudent()) {
            $student = Student::where('email', $user->email)->first();
            if (!$student || $letter->student_id !== $student->id) {
                abort(403, 'Unauthorized action.');
            }
        } elseif (method_exists($user, 'isStaff') && $user->isStaff() && $letter->student) {
            $branchIds = method_exists($user, 'branches')
                ? $user->branches()->pluck('branch_id')->toArray()
                : [];
            if (!in_array($letter->student->branch_id, $branchIds)) {
                abort(403, 'Unauthorized action.');
            }
        }

        $letter->load(['student.course', 'student.branch', 'student.academicYear', 'generator']);
        $schoolInfo = SchoolInformation::getActive();
        $verificationUrl = route('to-whom.verify', ['token' => $letter->verification_token], true);
        $qrCode = \QrCode::format('svg')->size(88)->margin(0)->generate($verificationUrl);
        $letter->content = Str::limit((string) $letter->content, 650);

        $pdf = Pdf::loadView('to-whom.pdf', compact('letter', 'schoolInfo', 'verificationUrl', 'qrCode'))
            ->setPaper('a4', 'portrait');
        
        return $pdf->download("letter-{$letter->reference_number}.pdf");
    }

    public function verify(string $token)
    {
        $letter = ToWhomLetter::query()
            ->where('verification_token', $token)
            ->where('is_active', true)
            ->first();

        return response()->view('to-whom.verify', compact('letter'), $letter ? 200 : 404);
    }

    /**
     * Generate letter content based on type.
     */
    private function generateLetterContent($type, $student, $purpose)
    {
        $content = "";
        
        switch ($type) {
            case ToWhomLetter::TYPE_ENROLLMENT:
                $content = $this->generateEnrollmentContent($student, $purpose);
                break;
            case ToWhomLetter::TYPE_ACADEMIC_STANDING:
                $content = $this->generateAcademicStandingContent($student, $purpose);
                break;
            case ToWhomLetter::TYPE_FINANCIAL_STATUS:
                $content = $this->generateFinancialStatusContent($student, $purpose);
                break;
            case ToWhomLetter::TYPE_COMPLETION:
                $content = $this->generateCompletionContent($student, $purpose);
                break;
            case ToWhomLetter::TYPE_GOOD_STANDING:
                $content = $this->generateGoodStandingContent($student, $purpose);
                break;
            case ToWhomLetter::TYPE_PROFESSIONAL_LETTER:
                $content = $this->generateProfessionalLetterContent($student, $purpose);
                break;
            case ToWhomLetter::TYPE_PROOF_OF_REGISTRATION:
                $content = $this->generateStudentDocumentContent($student, 'proof of registration');
                break;
            case ToWhomLetter::TYPE_STUDENT_CONFIRMATION:
                $content = $this->generateStudentDocumentContent($student, 'student confirmation');
                break;
            case ToWhomLetter::TYPE_TO_WHOM:
                $content = $this->generateStudentDocumentContent($student, 'student status');
                break;
            default:
                $content = $this->generateCustomContent($student, $purpose);
                break;
        }

        return $content;
    }

    private function generateStudentDocumentContent($student, string $documentDescription): string
    {
        $courseName = $student->course?->name ?? 'Not recorded';
        $branchName = $student->branch?->name ?? 'Not recorded';
        $academicYear = $student->academicYear?->year ?? 'Not recorded';
        $admissionDate = $student->created_at?->format('F j, Y') ?? 'Not recorded';

        return "This document confirms {$documentDescription} for {$student->full_name} (registration number {$student->reg_number}). "
            ."The student is registered in {$courseName} at the {$branchName} campus for academic year {$academicYear}. "
            ."Admission date: {$admissionDate}.";
    }

    /**
     * Generate enrollment verification content.
     */
    private function generateEnrollmentContent($student, $purpose)
    {
        $courseName = 'Not Specified';
        $branchName = 'Not Specified';
        $academicYear = 'Not Specified';
        
        // Get course name with fallback
        if ($student->course_id) {
            if ($student->course && !empty($student->course->name)) {
                $courseName = $student->course->name;
            } else {
                $courseName = "Course ID: {$student->course_id} (Name not available)";
            }
        }
        
        // Get branch name with fallback
        if ($student->branch_id) {
            if ($student->branch && !empty($student->branch->name)) {
                $branchName = $student->branch->name;
            } else {
                $branchName = "Branch ID: {$student->branch_id} (Name not available)";
            }
        }
        
        // Get academic year with fallback
        if ($student->academic_year_id) {
            if ($student->academicYear && !empty($student->academicYear->year)) {
                $academicYear = $student->academicYear->year;
            } else {
                $academicYear = "Academic Year ID: {$student->academic_year_id} (Name not available)";
            }
        }
        
        $idNumber = $student->id_number ? $student->id_number : 'Not Provided';
        
        return "This letter is to certify that {$student->full_name} is currently enrolled at our institution.
        
Student Details:
- Registration Number: {$student->reg_number}
- Course: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}

The student was admitted on " . $student->created_at->format('F j, Y') . " and is currently in good academic standing.";
    }

    /**
     * Generate academic standing content.
     */
    private function generateAcademicStandingContent($student, $purpose)
    {
        $courseName = 'Not Specified';
        $branchName = 'Not Specified';
        $academicYear = 'Not Specified';
        
        // Get course name with fallback
        if ($student->course_id) {
            if ($student->course && !empty($student->course->name)) {
                $courseName = $student->course->name;
            } else {
                $courseName = "Course ID: {$student->course_id} (Name not available)";
            }
        }
        
        // Get branch name with fallback
        if ($student->branch_id) {
            if ($student->branch && !empty($student->branch->name)) {
                $branchName = $student->branch->name;
            } else {
                $branchName = "Branch ID: {$student->branch_id} (Name not available)";
            }
        }
        
        // Get academic year with fallback
        if ($student->academic_year_id) {
            if ($student->academicYear && !empty($student->academicYear->year)) {
                $academicYear = $student->academicYear->year;
            } else {
                $academicYear = "Academic Year ID: {$student->academic_year_id} (Name not available)";
            }
        }
        
        return "This letter confirms the academic standing of {$student->full_name} at our institution.

Student Academic Information:
- Registration Number: {$student->reg_number}
- Course: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}
- Enrollment Status: Active
- Academic Performance: Satisfactory

The student has maintained satisfactory academic progress and has demonstrated commitment to their studies.";
    }

    /**
     * Generate financial status content.
     */
    private function generateFinancialStatusContent($student, $purpose)
    {
        $courseName = 'Not Specified';
        $branchName = 'Not Specified';
        $academicYear = 'Not Specified';
        
        // Get course name with fallback
        if ($student->course_id) {
            if ($student->course && !empty($student->course->name)) {
                $courseName = $student->course->name;
            } else {
                $courseName = "Course ID: {$student->course_id} (Name not available)";
            }
        }
        
        // Get branch name with fallback
        if ($student->branch_id) {
            if ($student->branch && !empty($student->branch->name)) {
                $branchName = $student->branch->name;
            } else {
                $branchName = "Branch ID: {$student->branch_id} (Name not available)";
            }
        }
        
        // Get academic year with fallback
        if ($student->academic_year_id) {
            if ($student->academicYear && !empty($student->academicYear->year)) {
                $academicYear = $student->academicYear->year;
            } else {
                $academicYear = "Academic Year ID: {$student->academic_year_id} (Name not available)";
            }
        }
        
        return "This letter certifies the financial status of {$student->full_name} regarding their account at our institution.

Student Financial Information:
- Registration Number: {$student->reg_number}
- Course: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}

The student's financial account is currently in good standing. All required fees have been paid according to the institution's payment schedule.";
    }

    /**
     * Generate course completion content.
     */
    private function generateCompletionContent($student, $purpose)
    {
        $courseName = 'Not Specified';
        $branchName = 'Not Specified';
        $academicYear = 'Not Specified';
        
        // Get course name with fallback
        if ($student->course_id) {
            if ($student->course && !empty($student->course->name)) {
                $courseName = $student->course->name;
            } else {
                $courseName = "Course ID: {$student->course_id} (Name not available)";
            }
        }
        
        // Get branch name with fallback
        if ($student->branch_id) {
            if ($student->branch && !empty($student->branch->name)) {
                $branchName = $student->branch->name;
            } else {
                $branchName = "Branch ID: {$student->branch_id} (Name not available)";
            }
        }
        
        // Get academic year with fallback
        if ($student->academic_year_id) {
            if ($student->academicYear && !empty($student->academicYear->year)) {
                $academicYear = $student->academicYear->year;
            } else {
                $academicYear = "Academic Year ID: {$student->academic_year_id} (Name not available)";
            }
        }
        
        return "This letter certifies that {$student->full_name} has successfully completed the requirements for their course of study.

Completion Details:
- Registration Number: {$student->reg_number}
- Course Completed: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}
- Completion Date: " . Carbon::now()->format('F j, Y') . "

The student has fulfilled all academic and administrative requirements for the course.";
    }

    /**
     * Generate good standing content.
     */
    private function generateGoodStandingContent($student, $purpose)
    {
        $courseName = 'Not Specified';
        $branchName = 'Not Specified';
        $academicYear = 'Not Specified';
        
        // Get course name with fallback
        if ($student->course_id) {
            if ($student->course && !empty($student->course->name)) {
                $courseName = $student->course->name;
            } else {
                $courseName = "Course ID: {$student->course_id} (Name not available)";
            }
        }
        
        // Get branch name with fallback
        if ($student->branch_id) {
            if ($student->branch && !empty($student->branch->name)) {
                $branchName = $student->branch->name;
            } else {
                $branchName = "Branch ID: {$student->branch_id} (Name not available)";
            }
        }
        
        // Get academic year with fallback
        if ($student->academic_year_id) {
            if ($student->academicYear && !empty($student->academicYear->year)) {
                $academicYear = $student->academicYear->year;
            } else {
                $academicYear = "Academic Year ID: {$student->academic_year_id} (Name not available)";
            }
        }
        
        return "This letter is to certify that {$student->full_name} is in good standing at our institution.

Good Standing Certificate:
- Registration Number: {$student->reg_number}
- Course: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}

The student has maintained satisfactory academic performance, demonstrated good conduct, and has no outstanding disciplinary or financial obligations to the institution.";
    }

    private function generateProfessionalLetterContent($student, $purpose): string
    {
        return $this->generateStudentDocumentContent($student, 'student confirmation');
    }

    /**
     * Generate custom content.
     */
    private function generateCustomContent($student, $purpose)
    {
        $courseName = 'Not Specified';
        $branchName = 'Not Specified';
        $academicYear = 'Not Specified';
        
        // Get course name with fallback
        if ($student->course_id) {
            if ($student->course && !empty($student->course->name)) {
                $courseName = $student->course->name;
            } else {
                $courseName = "Course ID: {$student->course_id} (Name not available)";
            }
        }
        
        // Get branch name with fallback
        if ($student->branch_id) {
            if ($student->branch && !empty($student->branch->name)) {
                $branchName = $student->branch->name;
            } else {
                $branchName = "Branch ID: {$student->branch_id} (Name not available)";
            }
        }
        
        // Get academic year with fallback
        if ($student->academic_year_id) {
            if ($student->academicYear && !empty($student->academicYear->year)) {
                $academicYear = $student->academicYear->year;
            } else {
                $academicYear = "Academic Year ID: {$student->academic_year_id} (Name not available)";
            }
        }
        
        return "This letter is written regarding {$student->full_name} as requested.

Student Information:
- Registration Number: {$student->reg_number}
- Course: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}

This custom letter is provided for the purpose of: {$purpose}";
    }

    /**
     * Calculate outstanding fees for a student.
     * Compares total fees due vs total paid.
     * Fees are stored in CourseTermFee, payments in StudentTransaction.
     */
    private function getStudentOutstandingFees($studentId): float
    {
        $student = Student::findOrFail($studentId);
        
        // Get total fees from course_term_fees for the student's course
        $totalFees = DB::table('course_term_fees')
            ->where('course_id', $student->course_id)
            ->sum(DB::raw('registration_fee + tuition_fee + practical_fee + trip_fee + student_card_fee'));
        
        // Get total paid from student_transactions (paid and successful transactions only)
        $totalPaid = DB::table('student_transactions')
            ->where('student_id', $studentId)
            ->whereIn('status', ['paid', 'successful'])
            ->sum('amount');
        
        $outstandingBalance = max(0, $totalFees - $totalPaid);
        return (float) $outstandingBalance;
    }
}
