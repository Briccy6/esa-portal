@extends('admin.layout')

@push('styles')
    @include('admin.report-cards.partials.layout-fixes')
@endpush

@section('content')
<div class="content">
    <div class="report-container">
        <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-file-alt me-2"></i>Generate New Report Card</h2>
            <p class="text-muted">Create report cards for students</p>
        </div>
        <a href="{{ route('admin.report-cards.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Report Cards
        </a>
    </div>

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle me-1"></i> Please fix the following errors:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Report Card Details</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.report-cards.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="student_id">Student *</label>
                            <select name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->full_name }} - {{ $student->reg_number ?? 'No Reg No' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="course_id">Course *</label>
                            <select name="course_id" id="course_id" class="form-control @error('course_id') is-invalid @enderror" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                                </select>
                                @error('course_id')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="academic_year_id">Academic Year *</label>
                                <select name="academic_year_id" id="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">Select Academic Year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                            {{ $year->year }}
                                            @if($year->is_ongoing) <span class="badge badge-success">Current</span> @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="term_ids">Term(s) * <small class="text-muted">(one report card per term)</small></label>
                                <select name="term_ids[]" id="term_ids" class="form-control @error('term_ids') is-invalid @enderror" multiple size="10" required>
                                    @forelse($terms->groupBy('academic_year_id') as $yearId => $yearTerms)
                                        @php
                                            $ogLabel = optional($yearTerms->first()->academicYear)->year ?? ('Year #'.$yearId);
                                        @endphp
                                        <optgroup label="{{ $ogLabel }}">
                                            @foreach($yearTerms as $term)
                                                <option value="{{ $term->id }}" {{ collect(old('term_ids', []))->contains($term->id) ? 'selected' : '' }}>
                                                    {{ $term->name ?: 'Term #'.$term->id }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @empty
                                        <option value="" disabled>No terms found — create terms under Academic Years first</option>
                                    @endforelse
                                </select>
                                <small class="form-text text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple terms. Each term generates a separate report card.</small>
                                @error('term_ids')
                                    <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>System Information</label>
                                <div class="alert alert-info mb-2">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        The system will automatically fetch all exam and test marks for the selected student and academic period.
                                    </small>
                                </div>
                                <div class="alert alert-warning mb-0">
                                    <small>
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Important:</strong> Students with outstanding fees will be rejected. 
                                        <a href="{{ route('admin.report-cards.outstanding-fees') }}" class="alert-link">View outstanding fees</a>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <!-- For Single Student Generation -->
                                <button type="submit" id="singleGenBtn" class="btn btn-primary" style="display: none;">
                                    <i class="fas fa-plus"></i> Generate Report Card for Student
                                </button>

                                <!-- For Class Generation - Check Fees First (MANDATORY) -->
                                <button type="button" id="checkClassFeesBtn" class="btn btn-info" onclick="checkClassFees()" style="display: none;">
                                    <i class="fas fa-search"></i> Step 1: Check Class Fees Before Generating
                                </button>

                                <!-- Legacy button -->
                                <button type="button" id="checkFeesBtn" class="btn btn-warning" style="display: none;">
                                    <i class="fas fa-search"></i> Check Outstanding Fees
                                </button>

                                <a href="{{ route('admin.report-cards.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
        </div>
    </div>
</div>

<!-- Outstanding Fees Modal -->
<div class="modal fade" id="outstandingFeesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-circle me-2"></i>Students with Outstanding Fees
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="feesModalBody">
                <div class="text-center">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="proceedAnywayBtn">Proceed Anyway</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-select course when student is selected
    $('#student_id').change(function() {
        var studentId = $(this).val();
        if (studentId) {
            // Get student info and auto-fill course
            $.get('/api/students/' + studentId + '/course', function(data) {
                if (data.course_id) {
                    $('#course_id').val(data.course_id);
                }
                if (data.academic_year_id) {
                    $('#academic_year_id').val(data.academic_year_id);
                }
            });
        }
    });

    // Check Outstanding Fees
    $('#checkFeesBtn').click(function(e) {
        e.preventDefault();
        
        const courseId = $('#course_id').val();
        const academicYearId = $('#academic_year_id').val();
        const termIds = $('#term_ids').val();

        if (!courseId || !academicYearId) {
            alert('Please select Course and Academic Year first');
            return;
        }

        if (!termIds || termIds.length === 0) {
            alert('Please select at least one Term');
            return;
        }

        // Show modal with loading spinner
        const modal = new bootstrap.Modal(document.getElementById('outstandingFeesModal'));
        modal.show();

        // Call API to check fees
        $.ajax({
            url: '{{ route("admin.report-cards.check-fees-for-generation") }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                course_id: courseId,
                academic_year_id: academicYearId,
                term_ids: termIds
            },
            success: function(response) {
                if (response.success && response.has_outstanding_fees) {
                    // Build HTML for fees table
                    let html = `
                        <div class="alert alert-danger">
                            <strong><i class="fas fa-warning me-2"></i>Warning:</strong> 
                            ${response.count} student(s) have outstanding fees in this course for the selected terms.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-danger">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Student ID</th>
                                        <th>Outstanding</th>
                                        <th>Required</th>
                                        <th>Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    response.students.forEach(function(student) {
                        // Format currency values with proper thousand separators and 2 decimal places
                        const formatCurrencyValue = (amount) => {
                            return new Intl.NumberFormat('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                                useGrouping: true
                            }).format(amount);
                        };
                        
                        html += `
                            <tr>
                                <td><strong>${student.full_name}</strong></td>
                                <td>${student.student_id}</td>
                                <td><span class="badge bg-danger">RWF ${formatCurrencyValue(student.outstanding_amount)}</span></td>
                                <td>RWF ${formatCurrencyValue(student.required_fees)}</td>
                                <td><span class="badge bg-success">RWF ${formatCurrencyValue(student.total_paid)}</span></td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mt-3">
                            <strong><i class="fas fa-info-circle me-2"></i>Note:</strong>
                            These students cannot have report cards generated until they pay their outstanding fees.
                            You can:
                            <ul class="mb-0 mt-2">
                                <li>Send them payment reminders (see Outstanding Fees Management)</li>
                                <li>Wait for them to pay</li>
                                <li>Proceed to generate report cards for other students</li>
                            </ul>
                        </div>
                    `;

                    $('#feesModalBody').html(html);
                } else if (response.success) {
                    // No outstanding fees
                    let html = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Great news!</strong> No students have outstanding fees in this course for the selected terms.
                            All students are eligible for report card generation.
                        </div>
                    `;
                    $('#feesModalBody').html(html);
                    $('#proceedAnywayBtn').hide();
                } else {
                    $('#feesModalBody').html(`
                        <div class="alert alert-danger">
                            <strong>Error:</strong> ${response.message || 'Failed to check fees'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                let errorMsg = 'An error occurred while checking fees';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#feesModalBody').html(`
                    <div class="alert alert-danger">
                        <strong>Error:</strong> ${errorMsg}
                    </div>
                `);
            }
        });
    });

    // Proceed Anyway button
    $('#proceedAnywayBtn').click(function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('outstandingFeesModal'));
        modal.hide();
        $('form').submit();
    });

    // Toggle buttons based on student selection
    updateButtonDisplay();
    $('#student_id').change(updateButtonDisplay);

    function updateButtonDisplay() {
        const studentId = document.getElementById('student_id').value;
        const courseId = document.getElementById('course_id').value;
        const yearId = document.getElementById('academic_year_id').value;
        const termIds = Array.from(document.getElementById('term_ids').selectedOptions).map(o => o.value);

        if (studentId) {
            // Single student mode
            document.getElementById('singleGenBtn').style.display = 'inline-block';
            document.getElementById('checkClassFeesBtn').style.display = 'none';
            document.getElementById('checkFeesBtn').style.display = 'none';
        } else if (courseId && yearId && termIds.length > 0) {
            // Class generation mode
            document.getElementById('singleGenBtn').style.display = 'none';
            document.getElementById('checkClassFeesBtn').style.display = 'inline-block';
            document.getElementById('checkFeesBtn').style.display = 'none';
        } else {
            document.getElementById('singleGenBtn').style.display = 'none';
            document.getElementById('checkClassFeesBtn').style.display = 'none';
            document.getElementById('checkFeesBtn').style.display = 'none';
        }
    }

    document.getElementById('course_id').addEventListener('change', updateButtonDisplay);
    document.getElementById('academic_year_id').addEventListener('change', updateButtonDisplay);
    document.getElementById('term_ids').addEventListener('change', updateButtonDisplay);
});

function checkClassFees() {
    const courseId = document.getElementById('course_id').value;
    const yearId = document.getElementById('academic_year_id').value;
    const termIds = Array.from(document.getElementById('term_ids').selectedOptions).map(o => o.value);

    if (!courseId || !yearId || termIds.length === 0) {
        alert('Please select Course, Academic Year, and at least one Term');
        return;
    }

    // Submit to the check-class-fees endpoint
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.report-cards.check-class-fees") }}';

    const csrfToken = document.querySelector('input[name="_token"]').value;
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="course_id" value="${courseId}">
        <input type="hidden" name="academic_year_id" value="${yearId}">
        ${termIds.map(id => `<input type="hidden" name="term_ids[]" value="${id}">`).join('')}
    `;

    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
