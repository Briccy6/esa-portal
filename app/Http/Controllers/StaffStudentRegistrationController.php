<?php

namespace App\Http\Controllers;

use App\Mail\StudentWelcomeMail;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CourseTermBranchFee;
use App\Models\CourseTermFee;
use App\Models\Student;
use App\Models\StudentTransaction;
use App\Models\Term;
use App\Services\StudentRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StaffStudentRegistrationController extends Controller
{
    private const PAYMENT_FEE_FIELDS = [
        'registration_fee' => 'registration_fee',
        'school_fee' => 'tuition_fee',
        'practical_fee' => 'practical_fee',
        'trip_fee' => 'trip_fee',
        'student_card' => 'student_card_fee',
    ];

    public function __construct(private StudentRegistrationService $registrationService)
    {
    }

    public function create()
    {
        $staff = auth('staff')->user();
        $branches = $staff->branches()
            ->select('branches.id', 'branches.name')
            ->orderBy('branches.name')
            ->get();

        $academicYears = AcademicYear::active()
            ->orderByDesc('starting_date')
            ->get(['id', 'year']);

        if ($academicYears->isEmpty()) {
            $academicYears = AcademicYear::orderByDesc('starting_date')->get(['id', 'year']);
        }

        return view('staff.create', compact('branches', 'academicYears'));
    }

    public function checkEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);
        $email = Str::lower(trim($validated['email']));
        $exists = Student::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->exists();

        return response()->json([
            'available' => ! $exists,
            'message' => $exists
                ? 'This email is already registered. Use another email to continue.'
                : 'Email is available.',
        ]);
    }

    public function terms(AcademicYear $academicYear)
    {
        $terms = Term::query()
            ->where('academic_year_id', $academicYear->id)
            ->orderBy('start_date')
            ->get(['id', 'name', 'start_date', 'end_date']);

        return response()->json($terms);
    }

    public function courses(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        $this->authorizeBranch((int) $validated['branch_id']);

        $courses = Course::query()
            ->where('academic_year_id', $validated['academic_year_id'])
            ->whereHas('branches', fn ($query) => $query->where('branches.id', $validated['branch_id']))
            ->orderBy('course_name')
            ->get(['id', 'course_name', 'abbreviation']);

        return response()->json($courses);
    }

    public function fees(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'term_id' => ['required', 'exists:terms,id'],
        ]);

        [, , $term, $feeSchedule, $source] = $this->resolveRegistrationScope($validated);
        if (! $feeSchedule) {
            return response()->json([
                'message' => 'No fee schedule is configured for this branch, course, and term.',
            ], 422);
        }

        $items = [
            ['type' => 'registration_fee', 'label' => 'Registration fee', 'amount' => (float) $feeSchedule->registration_fee],
            ['type' => 'school_fee', 'label' => 'Tuition', 'amount' => (float) $feeSchedule->tuition_fee],
            ['type' => 'practical_fee', 'label' => 'Practical fee', 'amount' => (float) $feeSchedule->practical_fee],
            ['type' => 'trip_fee', 'label' => 'Trip fee', 'amount' => (float) $feeSchedule->trip_fee],
            ['type' => 'student_card', 'label' => 'Student card', 'amount' => (float) $feeSchedule->student_card_fee],
        ];

        return response()->json([
            'term' => $term->name,
            'source' => $source,
            'items' => $items,
            'total' => array_sum(array_column($items, 'amount')),
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'full_name' => Str::title(trim(preg_replace('/\s+/u', ' ', (string) $request->input('full_name')) ?? '')),
            'email' => Str::lower(trim((string) $request->input('email'))),
            'phone' => trim((string) $request->input('phone')),
        ]);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email'),
                function ($attribute, $value, $fail) {
                    if (Student::query()->whereRaw('LOWER(email) = ?', [Str::lower(trim($value))])->exists()) {
                        $fail('This email is already registered. Use another email to continue.');
                    }
                },
            ],
            'phone' => ['required', 'string', 'max:20'],
            'sex' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'enrollment_term_id' => ['required', 'exists:terms,id'],
            'shift' => ['required', Rule::in(['day', 'evening', 'weekend'])],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.type' => ['required', 'string', 'distinct', Rule::in(array_keys(self::PAYMENT_FEE_FIELDS))],
            'payments.*.amount' => ['required', 'numeric', 'min:1'],
        ]);

        [$academicYear, $course, $term, $feeSchedule] = $this->resolveRegistrationScope([
            'academic_year_id' => $validated['academic_year_id'],
            'branch_id' => $validated['branch_id'],
            'course_id' => $validated['course_id'],
            'term_id' => $validated['enrollment_term_id'],
        ]);

        if (! $feeSchedule) {
            throw ValidationException::withMessages([
                'payments' => 'No fee schedule is configured for this branch, course, and term.',
            ]);
        }

        foreach ($validated['payments'] as $index => $payment) {
            $feeAmount = (float) $feeSchedule->{self::PAYMENT_FEE_FIELDS[$payment['type']]};
            if ($feeAmount <= 0) {
                throw ValidationException::withMessages([
                    "payments.{$index}.type" => 'The selected fee category has no amount due.',
                ]);
            }
            if ((float) $payment['amount'] > $feeAmount) {
                throw ValidationException::withMessages([
                    "payments.{$index}.amount" => 'The payment cannot exceed the selected fee category total.',
                ]);
            }
        }

        $staff = auth('staff')->user();
        $plainPassword = Str::random(12);
        $student = DB::transaction(function () use ($validated, $academicYear, $course, $term, $feeSchedule, $staff, $plainPassword) {
            $student = Student::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'sex' => $validated['sex'],
                'branch_id' => $validated['branch_id'],
                'course_id' => $course->id,
                'academic_year_id' => $academicYear->id,
                'enrollment_term_id' => $term->id,
                'shift' => $validated['shift'],
                'reg_number' => $this->registrationService->generateUniqueRegNumber($course->abbreviation, $academicYear->year),
                'password' => Hash::make($plainPassword),
            ]);

            $student->initializeTokens(10);

            $registrationFeePaid = false;
            foreach ($validated['payments'] as $payment) {
                StudentTransaction::create([
                    'student_id' => $student->id,
                    'term_id' => $term->id,
                    'tx_ref' => 'CASH-' . $student->id . '-' . now()->timestamp . '-' . Str::upper(Str::random(8)),
                    'type' => $payment['type'],
                    'amount' => $payment['amount'],
                    'currency' => 'RWF',
                    'status' => 'successful',
                    'meta' => [
                        'method' => 'cash',
                        'recorded_by' => $staff->name,
                    ],
                    'paid_at' => now(),
                ]);

                if (
                    $payment['type'] === 'registration_fee'
                    && (float) $payment['amount'] >= (float) $feeSchedule->registration_fee
                ) {
                    $registrationFeePaid = true;
                }
            }

            if ($registrationFeePaid) {
                $student->payed_registration_fees = true;
                $student->save();
            }

            return $student;
        });

        try {
            Mail::to($student->email)->send(new StudentWelcomeMail($student, $plainPassword));
        } catch (\Throwable $exception) {
            Log::error('Failed to send staff registration welcome email', [
                'student_id' => $student->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('staff.students.create')
            ->with('success', "Student {$student->full_name} registered. Registration number: {$student->reg_number}.");
    }

    private function resolveRegistrationScope(array $data): array
    {
        $this->authorizeBranch((int) $data['branch_id']);

        $academicYear = AcademicYear::findOrFail($data['academic_year_id']);
        $term = Term::query()
            ->where('academic_year_id', $academicYear->id)
            ->findOrFail($data['term_id']);
        $course = Course::query()
            ->where('academic_year_id', $academicYear->id)
            ->whereHas('branches', fn ($query) => $query->where('branches.id', $data['branch_id']))
            ->findOrFail($data['course_id']);

        $branchFee = CourseTermBranchFee::query()
            ->where('course_id', $course->id)
            ->where('branch_id', $data['branch_id'])
            ->where('term_id', $term->id)
            ->where('is_active', true)
            ->first();

        $feeSchedule = $branchFee ?? CourseTermFee::query()
            ->where('course_id', $course->id)
            ->where('term_id', $term->id)
            ->where('is_active', true)
            ->first();

        return [$academicYear, $course, $term, $feeSchedule, $branchFee ? 'branch-specific' : 'course schedule'];
    }

    private function authorizeBranch(int $branchId): void
    {
        $hasAccess = auth('staff')->user()
            ->branches()
            ->where('branches.id', $branchId)
            ->exists();

        abort_unless($hasAccess, 403, 'You may only register students in your assigned branches.');
    }
}
