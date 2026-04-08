@php
    // Show edit/delete buttons to staff users only (you can narrow this later)
    $isAuthorizedUser = auth('staff')->check();

    $course = \App\Models\Course::find($student->course_id);
    $discount = $student->discount ?? 0;

    // Get the current/latest term from payments
    $latestPayment = $payments->sortByDesc('created_at')->first();
    $currentTerm = $latestPayment ? $latestPayment->term : null;
    $academicYear = $currentTerm ? $currentTerm->academicYear : null;

    $registrationFee = $course ? floatval($course->registration_fee) : 0;
    $tuitionFee = $course ? floatval($course->tuition_fee) : 0;
    $practicalFee = $course ? floatval($course->practical_fee) : 0;
    $tripFee = $course ? floatval($course->trip_fee) : 0;
    $studentCardFee = 2000;

    // Check if registration fee was paid in the same academic year
    $registrationPaidInYear = false;
    // Student card fee should be paid once per academic year (not per term)
    $studentCardPaidInYear = false;
    if ($academicYear) {
        $registrationPaidInYear = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'registration')
            ->whereIn('status', ['successful', 'paid'])
            ->whereHas('term', function ($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id);
            })
            ->exists();

        $studentCardPaidInYear = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', 'student_card')
            ->whereIn('status', ['successful', 'paid'])
            ->whereHas('term', function ($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id);
            })
            ->exists();
    }

    $discountableFees = $tuitionFee + $practicalFee + $tripFee;
    $discountAmount = ($discount > 0) ? ($discountableFees * ($discount / 100)) : 0;

    // Calculate PER TERM: Use only payments for the current term
    if ($currentTerm) {
        $termPayments = $payments->filter(function($p) use ($currentTerm) {
            return $p->term_id == $currentTerm->id;
        });
        $totalPaid = $termPayments->sum('amount');
    } else {
        $totalPaid = $payments->sum('amount');
    }

    // Total fees for the term - EXCLUDE registration fee if already paid in this academic year
    $studentCardFeeToCharge = $studentCardPaidInYear ? 0 : $studentCardFee;
    if ($registrationPaidInYear) {
        // Don't include registration fee if already paid in this year
        $totalToPay = $studentCardFeeToCharge + ($discountableFees - $discountAmount);
    } else {
        // Include all fees if registration not paid yet
        $totalToPay = $studentCardFeeToCharge + $registrationFee + ($discountableFees - $discountAmount);
    }
    
    // Outstanding for the current term only
    $outstanding = max(0, $totalToPay - $totalPaid);
    
    // Get school information
    try {
        $schoolInfo = \App\Models\SchoolInformation::getActive();
    } catch (Exception $e) {
        // Fallback in case there are any issues with the model
        $schoolInfo = null;
    }
    $systemName = \Illuminate\Support\Facades\Cache::get('esa_system_name', 'ESA Learning Portal');
    $systemLogo = \App\Helpers\LogoHelper::getLogoUrl();
@endphp

<style>
/* Print-specific styles for professional transcript */
@media print {
    * {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    body {
        background: white !important;
        color: black !important;
        font-family: 'Arial', sans-serif !important;
        font-size: 8pt !important;
        line-height: 0.95 !important;
        margin: 0 !important;
        padding: 3mm !important;
        width: 100%;
        height: 100%;
    }
    
    html {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
    }
    
    /* Hide modal and backdrop */
    .modal, .modal-backdrop, .modal-dialog, .modal-content {
        display: none !important;
    }
    
    /* Hide all overlays and backdrops */
    [role="presentation"], .fade.in, .show {
        display: none !important;
    }
    
    /* Hide all navigation and header elements */
    nav, .navbar, .topbar, .sidebar, header, .header, 
    .d-print-none, #topbar, #navbar, #sidebar,
    .navbar-horizontal, .navbar-vertical, 
    .app-header, .app-sidebar, .page-header {
        display: none !important;
    }
    
    /* Hide links on print */
    a {
        color: black !important;
        text-decoration: none !important;
        pointer-events: none;
    }
    
    a[href]:after {
        content: "" !important;
    }
    
    /* Hide main layout wrappers that might contain sidebars */
    .page-wrapper, .wrapper {
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    
    /* Prevent any page breaks or overflow */
    body, html {
        page-break-after: avoid !important;
        page-break-before: avoid !important;
        orphans: 0 !important;
        widows: 0 !important;
    }
    
    /* The Actions column header and data */
    .transcript-container table th:nth-child(8),
    .transcript-container table td:nth-child(8) {
        display: none !important;
    }
    
    /* Hide the Edit Mode badge and all interactive elements */
    .transcript-container .badge.bg-warning,
    .transcript-container .btn,
    .transcript-container .modal,
    .transcript-container .d-print-none {
        display: none !important;
    }
    
    /* Hide fee summary section */
    .fee-summary-section {
        display: none !important;
    }
    
    /* Hide all action buttons in payment rows */
    .transcript-container .btn-sm,
    .transcript-container .btn-danger,
    .transcript-container .btn-warning {
        display: none !important;
    }
    
    /* Hide the regular title and show print title */
    .transcript-container h3:not(.d-print-block h3) {
        display: none !important;
    }
    
    .transcript-container .d-print-block {
        display: block !important;
    }
    
    /* Professional header styling for print */
    .transcript-header {
        text-align: center !important;
        margin-bottom: 1px !important;
        padding-bottom: 1px !important;
        border-bottom: 0.5px solid #000 !important;
    }
    
    .transcript-header h2 {
        font-size: 9.5pt !important;
        font-weight: bold !important;
        margin-bottom: 0px !important;
        color: black !important;
        line-height: 0.9 !important;
    }
    
    .transcript-header h3 {
        font-size: 7pt !important;
        font-weight: bold !important;
        margin: 0 !important;
        color: black !important;
        line-height: 0.9 !important;
    }

    .transcript-header p {
        font-size: 6pt !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: 0.8 !important;
    }
    
    /* Ensure proper table formatting for print */
    .transcript-container table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin-bottom: 2px !important;
        font-size: 7pt !important;
    }
    
    .transcript-container table th,
    .transcript-container table td {
        border: 1px solid #000 !important;
        padding: 1px 1px !important;
        text-align: left !important;
        vertical-align: middle !important;
    }
    
    .transcript-container table th {
        background: #e0e0e0 !important;
        font-weight: bold !important;
        color: black !important;
        font-size: 7pt !important;
    }
    
    /* Reduce payment history table size */
    .transcript-container .table-sm td,
    .transcript-container .table-sm th {
        padding: 0px 1px !important;
        font-size: 6.5pt !important;
    }
    
    /* Hide badge styling in print for less height */
    .transcript-container .badge {
        padding: 1px 4px !important;
        font-size: 7pt !important;
    }
    
    /* Print footer flex layout */
    .transcript-footer-flex {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 6px !important;
    }
    
    .footer-section {
        flex: 1 !important;
        text-align: center !important;
    }
    
    .footer-logo, .footer-stamp {
        max-height: 40px !important;
        width: auto !important;
    }
    
    .footer-contact {
        background: white !important;
        padding: 2px 3px !important;
        border: 1px solid #000 !important;
        font-size: 6.5pt !important;
    }
    
    .footer-contact p {
        margin: 1px 0 !important;
    }
    
    /* Remove card shadows and backgrounds for clean printing */
    .transcript-container .card {
        border: 0.5px solid #000 !important;
        box-shadow: none !important;
        margin-bottom: 0 !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    
    .transcript-container .card-header {
        background: #e0e0e0 !important;
        border-bottom: 0.5px solid #000 !important;
        color: black !important;
        padding: 0.5px !important;
    }

    .transcript-container .card-header h6 {
        font-size: 6.5pt !important;
        font-weight: bold !important;
        margin: 0 !important;
        line-height: 0.9 !important;
    }
    
    .transcript-container .card-body {
        color: black !important;
        padding: 0.5px !important;
    }
    
    /* Ensure all text is black for printing */
    .transcript-container * {
        color: black !important;
    }
    
    /* Student info section styling */
    .student-info-section {
        margin-bottom: 0px !important;
        padding: 0.5px !important;
        border: 0.5px solid #000 !important;
        background: white !important;
        font-size: 6.5pt !important;
        line-height: 0.9 !important;
    }
    
    .student-info-section strong {
        font-weight: bold !important;
    }
    
    /* Footer styling */
    .transcript-footer {
        margin-top: 1px !important;
        padding-top: 0.5px !important;
        border-top: 0.5px solid #000 !important;
        text-align: center !important;
        font-size: 5.5pt !important;
        color: black !important;
        line-height: 0.8 !important;
    }
    
    .transcript-footer p {
        margin: 0 !important;
        padding: 0 !important;
        font-size: 5.5pt !important;
        line-height: 0.8 !important;
    }
    
    /* Print footer flex layout */
    .transcript-footer-flex {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 1px !important;
    }
    
    .footer-section {
        flex: 1 !important;
        text-align: center !important;
    }
    
    .footer-logo, .footer-stamp {
        max-height: 25px !important;
        width: auto !important;
    }
    
    .footer-contact {
        background: white !important;
        padding: 0 !important;
        border: none !important;
        font-size: 4.5pt !important;
    }
    
    .footer-contact p {
        margin: 0 !important;
        padding: 0 !important;
        line-height: 0.8 !important;
    }
    
    /* Page break control - prevent all page breaks */
    .page-break {
        display: none !important;
    }
    
    /* Prevent any element from causing page breaks */
    * {
        page-break-inside: avoid !important;
        page-break-before: avoid !important;
        page-break-after: avoid !important;
    }
    
    /* Reduce authority section spacing */
    .authority-section {
        margin-top: 0.5px !important;
        padding-top: 0.5px !important;
        border-top: 0.5px solid #000 !important;
        text-align: right !important;
        font-size: 5.5pt !important;
    }
    
    .authority-section h5 {
        margin-bottom: 0px !important;
        font-size: 5.5pt !important;
        line-height: 0.8 !important;
    }
    
    .authority-section p {
        margin-bottom: 0px !important;
        font-size: 5.5pt !important;
        line-height: 0.75 !important;
    }
    
    .signature-line {
        border-bottom: 0.5px solid #000 !important;
        width: 60px !important;
        margin-left: auto !important;
        margin-bottom: 0px !important;
    }
    
    .authority-section .row {
        margin: 0 !important;
    }
    
    .authority-section .col-md-6 {
        padding: 0 1px !important;
    }
    
    @page {
        size: A4;
        margin: 4mm;
        margin-bottom: 0;
    }
    
    /* Prevent page breaks and hide browser headers/footers */
    .transcript-container {
        page-break-after: avoid !important;
        break-after: avoid !important;
        max-height: 100vh !important;
        overflow: hidden !important;
    }
    
    .authority-section {
        page-break-inside: avoid !important;
    }
    
    /* Ensure single page */
    @page {
        size: A4;
        margin: 3mm;
        orphans: 0;
        widows: 0;
    }
}

/* Screen styles */
.transcript-container {
    max-width: 100%;
    margin: 0 auto;
}

.transcript-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #dee2e6;
}

.transcript-header h2 {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #1e3a8a;
}

.transcript-header h3 {
    font-size: 20px;
    font-weight: bold;
    margin: 20px 0 10px 0;
    color: #2c3e50;
}

.school-logo {
    max-height: 80px;
    margin-bottom: 15px;
}

.footer-logo, .footer-stamp {
    max-height: 60px;
    width: auto;
}

.transcript-footer-flex {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-top: 20px;
}

.footer-section {
    flex: 1;
    text-align: center;
}

.footer-contact {
    font-size: 12px;
    line-height: 1.4;
}

.student-info-section,
.fee-summary-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid #1e3a8a;
}

.transcript-footer {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #dee2e6;
    text-align: center;
    color: #6c757d;
    font-size: 14px;
}

.authority-section {
    margin-top: 30px;
    text-align: right;
}

.signature-line {
    border-bottom: 1px solid #000;
    width: 250px;
    margin-left: auto;
    margin-bottom: 5px;
}
</style>

<div class="transcript-container">
    <!-- Print Button -->
    <div class="d-print-none mb-3">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Transcript
        </button>
    </div>

    <!-- School Header -->
    <div class="transcript-header">
        @if($systemLogo)
            <img src="{{ $systemLogo }}" alt="School Logo" class="school-logo">
        @endif
        <h2>{{ $systemName }}</h2>
        @if($schoolInfo)
            <p><strong>{{ $schoolInfo->address ?? '' }}</strong></p>
            <p><strong>Phone: {{ $schoolInfo->phone ?? '' }} | Email: {{ $schoolInfo->email ?? '' }}</strong></p>
            @if($schoolInfo->website)
                <p><strong>Website: {{ $schoolInfo->website }}</strong></p>
            @endif
            @if($schoolInfo->school_name)
                <p><strong>{{ $schoolInfo->school_name }}</strong></p>
            @endif
        @else
            <p><strong>123 Education Street, Learning City, Knowledge State</strong></p>
            <p><strong>Phone: +250-788-123-456 | Email: info@esa-learning.rw</strong></p>
        @endif
    </div>

    <!-- Print Title -->
    <div class="d-print-block text-center mb-4" style="display: none;">
        <h3 style="font-size: 20pt; font-weight: bold; margin-bottom: 10px;">OFFICIAL STUDENT PAYMENT TRANSCRIPT</h3>
        <p style="font-size: 12pt; margin: 0;">This document certifies the complete payment history of the student</p>
    </div>

    <!-- Transcript Title -->
    <h3 style="text-align: center; margin-bottom: 12px; font-size: 16pt;">STUDENT PAYMENT TRANSCRIPT</h3>
    
    <!-- Student Information -->
    <div class="student-info-section" style="padding: 8px; margin-bottom: 8px;">
        <table style="width: 100%; border: none; margin: 0; padding: 0;">
            <tr>
                <td style="border: none; padding: 2px 4px;"><strong>Registration Number:</strong> {{ $student->reg_number }}</td>
                <td style="border: none; padding: 2px 4px;"><strong>Course:</strong> {{ $student->course->course_name ?? 'N/A' }}</td>
                <td style="border: none; padding: 2px 4px;"><strong>Academic Year:</strong> {{ $academicYear ? $academicYear->year : 'N/A' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 4px;"><strong>Branch:</strong> {{ $student->branch->name ?? 'N/A' }}</td>
                <td style="border: none; padding: 2px 4px;"><strong>Current Term:</strong> {{ $currentTerm ? $currentTerm->name : 'N/A' }}</td>
                <td style="border: none; padding: 2px 4px;"><strong>Outstanding:</strong> <span style="font-weight: bold; color: @if($outstanding > 0) #dc3545 @else #28a745 @endif;">{{ number_format($outstanding, 2) }} RWF</span></td>
            </tr>
        </table>
    </div>

    <!-- Payment History -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0">PAYMENT HISTORY</h6>
            @if($isAuthorizedUser)
                <span class="badge bg-warning text-dark d-print-none">
                    <i class="fas fa-key"></i> Edit Mode (staff)
                </span>
            @endif
        </div>

        <div class="card-body">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Term</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                @if($isAuthorizedUser)
                                    <th class="text-center d-print-none">Actions</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($payments as $index => $payment)
                                @php
                                    $meta = is_array($payment->meta) ? $payment->meta : (json_decode($payment->meta, true) ?: []);
                                    $method = $meta['method'] ?? 'online';

                                    $feeTypeMap = [
                                        'registration_fee' => 'Registration',
                                        'school_fee' => 'School Fee',
                                        'practical_fee' => 'Practical',
                                        'trip_fee' => 'Trip',
                                        'student_card' => 'Student Card'
                                    ];
                                    $displayType = $feeTypeMap[$payment->type] ?? ucfirst(str_replace('_', ' ', $payment->type));
                                    $displayDate = $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') : ($payment->created_at ? $payment->created_at->format('Y-m-d H:i') : 'N/A');
                                    $termName = $payment->term ? $payment->term->name : 'No Term';
                                @endphp
                                <tr id="payment-row-{{ $payment->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $displayDate }}</td>
                                    <td><span class="badge bg-info">{{ $termName }}</span></td>
                                    <td><span class="badge bg-primary">{{ $displayType }}</span></td>
                                    <td><strong>{{ number_format($payment->amount, 2) }} RWF</strong></td>
                                    <td>
                                        @if(strtolower($method) === 'cash')
                                            <span class="badge bg-success"><i class="fas fa-money-bill-wave"></i> Cash</span>
                                        @else
                                            <span class="badge bg-primary"><i class="fas fa-credit-card"></i> Online</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(in_array($payment->status, ['successful', 'paid']))
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($payment->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">{{ ucfirst($payment->status) }}</span>
                                        @endif
                                    </td>

                                    @if($isAuthorizedUser)
                                        <td class="text-center d-print-none">
                                            <button type="button"
                                                    class="btn btn-sm btn-warning me-1"
                                                    onclick="openEditModal({{ $payment->id }})"
                                                    title="Edit Transaction">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="confirmDeleteTransaction({{ $payment->id }})"
                                                    title="Delete Transaction">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-receipt fa-2x mb-2"></i>
                    <div>No payment records found for this student.</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Authority Signature Section -->
    <div class="authority-section">
        <h5 style="margin-bottom: 20px; font-weight: bold;">CERTIFICATION</h5>
        <p style="margin-bottom: 30px;">This transcript certifies that the above student payment information is accurate and complete as of the date of issue.</p>
        
        <div class="row">
            <div class="col-md-6">
                <p style="margin-bottom: 5px;"><strong>Prepared By:</strong></p>
                <div class="signature-line"></div>
                <p style="font-size: 12px; color: #666;">Accounting Department</p>
                <p style="font-size: 12px; color: #666;">{{ date('d F Y') }}</p>
            </div>
            <div class="col-md-6">
                <p style="margin-bottom: 5px;"><strong>Authorized By:</strong></p>
                <div class="signature-line"></div>
                <p style="font-size: 12px; color: #666;">School Administration</p>
                <p style="font-size: 12px; color: #666;">{{ date('d F Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Footer with Logo and Stamp -->
    <div class="transcript-footer">
        <div class="transcript-footer-flex">
            <!-- Left: School Logo -->
            <div class="footer-section">
                @if($systemLogo)
                    <img src="{{ $systemLogo }}" alt="School Logo" class="footer-logo" style="max-height: 50px;">
                @endif
            </div>

            <!-- Center: Footer Information -->
            <div class="footer-section" style="flex: 2;">
                <p><strong>Official Document - {{ $systemName }}</strong></p>
                <p style="font-size: 11px; margin: 5px 0;">This document is an official record of student payment information and should be kept for future reference.</p>
                
                <p style="margin: 8px 0; font-weight: bold; font-size: 11px;">For inquiries, contact Accounting Department:</p>
                <div class="footer-contact" style="background: #f5f5f5; padding: 5px; border-radius: 4px;">
                    <p style="margin: 2px 0;"><strong>Email:</strong> ange.sec@excellentservantacademy.com</p>
                    <p style="margin: 2px 0;"><strong>Phone:</strong> 0788525247</p>
                </div>
                
                <p style="margin-top: 8px; font-size: 10px; color: #666;">
                    Generated: {{ date('d F Y H:i:s') }} | Student ID: {{ $student->id }} | Reg: {{ $student->reg_number }}
                </p>
            </div>

            <!-- Right: School Stamp -->
            <div class="footer-section">
                @if($schoolInfo && $schoolInfo->stamp_signature_url)
                    <img src="{{ $schoolInfo->stamp_signature_url }}" alt="Official Stamp" class="footer-stamp" style="max-height: 50px;">
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Transaction Modal -->
<div class="modal fade d-print-none" id="editTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Transaction
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editTransactionModalBody">
                <div class="text-center py-4">
                    <span class="spinner-border spinner-border-lg"></span> 
                    <p class="mt-2">Loading transaction form...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function handleTransactionFormSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalButtonText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        
        console.log('Submitting form to:', form.action);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(resp => {
            console.log('Response:', resp.status, resp.statusText);
            if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
            return resp.json();
        })
        .then(json => {
            console.log('Success response:', json);
            if (json.success) {
                bootstrap.Modal.getInstance(document.getElementById('editTransactionModal')).hide();
                alert(json.message || 'Transaction updated successfully!');
                location.reload();
            } else {
                let msg = json.message || 'Failed to update';
                if (json.errors) {
                    msg += '\n' + Object.entries(json.errors).map(([k,v]) => `${k}: ${v.join(', ')}`).join('\n');
                }
                alert(msg);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error: ' + err.message);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalButtonText;
        });
    }
    
    function openEditModal(paymentId) {
        // Use correct transaction route
        const url = '/transactions/' + paymentId + '/edit';
        const modal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
        const body = document.getElementById('editTransactionModalBody');
        body.innerHTML = '<div class="text-center"><span class="spinner-border"></span> Loading...</div>';
        modal.show();

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
        .then(resp => {
            // Log the full response for debugging
            console.log('Edit form fetch response:', {
                status: resp.status,
                statusText: resp.statusText,
                ok: resp.ok,
                headers: {
                    contentType: resp.headers.get('content-type')
                }
            });
            
            if (!resp.ok) {
                throw new Error(`HTTP Error ${resp.status}: ${resp.statusText}`);
            }
            return resp.text();
        })
        .then(html => {
            console.log('Form response received, length:', html.length);
            console.log('Contains form tag:', html.includes('<form'));
            console.log('First 500 chars:', html.substring(0, 500));
            
            // Clear the loading state
            body.innerHTML = '';
            
            // Create a temporary div to parse the HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // If we got a form, insert it
            const form = tempDiv.querySelector('form');
            if (form) {
                console.log('✓ Form found in response');
                body.innerHTML = html;
                
                // Attach submit handler
                const submitForm = body.querySelector('form');
                if (submitForm) {
                    submitForm.removeEventListener('submit', handleTransactionFormSubmit);
                    submitForm.addEventListener('submit', handleTransactionFormSubmit);
                    console.log('✓ Submit handler attached');
                }
            } else {
                console.error('✗ No form found in response.');
                console.error('Response data:', html);
                const errorHTML = `
                    <div class="alert alert-danger" role="alert">
                        <strong>Error:</strong> The form did not load correctly.
                        <p class="mt-2 mb-0 small"><code>Response length: ${html.length} chars</code></p>
                        <p class="mt-2">This might indicate an authentication issue or server error. Check the browser console.</p>
                    </div>
                `;
                body.innerHTML = errorHTML;
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            body.innerHTML = `<div class="alert alert-danger">
                <strong>Error:</strong> Could not load the form.<br>
                <small>${err.message || 'Unknown error'}</small>
            </div>`;
        });
    }

    function confirmDeleteTransaction(paymentId) {
        if (!confirm('Are you sure you want to delete this transaction? This action cannot be undone.')) {
            return;
        }

        const url = '/transactions/' + paymentId;
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(resp => {
            if (!resp.ok) {
                throw new Error(`HTTP ${resp.status}: ${resp.statusText}`);
            }
            return resp.json();
        })
        .then(json => {
            if (json.success) {
                const row = document.getElementById('payment-row-' + paymentId);
                if (row) row.remove();
                alert(json.message || 'Transaction deleted');
            } else {
                const errorDetails = `Error: ${json.message || 'Failed to delete transaction'}\n\nDetails:\n${JSON.stringify(json, null, 2)}`;
                console.error('Delete error:', json);
                alert(errorDetails);
            }
        })
        .catch(err => {
            console.error('Delete full error:', err);
            const errorMsg = `Error deleting transaction:\n\n${err.message}\n\nURL: ${url}\n\nPossible Causes:\n- Route may not exist\n- Authentication issue\n- Permission denied - check if you have accountant privileges\n\nCheck browser console (F12) for detailed error information.`;
            alert(errorMsg);
        });
    }
</script>
@endpush