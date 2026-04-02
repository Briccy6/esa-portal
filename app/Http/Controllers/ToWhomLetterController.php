<?php

namespace App\Http\Controllers;

use App\Models\ToWhomLetter;
use App\Models\Student;
use App\Models\SchoolInformation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
        } else {
            // Admins and staff can create letters for any student
            $students = Student::with(['course', 'branch', 'academicYear'])
                ->orderBy('full_name')
                ->get();
            return view('to-whom.create', compact('students', 'letterTypes', 'recipientTitles'));
        }
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
        $student = Student::findOrFail($request->student_id);

        // Generate letter content based on type
        $content = $this->generateLetterContent($request->letter_type, $student, $request->purpose);

        // Set generated_by based on user type
        $generatedById = null;
        if ($user && !$user->isStudent()) {
            // Only set generated_by for non-student users (admin, staff)
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
     * Display the specified resource.
     */
    public function show(ToWhomLetter $letter)
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->isStudent()) {
            $student = Student::where('email', $user->email)->first();
            if (!$student || $letter->student_id != $student->id) {
                abort(403, 'Unauthorized');
            }
        }

        $schoolInfo = SchoolInformation::getActive();
        
        if ($user->isStudent()) {
            return view('to-whom.student-show', compact('letter', 'schoolInfo'));
        } else {
            return view('to-whom.show', compact('letter', 'schoolInfo'));
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
        $courseName = $student->course ? $student->course->name : 'N/A';
        $branchName = $student->branch ? $student->branch->name : 'N/A';
        $academicYear = $student->academicYear ? $student->academicYear->name : 'N/A';
        
        $idNumber = $student->id_number ? $student->id_number : 'N/A';
        
        return "This letter is to certify that {$student->full_name} is currently enrolled at our institution.
        
Student Details:
- Registration Number: {$student->reg_number}
- Course: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}
- ID Number: {$idNumber}

The student was admitted on " . $student->created_at->format('F j, Y') . " and is currently in good academic standing. 
This verification is provided for the purpose of: {$purpose}

Should you require any additional information, please do not hesitate to contact our administration office.";
    }

    /**
     * Generate academic standing content.
     */
    private function generateAcademicStandingContent($student, $purpose)
    {
        $courseName = $student->course ? $student->course->name : 'N/A';
        $branchName = $student->branch ? $student->branch->name : 'N/A';
        $academicYear = $student->academicYear ? $student->academicYear->name : 'N/A';
        
        return "This letter confirms the academic standing of {$student->full_name} at our institution.

Student Academic Information:
- Registration Number: {$student->reg_number}
- Course: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}
- Enrollment Status: Active
- Academic Performance: Satisfactory

The student has maintained satisfactory academic progress and has demonstrated commitment to their studies. 
This academic standing verification is provided for the purpose of: {$purpose}

For more detailed academic records, please contact our academic affairs office.";
    }

    /**
     * Generate financial status content.
     */
    private function generateFinancialStatusContent($student, $purpose)
    {
        $courseName = $student->course ? $student->course->name : 'N/A';
        $branchName = $student->branch ? $student->branch->name : 'N/A';
        $academicYear = $student->academicYear ? $student->academicYear->name : 'N/A';
        
        return "This letter certifies the financial status of {$student->full_name} regarding their account at our institution.

Student Financial Information:
- Registration Number: {$student->reg_number}
- Course: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}

The student's financial account is currently in good standing. All required fees have been paid according to the institution's payment schedule.
This financial status verification is provided for the purpose of: {$purpose}

For detailed financial information, please contact our finance department.";
    }

    /**
     * Generate course completion content.
     */
    private function generateCompletionContent($student, $purpose)
    {
        $courseName = $student->course ? $student->course->name : 'N/A';
        $branchName = $student->branch ? $student->branch->name : 'N/A';
        $academicYear = $student->academicYear ? $student->academicYear->name : 'N/A';
        
        return "This letter certifies that {$student->full_name} has successfully completed the requirements for their course of study.

Completion Details:
- Registration Number: {$student->reg_number}
- Course Completed: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}
- Completion Date: " . Carbon::now()->format('F j, Y') . "

The student has fulfilled all academic and administrative requirements for the course. 
This completion certificate is provided for the purpose of: {$purpose}

Official transcripts and certificates can be obtained from our records office.";
    }

    /**
     * Generate good standing content.
     */
    private function generateGoodStandingContent($student, $purpose)
    {
        $courseName = $student->course ? $student->course->name : 'N/A';
        $branchName = $student->branch ? $student->branch->name : 'N/A';
        $academicYear = $student->academicYear ? $student->academicYear->name : 'N/A';
        
        return "This letter is to certify that {$student->full_name} is in good standing at our institution.

Good Standing Certificate:
- Registration Number: {$student->reg_number}
- Course: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}

The student has maintained satisfactory academic performance, demonstrated good conduct, and has no outstanding disciplinary or financial obligations to the institution.
This good standing certificate is provided for the purpose of: {$purpose}

The student's record reflects positively on their character and academic achievements.";
    }

    /**
     * Generate custom content.
     */
    private function generateCustomContent($student, $purpose)
    {
        $courseName = $student->course ? $student->course->name : 'N/A';
        $branchName = $student->branch ? $student->branch->name : 'N/A';
        $academicYear = $student->academicYear ? $student->academicYear->name : 'N/A';
        
        return "This letter is written regarding {$student->full_name} as requested.

Student Information:
- Registration Number: {$student->reg_number}
- Course: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}

This custom letter is provided for the purpose of: {$purpose}

Should you require specific information about the student, please provide detailed requirements and we will be happy to assist.";
    }
}
