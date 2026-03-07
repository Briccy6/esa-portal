@extends('staff.partials.sidebar_sec')

@section('content')
<style>
    .create-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 30px 0;
    }
    .create-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        max-width: 900px;
        margin: 0 auto;
        overflow: hidden;
    }
    .create-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }
    .create-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
    .create-body { padding: 40px; }
    .section-title {
        color: #667eea;
        font-size: 1.2rem;
        font-weight: 600;
        margin: 25px 0 15px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #667eea;
    }
    .form-label { font-weight: 600; color: #333; }
    .form-control, .form-select {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px 14px;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
    }
    .required::after { content: ' *'; color: #dc3545; }
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 13px 40px;
        border-radius: 8px;
        font-size: 1.05rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102,126,234,0.4);
        color: white;
    }
    .fee-info-box {
        background: #f0f4ff;
        border: 1px solid #c7d2fe;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }
    .fee-info-box p { margin: 0; color: #4338ca; font-size: 0.9rem; }
</style>

<div class="create-container">
    <div class="container">
        <div class="create-card">
            <div class="create-header">
                <h2><i class="fas fa-user-plus me-2"></i>Register New Student</h2>
                <p class="mb-0 mt-1 opacity-75">Cash / manual registration by staff</p>
            </div>

            <div class="create-body">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" id="createStudentForm">
                    @csrf

                    {{-- ── Personal Information ── --}}
                    <h5 class="section-title"><i class="fas fa-user me-2"></i>Personal Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Full Name</label>
                            <input type="text" name="full_name" class="form-control"
                                   value="{{ old('full_name') }}" required placeholder="Enter full name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Sex</label>
                            <select name="sex" class="form-select" required>
                                <option value="">Select Sex</option>
                                <option value="Male"   {{ old('sex') == 'Male'   ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other"  {{ old('sex') == 'Other'  ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Email Address</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email') }}" required placeholder="student@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Phone Number</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone') }}" required placeholder="+250 7xx xxx xxx">
                        </div>
                    </div>

                    {{-- ── Academic Information ── --}}
                    <h5 class="section-title"><i class="fas fa-graduation-cap me-2"></i>Academic Information</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label required">Academic Year</label>
                            <select name="academic_year_id" class="form-select" id="academicYearSelect" required>
                                <option value="">Select Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Branch</label>
                            <select name="branch_id" class="form-select" id="branchSelect" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Course</label>
                            <select name="course_id" class="form-select" id="courseSelect" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}"
                                            data-reg-fee="{{ $course->registration_fee }}"
                                            {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }} ({{ $course->abbreviation }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Discount (%)</label>
                            <input type="number" name="discount" class="form-control"
                                   value="{{ old('discount', 0) }}" min="0" max="100" step="0.01">
                        </div>
                    </div>

                    {{-- ── Initial Payment ── --}}
                    <h5 class="section-title"><i class="fas fa-money-bill-wave me-2"></i>Initial Payment (Cash)</h5>

                    <div class="fee-info-box" id="feeInfoBox" style="display:none;">
                        <p><i class="fas fa-info-circle me-1"></i>
                            Registration fee for selected course: <strong id="regFeeDisplay">—</strong> RWF
                        </p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Fee Type Being Paid</label>
                            <select name="fee_type" class="form-select" required>
                                <option value="">Select Fee Type</option>
                                <option value="registration_fee" {{ old('fee_type') == 'registration_fee' ? 'selected' : '' }}>Registration Fee</option>
                                <option value="school_fee"       {{ old('fee_type') == 'school_fee'       ? 'selected' : '' }}>School / Tuition Fee</option>
                                <option value="practical_fee"    {{ old('fee_type') == 'practical_fee'    ? 'selected' : '' }}>Practical Fee</option>
                                <option value="trip_fee"         {{ old('fee_type') == 'trip_fee'         ? 'selected' : '' }}>Trip Fee</option>
                                <option value="student_card"     {{ old('fee_type') == 'student_card'     ? 'selected' : '' }}>Student Card</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Amount Paid (RWF)</label>
                            <input type="number" name="amount_paid" class="form-control"
                                   value="{{ old('amount_paid') }}" min="1" step="1" required
                                   placeholder="e.g. 15000">
                        </div>
                    </div>

                    {{-- ── Action Buttons ── --}}
                    <div class="text-center mt-5">
                        <a href="{{ route('students.index') }}" class="btn btn-secondary me-3 px-4">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <i class="fas fa-user-plus me-2"></i>Register Student
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const courseSelect  = document.getElementById('courseSelect');
    const feeInfoBox    = document.getElementById('feeInfoBox');
    const regFeeDisplay = document.getElementById('regFeeDisplay');

    courseSelect.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        const regFee = option.dataset.regFee;
        if (regFee) {
            regFeeDisplay.textContent = Number(regFee).toLocaleString();
            feeInfoBox.style.display  = '';
        } else {
            feeInfoBox.style.display = 'none';
        }
    });

    // Loading state on submit
    document.getElementById('createStudentForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Registering…';
    });
});
</script>
@endsection