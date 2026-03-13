<?php
namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Transcript;
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
        $this->baseUrl = 'https://api.flutterwave.com/v3';
    }

    /**
     * Initialize Flutterwave payment (for student registration)
     */
    public function initializePayment(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'email' => 'required|email|unique:students,email',
                'phone' => 'required|string',
                'full_name' => 'required|string',
                'course_id' => 'required|exists:courses,id',
                'branch_id' => 'required|exists:branches,id',
                'fee_type' => 'required|string', // e.g., 'registration', 'tuition', 'exam', etc.
                'total_fee' => 'required|numeric|min:1',
                'term' => 'nullable|string', // e.g., 'Term 1', 'Semester 1', etc.
                'password' => 'required|string|min:6',
                'senior6_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'senior3_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'other_supporting_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'id_photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'passport_photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            ]);

            // Get course details
            $course = Course::findOrFail($request->course_id);

            // Generate unique transaction reference
            $txRef = 'REG_' . time() . '_' . Str::random(10);

            // Store student data temporarily in session or cache for later use
            $studentData = [
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'branch_id' => $request->branch_id,
                'course_id' => $request->course_id,
                'password' => bcrypt($request->password),
                'fee_type' => $request->fee_type,
                'total_fee' => $request->total_fee,
                'term' => $request->term,
                'amount_paid' => $request->amount,
            ];

            // Handle file uploads temporarily
            $documentPaths = [];
            if ($request->hasFile('senior6_document')) {
                $documentPaths['senior6_document'] = $request->file('senior6_document')->store('temp/documents', 'public');
            }
            if ($request->hasFile('senior3_document')) {
                $documentPaths['senior3_document'] = $request->file('senior3_document')->store('temp/documents', 'public');
            }
            if ($request->hasFile('other_supporting_document')) {
                $documentPaths['other_supporting_document'] = $request->file('other_supporting_document')->store('temp/documents', 'public');
            }
            if ($request->hasFile('id_photo')) {
                $documentPaths['id_photo'] = $request->file('id_photo')->store('temp/photos', 'public');
            }
            if ($request->hasFile('passport_photo')) {
                $documentPaths['passport_photo'] = $request->file('passport_photo')->store('temp/photos', 'public');
            }

            // Store in cache with transaction reference as key (expires in 1 hour)
            cache()->put("student_data_{$txRef}", array_merge($studentData, $documentPaths), 3600);

            // Create payment record without student_id for now
            $payment = Payment::create([
                'student_id' => null, // Will be updated after student creation
                'course_id' => $course->id,
                'amount' => $request->amount,
                'currency' => 'RWF', // Assuming Rwanda Francs
                'transaction_reference' => $txRef,
                'status' => 'pending',
                'payment_method' => 'flutterwave',
                'metadata' => json_encode([
                    'course_name' => $course->name,
                    'student_email' => $request->email,
                    'registration_data' => true,
                ])
            ]);

            // Prepare Flutterwave payment data
            $paymentData = [
                'tx_ref' => $txRef,
                'amount' => $request->amount,
                'currency' => 'NGN',
                'redirect_url' => route('flutterwave.callback'),
                'payment_options' => 'card,banktransfer,ussd',
                'meta' => [
                    'payment_id' => $payment->id,
                    'course_id' => $course->id,
                    'student_id' => $student->id
                ],
                'customer' => [
                    'email' => $request->email,
                    'phonenumber' => $request->phone,
                    'name' => $request->full_name
                ],
                'customizations' => [
                    'title' => 'Student Registration Payment',
                    'description' => "Registration fee for {$course->name} - {$request->fee_type}",
                    'logo' => config('app.logo_url', '')
                ]
            ];

            // Make request to Flutterwave
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/payments', $paymentData);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($responseData['status'] === 'success') {
                    // Update payment with Flutterwave response
                    $payment->update([
                        'gateway_reference' => $responseData['data']['id'] ?? null,
                        'gateway_response' => json_encode($responseData)
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Payment initialized successfully',
                        'data' => [
                            'payment_link' => $responseData['data']['link'],
                            'transaction_reference' => $txRef,
                            'payment_id' => $payment->id,
                            'expires_at' => now()->addHour()->toISOString()
                        ]
                    ]);
                }
            }

            // Handle failed initialization
            Log::error('Flutterwave payment initialization failed', [
                'response' => $response->json(),
                'status_code' => $response->status()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initialize payment. Please try again.'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Payment initialization error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request.'
            ], 500);
        }
    }

    /**
     * Handle Flutterwave callback
     */
    public function handleCallback(Request $request)
    {
        try {
            $txRef = $request->query('tx_ref');
            $transactionId = $request->query('transaction_id');

            if (!$txRef || !$transactionId) {
                return redirect()->route('payment.failed')
                    ->with('error', 'Invalid callback parameters');
            }

            // Find payment record
            $payment = Payment::where('transaction_reference', $txRef)->first();

            if (!$payment) {
                return redirect()->route('payment.failed')
                    ->with('error', 'Payment record not found');
            }

            // Verify transaction with Flutterwave
            $verification = $this->verifyTransaction($transactionId);

            if ($verification['status'] === 'success') {
                $transactionData = $verification['data'];

                if ($transactionData['status'] === 'successful' && 
                    $transactionData['tx_ref'] === $txRef) {
                    
                    // Get stored student data
                    $studentData = cache()->get("student_data_{$txRef}");
                    
                    if ($studentData) {
                        // Create student record
                        $student = $this->createStudentRecord($studentData, $txRef);
                        
                        // Record payment in transcripts table
                        $this->recordPaymentInTranscripts($student, $studentData, $transactionData);
                        
                        // Update payment with student_id
                        $payment->update([
                            'student_id' => $student->id,
                            'status' => 'completed',
                            'gateway_reference' => $transactionId,
                            'paid_at' => now(),
                            'gateway_response' => json_encode($transactionData)
                        ]);

                        // Clean up cached data
                        cache()->forget("student_data_{$txRef}");

                        return redirect()->route('registration.success')
                            ->with('success', 'Registration completed successfully!')
                            ->with('student', $student);
                    }
                }
            }

            // Payment failed or verification failed
            $payment->update([
                'status' => 'failed',
                'gateway_response' => json_encode($verification)
            ]);

            return redirect()->route('registration.failed')
                ->with('error', 'Payment verification failed');

        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage());
            
            return redirect()->route('registration.failed')
                ->with('error', 'An error occurred while processing your payment');
        }
    }

    /**
     * Verify transaction with Flutterwave
     */
    public function verifyTransaction($transactionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json'
            ])->get($this->baseUrl . "/transactions/{$transactionId}/verify");

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Transaction verification error: ' . $e->getMessage());
            
            return [
                'status' => 'error',
                'message' => 'Verification failed'
            ];
        }
    }

    /**
     * Handle webhook from Flutterwave
     */
    public function handleWebhook(Request $request)
    {
        try {
            // Verify webhook signature
            $signature = $request->header('verif-hash');
            
            if (!$signature || $signature !== config('services.flutterwave.webhook_hash')) {
                return response()->json(['status' => 'error'], 401);
            }

            $payload = $request->all();
            
            if ($payload['event'] === 'charge.completed') {
                $txRef = $payload['data']['tx_ref'];
                
                $payment = Payment::where('transaction_reference', $txRef)->first();
                
                if ($payment && $payment->status === 'pending') {
                    // Get stored student data
                    $studentData = cache()->get("student_data_{$txRef}");
                    
                    if ($studentData) {
                        // Create student record
                        $student = $this->createStudentRecord($studentData, $txRef);
                        
                        // Record payment in transcripts table
                        $this->recordPaymentInTranscripts($student, $studentData, $payload['data']);
                        
                        $payment->update([
                            'student_id' => $student->id,
                            'status' => 'completed',
                            'paid_at' => now(),
                            'gateway_response' => json_encode($payload['data'])
                        ]);

                        // Clean up cached data
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
     * Create student record with registration number
     */
    private function createStudentRecord($studentData, $txRef)
    {
        try {
            // Generate registration number
            $regNumber = $this->generateRegistrationNumber($studentData['branch_id'], $studentData['course_id']);
            
            // Move files from temp to permanent location
            $permanentPaths = $this->moveFilesToPermanentLocation($studentData, $regNumber);
            
            // Create student record
            $student = Student::create([
                'full_name' => $studentData['full_name'],
                'email' => $studentData['email'],
                'phone' => $studentData['phone'],
                'branch_id' => $studentData['branch_id'],
                'course_id' => $studentData['course_id'],
                'reg_number' => $regNumber,
                'password' => $studentData['password'],
                'senior6_document' => $permanentPaths['senior6_document'] ?? null,
                'senior3_document' => $permanentPaths['senior3_document'] ?? null,
                'other_supporting_document' => $permanentPaths['other_supporting_document'] ?? null,
                'id_photo' => $permanentPaths['id_photo'],
                'passport_photo' => $permanentPaths['passport_photo'],
            ]);

            // Send welcome email (optional)
            // Mail::to($student->email)->send(new StudentRegistrationConfirmation($student));

            return $student;

        } catch (\Exception $e) {
            Log::error('Student record creation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate unique registration number
     */
    private function generateRegistrationNumber($branchId, $courseId)
    {
        // Get branch and course codes (you might want to add these to your tables)
        $branch = DB::table('branches')->find($branchId);
        $course = DB::table('courses')->find($courseId);
        
        // Create a base code (you can customize this format)
        $branchCode = strtoupper(substr($branch->name ?? 'BR', 0, 3));
        $courseCode = strtoupper(substr($course->code ?? $course->name ?? 'CO', 0, 3));
        
        // Get current academic year
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $academicYear = $currentYear . '-' . $nextYear;
        
        // Get next sequence number for this year
        $lastStudent = Student::where('reg_number', 'like', "{$branchCode}{$courseCode}%{$academicYear}")
                            ->orderBy('reg_number', 'desc')
                            ->first();
        
        $sequence = 1;
        if ($lastStudent) {
            // Extract sequence number from last registration number
            preg_match('/(\d+)' . preg_quote($academicYear) . '$/', $lastStudent->reg_number, $matches);
            $sequence = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        }
        
        // Format: BRACO001-2025-2026
        return $branchCode . $courseCode . str_pad($sequence, 3, '0', STR_PAD_LEFT) . $academicYear;
    }

    /**
     * Move uploaded files to permanent location
     */
    private function moveFilesToPermanentLocation($studentData, $regNumber)
    {
        $permanentPaths = [];
        $studentFolder = 'students/' . $regNumber;
        
        foreach (['senior6_document', 'senior3_document', 'other_supporting_document'] as $docType) {
            if (isset($studentData[$docType])) {
                $tempPath = $studentData[$docType];
                $filename = $docType . '_' . time() . '.' . pathinfo($tempPath, PATHINFO_EXTENSION);
                $permanentPath = $studentFolder . '/documents/' . $filename;
                
                if (Storage::disk('public')->exists($tempPath)) {
                    Storage::disk('public')->move($tempPath, $permanentPath);
                    $permanentPaths[$docType] = $permanentPath;
                }
            }
        }
        
        foreach (['id_photo', 'passport_photo'] as $photoType) {
            if (isset($studentData[$photoType])) {
                $tempPath = $studentData[$photoType];
                $filename = $photoType . '_' . time() . '.' . pathinfo($tempPath, PATHINFO_EXTENSION);
                $permanentPath = $studentFolder . '/photos/' . $filename;
                
                if (Storage::disk('public')->exists($tempPath)) {
                    Storage::disk('public')->move($tempPath, $permanentPath);
                    $permanentPaths[$photoType] = $permanentPath;
                }
            }
        }
        
        return $permanentPaths;
    }

    /**
     * Record payment in transcripts table
     */
    private function recordPaymentInTranscripts($student, $studentData, $transactionData)
    {
        try {
            Transcript::create([
                'student_id' => $student->id,
                'course_id' => $student->course_id,
                'fee_type' => $studentData['fee_type'],
                'amount_paid' => $studentData['amount_paid'],
                'total_fee' => $studentData['total_fee'],
                'payment_method' => 'flutterwave',
                'payment_date' => now()->toDateString(),
                'term' => $studentData['term'] ?? null,
            ]);

            Log::info('Payment recorded in transcripts', [
                'student_id' => $student->id,
                'amount' => $studentData['amount_paid'],
                'fee_type' => $studentData['fee_type']
            ]);

        } catch (\Exception $e) {
            Log::error('Error recording payment in transcripts: ' . $e->getMessage(), [
                'student_id' => $student->id,
                'transaction_data' => $transactionData
            ]);
        }
    }

    /**
     * Add additional payment to existing student transcript
     */
    public function addPayment(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|exists:students,id',
                'amount' => 'required|numeric|min:1',
                'fee_type' => 'required|string',
                'total_fee' => 'required|numeric|min:1',
                'term' => 'nullable|string',
            ]);

            $student = Student::findOrFail($request->student_id);
            
            // Generate unique transaction reference
            $txRef = 'ADD_PAY_' . time() . '_' . Str::random(10);

            // Store payment data in cache
            $paymentData = [
                'student_id' => $student->id,
                'course_id' => $student->course_id,
                'fee_type' => $request->fee_type,
                'amount_paid' => $request->amount,
                'total_fee' => $request->total_fee,
                'term' => $request->term,
            ];
            
            cache()->put("additional_payment_{$txRef}", $paymentData, 3600);

            // Create payment record
            $payment = Payment::create([
                'student_id' => $student->id,
                'course_id' => $student->course_id,
                'amount' => $request->amount,
                'currency' => 'RWF',
                'transaction_reference' => $txRef,
                'status' => 'pending',
                'payment_method' => 'flutterwave',
                'metadata' => json_encode([
                    'fee_type' => $request->fee_type,
                    'student_name' => $student->full_name,
                    'additional_payment' => true,
                ])
            ]);

            // Prepare Flutterwave payment data
            $flutterwaveData = [
                'tx_ref' => $txRef,
                'amount' => $request->amount,
                'currency' => 'RWF',
                'redirect_url' => route('flutterwave.callback'),
                'payment_options' => 'card,banktransfer,ussd',
                'meta' => [
                    'payment_id' => $payment->id,
                    'student_id' => $student->id,
                    'additional_payment' => true
                ],
                'customer' => [
                    'email' => $student->email,
                    'phonenumber' => $student->phone,
                    'name' => $student->full_name
                ],
                'customizations' => [
                    'title' => 'Additional Payment',
                    'description' => "Additional {$request->fee_type} payment",
                    'logo' => config('app.logo_url', '')
                ]
            ];

            // Make request to Flutterwave
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/payments', $flutterwaveData);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($responseData['status'] === 'success') {
                    $payment->update([
                        'gateway_reference' => $responseData['data']['id'] ?? null,
                        'gateway_response' => json_encode($responseData)
                    ]);

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Additional payment initialized successfully',
                        'data' => [
                            'payment_link' => $responseData['data']['link'],
                            'transaction_reference' => $txRef,
                            'payment_id' => $payment->id
                        ]
                    ]);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initialize additional payment'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Additional payment initialization error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error processing additional payment request'
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

            $totalPaid = $paymentHistory->sum('amount_paid');
            $totalFees = $paymentHistory->sum('total_fee');
            $balance = $totalFees - $totalPaid;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->full_name,
                        'reg_number' => $student->reg_number,
                        'email' => $student->email,
                        'course' => $student->course->name,
                        'branch' => $student->branch->name ?? 'N/A'
                    ],
                    'payment_summary' => [
                        'total_paid' => $totalPaid,
                        'total_fees' => $totalFees,
                        'balance' => $balance
                    ],
                    'payment_history' => $paymentHistory->map(function ($payment) {
                        return [
                            'id' => $payment->id,
                            'fee_type' => $payment->fee_type,
                            'amount_paid' => $payment->amount_paid,
                            'total_fee' => $payment->total_fee,
                            'payment_method' => $payment->payment_method,
                            'payment_date' => $payment->payment_date,
                            'term' => $payment->term,
                            'created_at' => $payment->created_at
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching payment history: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving payment history'
            ], 500);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($transactionReference)
    {
        try {
            $payment = Payment::where('transaction_reference', $transactionReference)
                ->with(['student', 'course'])
                ->first();

            if (!$payment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'payment_status' => $payment->status,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'course' => $payment->course->name,
                    'student' => $payment->student->full_name,
                    'created_at' => $payment->created_at,
                    'paid_at' => $payment->paid_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get payment status error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving payment status'
            ], 500);
        }
    }

    /**
     * Refund payment
     */
    public function refundPayment(Request $request)
    {
        try {
            $request->validate([
                'transaction_id' => 'required|string',
                'amount' => 'sometimes|numeric|min:1'
            ]);

            $refundData = [
                'amount' => $request->amount ?? null // Full refund if amount not specified
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . "/transactions/{$request->transaction_id}/refund", $refundData);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($responseData['status'] === 'success') {
                    // Update payment record
                    $payment = Payment::where('gateway_reference', $request->transaction_id)->first();
                    
                    if ($payment) {
                        $payment->update([
                            'status' => 'refunded',
                            'refunded_at' => now(),
                            'refund_reference' => $responseData['data']['id'] ?? null
                        ]);
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Refund processed successfully',
                        'data' => $responseData['data']
                    ]);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Refund failed'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Refund error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error processing refund'
            ], 500);
        }
    }
}