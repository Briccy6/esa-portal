@extends('staff.partials.sidebar_sec')

@section('title', 'Subscription Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Subscription Payment Reports</h5>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ number_format($totalSubscriptions) }}</h4>
                                            <p class="mb-0">Total Subscriptions</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ number_format($totalRevenue, 0) }} RWF</h4>
                                            <p class="mb-0">Total Revenue</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-money-bill-wave fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ number_format($totalTokens) }}</h4>
                                            <p class="mb-0">Total Tokens Distributed</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-coins fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Buttons -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6>Subscription Payment History</h6>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" onclick="try { exportToCSV(); } catch(e) { console.error('Export error:', e); alert('Unable to export. Please try again.'); }">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="try { window.print(); } catch(e) { console.error('Print error:', e); alert('Unable to print. Please try again.'); }">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="date" id="filterStartDate" class="form-control" placeholder="Start Date">
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="filterEndDate" class="form-control" placeholder="End Date">
                        </div>
                        <div class="col-md-3">
                            <select id="filterTier" class="form-select">
                                <option value="">All Tiers</option>
                                <option value="basic">Basic</option>
                                <option value="standard">Standard</option>
                                <option value="premium">Premium</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="filterStudent" class="form-control" placeholder="Search student...">
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="subscriptionsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Subscription</th>
                                    <th>Tier</th>
                                    <th>Amount Paid</th>
                                    <th>Tokens Added</th>
                                    <th>Payment Method</th>
                                    <th>Reference</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscriptions as $subscription)
                                    <tr>
                                        <td>{{ $subscription->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <strong>{{ $subscription->student->full_name }}</strong><br>
                                            <small class="text-muted">{{ $subscription->student->reg_number }}</small>
                                        </td>
                                        <td>{{ $subscription->subscription->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $subscription->subscription->tier === 'premium' ? 'warning' : ($subscription->subscription->tier === 'standard' ? 'info' : 'secondary') }}">
                                                {{ ucfirst($subscription->subscription->tier) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($subscription->paid_amount, 0) }} RWF</td>
                                        <td>
                                            <span class="badge bg-success">{{ number_format($subscription->subscription->tokens_included) }} tokens</span>
                                        </td>
                                        <td>
                                            @php
                                                $metaRaw = $subscription->meta ?? '{}';
                                                $meta = is_array($metaRaw) ? $metaRaw : json_decode($metaRaw, true);
                                                $method = $meta['method'] ?? 'Unknown';
                                            @endphp
                                            <span class="badge bg-light text-dark">{{ ucfirst($method) }}</span>
                                        </td>
                                        <td>{{ $subscription->payment_reference ?? '-' }}</td>
                                        <td>
                                            @if($subscription->payment_status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No subscription payments recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('subscriptionsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    function filterTable() {
        const startDate = document.getElementById('filterStartDate').value;
        const endDate = document.getElementById('filterEndDate').value;
        const tier = document.getElementById('filterTier').value.toLowerCase();
        const studentSearch = document.getElementById('filterStudent').value.toLowerCase();
        
        Array.from(rows).forEach(row => {
            const date = row.cells[0].textContent.trim();
            const student = row.cells[1].textContent.toLowerCase();
            const subscriptionTier = row.cells[3].textContent.toLowerCase();
            const amount = row.cells[4].textContent;
            
            let showRow = true;
            
            // Date filter
            if (startDate) {
                const rowDate = new Date(date);
                const start = new Date(startDate);
                if (rowDate < start) showRow = false;
            }
            
            if (endDate) {
                const rowDate = new Date(date);
                const end = new Date(endDate);
                if (rowDate > end) showRow = false;
            }
            
            // Tier filter
            if (tier && !subscriptionTier.includes(tier)) {
                showRow = false;
            }
            
            // Student search filter
            if (studentSearch && !student.includes(studentSearch)) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    document.getElementById('filterStartDate').addEventListener('change', filterTable);
    document.getElementById('filterEndDate').addEventListener('change', filterTable);
    document.getElementById('filterTier').addEventListener('change', filterTable);
    document.getElementById('filterStudent').addEventListener('input', filterTable);
});

function exportToCSV() {
    const table = document.getElementById('subscriptionsTable');
    const rows = table.getElementsByTagName('tr');
    let csv = [];
    
    // Header
    const headers = ['Date', 'Student', 'Subscription', 'Tier', 'Amount Paid', 'Tokens Added', 'Payment Method', 'Reference', 'Status'];
    csv.push(headers.join(','));
    
    // Data rows
    for (let i = 1; i < rows.length; i++) {
        if (rows[i].style.display !== 'none') {
            const cells = rows[i].getElementsByTagName('td');
            const rowData = [
                cells[0].textContent.trim(),
                '"' + cells[1].textContent.trim().replace(/"/g, '""') + '"',
                '"' + cells[2].textContent.trim().replace(/"/g, '""') + '"',
                cells[3].textContent.trim(),
                cells[4].textContent.trim(),
                cells[5].textContent.trim(),
                cells[6].textContent.trim(),
                '"' + (cells[7].textContent.trim() || '-') + '"',
                cells[8].textContent.trim()
            ];
            csv.push(rowData.join(','));
        }
    }
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'subscription_report_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}
</script>
@endsection
@endsection
