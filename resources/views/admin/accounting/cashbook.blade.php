@extends(request()->routeIs('staff.accounting.*') ? 'staff.partials.sidebar_sec' : 'admin.layout')

@section('title', 'Cash Book Report')

@section('content')
<div class="container-fluid py-4 accounting-report accounting-cashbook-page">
    <div class="row">
        <div class="col-12">
            <div class="card report-shell">
                <div class="card-header report-header">
                    <div>
                        <p class="report-eyebrow mb-1">Finance / Reports</p>
                        <h1 class="report-title mb-1"><i class="fas fa-book-open me-2" aria-hidden="true"></i>Cash Book</h1>
                        <p class="report-subtitle mb-0">Review receipts, payments, and the resulting cash position.</p>
                    </div>
                    <div class="card-tools report-actions">
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                        <button onclick="downloadPDF()" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-file-pdf me-1"></i> Download PDF
                        </button>
                        <a href="{{ route(request()->routeIs('staff.accounting.*') ? 'staff.accounting.export.cashbook' : 'admin.accounting.export.cashbook', request()->query()) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-download me-1"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <div class="filter-panel mb-4 filter-form">
                        <div class="col-12">
                            <form method="GET" action="{{ route(request()->routeIs('staff.accounting.*') ? 'staff.accounting.cashbook' : 'admin.accounting.cashbook') }}" class="form-inline">
                                <div class="row g-3 align-items-end">
                                    <div class="col-12 col-lg-5">
                                        <label for="term_id" class="form-label">Academic Term</label>
                                        <select id="term_id" name="term_id" class="form-control">
                                            <option value="">All Terms</option>
                                            @foreach($terms as $term)
                                                <option value="{{ $term->id }}" {{ $termId == $term->id ? 'selected' : '' }}>
                                                    {{ $term->name }} - {{ $term->academicYear->year ?? '' }}
                                                    ({{ $term->start_date->format('M Y') }} - {{ $term->end_date->format('M Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-2">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-2">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="col-12 col-lg-3 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-grow-1">
                                            <i class="fas fa-filter me-1"></i> Apply filters
                                        </button>
                                        <a href="{{ route(request()->routeIs('staff.accounting.*') ? 'staff.accounting.cashbook' : 'admin.accounting.cashbook') }}" class="btn btn-outline-secondary">
                                            Clear
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <div class="card cashbook-summary income border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="summary-icon"><i class="fas fa-arrow-trend-up" aria-hidden="true"></i></span>
                                        <div class="flex-grow-1">
                                            <h2 class="summary-label mb-1">Total Income</h2>
                                            <div class="summary-value">{{ number_format($data['total_receipts'], 2) }} <small>RWF</small></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card cashbook-summary expense border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="summary-icon"><i class="fas fa-arrow-trend-down" aria-hidden="true"></i></span>
                                        <div class="flex-grow-1">
                                            <h2 class="summary-label mb-1">Total Expenses</h2>
                                            <div class="summary-value">{{ number_format($data['total_payments'], 2) }} <small>RWF</small></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="card cashbook-summary {{ $data['net_cash'] >= 0 ? 'balance-positive' : 'balance-negative' }} border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="summary-icon"><i class="fas fa-scale-balanced" aria-hidden="true"></i></span>
                                        <div class="flex-grow-1">
                                            <h2 class="summary-label mb-1">Net Cash</h2>
                                            <div class="summary-value">{{ number_format($data['net_cash'], 2) }} <small>RWF</small></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Book - Double Column Format -->
                    <div class="table-responsive">
                        <table class="table table-hover cashbook-table">
                            <thead>
                                <tr>
                                    <th colspan="6" class="text-center cashbook-period">
                                        <h5 class="mb-0">Cash Book</h5>
                                        <small>
                                            @if($termId && isset($terms))
                                                @php
                                                    $selectedTerm = $terms->where('id', $termId)->first();
                                                @endphp
                                                {{ $selectedTerm ? $selectedTerm->name . ' - ' . ($selectedTerm->academicYear->year ?? '') : 'Selected Term' }}
                                                @if($startDate && $endDate) 
                                                    ({{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }})
                                                @elseif($startDate) 
                                                    (From {{ $startDate->format('M d, Y') }})
                                                @elseif($endDate) 
                                                    (To {{ $endDate->format('M d, Y') }})
                                                @endif
                                            @else
                                                @if($startDate && $endDate) 
                                                    From {{ $startDate->format('M d, Y') }} To {{ $endDate->format('M d, Y') }}
                                                @elseif($startDate) 
                                                    From {{ $startDate->format('M d, Y') }}
                                                @elseif($endDate) 
                                                    To {{ $endDate->format('M d, Y') }}
                                                @else 
                                                    All Transactions
                                                @endif
                                            @endif
                                        </small>
                                    </th>
                                </tr>
                                <tr>
                                    <!-- Debit Side -->
                                    <th class="text-center" width="10%">Date</th>
                                    <th class="text-center" width="30%">Particulars</th>
                                    <th class="text-center" width="10%">Cash (FRW)</th>
                                    <!-- Credit Side -->
                                    <th class="text-center" width="10%">Date</th>
                                    <th class="text-center" width="30%">Particulars</th>
                                    <th class="text-center" width="10%">Cash (FRW)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $allTransactions = $data['transactions'];
                                $debitTransactions = [];
                                $creditTransactions = [];
                                
                                foreach ($allTransactions as $transaction) {
                                    if ($transaction->direction == '+') {
                                        $debitTransactions[] = $transaction;
                                    } else {
                                        $creditTransactions[] = $transaction;
                                    }
                                }
                                
                                $maxRows = max(count($debitTransactions), count($creditTransactions));
                                $debitIndex = 0;
                                $creditIndex = 0;
                                ?>
                                @if($maxRows === 0)
                                    <tr><td colspan="6" class="text-center text-muted py-5"><i class="fas fa-receipt fa-2x d-block mb-2" aria-hidden="true"></i>No transactions found for this period.</td></tr>
                                @endif
                                
                                @for($i = 0; $i < $maxRows; $i++)
                                    <tr>
                                        <!-- Debit Side -->
                                        @if(isset($debitTransactions[$i]))
                                            <?php $transaction = $debitTransactions[$i]; ?>
                                            <td>{{ \Carbon\Carbon::parse($transaction->entry_date)->format('M d') }}</td>
                                            <td>
                                                <span class="font-weight-bold">{{ $transaction->description }}</span>
                                                @if($transaction->term_name)
                                                    <br><small class="badge bg-info text-white">{{ $transaction->term_name }}</small>
                                                @endif
                                                <br><small class="text-muted">{{ $transaction->transaction_type }}</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="text-success font-weight-bold">
                                                    {{ number_format($transaction->amount, 2) }}
                                                </span>
                                            </td>
                                            <?php $debitIndex++; ?>
                                        @else
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @endif
                                        
                                        <!-- Credit Side -->
                                        @if(isset($creditTransactions[$i]))
                                            <?php $transaction = $creditTransactions[$i]; ?>
                                            <td>{{ \Carbon\Carbon::parse($transaction->entry_date)->format('M d') }}</td>
                                            <td>
                                                <span class="font-weight-bold">{{ $transaction->description }}</span>
                                                @if($transaction->term_name)
                                                    <br><small class="badge bg-info text-white">{{ $transaction->term_name }}</small>
                                                @endif
                                                <br><small class="text-muted">{{ $transaction->transaction_type }}</small>
                                            </td>
                                            <td class="text-right">
                                                <span class="text-danger font-weight-bold">
                                                    {{ number_format($transaction->amount, 2) }}
                                                </span>
                                            </td>
                                            <?php $creditIndex++; ?>
                                        @else
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @endif
                                    </tr>
                                @endfor
                                
                                <!-- Totals Row -->
                                @if($data['transactions']->count() > 0)
                                    <tr class="font-weight-bold bg-light">
                                        <td colspan="2" class="text-right">Total Debits:</td>
                                        <td class="text-right text-success">{{ number_format($data['total_receipts'], 2) }}</td>
                                        <td colspan="2" class="text-right">Total Credits:</td>
                                        <td class="text-right text-danger">{{ number_format($data['total_payments'], 2) }}</td>
                                    </tr>
                                    
                                    <!-- Balance Row -->
                                    <tr class="font-weight-bold bg-success text-white">
                                        <td colspan="2" class="text-right">Balance c/d:</td>
                                        <td class="text-right">{{ number_format($data['net_cash'], 2) }}</td>
                                        <td colspan="2" class="text-right">Balance b/d:</td>
                                        <td class="text-right">{{ number_format($data['net_cash'], 2) }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Transaction Count -->
                    @if($data['transactions']->count() > 0)
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>{{ $data['transactions']->count() }}</strong> transactions found 
                                    @if($startDate || $endDate)
                                        for the selected period
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .accounting-cashbook-page {
        --report-teal: #0f766e;
        --report-ink: #173247;
        --report-muted: #64748b;
        --report-line: #e2e8f0;
        font-family: 'Poppins', Arial, sans-serif;
        color: var(--report-ink);
    }
    .accounting-cashbook-page .report-shell { overflow: hidden; border: 1px solid var(--report-line); border-radius: 16px; box-shadow: 0 8px 26px rgba(15, 23, 42, .06); }
    .accounting-cashbook-page .report-header { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 18px; padding: 22px 24px; background: #fff; border-bottom: 1px solid var(--report-line); }
    .accounting-cashbook-page .report-eyebrow { color: var(--report-teal); font-size: .72rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
    .accounting-cashbook-page .report-title { color: var(--report-ink); font-size: clamp(1.35rem, 3vw, 1.8rem); font-weight: 700; }
    .accounting-cashbook-page .report-title i { color: var(--report-teal); }
    .accounting-cashbook-page .report-subtitle { color: var(--report-muted); font-size: .9rem; }
    .accounting-cashbook-page .report-actions { display: flex; flex-wrap: wrap; gap: 8px; }
    .accounting-cashbook-page .report-actions .btn { margin: 0; border-radius: 8px; }
    .accounting-cashbook-page .btn-primary { background: var(--report-teal); border-color: var(--report-teal); }
    .accounting-cashbook-page .btn-primary:hover { background: #115e59; border-color: #115e59; }
    .accounting-cashbook-page .btn-outline-primary { color: var(--report-teal); border-color: var(--report-teal); }
    .accounting-cashbook-page .btn-outline-primary:hover { color: #fff; background: var(--report-teal); }
    .accounting-cashbook-page .filter-panel { padding: 18px; border: 1px solid var(--report-line); border-radius: 12px; background: #f8fafc; }
    .accounting-cashbook-page .form-label { color: #475569; font-size: .82rem; font-weight: 600; }
    .accounting-cashbook-page .form-control:focus { border-color: #5aa69f; box-shadow: 0 0 0 .2rem rgba(15, 118, 110, .14); }
    .accounting-cashbook-page .cashbook-summary { height: 100%; border-radius: 12px; box-shadow: none; }
    .accounting-cashbook-page .cashbook-summary .card-body { padding: 18px; }
    .accounting-cashbook-page .cashbook-summary .summary-icon { display: grid; place-items: center; width: 44px; height: 44px; flex: 0 0 44px; border-radius: 12px; font-size: 1.1rem; }
    .accounting-cashbook-page .cashbook-summary .summary-label { color: var(--report-muted); font-size: .78rem; font-weight: 600; }
    .accounting-cashbook-page .cashbook-summary .summary-value { color: var(--report-ink); font-size: clamp(1.05rem, 2vw, 1.4rem); font-weight: 700; font-variant-numeric: tabular-nums; overflow-wrap: anywhere; }
    .accounting-cashbook-page .cashbook-summary .summary-value small { color: var(--report-muted); font-size: .7rem; font-weight: 600; }
    .accounting-cashbook-page .cashbook-summary.income { border: 1px solid #cce8df; background: #f3fbf7; }
    .accounting-cashbook-page .income .summary-icon { color: #14785d; background: #dff3ea; }
    .accounting-cashbook-page .cashbook-summary.expense { border: 1px solid #f1d8d6; background: #fff7f6; }
    .accounting-cashbook-page .expense .summary-icon { color: #b54742; background: #fde8e6; }
    .accounting-cashbook-page .cashbook-summary.balance-positive { border: 1px solid #cfe0ee; background: #f3f8fc; }
    .accounting-cashbook-page .balance-positive .summary-icon { color: #27669a; background: #e1eef8; }
    .accounting-cashbook-page .cashbook-summary.balance-negative { border: 1px solid #f0dfc0; background: #fffaf0; }
    .accounting-cashbook-page .balance-negative .summary-icon { color: #9a6a17; background: #fff0cf; }
    .accounting-cashbook-page .cashbook-table { min-width: 760px; border-color: var(--report-line); }
    .accounting-cashbook-page .cashbook-table th { padding: 12px 10px; color: #475569; background: #f1f5f9; font-size: .72rem; letter-spacing: .04em; text-transform: uppercase; }
    .accounting-cashbook-page .cashbook-table td { padding: 11px 10px; border-color: var(--report-line); vertical-align: middle; }
    .accounting-cashbook-page .cashbook-table .cashbook-period { color: #fff; background: #124e55; text-transform: none; letter-spacing: 0; }
    .accounting-cashbook-page .cashbook-table .cashbook-period small { color: #d7edeb; }
    .accounting-cashbook-page .cashbook-table .bg-success { background: #14785d !important; }
    .accounting-cashbook-page .print-header, .accounting-cashbook-page .print-footer { display: none; }
    @media (max-width: 575.98px) {
        .accounting-cashbook-page .card-body { padding: 14px; }
        .accounting-cashbook-page .report-header { padding: 18px; }
        .accounting-cashbook-page .report-actions { width: 100%; }
        .accounting-cashbook-page .report-actions .btn { flex: 1 1 auto; }
        .accounting-cashbook-page .filter-panel { padding: 12px; }
    }
</style>

<!-- Print Styles -->
<style>
@media print {
    /* Hide screen-only elements */
    .card-tools, .card-header, .btn, .no-print, .filter-form {
        display: none !important;
    }
    
    /* Print-specific styles */
    .card-body {
        padding: 0;
        box-shadow: none;
        border: none;
    }
    
    .table {
        margin: 0;
        border-collapse: collapse;
    }
    
    .table th, .table td {
        border: 1px solid #000 !important;
        padding: 8px;
        font-size: 12px;
    }
    
    .table th {
        background-color: #f8f9fa !important;
        font-weight: bold;
    }
    
    .font-weight-bold {
        font-weight: bold !important;
    }
    
    .text-success {
        color: #000 !important;
    }
    
    .text-danger {
        color: #000 !important;
    }
    
    /* Print header */
    .print-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }
    
    .print-logo {
        width: 80px;
        height: 80px;
        margin-bottom: 10px;
    }
    
    .print-school-name {
        font-size: 18px;
        font-weight: bold;
        margin: 5px 0;
    }
    
    .print-contact {
        font-size: 12px;
        margin: 5px 0;
    }
    
    /* Print footer */
    .print-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 10px;
        border-top: 1px solid #000;
        padding-top: 5px;
        background: white;
    }
    
    /* Page break */
    .page-break {
        page-break-after: always;
    }
}
</style>

<!-- Print Header (Hidden on screen) -->
<div class="print-header no-print" style="display: none;">
    @if($school && $school->logo_url)
        <img src="{{ $school->logo_url }}" alt="School Logo" class="print-logo">
    @endif
    <div class="print-school-name">{{ $school ? $school->school_name : 'ESA Education System' }}</div>
    <div class="print-contact">
        @if($school)
            {{ $school->address }} | {{ $school->phone }} | {{ $school->email }}
        @else
            Kigali, Rwanda | +250 788 123 456 | info@esa.rw
        @endif
    </div>
    <h4>Cash Book Report</h4>
    @if($startDate && $endDate)
        <div>From {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</div>
    @elseif($startDate)
        <div>From {{ $startDate->format('M d, Y') }}</div>
    @elseif($endDate)
        <div>To {{ $endDate->format('M d, Y') }}</div>
    @endif
</div>

<!-- Print Footer (Hidden on screen) -->
<div class="print-footer no-print" style="display: none;">
    Generated on {{ now()->format('M d, Y h:i A') }} by {{ auth()->guard('admin')->check() ? auth()->guard('admin')->user()->first_name . ' ' . auth()->guard('admin')->user()->last_name : auth()->guard('staff')->user()->name }} | {{ $school ? $school->school_name : 'ESA Education System' }}
</div>

<!-- JavaScript for PDF Download -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function downloadPDF() {
    const { jsPDF } = window.jspdf;
    
    // Create a new PDF document
    const pdf = new jsPDF('p', 'mm', 'a4');
    
    // Page dimensions
    const pageWidth = pdf.internal.pageSize.getWidth();
    const pageHeight = pdf.internal.pageSize.getHeight();
    
    // School data from Blade
    const schoolName = '{{ $school ? $school->school_name : "ESA Education System" }}';
    const schoolAddress = '{{ $school ? $school->address : "Kigali, Rwanda" }}';
    const schoolPhone = '{{ $school ? $school->phone : "+250 788 123 456" }}';
    const schoolEmail = '{{ $school ? $school->email : "info@esa.rw" }}';
    const schoolLogo = '{{ $school && $school->logo_url ? $school->logo_url : "" }}';
    
    // Add custom font for better text rendering
    pdf.setFont('helvetica');
    
    // Add header
    let yPosition = 20;
    
    // Add logo if available
    if (schoolLogo) {
        try {
            pdf.addImage(schoolLogo, 'JPEG', pageWidth/2 - 20, yPosition, 40, 40);
            yPosition += 45;
        } catch(e) {
            // If logo fails to load, continue without it
            console.log('Logo could not be loaded');
        }
    }
    
    // School name
    pdf.setFontSize(16);
    pdf.text(schoolName, pageWidth / 2, yPosition, { align: 'center' });
    yPosition += 8;
    
    // Contact info
    pdf.setFontSize(10);
    const contactText = schoolAddress + ' | ' + schoolPhone + ' | ' + schoolEmail;
    pdf.text(contactText, pageWidth / 2, yPosition, { align: 'center' });
    yPosition += 8;
    
    // Report title
    pdf.setFontSize(14);
    pdf.text('Cash Book Report', pageWidth / 2, yPosition, { align: 'center' });
    yPosition += 10;
    
    // Date range
    pdf.setFontSize(10);
    @if($startDate && $endDate)
        pdf.text('From {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}', pageWidth / 2, yPosition, { align: 'center' });
    @elseif($startDate)
        pdf.text('From {{ $startDate->format('M d, Y') }}', pageWidth / 2, yPosition, { align: 'center' });
    @elseif($endDate)
        pdf.text('To {{ $endDate->format('M d, Y') }}', pageWidth / 2, yPosition, { align: 'center' });
    @else
        pdf.text('All Transactions', pageWidth / 2, yPosition, { align: 'center' });
    @endif
    yPosition += 15;
    
    // Add table headers
    pdf.setFontSize(10);
    const headers = ['Date', 'Particulars', 'Cash (FRW)', 'Date', 'Particulars', 'Cash (FRW)'];
    const columnWidths = [25, 55, 25, 25, 55, 25];
    let xPos = 15;
    
    // Draw header background
    pdf.setFillColor(240, 240, 240);
    pdf.rect(15, yPosition - 5, pageWidth - 30, 8, 'F');
    
    // Draw header borders and text
    pdf.setDrawColor(0, 0, 0);
    pdf.setTextColor(0, 0, 0);
    
    for (let i = 0; i < headers.length; i++) {
        pdf.text(headers[i], xPos, yPosition);
        xPos += columnWidths[i];
    }
    
    yPosition += 3;
    
    // Draw horizontal line under headers
    pdf.line(15, yPosition, pageWidth - 15, yPosition);
    yPosition += 5;
    
    // Add transaction data
    pdf.setFontSize(8);
    
    @php
        $allTransactions = $data['transactions'];
        $debitTransactions = [];
        $creditTransactions = [];
        
        foreach ($allTransactions as $transaction) {
            if ($transaction->direction == '+') {
                $debitTransactions[] = $transaction;
            } else {
                $creditTransactions[] = $transaction;
            }
        }
        
        $maxRows = max(count($debitTransactions), count($creditTransactions));
    @endphp
    
    @for($i = 0; $i < $maxRows; $i++)
        @if(isset($debitTransactions[$i]))
            <?php $transaction = $debitTransactions[$i]; ?>
            // Debit side
            xPos = 15;
            pdf.text('{{ \Carbon\Carbon::parse($transaction->entry_date)->format('M d') }}', xPos, yPosition);
            xPos += columnWidths[0];
            
            // Wrap text for particulars
            const particular{{ $i }} = '{{ $transaction->description }}';
            const lines{{ $i }} = pdf.splitTextToSize(particular{{ $i }}, columnWidths[1] - 2);
            pdf.text(lines{{ $i }}, xPos, yPosition - 2);
            xPos += columnWidths[1];
            
            pdf.text('{{ number_format($transaction->amount, 2) }}', xPos, yPosition);
        @else
            // Empty debit row
            xPos = 15;
            for (let j = 0; j < 3; j++) {
                xPos += columnWidths[j];
            }
        @endif
        
        @if(isset($creditTransactions[$i]))
            <?php $transaction = $creditTransactions[$i]; ?>
            // Credit side
            xPos += columnWidths[3];
            pdf.text('{{ \Carbon\Carbon::parse($transaction->entry_date)->format('M d') }}', xPos, yPosition);
            xPos += columnWidths[3];
            
            // Wrap text for particulars
            const particularCredit{{ $i }} = '{{ $transaction->description }}';
            const linesCredit{{ $i }} = pdf.splitTextToSize(particularCredit{{ $i }}, columnWidths[4] - 2);
            pdf.text(linesCredit{{ $i }}, xPos, yPosition - 2);
            xPos += columnWidths[4];
            
            pdf.text('{{ number_format($transaction->amount, 2) }}', xPos, yPosition);
        @endif
        
        yPosition += 12;
        
        // Check if we need a new page
        if (yPosition > pageHeight - 40) {
            pdf.addPage();
            yPosition = 20;
            
            // Add headers to new page
            xPos = 15;
            pdf.setFillColor(240, 240, 240);
            pdf.rect(15, yPosition - 5, pageWidth - 30, 8, 'F');
            
            for (let i = 0; i < headers.length; i++) {
                pdf.text(headers[i], xPos, yPosition);
                xPos += columnWidths[i];
            }
            
            yPosition += 3;
            pdf.line(15, yPosition, pageWidth - 15, yPosition);
            yPosition += 5;
        }
        
        // Draw horizontal line after each row
        pdf.setDrawColor(200, 200, 200);
        pdf.line(15, yPosition - 2, pageWidth - 15, yPosition - 2);
        pdf.setDrawColor(0, 0, 0);
    @endfor
    
    // Add totals if there are transactions
    @if($data['transactions']->count() > 0)
        yPosition += 10;
        pdf.setFontSize(10);
        pdf.setFont('helvetica', 'bold');
        
        // Total Debits
        xPos = 15;
        pdf.text('Total Debits:', xPos + columnWidths[0] + columnWidths[1] - 20, yPosition);
        pdf.text('{{ number_format($data['total_receipts'], 2) }}', xPos + columnWidths[0] + columnWidths[1] + columnWidths[2] - 15, yPosition);
        
        // Total Credits
        xPos += columnWidths[3];
        pdf.text('Total Credits:', xPos + columnWidths[3] + columnWidths[4] - 20, yPosition);
        pdf.text('{{ number_format($data['total_payments'], 2) }}', xPos + columnWidths[3] + columnWidths[4] + columnWidths[5] - 15, yPosition);
        
        yPosition += 10;
        
        // Balance
        pdf.text('Balance c/d:', xPos + columnWidths[0] + columnWidths[1] - 20, yPosition);
        pdf.text('{{ number_format($data['net_cash'], 2) }}', xPos + columnWidths[0] + columnWidths[1] + columnWidths[2] - 15, yPosition);
        
        xPos += columnWidths[3];
        pdf.text('Balance b/d:', xPos + columnWidths[3] + columnWidths[4] - 20, yPosition);
        pdf.text('{{ number_format($data['net_cash'], 2) }}', xPos + columnWidths[3] + columnWidths[4] + columnWidths[5] - 15, yPosition);
    @endif
    
    // Add footer
    pdf.setFontSize(8);
    pdf.setFont('helvetica', 'normal');
    @php
        $userName = auth()->guard('admin')->check()
            ? auth()->guard('admin')->user()->first_name . ' ' . auth()->guard('admin')->user()->last_name
            : auth()->guard('staff')->user()->name;
    @endphp
    const footerText = 'Generated on {{ now()->format('M d, Y h:i A') }} by {{ $userName }} | ' + schoolName;
    pdf.text(footerText, pageWidth / 2, pageHeight - 10, { align: 'center' });
    
    // Save the PDF
    const fileName = 'cashbook_' + new Date().toISOString().split('T')[0] + '.pdf';
    pdf.save(fileName);
}

// Show print elements when printing
window.addEventListener('beforeprint', function() {
    document.querySelectorAll('.print-header, .print-footer').forEach(el => {
        el.style.display = 'block';
    });
});

// Hide print elements after printing
window.addEventListener('afterprint', function() {
    document.querySelectorAll('.print-header, .print-footer').forEach(el => {
        el.style.display = 'none';
    });
});
</script>

@endsection
