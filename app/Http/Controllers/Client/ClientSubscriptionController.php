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

    public function purchase()
    {
        $subscriptions = Subscription::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('client.subscriptions.purchase', compact('subscriptions'));
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
}
