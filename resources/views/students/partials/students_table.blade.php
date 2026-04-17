{{-- resources/views/staff/partials/students_table.blade.php --}}

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
                    <th class="d-none d-lg-table-cell">Course</th>
                    <th class="d-none d-lg-table-cell">Branch</th>
                    <th class="d-none d-xl-table-cell">Total Fees</th>
                    <th class="d-none d-lg-table-cell">Paid</th>
                    <th>Balance</th>
                    <th class="d-none d-lg-table-cell">Registration Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                    @php
                        $course = \App\Models\Course::find($student->course_id);
                        $discount = $student->discount ?? 0;

                        $registrationFee  = $course ? floatval($course->registration_fee) : 0;
                        $tuitionFee       = $course ? floatval($course->tuition_fee) : 0;
                        $practicalFee     = $course ? floatval($course->practical_fee ?? $course->pratical_fee ?? 0) : 0;
                        $tripFee          = $course ? floatval($course->trip_fee) : 0;
                        $studentcard      = 2000;

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

                        $discountableFees = $tuitionFee + $practicalFee + $tripFee;
                        $discountAmount   = ($discount > 0) ? ($discountableFees * ($discount / 100)) : 0;
                        
                        // Exclude registration fee if already paid in this academic year
                        if ($registrationPaidInYear) {
                            $totalToPay = $studentcard + ($discountableFees - $discountAmount);
                        } else {
                            $totalToPay = $studentcard + $registrationFee + ($discountableFees - $discountAmount);
                        }

                        $totalPaid  = \App\Models\StudentTransaction::where('student_id', $student->id)
                                        ->whereIn('status', ['successful', 'paid'])
                                        ->sum('amount');
                        $remaining  = max(0, $totalToPay - $totalPaid);

                        // Registration is complete only when ALL four fields are present
                        $registrationComplete = $student->identification_type
                                             && $student->id_number
                                             && $student->id_photo
                                             && $student->passport_photo
                                             && $student->sex;
                    @endphp

                    <tr class="student-row">
                        <td class="fw-bold text-primary d-none d-md-table-cell">
                            {{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}
                        </td>

                        <td>
                            <div class="student-info">
                                <div class="student-avatar d-none d-sm-flex">
                                    @if($student->passport_photo)
                                        <img src="{{ asset('storage/' . $student->passport_photo) }}" alt="Photo" class="avatar-img">
                                    @else
                                        <div class="avatar-placeholder">
                                            {{ substr($student->full_name, 0, 2) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="student-details">
                                    <span class="student-name d-block">{{ $student->full_name }}</span>
                                    <small class="text-muted d-block">{{ $student->reg_number }}</small>
                                    <small class="text-muted d-block d-lg-none">
                                        {{ $student->course->abbreviation ?? '-' }} | {{ $student->branch->name ?? '-' }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td class="d-none d-lg-table-cell">{{ $student->course->abbreviation ?? '-' }}</td>
                        <td class="d-none d-lg-table-cell">{{ $student->branch->name ?? '-' }}</td>

                        <td class="d-none d-xl-table-cell">
                            <span class="badge bg-primary">{{ number_format($totalToPay, 0) }} RWF</span>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <span class="badge bg-info">{{ number_format($totalPaid, 0) }} RWF</span>
                        </td>
                        <td>
                            <span class="badge {{ $remaining > 0 ? 'bg-warning text-dark' : 'bg-success' }}">
                                {{ number_format($remaining, 0) }} RWF
                            </span>
                        </td>

                        <td class="d-none d-lg-table-cell">
                            @if($registrationComplete)
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> Complete</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> Incomplete</span>
                            @endif
                        </td>

                        <td>
                            <div class="btn-group-vertical d-grid gap-1" role="group">
                                <a href="{{ route('staff.students.edit', $student) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Edit Student Information">
                                    <i class="fas fa-edit me-1"></i> Edit Info
                                </a>

                                @if(!$registrationComplete)
                                    <button class="btn btn-sm btn-outline-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#completeRegModal{{ $student->id }}"
                                            title="Complete Registration">
                                        <i class="fas fa-clipboard-check me-1"></i> Complete Reg
                                    </button>
                                @endif

                                <button class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#payModal{{ $student->id }}"
                                        title="Record Cash Payment">
                                    <i class="fas fa-money-bill-wave me-1"></i> Pay Cash
                                </button>

                                <button class="btn btn-sm btn-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#generateCardModal{{ $student->id }}"
                                        title="Generate Student ID Card">
                                    <i class="fas fa-id-card me-1"></i> ID Card
                                </button>

                                <button class="btn btn-sm btn-info text-white"
                                        data-bs-toggle="modal"
                                        data-bs-target="#transcriptModal{{ $student->id }}"
                                        title="View Payment Transcript">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> Transcript
                                </button>

                                <a href="{{ route('students.transcript.send', $student->id) }}"
                                   class="btn btn-sm btn-success"
                                   title="Email Transcript to Student">
                                    <i class="fas fa-envelope me-1"></i> Send Email
                                </a>

                                @if(auth()->user() && auth()->user()->email === 'hirwabran9@gmail.com')
                                    <button class="btn btn-sm btn-danger"
                                            onclick="confirmDelete({{ $student->id }}, '{{ addslashes($student->full_name) }}')"
                                            title="Delete Student">
                                        <i class="fas fa-trash me-1"></i> Delete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- ============================================================
                         COMPLETE REGISTRATION MODAL
                    ============================================================ --}}
                    @if(!$registrationComplete)
                    <div class="modal fade" id="completeRegModal{{ $student->id }}" tabindex="-1" aria-labelledby="completeRegLabel{{ $student->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title" id="completeRegLabel{{ $student->id }}">
                                        <i class="fas fa-clipboard-check me-2"></i>
                                        Complete Registration — {{ $student->full_name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <form id="completeRegForm{{ $student->id }}"
                                      enctype="multipart/form-data"
                                      onsubmit="handleCompleteRegistration(event, {{ $student->id }})">
                                    @csrf
                                    <div class="modal-body">

                                        <div class="alert alert-info mb-3">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Please fill in all missing information to finalise this student's registration.
                                        </div>

                                        {{-- ── Row 1: Identification ── --}}
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label required">Identification Type</label>
                                                <select name="identification_type" class="form-select" required>
                                                    <option value="">Select Type</option>
                                                    <option value="ID"       {{ $student->identification_type == 'ID'       ? 'selected' : '' }}>National ID</option>
                                                    <option value="Passport" {{ $student->identification_type == 'Passport' ? 'selected' : '' }}>Passport</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label required">ID / Passport Number</label>
                                                <input type="text" name="id_number" class="form-control"
                                                       value="{{ $student->id_number }}" required
                                                       placeholder="Enter ID or passport number">
                                            </div>
                                        </div>

                                        {{-- ── Row 2: Sex & Education Level ── --}}
                                        <div class="row g-3 mt-1">
                                            <div class="col-md-6">
                                                <label class="form-label {{ !$student->sex ? 'required' : '' }}">
                                                    Sex
                                                    @if($student->sex)
                                                        <small class="text-success ms-1">
                                                            <i class="fas fa-check-circle"></i> Saved: {{ $student->sex }}
                                                        </small>
                                                    @endif
                                                </label>
                                                <select name="sex" class="form-select" {{ !$student->sex ? 'required' : '' }}>
                                                    <option value="">Select Sex</option>
                                                    <option value="Male"   {{ $student->sex == 'Male'   ? 'selected' : '' }}>Male</option>
                                                    <option value="Female" {{ $student->sex == 'Female' ? 'selected' : '' }}>Female</option>
                                                    <option value="Other"  {{ $student->sex == 'Other'  ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    Education Level
                                                    @if($student->education_level)
                                                        <small class="text-success ms-1">
                                                            <i class="fas fa-check-circle"></i> Saved: {{ $student->education_level }}
                                                        </small>
                                                    @endif
                                                </label>
                                                <select name="education_level" class="form-select"
                                                        id="eduLevel{{ $student->id }}"
                                                        onchange="toggleDocFields({{ $student->id }})">
                                                    <option value="">Select Level</option>
                                                    <option value="Senior 3" {{ $student->education_level == 'Senior 3' ? 'selected' : '' }}>Senior 3</option>
                                                    <option value="Senior 6" {{ $student->education_level == 'Senior 6' ? 'selected' : '' }}>Senior 6</option>
                                                    <option value="Other"    {{ $student->education_level == 'Other'    ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- ── Row 3: Photos ── --}}
                                        <div class="row g-3 mt-1">
                                            <div class="col-md-6">
                                                <label class="form-label {{ !$student->id_photo ? 'required' : '' }}">
                                                    ID Photo
                                                    @if($student->id_photo)
                                                        <small class="text-success ms-1">
                                                            <i class="fas fa-check-circle"></i> Already uploaded
                                                        </small>
                                                    @endif
                                                </label>
                                                <input type="file" name="id_photo" class="form-control"
                                                       accept="image/jpg,image/jpeg,image/png"
                                                       {{ !$student->id_photo ? 'required' : '' }}>
                                                <small class="text-muted">JPG / PNG, max 2 MB</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label {{ !$student->passport_photo ? 'required' : '' }}">
                                                    Passport Photo
                                                    @if($student->passport_photo)
                                                        <small class="text-success ms-1">
                                                            <i class="fas fa-check-circle"></i> Already uploaded
                                                        </small>
                                                    @endif
                                                </label>
                                                <input type="file" name="passport_photo" class="form-control"
                                                       accept="image/jpg,image/jpeg,image/png"
                                                       {{ !$student->passport_photo ? 'required' : '' }}>
                                                <small class="text-muted">JPG / PNG, max 2 MB</small>
                                            </div>
                                        </div>

                                        {{-- ── Row 4: Supporting Documents (shown based on education level) ── --}}
                                        <div id="docFields{{ $student->id }}" class="mt-3"
                                             style="{{ in_array($student->education_level, ['Senior 3','Senior 6','Other']) ? '' : 'display:none;' }}">

                                            <div id="senior3Doc{{ $student->id }}"
                                                 style="{{ $student->education_level == 'Senior 3' ? '' : 'display:none;' }}">
                                                <label class="form-label">Senior 3 Certificate</label>
                                                <input type="file" name="senior3_document" class="form-control"
                                                       accept="image/jpg,image/jpeg,image/png,application/pdf">
                                                @if($student->senior3_document)
                                                    <small class="text-success"><i class="fas fa-check-circle"></i> Already uploaded</small>
                                                @endif
                                            </div>

                                            <div id="senior6Doc{{ $student->id }}"
                                                 style="{{ $student->education_level == 'Senior 6' ? '' : 'display:none;' }}">
                                                <label class="form-label">Senior 6 Certificate</label>
                                                <input type="file" name="senior6_document" class="form-control"
                                                       accept="image/jpg,image/jpeg,image/png,application/pdf">
                                                @if($student->senior6_document)
                                                    <small class="text-success"><i class="fas fa-check-circle"></i> Already uploaded</small>
                                                @endif
                                            </div>

                                            <div id="otherDoc{{ $student->id }}"
                                                 style="{{ $student->education_level == 'Other' ? '' : 'display:none;' }}">
                                                <label class="form-label">Supporting Document</label>
                                                <input type="file" name="other_supporting_document" class="form-control"
                                                       accept="image/jpg,image/jpeg,image/png,application/pdf">
                                                @if($student->other_supporting_document)
                                                    <small class="text-success"><i class="fas fa-check-circle"></i> Already uploaded</small>
                                                @endif
                                            </div>
                                        </div>

                                    </div>{{-- /modal-body --}}

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-warning" id="completeRegSubmit{{ $student->id }}">
                                            <i class="fas fa-check me-1"></i>Complete Registration
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ============================================================
                         PAY CASH MODAL
                    ============================================================ --}}
                    <div class="modal fade" id="payModal{{ $student->id }}" tabindex="-1" aria-labelledby="payLabel{{ $student->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="payLabel{{ $student->id }}">
                                        <i class="fas fa-money-bill-wave me-2"></i>Record Cash Payment — {{ $student->full_name }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form id="payForm{{ $student->id }}" onsubmit="handleCashPayment(event, {{ $student->id }})">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label required">Fee Type</label>
                                            <select name="fee_type" class="form-select" required>
                                                <option value="">Select Fee Type</option>
                                                <option value="registration_fee">Registration Fee</option>
                                                <option value="school_fee">School / Tuition Fee</option>
                                                <option value="practical_fee">Practical Fee</option>
                                                <option value="trip_fee">Trip Fee</option>
                                                <option value="student_card">Student Card</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label required">Amount Paid (RWF)</label>
                                            <input type="number" name="amount_paid" class="form-control"
                                                   min="1" step="1" required
                                                   placeholder="Enter amount in RWF">
                                        </div>
                                        <div class="alert alert-info mb-0">
                                            <small>
                                                <i class="fas fa-info-circle me-1"></i>
                                                Current balance: <strong>{{ number_format($remaining, 0) }} RWF</strong>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary" id="paySubmit{{ $student->id }}">
                                            <i class="fas fa-check me-1"></i>Record Payment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================================
                         TRANSCRIPT MODAL
                    ============================================================ --}}
                    <div class="modal fade" id="transcriptModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title">Payment Transcript — {{ $student->full_name }}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body transcript-modal-body-{{ $student->id }}">
                                    <div class="text-center py-4">
                                        <span class="spinner-border text-primary"></span>
                                        <p class="mt-2 text-muted">Loading transcript…</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================================
                         GENERATE STUDENT CARD MODAL
                    ============================================================ --}}
                    <div class="modal fade" id="generateCardModal{{ $student->id }}" tabindex="-1" aria-labelledby="generateCardLabel{{ $student->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" id="generateCardLabel{{ $student->id }}">
                                        <i class="fas fa-id-card me-2"></i>
                                        Generate Student ID Card — {{ $student->full_name }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('students.generate_card', $student->id) }}" onsubmit="return confirmGenerateCard(event, '{{ $student->full_name }}')">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Student Information:</strong>
                                            <div class="mt-2">
                                                <p class="mb-1"><strong>Name:</strong> {{ $student->full_name }}</p>
                                                <p class="mb-1"><strong>Reg Number:</strong> {{ $student->reg_number }}</p>
                                                <p class="mb-1"><strong>Course:</strong> {{ $student->course->course_name ?? 'N/A' }}</p>
                                                <p class="mb-0"><strong>Branch:</strong> {{ $student->branch->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Important:</strong> Student must have paid the Student Card fee (2000 RWF) before generating ID card.
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-id-card me-1"></i>
                                            Generate ID Card
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-1"></i>
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr class="no-data-row">
                        <td colspan="9" class="text-center py-5">
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
            <div id="paginationLinks">
                {{ $students->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.required::after {
    content: ' *';
    color: #dc3545;
}
.btn-group-vertical .btn {
    text-align: left;
    font-weight: 500;
    border-radius: 0.25rem !important;
    transition: all 0.3s ease;
    padding: 0.4rem 0.75rem;
    font-size: clamp(0.8rem, 2vw, 0.9rem);
}
.btn-group-vertical .btn:hover {
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
</style>

<script>
// ─── Education level document field toggle ────────────────────────────────────
function toggleDocFields(studentId) {
    const level   = document.getElementById('eduLevel' + studentId).value;
    const wrapper = document.getElementById('docFields' + studentId);
    const s3      = document.getElementById('senior3Doc' + studentId);
    const s6      = document.getElementById('senior6Doc' + studentId);
    const other   = document.getElementById('otherDoc' + studentId);

    wrapper.style.display = level ? '' : 'none';
    s3.style.display      = level === 'Senior 3' ? '' : 'none';
    s6.style.display      = level === 'Senior 6' ? '' : 'none';
    other.style.display   = level === 'Other'    ? '' : 'none';
}

// ─── Complete Registration ────────────────────────────────────────────────────
function handleCompleteRegistration(event, studentId) {
    event.preventDefault();

    const form      = document.getElementById('completeRegForm' + studentId);
    const submitBtn = document.getElementById('completeRegSubmit' + studentId);
    const formData  = new FormData(form);

    submitBtn.disabled    = true;
    submitBtn.innerHTML   = '<span class="spinner-border spinner-border-sm me-2"></span>Processing…';

    fetch(`/staff/students/${studentId}/complete-registration`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        // Always parse as JSON — the controller now guarantees this
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(
                document.getElementById('completeRegModal' + studentId)
            );
            modal.hide();
            showAlert('success', data.message || 'Registration completed successfully!');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            // Show field-level errors if present
            let msg = data.message || 'Registration completion failed.';
            if (data.errors) {
                msg += '<ul class="mb-0 mt-1">';
                Object.values(data.errors).forEach(errs => {
                    errs.forEach(e => { msg += `<li>${e}</li>`; });
                });
                msg += '</ul>';
            }
            showAlert('danger', msg);
            submitBtn.disabled  = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Complete Registration';
        }
    })
    .catch(error => {
        console.error('Complete registration error:', error);
        showAlert('danger', 'An unexpected error occurred. Please try again.');
        submitBtn.disabled  = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Complete Registration';
    });
}

// ─── Cash Payment ─────────────────────────────────────────────────────────────
function handleCashPayment(event, studentId) {
    event.preventDefault();

    const form      = document.getElementById('payForm' + studentId);
    const submitBtn = document.getElementById('paySubmit' + studentId);
    const formData  = new FormData(form);

    submitBtn.disabled  = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing…';

    fetch(`/staff/students/${studentId}/pay-cash`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const modal = bootstrap.Modal.getInstance(
            document.getElementById('payModal' + studentId)
        );
        if (data.success) {
            modal.hide();
            showAlert('success', data.message || 'Payment recorded successfully!');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            let msg = data.message || 'Payment failed.';
            if (data.errors) {
                msg += '<ul class="mb-0 mt-1">';
                Object.values(data.errors).forEach(errs => {
                    errs.forEach(e => { msg += `<li>${e}</li>`; });
                });
                msg += '</ul>';
            }
            showAlert('danger', msg);
            submitBtn.disabled  = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Record Payment';
        }
    })
    .catch(error => {
        console.error('Cash payment error:', error);
        showAlert('danger', 'An unexpected error occurred. Please try again.');
        submitBtn.disabled  = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Record Payment';
    });
}

// ─── Transcript lazy-load ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[id^="transcriptModal"]').forEach(modal => {
        modal.addEventListener('show.bs.modal', function () {
            const studentId = this.id.replace('transcriptModal', '');
            const body      = this.querySelector('.transcript-modal-body-' + studentId);

            // Only load once
            if (body.dataset.loaded) return;
            body.dataset.loaded = '1';

            fetch(`/students/${studentId}/transcript/ajax`)
                .then(r => r.text())
                .then(html => { body.innerHTML = html; })
                .catch(() => {
                    body.innerHTML = '<div class="alert alert-danger m-3">Failed to load transcript.</div>';
                });
        });
    });
});

// ─── Generate Student Card ───────────────────────────────────────────────────
function confirmGenerateCard(event, studentName) {
    event.preventDefault();
    
    if (!confirm(`Are you sure you want to generate the Student ID Card for "${studentName}"?`)) {
        return;
    }
    
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating...';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    // Make AJAX request
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new FormData(form),
        credentials: 'same-origin'
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        let data;
        
        if (contentType && contentType.includes('application/json')) {
            data = await response.json();
        } else {
            const text = await response.text();
            console.log('Non-JSON response:', text);
            try {
                data = JSON.parse(text);
            } catch (e) {
                data = { 
                    success: response.ok, 
                    message: response.ok ? 'Student card generated successfully' : 'Student card generation failed'
                };
            }
        }
        
        return { status: response.status, ok: response.ok, data };
    })
    .then(({ status, ok, data }) => {
        if (ok && data.success !== false) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.querySelector(`#generateCardModal${event.target.action.match(/\d+/)[0]}`));
            if (modal) modal.hide();
            
            // Show success message
            const message = data.message || 'Student card generated successfully';
            showAlert('success', message);
            
            // Reload page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            // Show error message
            const message = data.message || data.error || 'Failed to generate student card';
            showAlert('danger', message);
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-id-card me-1"></i>Generate ID Card';
        }
    })
    .catch(error => {
        console.error('Student card generation error:', error);
        showAlert('danger', 'Student card generation failed. Please check your connection and try again.');
        
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-id-card me-1"></i>Generate ID Card';
    });
}

// ─── Alert helper ─────────────────────────────────────────────────────────────
function showAlert(type, message) {
    const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    const title = type === 'success' ? 'Success' : (type === 'warning' ? 'Warning' : 'Error');

    const html = `
        <div class="alert alert-${type} alert-dismissible fade show alert-enhanced d-flex align-items-start" role="alert">
            <div class="alert-icon"><i class="fas fa-${icon}"></i></div>
            <div class="alert-content">
                <div class="alert-title">${title}</div>
                <div class="alert-message mb-0">${message}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;

    const container = document.querySelector('.alert-container');
    container.insertAdjacentHTML('afterbegin', html);

    // Auto-dismiss after 6 seconds
    setTimeout(() => {
        const a = container.querySelector('.alert');
        if (a) { a.classList.remove('show'); setTimeout(() => a.remove(), 300); }
    }, 6000);
}
</script>