@extends('admin.layout')

@section('title', 'Assign Course Fees')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-money-bill-wave me-2"></i>Assign Fees - {{ $course->course_name }}</h2>
            <p class="text-muted">Set up fee structure for different terms</p>
        </div>
        <a href="{{ route('admin.course.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Courses
        </a>
    </div>

    <!-- Course Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Course Code:</strong> {{ $course->course_code }}
                </div>
                <div class="col-md-4">
                    <strong>Course Type:</strong> {{ $course->course_type }}
                </div>
                <div class="col-md-4">
                    <strong>Branches:</strong> {{ implode(', ', $course->branches->pluck('name')->toArray()) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

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

    <!-- Existing Fees Display -->
    @if($existingTermFees->isNotEmpty() || $existingBranchFees->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Existing Fee Structure</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Below are the currently active fees for this course. You can add new fees or update existing ones using the form below.
                <strong>Important Notes:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>Registration Fee:</strong> Paid once per academic year (taken from first term's fee)</li>
                    <li><strong>Student Card Fee:</strong> Paid once per academic year (taken from first term's fee)</li>
                    <li><strong>Tuition, Practical, Trip Fees:</strong> Paid per term as configured</li>
                </ul>
            </div>

            @if($existingTermFees->isNotEmpty())
            <h6 class="mb-3">General Term Fees</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Academic Year</th>
                            <th>Term</th>
                            <th>Registration Fee</th>
                            <th>Tuition Fee</th>
                            <th>Practical Fee</th>
                            <th>Trip Fee</th>
                            <th>Student Card Fee</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($existingTermFees as $termId => $fee)
                        <tr>
                            <td>{{ $fee->term->academicYear->year ?? 'N/A' }}</td>
                            <td>{{ $fee->term->name ?? 'N/A' }}</td>
                            <td>{{ number_format($fee->registration_fee, 0) }} RWF</td>
                            <td>{{ number_format($fee->tuition_fee, 0) }} RWF</td>
                            <td>{{ number_format($fee->practical_fee, 0) }} RWF</td>
                            <td>{{ number_format($fee->trip_fee, 0) }} RWF</td>
                            <td>{{ number_format($fee->student_card_fee, 0) }} RWF</td>
                            <td><strong>{{ number_format($fee->total_fees, 0) }} RWF</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            @if($existingBranchFees->isNotEmpty())
            <h6 class="mb-3">Branch-Specific Term Fees</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Academic Year</th>
                            <th>Term</th>
                            <th>Branch</th>
                            <th>Registration Fee</th>
                            <th>Tuition Fee</th>
                            <th>Practical Fee</th>
                            <th>Trip Fee</th>
                            <th>Student Card Fee</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($existingBranchFees as $termId => $branchFees)
                            @foreach($branchFees as $branchFee)
                            <tr>
                                <td>{{ $branchFee->term->academicYear->year ?? 'N/A' }}</td>
                                <td>{{ $branchFee->term->name ?? 'N/A' }}</td>
                                <td>{{ $branchFee->branch->name ?? 'N/A' }}</td>
                                <td>{{ number_format($branchFee->registration_fee, 0) }} RWF</td>
                                <td>{{ number_format($branchFee->tuition_fee, 0) }} RWF</td>
                                <td>{{ number_format($branchFee->practical_fee, 0) }} RWF</td>
                                <td>{{ number_format($branchFee->trip_fee, 0) }} RWF</td>
                                <td>{{ number_format($branchFee->student_card_fee, 0) }} RWF</td>
                                <td><strong>{{ number_format($branchFee->total_fees, 0) }} RWF</strong></td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="alert alert-warning mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        No fees have been set for this course yet. Use the form below to add fees.
    </div>
    @endif

    <!-- Fee Assignment Form -->
    <form action="{{ route('admin.course.store-fees', $course->id) }}" method="POST" class="bg-white p-4 rounded shadow-sm">
        @csrf

        <div class="mb-4">
            <label class="form-label">Fee Structure by Term <span class="text-danger">*</span></label>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Set up fees for each term this course will be active. You can configure different fees for different terms.
            </div>
            <div class="alert alert-secondary small mb-3">
                Leave the general term fee cells blank if you are using only branch-specific fees for a term.
            </div>
            
            <div id="term-fees-container">
                <div class="table-responsive">
                    <table class="table table-bordered" id="term-fees-table">
                        <thead class="table-light">
                            <tr>
                                <th width="30%">Academic Term</th>
                                <th width="15%">Registration</th>
                                <th width="15%">Tuition</th>
                                <th width="15%">Practical</th>
                                <th width="15%">Trip</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="term-fee-row-template" style="display: none;" disabled>
                                <td>
                                    <select name="term_fees[template][term_id]" class="form-select term-select" disabled>
                                        <option value="">Select Term *</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->id }}">
                                                {{ $term->name }} - {{ $term->academicYear->year ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="term_fees[template][registration_fee]" 
                                           class="form-control" min="0" step="0.01" value="0" disabled>
                                </td>
                                <td>
                                    <input type="number" name="term_fees[template][tuition_fee]" 
                                           class="form-control" min="0" step="0.01" value="0" disabled>
                                </td>
                                <td>
                                    <input type="number" name="term_fees[template][practical_fee]" 
                                           class="form-control" min="0" step="0.01" value="0" disabled>
                                </td>
                                <td>
                                    <input type="number" name="term_fees[template][trip_fee]" 
                                           class="form-control" min="0" step="0.01" value="0" disabled>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-term-fee" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addTermFeeRow()">
                    <i class="fas fa-plus me-1"></i> Add Term Fee
                </button>
                
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Total Year Fees:</label>
                            <input type="text" id="total_year_fees" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Student Card Fee (per academic year):</label>
                            <input type="number" name="student_card_fee" class="form-control" value="2000" readonly>
                            <small class="text-muted">Fixed fee paid once per academic year</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Assign Fees
            </button>
            <a href="{{ route('admin.course.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
    // ✅ FIX 1: Collect terms from Blade into a JS array ONCE at page load
    const termsData = [
        @foreach($terms as $term)
        { id: '{{ $term->id }}', label: '{{ $term->name }} - {{ $term->academicYear->year ?? '' }}' },
        @endforeach
    ];

    // Course branches available for this course
    const courseBranches = [
        @foreach($course->branches as $branch)
        { id: '{{ $branch->id }}', name: '{{ addslashes($branch->name) }}' },
        @endforeach
    ];

    let termFeeRowIndex = 0;
    const usedTerms = new Set();

    // Pre-populate form with existing fees
    @php
    $existingFeesData = [];
    if(isset($existingTermFees)) {
        foreach($existingTermFees as $termId => $fee) {
            $existingFeesData[] = [
                'term_id' => $termId,
                'registration_fee' => $fee->registration_fee,
                'tuition_fee' => $fee->tuition_fee,
                'practical_fee' => $fee->practical_fee,
                'trip_fee' => $fee->trip_fee,
                'is_branch_specific' => false
            ];
        }
    }
    if(isset($existingBranchFees)) {
        foreach($existingBranchFees as $termId => $branchFees) {
            foreach($branchFees as $branchFee) {
                $existingFeesData[] = [
                    'term_id' => $termId,
                    'branch_id' => $branchFee->branch_id,
                    'registration_fee' => $branchFee->registration_fee,
                    'tuition_fee' => $branchFee->tuition_fee,
                    'practical_fee' => $branchFee->practical_fee,
                    'trip_fee' => $branchFee->trip_fee,
                    'is_branch_specific' => true
                ];
            }
        }
    }
    @endphp

    const existingFees = @json(isset($existingFeesData) ? $existingFeesData : []);

    // Load existing fees into the form on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (existingFees && existingFees.length > 0) {
            existingFees.forEach(fee => {
                addTermFeeRow();
                const lastRow = document.querySelector('#term-fees-table tbody tr:last-of-type');
                if (lastRow) {
                    const termSelect = lastRow.querySelector('.term-select');
                    termSelect.value = fee.term_id;
                    
                    // Set fee values
                    lastRow.querySelector('input[name$="[registration_fee]"]').value = fee.registration_fee || '';
                    lastRow.querySelector('input[name$="[tuition_fee]"]').value = fee.tuition_fee || '';
                    lastRow.querySelector('input[name$="[practical_fee]"]').value = fee.practical_fee || '';
                    lastRow.querySelector('input[name$="[trip_fee]"]').value = fee.trip_fee || '';

                    // If branch-specific, populate branch fees
                    if (fee.is_branch_specific && fee.branch_id) {
                        const branchBtn = lastRow.querySelector('.branch-fees-btn');
                        if (branchBtn) {
                            branchBtn.click();
                            setTimeout(() => {
                                const branchRow = lastRow.nextElementSibling;
                                if (branchRow && branchRow.classList.contains('branch-fees-row')) {
                                    const branchInput = branchRow.querySelector(`input[name*="[${fee.branch_id}][registration_fee]"]`);
                                    if (branchInput) {
                                        const branchInputs = branchRow.querySelectorAll(`input[name*="[${fee.branch_id}]"]`);
                                        branchInputs.forEach(input => {
                                            const fieldName = input.name.match(/\[(\w+)\]$/);
                                            if (fieldName) {
                                                const feeType = fieldName[1];
                                                if (fee[feeType] !== undefined) {
                                                    input.value = fee[feeType];
                                                }
                                            }
                                        });
                                    }
                                }
                            }, 100);
                        }
                    }
                    
                    updateUsedTerms();
                }
            });
            calculateTotalYearFees();
        }
    });

    function buildSelectOptions(currentValue) {
        let html = '<option value="">Select Term *</option>';
        termsData.forEach(term => {
            const disabled = usedTerms.has(term.id) && term.id !== currentValue ? 'disabled' : '';
            const selected  = term.id === currentValue ? 'selected' : '';
            html += `<option value="${term.id}" ${disabled} ${selected}>${term.label}</option>`;
        });
        return html;
    }

    function addTermFeeRow() {
        const tbody = document.querySelector('#term-fees-table tbody');
        const index  = termFeeRowIndex++;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="term_fees[${index}][term_id]" class="form-select term-select" required>
                    <option value="">Select Term *</option>
                    ${buildSelectOptions('')}
                </select>
            </td>
            <td><input type="number" name="term_fees[${index}][registration_fee]" class="form-control" min="0" step="0.01" value="" placeholder="Registration fee"></td>
            <td><input type="number" name="term_fees[${index}][tuition_fee]" class="form-control" min="0" step="0.01" value="" placeholder="Tuition fee"></td>
            <td><input type="number" name="term_fees[${index}][practical_fee]" class="form-control" min="0" step="0.01" value="" placeholder="Practical fee"></td>
            <td><input type="number" name="term_fees[${index}][trip_fee]" class="form-control" min="0" step="0.01" value="" placeholder="Trip fee"></td>
            <td>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary branch-fees-btn" title="Set branch-specific fees">
                            <i class="fas fa-code-branch"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger remove-term-fee">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="text-muted small">Optional: override fees per branch if needed.</div>
                </div>
            </td>
        `;

        // Branch-specific fees container
        const branchFeesDiv = document.createElement('tr');
        branchFeesDiv.className = 'branch-fees-row';
        branchFeesDiv.innerHTML = `<td colspan="6" style="display:none;" class="branch-fees-cell">
            <div class="p-3 border rounded bg-light branch-fees-panel">
                <div class="d-flex align-items-start justify-content-between mb-3 flex-wrap gap-3">
                    <div>
                        <h6 class="mb-1">Branch-specific fees</h6>
                        <p class="mb-0 text-muted small">If fees differ by branch, enter the amounts below for each branch. Leave blank if the branch uses the same term fees above.</p>
                        <div class="alert alert-info py-1 mt-2 mb-0 small branch-only-note d-none">
                            Branch-only override enabled: general term fees are optional for this term.
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary toggle-branch-fees">Show branch fees</button>
                </div>
                <div class="branch-fees-container">
                    ${buildBranchFeesInputs(index)}
                </div>
            </div>
        </td>`;

        const termSelect = tr.querySelector('.term-select');
        const feeInputs  = tr.querySelectorAll('input[type="number"]');

        function openBranchPanel() {
            const visible = branchPanel.style.display !== 'none';
            if (!visible) {
                prefillBranchFees(branchFeesDiv, tr);
                branchPanel.style.display = 'block';
                toggleButton.textContent = 'Hide branch fees';
                toggleButton.classList.remove('btn-outline-secondary');
                toggleButton.classList.add('btn-secondary');
                toggleButton.title = 'Hide branch-specific fees';
            }
        }

        termSelect.addEventListener('change', function () {
            updateUsedTerms();
            calculateTotalYearFees();
            if (termSelect.value) {
                openBranchPanel();
            }
        });

        feeInputs.forEach(input => {
            input.addEventListener('input', calculateTotalYearFees);
        });

        tr.querySelector('.remove-term-fee').addEventListener('click', function () {
            // also remove the following branch-fees-row if present
            const next = tr.nextElementSibling;
            if (next && next.classList.contains('branch-fees-row')) next.remove();
            tr.remove();
            updateUsedTerms();
            calculateTotalYearFees();
        });

        const branchBtn = tr.querySelector('.branch-fees-btn');
        const branchLabel = branchBtn.querySelector('i');
        const branchPanel = branchFeesDiv.querySelector('.branch-fees-cell');

        const branchNote = branchFeesDiv.querySelector('.branch-only-note');
        const toggleButton = branchFeesDiv.querySelector('.toggle-branch-fees');
        toggleButton.addEventListener('click', function () {
            const visible = branchPanel.style.display !== 'none';
            if (!visible) {
                prefillBranchFees(branchFeesDiv, tr);
                updateBranchOnlyNote(branchFeesDiv, tr);
            }
            branchPanel.style.display = visible ? 'none' : 'block';
            toggleButton.textContent = visible ? 'Show branch fees' : 'Hide branch fees';
            toggleButton.classList.toggle('btn-outline-secondary', visible);
            toggleButton.classList.toggle('btn-secondary', !visible);
            toggleButton.title = visible ? 'Show branch-specific fees' : 'Hide branch-specific fees';
        });

        branchBtn.addEventListener('click', function () {
            const visible = branchPanel.style.display !== 'none';
            if (!visible) {
                prefillBranchFees(branchFeesDiv, tr);
                updateBranchOnlyNote(branchFeesDiv, tr);
                branchPanel.style.display = 'block';
                toggleButton.textContent = 'Hide branch fees';
                toggleButton.classList.remove('btn-outline-secondary');
                toggleButton.classList.add('btn-secondary');
                toggleButton.title = 'Hide branch-specific fees';
            } else {
                branchPanel.style.display = 'none';
                toggleButton.textContent = 'Show branch fees';
                toggleButton.classList.add('btn-outline-secondary');
                toggleButton.classList.remove('btn-secondary');
                toggleButton.title = 'Show branch-specific fees';
            }
        });

        tbody.appendChild(tr);
        // append branch fees row after the term row
        tbody.appendChild(branchFeesDiv);
    }

    function buildBranchFeesInputs(rowIndex) {
        const branches = courseBranches;
        let html = '';
        branches.forEach(branch => {
            html += `<div class="branch-fee-card mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2 branch-fee-header">
                    <div>
                        <strong>${branch.name}</strong>
                        <div class="text-muted small">Branch ID: ${branch.id}</div>
                    </div>
                    <span class="badge bg-secondary">Branch-specific fees</span>
                </div>
                <div class="branch-fee-row flex-wrap gap-2">
                    <div class="branch-fee-field">
                        <label class="form-label small mb-1">Registration</label>
                        <input type="number" name="term_fees[${rowIndex}][branch_fees][${branch.id}][registration_fee]" class="form-control" min="0" step="0.01" placeholder="Registration fee" value="">
                    </div>
                    <div class="branch-fee-field">
                        <label class="form-label small mb-1">Tuition</label>
                        <input type="number" name="term_fees[${rowIndex}][branch_fees][${branch.id}][tuition_fee]" class="form-control" min="0" step="0.01" placeholder="Tuition fee" value="">
                    </div>
                    <div class="branch-fee-field">
                        <label class="form-label small mb-1">Practical</label>
                        <input type="number" name="term_fees[${rowIndex}][branch_fees][${branch.id}][practical_fee]" class="form-control" min="0" step="0.01" placeholder="Practical fee" value="">
                    </div>
                    <div class="branch-fee-field">
                        <label class="form-label small mb-1">Trip</label>
                        <input type="number" name="term_fees[${rowIndex}][branch_fees][${branch.id}][trip_fee]" class="form-control" min="0" step="0.01" placeholder="Trip fee" value="">
                    </div>
                </div>
            </div>`;
        });
        return html;
    }

    function updateUsedTerms() {
        usedTerms.clear();
        document.querySelectorAll('.term-select').forEach(sel => {
            if (sel.value) usedTerms.add(sel.value);
        });
        // Rebuild options in every select to reflect newly used/freed terms
        document.querySelectorAll('.term-select').forEach(sel => {
            const current = sel.value;
            sel.innerHTML = buildSelectOptions(current);
        });
    }

    function prefillBranchFees(branchFeesDiv, termRow) {
        const registration = parseFloat(termRow.querySelector('input[name$="[registration_fee]"]').value) || 0;
        const tuition      = parseFloat(termRow.querySelector('input[name$="[tuition_fee]"]').value)      || 0;
        const practical    = parseFloat(termRow.querySelector('input[name$="[practical_fee]"]').value)    || 0;
        const trip         = parseFloat(termRow.querySelector('input[name$="[trip_fee]"]').value)         || 0;

        branchFeesDiv.querySelectorAll('input[name$="[registration_fee]"]').forEach(input => {
            if (input.value.trim() === '') input.value = registration;
        });
        branchFeesDiv.querySelectorAll('input[name$="[tuition_fee]"]').forEach(input => {
            if (input.value.trim() === '') input.value = tuition;
        });
        branchFeesDiv.querySelectorAll('input[name$="[practical_fee]"]').forEach(input => {
            if (input.value.trim() === '') input.value = practical;
        });
        branchFeesDiv.querySelectorAll('input[name$="[trip_fee]"]').forEach(input => {
            if (input.value.trim() === '') input.value = trip;
        });
    }

    function updateBranchOnlyNote(branchFeesDiv, termRow) {
        const generalValues = ['registration_fee', 'tuition_fee', 'practical_fee', 'trip_fee'].map(field => {
            const input = termRow.querySelector(`input[name$="[${field}]"]`);
            return input ? input.value.trim() : '';
        });

        const branchInputs = Array.from(branchFeesDiv.querySelectorAll('input')).filter(input => {
            return ['registration_fee', 'tuition_fee', 'practical_fee', 'trip_fee'].some(field => input.name.endsWith(`[${field}]`));
        });

        const branchHasAny = branchInputs.some(input => input.value.trim() !== '');
        const generalAllBlank = generalValues.every(value => value === '');
        const branchNote = branchFeesDiv.querySelector('.branch-only-note');

        if (branchHasAny && generalAllBlank) {
            branchNote.classList.remove('d-none');
        } else {
            branchNote.classList.add('d-none');
        }
    }

    function calculateTotalYearFees() {
        const studentCardFee = 2000;
        let totalFees = 0;

        document.querySelectorAll('#term-fees-table tbody tr').forEach(row => {
            const termSelect = row.querySelector('.term-select');
            if (!termSelect || !termSelect.value) return;

            // ✅ FIX 2: corrected selector — closing ] was missing
            const registration = parseFloat(row.querySelector('input[name$="[registration_fee]"]').value) || 0;
            const tuition      = parseFloat(row.querySelector('input[name$="[tuition_fee]"]').value)      || 0;
            const practical    = parseFloat(row.querySelector('input[name$="[practical_fee]"]').value)    || 0;
            const trip         = parseFloat(row.querySelector('input[name$="[trip_fee]"]').value)         || 0;

            totalFees += registration + tuition + practical + trip + studentCardFee;
        });

        document.getElementById('total_year_fees').value = totalFees.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function () {
        addTermFeeRow();

        // Safe form submission handling
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                // Check if at least one term is selected
                const termSelects = document.querySelectorAll('.term-select');
                let hasValidTerm = false;
                
                termSelects.forEach(select => {
                    if (select.value && select.value.trim() !== '') {
                        hasValidTerm = true;
                    }
                });
                
                if (!hasValidTerm) {
                    e.preventDefault();
                    alert('Please select at least one academic term and configure fees.');
                    return false;
                }
                
                // Check for duplicate terms
                const selectedTerms = [];
                let hasDuplicates = false;
                
                termSelects.forEach(select => {
                    if (select.value && select.value.trim() !== '') {
                        if (selectedTerms.includes(select.value)) {
                            hasDuplicates = true;
                        } else {
                            selectedTerms.push(select.value);
                        }
                    }
                });
                
                if (hasDuplicates) {
                    e.preventDefault();
                    alert('Please remove duplicate academic terms. Each term can only be selected once.');
                    return false;
                }
                
                return true;
            });
        }
    });
</script>

<style>
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .btn-sm {
        border-radius: 8px;
        padding: 0.3rem 0.6rem;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .btn-outline-primary {
        border-color: #667eea;
        color: #667eea;
    }
    
    .btn-outline-primary:hover {
        background: #667eea;
        border-color: #667eea;
    }
    
    .table {
        border-radius: 12px;
        overflow: hidden;
    }

    .branch-fees-panel {
        background: #fafbff;
        border-color: #d8e2ff;
    }

    .branch-fee-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 15px;
    }

    .branch-fee-header {
        gap: 1rem;
        flex-wrap: wrap;
    }

    .branch-fee-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .branch-fee-field {
        display: flex;
        flex-direction: column;
        min-width: 180px;
        flex: 1 1 180px;
    }

    .branch-fee-field input {
        min-width: 120px;
    }

    .branch-fees-panel {
        background: #f6f8ff;
        border-color: #d8e2ff;
    }
    
    .table th {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        border: none;
        padding: 1rem;
    }
    
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .alert {
        border: none;
        border-radius: 12px;
    }
</style>
@endsection
