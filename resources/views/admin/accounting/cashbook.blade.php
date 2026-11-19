@extends(request()->routeIs('staff.accounting.*') ? 'staff.partials.sidebar_sec' : 'admin.layout')

@section('title', 'Cash Book Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-book mr-2"></i>Cash Book Report
                    </h3>
                    <div class="card-tools">
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="fas fa-print mr-1"></i> Print
                        </button>
                        <button onclick="downloadPDF()" class="btn btn-info btn-sm ml-2">
                            <i class="fas fa-file-pdf mr-1"></i> Download PDF
                        </button>
                        <a href="{{ route(request()->routeIs('staff.accounting.*') ? 'staff.accounting.export.cashbook' : 'admin.accounting.export.cashbook', request()->query()) }}" class="btn btn-success btn-sm ml-2">
                            <i class="fas fa-download mr-1"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <div class="row mb-4 filter-form">
                        <div class="col-12">
                            <form method="GET" action="{{ route(request()->routeIs('staff.accounting.*') ? 'staff.accounting.cashbook' : 'admin.accounting.cashbook') }}" class="form-inline">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-2">
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
                                    <div class="col-md-2">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter mr-1"></i> Filter
                                        </button>
                                        <a href="{{ route(request()->routeIs('staff.accounting.*') ? 'staff.accounting.cashbook' : 'admin.accounting.cashbook') }}" class="btn btn-secondary ml-2">
                                            <i class="fas fa-times mr-1"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title text-white mb-0">Total Income</h6>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-white mb-0">{{ number_format($data['total_receipts'], 2) }} RWF</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title text-white mb-0">Total Expenses</h6>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-white mb-0">{{ number_format($data['total_payments'], 2) }} RWF</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ $data['net_cash'] >= 0 ? 'bg-primary' : 'bg-warning' }} border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title text-white mb-0">Net Cash</h6>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-white mb-0">{{ number_format($data['net_cash'], 2) }} RWF</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Book - Double Column Format -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th colspan="7" class="text-center bg-primary text-white">
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
                                    <th class="text-center bg-light" width="8%">Date</th>
                                    <th class="text-center bg-light" width="42%">Particulars</th>
                                    <th class="text-center bg-light" width="20%">Cash (FRW)</th>
                                    <!-- Credit Side -->
                                    <th class="text-center bg-light" width="8%">Date</th>
                                    <th class="text-center bg-light" width="42%">Particulars</th>
                                    <th class="text-center bg-light" width="20%">Cash (FRW)</th>
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
    const footerText = 'Generated on {{ now()->format('M d, Y h:i A') }} by {{ auth('admin')->user()->first_name }} {{ auth('admin')->user()->last_name }} | ' + schoolName;
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
