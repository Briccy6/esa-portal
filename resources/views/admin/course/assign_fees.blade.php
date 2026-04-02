@extends('admin.layout')

@section('title', 'Assign Course Fees')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-money-bill-wave me-2"></i>Assign Fees - {{ $course->course_name }}</h2>
            <p class="text-muted">Set up fee structure for different terms</p>
        </div>
        <a href="{{ route('course.index') }}" class="btn btn-outline-secondary">
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

    <!-- Fee Assignment Form -->
    <form action="{{ route('course.store-fees', $course->id) }}" method="POST" class="bg-white p-4 rounded shadow-sm">
        @csrf

        <div class="mb-4">
            <label class="form-label">Fee Structure by Term <span class="text-danger">*</span></label>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Set up fees for each term this course will be active. You can configure different fees for different terms.
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
                            <tr id="term-fee-row-template" style="display: none;">
                                <td>
                                    <select name="term_fees[{index}][term_id]" class="form-select term-select" required>
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

        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Assign Fees
            </button>
            <a href="{{ route('course.index') }}" class="btn btn-secondary">
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

    let termFeeRowIndex = 0;
    const usedTerms = new Set();

    function buildSelectOptions(currentValue) {
        let html = '<option value="">Select Term</option>';
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
                    ${buildSelectOptions('')}
                </select>
            </td>
            <td><input type="number" name="term_fees[${index}][registration_fee]" class="form-control" min="0" step="0.01" value="0"></td>
            <td><input type="number" name="term_fees[${index}][tuition_fee]" class="form-control" min="0" step="0.01" value="0"></td>
            <td><input type="number" name="term_fees[${index}][practical_fee]" class="form-control" min="0" step="0.01" value="0"></td>
            <td><input type="number" name="term_fees[${index}][trip_fee]" class="form-control" min="0" step="0.01" value="0"></td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-term-fee">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        const termSelect = tr.querySelector('.term-select');
        const feeInputs  = tr.querySelectorAll('input[type="number"]');

        termSelect.addEventListener('change', function () {
            updateUsedTerms();
            calculateTotalYearFees();
        });

        feeInputs.forEach(input => {
            input.addEventListener('input', calculateTotalYearFees);
        });

        tr.querySelector('.remove-term-fee').addEventListener('click', function () {
            tr.remove();
            updateUsedTerms();
            calculateTotalYearFees();
        });

        tbody.appendChild(tr);
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

        document.querySelector('form').addEventListener('submit', function (e) {
            const hasValid = [...document.querySelectorAll('.term-select')].some(s => s.value);
            if (!hasValid) {
                e.preventDefault();
                alert('Please add at least one term with fee configuration.');
            }
        });
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
