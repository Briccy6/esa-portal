@extends('admin.layout')

@section('title', 'Create Course')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-graduation-cap me-2"></i>Create Course</h2>
            <p class="text-muted">Add a new course to your academic program</p>
        </div>
        <a href="{{ route('admin.course.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Courses
        </a>
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

    <!-- Course Form -->
    <form action="{{ route('admin.course.store') }}" method="POST" class="bg-white p-4 rounded shadow-sm">
        @csrf

        <div class="mb-3">
            <label for="course_name" class="form-label">Course Name <span class="text-danger">*</span></label>
            <input type="text" name="course_name" id="course_name" class="form-control" value="{{ old('course_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="abbreviation" class="form-label">Abbreviation <span class="text-danger">*</span></label>
            <input type="text" name="abbreviation" id="abbreviation" class="form-control" placeholder="e.g. PCB, TOU" value="{{ old('abbreviation') }}" required>
        </div>

        <div class="mb-3">
    <label class="form-label">Select Branches <span class="text-danger">*</span></label>
    <div>
        <div class="form-check">
            <input type="checkbox" name="branches[]" id="branch_all" value="all" class="form-check-input" {{ is_array(old('branches')) && in_array('all', old('branches')) ? 'checked' : '' }}>
            <label for="branch_all" class="form-check-label">All Branches</label>
        </div>
        @foreach($branches as $branch)
            <div class="form-check">
                <input type="checkbox" name="branches[]" id="branch_{{ $branch->id }}" value="{{ $branch->id }}" class="form-check-input" {{ is_array(old('branches')) && in_array($branch->id, old('branches')) ? 'checked' : '' }}>
                <label for="branch_{{ $branch->id }}" class="form-check-label">{{ $branch->name }} ({{ $branch->location }})</label>
            </div>
        @endforeach
    </div>
    <small class="text-muted">Hold Ctrl or Cmd to select multiple branches.</small>
</div>


        <div class="mb-3">
            <label for="course_type" class="form-label">Course Type <span class="text-danger">*</span></label>
            <select name="course_type" id="course_type" class="form-select" required>
                <option value="" disabled {{ old('course_type') ? '' : 'selected' }}>Select Course Type</option>
                <option value="rtb trade" {{ old('course_type') == 'rtb trade' ? 'selected' : '' }}>RTB Trade</option>
                <option value="training" {{ old('course_type') == 'training' ? 'selected' : '' }}>Training</option>
                <option value="internship" {{ old('course_type') == 'internship' ? 'selected' : '' }}>Internship</option>
                <option value="reb combination" {{ old('course_type') == 'reb combination' ? 'selected' : '' }}>REB Combination</option>
            </select>
        </div>
<div class="mb-3">
    <label class="form-label">Fee Structure by Term (Optional)</label>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Set up fees for each term now, or you can assign fees later after creating the course.
    </div>
    
    <div class="form-check mb-3">
        <input type="checkbox" name="setup_fees_now" id="setup_fees_now" class="form-check-input" onchange="toggleFeeSection()">
        <label for="setup_fees_now" class="form-check-label">Set up fees now</label>
    </div>
    
    <div id="term-fees-container" style="display: none;">
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
                    <tr id="term-fee-row-template" style="display: none;">
                        <td>
                            <select name="term_fees[{index}][term_id]" class="form-select term-select" required>
                                <option value="">Select Term</option>
                                @php
                                    $terms = \App\Models\Term::with('academicYear')
                                        ->orderBy('start_date', 'desc')
                                        ->get();
                                @endphp
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}">
                                        {{ $term->name }} - {{ $term->academicYear->year ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="term_fees[{index}][registration_fee]" 
                                   class="form-control" min="0" step="0.01" value="0">
                        </td>
                        <td>
                            <input type="number" name="term_fees[{index}][tuition_fee]" 
                                   class="form-control" min="0" step="0.01" value="0">
                        </td>
                        <td>
                            <input type="number" name="term_fees[{index}][practical_fee]" 
                                   class="form-control" min="0" step="0.01" value="0">
                        </td>
                        <td>
                            <input type="number" name="term_fees[{index}][trip_fee]" 
                                   class="form-control" min="0" step="0.01" value="0">
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
        <div class="mb-3">
    <label for="total_cost" class="form-label">Total Cost (Excluding Registration Fee & After Discount)</label>
    <input type="text" id="total_cost" class="form-control" readonly>
    <small class="text-muted">This is the sum of Tuition, Practical, and Trip fees minus discount (%), registration fee is not included.</small>
</div>
        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Create Course
            </button>
            <a href="{{ route('admin.course.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
        </div>
    </form>
</div>


<script>
    let termFeeRowIndex = 0;
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
            
            @php
                $terms = \App\Models\Term::with('academicYear')
                    ->orderBy('start_date', 'desc')
                    ->get();
            @endphp
            
            @foreach($terms as $term)
                const termOption{{ $loop->index }} = document.createElement('option');
                termOption{{ $loop->index }}.value = '{{ $term->id }}';
                termOption{{ $loop->index }}.textContent = '{{ $term->name }} - {{ $term->academicYear->year ?? '' }}';
                
                // Disable if term is already used elsewhere
                if (usedTerms.has('{{ $term->id }}') && '{{ $term->id }}' !== currentValue) {
                    termOption{{ $loop->index }}.disabled = true;
                }
                
                // Select current value
                if ('{{ $term->id }}' === currentValue) {
                    termOption{{ $loop->index }}.selected = true;
                }
                
                select.appendChild(termOption{{ $loop->index }});
            @endforeach
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

    function toggleFeeSection() {
        const checkbox = document.getElementById('setup_fees_now');
        const container = document.getElementById('term-fees-container');
        
        if (checkbox.checked) {
            container.style.display = 'block';
            // Add first row if none exists
            const tbody = document.querySelector('#term-fees-table tbody');
            if (tbody.children.length === 1) {
                addTermFeeRow();
            }
        } else {
            container.style.display = 'none';
        }
    }

    // Initialize with one empty row on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Define toggleFeeSection function globally for onclick handler
        window.toggleFeeSection = toggleFeeSection;
        
        // Handle form submission validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const setupFeesNow = document.getElementById('setup_fees_now').checked;
            
            if (setupFeesNow) {
                const termRows = document.querySelectorAll('#term-fees-table tbody tr:not([style*="display: none"])');
                let hasValidTerm = false;
                
                termRows.forEach(row => {
                    const termSelect = row.querySelector('.term-select');
                    if (termSelect.value) {
                        hasValidTerm = true;
                    }
                });
                
                if (!hasValidTerm) {
                    e.preventDefault();
                    alert('Please add at least one term with fee configuration.');
                    return false;
                }
            }
        });
    });
</script>
<style>
    /* Page Container */
    .page-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        padding: 0;
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .header-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .page-subtitle {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
        font-size: 1.1rem;
    }

    .header-actions .btn {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .header-actions .btn:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-2px);
    }

    /* Content Wrapper */
    .content-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem 2rem;
    }

    /* Form Card */
    .form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .form-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 2rem;
        border-bottom: 1px solid #e9ecef;
        text-align: center;
    }

    .form-title {
        color: #2c3e50;
        font-size: 1.8rem;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
    }

    .form-description {
        color: #6c757d;
        margin: 0;
        font-size: 1.1rem;
    }

    /* Alert Styles */
    .alert-container {
        padding: 0 2rem;
        margin-top: 2rem;
    }

    .alert {
        border: none;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fee 0%, #fdd 100%);
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-icon {
        font-size: 1.5rem;
        margin-top: 0.2rem;
    }

    .alert-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
    }

    .error-list {
        margin: 0;
        padding-left: 1.2rem;
    }

    .error-list li {
        margin-bottom: 0.3rem;
    }

    /* Form Styles */
    .form-modern {
        padding: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -0.75rem;
    }

    .form-group {
        flex: 1;
        padding: 0 0.75rem;
        margin-bottom: 1.5rem;
    }

    .form-group.col-md-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95rem;
    }

    .required {
        color: #dc3545;
        font-weight: 700;
    }

    .input-group {
        position: relative;
        display: flex;
        align-items: stretch;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 5;
        font-size: 1.1rem;
    }

    .form-control,
    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 0.8rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8f9fa;
        width: 100%;
    }

    .input-group .form-control {
        padding-left: 3rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background: white;
        outline: none;
    }

    .form-hint {
        color: #6c757d;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    /* Form Actions */
    .form-actions {
        background: #f8f9fa;
        margin: 2rem -2rem -2rem;
        padding: 2rem;
        border-top: 1px solid #e9ecef;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        border-radius: 12px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }

    .btn-lg {
        padding: 1rem 2.5rem;
        font-size: 1.1rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-outline-secondary {
        background: transparent;
        border: 2px solid #6c757d;
        color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background: #6c757d;
        color: white;
        text-decoration: none;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.8rem;
        }

        .form-group.col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .content-wrapper {
            padding: 0 1rem 2rem;
        }

        .form-modern {
            padding: 1.5rem;
        }

        .form-actions {
            margin: 1.5rem -1.5rem -1.5rem;
            padding: 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .page-title {
            font-size: 1.5rem;
        }

        .form-title {
            font-size: 1.5rem;
        }

        .form-row {
            margin: 0;
        }

        .form-group {
            padding: 0;
        }
    }

    /* Animation */
    .form-card {
        animation: slideInUp 0.6s ease-out;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-group {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
    }

    .form-group:nth-child(1) { animation-delay: 0.1s; }
    .form-group:nth-child(2) { animation-delay: 0.2s; }
    .form-group:nth-child(3) { animation-delay: 0.3s; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection