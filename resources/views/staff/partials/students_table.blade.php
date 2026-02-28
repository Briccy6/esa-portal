
{{-- resources/views/staff/partials/students_table.blade.php --}}

<!-- Enhanced Alert Messages -->
<div class="alert-container">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show alert-enhanced d-flex align-items-start" role="alert">
            <div class="alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="alert-content">
                <div class="alert-title">Error</div>
                <p class="alert-message mb-0">{{ session('error') }}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show alert-enhanced d-flex align-items-start" role="alert">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="alert-content">
                <div class="alert-title">Success</div>
                <p class="alert-message mb-0">{{ session('success') }}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show alert-enhanced d-flex align-items-start" role="alert">
            <div class="alert-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
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

                        // Calculate fees
                        $registrationFee = $course ? floatval($course->registration_fee) : 0;
                        $tuitionFee = $course ? floatval($course->tuition_fee) : 0;
                        $practicalFee = $course ? floatval($course->practical_fee) : 0;
                        $tripFee = $course ? floatval($course->trip_fee) : 0;

                        $discountableFees = $tuitionFee + $practicalFee + $tripFee;
                        $discountAmount = ($discount > 0) ? ($discountableFees * ($discount / 100)) : 0;
                        $studentcard = 2000;
                        
                        $totalToPay = $studentcard + $registrationFee + ($discountableFees - $discountAmount);

                        // Get all transactions for this student
                        $studentTransactions = \App\Models\StudentTransaction::where('student_id', $student->id)
                            ->whereIn('status', ['successful', 'paid'])
                            ->get();

                        $totalPaid = $studentTransactions->sum('amount');
                        $remaining = $totalToPay - $totalPaid;

                        // Calculate already paid amounts by fee type
                        $paidRegistration = $studentTransactions->where('type', 'registration_fee')->sum('amount');
                        $paidSchoolFee = $studentTransactions->where('type', 'school_fee')->sum('amount');
                        $paidPractical = $studentTransactions->where('type', 'practical_fee')->sum('amount');
                        $paidTrip = $studentTransactions->where('type', 'trip_fee')->sum('amount');
                        $paidStudentCard = $studentTransactions->where('type', 'student_card')->sum('amount');
                        
                        // Calculate remaining amounts
                        $remainingRegistration = max(0, $registrationFee - $paidRegistration);
                        $remainingSchoolFee = max(0, $tuitionFee - $paidSchoolFee);
                        $remainingPractical = max(0, $practicalFee - $paidPractical);
                        $remainingTrip = max(0, $tripFee - $paidTrip);
                        $remainingStudentCard = max(0, 2000 - $paidStudentCard);

                        // Check if registration is complete
                        $registrationComplete = $student->identification_type && $student->id_number && $student->id_photo && $student->passport_photo;
                    @endphp
                    
                    <tr class="student-row">
                        <td class="fw-bold text-primary d-none d-md-table-cell">{{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}</td>
                        <td>
                            <div class="student-info">
                                <div class="student-avatar d-none d-sm-flex">
                                    @if($student->passport_photo)
                                        @php
                                            // Check if it's already base64 with data:image prefix
                                            if(strpos($student->passport_photo, 'data:image') === 0) {
                                                $photoSrc = $student->passport_photo;
                                            } else {
                                                // If it's base64 without prefix, add it
                                                $photoSrc = 'data:image/jpeg;base64,' . $student->passport_photo;
                                            }
                                        @endphp
                                        <img src="{{ $photoSrc }}" 
                                             alt="{{ $student->full_name }}" 
                                             class="avatar-img"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="avatar-placeholder" style="display: none;">
                                            {{ strtoupper(substr($student->full_name, 0, 2)) }}
                                        </div>
                                    @else
                                        <div class="avatar-placeholder">
                                            {{ strtoupper(substr($student->full_name, 0, 2)) }}
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
                            <span class="badge bg-primary">
                                {{ number_format($totalToPay, 0) }} RWF
                            </span>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <span class="badge bg-info">
                                {{ number_format($totalPaid, 0) }} RWF
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $remaining > 0 ? 'bg-warning text-dark' : 'bg-success' }}">
                                {{ number_format($remaining, 0) }} RWF
                            </span>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            @if($registrationComplete)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Complete
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-exclamation-triangle"></i> Incomplete
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group-vertical d-grid gap-1" role="group">
                                <!-- Edit Student Button -->
                                <a href="{{ route('staff.students.edit', $student) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   title="Edit Student Information">
                                    <i class="fas fa-edit me-1"></i> Edit Info
                                </a>

                                <!-- Complete Registration Button (if incomplete) -->
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

                    <!-- Complete Registration Modal -->
                    @if(!$registrationComplete)
                    <div class="modal fade" id="completeRegModal{{ $student->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title">
                                        <i class="fas fa-clipboard-check me-2"></i>
                                        Complete Registration for {{ $student->full_name }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form id="completeRegForm{{ $student->id }}" onsubmit="handleCompleteRegistration(event, {{ $student->id }})">
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Please complete the required information to finalize student registration.
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required">Identification Type</label>
                                                <select name="identification_type" class="form-select" required>
                                                    <option value="">Select Type</option>
                                                    <option value="ID" {{ $student->identification_type == 'ID' ? 'selected' : '' }}>National ID</option>
                                                    <option value="Passport" {{ $student->identification_type == 'Passport' ? 'selected' : '' }}>Passport</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required">ID/Passport Number</label>
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

                    <!-- Payment Modal -->
                    <div class="modal fade" id="payModal{{ $student->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Record Cash Payment for {{ $student->full_name }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                
                                <form id="payForm{{ $student->id }}" 
                                      action="{{ route('staff.students.pay-cash', $student) }}" 
                                      method="POST"
                                      onsubmit="handlePayment(event, {{ $student->id }})">
                                    @csrf
                                    
                                    <div class="modal-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Payment Summary:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Total Fees: {{ number_format($totalToPay, 0) }} RWF</li>
                                                <li>Total Paid: {{ number_format($totalPaid, 0) }} RWF</li>
                                                <li>Balance: <strong>{{ number_format($remaining, 0) }} RWF</strong></li>
                                                @if($discount > 0)
                                                    <li class="text-success">Discount Applied: {{ $discount }}% ({{ number_format($discountAmount, 0) }} RWF)</li>
                                                @endif
                                            </ul>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label required">Fee Type</label>
                                                <select name="fee_type" class="form-select fee-type-select" required onchange="updateFeeInfo(this, {{ $student->id }})">
                                                    <option value="">Select Fee Type</option>
                                                    
                                                    @if($remainingRegistration > 0)
                                                    <option value="registration_fee" 
                                                            data-total="{{ $registrationFee }}" 
                                                            data-paid="{{ $paidRegistration }}"
                                                            data-remaining="{{ $remainingRegistration }}">
                                                        Registration Fee - Remaining: {{ number_format($remainingRegistration, 0) }} RWF
                                                    </option>
                                                    @endif
                                                    
                                                    @if($remainingSchoolFee > 0)
                                                    <option value="school_fee" 
                                                            data-total="{{ $tuitionFee }}" 
                                                            data-paid="{{ $paidSchoolFee }}"
                                                            data-remaining="{{ $remainingSchoolFee }}">
                                                        School/Tuition Fee - Remaining: {{ number_format($remainingSchoolFee, 0) }} RWF
                                                    </option>
                                                    @endif
                                                    
                                                    @if($remainingPractical > 0)
                                                    <option value="practical_fee" 
                                                            data-total="{{ $practicalFee }}" 
                                                            data-paid="{{ $paidPractical }}"
                                                            data-remaining="{{ $remainingPractical }}">
                                                        Practical Fee - Remaining: {{ number_format($remainingPractical, 0) }} RWF
                                                    </option>
                                                    @endif
                                                    
                                                    @if($remainingTrip > 0)
                                                    <option value="trip_fee" 
                                                            data-total="{{ $tripFee }}" 
                                                            data-paid="{{ $paidTrip }}"
                                                            data-remaining="{{ $remainingTrip }}">
                                                        Trip Fee - Remaining: {{ number_format($remainingTrip, 0) }} RWF
                                                    </option>
                                                    @endif
                                                    
                                                    @if($remainingStudentCard > 0)
                                                    <option value="student_card" 
                                                            data-total="2000" 
                                                            data-paid="{{ $paidStudentCard }}"
                                                            data-remaining="{{ $remainingStudentCard }}">
                                                        Student Card - Remaining: {{ number_format($remainingStudentCard, 0) }} RWF
                                                    </option>
                                                    @endif
                                                </select>
                                                
                                                @if($remainingRegistration == 0 && $remainingSchoolFee == 0 && $remainingPractical == 0 && $remainingTrip == 0 && $remainingStudentCard == 0)
                                                    <div class="alert alert-success mt-2 mb-0">
                                                        <i class="fas fa-check-circle me-2"></i>
                                                        All fees have been fully paid!
                                                    </div>
                                                @endif
                                                
                                                <div id="feeInfo{{ $student->id }}" class="mt-2"></div>
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label class="form-label required">Amount to Pay (RWF)</label>
                                                <input type="number" 
                                                       name="amount_paid" 
                                                       id="amountInput{{ $student->id }}"
                                                       class="form-control form-control-lg" 
                                                       placeholder="Enter amount"
                                                       min="1"
                                                       step="1"
                                                       required>
                                                <small class="text-muted">Enter the exact amount received from the student</small>
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Payment Notes (Optional)</label>
                                                <textarea name="payment_notes" 
                                                          class="form-control" 
                                                          rows="2" 
                                                          placeholder="Add any additional notes about this payment"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn{{ $student->id }}">
                                            <i class="fas fa-check me-1"></i>Record Payment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Transcript Modal -->
                    <div class="modal fade" id="transcriptModal{{ $student->id }}" tabindex="-1">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title">Payment Transcript - {{ $student->full_name }}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body transcript-modal-body">
                                    <div class="text-center">
                                        <span class="spinner-border"></span> Loading...
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
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

.student-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.student-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 18px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.student-details {
    flex: 1;
    min-width: 0;
}

.student-name {
    font-weight: 600;
    color: #2c3e50;
}

.card {
    border-radius: 0.5rem;
}

.progress {
    border-radius: 0.5rem;
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
// Handle Complete Registration Form
function handleCompleteRegistration(event, studentId) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    
    fetch(`/staff/students/${studentId}/complete-registration`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById(`completeRegModal${studentId}`));
            modal.hide();
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showAlert('danger', data.message || 'Registration completion failed');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Complete Registration';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Complete Registration';
    });
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show alert-enhanced d-flex align-items-start" role="alert">
            <div class="alert-icon">
                <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
            </div>
            <div class="alert-content">
                <div class="alert-title">${type === 'success' ? 'Success' : 'Error'}</div>
                <p class="alert-message mb-0">${message}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const alertContainer = document.querySelector('.alert-container');
    if (alertContainer) {
        alertContainer.innerHTML = alertHtml;
        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 300);
            }
        }, 5000);
    }
}

// Load transcript when modal opens
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="transcriptModal"]').forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            const studentId = this.id.replace('transcriptModal', '');
            const modalBody = this.querySelector('.transcript-modal-body');
            
            fetch(`/students/${studentId}/transcript/ajax`)
                .then(response => response.text())
                .then(html => {
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<div class="alert alert-danger">Failed to load transcript</div>';
                });
        });
    });
});

function updateFeeInfo(selectElement, studentId) {
    const option = selectElement.options[selectElement.selectedIndex];
    const total = parseFloat(option.getAttribute('data-total') || 0);
    const paid = parseFloat(option.getAttribute('data-paid') || 0);
    const remaining = parseFloat(option.getAttribute('data-remaining') || 0);
    
    const infoDiv = document.getElementById(`feeInfo${studentId}`);
    const amountInput = document.getElementById(`amountInput${studentId}`);
    
    if (selectElement.value && remaining > 0) {
        infoDiv.innerHTML = `
            <div class="card border-${remaining > 0 ? 'warning' : 'success'}">
                <div class="card-body py-2">
                    <h6 class="card-title mb-2">
                        <i class="fas fa-info-circle text-primary me-1"></i>
                        Fee Breakdown
                    </h6>
                    <div class="row g-2 small">
                        <div class="col-4">
                            <div class="text-muted">Total Fee:</div>
                            <strong>${total.toLocaleString()} RWF</strong>
                        </div>
                        <div class="col-4">
                            <div class="text-muted">Already Paid:</div>
                            <strong class="text-success">${paid.toLocaleString()} RWF</strong>
                        </div>
                        <div class="col-4">
                            <div class="text-muted">Remaining:</div>
                            <strong class="text-danger">${remaining.toLocaleString()} RWF</strong>
                        </div>
                    </div>
                    ${paid > 0 ? `
                        <div class="progress mt-2" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: ${(paid/total*100).toFixed(1)}%"
                                 aria-valuenow="${paid}" aria-valuemin="0" aria-valuemax="${total}">
                                ${(paid/total*100).toFixed(1)}% Paid
                            </div>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        if (amountInput) {
            amountInput.max = remaining;
            amountInput.value = remaining;
            amountInput.placeholder = `Max: ${remaining.toLocaleString()} RWF`;
            amountInput.disabled = false;
        }
    } else if (selectElement.value && remaining <= 0) {
        infoDiv.innerHTML = `
            <div class="alert alert-success mb-0">
                <i class="fas fa-check-circle me-2"></i>
                This fee has been fully paid!
            </div>
        `;
        if (amountInput) {
            amountInput.value = '';
            amountInput.disabled = true;
        }
    } else {
        infoDiv.innerHTML = '';
        if (amountInput) {
            amountInput.disabled = false;
            amountInput.value = '';
            amountInput.placeholder = 'Enter amount';
        }
    }
}

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta && meta.content) return meta.content;
    // fallback to hidden _token input in form
    const tokenInput = document.querySelector('input[name="_token"]');
    return tokenInput ? tokenInput.value : null;
}
// Replace the handlePayment function with this fixed version:

function handlePayment(event, studentId) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById(`submitBtn${studentId}`);
    
    const feeType = formData.get('fee_type');
    const amount = parseFloat(formData.get('amount_paid'));

    // Validation
    if (!feeType) {
        showAlert('danger', 'Please select a fee type');
        return;
    }

    if (!amount || amount <= 0) {
        showAlert('danger', 'Please enter a valid amount');
        return;
    }

    // FIX: Try multiple ways to find the select element
    let selectElement = form.querySelector('select[name="fee_type"]');
    
    if (!selectElement) {
        // Try finding by modal ID
        const modal = document.getElementById(`payModal${studentId}`);
        if (modal) {
            selectElement = modal.querySelector('select[name="fee_type"]');
        }
    }
    
    if (!selectElement) {
        // Try finding globally within the form
        selectElement = document.querySelector(`form[id*="${studentId}"] select[name="fee_type"]`);
    }
    
    // DEBUG: Log what we found
    console.log('Form:', form);
    console.log('Student ID:', studentId);
    console.log('Select Element:', selectElement);
    console.log('Fee Type Value:', feeType);
    
    // If we still can't find it, proceed without validation but log warning
    if (!selectElement) {
        console.warn('Could not find fee type select element, proceeding without remaining balance check');
        
        // Skip the remaining balance check and proceed with payment
        const feeName = feeType.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        
        if (!confirm(`Confirm payment of ${amount.toLocaleString()} RWF for ${feeName}?`)) {
            return;
        }
    } else {

    // Get remaining amount for selected fee
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    // FIX: Add null check for selected option
    if (!selectedOption) {
        showAlert('danger', 'Please select a valid fee type');
        return;
    }
    
    const remaining = parseFloat(selectedOption.getAttribute('data-remaining') || 0);

    if (amount > remaining) {
        showAlert('danger', `Amount exceeds remaining balance of ${remaining.toLocaleString()} RWF`);
        return;
    }

    const feeName = selectedOption.text.split(' - ')[0];
    if (!confirm(`Confirm payment of ${amount.toLocaleString()} RWF for ${feeName}?`)) {
        return;
    }

    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

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
        body: formData,
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
                // If we can't parse JSON, treat as success if status is 2xx
                data = { 
                    success: response.ok, 
                    message: response.ok ? 'Payment recorded successfully' : 'Payment failed'
                };
            }
        }
        
        return { status: response.status, ok: response.ok, data };
    })
    .then(({ status, ok, data }) => {
        if (ok && data.success !== false) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById(`payModal${studentId}`));
            if (modal) modal.hide();
            
            // Show success message
            const message = data.message || 'Payment recorded successfully';
            showAlert('success', message);
            
            // Reload page after short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            // Show error message
            const message = data.message || data.error || 'Payment failed. Please try again.';
            showAlert('danger', message);
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Record Payment';
        }
    })
    .catch(error => {
        console.error('Payment error:', error);
        showAlert('danger', 'Payment failed. Please check your connection and try again.');
        
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Record Payment';
    });
}

// Enhanced showAlert function with better positioning
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show alert-enhanced d-flex align-items-start" role="alert">
            <div class="alert-icon">
                <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
            </div>
            <div class="alert-content">
                <div class="alert-title">${type === 'success' ? 'Success' : 'Error'}</div>
                <p class="alert-message mb-0">${message}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    let alertContainer = document.querySelector('.alert-container');
    
    // Create alert container if it doesn't exist
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.className = 'alert-container';
        document.body.insertBefore(alertContainer, document.body.firstChild);
    }
    
    alertContainer.innerHTML = alertHtml;
    
    // Scroll to top to show alert
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 300);
        }
    }, 5000);
}

</script>