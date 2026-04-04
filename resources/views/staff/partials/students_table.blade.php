{{-- resources/views/staff/partials/students_table.blade.php --}}

<style>
.action-btn-icon {
    width: 36px;
    height: 36px;
    padding: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
    margin: 2px;
    position: relative;
}

.action-btn-icon:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.action-btn-icon i {
    margin: 0;
}

.student-photo-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>

<!-- Enhanced Alert Messages -->
<div class="alert-container">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-enhanced d-flex align-items-start" role="alert">
            <div class="alert-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="alert-content">
                <div class="alert-title">Error</div>
                <p class="alert-message mb-0">{{ session('error') }}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-enhanced d-flex align-items-start" role="alert">
            <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
            <div class="alert-content">
                <div class="alert-title">Success</div>
                <p class="alert-message mb-0">{{ session('success') }}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show alert-enhanced d-flex align-items-start" role="alert">
            <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="alert-content">
                <div class="alert-title">Warning</div>
                <p class="alert-message mb-0">{{ session('warning') }}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>

<div class="table-card">
    <div class="table-header">
        <h5 class="table-title mb-0">
            <i class="fas fa-users me-2"></i>Students List
        </h5>
        <div class="table-actions">
            <button class="btn btn-outline-primary btn-sm me-2" onclick="exportToCSV()">
                <i class="fas fa-download me-1"></i><span class="d-none d-sm-inline">Export</span>
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="printTable()">
                <i class="fas fa-printer me-1"></i><span class="d-none d-sm-inline">Print</span>
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-header-custom">
                <tr>
                    <th class="d-none d-md-table-cell">#</th>
                    <th>Student Info</th>
                    <th class="d-none d-lg-table-cell">Branch</th>
                    <th class="d-none d-lg-table-cell">NESA Status</th>
                    <th class="d-none d-lg-table-cell">Registration Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                    @php
                        $student->load(['course', 'branch']);
                        $course = $student->course;
                        $discount = $student->discount ?? 0;

                        $registrationFee = $course ? floatval($course->registration_fee) : 0;
                        $tuitionFee      = $course ? floatval($course->tuition_fee) : 0;
                        $practicalFee    = $course ? floatval($course->practical_fee) : 0;
                        $tripFee         = $course ? floatval($course->trip_fee) : 0;

                        $discountableFees = $tuitionFee + $practicalFee + $tripFee;
                        $discountAmount   = ($discount > 0) ? ($discountableFees * ($discount / 100)) : 0;
                        $studentcard      = 2000;
                        $totalToPay       = $studentcard + $registrationFee + ($discountableFees - $discountAmount);

                        $studentTransactions = \App\Models\StudentTransaction::where('student_id', $student->id)
                            ->whereIn('status', ['successful', 'paid'])
                            ->get();

                        $totalPaid  = $studentTransactions->sum('amount');
                        $remaining  = $totalToPay - $totalPaid;

                        $paidRegistration = $studentTransactions->where('type', 'registration_fee')->sum('amount');
                        $paidSchoolFee    = $studentTransactions->where('type', 'school_fee')->sum('amount');
                        $paidPractical    = $studentTransactions->where('type', 'practical_fee')->sum('amount');
                        $paidTrip         = $studentTransactions->where('type', 'trip_fee')->sum('amount');
                        $paidStudentCard  = $studentTransactions->where('type', 'student_card')->sum('amount');

                        $remainingRegistration = max(0, $registrationFee - $paidRegistration);
                        $remainingSchoolFee    = max(0, $tuitionFee - $paidSchoolFee);
                        $remainingPractical    = max(0, $practicalFee - $paidPractical);
                        $remainingTrip         = max(0, $tripFee - $paidTrip);
                        $remainingStudentCard  = max(0, 2000 - $paidStudentCard);

                        $registrationComplete = $student->identification_type
                            && $student->id_number
                            && $student->id_photo
                            && $student->passport_photo;
                    @endphp

                    <tr class="student-row">
                        <td class="fw-bold text-primary d-none d-md-table-cell">
                            {{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}
                        </td>

                        <td>
                            <div class="student-info">
                                <div class="student-avatar d-none d-sm-flex">
                                    @if($student->passport_photo)
                                        @php
                                            if (strpos($student->passport_photo, 'data:image') === 0) {
                                                $photoSrc = $student->passport_photo;
                                            } elseif (strpos($student->passport_photo, 'students/') !== false) {
                                                $photoSrc = Storage::url($student->passport_photo);
                                            } else {
                                                $photoSrc = 'data:image/jpeg;base64,' . $student->passport_photo;
                                            }
                                        @endphp
                                        <img src="{{ $photoSrc }}"
                                             alt="{{ $student->full_name }}"
                                             class="student-photo-avatar"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="avatar-placeholder" style="display:none;">
                                            {{ strtoupper(substr($student->full_name, 0, 2)) }}
                                        </div>
                                    @else
                                        <div class="avatar-placeholder">
                                            {{ strtoupper(substr($student->full_name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="student-details">
                                    <span class="student-name d-block">
                                        {{ $student->full_name }}
                                        @if($student->nesa_registered)
                                            <small class="text-success d-block ms-2">
                                                <i class="fas fa-check-circle"></i>
                                                SDMS: {{ $student->nesa_sdms_code ?? 'N/A' }}
                                            </small>
                                        @endif
                                    </span>
                                    <small class="text-muted d-block">{{ $student->reg_number }}</small>
                                    <small class="text-muted d-block d-lg-none">
                                        {{ $student->branch->name ?? '-' }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td class="d-none d-lg-table-cell">{{ $student->branch->name ?? '-' }}</td>

                        <td class="d-none d-lg-table-cell">
                            @if($student->nesa_registered)
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i></span>
                            @else
                                <span class="badge bg-secondary"><i class="fas fa-times-circle"></i></span>
                            @endif
                        </td>

                        <td class="d-none d-lg-table-cell">
                            @if($registrationComplete)
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i></span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i></span>
                            @endif
                        </td>

                        <td>
                            {{-- ─────────────────────────────────────────────────────────────
                                 ACTION BUTTONS
                                 FIX: Each button uses ONLY data-bs-toggle="modal".
                                 Tooltips are handled via the title attribute + Bootstrap
                                 tooltip initialisation below (title-only, no duplicate toggle).
                            ───────────────────────────────────────────────────────────── --}}
                            <div class="d-flex gap-1 flex-wrap" role="group">

                                {{-- Edit --}}
                                <a href="{{ route('staff.students.edit', $student) }}"
                                   class="btn btn-sm btn-outline-primary action-btn-icon"
                                   title="Edit Student Info">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Complete Registration (only shown when incomplete) --}}
                                @if(!$registrationComplete)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-warning action-btn-icon"
                                            data-bs-toggle="modal"
                                            data-bs-target="#completeRegModal{{ $student->id }}"
                                            title="Complete Registration">
                                        <i class="fas fa-clipboard-check"></i>
                                    </button>
                                @endif

                                {{-- Pay Cash --}}
                                <button type="button"
                                        class="btn btn-sm btn-primary action-btn-icon"
                                        data-bs-toggle="modal"
                                        data-bs-target="#payModal{{ $student->id }}"
                                        title="Record Cash Payment">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>

                                {{-- Generate ID Card --}}
                                <button type="button"
                                        class="btn btn-sm btn-warning action-btn-icon"
                                        data-bs-toggle="modal"
                                        data-bs-target="#generateCardModal{{ $student->id }}"
                                        title="Generate Student ID Card">
                                    <i class="fas fa-id-card"></i>
                                </button>

                                {{-- Transcript (modal) --}}
                                <button type="button"
                                        class="btn btn-sm btn-info text-white action-btn-icon"
                                        data-bs-toggle="modal"
                                        data-bs-target="#transcriptModal{{ $student->id }}"
                                        title="View Payment Transcript">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </button>

                                {{-- Send Email --}}
                                <a href="{{ route('students.transcript.send', $student->id) }}"
                                   class="btn btn-sm btn-success action-btn-icon"
                                   title="Email Transcript to Student">
                                    <i class="fas fa-envelope"></i>
                                </a>

                                {{-- NESA Status Management --}}
                                <button type="button"
                                        class="btn btn-sm @if($student->nesa_registered) btn-outline-success @else btn-warning @endif action-btn-icon"
                                        data-bs-toggle="modal"
                                        data-bs-target="#nesaStatusModal{{ $student->id }}"
                                        title="@if($student->nesa_registered) Update NESA Status @else Set NESA Status @endif">
                                    <i class="fas @if($student->nesa_registered) fa-check-circle @else fa-exclamation-triangle @endif"></i>
                                </button>

                                {{-- Delete (admin only) --}}
                                @if(auth()->user() && auth()->user()->email === 'hirwabran9@gmail.com')
                                    <button type="button"
                                            class="btn btn-sm btn-danger action-btn-icon"
                                            onclick="confirmDelete({{ $student->id }}, '{{ addslashes($student->full_name) }}')"
                                            title="Delete Student">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- ═══════════════════════════════════════════════════
                         MODALS (one set per student row)
                    ════════════════════════════════════════════════════ --}}

                    {{-- Complete Registration Modal --}}
                    @if(!$registrationComplete)
                    <div class="modal fade" id="completeRegModal{{ $student->id }}" tabindex="-1" aria-labelledby="completeRegLabel{{ $student->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title" id="completeRegLabel{{ $student->id }}">
                                        <i class="fas fa-clipboard-check me-2"></i>
                                        Complete Registration — {{ $student->full_name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form id="completeRegForm{{ $student->id }}"
                                      onsubmit="handleCompleteRegistration(event, {{ $student->id }})">
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Please complete the required information to finalise student registration.
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required">Identification Type</label>
                                                <select name="identification_type" class="form-select" required>
                                                    <option value="">Select Type</option>
                                                    <option value="ID"       {{ $student->identification_type == 'ID'       ? 'selected' : '' }}>National ID</option>
                                                    <option value="Passport" {{ $student->identification_type == 'Passport' ? 'selected' : '' }}>Passport</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required">ID / Passport Number</label>
                                                <input type="text" name="id_number" class="form-control"
                                                       value="{{ $student->id_number }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required">ID Photo</label>
                                                <input type="file" name="id_photo" class="form-control"
                                                       accept="image/*" {{ $student->id_photo ? '' : 'required' }}>
                                                @if($student->id_photo)
                                                    <small class="text-success">
                                                        <i class="fas fa-check-circle"></i> Current photo exists
                                                        <a href="javascript:void(0)" onclick="viewDocument('id_photo', {{ $student->id }})" class="text-primary ms-1">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required">Passport Photo</label>
                                                <input type="file" name="passport_photo" class="form-control"
                                                       accept="image/*" {{ $student->passport_photo ? '' : 'required' }}>
                                                @if($student->passport_photo)
                                                    <small class="text-success">
                                                        <i class="fas fa-check-circle"></i> Current photo exists
                                                        <a href="javascript:void(0)" onclick="viewDocument('passport_photo', {{ $student->id }})" class="text-primary ms-1">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <hr class="my-4">
                                        <h6 class="mb-3"><i class="fas fa-file-alt me-2"></i>Supporting Documents</h6>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">S3 Result Slip</label>
                                                <input type="file" name="senior3_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                                @if($student->senior3_document)
                                                    <small class="text-success d-block">
                                                        <i class="fas fa-check-circle"></i> Document uploaded
                                                        <a href="javascript:void(0)" onclick="viewDocument('senior3_document', {{ $student->id }})" class="text-primary ms-1">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">S6 Result Slip</label>
                                                <input type="file" name="senior6_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                                @if($student->senior6_document)
                                                    <small class="text-success d-block">
                                                        <i class="fas fa-check-circle"></i> Document uploaded
                                                        <a href="javascript:void(0)" onclick="viewDocument('senior6_document', {{ $student->id }})" class="text-primary ms-1">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Other Supporting Document</label>
                                                <input type="file" name="other_supporting_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                                @if($student->other_supporting_document)
                                                    <small class="text-success d-block">
                                                        <i class="fas fa-check-circle"></i> Document uploaded
                                                        <a href="javascript:void(0)" onclick="viewDocument('other_supporting_document', {{ $student->id }})" class="text-primary ms-1">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-check me-1"></i>Complete Registration
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Payment Modal --}}
                    <div class="modal fade" id="payModal{{ $student->id }}" tabindex="-1" aria-labelledby="payLabel{{ $student->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="payLabel{{ $student->id }}">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Record Cash Payment — {{ $student->full_name }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form id="payForm{{ $student->id }}"
                                      action="{{ route('staff.students.pay-cash', $student) }}"
                                      method="POST"
                                      onsubmit="handlePayment(event, {{ $student->id }})">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row">
                                            <!-- Step 1: Select Term (REQUIRED FIRST) -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label required">
                                                    <i class="fas fa-calendar-alt me-1"></i>Academic Term
                                                </label>
                                                <select name="term_id"
                                                        id="termSelect{{ $student->id }}"
                                                        class="form-select"
                                                        required
                                                        onchange="loadTermFees({{ $student->id }})">
                                                    <option value="">-- Select Term First --</option>
                                                    @php
                                                        $terms = \App\Models\Term::with('academicYear')
                                                            ->orderBy('start_date', 'desc')
                                                            ->get();
                                                    @endphp
                                                    @foreach($terms as $term)
                                                        <option value="{{ $term->id }}">
                                                            {{ $term->name }} — {{ $term->academicYear->year ?? '' }}
                                                            ({{ $term->start_date->format('M d') }} - {{ $term->end_date->format('M d, Y') }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">You must select a term to proceed</small>
                                            </div>

                                            <!-- Term Summary Alert -->
                                            <div id="termSummary{{ $student->id }}" class="col-md-12 mb-3 d-none">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong>Term Summary:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        <li>Total Fees: <strong id="totalTermFees{{ $student->id }}">0</strong> RWF</li>
                                                        <li>Total Paid: <strong id="totalTermPaid{{ $student->id }}">0</strong> RWF</li>
                                                        <li>Remaining: <strong id="totalTermRemaining{{ $student->id }}">0</strong> RWF</li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- Step 2: Select Fee Type (Hidden Until Term Selected) -->
                                            <div id="feeTypeContainer{{ $student->id }}" class="col-md-12 mb-3 d-none">
                                                <label class="form-label required">
                                                    <i class="fas fa-receipt me-1"></i>Fee Type for {{ $student->course->course_name ?? 'Course' }}
                                                </label>
                                                <select name="fee_type"
                                                        id="feeTypeSelect{{ $student->id }}"
                                                        class="form-select"
                                                        required
                                                        onchange="updateFeeInfo(this, {{ $student->id }})">
                                                    <option value="">-- Select a Fee --</option>
                                                </select>
                                                <small class="text-muted">Select which fee type you are recording payment for</small>
                                                <div id="feeInfo{{ $student->id }}" class="mt-2"></div>
                                            </div>

                                            <!-- Step 3: Enter Amount (Hidden Until Fee Selected) -->
                                            <div id="amountContainer{{ $student->id }}" class="col-md-12 mb-3 d-none">
                                                <label class="form-label required">
                                                    <i class="fas fa-money-bill-wave me-1"></i>Amount to Pay (RWF)
                                                </label>
                                                <input type="number"
                                                       name="amount_paid"
                                                       id="amountInput{{ $student->id }}"
                                                       class="form-control form-control-lg"
                                                       placeholder="Enter amount received from student"
                                                       min="1" step="1"
                                                       oninput="validateAmount({{ $student->id }})"
                                                       onchange="validateAmount({{ $student->id }})">
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Enter the exact amount received from the student
                                                </small>
                                                <div id="amountError{{ $student->id }}" class="text-danger small mt-2 fw-bold"></div>
                                            </div>

                                            <!-- Step 4: Notes (Optional) -->
                                            <div id="notesContainer{{ $student->id }}" class="col-md-12 mb-3 d-none">
                                                <label class="form-label">Payment Notes (Optional)</label>
                                                <textarea name="payment_notes" 
                                                         id="notesInput{{ $student->id }}"
                                                         class="form-control" rows="2"
                                                         placeholder="Add any additional notes about this payment (e.g., partial payment, late payment, etc.)"></textarea>
                                            </div>

                                            <!-- Loading Indicator -->
                                            <div id="loadingIndicator{{ $student->id }}" class="col-md-12 text-center d-none">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <small class="text-muted ms-2">Loading fees for selected term...</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn{{ $student->id }}" disabled>
                                            <i class="fas fa-check me-1"></i>Record Payment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                    // Load term fees via AJAX
                    function loadTermFees(studentId) {
                        const termSelect = document.getElementById('termSelect' + studentId);
                        const termId = termSelect.value;
                        
                        if (!termId) {
                            // Hide all subsequent fields when term is cleared
                            document.getElementById('termSummary' + studentId)?.classList.add('d-none');
                            document.getElementById('feeTypeContainer' + studentId)?.classList.add('d-none');
                            document.getElementById('amountContainer' + studentId)?.classList.add('d-none');
                            document.getElementById('notesContainer' + studentId)?.classList.add('d-none');
                            document.getElementById('submitBtn' + studentId).disabled = true;
                            return;
                        }

                        // Show loading indicator
                        document.getElementById('loadingIndicator' + studentId).classList.remove('d-none');
                        document.getElementById('feeTypeContainer' + studentId).classList.add('d-none');
                        document.getElementById('amountContainer' + studentId).classList.add('d-none');
                        document.getElementById('notesContainer' + studentId).classList.add('d-none');

                        // Fetch term fees
                        fetch(`/staff/students/${studentId}/term-fees?term_id=${termId}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update summary
                                document.getElementById('totalTermFees' + studentId).textContent = 
                                    new Intl.NumberFormat('en-US').format(Math.round(data.summary.total_fees));
                                document.getElementById('totalTermPaid' + studentId).textContent = 
                                    new Intl.NumberFormat('en-US').format(Math.round(data.summary.total_paid));
                                document.getElementById('totalTermRemaining' + studentId).textContent = 
                                    new Intl.NumberFormat('en-US').format(Math.round(data.summary.total_remaining));
                                document.getElementById('termSummary' + studentId)?.classList.remove('d-none');

                                // Populate fee dropdown
                                const feeSelect = document.getElementById('feeTypeSelect' + studentId);
                                feeSelect.innerHTML = '<option value="">-- Select a Fee --</option>';
                                
                                if (data.fees.length === 0) {
                                    feeSelect.innerHTML += '<option disabled>All fees have been paid for this term!</option>';
                                    document.getElementById('feeTypeContainer' + studentId)?.classList.add('d-none');
                                } else {
                                    data.fees.forEach(fee => {
                                        const option = document.createElement('option');
                                        option.value = fee.type;
                                        option.textContent = `${fee.name} — Remaining: ${new Intl.NumberFormat('en-US').format(Math.round(fee.remaining))} RWF`;
                                        option.dataset.total = fee.total;
                                        option.dataset.paid = fee.paid;
                                        option.dataset.remaining = fee.remaining;
                                        feeSelect.appendChild(option);
                                    });
                                    document.getElementById('feeTypeContainer' + studentId)?.classList.remove('d-none');
                                }
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to load fees. Please try again.');
                        })
                        .finally(() => {
                            document.getElementById('loadingIndicator' + studentId).classList.add('d-none');
                        });
                    }

                    // Update fee info when fee type changes
                    function updateFeeInfo(select, studentId) {
                        const feeType = select.value;
                        const amountContainer = document.getElementById('amountContainer' + studentId);
                        const notesContainer = document.getElementById('notesContainer' + studentId);
                        const amountInput = document.getElementById('amountInput' + studentId);
                        const submitBtn = document.getElementById('submitBtn' + studentId);
                        const feeInfoDiv = document.getElementById('feeInfo' + studentId);

                        if (!feeType) {
                            amountContainer?.classList.add('d-none');
                            notesContainer?.classList.add('d-none');
                            feeInfoDiv.innerHTML = '';
                            submitBtn.disabled = true;
                            return;
                        }

                        const option = select.querySelector(`option[value="${feeType}"]`);
                        const remaining = parseFloat(option.dataset.remaining) || 0;
                        const total = parseFloat(option.dataset.total) || 0;
                        const paid = parseFloat(option.dataset.paid) || 0;

                        // Show remaining amount info with fee breakdown
                        feeInfoDiv.innerHTML = `
                            <div class="alert alert-info m-0 small">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <strong>Total Fee:</strong><br>${new Intl.NumberFormat('en-US').format(Math.round(total))} RWF
                                    </div>
                                    <div class="col-6">
                                        <strong>Already Paid:</strong><br>${new Intl.NumberFormat('en-US').format(Math.round(paid))} RWF
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-top">
                                    <strong class="text-danger">Remaining Balance:</strong><br>
                                    <strong class="text-danger">${new Intl.NumberFormat('en-US').format(Math.round(remaining))} RWF</strong>
                                </div>
                            </div>
                        `;

                        amountInput.max = remaining;
                        amountInput.value = '';
                        amountInput.placeholder = `Max: ${new Intl.NumberFormat('en-US').format(Math.round(remaining))} RWF`;
                        
                        // Show amount and notes containers
                        amountContainer?.classList.remove('d-none');
                        notesContainer?.classList.remove('d-none');
                        
                        // Scroll to amount input if needed
                        setTimeout(() => {
                            amountInput?.focus();
                        }, 100);
                        
                        submitBtn.disabled = true;
                    }

                    // Validate amount before submission
                    function validateAmount(studentId) {
                        const amountInput = document.getElementById('amountInput' + studentId);
                        const feeSelect = document.getElementById('feeTypeSelect' + studentId);
                        const errorDiv = document.getElementById('amountError' + studentId);
                        const submitBtn = document.getElementById('submitBtn' + studentId);

                        const amount = parseFloat(amountInput.value) || 0;
                        const option = feeSelect.querySelector(`option[value="${feeSelect.value}"]`);
                        const remaining = parseFloat(option?.dataset.remaining) || 0;

                        errorDiv.textContent = '';

                        if (amount <= 0) {
                            errorDiv.textContent = '⚠️ Amount must be greater than 0';
                            submitBtn.disabled = true;
                            return false;
                        }

                        if (amount > remaining) {
                            errorDiv.textContent = `⚠️ Amount exceeds remaining balance of ${new Intl.NumberFormat('en-US').format(Math.round(remaining))} RWF`;
                            submitBtn.disabled = true;
                            return false;
                        }

                        // Amount is valid
                        submitBtn.disabled = false;
                        return true;
                    }
                    </script>

                    {{-- ─────────────────────────────────────────────────────────────
                         FIX: Generate ID Card Modal — was COMPLETELY MISSING before.
                         This is why the button caused "Cannot read properties of
                         undefined (reading 'backdrop')" — Bootstrap tried to open a
                         modal element that didn't exist in the DOM.
                    ───────────────────────────────────────────────────────────── --}}
                    <div class="modal fade" id="generateCardModal{{ $student->id }}" tabindex="-1" aria-labelledby="generateCardLabel{{ $student->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title" id="generateCardLabel{{ $student->id }}">
                                        <i class="fas fa-id-card me-2"></i>Generate Student ID Card
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form id="generateCardForm{{ $student->id }}"
                                      action="{{ route('staff.students.generate_card', $student->id) }}"
                                      method="POST"
                                      onsubmit="handleGenerateCard(event, {{ $student->id }})">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="text-center mb-3">
                                            @if($student->passport_photo)
                                                <img src="{{ isset($photoSrc) ? $photoSrc : '' }}"
                                                     alt="{{ $student->full_name }}"
                                                     class="rounded-circle mb-3"
                                                     style="width:80px;height:80px;object-fit:cover;border:3px solid #ffc107;">
                                            @else
                                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                     style="width:80px;height:80px;background:linear-gradient(135deg,#667eea,#764ba2);font-size:28px;color:#fff;font-weight:700;">
                                                    {{ strtoupper(substr($student->full_name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <h6 class="mb-1">{{ $student->full_name }}</h6>
                                            <small class="text-muted">{{ $student->reg_number }}</small>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            This will generate and print/download a student ID card.
                                            @if(!$registrationComplete)
                                                <hr class="my-2">
                                                <span class="text-warning fw-bold">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Registration is incomplete. The card may be missing some information.
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-warning" id="generateCardBtn{{ $student->id }}">
                                            <i class="fas fa-id-card me-1"></i>Generate ID Card
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Transcript Modal --}}
                    <div class="modal fade" id="transcriptModal{{ $student->id }}" tabindex="-1" aria-labelledby="transcriptLabel{{ $student->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title" id="transcriptLabel{{ $student->id }}">
                                        Payment Transcript — {{ $student->full_name }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body transcript-modal-body">
                                    <div class="text-center">
                                        <span class="spinner-border"></span> Loading…
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- NESA Registration Modal --}}
                    @if(!$student->nesa_registered)
                    <div class="modal fade" id="nesaRegistrationModal{{ $student->id }}" tabindex="-1" aria-labelledby="nesaRegistrationLabel{{ $student->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-info text-white">
                                    <h5 class="modal-title" id="nesaRegistrationLabel{{ $student->id }}">
                                        <i class="fas fa-award me-2"></i>
                                        NESA Registration — {{ $student->full_name }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form id="nesaRegistrationForm{{ $student->id }}"
                                      action="{{ route('staff.students.nesa', $student) }}"
                                      method="POST"
                                      onsubmit="handleNesaRegistration(event, {{ $student->id }})">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Register this student for NESA examination and send notification email.
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="nesa_sdms_code_{{ $student->id }}" class="form-label required">NESA SDMS Code</label>
                                                <input type="text" name="nesa_sdms_code" id="nesa_sdms_code_{{ $student->id }}" 
                                                       class="form-control" required maxlength="50"
                                                       placeholder="Enter SDMS code">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="nesa_reg_number_{{ $student->id }}" class="form-label required">NESA Registration Number</label>
                                                <input type="text" name="nesa_reg_number" id="nesa_reg_number_{{ $student->id }}" 
                                                       class="form-control" required maxlength="50"
                                                       placeholder="Enter registration number">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="nesa_document_{{ $student->id }}" class="form-label">NESA Document</label>
                                                <input type="file" name="nesa_document" id="nesa_document_{{ $student->id }}" 
                                                       class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                <div class="form-text">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 5MB)</div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="nesa_remarks_{{ $student->id }}" class="form-label">Remarks</label>
                                                <textarea name="nesa_remarks" id="nesa_remarks_{{ $student->id }}" 
                                                          class="form-control" rows="3" maxlength="500" 
                                                          placeholder="Any additional remarks..."></textarea>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="send_email" id="send_email_{{ $student->id }}" checked>
                                                    <label class="form-check-label" for="send_email_{{ $student->id }}">
                                                        Send registration confirmation email to student
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-info" id="nesaSubmitBtn{{ $student->id }}">
                                            <i class="fas fa-award me-1"></i>Register for NESA
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- NESA Status Modal --}}
                    <div class="modal fade" id="nesaStatusModal{{ $student->id }}" tabindex="-1" aria-labelledby="nesaStatusLabel{{ $student->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header @if($student->nesa_registered) bg-success @else bg-warning @endif text-white">
                                    <h5 class="modal-title" id="nesaStatusLabel{{ $student->id }}">
                                        <i class="fas fa-award me-2"></i>
                                        NESA Status Management — {{ $student->full_name }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    @if($student->nesa_registered)
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            Current Status: Registered with NESA
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <strong>SDMS Code:</strong><br>
                                                <span class="text-success">{{ $student->nesa_sdms_code ?? 'N/A' }}</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <strong>Registration Number:</strong><br>
                                                <span class="text-success">{{ $student->nesa_reg_number ?? 'N/A' }}</span>
                                            </div>
                                        </div>

                                        @if($student->nesa_registration_date)
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <strong>Registration Date:</strong><br>
                                                <span>{{ $student->nesa_registration_date->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                        @endif

                                        @if($student->nesa_document)
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <strong>NESA Document:</strong><br>
                                                <button type="button" class="btn btn-success btn-sm" onclick="downloadNesaDocument({{ $student->id }})">
                                                    <i class="fas fa-download me-1"></i>Download Document
                                                </button>
                                            </div>
                                        </div>
                                        @endif
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Current Status: Not registered with NESA
                                        </div>
                                    @endif

                                    <hr>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h6 class="mb-3">Update NESA Status</h6>
                                            
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio" name="nesa_status" id="registered_{{ $student->id }}" value="registered" @if($student->nesa_registered) checked @endif>
                                                <label class="form-check-label" for="registered_{{ $student->id }}">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    <strong>Registered</strong> - Student successfully registered with NESA
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio" name="nesa_status" id="failed_{{ $student->id }}" value="failed" @if(!$student->nesa_registered) checked @endif>
                                                <label class="form-check-label" for="failed_{{ $student->id }}">
                                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                                    <strong>Failed</strong> - Student registration was not successful
                                                </label>
                                            </div>

                                            <div class="mb-3" id="reason_section_{{ $student->id }}" style="display: none;">
                                                <label for="failure_reason_{{ $student->id }}" class="form-label">Reason for Failure</label>
                                                <textarea class="form-control" id="failure_reason_{{ $student->id }}" rows="3" placeholder="Please explain why the registration failed..."></textarea>
                                            </div>

                                            <div class="mb-3" id="document_section_{{ $student->id }}" style="display: none;">
                                                <label for="nesa_document_{{ $student->id }}" class="form-label">NESA Document (Proof of Registration)</label>
                                                <input type="file" class="form-control" id="nesa_document_{{ $student->id }}" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                <div class="form-text">Upload the NESA registration document as proof</div>
                                            </div>

                                            <div class="mb-3" id="email_section_{{ $student->id }}">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="send_email_{{ $student->id }}" checked>
                                                    <label class="form-check-label" for="send_email_{{ $student->id }}">
                                                        Send email notification to student
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" onclick="updateNesaStatus({{ $student->id }})">
                                        <i class="fas fa-save me-1"></i>Update Status
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr class="no-data-row">
                        <td colspan="6" class="text-center py-5">
                            <div class="no-data-content">
                                <i class="fas fa-users display-1 text-muted mb-3"></i>
                                <h5 class="text-muted">No Students Found</h5>
                                <p class="text-muted">There are no students matching your search criteria.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->hasPages())
    <div class="table-footer">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center p-3 gap-3">
            <div class="text-muted">
                Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
            </div>
            <div id="paginationLinks">{{ $students->links() }}</div>
        </div>
    </div>
    @endif
</div>

<style>
.required::after { content: ' *'; color: #dc3545; }

.student-info    { display: flex; align-items: center; gap: 12px; }

.student-avatar  {
    width: 50px; height: 50px; border-radius: 50%; overflow: hidden;
    flex-shrink: 0; border: 2px solid #e9ecef;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.avatar-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; color: white; font-size: 18px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.student-details { flex: 1; min-width: 0; }
.student-name    { font-weight: 600; color: #2c3e50; }
</style>

<script>
// ─── Utility ────────────────────────────────────────────────────────────────

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value
        || '';
}

function showAlert(type, message) {
    const icons = { success: 'check-circle', danger: 'exclamation-circle', warning: 'exclamation-triangle' };
    const titles = { success: 'Success', danger: 'Error', warning: 'Warning' };

    const html = `
        <div class="alert alert-${type} alert-dismissible fade show alert-enhanced d-flex align-items-start" role="alert">
            <div class="alert-icon"><i class="fas fa-${icons[type] || 'info-circle'}"></i></div>
            <div class="alert-content">
                <div class="alert-title">${titles[type] || type}</div>
                <p class="alert-message mb-0">${message}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;

    let container = document.querySelector('.alert-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'alert-container';
        document.body.insertBefore(container, document.body.firstChild);
    }
    container.innerHTML = html;
    window.scrollTo({ top: 0, behavior: 'smooth' });

    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) { alert.classList.remove('show'); setTimeout(() => alert.remove(), 300); }
    }, 5000);
}

// ─── Fee info updater ────────────────────────────────────────────────────────

function updateFeeInfo(selectEl, studentId) {
    const option     = selectEl.options[selectEl.selectedIndex];
    const total      = parseFloat(option?.getAttribute('data-total')     || 0);
    const paid       = parseFloat(option?.getAttribute('data-paid')      || 0);
    const remaining  = parseFloat(option?.getAttribute('data-remaining') || 0);
    const infoDiv    = document.getElementById(`feeInfo${studentId}`);
    const amountInput = document.getElementById(`amountInput${studentId}`);

    if (selectEl.value && remaining > 0) {
        infoDiv.innerHTML = `
            <div class="card border-warning">
                <div class="card-body py-2">
                    <h6 class="card-title mb-2"><i class="fas fa-info-circle text-primary me-1"></i>Fee Breakdown</h6>
                    <div class="row g-2 small">
                        <div class="col-4"><div class="text-muted">Total Fee:</div><strong>${total.toLocaleString()} RWF</strong></div>
                        <div class="col-4"><div class="text-muted">Already Paid:</div><strong class="text-success">${paid.toLocaleString()} RWF</strong></div>
                        <div class="col-4"><div class="text-muted">Remaining:</div><strong class="text-danger">${remaining.toLocaleString()} RWF</strong></div>
                    </div>
                    ${paid > 0 ? `<div class="progress mt-2" style="height:20px;">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width:${(paid/total*100).toFixed(1)}%">
                            ${(paid/total*100).toFixed(1)}% Paid
                        </div></div>` : ''}
                </div>
            </div>`;
        if (amountInput) { amountInput.max = remaining; amountInput.value = remaining; amountInput.disabled = false; }
    } else if (selectEl.value) {
        infoDiv.innerHTML = `<div class="alert alert-success mb-0"><i class="fas fa-check-circle me-2"></i>This fee has been fully paid!</div>`;
        if (amountInput) { amountInput.value = ''; amountInput.disabled = true; }
    } else {
        infoDiv.innerHTML = '';
        if (amountInput) { amountInput.disabled = false; amountInput.value = ''; amountInput.placeholder = 'Enter amount'; }
    }
}

// ─── Handle Payment ──────────────────────────────────────────────────────────
// FIX: The original function had a missing closing `}` that caused
//      "Uncaught SyntaxError: Unexpected end of input", breaking ALL JS on the page.

function handlePayment(event, studentId) {
    event.preventDefault();

    const form        = event.target;
    const formData    = new FormData(form);
    const submitBtn   = document.getElementById(`submitBtn${studentId}`);
    const selectEl    = document.getElementById(`feeTypeSelect${studentId}`);

    const feeType = formData.get('fee_type');
    const amount  = parseFloat(formData.get('amount_paid'));

    if (!feeType)          { showAlert('danger', 'Please select a fee type');    return; }
    if (!amount || amount <= 0) { showAlert('danger', 'Please enter a valid amount'); return; }

    if (selectEl) {
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const remaining = parseFloat(selectedOption?.getAttribute('data-remaining') || 0);
        if (amount > remaining) {
            showAlert('danger', `Amount exceeds remaining balance of ${remaining.toLocaleString()} RWF`);
            return;
        }
        const feeName = (selectedOption?.text || '').split(' — ')[0] || feeType;
        if (!confirm(`Confirm payment of ${amount.toLocaleString()} RWF for ${feeName}?`)) return;
    } else {
        const feeName = feeType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        if (!confirm(`Confirm payment of ${amount.toLocaleString()} RWF for ${feeName}?`)) return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing…';

    fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
        credentials: 'same-origin'
    })
    .then(async response => {
        const ct = response.headers.get('content-type') || '';
        let data;
        if (ct.includes('application/json')) {
            data = await response.json();
        } else {
            const text = await response.text();
            try { data = JSON.parse(text); } catch { data = { success: response.ok, message: response.ok ? 'Payment recorded successfully' : 'Payment failed' }; }
        }
        return { ok: response.ok, data };
    })
    .then(({ ok, data }) => {
        if (ok && data.success !== false) {
            bootstrap.Modal.getInstance(document.getElementById(`payModal${studentId}`))?.hide();
            showAlert('success', data.message || 'Payment recorded successfully');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('danger', data.message || data.error || 'Payment failed. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Record Payment';
        }
    })
    .catch(err => {
        console.error('Payment error:', err);
        showAlert('danger', 'Payment failed. Please check your connection and try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Record Payment';
    });
}

// ─── Handle Complete Registration ────────────────────────────────────────────

function handleCompleteRegistration(event, studentId) {
    event.preventDefault();

    const form      = document.getElementById(`completeRegForm${studentId}`);
    const formData  = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing…';

    fetch(`/staff/students/${studentId}/complete-registration`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
        credentials: 'same-origin'
    })
    .then(async response => {
        const ct = response.headers.get('content-type') || '';
        let data;
        if (ct.includes('application/json')) {
            data = await response.json();
        } else {
            const text = await response.text();
            try { data = JSON.parse(text); } catch { data = { success: response.ok, message: response.ok ? 'Registration completed successfully' : 'Registration completion failed' }; }
        }
        return { ok: response.ok, data };
    })
    .then(({ ok, data }) => {
        if (ok && data.success !== false) {
            bootstrap.Modal.getInstance(document.getElementById(`completeRegModal${studentId}`))?.hide();
            showAlert('success', data.message || 'Registration completed successfully');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('danger', data.message || data.error || 'Registration completion failed.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Complete Registration';
        }
    })
    .catch(err => {
        console.error('Registration error:', err);
        showAlert('danger', 'Registration completion failed. Please check your connection and try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Complete Registration';
    });
}

// ─── Handle Generate ID Card ─────────────────────────────────────────────────
// FIX: Replaced fragile regex approach with explicit studentId parameter.

function handleGenerateCard(event, studentId) {
    event.preventDefault();

    const form      = event.target;
    const submitBtn = document.getElementById(`generateCardBtn${studentId}`);

    if (!confirm(`Generate ID Card for this student?`)) return;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating…';

    fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form),
        credentials: 'same-origin'
    })
    .then(async response => {
        const ct = response.headers.get('content-type') || '';
        let data;
        if (ct.includes('application/json')) {
            data = await response.json();
        } else {
            const text = await response.text();
            try { data = JSON.parse(text); } catch { data = { success: response.ok, message: response.ok ? 'Student card generated successfully' : 'Card generation failed' }; }
        }
        return { ok: response.ok, data };
    })
    .then(({ ok, data }) => {
        if (ok && data.success !== false) {
            bootstrap.Modal.getInstance(document.getElementById(`generateCardModal${studentId}`))?.hide();
            showAlert('success', data.message || 'Student card generated successfully');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('danger', data.message || data.error || 'Failed to generate student card.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-id-card me-1"></i>Generate ID Card';
        }
    })
    .catch(err => {
        console.error('Card generation error:', err);
        showAlert('danger', 'Card generation failed. Please check your connection and try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-id-card me-1"></i>Generate ID Card';
    });
}

// ─── View Document ───────────────────────────────────────────────────────────

function viewDocument(documentType, studentId) {
    const existing = document.getElementById('documentModal');
    if (existing) existing.remove();

    document.body.insertAdjacentHTML('beforeend', `
        <div class="modal fade" id="documentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Document Viewer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="spinner-border" role="status"></div>
                        <p class="mt-2">Loading document…</p>
                    </div>
                </div>
            </div>
        </div>`);

    new bootstrap.Modal(document.getElementById('documentModal')).show();

    fetch(`/staff/students/${studentId}/view-document/${documentType}`)
        .then(r => { if (!r.ok) throw new Error('Failed'); return r.blob(); })
        .then(blob => {
            const url = URL.createObjectURL(blob);
            const body = document.querySelector('#documentModal .modal-body');
            if (blob.type.startsWith('image/')) {
                body.innerHTML = `<img src="${url}" class="img-fluid" style="max-height:70vh;" alt="Document">
                    <div class="mt-3"><a href="${url}" download class="btn btn-primary"><i class="fas fa-download me-1"></i>Download</a></div>`;
            } else if (blob.type === 'application/pdf') {
                body.innerHTML = `<iframe src="${url}" width="100%" height="500px" style="border:none;"></iframe>
                    <div class="mt-3"><a href="${url}" download="document.pdf" class="btn btn-primary"><i class="fas fa-download me-1"></i>Download</a></div>`;
            } else {
                body.innerHTML = `<div class="alert alert-info"><i class="fas fa-file me-2"></i>Document ready</div>
                    <a href="${url}" download class="btn btn-primary"><i class="fas fa-download me-1"></i>Download Document</a>`;
            }
        })
        .catch(() => {
            document.querySelector('#documentModal .modal-body').innerHTML =
                `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Failed to load document.</div>`;
        });
}

// ─── Handle NESA Registration ─────────────────────────────────────────────────────

function handleNesaRegistration(event, studentId) {
    event.preventDefault();

    const form      = event.target;
    const formData  = new FormData(form);
    const submitBtn = document.getElementById(`nesaSubmitBtn${studentId}`);

    const sdmsCode = formData.get('nesa_sdms_code');
    const regNumber = formData.get('nesa_reg_number');

    if (!sdmsCode) { showAlert('danger', 'Please enter NESA SDMS code'); return; }
    if (!regNumber) { showAlert('danger', 'Please enter NESA registration number'); return; }

    if (!confirm(`Register this student for NESA examination?`)) return;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registering…';

    fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: formData,
        credentials: 'same-origin'
    })
    .then(async response => {
        const ct = response.headers.get('content-type') || '';
        let data;
        if (ct.includes('application/json')) {
            data = await response.json();
        } else {
            const text = await response.text();
            try { data = JSON.parse(text); } catch { data = { success: response.ok, message: response.ok ? 'NESA registration successful' : 'NESA registration failed' }; }
        }
        return { ok: response.ok, data };
    })
    .then(({ ok, data }) => {
        if (ok && data.success !== false) {
            bootstrap.Modal.getInstance(document.getElementById(`nesaRegistrationModal${studentId}`))?.hide();
            showAlert('success', data.message || 'NESA registration successful');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('danger', data.message || data.error || 'NESA registration failed. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-award me-1"></i>Register for NESA';
        }
    })
    .catch(err => {
        console.error('NESA registration error:', err);
        showAlert('danger', 'NESA registration failed. Please check your connection and try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-award me-1"></i>Register for NESA';
    });
}

// ─── Send NESA Status Email ─────────────────────────────────────────────────────

function sendNesaStatusEmail(studentId, status) {
    const confirmation = status === 'approved' 
        ? 'Send NESA approval email to this student?' 
        : 'Send NESA rejection email to this student?';
    
    if (!confirm(confirmation)) return;

    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

    fetch(`/staff/students/${studentId}/send-nesa-email`, {
        method: 'POST',
        headers: { 
            'X-CSRF-TOKEN': getCsrfToken(), 
            'Accept': 'application/json', 
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: status }),
        credentials: 'same-origin'
    })
    .then(async response => {
        const ct = response.headers.get('content-type') || '';
        let data;
        if (ct.includes('application/json')) {
            data = await response.json();
        } else {
            const text = await response.text();
            try { data = JSON.parse(text); } catch { data = { success: response.ok, message: response.ok ? 'Email sent successfully' : 'Email sending failed' }; }
        }
        return { ok: response.ok, data };
    })
    .then(({ ok, data }) => {
        if (ok && data.success !== false) {
            showAlert('success', data.message || `${status === 'approved' ? 'Approval' : 'Rejection'} email sent successfully`);
        } else {
            showAlert('danger', data.message || data.error || 'Failed to send email. Please try again.');
        }
        button.disabled = false;
        button.innerHTML = originalText;
    })
    .catch(err => {
        console.error('Email sending error:', err);
        showAlert('danger', 'Failed to send email. Please check your connection and try again.');
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

// ─── Edit NESA Registration ─────────────────────────────────────────────────────

function editNesaRegistration(studentId) {
    // Close the status modal
    bootstrap.Modal.getInstance(document.getElementById(`nesaStatusModal${studentId}`))?.hide();
    
    // Show a simple edit form (could be enhanced to open a new modal)
    const sdmsCode = prompt('Enter new SDMS code:');
    if (sdmsCode === null) return;
    
    const regNumber = prompt('Enter new registration number:');
    if (regNumber === null) return;
    
    if (!sdmsCode.trim() || !regNumber.trim()) {
        showAlert('danger', 'Both SDMS code and registration number are required');
        return;
    }

    fetch(`/staff/students/${studentId}/nesa`, {
        method: 'PUT',
        headers: { 
            'X-CSRF-TOKEN': getCsrfToken(), 
            'Accept': 'application/json', 
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ 
            nesa_sdms_code: sdmsCode.trim(),
            nesa_reg_number: regNumber.trim()
        }),
        credentials: 'same-origin'
    })
    .then(async response => {
        const ct = response.headers.get('content-type') || '';
        let data;
        if (ct.includes('application/json')) {
            data = await response.json();
        } else {
            const text = await response.text();
            try { data = JSON.parse(text); } catch { data = { success: response.ok, message: response.ok ? 'NESA registration updated' : 'Update failed' }; }
        }
        return { ok: response.ok, data };
    })
    .then(({ ok, data }) => {
        if (ok && data.success !== false) {
            showAlert('success', data.message || 'NESA registration updated successfully');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('danger', data.message || data.error || 'Failed to update NESA registration. Please try again.');
        }
    })
    .catch(err => {
        console.error('NESA update error:', err);
        showAlert('danger', 'Failed to update NESA registration. Please check your connection and try again.');
    });
}

// ─── Download NESA Document ───────────────────────────────────────────────────

function downloadNesaDocument(studentId) {
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Downloading...';

    fetch(`/staff/students/${studentId}/download-nesa-document`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Download failed');
            }
            return response.blob();
        })
        .then(blob => {
            // Get filename from response headers or create one
            const contentDisposition = response.headers.get('content-disposition');
            let filename = 'nesa_document.pdf';
            
            if (contentDisposition) {
                const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                if (filenameMatch) {
                    filename = filenameMatch[1];
                }
            }
            
            // Create download link
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            showAlert('success', 'NESA document downloaded successfully');
        })
        .catch(error => {
            console.error('Download error:', error);
            showAlert('danger', 'Failed to download NESA document. Please try again.');
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        });
}

// ─── Transcript lazy-load ────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {
    // Transcript modals — lazy-load content on open
    document.querySelectorAll('[id^="transcriptModal"]').forEach(modal => {
        modal.addEventListener('show.bs.modal', function () {
            const studentId = this.id.replace('transcriptModal', '');
            const body = this.querySelector('.transcript-modal-body');
            fetch(`/students/${studentId}/transcript/ajax`)
                .then(r => r.text())
                .then(html => { body.innerHTML = html; })
                .catch(() => { body.innerHTML = '<div class="alert alert-danger">Failed to load transcript</div>'; });
        });
    });

    // Bootstrap tooltips — title attribute only, no data-bs-toggle conflict
    document.querySelectorAll('[title]').forEach(el => {
        if (!el.hasAttribute('data-bs-toggle')) {
            new bootstrap.Tooltip(el, { trigger: 'hover' });
        }
    });
});
</script>