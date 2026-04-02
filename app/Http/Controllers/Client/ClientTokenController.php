<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\ClientSubscription;
use App\Models\TokenTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientTokenController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        
        $transactions = $client->transactions()
            ->with('subscription')
            ->latest()
            ->paginate(20);

        return view('client.tokens.index', compact('transactions'));
    }

    public function purchase()
    {
        $tokenPackages = Subscription::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('client.tokens.purchase', compact('tokenPackages'));
    }

    public function processPurchase(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'payment_method' => 'required|in:flutterwave',
        ]);

        $client = Auth::guard('client')->user();

        // Check if client already has active subscription
        if ($client->hasActiveSubscription()) {
            return back()->with('error', 'You already have an active subscription.');
        }

        $subscription = Subscription::findOrFail($request->subscription_id);

        // Create client subscription
        $clientSubscription = ClientSubscription::create([
            'client_id' => $client->id,
            'subscription_id' => $subscription->id,
            'starts_at' => now(),
            'ends_at' => now()->addDays($subscription->duration_days),
            'status' => 'pending',
        ]);

        // Initialize Flutterwave payment
        $paymentData = [
            'tx_ref' => 'SUB_' . time() . '_' . $client->id,
            'amount' => $subscription->price * 100, // Convert to cents
            'currency' => 'RWF',
            'payment_options' => 'card,banktransfer',
            'customer' => [
                'email' => $client->email,
                'name' => $client->name,
            ],
            'customizations' => [
                'title' => 'Subscription Purchase',
                'description' => "Purchase of {$subscription->name} subscription",
            ],
            'meta' => [
                'client_subscription_id' => $clientSubscription->id,
                'type' => 'subscription',
            ],
        ];

        return redirect()->away('https://checkout.flutterwave.com/v3/payment?' . http_build_query($paymentData));
    }

    public function handleCallback(Request $request)
    {
        \Log::info('Flutterwave callback received', ['data' => $request->all()]);
        
        // Verify the payment was successful
        if ($request->status === 'successful') {
            // Find the client subscription
            $clientSubscription = ClientSubscription::where('tx_ref', $request->tx_ref)
                ->where('status', 'pending')
                ->first();
            
            if ($clientSubscription) {
                // Update subscription status to active
                $clientSubscription->update([
                    'status' => 'active',
                    'payment_reference' => $request->tx_ref,
                    'paid_at' => now(),
                ]);
                
                // Add tokens to client's token balance
                $client = Auth::guard('client')->user();
                $client->tokens()->create([
                    'balance' => $client->tokens->balance + $clientSubscription->subscription->tokens_included,
                    'total_earned' => $client->tokens->total_earned + $clientSubscription->subscription->tokens_included,
                    'total_spent' => $client->tokens->total_spent,
                    'last_earned_at' => now(),
                ]);
                
                \Log::info('Subscription activated successfully', [
                    'client_subscription_id' => $clientSubscription->id,
                    'tx_ref' => $request->tx_ref,
                    'tokens_added' => $clientSubscription->subscription->tokens_included,
                ]);
                
                return redirect()->route('client.tokens.index')
                    ->with('success', 'Subscription activated successfully! You now have access to all premium features plus ' . $clientSubscription->subscription->tokens_included . ' tokens.');
            } else {
                \Log::error('Payment failed or subscription not found', [
                    'tx_ref' => $request->tx_ref,
                    'status' => $request->status,
                ]);
                
                return redirect()->route('client.tokens.index')
                    ->with('error', 'Payment was not successful. Please contact support if the issue persists.');
            }
        } else {
            \Log::error('Invalid payment callback', [
                'status' => $request->status,
                'data' => $request->all(),
            ]);
            
            return redirect()->route('client.tokens.index')
                ->with('error', 'Invalid payment callback received.');
        }
    }
}
