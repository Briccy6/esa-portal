@extends('admin.layout')

@section('title', 'Admin Reports')

@push('styles')
<style>
    .reports-container {
        background: #f8f9fa;
        min-height: 100vh;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: 0.375rem;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        border-bottom: none;
    }
    
    .form-floating > label {
        color: #6c757d;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.5);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.4);
    }
    
    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.5);
    }
    
    .btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border: none;
        box-shadow: 0 2px 4px rgba(23, 162, 184, 0.4);
    }
    
    .btn-info:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(23, 162, 184, 0.5);
    }
    
    .search-box {
        position: relative;
    }
    
    .search-box input {
        padding-left: 2.5rem;
        border-radius: 25px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .search-box input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 5;
    }
    
    .professional-table {
        background: white;
        border-radius: 0.375rem;
        overflow: hidden;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .professional-table th {
        background: linear-gradient(135deg, #495057 0%, #343a40 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.875rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
        border: none;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .professional-table td {
        padding: 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
        font-size: 0.9rem;
    }
    
    .professional-table tbody tr {
        transition: all 0.3s ease;
    }
    
    .professional-table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .professional-table tbody tr:nth-child(even) {
        background-color: #fbfbfb;
    }
    
    .total-summary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        border-radius: 0.375rem;
        font-weight: 600;
        font-size: 1.1rem;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.4);
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
    
    .table-actions {
        background: white;
        padding: 1rem;
        border-radius: 0.375rem 0.375rem 0 0;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 0;
    }
    
    .no-results {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
        font-style: italic;
    }
    
    /* Print Styles */
    @media print {
        .reports-container {
            background: white !important;
        }
        
        .card {
            box-shadow: none !important;
            border: 1px solid #000 !important;
        }
        
        .btn, .search-box, .form-floating {
            display: none !important;
        }
        
        .professional-table {
            box-shadow: none !important;
        }
        
        .professional-table th {
            background: #f8f9fa !important;
            color: #000 !important;
            border: 1px solid #000 !important;
        }
        
        .professional-table td {
            border: 1px solid #000 !important;
            padding: 0.5rem !important;
            font-size: 0.8rem !important;
        }
        
        .professional-table tbody tr:hover {
            transform: none !important;
            box-shadow: none !important;
        }
        
        .total-summary {
            background: #f8f9fa !important;
            color: #000 !important;
            border: 1px solid #000 !important;
        }
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .professional-table {
            font-size: 0.8rem;
        }
        
        .professional-table th,
        .professional-table td {
            padding: 0.5rem 0.25rem;
        }
    }

    .pagination {
        justify-content: center;
        margin-top: 1rem;
    }

    .pagination .page-item .page-link {
        border-radius: 50% !important;
        margin: 0 3px;
        color: #667eea;
        border: none;
        background: #fff;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.08);
        transition: all 0.2s;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        min-height: 38px;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.15);
        border: none;
    }

    .pagination .page-item .page-link:hover {
        background: #f8f9fa;
        color: #764ba2;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.12);
    }

    .pagination .page-item.disabled .page-link {
        color: #ccc;
        background: #fff;
        border: none;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .pagination .page-link i {
        font-size: 1.2rem;
        vertical-align: middle;
    }

    /* Multi-select dropdown styling */
    #terms {
        min-height: 58px;
        padding: 0.75rem 2.25rem 0.75rem 0.75rem !important;
    }

    #terms option {
        padding: 0.5rem;
        border-bottom: 1px solid #e9ecef;
    }

    #terms option:last-child {
        border-bottom: none;
    }

    #terms option:checked {
        background-color: #667eea;
        color: white;
    }

    .form-floating > select.form-select[multiple] {
        background-image: none;
        padding-right: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="reports-container">
    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="display-6 fw-bold text-dark mb-1">
                    <i class="bi bi-bar-chart-fill me-2 text-primary"></i>Admin Reports
                </h1>
                <p class="text-muted">Generate and analyze comprehensive business reports</p>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel-fill me-2"></i>Report Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" id="reportForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="report_type" id="report_type" class="form-select">
                                    <option value="expenses" {{ request('report_type') == 'expenses' ? 'selected' : '' }}>Expenses Report</option>
                                    <option value="student_transactions" {{ request('report_type') == 'student_transactions' ? 'selected' : '' }}>Student Transactions</option>
                                    <option value="teacher_dailies" {{ request('report_type') == 'teacher_dailies' ? 'selected' : '' }}>Teacher Dailies</option>
                                </select>
                                <label for="report_type">Report Type</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="terms[]" id="terms" class="form-select" multiple size="1" style="height: auto;">
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" 
                                            {{ in_array($term->id, request('terms', [])) ? 'selected' : '' }}>
                                            {{ $term->name }} - {{ $term->academicYear->year }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="terms">Terms (Hold Ctrl/Cmd to select multiple)</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="from" id="from" class="form-control" value="{{ request('from', $from) }}">
                                <label for="from">From Date</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" name="to" id="to" class="form-control" value="{{ request('to', $to) }}">
                                <label for="to">To Date</label>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 py-3">
                                <i class="bi bi-search me-2"></i>Generate Report
                            </button>
                        </div>
                    </div>
                    
                    @if(request('terms'))
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <div>
                                        <strong>Selected Terms:</strong>
                                        @foreach($terms->whereIn('id', request('terms')) as $selectedTerm)
                                            <span class="badge bg-primary me-1">{{ $selectedTerm->name }} - {{ $selectedTerm->academicYear->year }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        @if(request('report_type') == 'expenses')
            <!-- Expenses Report -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Expenses Report</h5>
                </div>
                
                <div class="table-actions">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" id="searchExpenses" class="form-control" placeholder="Search expenses...">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('admin.expenses.export', ['from' => $from, 'to' => $to, 'terms' => request('terms', [])]) }}" class="btn btn-warning me-2" id="exportExpensesBtn">
                                <i class="bi bi-file-earmark-arrow-down me-2"></i>Export Excel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table professional-table mb-0" id="expenses-table">
                        <thead>
                            <tr>
                                <th><i class="bi bi-calendar3 me-1"></i>Date</th>
                                <th><i class="bi bi-card-text me-1"></i>Description</th>
                                <th><i class="bi bi-currency-dollar me-1"></i>Amount</th>
                                <th><i class="bi bi-person-check me-1"></i>Paid By</th>
                                <th><i class="bi bi-person-check me-1"></i>Paid To</th>
                                <th><i class="bi bi-building me-1"></i>Branch</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td class="fw-bold text-end">RWf{{ number_format($expense->amount, 2) }}</td>
                                    <td>{{ $expense->paid_by_staff->full_name ?? 'N/A' }}</td>
                                    <td>{{ $expense->paid_to ?? 'N/A' }}</td>
                                    <td>
                                        {{
                                            $expense->paid_by_staff && $expense->paid_by_staff->branches->count()
                                                ? $expense->paid_by_staff->branches->first()->name
                                                : 'N/A'
                                        }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="total-summary text-center">
                        <i class="bi bi-calculator me-2"></i>Total Expenses: Rwf{{ number_format($expenseTotal, 2) }}
                    </div>
                </div>
            </div>
        @elseif(request('report_type') == 'student_transactions')
            <!-- Student Transactions Report -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-credit-card-2-front me-2"></i>Student Transactions Report</h5>
                </div>
                <div class="table-actions">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" id="searchTransactions" class="form-control" placeholder="Search transactions...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table professional-table mb-0" id="transactions-table">
                        <thead>
                            <tr>
                                <th><i class="bi bi-calendar3 me-1"></i>Date</th>
                                <th><i class="bi bi-person-fill me-1"></i>Student</th>
                                <th><i class="bi bi-tag-fill me-1"></i>Type</th>
                                <th><i class="bi bi-currency-dollar me-1"></i>Amount</th>
                                <th><i class="bi bi-check-circle me-1"></i>Status</th>
                                <th><i class="bi bi-hash me-1"></i>Reference ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $transaction->student->full_name ?? '' }}</td>
                                    <td><span class="badge bg-primary">{{ ucfirst($transaction->type) }}</span></td>
                                    <td class="fw-bold text-end">{{ number_format($transaction->amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $transaction->status == 'completed' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                    <td><code>{{ $transaction->tx_ref ?? $transaction->reference_id ?? 'N/A' }}</code></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="total-summary text-center">
                        <i class="bi bi-calculator me-2"></i>Total Payments: Rwf{{ number_format($paymentTotal, 2) }}
                    </div>
                </div>
            </div>
        @elseif(request('report_type') == 'teacher_dailies')
            <!-- Teacher Dailies Report -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Teacher Dailies Report</h5>
                </div>
                <div class="table-actions">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" id="searchDailies" class="form-control" placeholder="Search teacher dailies...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table professional-table mb-0" id="dailies-table">
                        <thead>
                            <tr>
                                <th><i class="bi bi-calendar3 me-1"></i>Date</th>
                                <th><i class="bi bi-person-fill me-1"></i>Teacher</th>
                                <th><i class="bi bi-book me-1"></i>Course</th>
                                <th><i class="bi bi-clock me-1"></i>Hours</th>
                                <th><i class="bi bi-check-circle me-1"></i>Status</th>
                                <th><i class="bi bi-people-fill me-1"></i>Present</th>
                                <th><i class="bi bi-person-x me-1"></i>Absent</th>
                                <th><i class="bi bi-list-task me-1"></i>Activities</th>
                                <th><i class="bi bi-file-earmark-text me-1"></i>Test</th>
                                <th><i class="bi bi-chat-text me-1"></i>Comment</th>
                                <th><i class="bi bi-exclamation-triangle me-1"></i>Challenges</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailies as $daily)
                                @php
                                    $attendedCount = \App\Models\Attendance::where('lesson_id', $daily->lesson_id)
                                        ->where('status', 'P')
                                        ->count();

                                    $absentCount = \App\Models\Attendance::where('lesson_id', $daily->lesson_id)
                                        ->where('status', 'A')
                                        ->count();

                                    $activities = is_array($daily->activity) 
                                        ? $daily->activity
                                        : (is_string($daily->activity) && str_starts_with($daily->activity, '[') 
                                            ? json_decode($daily->activity, true) 
                                            : [$daily->activity]);
                                @endphp
                                <tr>
                                    <td>{{ $daily->date }}</td>
                                    <td>{{ $daily->teacher->teacher_name ?? '' }}</td>
                                    <td><span class="badge bg-info">{{ $daily->course->course_name ?? '' }}</span></td>
                                    <td class="text-center fw-bold">{{ $daily->hours_studied }}h</td>
                                    <td>Teacher Daily Entry</td>
                                    <td class="text-center"><span class="badge bg-success">{{ $attendedCount }}</span></td>
                                    <td class="text-center"><span class="badge bg-danger">{{ $absentCount }}</span></td>
                                    <td>
                                        @if(is_array($activities))
                                            <ul class="mb-0 ps-3 small">
                                                @foreach($activities as $act)
                                                    @if($act)
                                                        <li>{{ $act }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @else
                                            {{ $activities }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(isset($daily->test_done))
                                            <span class="badge {{ $daily->test_done ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $daily->test_done ? 'Yes' : 'No' }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">No Quiz</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ Str::limit($daily->comment ?? 'N/A', 50) }}</td>
                                    <td class="small">{{ Str::limit($daily->challenges ?? 'N/A', 50) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="total-summary text-center">
                                <i class="bi bi-calendar-check me-2"></i>Total Dailies: {{ $totalDailies }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="total-summary text-center" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                                <i class="bi bi-clock-history me-2"></i>Recent: {{ $recentDailies->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live search functionality for tables
    function setupSearch(inputId, tableId) {
        const searchInput = document.getElementById(inputId);
        const table = document.getElementById(tableId);
        if (!searchInput || !table) return;

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            let visibleRows = 0;
            let cellsLength = rows.length > 0 ? rows[0].getElementsByTagName('td').length : 1;

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(searchTerm)) {
                        found = true;
                        break;
                    }
                }

                if (found) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            }

            // Show/hide no results message
            let noResultsRow = table.querySelector('.no-results-row');
            if (visibleRows === 0 && searchTerm.length > 0) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    noResultsRow.innerHTML = `
                        <td colspan="${cellsLength}" class="no-results">
                            <i class="bi bi-search me-2"></i>No results found for "${searchTerm}"
                        </td>
                    `;
                    table.getElementsByTagName('tbody')[0].appendChild(noResultsRow);
                }
            } else if (noResultsRow) {
                noResultsRow.remove();
            }
        });
    }

    setupSearch('searchExpenses', 'expenses-table');
    setupSearch('searchTransactions', 'transactions-table');
    setupSearch('searchDailies', 'dailies-table');
});
</script>
@endpush