<?php
namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Transcript;
use App\Models\StudentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FlutterwaveController extends Controller
{
    private $publicKey;
    private $secretKey;
    private $baseUrl;

    public function __construct()
    {
        $this->publicKey = config('services.flutterwave.public_key');
        $this->secretKey = config('services.flutterwave.secret_key');
        $this->baseUrl   = 'https://api.flutterwave.com/v3';

        if (empty($this->secretKey) || empty($this->publicKey)) {
            Log::error('Flutterwave API keys are not configured');
        }
    }

    /**
     * Initialize Flutterwave payment (for student registration)
     */
    public function initializePayment(Request $request)
    {
        try {
            $request->validate([
                'amount'                    => 'required|numeric|min:1',
                'email'                     => 'required|email|unique:students,email',
                'phone'                     => 'required|string',
                'full_name'                 => 'required|string',
                'course_id'                 => 'required|exists:courses,id',
                'branch_id'                 => 'required|exists:branches,id',
                'fee_type'                  => 'required|string',
                'total_fee'                 => 'required|numeric|min:1',
                'term'                      => 'nullable|string',
                'password'                  => 'required|string|min:6',
                'senior6_document'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'senior3_document'          => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'other_supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'id_photo'                  => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'passport_photo'            => 'required|file|mimes:jpg,jpeg,png|max:2048',
            ]);

            $course = Course::findOrFail($request->course_id);
            $txRef  = 'REG_' . time() . '_' . Str::random(10);

            $studentData = [
                'full_name'   => $request->full_name,
                'email'       => $request->email,
                'phone'       => $request->phone,
                'branch_id'   => $request->branch_id,
                'course_id'   => $request->course_id,
                'password'    => bcrypt($request->password),
                'fee_type'    => $request->fee_type,
                'total_fee'   => $request->total_fee,
                'term'        => $request->term,
                'amount_paid' => $request->amount,
            ];

            $documentPaths = [];
            foreach (['senior6_document', 'senior3_document', 'other_supporting_document'] as $doc) {
                if ($request->hasFile($doc)) {
                    $documentPaths[$doc] = $request->file($doc)->store('temp/documents', 'public');
                }
            }
            foreach (['id_photo', 'passport_photo'] as $photo) {
                if ($request->hasFile($photo)) {
                    $documentPaths[$photo] = $request->file($photo)->store('temp/photos', 'public');
                }
            }

            cache()->put("student_data_{$txRef}", array_merge($studentData, $documentPaths), 3600);

            // Course name — handles both column naming conventions
            $courseName = $course->course_name ?? $course->name ?? 'Course';

            $paymentData = [
                'tx_ref'          => $txRef,
                'amount'          => $request->amount,
                'currency'        => 'RWF',
                'redirect_url'    => route('flutterwave.callback'),
                'payment_options' => 'card,banktransfer,ussd,mobilemoneyruanda',
                'meta'            => [
                    'course_id' => $course->id,
                ],
                'customer'        => [
                    'email'       => $request->email,
                    'phonenumber' => $request->phone,
                    'name'        => $request->full_name,
                ],
                'customizations'  => [
                    'title'       => 'Student Registration Payment',
                    'description' => "Registration fee for {$courseName} - {$request->fee_type}",
                    'logo'        => config('app.logo_url', ''),
                ],
            ];

            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false, // Disable SSL verification for local development
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->secretKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->baseUrl . '/payments', $paymentData);

            if ($response->successful()) {
                $responseData = $response->json();

                if (($responseData['status'] ?? '') === 'success') {
                    return response()->json([
                        'status'  => 'success',
                        'message' => 'Payment initialized successfully',
                        'data'    => [
                            'payment_link'          => $responseData['data']['link'],
                            'transaction_reference' => $txRef,
                            'expires_at'            => now()->addHour()->toISOString(),
                        ],
                    ]);
                }
            }

            Log::error('Flutterwave payment initialization failed', [
                'response'    => $response->json(),
                'status_code' => $response->status(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to initialize payment. Please try again.',
            ], 400);

        } catch (\Exception $e) {
            Log::error('Payment initialization error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }

    /**
     * Handle Flutterwave callback
     */
    public function handleCallback(Request $request)
    {
        try {
            $txRef         = $request->query('tx_ref');
            $transactionId = $request->query('transaction_id');

            if (!$txRef || !$transactionId) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'Invalid callback parameters');
            }

            $verification = $this->verifyTransaction($transactionId);

            if (($verification['status'] ?? '') === 'success') {
                $transactionData = $verification['data'];

                if ($transactionData['status'] === 'successful' &&
                    $transactionData['tx_ref'] === $txRef) {

                    // New student registration
                    if (strpos($txRef, 'REG_') === 0) {
                        $studentData = cache()->get("student_data_{$txRef}");

                        if ($studentData) {
                            $student = $this->createStudentRecord($studentData, $txRef);
                            $this->recordPaymentInTranscripts($student, $studentData, $transactionData);
                            cache()->forget("student_data_{$txRef}");

                            return redirect()->route('student.dashboard')
                                ->with('success', 'Registration completed successfully!');
                        }

                    // Additional payment for existing student
                    } elseif (strpos($txRef, 'ADD_PAY_') === 0) {
                        $paymentCache = cache()->get("additional_payment_{$txRef}");

                        if ($paymentCache) {
                            // Update the pending StudentTransaction to paid
                            StudentTransaction::where('tx_ref', $txRef)->update([
                                'transaction_id' => $transactionId,
                                'status'         => 'paid',
                                'paid_at'        => now(),
                            ]);

                            // Record in transcripts
                            Transcript::create([
                                'student_id'     => $paymentCache['student_id'],
                                'course_id'      => $paymentCache['course_id'],
                                'fee_type'       => $paymentCache['fee_type'],
                                'amount_paid'    => $paymentCache['amount_paid'],
                                'total_fee'      => $paymentCache['total_fee'],
                                'payment_method' => 'flutterwave',
                                'payment_date'   => now()->toDateString(),
                                'term'           => $paymentCache['term'] ?? null,
                            ]);

                            cache()->forget("additional_payment_{$txRef}");

                            return redirect()->route('student.dashboard')
                                ->with('success', 'Payment of RWF ' . number_format($paymentCache['amount_paid']) . ' completed successfully!');
                        }
                    }
                }
            }

            return redirect()->route('student.dashboard')
                ->with('error', 'Payment verification failed. Contact support if money was deducted.');

        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('student.dashboard')
                ->with('error', 'An error occurred while processing your payment');
        }
    }

    /**
     * Verify transaction with Flutterwave
     */
    public function verifyTransaction($transactionId)
    {
        try {
            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false, // Disable SSL verification for local development
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->secretKey,
                    'Content-Type'  => 'application/json',
                ])
                ->get($this->baseUrl . "/transactions/{$transactionId}/verify");

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Transaction verification error: ' . $e->getMessage());

            return ['status' => 'error', 'message' => 'Verification failed'];
        }
    }

    /**
     * Handle webhook from Flutterwave
     */
    public function handleWebhook(Request $request)
    {
        try {
            $signature = $request->header('verif-hash');

            if (!$signature || $signature !== config('services.flutterwave.webhook_hash')) {
                return response()->json(['status' => 'error'], 401);
            }

            $payload = $request->all();

            if (($payload['event'] ?? '') === 'charge.completed' &&
                ($payload['data']['status'] ?? '') === 'successful') {

                $txRef = $payload['data']['tx_ref'];

                if (strpos($txRef, 'ADD_PAY_') === 0) {
                    $paymentCache = cache()->get("additional_payment_{$txRef}");

                    if ($paymentCache) {
                        StudentTransaction::where('tx_ref', $txRef)->update([
                            'transaction_id' => $payload['data']['id'],
                            'status'         => 'paid',
                            'paid_at'        => now(),
                        ]);

                        Transcript::create([
                            'student_id'     => $paymentCache['student_id'],
                            'course_id'      => $paymentCache['course_id'],
                            'fee_type'       => $paymentCache['fee_type'],
                            'amount_paid'    => $paymentCache['amount_paid'],
                            'total_fee'      => $paymentCache['total_fee'],
                            'payment_method' => 'flutterwave',
                            'payment_date'   => now()->toDateString(),
                            'term'           => $paymentCache['term'] ?? null,
                        ]);

                        cache()->forget("additional_payment_{$txRef}");
                    }
                }

                if (strpos($txRef, 'REG_') === 0) {
                    $studentData = cache()->get("student_data_{$txRef}");

                    if ($studentData) {
                        $student = $this->createStudentRecord($studentData, $txRef);
                        $this->recordPaymentInTranscripts($student, $studentData, $payload['data']);
                        cache()->forget("student_data_{$txRef}");
                    }
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Add additional payment to existing student transcript
     * Called from the student dashboard Pay Now buttons
     */
    public function addPayment(Request $request)
    {
        try {
            // ── 1. Check keys are configured ──────────────────────────────────
            if (empty($this->secretKey) || empty($this->publicKey)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Payment service is not configured. Please contact the administrator.',
                ], 503);
            }

            // ── 2. Validate ────────────────────────────────────────────────────
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'fee_type'   => 'required|string|in:registration_fee,school_fee,trip_fee,practical_fee,student_card',
                'amount'     => 'required|numeric|min:1',
                'total_fee'  => 'required|numeric|min:1',
                'term'       => 'nullable|string',
            ]);

            // ── 3. Load student ────────────────────────────────────────────────
            $student = Student::with(['course', 'branch'])->findOrFail($validated['student_id']);

            if (!$student->course_id) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Student is not assigned to any course. Please contact the administrator.',
                ], 422);
            }

            // Course model uses 'course_name' column, not 'name'
            $courseName = $student->course->course_name
                       ?? $student->course->name
                       ?? 'Course #' . $student->course_id;

            // ── 4. Build tx reference & cache payload ──────────────────────────
            $txRef = 'ADD_PAY_' . time() . '_' . Str::random(10);

            $paymentCache = [
                'student_id'  => $student->id,
                'course_id'   => $student->course_id,
                'fee_type'    => $validated['fee_type'],
                'amount_paid' => $validated['amount'],
                'total_fee'   => $validated['total_fee'],
                'term'        => $validated['term'] ?? null,
            ];

            cache()->put("additional_payment_{$txRef}", $paymentCache, 3600);

            // ── 5. Create pending StudentTransaction record ────────────────────
            // NOTE: We use StudentTransaction here — NOT Payment model,
            // because the payments table belongs to a different legacy system
            // and does not have the columns this flow needs.
            StudentTransaction::create([
                'student_id' => $student->id,
                'tx_ref'     => $txRef,
                'type'       => $validated['fee_type'],
                'amount'     => $validated['amount'],
                'currency'   => 'RWF',
                'status'     => 'pending',
            ]);

            Log::info('addPayment: StudentTransaction created', [
                'tx_ref'     => $txRef,
                'student_id' => $student->id,
                'fee_type'   => $validated['fee_type'],
                'amount'     => $validated['amount'],
            ]);

            // ── 6. Call Flutterwave API ────────────────────────────────────────
            $flutterwaveData = [
                'tx_ref'          => $txRef,
                'amount'          => $validated['amount'],
                'currency'        => 'RWF',
                'redirect_url'    => route('student.payment.callback'),
                'payment_options' => 'card,banktransfer,ussd,mobilemoneyruanda',
                'meta'            => [
                    'student_id'         => $student->id,
                    'fee_type'           => $validated['fee_type'],
                    'additional_payment' => true,
                ],
                'customer' => [
                    'email'       => $student->email,
                    'phonenumber' => $student->phone ?? '',
                    'name'        => $student->full_name,
                ],
                'customizations' => [
                    'title'       => 'Fee Payment – ' . config('app.name'),
                    'description' => ucfirst(str_replace('_', ' ', $validated['fee_type'])) . ' for ' . $courseName,
                    'logo'        => config('app.logo_url', ''),
                ],
            ];

            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false, // Disable SSL verification for local development
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->secretKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->baseUrl . '/payments', $flutterwaveData);

            // ── 7. Handle API response ─────────────────────────────────────────
            if ($response->successful()) {
                $responseData = $response->json();

                if (($responseData['status'] ?? '') === 'success') {
                    return response()->json([
                        'status'       => 'success',
                        'message'      => 'Payment initialized successfully',
                        'payment_link' => $responseData['data']['link'],
                    ]);
                }

                Log::error('addPayment: Flutterwave body not success', ['response' => $responseData]);

                return response()->json([
                    'status'  => 'error',
                    'message' => $responseData['message'] ?? 'Payment gateway returned an error. Please try again.',
                ], 400);
            }

            Log::error('addPayment: Flutterwave HTTP error', [
                'status_code' => $response->status(),
                'body'        => $response->body(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Payment gateway error (HTTP ' . $response->status() . '). Please try again.',
            ], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => collect($e->errors())->flatten()->implode(' '),
            ], 422);

        } catch (\Exception $e) {
            Log::error('addPayment: exception', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get student payment history from transcripts
     */
    public function getStudentPaymentHistory($studentId)
    {
        try {
            $student = Student::with('course', 'branch')->findOrFail($studentId);

            $paymentHistory = Transcript::where('student_id', $studentId)
                ->orderBy('payment_date', 'desc')
                ->get();

            $totalPaid  = $paymentHistory->sum('amount_paid');
            $totalFees  = $paymentHistory->sum('total_fee');
            $balance    = $totalFees - $totalPaid;

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'student' => [
                        'id'         => $student->id,
                        'name'       => $student->full_name,
                        'reg_number' => $student->reg_number,
                        'email'      => $student->email,
                        'course'     => $student->course->course_name ?? $student->course->name ?? 'N/A',
                        'branch'     => $student->branch->name ?? 'N/A',
                    ],
                    'payment_summary' => [
                        'total_paid'  => $totalPaid,
                        'total_fees'  => $totalFees,
                        'balance'     => $balance,
                    ],
                    'payment_history' => $paymentHistory->map(fn($p) => [
                        'id'             => $p->id,
                        'fee_type'       => $p->fee_type,
                        'amount_paid'    => $p->amount_paid,
                        'total_fee'      => $p->total_fee,
                        'payment_method' => $p->payment_method,
                        'payment_date'   => $p->payment_date,
                        'term'           => $p->term,
                        'created_at'     => $p->created_at,
                    ]),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching payment history: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Error retrieving payment history',
            ], 500);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($transactionReference)
    {
        try {
            $transaction = StudentTransaction::where('tx_ref', $transactionReference)->first();

            if (!$transaction) {
                return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
            }

            $student = Student::find($transaction->student_id);

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'payment_status' => $transaction->status,
                    'amount'         => $transaction->amount,
                    'currency'       => $transaction->currency ?? 'RWF',
                    'student'        => $student->full_name ?? 'N/A',
                    'created_at'     => $transaction->created_at,
                    'paid_at'        => $transaction->paid_at,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Get payment status error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Error retrieving payment status',
            ], 500);
        }
    }

    /**
     * Handle payment success redirect
     */
    public function paymentSuccess(Request $request)
    {
        return redirect()->route('student.dashboard')
            ->with('success', 'Payment completed successfully!');
    }

    /**
     * Handle payment failed redirect
     */
    public function paymentFailed(Request $request)
    {
        return redirect()->route('student.dashboard')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Refund payment
     */
    public function refundPayment(Request $request)
    {
        try {
            $request->validate([
                'transaction_id' => 'required|string',
                'amount'         => 'sometimes|numeric|min:1',
            ]);

            $refundData = ['amount' => $request->amount ?? null];

            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false, // Disable SSL verification for local development
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->secretKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->baseUrl . "/transactions/{$request->transaction_id}/refund", $refundData);

            if ($response->successful()) {
                $responseData = $response->json();

                if (($responseData['status'] ?? '') === 'success') {
                    StudentTransaction::where('transaction_id', $request->transaction_id)
                        ->update(['status' => 'refunded']);

                    return response()->json([
                        'status'  => 'success',
                        'message' => 'Refund processed successfully',
                        'data'    => $responseData['data'],
                    ]);
                }
            }

            return response()->json(['status' => 'error', 'message' => 'Refund failed'], 400);

        } catch (\Exception $e) {
            Log::error('Refund error: ' . $e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'Error processing refund'], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function createStudentRecord($studentData, $txRef)
    {
        try {
            $regNumber      = $this->generateRegistrationNumber($studentData['branch_id'], $studentData['course_id']);
            $permanentPaths = $this->moveFilesToPermanentLocation($studentData, $regNumber);

            $student = Student::create([
                'full_name'                 => $studentData['full_name'],
                'email'                     => $studentData['email'],
                'phone'                     => $studentData['phone'],
                'branch_id'                 => $studentData['branch_id'],
                'course_id'                 => $studentData['course_id'],
                'reg_number'                => $regNumber,
                'password'                  => $studentData['password'],
                'senior6_document'          => $permanentPaths['senior6_document'] ?? null,
                'senior3_document'          => $permanentPaths['senior3_document'] ?? null,
                'other_supporting_document' => $permanentPaths['other_supporting_document'] ?? null,
                'id_photo'                  => $permanentPaths['id_photo'] ?? null,
                'passport_photo'            => $permanentPaths['passport_photo'] ?? null,
            ]);

            return $student;

        } catch (\Exception $e) {
            Log::error('Student record creation error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function generateRegistrationNumber($branchId, $courseId)
    {
        $branch     = DB::table('branches')->find($branchId);
        $course     = DB::table('courses')->find($courseId);
        $branchCode = strtoupper(substr($branch->name ?? 'BR', 0, 3));
        $courseCode = strtoupper(substr($course->code ?? $course->course_name ?? $course->name ?? 'CO', 0, 3));
        $currentYear  = date('Y');
        $academicYear = $currentYear . '-' . ($currentYear + 1);

        $lastStudent = Student::where('reg_number', 'like', "{$branchCode}{$courseCode}%{$academicYear}")
            ->orderBy('reg_number', 'desc')
            ->first();

        $sequence = 1;
        if ($lastStudent) {
            preg_match('/(\d+)' . preg_quote($academicYear) . '$/', $lastStudent->reg_number, $matches);
            $sequence = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        }

        return $branchCode . $courseCode . str_pad($sequence, 3, '0', STR_PAD_LEFT) . $academicYear;
    }

    private function moveFilesToPermanentLocation($studentData, $regNumber)
    {
        $permanentPaths = [];
        $studentFolder  = 'students/' . $regNumber;

        foreach (['senior6_document', 'senior3_document', 'other_supporting_document'] as $docType) {
            if (isset($studentData[$docType])) {
                $tempPath = $studentData[$docType];
                $ext      = pathinfo($tempPath, PATHINFO_EXTENSION);
                $filename = $docType . '_' . time() . '.' . $ext;
                $destPath = $studentFolder . '/documents/' . $filename;

                if (Storage::disk('public')->exists($tempPath)) {
                    Storage::disk('public')->move($tempPath, $destPath);
                    $permanentPaths[$docType] = $destPath;
                }
            }
        }

        foreach (['id_photo', 'passport_photo'] as $photoType) {
            if (isset($studentData[$photoType])) {
                $tempPath = $studentData[$photoType];
                $ext      = pathinfo($tempPath, PATHINFO_EXTENSION);
                $filename = $photoType . '_' . time() . '.' . $ext;
                $destPath = $studentFolder . '/photos/' . $filename;

                if (Storage::disk('public')->exists($tempPath)) {
                    Storage::disk('public')->move($tempPath, $destPath);
                    $permanentPaths[$photoType] = $destPath;
                }
            }
        }

        return $permanentPaths;
    }

    /**
     * Initialize Flutterwave payment for subscription purchase
     */
    public function initializeSubscriptionPayment(Request $request)
    {
        try {
            $request->validate([
                'subscription_id' => 'required|exists:subscriptions,id',
            ]);

            $student = auth('student')->user();
            $subscription = \App\Models\Subscription::findOrFail($request->subscription_id);

            // Create student subscription record
            $studentSubscription = \App\Models\StudentSubscription::create([
                'student_id' => $student->id,
                'subscription_id' => $subscription->id,
                'paid_amount' => $subscription->price,
                'payment_status' => 'pending',
                'starts_at' => now(),
                'ends_at' => now()->addDays($subscription->duration_days),
                'is_active' => false,
            ]);

            // Generate transaction reference
            $txRef = 'SUB-' . $student->id . '-' . time() . '-' . uniqid();

            // Create Flutterwave payment payload
            $payload = [
                'tx_ref' => $txRef,
                'amount' => $subscription->price,
                'currency' => 'RWF',
                'email' => $student->email,
                'phone_number' => $student->phone ?? '',
                'fullname' => $student->full_name,
                'customer' => [
                    'email' => $student->email,
                    'name' => $student->full_name,
                ],
                'customizations' => [
                    'title' => 'Subscription Purchase - ' . $subscription->name,
                    'description' => $subscription->tokens_included . ' tokens included',
                ],
                'redirect_url' => route('student.dashboard'),
                'meta' => [
                    'student_id' => $student->id,
                    'subscription_id' => $subscription->id,
                    'tokens_included' => $subscription->tokens_included,
                    'duration_days' => $subscription->duration_days,
                ],
            ];

            // Initialize Flutterwave payment
            $response = Http::withToken($this->secretKey)
                ->asJson()
                ->post($this->baseUrl . '/payments', $payload);

            $responseData = $response->json();

            if ($responseData['status'] === 'success') {
                // Update student subscription with payment link
                $studentSubscription->update([
                    'payment_reference' => $responseData['data']['link'],
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment initialized successfully',
                    'payment_link' => $responseData['data']['link'],
                    'tx_ref' => $txRef,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $responseData['message'] ?? 'Failed to initialize payment',
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Flutterwave subscription payment initialization failed', [
                'error' => $e->getMessage(),
                'student_id' => auth('student')->id(),
                'subscription_id' => $request->subscription_id,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Payment initialization failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle Flutterwave payment callback
     */
    public function handlePaymentCallback(Request $request)
    {
        try {
            $txRef = $request->input('tx_ref');
            $status = $request->input('status');

            if ($status === 'successful') {
                // Find student subscription by transaction reference
                $studentSubscription = \App\Models\StudentSubscription::whereHas('subscription', function ($query) use ($txRef) {
                    $query->where('payment_reference', 'like', '%' . $txRef . '%');
                })->first();

                if ($studentSubscription) {
                    // Mark subscription as paid and active
                    $studentSubscription->update([
                        'payment_status' => 'paid',
                        'is_active' => true,
                        'paid_at' => now(),
                    ]);

                    // Initialize tokens for student if not already done
                    $student = $studentSubscription->student;
                    $student->initializeTokens();
                    
                    // Add tokens from subscription
                    $token = $student->token;
                    $token->addTokens(
                        $studentSubscription->subscription->tokens_included,
                        "Tokens from {$studentSubscription->subscription->name} subscription",
                        'subscription'
                    );

                    // Create payment transaction record
                    StudentTransaction::create([
                        'student_id' => $student->id,
                        'tx_ref' => $txRef,
                        'type' => 'subscription',
                        'amount' => $studentSubscription->paid_amount,
                        'currency' => 'RWF',
                        'status' => 'successful',
                        'meta' => json_encode([
                            'method' => 'flutterwave',
                            'subscription_id' => $studentSubscription->subscription_id,
                            'subscription_name' => $studentSubscription->subscription->name,
                            'tokens_included' => $studentSubscription->subscription->tokens_included,
                            'duration_days' => $studentSubscription->subscription->duration_days,
                        ]),
                        'paid_at' => now(),
                    ]);

                    Log::info('Subscription payment successful', [
                        'student_id' => $student->id,
                        'subscription_id' => $studentSubscription->subscription_id,
                        'amount' => $studentSubscription->paid_amount,
                        'tokens_added' => $studentSubscription->subscription->tokens_included,
                    ]);
                }
            }

            return redirect()->route('student.dashboard')
                ->with('success', 'Payment processed successfully');

        } catch (\Exception $e) {
            Log::error('Flutterwave payment callback failed', [
                'error' => $e->getMessage(),
                'tx_ref' => $request->input('tx_ref'),
                'status' => $request->input('status'),
            ]);

            return redirect()->route('student.dashboard')
                ->with('error', 'Payment processing failed');
        }
    }

    private function recordPaymentInTranscripts($student, $studentData, $transactionData)
    {
        try {
            Transcript::create([
                'student_id'     => $student->id,
                'course_id'      => $student->course_id,
                'fee_type'       => $studentData['fee_type'],
                'amount_paid'    => $studentData['amount_paid'],
                'total_fee'      => $studentData['total_fee'],
                'payment_method' => 'flutterwave',
                'payment_date'   => now()->toDateString(),
                'term'           => $studentData['term'] ?? null,
            ]);

            Log::info('Payment recorded in transcripts', [
                'student_id' => $student->id,
                'amount'     => $studentData['amount_paid'],
                'fee_type'   => $studentData['fee_type'],
            ]);

        } catch (\Exception $e) {
            Log::error('Error recording payment in transcripts: ' . $e->getMessage(), [
                'student_id'       => $student->id,
                'transaction_data' => $transactionData,
            ]);
        }
    }
}