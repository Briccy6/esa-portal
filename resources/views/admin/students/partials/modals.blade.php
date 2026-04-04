<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="delete-icon mb-3">
                    <i class="bi bi-trash3 display-4 text-danger"></i>
                </div>
                <h6>Are you sure you want to delete this student?</h6>
                <p class="text-muted mb-0">
                    Student: <strong id="studentName"></strong><br>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete Student
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Show Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-key me-2"></i>Student Credentials</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <label class="form-label fw-bold">Registration Number</label>
                    <div id="modalRegNumber" class="form-control text-center mb-2" readonly></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <input type="text" id="modalPassword" class="form-control text-center" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility()">
                            <i id="togglePasswordIcon" class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Discount Modal -->
<div class="modal fade" id="discountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="discountForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-percent me-2"></i>Update Student Discount</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Registration Number</label>
                        <div id="discountRegNumber" class="form-control" readonly></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <div id="discountFullName" class="form-control" readonly></div>
                    </div>
                    <div class="mb-3">
                        <label for="discountInput" class="form-label fw-bold">Discount (%)</label>
                        <input type="number" min="0" max="100" step="0.01" class="form-control" id="discountInput" name="discount" required>
                        <small class="form-text text-muted">Enter a value between 0 and 100</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Update Discount</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Transcript Modal -->
<div class="modal fade" id="transcriptModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="bi bi-receipt me-2"></i>Student School Fees Transcript
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transcriptModalBody">
                <div class="text-center">
                    <span class="spinner-border"></span> Loading...
                </div>
            </div>
            <div class="modal-footer">
                <div>
                    <small class="text-muted">Generated by: <strong>{{ Auth::user()->name ?? 'Admin' }}</strong></small>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Edit Modal (Only for authorized staff) -->
@if(auth('staff')->check() && (str_ends_with(auth('staff')->user()->email, '@excellentservantacademy.com') || auth('staff')->user()->email === 'hirwabran9@gmail.com'))
<div class="modal fade" id="editTransactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Transaction
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTransactionForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_transaction_id" name="transaction_id">
                
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Editing transactions will affect student balances and reports.
                    </div>

                    <div class="mb-3">
                        <label for="edit_amount" class="form-label">Amount (RWF)</label>
                        <input type="number" class="form-control" id="edit_amount" name="amount" step="0.01" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_type" class="form-label">Fee Type</label>
                        <select class="form-select" id="edit_type" name="type" required>
                            <option value="registration_fee">Registration Fee</option>
                            <option value="school_fee">School Fee (Tuition)</option>
                            <option value="practical_fee">Practical Fee</option>
                            <option value="trip_fee">Trip Fee</option>
                            <option value="student_card">Student Card</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_term" class="form-label">Term</label>
                        <select class="form-select" id="edit_term" name="term_id">
                            <option value="">-- No Term --</option>
                            @foreach(\App\Models\Term::all() as $term)
                                <option value="{{ $term->id }}">{{ $term->name }} - {{ $term->academicYear->year ?? '' }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Optional: Select the term this fee applies to</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="successful">Successful</option>
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_paid_at" class="form-label">Payment Date</label>
                        <input type="datetime-local" class="form-control" id="edit_paid_at" name="paid_at">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Update Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
    /* Edit Transaction Modal styles */
    #editTransactionModal .modal-header {
        border-bottom: 2px solid #ffc107;
    }

    #editTransactionModal .modal-body {
        padding: 2rem;
    }

    #editTransactionModal .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    #editTransactionModal .form-control,
    #editTransactionModal .form-select {
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        padding: 0.75rem;
    }

    #editTransactionModal .form-control:focus,
    #editTransactionModal .form-select:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }

    #editTransactionModal .mb-3 {
        margin-bottom: 1.5rem;
    }

    #editTransactionModal .alert {
        border-radius: 0.375rem;
    }

    /* Modal responsive styles */
    .modal-dialog {
        margin: 1rem;
    }

    @media (min-width: 576px) {
        .modal-dialog {
            max-width: 500px;
            margin: 1.75rem auto;
        }

        #editTransactionModal .modal-dialog {
            max-width: 600px;
        }

        .modal-dialog-centered {
            min-height: calc(100% - 3.5rem);
        }
    }

    @media (min-width: 992px) {
        .modal-xl {
            max-width: 1140px;
        }
    }

    @media (max-width: 576px) {
        .modal-dialog-centered {
            min-height: calc(100% - 1rem);
        }

        .modal-content {
            border-radius: 0.5rem;
        }

        .modal-header {
            padding: 1rem;
        }

        .modal-body {
            padding: 1rem;
        }

        .modal-footer {
            padding: 0.75rem;
        }

        .modal-title {
            font-size: 1rem;
        }
    }

    .delete-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .form-control[readonly] {
        background-color: #f8f9fa;
    }

    /* Ensure modals are scrollable on small screens */
    .modal-dialog-scrollable .modal-body {
        overflow-y: auto;
    }
</style>