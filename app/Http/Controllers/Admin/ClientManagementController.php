<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Models\ClientTransaction;
use App\Models\ClientToken;
use App\Models\TokenPackage;
use App\Models\Subscription;
use App\Models\ClientSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class ClientManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['activeSubscription', 'transactions' => function($query) {
            $query->latest()->limit(5);
        }]);

        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $clients = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('is_active', true)->count(),
            'inactive_clients' => Client::where('is_active', false)->count(),
            'total_tokens_sold' => ClientTransaction::where('type', 'purchase')->sum('total_tokens'),
            'total_revenue' => ClientTransaction::where('type', 'purchase')->sum('amount'),
        ];

        return view('admin.clients.index', compact('clients', 'stats'));
    }

    public function show(Client $client)
    {
        $client->load(['activeSubscription', 'transactions' => function($query) {
            $query->with('tokenPackage')->latest()->limit(10);
        }, 'subscriptions' => function($query) {
            $query->with('subscription')->latest()->limit(5);
        }]);

        // Calculate token statistics
        $tokenStats = [
            'current_balance' => $client->getCurrentTokens(),
            'total_purchased' => $client->transactions()->where('type', 'purchase')->sum('total_tokens'),
            'total_used' => $client->transactions()->where('type', 'usage')->sum('amount_deducted'),
            'total_bonus' => $client->transactions()->where('type', 'purchase')->sum('bonus_tokens'),
        ];

        // Recent transactions
        $recentTransactions = $client->transactions()
            ->with('tokenPackage')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.clients.show', compact('client', 'tokenStats', 'recentTransactions'));
    }

    public function addTokens(Client $client)
    {
        $tokenPackages = TokenPackage::where('is_active', true)
            ->orderBy('tokens')
            ->get();

        return view('admin.clients.add-tokens', compact('client', 'tokenPackages'));
    }

    public function storeTokens(Request $request, Client $client)
    {
        $request->validate([
            'amount' => 'required|integer|min:1|max:10000',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:purchase,bonus,manual,subscription',
            'package_id' => 'nullable|exists:token_packages,id',
            'payment_status' => 'required|in:pending,completed,failed',
            'payment_method' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        // Calculate tokens based on package or manual amount
        $tokensToAdd = 0;
        $bonusTokens = 0;
        $amount = 0;

        if ($request->package_id) {
            $package = TokenPackage::findOrFail($request->package_id);
            $tokensToAdd = $package->tokens;
            $bonusTokens = $package->bonus_tokens;
            $amount = $package->price;
            $description = "Purchased {$package->name} package";
        } else {
            $tokensToAdd = $request->amount;
            $bonusTokens = $request->bonus_tokens ?? 0;
            $amount = $request->payment_amount ?? 0;
            $description = $request->description;
        }

        // Create client transaction
        $transaction = ClientTransaction::create([
            'client_id' => $client->id,
            'token_package_id' => $request->package_id,
            'tokens' => $tokensToAdd,
            'bonus_tokens' => $bonusTokens,
            'total_tokens' => $tokensToAdd + $bonusTokens,
            'amount' => $amount,
            'currency' => 'RWF',
            'type' => $request->type,
            'description' => $description,
            'status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'created_at' => $request->payment_date ? Carbon::parse($request->payment_date) : now(),
        ]);

        // Update client token balance
        $currentBalance = $client->getCurrentTokens();
        ClientToken::create([
            'client_id' => $client->id,
            'balance' => $currentBalance + $tokensToAdd + $bonusTokens,
            'amount_added' => $tokensToAdd + $bonusTokens,
            'description' => $description,
            'type' => $request->type,
        ]);

        return redirect()->route('admin.clients.show', $client)
            ->with('success', "Successfully added {$tokensToAdd} tokens to {$client->name}");
    }

    public function recordSubscription(Client $client)
    {
        $subscriptions = Subscription::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('admin.clients.subscription', compact('client', 'subscriptions'));
    }

    public function storeSubscription(Request $request, Client $client)
    {
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'payment_status' => 'required|in:pending,completed,failed',
            'payment_method' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:255',
            'payment_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $subscription = Subscription::findOrFail($request->subscription_id);

        // Create client subscription
        $clientSubscription = ClientSubscription::create([
            'client_id' => $client->id,
            'subscription_id' => $subscription->id,
            'payment_status' => $request->payment_status,
            'starts_at' => $request->starts_at ? Carbon::parse($request->starts_at) : now(),
            'ends_at' => $request->ends_at ? Carbon::parse($request->ends_at) : now()->addMonths($subscription->duration_months),
            'is_active' => $request->payment_status === 'completed',
            'amount' => $request->payment_amount,
            'currency' => 'RWF',
        ]);

        // Add subscription tokens as bonus
        if ($request->payment_status === 'completed') {
            $currentBalance = $client->getCurrentTokens();
            
            ClientToken::create([
                'client_id' => $client->id,
                'balance' => $currentBalance + $subscription->tokens_included,
                'amount_added' => $subscription->tokens_included,
                'description' => "Subscription: {$subscription->name}",
                'type' => 'subscription',
            ]);

            ClientTransaction::create([
                'client_id' => $client->id,
                'tokens' => $subscription->tokens_included,
                'bonus_tokens' => 0,
                'total_tokens' => $subscription->tokens_included,
                'amount' => $request->payment_amount,
                'currency' => 'RWF',
                'type' => 'subscription',
                'description' => "Subscription: {$subscription->name}",
                'status' => 'completed',
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'created_at' => $request->payment_date ? Carbon::parse($request->payment_date) : now(),
            ]);
        }

        return redirect()->route('admin.clients.show', $client)
            ->with('success', "Subscription recorded successfully for {$client->name}");
    }

    public function toggleStatus(Client $client)
    {
        $client->is_active = !$client->is_active;
        $client->save();

        $status = $client->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.clients.index')
            ->with('success', "Client {$client->name} has been {$status}");
    }

    public function destroy(Client $client)
    {
        // Soft delete by deactivating
        $client->is_active = false;
        $client->save();

        return redirect()->route('admin.clients.index')
            ->with('success', "Client {$client->name} has been deactivated");
    }
}
