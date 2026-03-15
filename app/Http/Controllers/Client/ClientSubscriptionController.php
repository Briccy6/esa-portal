<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\ClientSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientSubscriptionController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        
        $activeSubscription = $client->activeSubscription;
        $subscriptions = Subscription::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('client.subscriptions.index', compact('activeSubscription', 'subscriptions'));
    }

    public function purchase(Request $request)
    {
        $subscriptionId = $request->query('id');
        
        if (!$subscriptionId) {
            return redirect()->route('client.subscriptions.index')
                ->with('error', 'Please select a subscription to purchase.');
        }
        
        $subscription = Subscription::find($subscriptionId);
        
        if (!$subscription) {
            return redirect()->route('client.subscriptions.index')
                ->with('error', 'Subscription not found.');
        }

        return view('client.subscriptions.purchase', compact('subscription'));
    }

    public function processPurchase(Request $request, Subscription $subscription)
    {
        $request->validate([
            'payment_method' => 'required|in:flutterwave',
        ]);

        $client = Auth::guard('client')->user();

        // Check if client already has active subscription
        if ($client->hasActiveSubscription()) {
            return back()->with('error', 'You already have an active subscription.');
        }

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

    /**
     * Handle Flutterwave payment callback
     */
    public function handlePaymentCallback(Request $request)
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
                
                \Log::info('Subscription activated successfully', [
                    'client_subscription_id' => $clientSubscription->id,
                    'tx_ref' => $request->tx_ref,
                ]);
                
                return redirect()->route('client.subscriptions.index')
                    ->with('success', 'Subscription activated successfully! You now have access to all premium features.');
            } else {
                \Log::error('Payment failed or subscription not found', [
                    'tx_ref' => $request->tx_ref,
                    'status' => $request->status,
                ]);
                
                return redirect()->route('client.subscriptions.index')
                    ->with('error', 'Payment was not successful. Please contact support if the issue persists.');
            }
        } else {
            \Log::error('Invalid payment callback', [
                'status' => $request->status,
                'data' => $request->all(),
            ]);
            
            return redirect()->route('client.subscriptions.index')
                ->with('error', 'Invalid payment callback received.');
        }
    }
}
