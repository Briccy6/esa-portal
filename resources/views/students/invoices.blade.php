@extends('students.app')
@section('content')
<div class="container py-4">
    <h2>My Invoices</h2>
    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Search invoices...">
    </div>
    <div class="table-responsive">
        <table class="table table-striped" id="invoicesTable">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td data-label="Reference">{{ $invoice->tx_ref }}</td>
                        <td data-label="Type">{{ ucfirst($invoice->type) }}</td>
                        <td data-label="Amount">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</td>
                        <td data-label="Status">
                            @if($invoice->status === 'successful' || $invoice->status === 'paid')
                                <span class="badge bg-success">{{ ucfirst($invoice->status) }}</span>
                            @elseif($invoice->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($invoice->status) }}</span>
                            @endif
                        </td>
                        <td data-label="Date">{{ $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at)->format('Y-m-d') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No invoices found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
/* Enhanced styling for invoices page */
.container {
    max-width: 1200px;
}

h2 {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 2rem;
    position: relative;
    padding-bottom: 0.5rem;
}

h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 2px;
}

/* Search input styling */
#searchInput {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    background: white;
}

#searchInput:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    outline: none;
}

#searchInput::placeholder {
    color: #8e9aaf;
    font-style: italic;
}

/* Table container styling */
.table-responsive {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    background: white;
    border: none;
}

.table {
    margin-bottom: 0;
    background: white;
}

.table thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1.25rem 1rem;
    position: relative;
}

.table thead th::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: rgba(255,255,255,0.2);
}

.table tbody tr {
    transition: all 0.3s ease;
    border: none;
}

.table tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
    transform: translateX(2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

.table tbody tr:nth-child(even):hover {
    background-color: rgba(102, 126, 234, 0.08);
}

.table tbody td {
    padding: 1rem;
    border: none;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
    font-weight: 500;
}

.table tbody tr:last-child td {
    border-bottom: none;
}

    @media (max-width: 640px) {
        .table-responsive {
            overflow: visible;
        }

        .table {
            border: none;
        }

        .table thead {
            display: none;
        }

        .table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .table tbody td {
            display: flex;
            justify-content: space-between;
            padding: 0.85rem 1rem;
            font-size: 0.95rem;
            border-bottom: 1px solid #f1f3f5;
        }

        .table tbody td:last-child {
            border-bottom: none;
        }

        .table tbody td::before {
            content: attr(data-label);
            font-weight: 700;
            color: #6c757d;
            margin-right: 0.75rem;
            flex: 1 0 45%;
            white-space: normal;
        }

        .table tbody td {
            flex-wrap: wrap;
        }
    }
.badge.bg-success {
    background-color: #28a745 !important;
    color: white;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
}

.badge.bg-danger {
    background-color: #dc3545 !important;
    color: white;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

/* Amount column styling */
.table tbody td:nth-child(3) {
    font-weight: 700;
    color: #2c3e50;
    font-size: 1.05rem;
}

/* Reference column styling */
.table tbody td:nth-child(1) {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    color: #6c757d;
    background-color: rgba(108, 117, 125, 0.05);
    border-radius: 4px;
    padding: 0.5rem 0.75rem;
}

/* Date column styling */
.table tbody td:nth-child(5) {
    color: #6c757d;
    font-size: 0.9rem;
}

/* Empty state styling */
.table tbody tr td.text-center {
    padding: 4rem 1rem;
    color: #8e9aaf;
    font-style: italic;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    font-size: 1.1rem;
}

.table tbody tr td.text-center::before {
    content: '📄';
    display: block;
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .table tbody tr:hover {
        transform: none;
    }
    
    .table thead th,
    .table tbody td {
        padding: 0.75rem 0.5rem;
        font-size: 0.85rem;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
    }
    
    h2 {
        font-size: 1.5rem;
    }
    
    #searchInput {
        padding: 0.6rem 0.8rem;
        font-size: 0.9rem;
    }
    
    .table tbody td:nth-child(1) {
        font-size: 0.8rem;
    }
    
    .table tbody td:nth-child(3) {
        font-size: 0.95rem;
    }
}

/* Search animation */
@keyframes searchPulse {
    0% { box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    50% { box-shadow: 0 4px 20px rgba(102, 126, 234, 0.1); }
    100% { box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
}

#searchInput:focus {
    animation: searchPulse 2s infinite;
}

/* Custom scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('invoicesTable');
    searchInput.addEventListener('keyup', function () {
        const filter = searchInput.value.toLowerCase();
        const rows = table.tBodies[0].rows;
        for (let i = 0; i < rows.length; i++) {
            let row = rows[i];
            let cells = row.getElementsByTagName('td');
            let rowContainsFilter = false;
            for (let j = 0; j < cells.length; j++) {
                let cell = cells[j];
                if (cell) {
                    let cellText = cell.textContent || cell.innerText;
                    if (cellText.toLowerCase().indexOf(filter) > -1) {
                        rowContainsFilter = true;
                        break;
                    }
                }
            }
            row.style.display = rowContainsFilter ? '' : 'none';
        }
    });
});
</script>
@endsection
