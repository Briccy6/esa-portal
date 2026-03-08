<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Subscription;
use App\Models\ClientSubscription;
use App\Models\ClientTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Flutterwave\Flutterwave;

class PublicSubscriptionController extends Controller
{
    /**
     * Display public subscription page
     */
    public function index()
    {
        $subscriptions = Subscription::active()
            ->orderBy('price')
            ->get()
            ->groupBy('tier');
            
        return view('public.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show registration form
     */
    public function showRegistration()
    {
        return view('public.subscriptions.register');
    }

    /**
     * Handle client registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'terms' => 'accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $client = Client::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'city' => $request->city,
                'country' => $request->country,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'is_active' => true,
                'verification_status' => 'pending',
            ]);

            // Log in the client
            Auth::guard('client')->login($client);

            return redirect()->route('public.subscriptions.dashboard')
                ->with('success', 'Account created successfully! Welcome to ESA Portal.');

        } catch (\Exception $e) {
            \Log::error('Client registration failed', [
                'error' => $e->getMessage(),
                'email' => $request->email
            ]);

            return redirect()->back()
                ->with('error', 'Registration failed. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show client dashboard
     */
    public function dashboard()
    {
        $client = Auth::guard('client')->user();
        $client->load(['activeSubscription.subscription', 'transactions' => function($query) {
            $query->latest()->limit(10);
        }]);

        $currentTokens = $client->getCurrentTokens();
        $subscriptions = Subscription::active()->orderBy('price')->get();

        return view('public.subscriptions.dashboard', compact('client', 'currentTokens', 'subscriptions'));
    }

    /**
     * Show subscription purchase form
     */
    public function showPurchase(Subscription $subscription)
    {
        $client = Auth::guard('client')->user();
        
        if ($client->hasActiveSubscription()) {
            return redirect()->route('public.subscriptions.dashboard')
                ->with('error', 'You already have an active subscription.');
        }

        return view('public.subscriptions.purchase', compact('subscription'));
    }

    /**
     * Initialize subscription payment
     */
    public function initializePayment(Request $request, Subscription $subscription)
    {
        $client = Auth::guard('client')->user();
        
        if ($client->hasActiveSubscription()) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active subscription'
            ], 400);
        }

        // Create client subscription record
        $clientSubscription = $client->purchaseSubscription($subscription->id);

        // Generate unique transaction reference
        $tx_ref = 'SUB-' . $client->id . '-' . $subscription->id . '-' . time();

        // Prepare payment data
        $paymentData = [
            'tx_ref' => $tx_ref,
            'amount' => $subscription->price,
            'currency' => 'RWF',
            'payment_options' => 'card,bank_transfer,ussd,mobile_money',
            'redirect_url' => route('public.subscriptions.payment.callback'),
            'customer' => [
                'email' => $client->email,
                'name' => $client->full_name,
                'phone' => $client->phone ?? '',
            ],
            'meta' => [
                'client_id' => $client->id,
                'subscription_id' => $subscription->id,
                'client_subscription_id' => $clientSubscription->id,
                'tokens_included' => $subscription->tokens_included,
                'type' => 'client_subscription'
            ],
            'customizations' => [
                'title' => "Subscription: {$subscription->name}",
                'description' => "Purchase {$subscription->name} - {$subscription->tokens_included} tokens",
                'logo' => asset('images/logo.png')
            ]
        ];

        try {
            // Bootstrap Flutterwave
            Flutterwave::bootstrap();
            
            // Create a standard modal for payment link generation
            $modal = new \Flutterwave\Library\Modal(
                \Flutterwave\Library\Modal::STANDARD,
                [
                    'amount' => $subscription->price,
                    'currency' => 'RWF',
                    'tx_ref' => $tx_ref,
                    'redirect_url' => route('public.subscriptions.payment.callback'),
                ],
                new \Flutterwave\EventHandlers\ModalEventHandler(),
                \Flutterwave\Config\ForkConfig::setUp(
                    config('services.flutterwave.secret_key'),
                    config('services.flutterwave.public_key'),
                    config('services.flutterwave.encryption_key'),
                    config('services.flutterwave.env', 'sandbox')
                )
            );

            // Configure the modal with payment details
            $paymentLink = $modal->with([
                'email' => $client->email,
                'first_name' => $client->first_name,
                'last_name' => $client->last_name,
                'phone_number' => $client->phone ?? '',
                'payment_method' => 'card,bank_transfer,ussd,mobile_money',
                'meta' => [
                    'client_id' => $client->id,
                    'subscription_id' => $subscription->id,
                    'client_subscription_id' => $clientSubscription->id,
                    'tokens_included' => $subscription->tokens_included,
                    'type' => 'client_subscription'
                ],
                'customization' => [
                    'title' => "Subscription: {$subscription->name}",
                    'description' => "Purchase {$subscription->name} - {$subscription->tokens_included} tokens",
                    'logo' => asset('images/logo.png')
                ]
            ])->getUrl();

            if ($paymentLink) {
                // Update client subscription with transaction reference
                $clientSubscription->update(['payment_reference' => $tx_ref]);

                // Create transaction record
                ClientTransaction::create([
                    'client_id' => $client->id,
                    'subscription_id' => $subscription->id,
                    'tx_ref' => $tx_ref,
                    'type' => 'subscription',
                    'amount' => $subscription->price,
                    'currency' => 'RWF',
                    'status' => 'pending',
                    'meta' => json_encode([
                        'subscription_name' => $subscription->name,
                        'tokens_included' => $subscription->tokens_included,
                        'client_subscription_id' => $clientSubscription->id,
                    ]),
                ]);

                return response()->json([
                    'success' => true,
                    'redirect_url' => $paymentLink,
                    'tx_ref' => $tx_ref
                ]);
            } else {
                // Delete the created subscription if payment initialization fails
                $clientSubscription->delete();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize payment. Please try again.'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Flutterwave payment initialization failed', [
                'error' => $e->getMessage(),
                'client_id' => $client->id,
                'subscription_id' => $subscription->id
            ]);

            // Delete the created subscription if payment initialization fails
            $clientSubscription->delete();

            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment callback
     */
    public function handlePaymentCallback(Request $request)
    {
        $tx_ref = $request->query('transaction_id') ?? $request->query('tx_ref');
        
        if (!$tx_ref) {
            return redirect()->route('public.subscriptions.dashboard')
                ->with('error', 'Payment verification failed. No transaction reference found.');
        }

        try {
            // Find transaction
            $transaction = ClientTransaction::where('tx_ref', $tx_ref)->first();
            
            if (!$transaction) {
                return redirect()->route('public.subscriptions.dashboard')
                    ->with('error', 'Transaction not found.');
            }

            // Find client subscription
            $clientSubscription = ClientSubscription::find($transaction->meta['client_subscription_id'] ?? null);
            
            if (!$clientSubscription) {
                return redirect()->route('public.subscriptions.dashboard')
                    ->with('error', 'Subscription not found.');
            }

            // Bootstrap Flutterwave and verify transaction
            Flutterwave::bootstrap();
            
            // Use the service to verify transaction
            $transactionService = new \Flutterwave\Service\Transactions();
            $flutterwaveTransaction = $transactionService->verify($tx_ref);

            if ($flutterwaveTransaction['status'] === 'success') {
                $txData = $flutterwaveTransaction['data'];
                
                DB::beginTransaction();

                // Update transaction record
                $transaction->update([
                    'status' => 'successful',
                    'gateway_response' => $txData,
                    'paid_at' => now(),
                ]);

                // Update client subscription
                $clientSubscription->markAsPaid($txData);

                DB::commit();

                return redirect()->route('public.subscriptions.dashboard')
                    ->with('success', 'Payment successful! Your subscription is now active.');
            } else {
                $transaction->update([
                    'status' => 'failed',
                    'gateway_response' => $flutterwaveTransaction
                ]);

                $clientSubscription->update([
                    'payment_status' => 'failed'
                ]);

                return redirect()->route('public.subscriptions.dashboard')
                    ->with('error', 'Payment failed or was cancelled.');
            }
        } catch (\Exception $e) {
            \Log::error('Payment callback processing failed', [
                'error' => $e->getMessage(),
                'tx_ref' => $tx_ref
            ]);

            return redirect()->route('public.subscriptions.dashboard')
                ->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    /**
     * Show client login form
     */
    public function showLogin()
    {
        return view('public.subscriptions.login');
    }

    /**
     * Handle client login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('client')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->route('public.subscriptions.dashboard')
                ->with('success', 'Welcome back!');
        }

        return redirect()->back()
            ->with('error', 'Invalid credentials')
            ->withInput($request->only('email'));
    }

    /**
     * Handle client logout
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
