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
    <label class="form-label">Add Fees & Discount</label>
    <div class="d-flex flex-wrap gap-2 mb-2">
        <button type="button" class="btn btn-outline-primary" onclick="showFeeInput('registration')">Add Registration Fee</button>
        <button type="button" class="btn btn-outline-primary" onclick="showFeeInput('tuition')">Add Tuition Fee</button>
        <button type="button" class="btn btn-outline-primary" onclick="showFeeInput('practical')">Add Practical Fee</button>
        <button type="button" class="btn btn-outline-primary" onclick="showFeeInput('trip')">Add Trip Fee</button>
        <button type="button" class="btn btn-outline-warning" onclick="showFeeInput('discount')">Add Discount</button>
    </div>
    <div id="fee-inputs">
        {{-- Always include hidden inputs for optional fees to prevent null errors --}}
        <input type="hidden" name="practical_fee" value="0">
        <input type="hidden" name="trip_fee" value="0">
        <input type="hidden" name="discount_fee" value="0">
        
        {{-- Dynamic fee inputs will appear here --}}
        @foreach(['registration', 'tuition', 'practical', 'trip', 'discount'] as $type)
            @if(old($type . '_fee'))
                <div class="mb-2" id="{{ $type }}-fee-input">
                    <label class="form-label text-capitalize">{{ ucfirst($type) }} {{ $type == 'discount' ? '' : 'Fee' }}</label>
                    <input type="number" step="0.01" min="0" name="{{ $type }}_fee" class="form-control" value="{{ old($type . '_fee') }}" required>
                </div>
            @endif
        @endforeach
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
    function showFeeInput(type) {
        const labels = {
            registration: 'Registration Fee',
            tuition: 'Tuition Fee',
            practical: 'Practical Fee',
            trip: 'Trip Fee',
            discount: 'Discount (%)'
        };
        if (!document.getElementById(type + '-fee-input')) {
            const div = document.createElement('div');
            div.className = 'mb-2';
            div.id = type + '-fee-input';
            div.innerHTML = `
                <label class="form-label">${labels[type]}</label>
                <input type="number" step="0.01" min="0" name="${type}_fee" class="form-control fee-input" ${type === 'discount' ? 'max="100"' : ''} required>
            `;
            document.getElementById('fee-inputs').appendChild(div);
            attachFeeListeners();
        }
    }

    function attachFeeListeners() {
        // Attach input event to all fee inputs for live calculation
        document.querySelectorAll('.fee-input').forEach(input => {
            input.removeEventListener('input', calculateTotalCost); // Prevent duplicate listeners
            input.addEventListener('input', calculateTotalCost);
        });
    }

    function calculateTotalCost() {
        // Get values, default to 0 if not present
        const tuition = parseFloat(document.querySelector('input[name="tuition_fee"]')?.value) || 0;
        const practical = parseFloat(document.querySelector('input[name="practical_fee"]')?.value) || 0;
        const trip = parseFloat(document.querySelector('input[name="trip_fee"]')?.value) || 0;
        const discount = parseFloat(document.querySelector('input[name="discount_fee"]')?.value) || 0;

        let subtotal = tuition + practical + trip;
        let total = subtotal;

        if (discount > 0 && discount <= 100) {
            total = subtotal - (subtotal * (discount / 100));
        }

        document.getElementById('total_cost').value = total.toFixed(2);
    }

    // Attach listeners on page load (for old inputs)
    document.addEventListener('DOMContentLoaded', function() {
        attachFeeListeners();
        calculateTotalCost();
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
        padding: 0.8rem 1rem 0.8rem 3rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8f9fa;
        width: 100%;
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