<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Flutterwave\Flutterwave;

class PublicTokenController extends Controller
{
    /**
     * Display token purchase page
     */
    public function purchase()
    {
        if (!Auth::guard('client')->check()) {
            return redirect()->route('public.subscriptions.login')
                ->with('info', 'Please login to purchase tokens');
        }

        $client = Auth::guard('client')->user();
        $currentTokens = $client->getCurrentTokens();

        // Define token packages with bulk discounts
        $tokenPackages = [
            [
                'tokens' => 100,
                'amount' => 5000,
                'price' => 'RWF 5,000',
                'formatted_price' => number_format(5000, 2),
                'original_price' => 'RWF 5,000',
                'discount' => 0,
                'popular' => false,
                'bonus' => 0
            ],
            [
                'tokens' => 250,
                'amount' => 12000,
                'price' => 'RWF 12,000',
                'formatted_price' => number_format(12000, 2),
                'original_price' => 'RWF 12,500',
                'discount' => 4,
                'popular' => false,
                'bonus' => 10
            ],
            [
                'tokens' => 500,
                'amount' => 22500,
                'price' => 'RWF 22,500',
                'formatted_price' => number_format(22500, 2),
                'original_price' => 'RWF 25,000',
                'discount' => 10,
                'popular' => true,
                'bonus' => 25
            ],
            [
                'tokens' => 1000,
                'amount' => 40000,
                'price' => 'RWF 40,000',
                'formatted_price' => number_format(40000, 2),
                'original_price' => 'RWF 50,000',
                'discount' => 20,
                'popular' => false,
                'bonus' => 50
            ],
            [
                'tokens' => 2000,
                'amount' => 75000,
                'price' => 'RWF 75,000',
                'formatted_price' => number_format(75000, 2),
                'original_price' => 'RWF 100,000',
                'discount' => 25,
                'popular' => false,
                'bonus' => 100
            ]
        ];

        return view('public.tokens.purchase', compact('client', 'currentTokens', 'tokenPackages'));
    }

    /**
     * Process token purchase
     */
    public function processPurchase(Request $request)
    {
        if (!Auth::guard('client')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        $request->validate([
            'tokens' => 'required|integer|min:100|max:5000',
            'amount' => 'required|integer|min:500',
            'payment_method' => 'required|in:card,mobile_money,bank_transfer',
        ]);

        $client = Auth::guard('client')->user();
        $tokens = $request->tokens;
        $amount = $request->amount;

        // Generate unique transaction reference
        $tx_ref = 'TOKEN_' . $client->id . '_' . $tokens . '_' . time();

        try {
            // Create transaction record
            $transaction = ClientTransaction::create([
                'client_id' => $client->id,
                'tx_ref' => $tx_ref,
                'type' => 'token_purchase',
                'amount' => $amount,
                'currency' => 'RWF',
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'meta' => json_encode([
                    'tokens_purchased' => $tokens,
                    'price_per_token' => $amount / $tokens,
                    'bonus_tokens' => $this->calculateBonusTokens($tokens),
                ]),
            ]);

            // Prepare Flutterwave payment data
            $paymentData = [
                'tx_ref' => $tx_ref,
                'amount' => $amount,
                'currency' => 'RWF',
                'payment_options' => $request->payment_method === 'mobile_money' ? 'mobilemoneyrwanda' : $request->payment_method,
                'redirect_url' => route('public.tokens.callback'),
                'customer' => [
                    'email' => $client->email,
                    'name' => $client->full_name,
                    'phone' => $client->phone ?? '',
                ],
                'customizations' => [
                    'title' => "Token Purchase - {$tokens} Tokens",
                    'description' => "Purchase {$tokens} tokens for practice tests",
                    'logo' => asset('images/logo.png')
                ],
                'meta' => [
                    'client_id' => $client->id,
                    'tokens' => $tokens,
                    'type' => 'token_purchase'
                ]
            ];

            // Initialize Flutterwave payment
            $flutterwave = new Flutterwave();
            $payment = $flutterwave->initializePayment($paymentData);

            if (isset($payment['status']) && $payment['status'] === 'success') {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $payment['data']['link'],
                    'tx_ref' => $tx_ref
                ]);
            } else {
                // Update transaction as failed
                $transaction->update([
                    'status' => 'failed',
                    'gateway_response' => $payment
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize payment. Please try again.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Token purchase payment initialization failed', [
                'error' => $e->getMessage(),
                'client_id' => $client->id,
                'tokens' => $tokens
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment callback
     */
    public function handleCallback(Request $request)
    {
        $tx_ref = $request->query('transaction_id') ?? $request->query('tx_ref');
        
        if (!$tx_ref) {
            return redirect()->route('public.tokens.index')
                ->with('error', 'Payment verification failed. No transaction reference found.');
        }

        try {
            // Find transaction
            $transaction = ClientTransaction::where('tx_ref', $tx_ref)->first();
            
            if (!$transaction) {
                return redirect()->route('public.tokens.index')
                    ->with('error', 'Transaction not found.');
            }

            // Verify transaction with Flutterwave
            $flutterwave = new Flutterwave();
            $flutterwaveTransaction = $flutterwave->verifyTransaction($tx_ref);

            if ($flutterwaveTransaction['status'] === 'success') {
                $txData = $flutterwaveTransaction['data'];
                $meta = json_decode($transaction->meta, true);
                
                // Update transaction record
                $transaction->update([
                    'status' => 'successful',
                    'gateway_response' => $txData,
                    'paid_at' => now(),
                ]);

                // Add tokens to client account
                $client = Client::find($transaction->client_id);
                $totalTokens = $meta['tokens_purchased'] + ($meta['bonus_tokens'] ?? 0);
                $client->addTokens($totalTokens, "Purchased {$meta['tokens_purchased']} tokens", 'purchase');

                return redirect()->route('public.tokens.index')
                    ->with('success', "Payment successful! {$totalTokens} tokens have been added to your account.");
            } else {
                $transaction->update([
                    'status' => 'failed',
                    'gateway_response' => $flutterwaveTransaction
                ]);

                return redirect()->route('public.tokens.index')
                    ->with('error', 'Payment failed or was cancelled.');
            }
        } catch (\Exception $e) {
            Log::error('Token payment callback processing failed', [
                'error' => $e->getMessage(),
                'tx_ref' => $tx_ref
            ]);

            return redirect()->route('public.tokens.index')
                ->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    /**
     * Display transaction history
     */
    public function index()
    {
        if (!Auth::guard('client')->check()) {
            return redirect()->route('public.subscriptions.login')
                ->with('info', 'Please login to view your tokens.');
        }

        $client = Auth::guard('client')->user();
        $transactions = $client->transactions()
            ->with(['subscription'])
            ->latest()
            ->paginate(20);

        return view('public.tokens.index', compact('client', 'transactions'));
    }

    /**
     * Calculate bonus tokens based on purchase amount
     */
    private function calculateBonusTokens($tokens)
    {
        if ($tokens >= 2000) return 100;
        if ($tokens >= 1000) return 50;
        if ($tokens >= 500) return 25;
        if ($tokens >= 250) return 10;
        return 0;
    }

    /**
     * Handle client logout (from tokens section)
     */
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('public.subscriptions.index')
            ->with('success', 'You have been logged out.');
    }
}
