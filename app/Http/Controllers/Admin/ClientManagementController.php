<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Models\ClientTransaction;
use App\Models\ClientToken;
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

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        // Generate a random password
        $plainPassword = $this->generateRandomPassword();
        
        // Create client with hashed password
        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => bcrypt($plainPassword), // Hash the password
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        // Send password to client via email
        try {
            \Mail::to($client->email)->send(new \App\Mail\ClientWelcomeMail($client, $plainPassword));
        } catch (\Exception $e) {
            \Log::error('Failed to send client welcome email: ' . $e->getMessage());
        }

        return redirect()->route('admin.clients.show', $client)
            ->with('success', "Client {$client->name} has been created successfully! Password has been sent to {$client->email}");
    }

    /**
     * Generate a random password
     */
    private function generateRandomPassword($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $password;
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $client->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.clients.show', $client)
            ->with('success', "Client {$client->name} has been updated successfully!");
    }

    public function show(Client $client)
    {
        $client->load(['activeSubscription', 'transactions' => function($query) {
            $query->with('subscription')->latest()->limit(10);
        }, 'subscriptions' => function($query) {
            $query->with('subscription')->latest()->limit(5);
        }]);

        // Calculate token statistics
        $tokenStats = [
            'current_balance' => $client->getCurrentTokens(),
            'total_purchased' => $client->tokens()->where('type', 'purchase')->sum('amount_added'),
            'total_used' => $client->tokens()->where('type', 'usage')->sum('amount_deducted'),
            'total_bonus' => $client->tokens()->where('type', 'bonus')->sum('amount_added'),
        ];

        // Recent transactions
        $recentTransactions = $client->transactions()
            ->with('subscription')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.clients.show', compact('client', 'tokenStats', 'recentTransactions'));
    }

    public function addTokens(Client $client)
    {
        $subscriptions = Subscription::where('is_active', true)
            ->orderBy('tokens_included')
            ->get();

        return view('admin.clients.add-tokens', compact('client', 'subscriptions'));
    }

    public function storeTokens(Request $request, Client $client)
    {
        $request->validate([
            'amount' => 'required|integer|min:1|max:10000',
            'description' => 'nullable|string|max:255',
            'type' => 'required|in:purchase,bonus,manual,subscription',
            'subscription_id' => 'nullable|exists:subscriptions,id',
            'payment_status' => 'required|in:pending,completed,failed',
            'payment_method' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        // Calculate tokens based on package or manual amount
        $tokensToAdd = 0;
        $bonusTokens = 0;
        $amount = 0;
        $subscription = null;

        if ($request->subscription_id) {
            $subscription = Subscription::findOrFail($request->subscription_id);
            $tokensToAdd = $subscription->tokens_included;
            $bonusTokens = 0;
            $amount = $subscription->price;
            $description = "Purchased {$subscription->name} subscription";
        } else {
            $tokensToAdd = $request->amount;
            $bonusTokens = $request->bonus_tokens ?? 0;
            $amount = $request->payment_amount ?? 0;
            $description = $request->description;
        }

        // Generate transaction reference for manual transactions
        $txRef = 'MAN-' . strtoupper(uniqid()) . '-' . time();

        DB::beginTransaction();
        try {
            // Create client subscription if subscription_id is provided
            $clientSubscription = null;
            if ($subscription && $request->payment_status === 'completed') {
                $clientSubscription = ClientSubscription::create([
                    'client_id' => $client->id,
                    'subscription_id' => $subscription->id,
                    'payment_status' => $request->payment_status,
                    'payment_reference' => $request->payment_reference,
                    'paid_amount' => $amount,
                    'amount' => $amount,
                    'currency' => 'RWF',
                    'payment_gateway' => $request->payment_method,
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($subscription->duration_days),
                    'is_active' => true,
                ]);
            }

            // Create client transaction
            $transaction = ClientTransaction::create([
                'client_id' => $client->id,
                'subscription_id' => $request->subscription_id,
                'tx_ref' => $txRef,
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
                'paid_at' => $request->payment_status === 'completed' ? now() : null,
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
                'created_at' => $request->payment_date ? Carbon::parse($request->payment_date) : now(),
            ]);

            DB::commit();

            $successMessage = $subscription 
                ? "Successfully added {$tokensToAdd} tokens and assigned {$subscription->name} subscription to {$client->name}"
                : "Successfully added {$tokensToAdd} tokens to {$client->name}";

            return redirect()->route('admin.clients.show', $client)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'An error occurred while adding tokens: ' . $e->getMessage())
                ->withInput();
        }
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
            'ends_at' => $request->ends_at ? Carbon::parse($request->ends_at) : now()->addMonths($subscription->duration_months ?? 1),
            'is_active' => $request->payment_status === 'completed',
            'amount' => $request->payment_amount,
            'currency' => 'RWF',
        ]);

        // Add subscription tokens automatically when payment is completed
        if ($request->payment_status === 'completed') {
            $currentBalance = $client->getCurrentTokens();
            
            ClientToken::create([
                'client_id' => $client->id,
                'balance' => $currentBalance + $subscription->tokens_included,
                'amount_added' => $subscription->tokens_included,
                'description' => "Subscription: {$subscription->name}",
                'type' => 'subscription',
                'created_at' => $request->payment_date ? Carbon::parse($request->payment_date) : now(),
            ]);

            // Generate transaction reference for subscription
            $txRef = 'SUB-' . strtoupper(uniqid()) . '-' . time();

            ClientTransaction::create([
                'client_id' => $client->id,
                'subscription_id' => $subscription->id,
                'tx_ref' => $txRef,
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
                'paid_at' => $request->payment_date ? Carbon::parse($request->payment_date) : now(),
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
