@extends('layouts.client')

@section('title', 'Tokens - ESA Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap');

    :root {
        --sidebar-w: 260px;
        --bg: #0f1117;
        --surface: #181c27;
        --surface-2: #1e2436;
        --border: rgba(255,255,255,0.07);
        --accent: #4f8ef7;
        --accent-2: #a78bfa;
        --accent-3: #34d399;
        --accent-warm: #fbbf24;
        --text-primary: #f0f2f8;
        --text-muted: #6b7a99;
        --text-dim: #3d4663;
    }

    body {
        font-family: 'DM Sans', sans-serif;
        background: var(--bg);
        color: var(--text-primary);
        min-height: 100vh;
    }

    /* Page content */
    .page-content {
        padding: 32px;
    }

    /* Token Balance Cards */
    .balance-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }

    @media (max-width: 900px) {
        .balance-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }

    .balance-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .balance-card:hover {
        transform: translateY(-2px);
        border-color: rgba(79,142,247,0.3);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }

    .balance-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 24px;
        color: #fff;
    }

    .balance-value {
        font-family: 'Syne', sans-serif;
        font-size: 32px;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 8px;
        line-height: 1;
    }

    .balance-label {
        font-size: 13px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 500;
    }

    /* Transaction History */
    .history-section {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }

    .history-header {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .history-title {
        font-family: 'Syne', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: #fff;
    }

    .history-actions {
        display: flex;
        gap: 12px;
    }

    .btn-history {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 8px;
        color: #fff;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-history:hover {
        background: rgba(255,255,255,0.2);
        border-color: rgba(255,255,255,0.3);
    }

    /* History Table */
    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table thead {
        background: var(--surface-2);
    }

    .history-table th {
        padding: 16px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        border-bottom: 1px solid var(--border);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .history-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background-color 0.2s ease;
    }

    .history-table tbody tr:hover {
        background: var(--surface-2);
    }

    .history-table td {
        padding: 16px;
        font-size: 14px;
        color: var(--text-primary);
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-purchase {
        background: rgba(52,211,153,0.15);
        color: var(--accent-3);
        border: 1px solid rgba(52,211,153,0.2);
    }

    .status-usage {
        background: rgba(251,191,36,0.15);
        color: var(--accent-warm);
        border: 1px solid rgba(251,191,36,0.2);
    }

    .status-completed {
        background: rgba(79,142,247,0.15);
        color: var(--accent);
        border: 1px solid rgba(79,142,247,0.2);
    }

    .status-pending {
        background: rgba(251,191,36,0.15);
        color: var(--accent-warm);
        border: 1px solid rgba(251,191,36,0.2);
    }

    .status-failed {
        background: rgba(239,68,68,0.15);
        color: #ef4444;
        border: 1px solid rgba(239,68,68,0.2);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 16px;
        margin-top: 32px;
    }

    .btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 16px 24px;
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: #fff;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        box-shadow: 0 4px 12px rgba(79,142,247,0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(79,142,247,0.4);
    }

    .btn-secondary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 20px;
        background: var(--surface-2);
        color: var(--text-primary);
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-secondary:hover {
        background: var(--surface);
        border-color: rgba(79,142,247,0.3);
        color: var(--accent);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.4;
        display: block;
    }

    .empty-state h3 {
        font-family: 'Syne', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 14px;
        margin-bottom: 24px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-content {
            padding: 20px;
        }
        
        .balance-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .history-table {
            font-size: 12px;
        }
        
        .history-table th,
        .history-table td {
            padding: 12px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
    }
</style>

<!-- Page Header -->
<div class="topbar">
    <div class="topbar-left">
        <h1>My Tokens</h1>
        <p>Manage your exam tokens</p>
    </div>
    <div class="topbar-right">
        <a href="{{ route('client.tokens.purchase') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            Buy Tokens
        </a>
    </div>
</div>

<!-- Token Balance Cards -->
<div class="balance-grid">
    <div class="balance-card">
        <div class="balance-icon">
            <i class="fas fa-coins"></i>
        </div>
        <div class="balance-value">{{ auth()->guard('client')->user()->getCurrentTokens() ?? 0 }}</div>
        <div class="balance-label">Available Tokens</div>
    </div>
    
    <div class="balance-card">
        <div class="balance-icon" style="background: linear-gradient(135deg, var(--accent-3), var(--accent));">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="balance-value">{{ auth()->guard('client')->user()->getTotalTokensPurchased() ?? 0 }}</div>
        <div class="balance-label">Total Purchased</div>
    </div>
    
    <div class="balance-card">
        <div class="balance-icon" style="background: linear-gradient(135deg, var(--accent-warm), var(--accent-2));">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="balance-value">{{ auth()->guard('client')->user()->getTotalTokensUsed() ?? 0 }}</div>
        <div class="balance-label">Tokens Used</div>
    </div>
</div>

<!-- Transaction History -->
@if($transactions && $transactions->count() > 0)
    <div class="history-section">
        <div class="history-header">
            <h3 class="history-title">Transaction History</h3>
            <div class="history-actions">
                <a href="#" class="btn-history">
                    <i class="fas fa-download"></i>
                    Export
                </a>
                <a href="#" class="btn-history">
                    <i class="fas fa-filter"></i>
                    Filter
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Tokens</th>
                        <th>Bonus</th>
                        <th>Total</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('M j, Y H:i') }}</td>
                            <td>
                                @if($transaction->type === 'purchase')
                                    <span class="status-badge status-purchase">
                                        <i class="fas fa-plus"></i>
                                        Purchase
                                    </span>
                                @elseif($transaction->type === 'usage')
                                    <span class="status-badge status-usage">
                                        <i class="fas fa-minus"></i>
                                        Usage
                                    </span>
                                @else
                                    <span class="status-badge status-pending">
                                        <i class="fas fa-question"></i>
                                        {{ $transaction->type }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($transaction->tokenPackage)
                                    {{ $transaction->tokenPackage->name }} Package
                                @else
                                    {{ $transaction->description ?? 'Token transaction' }}
                                @endif
                            </td>
                            <td class="balance-value">{{ $transaction->tokens ?? 0 }}</td>
                            <td>
                                @if($transaction->bonus_tokens > 0)
                                    <span style="color: var(--accent-3); font-weight: 600;">+{{ $transaction->bonus_tokens }}</span>
                                @else
                                    <span style="color: var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td class="balance-value">{{ $transaction->total_tokens ?? 0 }}</td>
                            <td class="balance-value">{{ number_format($transaction->amount ?? 0) }} RWF</td>
                            <td>
                                @if($transaction->status === 'completed')
                                    <span class="status-badge status-completed">
                                        <i class="fas fa-check"></i>
                                        Completed
                                    </span>
                                @elseif($transaction->status === 'pending')
                                    <span class="status-badge status-pending">
                                        <i class="fas fa-clock"></i>
                                        Pending
                                    </span>
                                @else
                                    <span class="status-badge status-failed">
                                        <i class="fas fa-times"></i>
                                        Failed
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div style="padding: 24px; text-align: center;">
            {{ $transactions->links() }}
        </div>
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-coins"></i>
        <h3>No Token History</h3>
        <p>You haven't had any token transactions yet. Purchase tokens to start taking exams!</p>
        <a href="{{ route('client.tokens.purchase') }}" class="btn-primary">
            <i class="fas fa-plus"></i>
            Buy Your First Tokens
        </a>
    </div>
@endif

<!-- Action Buttons -->
<div class="action-buttons">
    <a href="{{ route('client.tokens.purchase') }}" class="btn-primary">
        <i class="fas fa-plus"></i>
        Purchase More Tokens
    </a>
    <a href="{{ route('client.dashboard') }}" class="btn-secondary">
        <i class="fas fa-arrow-left"></i>
        Back to Dashboard
    </a>
</div>
@endsection
