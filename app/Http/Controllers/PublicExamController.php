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
            ->with(['questions', 'academic_year', 'term', 'teacher', 'courses', 'lessons', 'course', 'lesson'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $subscriptions = Subscription::active()
            ->orderBy('price')
            ->get();

        $isClientAuthenticated        = false;
        $clientHasActiveSubscription  = false;

        try {
            if (Auth::guard('client')->check()) {
                $isClientAuthenticated       = true;
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
        // Allow public viewing of any exam that's marked public and active
        if (!$exam->is_public || !$exam->is_active) {
            abort(404, 'Exam not available for public access');
        }

        $exam->load(['questions', 'academic_year', 'term', 'teacher', 'courses', 'lessons', 'course', 'lesson']);

        $subscriptions = Subscription::active()
            ->orderBy('price')
            ->get();

        $userAccess = null;
        if (Auth::guard('student')->check()) {
            $student    = Auth::guard('student')->user();
            $userAccess = $exam->canBeAttemptedByStudent($student);
        } elseif (Auth::guard('client')->check()) {
            $client     = Auth::guard('client')->user();
            $userAccess = $exam->canBeAttemptedByClient($client);
        }

        $flutterwavePublicKey = $this->flutterwavePublicKey;

        return view('public.exams.show', compact('exam', 'subscriptions', 'userAccess', 'flutterwavePublicKey'));
    }

    /**
     * Show payment page for exam attempt
     */
    public function showPayment(Exam $exam)
    {
        // Only allow payment for public exams
        if (!$exam->is_public) {
            abort(404, 'Exam not available for public access');
        }

        if (!$exam->requiresPayment()) {
            return redirect()->route('public.exams.show', $exam);
        }

        $flutterwavePublicKey = $this->flutterwavePublicKey;
        $systemName           = config('app.name', 'ESA E-Learning');

        return view('public.exams.payment', compact('exam', 'flutterwavePublicKey', 'systemName'));
    }

    /**
     * Process payment for exam attempt using Flutterwave
     */
    public function processPayment(Request $request, Exam $exam)
    {
        $request->validate([
            'email'          => 'required|email',
            'phone_number'   => 'required|string',
            'payment_method' => 'required|in:card,mobile_money,bank_transfer',
            'name'           => 'required|string|max:255',
        ]);

        if (!$exam->requiresPayment()) {
            return redirect()->route('public.exams.show', $exam)
                ->with('error', 'This exam does not require payment');
        }

        try {
            $txRef = 'EXAM_' . $exam->id . '_' . uniqid() . '_' . time();

            $paymentData = [
                'tx_ref'          => $txRef,
                'amount'          => $exam->public_price,
                'currency'        => 'RWF',
                'payment_options' => $request->payment_method === 'mobile_money' ? 'mobilemoneyrwanda' : $request->payment_method,
                'redirect_url'    => route('public.exams.payment.callback', $exam),
                'customer'        => [
                    'email'        => $request->email,
                    'name'         => $request->name,
                    'phone_number' => $request->phone_number,
                ],
                'customizations' => [
                    'title'       => 'Exam Payment: ' . $exam->title,
                    'description' => 'Payment for exam: ' . $exam->title,
                ],
                'meta' => [
                    'exam_id'      => $exam->id,
                    'exam_title'   => $exam->title,
                    'payment_type' => 'exam_fee',
                ],
            ];

            $response = $this->initializeFlutterwavePayment($paymentData);

            if (!$response['status']) {
                return back()->with('error', 'Payment initialization failed: ' . $response['message']);
            }

            $exam->examTokens()->create([
                'student_id'                 => null,
                'tokens_used'                => 0,
                'payment_status'             => 'pending',
                'payment_amount'             => $exam->public_price,
                'payment_reference'          => $txRef,
                'flutterwave_transaction_id' => $response['data']['link'],
                'customer_email'             => $request->email,
                'customer_phone'             => $request->phone_number,
                'customer_name'              => $request->name,
            ]);

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
        $txRef         = $request->query('tx_ref');
        $transactionId = $request->query('transaction_id');
        $status        = $request->query('status');

        if ($status === 'successful' || $status === 'completed') {
            $verification = $this->verifyFlutterwaveTransaction($transactionId);

            if ($verification['status'] === 'successful') {
                $examToken = $exam->examTokens()
                    ->where('payment_reference', $txRef)
                    ->first();

                if ($examToken) {
                    $examToken->update([
                        'payment_status'             => 'paid',
                        'flutterwave_transaction_id' => $transactionId,
                        'paid_at'                    => now(),
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

        if (!$exam->requiresPayment()) {
            return redirect()->route('public.exams.take', $exam);
        }

        if (!Auth::guard('client')->check() && !Auth::guard('student')->check()) {
            return redirect()->route('client.auth.login')
                ->with('intent', 'exam_attempt')
                ->with('exam_id', $exam->id)
                ->with('info', 'Please login to access this paid exam');
        }

        $hasPaid = false;
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            $hasPaid = $exam->examTokens()
                ->where('student_id', $student->id)
                ->where('payment_status', 'paid')
                ->exists();
        } elseif (Auth::guard('client')->check()) {
            $client  = Auth::guard('client')->user();
            $hasPaid = $exam->examTokens()
                ->where('client_id', $client->id)
                ->where('payment_status', 'paid')
                ->exists();
        }

        if (!$hasPaid) {
            $hasSubscription = false;
            if (Auth::guard('student')->check()) {
                $student         = Auth::guard('student')->user();
                $hasSubscription = $student->subscriptions()
                    ->where('status', 'active')
                    ->where('end_date', '>', now())
                    ->exists();
            } elseif (Auth::guard('client')->check()) {
                $client          = Auth::guard('client')->user();
                $hasSubscription = $client->hasActiveSubscription();
            }

            if ($hasSubscription) {
                if (Auth::guard('student')->check()) {
                    return redirect()->route('exams.start', $exam);
                } else {
                    return redirect()->route('public.exams.take', $exam);
                }
            } else {
                return redirect()->route('public.exams.payment', $exam)
                    ->with('error', 'Payment or subscription required to attempt this exam');
            }
        }

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

        if ($exam->requiresPayment()) {
            if (!Auth::guard('client')->check() && !Auth::guard('student')->check()) {
                return redirect()->route('client.auth.login')
                    ->with('intent', 'exam_attempt')
                    ->with('exam_id', $exam->id)
                    ->with('error', 'Authentication required for paid exams');
            }

            $hasPaid         = false;
            $hasSubscription = false;

            if (Auth::guard('student')->check()) {
                $student         = Auth::guard('student')->user();
                $hasPaid         = $exam->examTokens()
                    ->where('student_id', $student->id)
                    ->where('payment_status', 'paid')
                    ->exists();
                $hasSubscription = $student->subscriptions()
                    ->where('status', 'active')
                    ->where('end_date', '>', now())
                    ->exists();
            } elseif (Auth::guard('client')->check()) {
                $client          = Auth::guard('client')->user();
                $hasPaid         = $exam->examTokens()
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

        $sessionId = $this->createGuestSession($exam);

        return redirect()->route('public.exams.take.question', [$exam, $sessionId, 1]);
    }

    /**
     * Create guest session for exam taking
     */
    private function createGuestSession(Exam $exam)
    {
        $sessionId = 'guest_' . uniqid() . '_' . time();

        $sessionData = [
            'exam_id'          => $exam->id,
            'start_time'       => now()->toISOString(),
            'user_type'        => 'guest',
            'user_id'          => null,
            'answers'          => [],
            'flagged'          => [],
            'current_question' => 1,
            'total_questions'  => $exam->questions()->count(),
        ];

        session(["exam_session_{$sessionId}" => $sessionData]);

        \Log::info('Guest exam session created', [
            'session_id'  => $sessionId,
            'exam_id'     => $exam->id,
            'session_key' => "exam_session_{$sessionId}",
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

        $sessionKey  = "exam_session_{$sessionId}";
        $sessionData = session($sessionKey);

        if (!$sessionData) {
            \Log::error('Session data not found', [
                'session_id'   => $sessionId,
                'session_key'  => $sessionKey,
                'all_sessions' => array_keys(session()->all()),
            ]);

            if (request()->ajax()) {
                return response()->json(['error' => 'Session expired'], 419);
            }
            return redirect()->route('public.exams.start', $exam)
                ->with('error', 'Session expired. Please start the exam again.');
        }

        if ($sessionData['exam_id'] != $exam->id) {
            \Log::error('Session exam ID mismatch', [
                'session_exam_id'    => $sessionData['exam_id'],
                'requested_exam_id'  => $exam->id,
                'session_id'         => $sessionId,
            ]);

            if (request()->ajax()) {
                return response()->json(['error' => 'Invalid session'], 419);
            }
            return redirect()->route('public.exams.start', $exam)
                ->with('error', 'Invalid session. Please start the exam again.');
        }

        $questions      = $exam->questions()->orderBy('id')->get();
        $totalQuestions = $questions->count();

        if ($questionNumber < 1 || $questionNumber > $totalQuestions) {
            \Log::error('Invalid question number', [
                'question_number'  => $questionNumber,
                'total_questions'  => $totalQuestions,
                'session_id'       => $sessionId,
            ]);

            if (request()->ajax()) {
                return response()->json(['redirect' => route('public.exams.submit', [$exam, $sessionId])]);
            }
            return redirect()->route('public.exams.submit', [$exam, $sessionId]);
        }

        $currentQuestion                   = $questions[$questionNumber - 1];
        $sessionData['current_question']   = $questionNumber;
        session([$sessionKey => $sessionData]);

        if (request()->ajax()) {
            return response()->json([
                'success'         => true,
                'question'        => $currentQuestion,
                'currentQuestion' => $questionNumber,
                'totalQuestions'  => $totalQuestions,
                'sessionData'     => $sessionData,
            ]);
        }

        return view('public.exams.take', compact('exam', 'currentQuestion', 'questionNumber', 'totalQuestions', 'sessionId', 'sessionData'));
    }

    /**
     * Save answer for public exam
     */
    public function saveAnswer(Request $request, Exam $exam, $sessionId, $questionNumber)
    {
        if (!$exam->isPubliclyAccessible()) {
            abort(404, 'Exam not available for public access');
        }

        $request->validate([
            'answer' => 'required|string',
        ]);

        $sessionKey  = "exam_session_{$sessionId}";
        $sessionData = session($sessionKey);

        \Log::info("Save Answer Debug", [
            'sessionKey'     => $sessionKey,
            'sessionId'      => $sessionId,
            'questionNumber' => $questionNumber,
            'answer'         => $request->answer,
            'sessionData'    => $sessionData,
            'hasSessionData' => !empty($sessionData),
        ]);

        if (!$sessionData || $sessionData['exam_id'] != $exam->id) {
            \Log::error("Invalid session for saveAnswer", ['sessionData' => $sessionData]);
            return response()->json(['error' => 'Invalid session'], 400);
        }

        $sessionData['answers'][$questionNumber] = $request->answer;
        session([$sessionKey => $sessionData]);

        \Log::info("Answer saved successfully", ['answers' => $sessionData['answers']]);

        return response()->json(['success' => true]);
    }

    /**
     * Submit public exam
     */
    public function submitExam(Request $request, Exam $exam, $sessionId)
    {
        // Only allow POST requests
        if (!$request->isMethod('post')) {
            return redirect()->route('public.exams.take.question', [$exam, $sessionId, 1])
                ->with('error', 'Invalid request method. Please use the submit button.');
        }
        
        if (!$exam->isPubliclyAccessible()) {
            abort(404, 'Exam not available for public access');
        }

        $sessionData = session("exam_session_{$sessionId}");
        if (!$sessionData || $sessionData['exam_id'] != $exam->id) {
            return redirect()->route('public.exams.start', $exam)
                ->with('error', 'Invalid session. Please start the exam again.');
        }

        $sessionData['completed'] = true;
        $sessionData['end_time']  = now();
        session(["exam_session_{$sessionId}" => $sessionData]);

        $questions      = $exam->questions()->orderBy('id')->get();
        $correctAnswers = 0;
        $totalQuestions = $questions->count();

        foreach ($questions as $index => $question) {
            $questionNumber = $index + 1;
            if (isset($sessionData['answers'][$questionNumber]) &&
                $sessionData['answers'][$questionNumber] === $question->correct_answer) {
                $correctAnswers++;
            }
        }

        $score      = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
        $passed     = $score >= ($exam->passing_score ?? 50);

        // Load exam with relationships for results display
        $exam->load(['courses', 'lessons', 'academic_year', 'term', 'teacher']);

        return view('public.exams.results', compact(
            'exam', 'sessionId', 'sessionData',
            'correctAnswers', 'totalQuestions', 'score', 'passed'
        ));
    }

    /**
     * Initialize Flutterwave payment
     */
    private function initializeFlutterwavePayment($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api.flutterwave.com/v3/payments',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->flutterwaveSecretKey,
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($err) {
            Log::error('Flutterwave cURL Error: ' . $err);
            throw new \Exception('cURL Error: ' . $err);
        }

        Log::info('Flutterwave Response HTTP Code: ' . $httpCode);
        Log::info('Flutterwave Response: ' . $response);

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Flutterwave JSON Decode Error: ' . json_last_error_msg());
            throw new \Exception('Invalid JSON response from Flutterwave');
        }

        return $decodedResponse;
    }

    /**
     * Verify Flutterwave transaction
     */
    private function verifyFlutterwaveTransaction($transactionId)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->flutterwaveBaseUrl . '/transactions/' . $transactionId . '/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->flutterwaveSecretKey,
            ],
        ]);

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new \Exception('cURL Error: ' . $err);
        }

        return json_decode($response, true);
    }
}