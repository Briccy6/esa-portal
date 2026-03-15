@extends('layouts.client')

@section('title', 'My Tokens - ESA Portal')

@section('content')
<style>
:root {
    --bg: #f4f6fb;
    --surface: #ffffff;
    --surface-2: #f8f9fc;
    --border: #e5e7eb;
    --accent: #7c3aed;
    --accent-2: #8b5cf6;
    --accent-3: #06b6d4;
    --accent-warm: #f59e0b;
    --text-primary: #1f2937;
    --text-muted: #6b7280;
    --text-dim: #9ca3af;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg);
    color: var(--text-primary);
    line-height: 1.6;
}

/* Topbar */
.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    border-radius: 12px 12px 0 0;
    margin-bottom: 24px;
}

.topbar-left h1 {
    font-family: 'Syne', sans-serif;
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.topbar-left p {
    font-size: 14px;
    color: var(--text-muted);
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Page Content Wrapper */
.page-content {
    padding: 0 24px 24px;
}

/* Balance Cards Grid */
.balance-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.balance-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    text-align: center;
}

.balance-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--accent), var(--accent-2));
}

.balance-card.available::before {
    background: linear-gradient(90deg, #7c3aed, #8b5cf6);
}

.balance-card.purchased::before {
    background: linear-gradient(90deg, #06b6d4, #3b82f6);
}

.balance-card.used::before {
    background: linear-gradient(90deg, #f59e0b, #ef4444);
}

.balance-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.balance-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 24px;
    color: white;
}

.balance-card.available .balance-icon {
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
}

.balance-card.purchased .balance-icon {
    background: linear-gradient(135deg, #06b6d4, #3b82f6);
}

.balance-card.used .balance-icon {
    background: linear-gradient(135deg, #f59e0b, #ef4444);
}

.balance-value {
    font-family: 'Syne', sans-serif;
    font-size: 36px;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 8px;
}

.balance-label {
    font-size: 14px;
    color: var(--text-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

/* Transaction History Section */
.history-section {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}

.history-header {
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    padding: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.history-title {
    font-family: 'Syne', sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: white;
}

.history-actions {
    display: flex;
    gap: 12px;
}

.btn-history {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: white;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-history:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
}

/* Transaction Table */
.table-container {
    overflow-x: auto;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.history-table thead {
    background: var(--surface-2);
}

.history-table th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: var(--text-primary);
    border-bottom: 1px solid var(--border);
    font-size: 13px;
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
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.status-usage {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.status-completed {
    background: rgba(79, 142, 247, 0.1);
    color: var(--accent);
    border: 1px solid rgba(79, 142, 247, 0.2);
}

.status-pending {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.2);
}

.status-failed {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    color: white;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
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
    opacity: 0.3;
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

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    padding: 24px;
}

.pagination-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text-muted);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.pagination-link:hover {
    background: var(--surface-2);
    border-color: var(--accent);
    color: var(--accent);
}

.pagination-link.active {
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    border-color: var(--accent);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
    }
    
    .topbar-right {
        width: 100%;
        justify-content: flex-start;
    }
    
    .balance-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .page-content {
        padding: 0 16px 16px;
    }
    
    .balance-value {
        font-size: 28px;
    }
    
    .history-table {
        font-size: 12px;
    }
    
    .history-table th,
    .history-table td {
        padding: 12px 8px;
    }
    
    .history-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .history-actions {
        width: 100%;
        justify-content: flex-start;
    }
}

@media (max-width: 480px) {
    .history-table {
        font-size: 11px;
    }
    
    .history-table th,
    .history-table td {
        padding: 8px 4px;
    }
}
</style>

<!-- Topbar -->
<div class="topbar">
    <div class="topbar-left">
        <h1>My Tokens</h1>
        <p>Manage your exam tokens and view transaction history</p>
    </div>
    <div class="topbar-right">
        <a href="{{ route('client.tokens.purchase') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Buy Tokens
        </a>
    </div>
</div>

<!-- Page Content -->
<div class="page-content">
    <!-- Balance Cards -->
    <div class="balance-grid">
        <div class="balance-card available">
            <div class="balance-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div class="balance-value">{{ auth()->guard('client')->user()->getCurrentTokens() ?? 0 }}</div>
            <div class="balance-label">Available Tokens</div>
        </div>

        <div class="balance-card purchased">
            <div class="balance-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="balance-value">{{ auth()->guard('client')->user()->getTotalTokensPurchased() ?? 0 }}</div>
            <div class="balance-label">Total Purchased</div>
        </div>

        <div class="balance-card used">
            <div class="balance-icon">
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

            <div class="table-container">
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
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>
                                    @if($transaction->subscription)
                                        {{ $transaction->subscription->name }}
                                    @else
                                        {{ $transaction->description ?? 'Token transaction' }}
                                    @endif
                                </td>
                                <td>{{ $transaction->tokens ?? 0 }}</td>
                                <td>{{ $transaction->bonus_tokens ?? 0 }}</td>
                                <td>{{ $transaction->total_tokens ?? 0 }}</td>
                                <td>{{ $transaction->formatted_amount ?? '-' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $transaction->status ?? $transaction->type }}">
                                        {{ ucfirst($transaction->status ?? $transaction->type) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="pagination">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-coins"></i>
            <h3>No Transactions Yet</h3>
            <p>You haven't made any token transactions yet. Purchase tokens to get started!</p>
            <a href="{{ route('client.tokens.purchase') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Buy Tokens
            </a>
        </div>
    @endif
</div>
@endsection
