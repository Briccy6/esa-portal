<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Subscription;
use App\Models\Client;
use App\Models\ClientSubscription;
use App\Models\ClientTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Flutterwavedev\Flutterwave\Flutterwave;

class PublicExamController extends Controller
{
    private $flutterwavePublicKey;
    private $flutterwaveSecretKey;
    private $flutterwaveBaseUrl;

    public function __construct()
    {
        $this->flutterwavePublicKey = config('services.flutterwave.public_key');
        $this->flutterwaveSecretKey = config('services.flutterwave.secret_key');
        $this->flutterwaveBaseUrl = 'https://api.flutterwave.com/v3';
    }

    /**
     * Display all publicly available exams
     */
    public function index()
    {
        $exams = Exam::where('is_public', true)
            ->where('is_active', true)
            ->with(['questions', 'academic_year', 'term', 'teacher', 'courses', 'lessons'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get available subscriptions for display
        $subscriptions = Subscription::active()
            ->orderBy('price')
            ->get();

        // Check if client is authenticated and has subscription
        $isClientAuthenticated = false;
        $clientHasActiveSubscription = false;
        
        try {
            if (Auth::guard('client')->check()) {
                $isClientAuthenticated = true;
                $clientHasActiveSubscription = Auth::guard('client')->user()->hasActiveSubscription();
            }
        } catch (\Exception $e) {
            // Guard not properly configured, continue without client auth
        }

        return view('public.exams.index', compact('exams', 'subscriptions', 'isClientAuthenticated', 'clientHasActiveSubscription'));
    }

    /**
     * Show exam details with payment options
     */
    public function show(Exam $exam)
    {
        if (!$exam->isPubliclyAccessible()) {
            abort(404, 'Exam not available for public access');
        }

        $exam->load(['questions', 'academic_year', 'term', 'teacher', 'courses', 'lessons']);
        
        // Get available subscriptions
        $subscriptions = Subscription::active()
            ->orderBy('price')
            ->get();

        // Check user access if logged in
        $userAccess = null;
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            $userAccess = $exam->canBeAttemptedByStudent($student);
        } elseif (Auth::guard('client')->check()) {
            $client = Auth::guard('client')->user();
            $userAccess = $exam->canBeAttemptedByClient($client);
        }

        // Get Flutterwave public key for frontend
        $flutterwavePublicKey = $this->flutterwavePublicKey;

        return view('public.exams.show', compact('exam', 'subscriptions', 'userAccess', 'flutterwavePublicKey'));
    }

    /**
     * Show payment page for exam attempt
     */
    public function showPayment(Exam $exam)
    {
        if (!$exam->requiresPayment()) {
            return redirect()->route('public.exams.show', $exam);
        }

        if (!$exam->isPubliclyAccessible()) {
            abort(404, 'Exam not available for public access');
        }

        // Get Flutterwave public key for frontend
        $flutterwavePublicKey = $this->flutterwavePublicKey;

        return view('public.exams.payment', compact('exam', 'flutterwavePublicKey'));
    }

    /**
     * Process payment for exam attempt using Flutterwave
     */
    public function processPayment(Request $request, Exam $exam)
    {
        $request->validate([
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'payment_method' => 'required|in:card,mobile_money,bank_transfer',
            'name' => 'required|string|max:255',
        ]);

        if (!$exam->requiresPayment()) {
            return redirect()->route('public.exams.show', $exam)
                ->with('error', 'This exam does not require payment');
        }

        try {
            // Generate unique transaction reference
            $txRef = 'EXAM_' . $exam->id . '_' . uniqid() . '_' . time();
            
            // Prepare payment data for Flutterwave
            $paymentData = [
                'tx_ref' => $txRef,
                'amount' => $exam->public_price,
                'currency' => 'RWF', // Rwandan Francs
                'payment_options' => $request->payment_method === 'mobile_money' ? 'mobilemoneyrwanda' : $request->payment_method,
                'redirect_url' => route('public.exams.payment.callback', $exam),
                'customer' => [
                    'email' => $request->email,
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                ],
                'customizations' => [
                    'title' => 'Exam Payment: ' . $exam->title,
                    'description' => 'Payment for exam: ' . $exam->title,
                ],
                'meta' => [
                    'exam_id' => $exam->id,
                    'exam_title' => $exam->title,
                    'payment_type' => 'exam_fee',
                ],
            ];

            // Initialize Flutterwave payment
            $response = $this->initializeFlutterwavePayment($paymentData);

            if (!$response['status']) {
                return back()->with('error', 'Payment initialization failed: ' . $response['message']);
            }

            // Store pending payment record
            $exam->examTokens()->create([
                'student_id' => null, // Will be updated when user registers/logs in
                'tokens_used' => 0,
                'payment_status' => 'pending',
                'payment_amount' => $exam->public_price,
                'payment_reference' => $txRef,
                'flutterwave_transaction_id' => $response['data']['link'],
                'customer_email' => $request->email,
                'customer_phone' => $request->phone_number,
                'customer_name' => $request->name,
            ]);

            // Redirect to Flutterwave payment page
            return redirect($response['data']['link']);

        } catch (\Exception $e) {
            Log::error('Exam payment initialization failed: ' . $e->getMessage());
            return back()->with('error', 'Payment initialization failed. Please try again.');
        }
    }

    /**
     * Handle Flutterwave payment callback
     */
    public function handlePaymentCallback(Request $request, Exam $exam)
    {
        $txRef = $request->query('tx_ref');
        $transactionId = $request->query('transaction_id');
        $status = $request->query('status');

        if ($status === 'successful' || $status === 'completed') {
            // Verify transaction with Flutterwave
            $verification = $this->verifyFlutterwaveTransaction($transactionId);

            if ($verification['status'] === 'successful') {
                // Update payment record
                $examToken = $exam->examTokens()
                    ->where('payment_reference', $txRef)
                    ->first();

                if ($examToken) {
                    $examToken->update([
                        'payment_status' => 'paid',
                        'flutterwave_transaction_id' => $transactionId,
                        'paid_at' => now(),
                    ]);
                }

                return redirect()->route('public.exams.payment.success', [$exam, 'reference' => $txRef])
                    ->with('success', 'Payment successful! You can now attempt this exam.');
            }
        }

        return redirect()->route('public.exams.show', $exam)
            ->with('error', 'Payment failed or was cancelled. Please try again.');
    }

    /**
     * Show payment success page
     */
    public function paymentSuccess(Exam $exam, Request $request)
    {
        $reference = $request->query('reference');
        
        return view('public.exams.payment-success', compact('exam', 'reference'));
    }

    /**
     * Start exam attempt (for public users)
     */
    public function startExam(Exam $exam)
    {
        if (!$exam->isPubliclyAccessible()) {
            abort(404, 'Exam not available for public access');
        }

        // For free exams, allow direct access without authentication
        if (!$exam->requiresPayment()) {
            // Create a guest session and redirect to public exam taking
            return redirect()->route('public.exams.take', $exam);
        }

        // For paid exams, check authentication
        if (!Auth::guard('client')->check() && !Auth::guard('student')->check()) {
            // No user logged in, redirect to client login
            return redirect()->route('client.auth.login')
                ->with('intent', 'exam_attempt')
                ->with('exam_id', $exam->id)
                ->with('info', 'Please login to access this paid exam');
        }

        // Check if user has already paid
        $hasPaid = false;
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            $hasPaid = $exam->examTokens()
                ->where('student_id', $student->id)
                ->where('payment_status', 'paid')
                ->exists();
        } elseif (Auth::guard('client')->check()) {
            $client = Auth::guard('client')->user();
            $hasPaid = $exam->examTokens()
                ->where('client_id', $client->id)
                ->where('payment_status', 'paid')
                ->exists();
        }

        if (!$hasPaid) {
            // Check if user has active subscription
            $hasSubscription = false;
            if (Auth::guard('student')->check()) {
                $student = Auth::guard('student')->user();
                $hasSubscription = $student->subscriptions()
                    ->where('status', 'active')
                    ->where('end_date', '>', now())
                    ->exists();
            } elseif (Auth::guard('client')->check()) {
                $client = Auth::guard('client')->user();
                $hasSubscription = $client->hasActiveSubscription();
            }

            if ($hasSubscription) {
                // User has subscription, allow access
                if (Auth::guard('student')->check()) {
                    return redirect()->route('exams.start', $exam);
                } else {
                    return redirect()->route('public.exams.take', $exam);
                }
            } else {
                // No subscription and no payment, redirect to payment
                return redirect()->route('public.exams.payment', $exam)
                    ->with('error', 'Payment or subscription required to attempt this exam');
            }
        }

        // User has paid, redirect to appropriate exam taking route
        if (Auth::guard('student')->check()) {
            return redirect()->route('exams.start', $exam);
        } else {
            return redirect()->route('public.exams.take', $exam);
        }
    }

    /**
     * Take exam for public users (including guests)
     */
    public function takeExam(Exam $exam)
    {
        if (!$exam->isPubliclyAccessible()) {
            abort(404, 'Exam not available for public access');
        }

        // For paid exams, ensure user is authenticated and has paid
        if ($exam->requiresPayment()) {
            if (!Auth::guard('client')->check() && !Auth::guard('student')->check()) {
                return redirect()->route('client.auth.login')
                    ->with('intent', 'exam_attempt')
                    ->with('exam_id', $exam->id)
                    ->with('error', 'Authentication required for paid exams');
            }

            // Check payment status (similar logic as startExam)
            $hasPaid = false;
            $hasSubscription = false;

            if (Auth::guard('student')->check()) {
                $student = Auth::guard('student')->user();
                $hasPaid = $exam->examTokens()
                    ->where('student_id', $student->id)
                    ->where('payment_status', 'paid')
                    ->exists();
                $hasSubscription = $student->subscriptions()
                    ->where('status', 'active')
                    ->where('end_date', '>', now())
                    ->exists();
            } elseif (Auth::guard('client')->check()) {
                $client = Auth::guard('client')->user();
                $hasPaid = $exam->examTokens()
                    ->where('client_id', $client->id)
                    ->where('payment_status', 'paid')
                    ->exists();
                $hasSubscription = $client->hasActiveSubscription();
            }

            if (!$hasPaid && !$hasSubscription) {
                return redirect()->route('public.exams.payment', $exam)
                    ->with('error', 'Payment or subscription required to attempt this exam');
            }
        }

        // Create or get exam session
        $sessionId = $this->createGuestSession($exam);
        
        return redirect()->route('public.exams.take.question', [$exam, $sessionId, 1]);
    }

    /**
     * Create guest session for exam taking
     */
    private function createGuestSession(Exam $exam)
    {
        $sessionId = 'guest_' . uniqid() . '_' . time();
        
        // Store session data in cache or session
        session([
            "exam_session_{$sessionId}" => [
                'exam_id' => $exam->id,
                'start_time' => now(),
                'user_type' => Auth::guard('student')->check() ? 'student' : 
                              (Auth::guard('client')->check() ? 'client' : 'guest'),
                'user_id' => Auth::guard('student')->check() ? Auth::guard('student')->id() : 
                         (Auth::guard('client')->check() ? Auth::guard('client')->id() : null),
                'answers' => [],
                'current_question' => 1,
                'completed' => false
            ]
        ]);
        
        return $sessionId;
    }

    /**
     * Show specific question for public exam taking
     */
    public function takeQuestion(Exam $exam, $sessionId, $questionNumber = 1)
    {
        if (!$exam->isPubliclyAccessible()) {
            abort(404, 'Exam not available for public access');
        }

        // Get session data
        $sessionData = session("exam_session_{$sessionId}");
        if (!$sessionData || $sessionData['exam_id'] != $exam->id) {
            return redirect()->route('public.exams.start', $exam)
                ->with('error', 'Invalid session. Please start the exam again.');
        }

        // Get questions
        $questions = $exam->questions()->orderBy('id')->get();
        $totalQuestions = $questions->count();
        
        if ($questionNumber > $totalQuestions) {
            return redirect()->route('public.exams.submit', [$exam, $sessionId]);
        }

        $currentQuestion = $questions[$questionNumber - 1];
        
        return view('public.exams.take', compact('exam', 'currentQuestion', 'questionNumber', 'totalQuestions', 'sessionId', 'sessionData'));
    }

    /**
     * Save answer for public exam taking
     */
    public function saveAnswer(Request $request, Exam $exam, $sessionId, $questionNumber)
    {
        if (!$exam->isPubliclyAccessible()) {
            abort(404, 'Exam not available for public access');
        }

        $request->validate([
            'answer' => 'required|string',
        ]);

        // Get and update session data
        $sessionData = session("exam_session_{$sessionId}");
        if (!$sessionData || $sessionData['exam_id'] != $exam->id) {
            return response()->json(['error' => 'Invalid session'], 400);
        }

        $sessionData['answers'][$questionNumber] = $request->answer;
        session(["exam_session_{$sessionId}" => $sessionData]);

        return response()->json(['success' => true]);
    }

    /**
     * Submit public exam
     */
    public function submitExam(Request $request, Exam $exam, $sessionId)
    {
        if (!$exam->isPubliclyAccessible()) {
            abort(404, 'Exam not available for public access');
        }

        // Get session data
        $sessionData = session("exam_session_{$sessionId}");
        if (!$sessionData || $sessionData['exam_id'] != $exam->id) {
            return redirect()->route('public.exams.start', $exam)
                ->with('error', 'Invalid session. Please start the exam again.');
        }

        // Mark as completed
        $sessionData['completed'] = true;
        $sessionData['end_time'] = now();
        session(["exam_session_{$sessionId}" => $sessionData]);

        // Calculate results
        $questions = $exam->questions()->orderBy('id')->get();
        $correctAnswers = 0;
        $totalQuestions = $questions->count();

        foreach ($questions as $index => $question) {
            $questionNumber = $index + 1;
            if (isset($sessionData['answers'][$questionNumber]) && 
                $sessionData['answers'][$questionNumber] === $question->correct_answer) {
                $correctAnswers++;
            }
        }

        $score = ($correctAnswers / $totalQuestions) * 100;

        return view('public.exams.results', compact('exam', 'score', 'correctAnswers', 'totalQuestions', 'sessionData'));
    }

    /**
     * API endpoint to check exam access
     */
    public function checkAccess(Exam $exam)
    {
        if (!$exam->isPubliclyAccessible()) {
            return response()->json([
                'accessible' => false,
                'reason' => 'Exam not available for public access'
            ], 403);
        }

        $response = [
            'accessible' => true,
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'requires_payment' => $exam->requiresPayment(),
                'payment_amount' => $exam->public_price,
                'requires_tokens' => $exam->requiresTokens(),
                'token_cost' => $exam->getCalculatedTokenCost(),
                'question_count' => $exam->questions()->count(),
            ]
        ];

        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            $access = $exam->canBeAttemptedByStudent($student);
            $response['user_access'] = $access;
        }

        return response()->json($response);
    }

    /**
     * Initialize Flutterwave payment
     */
    private function initializeFlutterwavePayment($data)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->flutterwaveBaseUrl . '/payments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->flutterwaveSecretKey,
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new \Exception('cURL Error: ' . $err);
        }

        return json_decode($response, true);
    }

    /**
     * Verify Flutterwave transaction
     */
    private function verifyFlutterwaveTransaction($transactionId)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->flutterwaveBaseUrl . '/transactions/' . $transactionId . '/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->flutterwaveSecretKey,
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new \Exception('cURL Error: ' . $err);
        }

        return json_decode($response, true);
    }
}
