{{-- resources/views/staff/partials/students_table.blade.php --}}

<style>
.action-btn-icon {
    min-height: 40px;
    padding: .5rem .85rem;
    border-radius: 9px;
    font-size: .85rem;
    font-weight: 600;
    white-space: nowrap;
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
        <div>
            <h5 class="table-title mb-1">
                <i class="fas fa-users me-2"></i>Students List
            </h5>
            <div class="table-caption">Student records, registration and payment activity</div>
        </div>
        <div class="table-actions">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="try { exportToCSV(); } catch(e) { console.error('Export error:', e); alert('Unable to export. Please try again.'); }">
                <i class="fas fa-download me-2"></i>Export
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="try { printTable(); } catch(e) { console.error('Print error:', e); alert('Unable to print. Please try again.'); }">
                <i class="fas fa-print me-2"></i>Print
            </button>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="filter-section mb-3">
        <form method="GET" action="{{ request()->url() }}" class="row g-2">
            <div class="col-md-3 col-sm-6">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 col-sm-6">
                <select name="nesa_status" class="form-select form-select-sm">
                    <option value="">All NESA Status</option>
                    <option value="registered" {{ request('nesa_status') === 'registered' ? 'selected' : '' }}>Registered</option>
                    <option value="not_registered" {{ request('nesa_status') === 'not_registered' ? 'selected' : '' }}>Not Registered</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <select name="branch_id" class="form-select form-select-sm">
                    <option value="">All Branches</option>
                    @if(isset($branches))
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <select name="registration_status" class="form-select form-select-sm">
                    <option value="">All Registration</option>
                    <option value="complete" {{ request('registration_status') === 'complete' ? 'selected' : '' }}>Complete</option>
                    <option value="incomplete" {{ request('registration_status') === 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ request()->url() }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
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

                        $studentTransactions = \App\Models\StudentTransaction::where('student_id', $student->id)
                            ->whereIn('status', ['successful', 'paid'])
                            ->get();

                        // Get the latest academic year from student's term
                        $latestPayment = \App\Models\StudentTransaction::where('student_id', $student->id)
                            ->with('term.academicYear')
                            ->orderByDesc('created_at')
                            ->first();
                        $currentAcademicYear = $latestPayment && $latestPayment->term ? $latestPayment->term->academicYear : null;

                        // Check if registration fee was paid in the same academic year
                        $registrationPaidInYear = false;
                        if ($currentAcademicYear) {
                            $registrationPaidInYear = \App\Models\StudentTransaction::where('student_id', $student->id)
                                ->where('type', 'registration')
                                ->whereIn('status', ['successful', 'paid'])
                                ->whereHas('term', function ($query) use ($currentAcademicYear) {
                                    $query->where('academic_year_id', $currentAcademicYear->id);
                                })
                                ->exists();
                        }

                        // Exclude registration fee if already paid in this academic year
                        if ($registrationPaidInYear) {
                            $totalToPay = $studentcard + ($discountableFees - $discountAmount);
                        } else {
                            $totalToPay = $studentcard + $registrationFee + ($discountableFees - $discountAmount);
                        }

                        $totalPaid  = $studentTransactions->sum('amount');
                        $remaining  = $totalToPay - $totalPaid;

                        $paidRegistration = $studentTransactions->where('type', 'registration')->sum('amount');
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
                                <span class="badge status-badge status-success"><i class="fas fa-check-circle me-1"></i>Registered</span>
                            @else
                                <span class="badge status-badge status-neutral"><i class="fas fa-times-circle me-1"></i>Not registered</span>
                            @endif
                        </td>

                        <td class="d-none d-lg-table-cell">
                            @if($registrationComplete)
                                <span class="badge status-badge status-success"><i class="fas fa-check-circle me-1"></i>Complete</span>
                            @else
                                <span class="badge status-badge status-warning"><i class="fas fa-exclamation-triangle me-1"></i>Incomplete</span>
                            @endif
                        </td>

                        <td>
                            <div class="dropdown student-actions">
                                <button type="button"
                                        class="btn btn-outline-secondary dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        data-bs-boundary="viewport"
                                        aria-expanded="false"
                                        aria-label="Actions for {{ $student->full_name }}"
                                        onclick="toggleStudentActionsDropdown(this, event)">
                                    <i class="fas fa-ellipsis me-2"></i>Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end student-actions-menu" id="actions-menu-{{ $student->id }}">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('staff.students.edit', $student) }}">
                                            <i class="fas fa-user-pen"></i><span>Edit student information</span>
                                        </a>
                                    </li>

                                    @if(!$registrationComplete)
                                        <li>
                                            <button type="button"
                                                    class="dropdown-item"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#completeRegModal{{ $student->id }}">
                                                <i class="fas fa-clipboard-check"></i><span>Complete registration</span>
                                            </button>
                                        </li>
                                    @endif

                                    <li>
                                        <button type="button"
                                                class="dropdown-item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#payModal{{ $student->id }}"
                                                data-student-id="{{ $student->id }}"
                                                onclick="openPayModal({{ $student->id }}); return false;">
                                            <i class="fas fa-money-bill-wave"></i><span>Record cash payment</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button"
                                                class="dropdown-item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#generateCardModal{{ $student->id }}">
                                            <i class="fas fa-id-card"></i><span>Generate student ID card</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button"
                                                class="dropdown-item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#transcriptModal{{ $student->id }}">
                                            <i class="fas fa-file-invoice-dollar"></i><span>View payment transcript</span>
                                        </button>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('staff.students.transcript.send', $student->id) }}">
                                            <i class="fas fa-envelope"></i><span>Email transcript to student</span>
                                        </a>
                                    </li>
                                    <li>
                                        <button type="button"
                                                class="dropdown-item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#nesaStatusModal{{ $student->id }}">
                                            <i class="fas fa-award"></i>
                                            <span>@if($student->nesa_registered) Update NESA status @else Set NESA status @endif</span>
                                        </button>
                                    </li>

                                    @if(auth()->user() && auth()->user()->email === 'hirwabran9@gmail.com')
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button type="button"
                                                    class="dropdown-item text-danger"
                                                    onclick="try { confirmDelete({{ $student->id }}, '{{ addslashes($student->full_name) }}'); } catch(e) { console.error('Delete error:', e); alert('Unable to delete student. Please try again.'); }">
                                                <i class="fas fa-trash"></i><span>Delete student</span>
                                            </button>
                                        </li>
                                    @endif
                                </ul>
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
                                      action="/staff/students/{{ $student->id }}/pay-cash"
                                      method="POST"
                                      onsubmit="handlePayment(event, {{ $student->id }})">
                                    @csrf
                                    <div class="modal-body" style="display: block !important; visibility: visible !important; opacity: 1 !important; padding: 20px; width: 100%;">
                                        <div style="display: flex; flex-wrap: wrap; gap: 15px; width: 100%; margin: 0;">
                                            <!-- Step 1: Select Term (REQUIRED FIRST) -->
                                            <div class="col-md-12 mb-3" style="display: block !important; width: 100%; flex: 0 0 100%;">
                                                <label class="form-label required">
                                                    <i class="fas fa-calendar-alt me-1"></i>Academic Term
                                                </label>
                                                <select name="term_id"
                                                        id="termSelect{{ $student->id }}"
                                                        class="form-select"
                                                        required
                                                        onchange="loadTermFees({{ $student->id }})"
                                                        style="display: block !important; width: 100% !important; visibility: visible !important; opacity: 1 !important;">
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
                                            <div id="termSummary{{ $student->id }}" class="col-md-12 mb-3" style="display: block !important; width: 100%; flex: 0 0 100%;">
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

                                            <!-- Step 2: Select Fee Type (ALWAYS VISIBLE) -->
                                            <div id="feeTypeContainer{{ $student->id }}" class="col-md-12 mb-3" style="display: block !important; width: 100%; flex: 0 0 100%;">
                                                <label class="form-label required">
                                                    <i class="fas fa-receipt me-1"></i>Fee Type for {{ $student->course->course_name ?? 'Course' }}
                                                </label>
                                                <select name="fee_type"
                                                        id="feeTypeSelect{{ $student->id }}"
                                                        class="form-select"
                                                        required
                                                        onchange="updateFeeInfo(this, {{ $student->id }})"
                                                        style="display: block !important; width: 100% !important; visibility: visible !important; opacity: 1 !important;">
                                                    <option value="">-- Select a Fee --</option>
                                                </select>
                                                <small class="text-muted">Select which fee type you are recording payment for</small>
                                                <div id="feeInfo{{ $student->id }}" class="mt-2"></div>
                                            </div>

                                            <!-- Step 3: Enter Amount (ALWAYS VISIBLE) -->
                                            <div id="amountContainer{{ $student->id }}" class="col-md-12 mb-3" style="display: block !important; width: 100%; flex: 0 0 100%;">
                                                <label class="form-label required">
                                                    <i class="fas fa-money-bill-wave me-1"></i>Amount to Pay (RWF)
                                                </label>
                                                <input type="number"
                                                       name="amount_paid"
                                                       id="amountInput{{ $student->id }}"
                                                       class="form-control form-control-lg"
                                                       placeholder="Enter amount received from student"
                                                       min="1" step="1"
                                                       style="display: block !important; width: 100% !important; visibility: visible !important; opacity: 1 !important; padding: 10px; border: 1px solid #ccc; font-size: 16px;">
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Enter the exact amount received from the student
                                                </small>
                                                <div id="amountError{{ $student->id }}" class="text-danger small mt-2 fw-bold"></div>
                                            </div>

                                            <!-- Step 4: Notes (Optional - ALWAYS VISIBLE) -->
                                            <div id="notesContainer{{ $student->id }}" class="col-md-12 mb-3" style="display: block !important; width: 100%; flex: 0 0 100%;">
                                                <label class="form-label">Payment Notes (Optional)</label>
                                                <textarea name="payment_notes" 
                                                         id="notesInput{{ $student->id }}"
                                                         class="form-control" rows="2"
                                                         placeholder="Add any additional notes about this payment (e.g., partial payment, late payment, etc.)"
                                                         style="display: block !important; width: 100% !important; visibility: visible !important; opacity: 1 !important; padding: 10px; border: 1px solid #ccc; font-size: 14px;"></textarea>
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
                                        <button type="button" class="btn btn-secondary" onclick="closePayModal({{ $student->id }})">Cancel</button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn{{ $student->id }}">
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
                            // Clear fee dropdown when term is cleared
                            const feeSelect = document.getElementById('feeTypeSelect' + studentId);
                            feeSelect.innerHTML = '<option value="">-- Select a Fee --</option>';
                            document.getElementById('feeInfo' + studentId).innerHTML = '';
                            return;
                        }

                        // Show loading indicator
                        document.getElementById('loadingIndicator' + studentId).classList.remove('d-none');

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

                                // Populate fee dropdown
                                const feeSelect = document.getElementById('feeTypeSelect' + studentId);
                                feeSelect.innerHTML = '<option value="">-- Select a Fee --</option>';
                                
                                if (data.fees.length === 0) {
                                    feeSelect.innerHTML += '<option disabled>All fees have been paid for this term!</option>';
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
                                }
                            } else {
                                showAlert('danger', 'Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showAlert('danger', 'Failed to load fees. Please try again.');
                        })
                        .finally(() => {
                            document.getElementById('loadingIndicator' + studentId).classList.add('d-none');
                        });
                    }

                    // Update fee info when fee type changes
                    function updateFeeInfo(select, studentId) {
                        const feeType = select.value;
                        const feeInfoDiv = document.getElementById('feeInfo' + studentId);

                        if (!feeType) {
                            feeInfoDiv.innerHTML = '';
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

                        // Update max amount input
                        const amountInput = document.getElementById('amountInput' + studentId);
                        amountInput.max = remaining;
                        amountInput.placeholder = `Max: ${new Intl.NumberFormat('en-US').format(Math.round(remaining))} RWF`;
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
        <div class="table-pagination">
            <div class="pagination-summary" aria-live="polite">
                Showing <strong>{{ $students->firstItem() }}</strong>–<strong>{{ $students->lastItem() }}</strong>
                of <strong>{{ number_format($students->total()) }}</strong> students
            </div>
            <div id="paginationLinks">
                {{ $students->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.required::after { content: ' *'; color: #dc3545; }

.table-card {
    overflow: visible;
    border: 1px solid #e3e9e5;
    border-radius: 15px;
    background: #fff;
    box-shadow: 0 10px 28px rgba(30, 52, 44, .06);
}

.table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.15rem 1.4rem;
    border-bottom: 1px solid #e8ede9;
}

.table-title {
    color: #20352e;
    font-size: 1.02rem;
    font-weight: 700;
}

.table-title i { color: #39705c; }

.table-caption {
    color: #78847e;
    font-size: .82rem;
}

.table-actions {
    display: flex;
    gap: .55rem;
}

.table-actions .btn {
    padding: .48rem .75rem;
    border-color: #d7e0da;
    border-radius: 8px;
    color: #40574d;
    font-weight: 600;
}

.table-actions .btn:hover {
    border-color: #9ab4a6;
    color: #1d493d;
    background: #f3f7f4;
}

.table-card .table-responsive { overflow: visible; }

.table-card .table {
    margin-bottom: 0;
    color: #34443d;
    font-size: .9rem;
}

.table-card .table > :not(caption) > * > * {
    padding: .9rem .8rem;
    border-bottom-color: #edf0ee;
}

.table-header-custom th {
    padding-top: .75rem !important;
    padding-bottom: .75rem !important;
    color: #6b7972;
    background: #f7f9f7;
    border-bottom: 1px solid #e3e9e5 !important;
    font-size: .73rem;
    font-weight: 700;
    letter-spacing: .055em;
    text-transform: uppercase;
    white-space: nowrap;
}

.student-row { transition: background-color .16s ease; }
.student-row:hover { background: #f8faf8; }

.student-info    { display: flex; align-items: center; gap: 12px; }

.student-avatar  {
    width: 50px; height: 50px; border-radius: 50%; overflow: hidden;
    flex-shrink: 0; border: 2px solid #e9ecef;
    display: flex; align-items: center; justify-content: center;
    background: #e8f0eb;
}

.avatar-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; color: #285748; font-size: 18px;
    background: #e8f0eb;
}

.student-details { flex: 1; min-width: 0; }
.student-name    { font-weight: 650; color: #23372f; }

.status-badge {
    padding: .42rem .58rem;
    border: 1px solid transparent;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 600;
}

.status-success { color: #286047; background: #eaf4ee; border-color: #d4e9dc; }
.status-neutral { color: #66736c; background: #f0f3f1; border-color: #e1e7e3; }
.status-warning { color: #825d19; background: #fff5dc; border-color: #f2e3b9; }

.student-actions .dropdown-toggle {
    min-height: 38px;
    padding: .42rem .7rem;
    border-color: #d5dfd9;
    border-radius: 8px;
    color: #355347;
    background: #fff;
    font-size: .82rem;
    font-weight: 600;
    white-space: nowrap;
}

.student-actions .dropdown-toggle:hover,
.student-actions .dropdown-toggle:focus {
    border-color: #8eac9d;
    color: #1f4b3e;
    background: #f4f8f5;
    box-shadow: 0 0 0 .18rem rgba(36, 87, 75, .1);
}

.student-actions-menu {
    min-width: 245px;
    padding: .4rem;
    border: 1px solid #e1e8e3;
    border-radius: 11px;
    box-shadow: 0 14px 34px rgba(26, 47, 39, .16);
    z-index: 1090;
}

.student-actions-menu .dropdown-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    min-height: 39px;
    padding: .55rem .65rem;
    border-radius: 7px;
    color: #34463d;
    font-size: .84rem;
    text-align: left;
}

.student-actions-menu .dropdown-item:hover,
.student-actions-menu .dropdown-item:focus {
    color: #1f5141;
    background: #eef5f0;
}

.student-actions-menu .dropdown-item i {
    width: 17px;
    color: #648274;
    text-align: center;
}

.student-actions-menu .dropdown-item.text-danger i { color: inherit; }
.student-actions-menu .dropdown-divider { margin: .35rem 0; }

.table-footer {
    padding: .9rem 1.25rem;
    border-top: 1px solid #e8ede9;
    background: #fbfcfb;
}

.table-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.pagination-summary {
    color: #738078;
    font-size: .84rem;
    white-space: nowrap;
}

.pagination-summary strong { color: #34483e; font-weight: 700; }

#paginationLinks .pagination {
    gap: .3rem;
    margin: 0;
}

#paginationLinks .page-link {
    min-width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .35rem .65rem;
    border: 1px solid #dfe7e2;
    border-radius: 8px !important;
    color: #4a6256;
    background: #fff;
    font-size: .84rem;
    box-shadow: none;
}

#paginationLinks .page-link:hover {
    border-color: #9db8a8;
    color: #1d503f;
    background: #f0f6f2;
}

#paginationLinks .page-item.active .page-link {
    border-color: #285c4d;
    color: #fff;
    background: #285c4d;
}

#paginationLinks .page-item.disabled .page-link {
    color: #a4aea8;
    background: #f7f9f7;
}

#paginationLinks .page-link:focus {
    box-shadow: 0 0 0 .18rem rgba(36, 87, 75, .14);
}

@media (max-width: 768px) {
    .table-header { padding: 1rem; }
    .table-pagination { flex-direction: column; align-items: flex-start; }
    #paginationLinks { max-width: 100%; }
    #paginationLinks .pagination { flex-wrap: wrap; }
}

@media (max-width: 480px) {
    .table-header { align-items: flex-start; }
    .table-caption { max-width: 220px; }
    .table-actions { flex-direction: column; }
    .table-actions .btn { padding: .4rem .55rem; }
    .table-card .table > :not(caption) > * > * { padding: .7rem .55rem; }
}
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

// ─── Dropdown Toggle (Fallback for Bootstrap issues) ───────────────────────────
function toggleStudentActionsDropdown(button, event) {
    event.preventDefault();
    event.stopPropagation();

    // Close all other dropdowns first
    document.querySelectorAll('.student-actions-menu.show').forEach(menu => {
        if (menu.id !== button.nextElementSibling.id) {
            menu.classList.remove('show');
        }
    });

    // Toggle this dropdown
    const menu = button.nextElementSibling;
    menu.classList.toggle('show');
    button.setAttribute('aria-expanded', menu.classList.contains('show'));
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.student-actions')) {
        document.querySelectorAll('.student-actions-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
        document.querySelectorAll('.student-actions .dropdown-toggle').forEach(btn => {
            btn.setAttribute('aria-expanded', 'false');
        });
    }
});

// ─── Explicit Modal Opener (Fallback for Bootstrap issues) ────────────────────
// FIX: If Bootstrap isn't working due to SSL issues,  these functions will
//      explicitly open modals using vanilla JavaScript
function openPayModal(studentId) {
    console.log('🔓 openPayModal called for:', studentId);
    const modalElement = document.getElementById('payModal' + studentId);
    
    if (!modalElement) {
        console.error('❌ Modal not found: payModal' + studentId);
        showAlert('danger', 'Payment modal not found. Please refresh the page.');
        return;
    }
    
    try {
        // Try Bootstrap first
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log('✅ Modal opened with Bootstrap');
    } catch (err) {
        console.warn('⚠️ Bootstrap Modal failed:', err);
        // Fallback: show modal manually
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        document.body.classList.add('modal-open');
        console.log('✅ Modal opened with fallback');
    }
}

// Close Payment Modal
function closePayModal(studentId) {
    const modalElement = document.getElementById('payModal' + studentId);
    if (!modalElement) return;
    
    try {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        } else {
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            document.body.classList.remove('modal-open');
        }
    } catch (err) {
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
}

// ─── Load Transcript ──────────────────────────────────────────────────────────
function loadTranscript(studentId) {
    const modalBody = document.querySelector(`#transcriptModal${studentId} .transcript-modal-body`);
    if (!modalBody) {
        console.error('Transcript modal body not found for student:', studentId);
        return;
    }
    
    modalBody.innerHTML = '<div class="text-center py-4"><span class="spinner-border text-primary"></span><p class="mt-2 text-muted">Loading transcript…</p></div>';
    
    fetch(`/staff/students/${studentId}/transcript/ajax`, {
        method: 'GET',
        headers: {
            'Accept': 'text/html',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to load transcript');
        }
        return response.text();
    })
    .then(html => {
        modalBody.innerHTML = html;
    })
    .catch(error => {
        console.error('Error loading transcript:', error);
        modalBody.innerHTML = '<div class="alert alert-danger">Failed to load transcript. Please try again.</div>';
    });
}

// ─── Initialize Transcript Modal Listeners ─────────────────────────────────────
function initializeTranscriptModals() {
    // Add event listeners for transcript modals
    document.querySelectorAll('[id^="transcriptModal"]').forEach(modal => {
        modal.removeEventListener('show.bs.modal', handleTranscriptModalShow);
        modal.addEventListener('show.bs.modal', handleTranscriptModalShow);
    });
}

function handleTranscriptModalShow(event) {
    const modalId = event.target.id;
    const studentId = modalId.replace('transcriptModal', '');
    loadTranscript(studentId);
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
    const termSelect  = document.getElementById(`termSelect${studentId}`);
    const feeSelect   = document.getElementById(`feeTypeSelect${studentId}`);
    const amountInput = document.getElementById(`amountInput${studentId}`);
    const errorDiv    = document.getElementById(`amountError${studentId}`);

    const termId  = termSelect.value;
    const feeType = formData.get('fee_type');
    const amount  = parseFloat(formData.get('amount_paid')) || 0;

    // ─── VALIDATION ──────────────────────────────────────────
    if (!termId) {
        errorDiv.textContent = '❌ Please select an academic term';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return false;
    }

    if (!feeType) {
        errorDiv.textContent = '❌ Please select a fee type';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return false;
    }

    if (!amount || amount <= 0) {
        errorDiv.textContent = '❌ Please enter a valid amount greater than 0';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return false;
    }

    // Check if amount exceeds remaining balance
    const selectedOption = feeSelect.options[feeSelect.selectedIndex];
    const remaining = parseFloat(selectedOption?.dataset.remaining) || 0;

    if (amount > remaining) {
        errorDiv.textContent = `❌ Amount (${amount.toLocaleString()} RWF) exceeds remaining balance of ${remaining.toLocaleString()} RWF`;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return false;
    }

    // Clear any previous errors
    errorDiv.textContent = '';

    // ─── CONFIRMATION ────────────────────────────────────────
    const feeName = (selectedOption?.text || '').split(' — ')[0] || feeType;
    if (!confirm(`Confirm payment of ${amount.toLocaleString()} RWF for ${feeName}?`)) return false;

    // ─── SUBMIT ──────────────────────────────────────────────
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing…';

    // Use AJAX POST with correct path
    fetch(`/staff/students/${studentId}/pay-cash`, {
        method: 'POST',
        headers: { 
            'X-CSRF-TOKEN': getCsrfToken(), 
            'Accept': 'application/json', 
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(formData),
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
            closePayModal(studentId);
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

    return false;
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
            if (data.cards_url) {
                window.location.assign(data.cards_url);
                return;
            }

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
    // Initialize transcript modals
    initializeTranscriptModals();

    // Bootstrap tooltips — title attribute only, no data-bs-toggle conflict
    document.querySelectorAll('[title]').forEach(el => {
        if (!el.hasAttribute('data-bs-toggle')) {
            new bootstrap.Tooltip(el, { trigger: 'hover' });
        }
    });
});
</script>