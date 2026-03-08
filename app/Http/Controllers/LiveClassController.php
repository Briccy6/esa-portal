<?php

namespace App\Http\Controllers;

use App\Models\LiveClass;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LiveClassController extends Controller
{
    public function index()
    {
        $liveClasses = LiveClass::with(['branches', 'courses', 'lessons', 'teacher'])
            ->orderBy('start_time', 'desc')
            ->paginate(10);
        
        return view('admin.live-classes.index', compact('liveClasses'));
    }

    /**
     * Student-specific live classes index - only shows enrolled courses
     */
    public function studentIndex()
    {
        $student = Auth::guard('student')->user();
        
        $liveClasses = LiveClass::whereHas('courses', function($query) use ($student) {
            $query->where('course_id', $student->course_id);
        })
        ->with(['branches', 'courses', 'lessons', 'teacher'])
        ->orderBy('start_time', 'desc')
        ->paginate(10);

        return view('students.live-classes.index', compact('liveClasses'));
    }

    public function homepageLiveClasses()
    {
        $query = LiveClass::with(['branches', 'courses', 'lessons', 'teacher'])
            ->whereIn('status', ['scheduled', 'active'])
            ->where('end_time', '>=', now())
            ->orderBy('start_time', 'asc');
        
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            $query->whereHas('courses', function($q) use ($student) {
                $q->where('courses.id', $student->course_id);
            });
        }
        
        $liveClasses = $query->get();
            
        \Log::info('Homepage Live Classes Query', [
            'current_time' => now(),
            'student_logged_in' => Auth::guard('student')->check(),
            'count' => $liveClasses->count(),
        ]);
            
        return response()->json([
            'liveClasses' => $liveClasses,
            'count' => $liveClasses->count()
        ]);
    }

    public function checkClassAccess(LiveClass $liveClass)
    {
        $user = Auth::user();
        $response = [
            'canJoin' => false,
            'requiresPayment' => false,
            'paymentAmount' => 0,
            'currency' => 'USD',
            'reason' => '',
            'userType' => 'guest'
        ];

        if (!$user) {
            if ($liveClass->public_fee == 0 && ($liveClass->access_type === 'public' || $liveClass->access_type === 'both')) {
                $response['canJoin'] = true;
                $response['reason'] = 'Access granted - free public class';
                return response()->json($response);
            }
            
            $response['requiresPayment'] = $liveClass->access_type === 'public' || $liveClass->access_type === 'both';
            $response['paymentAmount'] = $liveClass->public_fee;
            $response['currency'] = $liveClass->currency;
            $response['reason'] = $response['requiresPayment'] ? 'Payment required for public access' : 'Please login to access this class';
            return response()->json($response);
        }

        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            $response['userType'] = 'student';
            
            if (!$liveClass->courses->contains('id', $student->course_id)) {
                $response['reason'] = 'This is not your course';
                return response()->json($response);
            }
            
            $approvedRequest = $liveClass->requests()
                ->where('user_id', $student->id)
                ->where('user_type', 'student')
                ->where('status', 'approved')
                ->first();
                
            if ($approvedRequest) {
                $response['canJoin'] = true;
                $response['reason'] = 'Access granted - request approved';
                return response()->json($response);
            }
            
            if ($liveClass->access_type === 'public' || $liveClass->access_type === 'both') {
                if ($liveClass->public_fee == 0) {
                    $response['canJoin'] = true;
                    $response['reason'] = 'Access granted - free class';
                    return response()->json($response);
                }
                
                $payment = \App\Models\LiveClassPayment::where('live_class_id', $liveClass->id)
                    ->where('user_id', $student->id)
                    ->where('user_type', 'student')
                    ->where('status', 'completed')
                    ->first();
                
                if (!$payment && $liveClass->public_fee > 0) {
                    $response['requiresPayment'] = true;
                    $response['paymentAmount'] = $liveClass->public_fee;
                    $response['currency'] = $liveClass->currency;
                    $response['reason'] = 'Payment required to join this class';
                    return response()->json($response);
                }
            }
            
            $response['canJoin'] = true;
            $response['reason'] = 'Access granted';
            return response()->json($response);
        }

        if ($liveClass->access_type === 'public' || $liveClass->access_type === 'both') {
            $response['userType'] = 'public';
            $response['requiresPayment'] = $liveClass->public_fee > 0;
            $response['paymentAmount'] = $liveClass->public_fee;
            $response['currency'] = $liveClass->currency;
            
            if ($response['requiresPayment']) {
                $hasPaid = \App\Models\LiveClassPayment::where('live_class_id', $liveClass->id)
                    ->where('email', $user->email)
                    ->where('status', 'completed')
                    ->exists();
                    
                if ($hasPaid) {
                    $response['canJoin'] = true;
                    $response['requiresPayment'] = false;
                } else {
                    $response['reason'] = 'Payment required to join this class';
                }
            } else {
                $response['canJoin'] = true;
            }
        } else {
            $response['reason'] = 'This class is for students only';
        }

        return response()->json($response);
    }

    public function join(LiveClass $liveClass)
    {
        $user = Auth::user();
        
        if ($liveClass->device_restriction) {
            $existingSession = \App\Models\LiveClassParticipant::where('live_class_id', $liveClass->id)
                ->where('email', $user->email)
                ->where('is_active', true)
                ->where('left_at', null)
                ->first();
                
            if ($existingSession) {
                return redirect()->back()->with('error', 'You already have an active session for this class. Please close the existing session first.');
            }
        }

        \App\Models\LiveClassParticipant::create([
            'live_class_id' => $liveClass->id,
            'user_id' => $user->id,
            'user_type' => Auth::guard('student')->check() ? 'student' : (Auth::guard('teacher')->check() ? 'teacher' : 'admin'),
            'email' => $user->email,
            'name' => $user->name ?? $user->first_name . ' ' . $user->last_name,
            'joined_at' => now(),
            'is_active' => true,
            'ip_address' => request()->ip(),
            'browser_info' => request()->userAgent(),
        ]);

        return redirect()->route('live-classes.room', $liveClass);
    }

    public function processPayment(Request $request, LiveClass $liveClass)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'nullable|string|max:20',
            'payment_method' => 'required|string|in:card,mobile_money,bank_transfer,ussd',
            'network'        => 'required_if:payment_method,mobile_money|nullable|string',
        ]);

        $tx_ref = 'LC_' . $liveClass->id . '_' . time() . '_' . Str::random(8);

        // Normalise currency to uppercase (db may store 'Rwf' instead of 'RWF')
        $currency = strtoupper(trim($liveClass->currency));

        $payment = \App\Models\LiveClassPayment::create([
            'live_class_id'   => $liveClass->id,
            'user_type'       => 'public',
            'email'           => $request->email,
            'name'            => $request->name,
            'phone'           => $request->phone,
            'amount'          => $liveClass->public_fee,
            'currency'        => $currency,
            'payment_method'  => $request->payment_method,
            'transaction_id'  => $tx_ref,
            'status'          => 'pending',
            'payment_gateway' => 'flutterwave',
        ]);

        try {
            // Bootstrap MUST be called first – it binds the config into the
            // service container that every SDK service class depends on.
            \Flutterwave\Flutterwave::bootstrap();

            // ----------------------------------------------------------------
            // Map the user's selected method → the SDK factory key that
            // Flutterwave::create() understands.
            // ----------------------------------------------------------------
            $selectedMethod        = $request->payment_method;
            $mobileMoneyCurrencies = ['RWF', 'GHS', 'UGX', 'XAF', 'ZMW', 'TZS', 'KES'];
            $cardCurrencies        = ['USD', 'EUR', 'GBP', 'NGN'];

            if ($selectedMethod === 'card') {
                // Fall back to mobile money when currency doesn't support cards
                $sdkKey         = in_array($currency, $cardCurrencies) ? 'card' : 'mobilemoney';
                $paymentOptions = in_array($currency, $cardCurrencies) ? 'card' : 'mobilemoney';
            } elseif ($selectedMethod === 'mobile_money') {
                $sdkKey         = 'mobilemoney';
                $paymentOptions = 'mobilemoney';
            } elseif ($selectedMethod === 'bank_transfer') {
                $sdkKey         = 'banktransfer';
                $paymentOptions = 'banktransfer';
            } elseif ($selectedMethod === 'ussd') {
                $sdkKey         = 'ussd';
                $paymentOptions = 'ussd';
            } else {
                $sdkKey         = in_array($currency, $mobileMoneyCurrencies) ? 'mobilemoney' : 'card';
                $paymentOptions = $sdkKey;
            }

            \Log::info('Flutterwave payment init', [
                'selected_method' => $selectedMethod,
                'currency'        => $currency,
                'sdk_key'         => $sdkKey,
                'payment_options' => $paymentOptions,
                'tx_ref'          => $tx_ref,
            ]);

            // ----------------------------------------------------------------
            // Build the payload
            // ----------------------------------------------------------------
            $payload = new \Flutterwave\Entities\Payload();
            $payload->txref           = $tx_ref;
            $payload->amount          = $liveClass->public_fee;
            $payload->currency        = $currency;
            $payload->payment_options = $paymentOptions;
            $payload->redirect_url    = route('live-classes.payment.callback', $liveClass);

            $payload->customer = [
                'email' => $request->email,
                'name'  => $request->name,
                'phone' => $request->phone ?? '',
            ];

            $payload->meta = [
                'live_class_id' => $liveClass->id,
                'payment_id'    => $payment->id,
                'user_type'     => 'public',
                'type'          => 'live_class_payment',
            ];

            $payload->customizations = [
                'title'       => "Live Class: {$liveClass->title}",
                'description' => "Payment for live class - {$liveClass->title}",
                'logo'        => asset('images/logo.png'),
            ];

            // ----------------------------------------------------------------
            // Mobile Money extras
            // ----------------------------------------------------------------
            if ($sdkKey === 'mobilemoney') {
                $networkMap = [
                    'RWF' => ['MTN', 'AIRTEL', 'TIGO'],
                    'GHS' => ['MTN', 'VODAFONE', 'TIGO'],
                    'UGX' => ['MTN', 'AIRTEL'],
                    'XAF' => ['MTN', 'ORANGE', 'VODAFONE'],
                    'ZMW' => ['MTN', 'ZAMTEL'],
                    'TZS' => ['AIRTEL', 'TIGO', 'HALOPESA', 'VODAFONE'],
                    'KES' => ['MPESA'],
                ];

                $availableNetworks = $networkMap[$currency] ?? ['MTN'];
                $selectedNetwork   = strtoupper($request->network ?? $availableNetworks[0]);

                if (!in_array($selectedNetwork, $availableNetworks)) {
                    $selectedNetwork = $availableNetworks[0];
                }

                $payload->network     = $selectedNetwork;
                $payload->phonenumber = $request->phone ?? '';

                \Log::info('Mobile Money details', [
                    'network' => $selectedNetwork,
                    'phone'   => $request->phone ?? 'none',
                ]);
            }

            // ----------------------------------------------------------------
            // USSD extras
            // ----------------------------------------------------------------
            if ($sdkKey === 'ussd') {
                $payload->account_bank = $request->bank_code ?? '058';
                $payload->phonenumber  = $request->phone ?? '';
            }

            // ----------------------------------------------------------------
            // Use the SDK factory AFTER bootstrap so the IoC container has the
            // config bound and every service gets it injected correctly.
            // ----------------------------------------------------------------
            $paymentService = \Flutterwave\Flutterwave::create($sdkKey);

            \Log::info('Flutterwave service created: ' . get_class($paymentService));

            $result = $paymentService->initiate($payload);

            \Log::info('Flutterwave initiate response', ['result' => $result]);

            if (isset($result['status']) && $result['status'] === 'success') {
                $payment->update(['gateway_response' => $result]);

                return response()->json([
                    'success'      => true,
                    'redirect_url' => $result['data']['link'],
                    'tx_ref'       => $tx_ref,
                ]);
            }

            $payment->update([
                'status'           => 'failed',
                'gateway_response' => $result,
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to initialize payment. Please try again.',
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Flutterwave payment initialization failed for live class', [
                'error'         => $e->getMessage(),
                'trace'         => $e->getTraceAsString(),
                'live_class_id' => $liveClass->id,
                'payment_id'    => $payment->id,
            ]);

            $payment->update([
                'status'           => 'failed',
                'gateway_response' => ['error' => $e->getMessage()],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function showPaymentForm(LiveClass $liveClass)
    {
        return view('live-classes.public-payment', compact('liveClass'));
    }

    /**
     * Handle Flutterwave payment callback for live class payments
     */
    public function handlePaymentCallback(Request $request, LiveClass $liveClass)
    {
        // Flutterwave sends transaction_id on success, tx_ref on cancel/failure
        $tx_ref         = $request->query('tx_ref');
        $transactionId  = $request->query('transaction_id');
        $status         = $request->query('status');

        \Log::info('Payment callback received', [
            'tx_ref'         => $tx_ref,
            'transaction_id' => $transactionId,
            'status'         => $status,
        ]);

        if (!$tx_ref) {
            return redirect()->route('live-classes.public-join', $liveClass)
                ->with('error', 'Payment verification failed. No transaction reference found.');
        }

        // Handle cancelled payments early
        if ($status === 'cancelled') {
            $payment = \App\Models\LiveClassPayment::where('transaction_id', $tx_ref)->first();
            if ($payment) {
                $payment->update(['status' => 'cancelled']);
            }
            return redirect()->route('live-classes.payment', $liveClass)
                ->with('error', 'Payment was cancelled. Please try again.');
        }

        try {
            \Flutterwave\Flutterwave::bootstrap();

            $payment = \App\Models\LiveClassPayment::where('transaction_id', $tx_ref)->first();

            if (!$payment) {
                return redirect()->route('live-classes.public-join', $liveClass)
                    ->with('error', 'Payment record not found.');
            }

            // ----------------------------------------------------------------
            // Use the SDK factory to get a Transactions service – bootstrap()
            // must be called first so the config is bound in the container.
            // The 'transactions' key works for ALL payment types.
            // ----------------------------------------------------------------
            $transactionService = \Flutterwave\Flutterwave::create('transactions');
            $transaction        = $transactionService->verify($tx_ref);

            \Log::info('Payment verification response', ['transaction' => $transaction]);

            if (isset($transaction['status']) && $transaction['status'] === 'success') {
                $txData = $transaction['data'];

                // Double-check amount and currency to prevent fraud
                if (
                    (float) $txData['amount'] >= (float) $payment->amount &&
                    strtoupper($txData['currency']) === strtoupper($payment->currency)
                ) {
                    $payment->update([
                        'status'           => 'completed',
                        'paid_at'          => now(),
                        'gateway_response' => $txData,
                    ]);

                    session(['live_class_payment_' . $liveClass->id => [
                        'email'      => $payment->email,
                        'name'       => $payment->name,
                        'phone'      => $payment->phone,
                        'payment_id' => $payment->id,
                    ]]);

                    return redirect()->route('live-classes.room', $liveClass)
                        ->with('success', 'Payment successful! You can now join the live class.');
                }

                // Amount/currency mismatch
                $payment->update([
                    'status'           => 'failed',
                    'gateway_response' => array_merge($txData, ['fraud_check' => 'amount_mismatch']),
                ]);

                return redirect()->route('live-classes.public-join', $liveClass)
                    ->with('error', 'Payment verification failed – amount mismatch. Please contact support.');
            }

            $payment->update([
                'status'           => 'failed',
                'gateway_response' => $transaction,
            ]);

            return redirect()->route('live-classes.public-join', $liveClass)
                ->with('error', 'Payment failed or was cancelled.');

        } catch (\Exception $e) {
            \Log::error('Flutterwave payment callback processing failed for live class', [
                'error'         => $e->getMessage(),
                'tx_ref'        => $tx_ref,
                'live_class_id' => $liveClass->id,
            ]);

            return redirect()->route('live-classes.public-join', $liveClass)
                ->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    /**
     * Handle Flutterwave webhook for live class payments
     */
    public function handleWebhook(Request $request)
    {
        $payload   = $request->getContent();
        $signature = $request->header('verif-hash');

        $webhookHash = config('services.flutterwave.webhook_hash');
        if ($signature !== $webhookHash) {
            \Log::warning('Invalid Flutterwave webhook signature received for live class');
            return response()->json(['status' => 'error'], 400);
        }

        $data = json_decode($payload, true);

        if (isset($data['event']) && $data['event'] === 'charge.completed') {
            $txData = $data['data'];
            $tx_ref = $txData['tx_ref'];

            try {
                if ($txData['status'] === 'successful') {
                    $payment = \App\Models\LiveClassPayment::where('transaction_id', $tx_ref)
                        ->where('status', '!=', 'completed')
                        ->first();

                    if ($payment) {
                        $payment->update([
                            'status'           => 'completed',
                            'paid_at'          => now(),
                            'gateway_response' => $txData,
                        ]);

                        \Log::info('Live class payment processed via webhook', [
                            'payment_id'    => $payment->id,
                            'live_class_id' => $payment->live_class_id,
                            'tx_ref'        => $tx_ref,
                            'amount'        => $txData['amount'],
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Flutterwave webhook processing failed for live class', [
                    'error'  => $e->getMessage(),
                    'tx_ref' => $tx_ref,
                ]);
                return response()->json(['status' => 'error'], 500);
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Show the form for creating a new live class
     */
    public function create()
    {
        $branches = Branch::all();
        $courses  = Course::all();
        $lessons  = Lesson::all();
        $teachers = Teacher::all();
        
        return view('admin.live-classes.create', compact('branches', 'courses', 'lessons', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'                         => 'required|string|max:255',
            'description'                   => 'nullable|string',
            'branches'                      => 'required|array|min:1',
            'branches.*'                    => 'exists:branches,id',
            'courses'                       => 'required|array|min:1',
            'courses.*'                     => 'exists:courses,id',
            'lessons'                       => 'nullable|array',
            'lessons.*'                     => 'exists:lessons,id',
            'teacher_id'                    => 'nullable|exists:teachers,id',
            'start_time'                    => 'required|date|after:now',
            'end_time'                      => 'required|date|after:start_time',
            'access_type'                   => 'required|in:students_only,public,both',
            'public_fee'                    => 'nullable|numeric|min:0',
            'currency'                      => 'required_with:public_fee|string|size:3',
            'required_payment_percentage'   => 'nullable|integer|min:0|max:100',
            'allow_public_requests'         => 'sometimes|boolean',
            'max_participants'              => 'nullable|integer|min:1',
            'auto_approve_online_students'  => 'sometimes|boolean',
            'require_student_approval'      => 'sometimes|boolean',
            'device_restriction'            => 'sometimes|boolean',
            'recording_enabled'             => 'sometimes|boolean',
            'chat_enabled'                  => 'sometimes|boolean',
            'screen_share_enabled'          => 'sometimes|boolean',
        ]);

        $liveClass = LiveClass::create([
            'title'                        => $request->title,
            'summary'                      => $request->title,
            'description'                  => $request->description,
            'meet_link'                    => '',
            'teacher_id'                   => $request->teacher_id ?: null,
            'start_time'                   => $request->start_time,
            'end_time'                     => $request->end_time,
            'status'                       => 'scheduled',
            'meeting_id'                   => 'meeting_' . Str::random(32),
            'password'                     => Str::random(8),
            'access_type'                  => $request->access_type,
            'public_fee'                   => $request->public_fee ?? 0,
            'currency'                     => $request->currency ?? 'USD',
            'required_payment_percentage'  => $request->required_payment_percentage ?? 0,
            'allow_public_requests'        => $request->has('allow_public_requests'),
            'max_participants'             => $request->max_participants ?: null,
            'auto_approve_online_students' => $request->has('auto_approve_online_students'),
            'require_student_approval'     => $request->has('require_student_approval'),
            'device_restriction'           => $request->has('device_restriction'),
            'recording_enabled'            => $request->has('recording_enabled'),
            'chat_enabled'                 => $request->has('chat_enabled'),
            'screen_share_enabled'         => $request->has('screen_share_enabled'),
        ]);

        $liveClass->branches()->attach($request->branches);
        $liveClass->courses()->attach($request->courses);
        if ($request->has('lessons') && is_array($request->lessons) && count($request->lessons) > 0) {
            $liveClass->lessons()->attach($request->lessons);
        }

        if ($liveClass->teacher_id) {
            $teacher = Teacher::find($liveClass->teacher_id);
            if ($teacher && $teacher->email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($teacher->email)
                        ->send(new \App\Mail\LiveClassCreatedNotification($liveClass, $teacher));
                } catch (\Exception $e) {
                    \Log::error('Failed to send live class notification email: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.live-classes.index')
            ->with('success', 'Live class scheduled successfully!');
    }

    public function show(LiveClass $liveClass)
    {
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            if (!$liveClass->courses->contains('id', $student->course_id)) {
                abort(403, 'You are not authorized to join this class');
            }
        }

        $liveClass->load(['branches', 'courses', 'lessons', 'teacher']);
        
        return view('admin.live-classes.show', compact('liveClass'));
    }

    /**
     * Student-specific live class show
     */
    public function studentShow(LiveClass $liveClass)
    {
        $student = Auth::guard('student')->user();
        
        if (!$liveClass->courses->contains('id', $student->course_id)) {
            abort(403, 'You are not authorized to access this live class');
        }

        $liveClass->load(['branches', 'courses', 'lessons', 'teacher']);
        
        return view('students.live-classes.show', compact('liveClass'));
    }

    public function edit(LiveClass $liveClass)
    {
        $branches = Branch::all();
        $courses  = Course::all();
        $lessons  = Lesson::all();
        $teachers = Teacher::all();
        
        return view('admin.live-classes.edit', compact('liveClass', 'branches', 'courses', 'lessons', 'teachers'));
    }

    public function update(Request $request, LiveClass $liveClass)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'branches'    => 'required|array|min:1',
            'branches.*'  => 'exists:branches,id',
            'courses'     => 'required|array|min:1',
            'courses.*'   => 'exists:courses,id',
            'lessons'     => 'nullable|array',
            'lessons.*'   => 'exists:lessons,id',
            'teacher_id'  => 'nullable|exists:teachers,id',
            'start_time'  => 'required|date',
            'end_time'    => 'required|date|after:start_time',
        ]);

        $liveClass->update([
            'title'       => $request->title,
            'description' => $request->description,
            'teacher_id'  => $request->teacher_id,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
        ]);

        $liveClass->branches()->sync($request->branches);
        $liveClass->courses()->sync($request->courses);
        $liveClass->lessons()->sync($request->lessons ?? []);

        return redirect()->route('admin.live-classes.index')
            ->with('success', 'Live class updated successfully!');
    }

    public function destroy(LiveClass $liveClass)
    {
        $liveClass->delete();
        
        return redirect()->route('admin.live-classes.index')
            ->with('success', 'Live class deleted successfully!');
    }

    public function start(LiveClass $liveClass)
    {
        if (!Auth::guard('admin')->check() && 
            !Auth::guard('staff')->check() && 
            !(Auth::guard('teacher')->check() && Auth::guard('teacher')->user()->id == $liveClass->teacher_id)) {
            abort(403, 'Only teachers can start live classes');
        }

        $liveClass->update([
            'status'     => 'active',
            'start_time' => now(),
        ]);

        return redirect()->route('admin.live-classes.room', $liveClass);
    }

    public function adminRoom(LiveClass $liveClass)
    {
        if (!Auth::guard('admin')->check() && !Auth::guard('staff')->check()) {
            abort(403, 'You are not authorized to access this admin live class room');
        }

        $liveClass->load(['branches', 'courses', 'lessons', 'teacher']);
        
        $studentRequests = $liveClass->requests()
            ->with(['user' => function($query) {
                $query->select('id', 'full_name', 'course_id');
            }])
            ->where('user_type', 'student')
            ->where('status', 'pending')
            ->latest()
            ->get();
        
        return view('admin.live-classes.room', compact('liveClass', 'studentRequests'));
    }

    public function requestAccess(LiveClass $liveClass)
    {
        if (!Auth::guard('student')->check()) {
            return back()->with('error', 'Only students can request access to classes');
        }

        $student = Auth::guard('student')->user();
        $student->load(['branch', 'course']);
        $liveClass->load(['courses', 'teacher']);
        
        $existingRequest = $liveClass->requests()
            ->where('user_id', $student->id)
            ->where('user_type', 'student')
            ->first();
        
        if ($existingRequest) {
            if ($existingRequest->status === 'pending') {
                return back()->with('info', 'Your request is already pending approval');
            } elseif ($existingRequest->status === 'approved') {
                return redirect()->route('live-classes.room', $liveClass)
                    ->with('success', 'Your request has been approved! You can now join the class.');
            } else {
                return back()->with('error', 'Your request was rejected: ' . $existingRequest->rejected_reason);
            }
        }

        return view('live-classes.request', compact('liveClass', 'student'));
    }

    public function submitRequest(Request $request, LiveClass $liveClass)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $student = Auth::guard('student')->user();
        
        $liveClass->requests()->create([
            'user_id'   => $student->id,
            'user_type' => 'student',
            'reason'    => $request->reason,
            'status'    => 'pending',
        ]);

        return redirect()->route('student.live-classes.index')
            ->with('success', 'Your request has been submitted and is pending approval.');
    }

    public function publicJoin(Request $request, LiveClass $liveClass)
    {
        $request->validate([
            'email' => 'required|email',
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $canJoin = $liveClass->canPublicUserJoin($request->email);
        
        if (!$canJoin['allowed']) {
            if (isset($canJoin['requires_payment'])) {
                return redirect()->route('live-classes.payment', $liveClass)
                    ->with(['email' => $request->email, 'name' => $request->name, 'phone' => $request->phone]);
            }
            return back()->with('error', $canJoin['reason']);
        }

        $this->recordPublicParticipant($liveClass, $request->email, $request->name, $request->phone);

        return redirect()->route('live-classes.room', $liveClass);
    }

    public function manageRequests(LiveClass $liveClass)
    {
        $requests = $liveClass->requests()->with('user')->latest()->get();
        
        return view('admin.live-classes.requests', compact('liveClass', 'requests'));
    }

    public function approveRequest(Request $request, LiveClass $liveClass, $requestId)
    {
        $classRequest = $liveClass->requests()->findOrFail($requestId);
        $admin = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('staff')->user();
        
        $classRequest->approve($admin->id ?? null);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Request approved successfully']);
        }

        return back()->with('success', 'Request approved successfully');
    }

    public function rejectRequest(Request $request, LiveClass $liveClass, $requestId)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $data   = json_decode($request->getContent(), true) ?? [];
            $reason = $data['reason'] ?? null;
        } else {
            $request->validate(['reason' => 'required|string|max:500']);
            $reason = $request->reason;
        }

        $classRequest = $liveClass->requests()->findOrFail($requestId);
        $admin = Auth::guard('admin')->user() ?? Auth::guard('teacher')->user() ?? Auth::guard('staff')->user();
        
        $classRequest->reject($reason, $admin->id ?? null);

        return response()->json(['success' => true, 'message' => 'Request rejected']);
    }

    /**
     * API endpoint to get requests for real-time monitoring
     */
    public function apiGetRequests(LiveClass $liveClass)
    {
        $requests = $liveClass->requests()
            ->with(['user' => function($query) {
                $query->select('id', 'full_name', 'course_id');
            }])
            ->where('user_type', 'student')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return response()->json(['requests' => $requests]);
    }

    private function recordPublicParticipant($liveClass, $email, $name, $phone)
    {
        return $liveClass->participants()->create([
            'email'      => $email,
            'name'       => $name,
            'user_type'  => 'public',
            'joined_at'  => now(),
            'is_active'  => true,
            'ip_address' => request()->ip(),
            'device_info' => [
                'user_agent' => request()->userAgent(),
                'device'     => $this->getDeviceInfo(),
            ],
        ]);
    }

    private function getDeviceInfo()
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet/', $userAgent)) {
            return 'tablet';
        }
        return 'desktop';
    }

    public function room(LiveClass $liveClass)
    {
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            
            $approvedRequest = $liveClass->requests()
                ->where('user_id', $student->id)
                ->where('user_type', 'student')
                ->where('status', 'approved')
                ->first();
            
            $isEnrolled = $liveClass->courses->contains('id', $student->course_id);
            
            if ((!$approvedRequest && !$isEnrolled) || $liveClass->status !== 'active') {
                abort(403, 'You cannot join this class');
            }
            
            $existingParticipant = $liveClass->participants()
                ->where('user_id', $student->id)
                ->where('user_type', 'student')
                ->where('is_active', true)
                ->whereNull('left_at')
                ->first();
                
            if (!$existingParticipant) {
                $liveClass->participants()->create([
                    'user_id'      => $student->id,
                    'user_type'    => 'student',
                    'email'        => $student->email,
                    'name'         => $student->full_name,
                    'joined_at'    => now(),
                    'is_active'    => true,
                    'ip_address'   => request()->ip(),
                    'browser_info' => request()->userAgent(),
                ]);
            }
            
            $isTeacher = false;

        } elseif (session('live_class_payment_' . $liveClass->id)) {
            $paymentSession = session('live_class_payment_' . $liveClass->id);
            
            $payment = \App\Models\LiveClassPayment::where('id', $paymentSession['payment_id'])
                ->where('status', 'completed')
                ->first();
                
            if (!$payment || $liveClass->status !== 'active') {
                abort(403, 'You cannot join this class');
            }
            
            $existingParticipant = $liveClass->participants()
                ->where('email', $paymentSession['email'])
                ->where('user_type', 'public')
                ->where('is_active', true)
                ->whereNull('left_at')
                ->first();
                
            if (!$existingParticipant) {
                $liveClass->participants()->create([
                    'email'        => $paymentSession['email'],
                    'name'         => $paymentSession['name'],
                    'user_type'    => 'public',
                    'joined_at'    => now(),
                    'is_active'    => true,
                    'ip_address'   => request()->ip(),
                    'browser_info' => request()->userAgent(),
                ]);
            }
            
            $isTeacher = false;
        } else {
            $isTeacher = Auth::guard('teacher')->check() && 
                         Auth::guard('teacher')->user()->id == $liveClass->teacher_id;
        }

        $liveClass->load(['branches', 'courses', 'lessons', 'teacher']);
        
        $studentRequests = [];
        if (Auth::guard('admin')->check() || Auth::guard('teacher')->check() || Auth::guard('staff')->check()) {
            $studentRequests = $liveClass->requests()
                ->with(['user' => function($query) {
                    $query->select('id', 'full_name', 'course_id');
                }])
                ->where('user_type', 'student')
                ->where('status', 'pending')
                ->latest()
                ->get();
        }
        
        $viewName = session('live_class_payment_' . $liveClass->id)
            ? 'live-classes.public-room'
            : 'live-classes.room';
        
        return view($viewName, compact('liveClass', 'isTeacher', 'studentRequests'));
    }

    public function end(LiveClass $liveClass)
    {
        if (!Auth::guard('admin')->check() && 
            !Auth::guard('staff')->check() && 
            !(Auth::guard('teacher')->check() && Auth::guard('teacher')->user()->id == $liveClass->teacher_id)) {
            abort(403, 'Only teachers can end live classes');
        }

        $liveClass->update([
            'status'   => 'ended',
            'end_time' => now(),
        ]);

        return redirect()->route('admin.live-classes.index')
            ->with('success', 'Live class ended successfully!');
    }
}