<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TokenPackage;
use App\Models\TokenTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientTokenController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        
        $transactions = $client->transactions()
            ->with('tokenPackage')
            ->latest()
            ->paginate(20);

        return view('client.tokens.index', compact('transactions'));
    }

    public function purchase()
    {
        $tokenPackages = TokenPackage::where('is_active', true)
            ->orderBy('tokens')
            ->get();

        return view('client.tokens.purchase', compact('tokenPackages'));
    }

    public function processPurchase(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:token_packages,id',
            'payment_method' => 'required|in:flutterwave',
        ]);

        $client = Auth::guard('client')->user();
        $package = TokenPackage::findOrFail($request->package_id);

        // Create transaction
        $transaction = TokenTransaction::create([
            'client_id' => $client->id,
            'token_package_id' => $package->id,
            'amount' => $package->price,
            'tokens' => $package->tokens,
            'bonus_tokens' => $package->bonus_tokens,
            'total_tokens' => $package->tokens + $package->bonus_tokens,
            'type' => 'purchase',
            'status' => 'pending',
        ]);

        // Initialize Flutterwave payment
        $paymentData = [
            'tx_ref' => 'TOKEN_' . time() . '_' . $client->id,
            'amount' => $package->price * 100, // Convert to cents
            'currency' => 'RWF',
            'payment_options' => 'card,banktransfer',
            'customer' => [
                'email' => $client->email,
                'name' => $client->name,
            ],
            'customizations' => [
                'title' => 'Token Purchase',
                'description' => "Purchase of {$package->tokens} tokens",
            ],
            'meta' => [
                'transaction_id' => $transaction->id,
                'type' => 'token_purchase',
            ],
        ];

        return redirect()->away('https://checkout.flutterwave.com/v3/payment?' . http_build_query($paymentData));
    }
}
