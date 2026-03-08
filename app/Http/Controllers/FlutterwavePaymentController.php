<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\StudentSubscription;
use App\Models\Student;
use App\Models\StudentTransaction;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Flutterwavedev\Flutterwave\Flutterwave;

class FlutterwavePaymentController extends Controller
{
    /**
     * Initialize payment for subscription purchase
     */
    public function initializeSubscriptionPayment(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        $student = Auth::user();
        $subscription = Subscription::findOrFail($request->subscription_id);

        // Check if student already has active subscription
        if ($student->activeSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active subscription'
            ], 400);
        }

        // Create student subscription record
        $studentSubscription = StudentSubscription::create([
            'student_id' => $student->id,
            'subscription_id' => $subscription->id,
            'payment_status' => 'pending',
            'starts_at' => null,
            'ends_at' => null,
            'is_active' => false,
        ]);

        // Generate unique transaction reference
        $tx_ref = 'SUB-' . $student->id . '-' . $subscription->id . '-' . time();

        // Prepare payment data
        $paymentData = [
            'tx_ref' => $tx_ref,
            'amount' => $subscription->price,
            'currency' => 'RWF',
            'payment_options' => 'card,bank_transfer,ussd,mobile_money',
            'redirect_url' => route('flutterwave.payment.callback'),
            'customer' => [
                'email' => $student->email ?? 'student@example.com',
                'name' => $student->full_name,
                'phone' => $student->phone ?? '',
            ],
            'meta' => [
                'student_id' => $student->id,
                'subscription_id' => $subscription->id,
                'student_subscription_id' => $studentSubscription->id,
                'tokens_included' => $subscription->tokens_included,
                'type' => 'subscription'
            ],
            'customizations' => [
                'title' => "Subscription: {$subscription->name}",
                'description' => "Purchase {$subscription->name} - {$subscription->tokens_included} tokens",
                'logo' => asset('images/logo.png')
            ]
        ];

        try {
            // Initialize Flutterwave payment
            $payment = Flutterwave::initializePayment($paymentData);

            if (isset($payment['status']) && $payment['status'] === 'success') {
                // Update student subscription with transaction reference
                $studentSubscription->update(['payment_reference' => $tx_ref]);

                return response()->json([
                    'success' => true,
                    'redirect_url' => $payment['data']['link'],
                    'tx_ref' => $tx_ref
                ]);
            } else {
                // Delete the created subscription if payment initialization fails
                $studentSubscription->delete();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize payment. Please try again.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Flutterwave payment initialization failed', [
                'error' => $e->getMessage(),
                'student_id' => $student->id,
                'subscription_id' => $subscription->id
            ]);

            // Delete the created subscription if payment initialization fails
            $studentSubscription->delete();

            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initialize payment for custom token purchase
     */
    public function initializeTokenPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1|max:1000',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $student = Auth::user();
        $tokenAmount = $request->amount;
        $basePrice = 500;
        
        // Calculate bulk discounts
        $discount = 0;
        if ($tokenAmount >= 100) {
            $discount = 0.10;
        } elseif ($tokenAmount >= 50) {
            $discount = 0.05;
        }
        
        $totalPrice = $tokenAmount * $basePrice;
        $discountAmount = $totalPrice * $discount;
        $finalPrice = $totalPrice - $discountAmount;

        // Generate unique transaction reference
        $tx_ref = 'TKN-' . $student->id . '-' . time();

        // Prepare payment data
        $paymentData = [
            'tx_ref' => $tx_ref,
            'amount' => $finalPrice,
            'currency' => 'RWF',
            'payment_options' => 'card,bank_transfer,ussd,mobile_money',
            'redirect_url' => route('flutterwave.payment.callback'),
            'customer' => [
                'email' => $student->email ?? 'student@example.com',
                'name' => $student->full_name,
                'phone' => $student->phone ?? '',
            ],
            'meta' => [
                'student_id' => $student->id,
                'token_amount' => $tokenAmount,
                'base_price' => $basePrice,
                'discount' => $discount,
                'final_price' => $finalPrice,
                'type' => 'token_purchase',
                'payment_reference' => $request->payment_reference
            ],
            'customizations' => [
                'title' => 'Token Purchase',
                'description' => "Purchase {$tokenAmount} tokens" . ($discount > 0 ? " with discount" : ""),
                'logo' => asset('images/logo.png')
            ]
        ];

        try {
            // Initialize Flutterwave payment
            $payment = Flutterwave::initializePayment($paymentData);

            if (isset($payment['status']) && $payment['status'] === 'success') {
                // Create pending transaction record
                StudentTransaction::create([
                    'student_id' => $student->id,
                    'tx_ref' => $tx_ref,
                    'type' => 'token_purchase',
                    'amount' => $finalPrice,
                    'currency' => 'RWF',
                    'status' => 'pending',
                    'meta' => json_encode([
                        'token_amount' => $tokenAmount,
                        'base_price' => $basePrice,
                        'discount' => $discount,
                        'payment_reference' => $request->payment_reference
                    ])
                ]);

                return response()->json([
                    'success' => true,
                    'redirect_url' => $payment['data']['link'],
                    'tx_ref' => $tx_ref
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize payment. Please try again.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Flutterwave token payment initialization failed', [
                'error' => $e->getMessage(),
                'student_id' => $student->id,
                'token_amount' => $tokenAmount
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Flutterwave payment callback
     */
    public function handlePaymentCallback(Request $request)
    {
        $tx_ref = $request->query('transaction_id');
        
        if (!$tx_ref) {
            return redirect()->route('students.dashboard')
                ->with('error', 'Payment verification failed. No transaction reference found.');
        }

        try {
            // Verify transaction with Flutterwave
            $transaction = Flutterwave::verifyTransaction($tx_ref);

            if ($transaction['status'] === 'success') {
                $txData = $transaction['data'];
                $meta = $txData['meta'] ?? [];
                $paymentType = $meta['type'] ?? '';

                DB::beginTransaction();

                if ($paymentType === 'subscription') {
                    $this->processSubscriptionPayment($txData, $meta);
                } elseif ($paymentType === 'token_purchase') {
                    $this->processTokenPayment($txData, $meta);
                }

                DB::commit();

                return redirect()->route('students.dashboard')
                    ->with('success', 'Payment successful! Your tokens have been added to your account.');
            } else {
                return redirect()->route('students.dashboard')
                    ->with('error', 'Payment failed or was cancelled.');
            }
        } catch (\Exception $e) {
            Log::error('Flutterwave payment callback processing failed', [
                'error' => $e->getMessage(),
                'tx_ref' => $tx_ref
            ]);

            return redirect()->route('students.dashboard')
                ->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    /**
     * Process successful subscription payment
     */
    private function processSubscriptionPayment($txData, $meta)
    {
        $studentId = $meta['student_id'];
        $subscriptionId = $meta['subscription_id'];
        $studentSubscriptionId = $meta['student_subscription_id'];
        $tokensIncluded = $meta['tokens_included'];

        // Update student subscription
        $studentSubscription = StudentSubscription::findOrFail($studentSubscriptionId);
        $subscription = Subscription::findOrFail($subscriptionId);

        $studentSubscription->update([
            'payment_status' => 'paid',
            'payment_reference' => $txData['tx_ref'],
            'paid_amount' => $txData['amount'],
            'starts_at' => now(),
            'ends_at' => now()->addDays($subscription->duration_days),
            'is_active' => true,
        ]);

        // Create payment transaction record
        StudentTransaction::create([
            'student_id' => $studentId,
            'tx_ref' => $txData['tx_ref'],
            'type' => 'subscription',
            'amount' => $txData['amount'],
            'currency' => $txData['currency'],
            'status' => 'successful',
            'meta' => json_encode([
                'method' => 'flutterwave',
                'subscription_id' => $subscriptionId,
                'subscription_name' => $subscription->name,
                'tokens_included' => $tokensIncluded,
                'flutterwave_transaction_id' => $txData['id'],
            ]),
            'paid_at' => now(),
        ]);

        // Initialize tokens for student if not already done
        $student = Student::findOrFail($studentId);
        $token = $student->initializeTokens();
        
        // Add tokens from subscription
        $token->addTokens(
            $tokensIncluded,
            "Tokens from {$subscription->name} subscription",
            'subscription'
        );

        Log::info('Subscription payment processed successfully', [
            'student_id' => $studentId,
            'subscription_id' => $subscriptionId,
            'amount' => $txData['amount'],
            'tokens_added' => $tokensIncluded,
            'tx_ref' => $txData['tx_ref']
        ]);
    }

    /**
     * Process successful token purchase payment
     */
    private function processTokenPayment($txData, $meta)
    {
        $studentId = $meta['student_id'];
        $tokenAmount = $meta['token_amount'];

        // Update transaction record
        $transaction = StudentTransaction::where('tx_ref', $txData['tx_ref'])->first();
        if ($transaction) {
            $transaction->update([
                'status' => 'successful',
                'meta' => json_encode(array_merge(json_decode($transaction->meta, true), [
                    'flutterwave_transaction_id' => $txData['id'],
                ])),
                'paid_at' => now(),
            ]);
        } else {
            // Create transaction record if it doesn't exist
            StudentTransaction::create([
                'student_id' => $studentId,
                'tx_ref' => $txData['tx_ref'],
                'type' => 'token_purchase',
                'amount' => $txData['amount'],
                'currency' => $txData['currency'],
                'status' => 'successful',
                'meta' => json_encode([
                    'token_amount' => $tokenAmount,
                    'flutterwave_transaction_id' => $txData['id'],
                ]),
                'paid_at' => now(),
            ]);
        }

        // Initialize tokens for student if not already done
        $student = Student::findOrFail($studentId);
        $token = $student->initializeTokens();
        
        // Add purchased tokens
        $token->addTokens(
            $tokenAmount,
            "Purchased {$tokenAmount} tokens",
            'purchase'
        );

        Log::info('Token purchase payment processed successfully', [
            'student_id' => $studentId,
            'token_amount' => $tokenAmount,
            'amount' => $txData['amount'],
            'tx_ref' => $txData['tx_ref']
        ]);
    }

    /**
     * Webhook handler for Flutterwave events
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('verif-hash');

        // Verify webhook signature (you should store your webhook hash in .env)
        $webhookHash = config('services.flutterwave.webhook_hash');
        if ($signature !== $webhookHash) {
            Log::warning('Invalid webhook signature received');
            return response()->json(['status' => 'error'], 400);
        }

        $data = json_decode($payload, true);

        if ($data['event'] === 'charge.completed') {
            $txData = $data['data'];
            $tx_ref = $txData['tx_ref'];

            try {
                // Only process if transaction is successful and not already processed
                if ($txData['status'] === 'successful') {
                    $existingTransaction = StudentTransaction::where('tx_ref', $tx_ref)
                        ->where('status', 'successful')
                        ->first();

                    if (!$existingTransaction) {
                        $meta = $txData['meta'] ?? [];
                        $paymentType = $meta['type'] ?? '';

                        DB::beginTransaction();

                        if ($paymentType === 'subscription') {
                            $this->processSubscriptionPayment($txData, $meta);
                        } elseif ($paymentType === 'token_purchase') {
                            $this->processTokenPayment($txData, $meta);
                        }

                        DB::commit();

                        Log::info('Webhook payment processed successfully', [
                            'tx_ref' => $tx_ref,
                            'event' => $data['event']
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Webhook processing failed', [
                    'error' => $e->getMessage(),
                    'tx_ref' => $tx_ref
                ]);
                return response()->json(['status' => 'error'], 500);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
