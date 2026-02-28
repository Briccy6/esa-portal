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

                        $studentTransactions = \App\Models\StudentTransaction::where('student_id', $student->id)
                            ->whereIn('status', ['successful', 'paid'])
                            ->get();

                        $totalPaid = $studentTransactions->sum('amount');
                        $remaining = $totalToPay - $totalPaid;

                        // Check if registration is complete
                        $registrationComplete = $student->identification_type && $student->id_number && $student->id_photo && $student->passport_photo;
                    @endphp
                    <tr class="student-row">
                        <td class="fw-bold text-primary d-none d-md-table-cell">{{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}</td>
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

                    <!-- Payment Modal (same as before) -->
                    <div class="modal fade" id="payModal{{ $student->id }}" tabindex="-1">
                        <!-- Your existing payment modal content -->
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
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById(`completeRegModal${studentId}`));
            modal.hide();
            
            // Show success message
            showAlert('success', data.message);
            
            // Reload page after 2 seconds
            setTimeout(() => {
                window.location.reload();
            }, 2000);
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
    alertContainer.innerHTML = alertHtml;
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 300);
        }
    }, 5000);
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
</script>