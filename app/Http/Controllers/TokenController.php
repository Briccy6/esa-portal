<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\Student;
use App\Models\TokenTransaction;
use App\Models\Subscription;
use App\Models\StudentSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TokenController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $token = $student->token;
        $transactions = $token ? $token->transactions()->latest()->paginate(20) : collect();

        return view('students.tokens.index', compact('student', 'token', 'transactions'));
    }

    public function purchaseSubscription(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'payment_method' => 'required|in:flutterwave,paypal,stripe,mobile_money',
        ]);

        $student = Auth::user();
        $subscription = Subscription::findOrFail($request->subscription_id);

        try {
            // Create student subscription record
            $studentSubscription = StudentSubscription::create([
                'student_id' => $student->id,
                'subscription_id' => $subscription->id,
                'status' => 'pending',
                'amount' => $subscription->price,
                'tokens_included' => $subscription->tokens_included,
                'payment_method' => $request->payment_method,
                'started_at' => now(),
                'expires_at' => now()->addDays($subscription->duration_days),
                'transaction_id' => 'ESA_' . time() . '_' . $student->id,
            ]);

            // Process payment based on selected method
            $paymentResult = $this->processPayment($studentSubscription, $request->payment_method);

            if ($paymentResult['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'redirect_url' => $paymentResult['redirect_url'] ?? null,
                    'transaction_id' => $studentSubscription->transaction_id,
                ]);
            } else {
                $studentSubscription->update(['status' => 'failed']);
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message'] ?? 'Payment failed',
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Token purchase error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request',
            ], 500);
        }
    }

    /**
     * Process payment based on selected gateway
     */
    private function processPayment($studentSubscription, $paymentMethod)
    {
        switch ($paymentMethod) {
            case 'flutterwave':
                return $this->processFlutterwavePayment($studentSubscription);
            
            case 'paypal':
                return $this->processPayPalPayment($studentSubscription);
            
            case 'stripe':
                return $this->processStripePayment($studentSubscription);
            
            case 'mobile_money':
                return $this->processMobileMoneyPayment($studentSubscription);
            
            default:
                return ['success' => false, 'message' => 'Unsupported payment method'];
        }
    }

    /**
     * Process Flutterwave payment
     */
    private function processFlutterwavePayment($studentSubscription)
    {
        // For now, simulate payment processing
        // In production, integrate with actual Flutterwave SDK
        return [
            'success' => true,
            'redirect_url' => route('tokens.payment.callback', ['gateway' => 'flutterwave', 'transaction_id' => $studentSubscription->transaction_id]),
            'transaction_id' => $studentSubscription->transaction_id,
        ];
    }

    /**
     * Process PayPal payment
     */
    private function processPayPalPayment($studentSubscription)
    {
        return [
            'success' => true,
            'redirect_url' => route('tokens.payment.callback', ['gateway' => 'paypal', 'transaction_id' => $studentSubscription->transaction_id]),
            'transaction_id' => $studentSubscription->transaction_id,
        ];
    }

    /**
     * Process Stripe payment
     */
    private function processStripePayment($studentSubscription)
    {
        return [
            'success' => true,
            'redirect_url' => route('tokens.payment.callback', ['gateway' => 'stripe', 'transaction_id' => $studentSubscription->transaction_id]),
            'transaction_id' => $studentSubscription->transaction_id,
        ];
    }

    /**
     * Process Mobile Money payment
     */
    private function processMobileMoneyPayment($studentSubscription)
    {
        return [
            'success' => true,
            'redirect_url' => route('tokens.mobile-money.form', $studentSubscription->id),
            'transaction_id' => $studentSubscription->transaction_id,
        ];
    }

    /**
     * Handle payment callback from payment gateways
     */
    public function paymentCallback(Request $request, $gateway)
    {
        try {
            $transactionId = $request->get('transaction_id') ?? $request->get('tx_ref') ?? $request->get('payment_id');
            
            if (!$transactionId) {
                return redirect()->route('dashboard')->with('error', 'Invalid payment callback');
            }

            $studentSubscription = StudentSubscription::where('transaction_id', $transactionId)->firstOrFail();

            // Simulate successful payment verification
            // In production, verify with actual payment gateway APIs
            $this->completePurchase($studentSubscription);
            
            return redirect()->route('dashboard')->with('success', 
                'Payment successful! ' . $studentSubscription->tokens_included . ' tokens have been added to your account.'
            );

        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'An error occurred during payment verification');
        }
    }

    /**
     * Complete purchase and add tokens
     */
    private function completePurchase($studentSubscription)
    {
        $studentSubscription->update(['status' => 'completed']);

        // Get or create student token record
        $token = Token::firstOrCreate(
            ['student_id' => $studentSubscription->student_id],
            [
                'balance' => 0,
                'total_earned' => 0,
                'total_spent' => 0,
            ]
        );

        // Add tokens to student account
        $token->addTokens(
            $studentSubscription->tokens_included,
            "Purchased {$studentSubscription->subscription->name} subscription",
            'purchase'
        );
    }

    /**
     * Show mobile money payment form
     */
    public function showMobileMoneyForm($subscriptionId)
    {
        $studentSubscription = StudentSubscription::findOrFail($subscriptionId);
        
        return view('tokens.mobile-money', compact('studentSubscription'));
    }

    /**
     * Process mobile money payment
     */
    public function processMobileMoney(Request $request, $subscriptionId)
    {
        $request->validate([
            'phone_number' => 'required|string|regex:/^[0-9]{10}$/',
            'provider' => 'required|in:mtn,airtel',
        ]);

        $studentSubscription = StudentSubscription::findOrFail($subscriptionId);
        
        // Process mobile money payment (integration would go here)
        // For now, simulate successful payment
        $this->completePurchase($studentSubscription);

        return redirect()->route('dashboard')->with('success', 
            'Mobile money payment successful! ' . $studentSubscription->tokens_included . ' tokens have been added to your account.'
        );
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1|max:1000',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $student = Auth::user();
        $token = $student->initializeTokens();

        // Calculate bulk discounts
        $amount = $request->amount;
        $basePrice = 500; // RWF per token
        $discount = 0;
        
        if ($amount >= 100) {
            $discount = 0.10; // 10% discount for 100+ tokens
        } elseif ($amount >= 50) {
            $discount = 0.05; // 5% discount for 50+ tokens
        }
        
        $finalAmount = $amount * (1 - $discount);
        $totalPrice = $finalAmount * $basePrice;

        // Create transaction record
        $transaction = $token->addTokens(
            $amount,
            'Custom token purchase' . ($discount > 0 ? ' (' . ($discount * 100) . '% discount applied)' : ''),
            'purchase',
            $request->payment_reference
        );

        return response()->json([
            'success' => true,
            'message' => 'Successfully purchased ' . $amount . ' tokens!' . ($discount > 0 ? ' Discount: RWF ' . number_format($totalPrice * $discount) : ''),
            'tokens_added' => $amount,
            'total_price' => $totalPrice,
            'discount_applied' => $discount > 0,
            'transaction_id' => $transaction->id
        ]);
    }

    public function secretaryIndex()
    {
        $students = Student::with('token')->paginate(20);
        
        // Determine the base routes for token management based on auth guard
        $isAdmin = auth()->guard('admin')->check();
        $tokenHistoryBaseRoute = $isAdmin ? route('admin.tokens.history') : route('secretary.tokens.history');
        $tokenHistoryRoute = $isAdmin ? route('admin.tokens.history', ':studentId') : route('secretary.tokens.history', ':studentId');
        $tokenAddRoute = $isAdmin ? route('admin.tokens.add', ':studentId') : route('secretary.tokens.add', ':studentId');
        $tokenDeductRoute = $isAdmin ? route('admin.tokens.deduct', ':studentId') : route('secretary.tokens.deduct', ':studentId');
        
        return view('tokens.secretary-index', compact('students', 'tokenHistoryBaseRoute', 'tokenHistoryRoute', 'tokenAddRoute', 'tokenDeductRoute'));
    }

    public function secretaryAddTokens(Request $request, Student $student)
    {
        $request->validate([
            'amount' => 'required|integer|min:1|max:1000',
            'description' => 'required|string|max:255',
            'source' => 'required|in:system,bonus,compensation,refund',
        ]);

        $token = $student->initializeTokens();

        $transaction = $token->addTokens(
            $request->amount,
            $request->description,
            $request->source
        );

        return redirect()->route('tokens.secretary-index')
            ->with('success', "Added {$request->amount} tokens to {$student->full_name}");
    }

    public function secretaryDeductTokens(Request $request, Student $student)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);

        $token = $student->token;

        if (!$token || !$token->canAfford($request->amount)) {
            return redirect()->route('tokens.secretary-index')
                ->with('error', 'Student does not have enough tokens');
        }

        $transaction = $token->spendTokens(
            $request->amount,
            $request->description,
            'admin'
        );

        return redirect()->route('tokens.secretary-index')
            ->with('success', "Deducted {$request->amount} tokens from {$student->full_name}");
    }

    public function transactionHistory(Student $student = null)
    {
        if ($student) {
            $token = $student->token;
            $transactions = $token ? $token->transactions()->latest()->paginate(50) : collect();
            $title = "Token History - {$student->full_name}";
        } else {
            $transactions = TokenTransaction::with('token.student')->latest()->paginate(50);
            $title = 'All Token Transactions';
        }

        return view('transactions.history', compact('transactions', 'title', 'student'));
    }

    public function dashboard()
    {
        $student = Auth::user();
        $token = $student->token;
        
        $stats = [
            'total_tokens' => $token ? $token->balance : 0,
            'total_earned' => $token ? $token->total_earned : 0,
            'total_spent' => $token ? $token->total_spent : 0,
            'recent_transactions' => $token ? $token->transactions()->latest()->take(5)->get() : collect(),
        ];

        // Get active subscriptions for the purchase modal
        $subscriptions = Subscription::active()->orderBy('price')->get();

        return view('tokens.dashboard', compact('student', 'stats', 'subscriptions'));
    }
}
