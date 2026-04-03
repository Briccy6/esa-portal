<?php

namespace App\Http\Controllers;

use App\Models\ToWhomLetter;
use App\Models\Student;
use App\Models\SchoolInformation;
use App\Models\User;
use App\Models\CourseTermFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
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
            return view('to-whom.create', compact('students', 'letterTypes', 'recipientTitles'));
        } else {
            // Admins can see all students
            $students = Student::with(['course', 'branch', 'academicYear'])
                ->orderBy('full_name')
                ->get();
            return view('to-whom.create', compact('students', 'letterTypes', 'recipientTitles'));
        }
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
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'letter_type' => 'required|in:' . implode(',', array_keys(ToWhomLetter::getLetterTypes())),
            'recipient_title' => 'required|in:' . implode(',', array_keys(ToWhomLetter::getRecipientTitles())),
            'purpose' => 'required|string|max:255',
            'letter_date' => 'required|date',
        ]);

        $user = Auth::user();
        // Eager load relationships to ensure course and academic year names are available
        $student = Student::with(['course', 'branch', 'academicYear'])->findOrFail($request->student_id);

        // Check for outstanding fees on financial status and good standing documents
        if (in_array($request->letter_type, [ToWhomLetter::TYPE_FINANCIAL_STATUS, ToWhomLetter::TYPE_GOOD_STANDING])) {
            $outstandingFees = $this->getStudentOutstandingFees($student->id);
            if ($outstandingFees > 0) {
                return response()->json([
                    'error' => 'Student has outstanding fees',
                    'message' => "This student has outstanding fees of RWF " . number_format($outstandingFees, 2) . ". All fees must be paid before generating a {$request->letter_type} document.",
                    'outstanding_amount' => $outstandingFees
                ], 422);
            }
        }

        // Generate letter content based on type
        $content = $this->generateLetterContent($request->letter_type, $student, $request->purpose);

        // Set generated_by based on user type
        $generatedById = null;
        if ($user && $user instanceof User) {
            // Only set generated_by for User model instances (not Staff)
            // Staff members don't have corresponding user records
            $generatedById = $user->id;
        }

        $letter = ToWhomLetter::create([
            'student_id' => $student->id,
            'generated_by' => $generatedById,
            'letter_type' => $request->letter_type,
            'recipient_title' => $request->recipient_title,
            'purpose' => $request->purpose,
            'content' => $content,
            'letter_date' => $request->letter_date,
            'reference_number' => ToWhomLetter::generateReferenceNumber(),
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
            if (!in_array($letter->student->branch_id, $branchIds)) {
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
            if (!in_array($letter->student->branch_id, $branchIds)) {
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
        }

        $letter->load(['student.course', 'student.branch', 'student.academicYear', 'generator']);
        $schoolInfo = SchoolInformation::getActive();

        $pdf = PDF::loadView('to-whom.pdf', compact('letter', 'schoolInfo'));
        
        return $pdf->download("letter-{$letter->reference_number}.pdf");
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
            default:
                $content = $this->generateCustomContent($student, $purpose);
                break;
        }

        return $content;
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

    /**
     * Generate professional letter with fee details and payment history.
     */
    private function generateProfessionalLetterContent($student, $purpose)
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
        
        $enrollmentDate = $student->created_at->format('F j, Y');
        $currentDate = Carbon::now()->format('F j, Y');
        
        // Start building the letter
        $content = "This is to certify that {$student->full_name} (Registration Number: {$student->reg_number}) ";
        $content .= "has been enrolled as a student at our institution.\n\n";
        
        // ENROLLMENT PROOF
        $content .= "ENROLLMENT INFORMATION:\n";
        $content .= "- Full Name: {$student->full_name}\n";
        $content .= "- Registration Number: {$student->reg_number}\n";
        $content .= "- Course: {$courseName}\n";
        $content .= "- Branch: {$branchName}\n";
        $content .= "- Academic Year: {$academicYear}\n";
        $content .= "- Enrollment Date: {$enrollmentDate}\n";
        $content .= "- Date of This Letter: {$currentDate}\n\n";
        
        // FEE SCHEDULE
        $content .= "FEE STRUCTURE:\n";
        
        if ($student->course_id) {
            // Get the course term fees
            $courseTermFees = DB::table('course_term_fees')
                ->where('course_id', $student->course_id)
                ->where('is_active', true)
                ->get();
            
            if ($courseTermFees->isNotEmpty()) {
                foreach ($courseTermFees as $fee) {
                    $termName = DB::table('terms')->where('id', $fee->term_id)->value('name') ?? "Term {$fee->term_id}";
                    $content .= "\n{$termName}:\n";
                    $content .= "  - Registration Fee: RWF " . number_format($fee->registration_fee, 2) . "\n";
                    $content .= "  - Tuition Fee: RWF " . number_format($fee->tuition_fee, 2) . "\n";
                    $content .= "  - Practical Fee: RWF " . number_format($fee->practical_fee, 2) . "\n";
                    $content .= "  - Trip Fee: RWF " . number_format($fee->trip_fee, 2) . "\n";
                    $content .= "  - Student Card Fee: RWF " . number_format($fee->student_card_fee, 2) . "\n";
                    $totalTermFee = $fee->registration_fee + $fee->tuition_fee + $fee->practical_fee + $fee->trip_fee + $fee->student_card_fee;
                    $content .= "  - Total: RWF " . number_format($totalTermFee, 2) . "\n";
                }
            }
        }
        
        // PAYMENT HISTORY
        $content .= "\n\nPAYMENT HISTORY:\n";
        
        $payments = DB::table('payments')
            ->join('school_fees', 'payments.school_fee_id', '=', 'school_fees.id')
            ->where('school_fees.student_id', $student->id)
            ->select('payments.*', 'school_fees.term_id', 'school_fees.amount')
            ->orderBy('payments.created_at', 'desc')
            ->get();
        
        if ($payments->isNotEmpty()) {
            $totalPaid = 0;
            foreach ($payments as $payment) {
                $termName = DB::table('terms')->where('id', $payment->term_id)->value('name') ?? "Term {$payment->term_id}";
                $content .= "- {$termName}: RWF " . number_format($payment->payed, 2);
                if ($payment->discount > 0) {
                    $content .= " (with discount: RWF " . number_format($payment->discount, 2) . ")";
                }
                $content .= " - Date: " . Carbon::parse($payment->created_at)->format('F j, Y') . "\n";
                $totalPaid += $payment->payed;
            }
            $content .= "\nTotal Paid: RWF " . number_format($totalPaid, 2) . "\n";
        } else {
            $content .= "No payments recorded yet.\n";
        }
        
        // OUTSTANDING BALANCE
        $content .= "\n\nFINANCIAL STATUS:\n";
        
        $totalFees = 0;
        $totalPaid = 0;
        
        // Calculate total fees from school_fees
        $schoolFees = DB::table('school_fees')
            ->where('student_id', $student->id)
            ->sum('amount');
        
        // Calculate total paid from payments
        $paymentTotal = DB::table('payments')
            ->join('school_fees', 'payments.school_fee_id', '=', 'school_fees.id')
            ->where('school_fees.student_id', $student->id)
            ->sum('payments.payed');
        
        $totalFees = $schoolFees;
        $totalPaid = $paymentTotal;
        $outstandingBalance = $totalFees - $totalPaid;
        
        $content .= "- Total Fees Due: RWF " . number_format($totalFees, 2) . "\n";
        $content .= "- Total Paid: RWF " . number_format($totalPaid, 2) . "\n";
        $content .= "- Outstanding Balance: RWF " . number_format(max(0, $outstandingBalance), 2) . "\n";
        
        if ($outstandingBalance <= 0) {
            $content .= "\n✓ CERTIFICATION: The student has NO OUTSTANDING FEES. All required fees have been paid in full or exceeded.\n";
        } else {
            $content .= "\n⚠ NOTE: The student has an outstanding balance of RWF " . number_format($outstandingBalance, 2) . ".\n";
        }
        
        // CLOSING STATEMENT
        $content .= "\n\nThis letter is issued for official purposes and certified to be accurate as per the records ";
        $content .= "available in our institution as of " . Carbon::now()->format('F j, Y') . ".\n";
        
        return $content;
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
     */
    private function getStudentOutstandingFees($studentId): float
    {
        // Get total fees from school_fees
        $totalFees = DB::table('school_fees')
            ->where('student_id', $studentId)
            ->sum('amount');
        
        // Get total paid from payments
        $totalPaid = DB::table('payments')
            ->join('school_fees', 'payments.school_fee_id', '=', 'school_fees.id')
            ->where('school_fees.student_id', $studentId)
            ->sum('payments.payed');
        
        $outstandingBalance = max(0, $totalFees - $totalPaid);
        return (float) $outstandingBalance;
    }
}
