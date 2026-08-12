@extends('admin.layout')

@section('title', 'Edit Course Fees')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-money-bill-wave me-2"></i>Edit Fees - {{ $course->course_name }}</h2>
            <p class="text-muted">Update fee structure for different terms</p>
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

    <!-- Current Fees Display -->
    @if($course->courseTermFees->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Current Fee Structure</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Term</th>
                                <th>Registration</th>
                                <th>Tuition</th>
                                <th>Practical</th>
                                <th>Trip</th>
                                <th>Student Card</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($course->courseTermFees->where('is_active', true) as $termFee)
                                <tr>
                                    <td>{{ $termFee->term->name }} - {{ $termFee->term->academicYear->year ?? '' }}</td>
                                    <td>{{ number_format($termFee->registration_fee, 0) }} Rwf</td>
                                    <td>{{ number_format($termFee->tuition_fee, 0) }} Rwf</td>
                                    <td>{{ number_format($termFee->practical_fee, 0) }} Rwf</td>
                                    <td>{{ number_format($termFee->trip_fee, 0) }} Rwf</td>
                                    <td>{{ number_format($termFee->student_card_fee, 0) }} Rwf</td>
                                    <td><strong>{{ number_format($termFee->total_fees, 0) }} Rwf</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

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

    <!-- Fee Edit Form -->
    <form action="{{ route('admin.course.update-fees', $course->id) }}" method="POST" class="bg-white p-4 rounded shadow-sm">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="form-label">Fee Structure by Term <span class="text-danger">*</span></label>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Update fees for each term. The current fees will be replaced with the new configuration.
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
                            @php
                                $branchFeesByTerm = $course->courseTermBranchFees->where('is_active', true)->groupBy('term_id');
                                $existingTermIds = $course->courseTermFees->where('is_active', true)->pluck('term_id')
                                    ->merge($branchFeesByTerm->keys())
                                    ->unique()
                                    ->values();
                            @endphp

                            @foreach($existingTermIds as $index => $termId)
                                @php
                                    $termFee = $course->courseTermFees->where('term_id', $termId)->where('is_active', true)->first();
                                    $branchFees = $branchFeesByTerm->get($termId, collect())->keyBy('branch_id');
                                @endphp
                                <tr class="term-fee-row">
                                    <td>
                                        <select name="term_fees[{{ $index }}][term_id]" class="form-select term-select" required>
                                            <option value="">Select Term</option>
                                            @foreach($terms as $term)
                                                <option value="{{ $term->id }}" {{ $termId == $term->id ? 'selected' : '' }}>
                                                    {{ $term->name }} - {{ $term->academicYear->year ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="term_fees[{{ $index }}][registration_fee]" 
                                               class="form-control" min="0" step="0.01" value="{{ optional($termFee)->registration_fee }}">
                                    </td>
                                    <td>
                                        <input type="number" name="term_fees[{{ $index }}][tuition_fee]" 
                                               class="form-control" min="0" step="0.01" value="{{ optional($termFee)->tuition_fee }}">
                                    </td>
                                    <td>
                                        <input type="number" name="term_fees[{{ $index }}][practical_fee]" 
                                               class="form-control" min="0" step="0.01" value="{{ optional($termFee)->practical_fee }}">
                                    </td>
                                    <td>
                                        <input type="number" name="term_fees[{{ $index }}][trip_fee]" 
                                               class="form-control" min="0" step="0.01" value="{{ optional($termFee)->trip_fee }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger remove-term-fee">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="branch-fees-row" style="{{ $branchFees->isNotEmpty() ? '' : 'display:none;' }}">
                                    <td colspan="6">
                                        <div class="p-3 border rounded bg-light branch-fees-panel">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div>
                                                    <h6 class="mb-1">Branch-specific fees for this term</h6>
                                                    <p class="mb-0 text-muted small">Existing branch fee overrides are shown below. Change any value to update that branch&apos;s fees.</p>
                                                </div>
                                                <span class="badge bg-secondary">Branch-specific</span>
                                            </div>

                                            <div class="row g-3">
                                                @foreach($course->branches as $branch)
                                                    @php $branchFee = $branchFees->get($branch->id); @endphp
                                                    <div class="col-md-6">
                                                        <div class="card p-3">
                                                            <div class="mb-2">
                                                                <strong>{{ $branch->name }}</strong>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label small">Registration</label>
                                                                <input type="number" name="term_fees[{{ $index }}][branch_fees][{{ $branch->id }}][registration_fee]" class="form-control" min="0" step="0.01" value="{{ optional($branchFee)->registration_fee }}">
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label small">Tuition</label>
                                                                <input type="number" name="term_fees[{{ $index }}][branch_fees][{{ $branch->id }}][tuition_fee]" class="form-control" min="0" step="0.01" value="{{ optional($branchFee)->tuition_fee }}">
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label small">Practical</label>
                                                                <input type="number" name="term_fees[{{ $index }}][branch_fees][{{ $branch->id }}][practical_fee]" class="form-control" min="0" step="0.01" value="{{ optional($branchFee)->practical_fee }}">
                                                            </div>
                                                            <div>
                                                                <label class="form-label small">Trip</label>
                                                                <input type="number" name="term_fees[{{ $index }}][branch_fees][{{ $branch->id }}][trip_fee]" class="form-control" min="0" step="0.01" value="{{ optional($branchFee)->trip_fee }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr id="term-fee-row-template" style="display: none;">
                                <td>
                                    <select name="term_fees[{index}][term_id]" class="form-select term-select" disabled>
                                        <option value="">Select Term</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->id }}">
                                                {{ $term->name }} - {{ $term->academicYear->year ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="term_fees[{index}][registration_fee]" 
                                           class="form-control" min="0" step="0.01" value="0" disabled>
                                </td>
                                <td>
                                    <input type="number" name="term_fees[{index}][tuition_fee]" 
                                           class="form-control" min="0" step="0.01" value="0" disabled>
                                </td>
                                <td>
                                    <input type="number" name="term_fees[{index}][practical_fee]" 
                                           class="form-control" min="0" step="0.01" value="0" disabled>
                                </td>
                                <td>
                                    <input type="number" name="term_fees[{index}][trip_fee]" 
                                           class="form-control" min="0" step="0.01" value="0" disabled>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-term-fee">
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
                            <label class="form-label">Student Card Fee (per term):</label>
                            <input type="number" name="student_card_fee" class="form-control" value="2000" readonly>
                            <small class="text-muted">Fixed fee per term</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Update Fees
            </button>
            <a href="{{ route('admin.course.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
    let termFeeRowIndex = {{ $existingTermIds->count() ?? $course->courseTermFees->where('is_active', true)->count() }};
    const usedTerms = new Set();

    function addTermFeeRow() {
        const tbody = document.querySelector('#term-fees-table tbody');
        const template = document.getElementById('term-fee-row-template');
        
        // Clone the template row
        const newRow = template.cloneNode(true);
        newRow.style.display = '';
        newRow.id = '';
        
        // Replace {index} with actual index
        const index = termFeeRowIndex++;
        newRow.innerHTML = newRow.innerHTML.replace(/{index}/g, index);
        
        // Remove disabled attribute from all form controls
        newRow.querySelectorAll('input, select').forEach(control => {
            control.removeAttribute('disabled');
        });
        
        // Add event listeners
        const termSelect = newRow.querySelector('.term-select');
        const removeBtn = newRow.querySelector('.remove-term-fee');
        const feeInputs = newRow.querySelectorAll('input[type="number"]');
        
        termSelect.addEventListener('change', function() {
            updateUsedTerms();
            calculateTotalYearFees();
        });
        
        feeInputs.forEach(input => {
            input.addEventListener('input', calculateTotalYearFees);
        });
        
        removeBtn.addEventListener('click', function() {
            const termId = termSelect.value;
            if (termId) {
                usedTerms.delete(termId);
                updateTermSelectOptions();
            }
            newRow.remove();
            calculateTotalYearFees();
        });
        
        tbody.appendChild(newRow);
        updateTermSelectOptions();
    }

    function updateUsedTerms() {
        usedTerms.clear();
        document.querySelectorAll('.term-select').forEach(select => {
            if (select.value) {
                usedTerms.add(select.value);
            }
        });
        updateTermSelectOptions();
    }

    function updateTermSelectOptions() {
        document.querySelectorAll('.term-select').forEach(select => {
            const currentValue = select.value;
            select.innerHTML = '<option value="">Select Term</option>';
            
            const termsData = [
                @foreach($terms as $term)
                    {
                        id: '{{ $term->id }}',
                        name: '{{ $term->name }} - {{ $term->academicYear->year ?? '' }}'
                    },
                @endforeach
            ];
            
            termsData.forEach(term => {
                const option = document.createElement('option');
                option.value = term.id;
                option.textContent = term.name;
                
                // Disable if term is already used elsewhere
                if (usedTerms.has(term.id) && term.id !== currentValue) {
                    option.disabled = true;
                }
                
                // Select current value
                if (term.id === currentValue) {
                    option.selected = true;
                }
                
                select.appendChild(option);
            });
        });
    }

    function calculateTotalYearFees() {
        let totalFees = 0;
        const studentCardFee = 2000; // Fixed per term
        let termCount = 0;
        
        document.querySelectorAll('#term-fees-table tbody tr:not([style*="display: none"])').forEach(row => {
            const termSelect = row.querySelector('.term-select');
            if (termSelect.value) {
                termCount++;
                const registration = parseFloat(row.querySelector('input[name$="[registration_fee]"]').value) || 0;
                const tuition = parseFloat(row.querySelector('input[name$="[tuition_fee]"]').value) || 0;
                const practical = parseFloat(row.querySelector('input[name$="[practical_fee]"]').value) || 0;
                const trip = parseFloat(row.querySelector('input[name$="[trip_fee]"]').value) || 0;
                
                totalFees += registration + tuition + practical + trip + studentCardFee;
            }
        });
        
        document.getElementById('total_year_fees').value = totalFees.toFixed(2);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateUsedTerms();
        calculateTotalYearFees();
        
        // Add event listeners to existing rows
        document.querySelectorAll('#term-fees-table tbody tr:not([style*="display: none"])').forEach(row => {
            const termSelect = row.querySelector('.term-select');
            const removeBtn = row.querySelector('.remove-term-fee');
            const feeInputs = row.querySelectorAll('input[type="number"]');
            
            if (termSelect) {
                termSelect.addEventListener('change', function() {
                    updateUsedTerms();
                    calculateTotalYearFees();
                });
            }
            
            if (feeInputs) {
                feeInputs.forEach(input => {
                    input.addEventListener('input', calculateTotalYearFees);
                });
            }
            
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    const termId = termSelect.value;
                    if (termId) {
                        usedTerms.delete(termId);
                        updateTermSelectOptions();
                    }
                    const next = row.nextElementSibling;
                    if (next && next.classList.contains('branch-fees-row')) {
                        next.remove();
                    }
                    row.remove();
                    calculateTotalYearFees();
                });
            }
        });
        
        // Handle form submission
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const termRows = document.querySelectorAll('#term-fees-table tbody tr:not([style*="display: none"]):not(#term-fee-row-template)');
            
            // Remove empty rows (rows without a selected term)
            termRows.forEach(row => {
                const termSelect = row.querySelector('.term-select');
                if (!termSelect || !termSelect.value) {
                    row.remove();
                }
            });
            
            // Allow submission with or without term fees
            // (the backend will handle updating the course with 0 or more fees)
        });
    });
</script>

<style>
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #e9ecef;
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
