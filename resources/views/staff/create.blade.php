@extends('staff.partials.sidebar_sec')

@section('content')
@push('styles')
<style>
    .student-registration {
        --registration-ink: #182b28;
        --registration-muted: #61716d;
        --registration-line: #dce5e1;
        --registration-paper: #fff;
        --registration-canvas: #f3f6f3;
        --registration-green: #176b55;
        --registration-green-dark: #10513f;
        --registration-gold: #b38339;
        color: var(--registration-ink);
        margin: -2rem;
        min-height: calc(100vh - 80px);
        padding: clamp(1.25rem, 3vw, 2.75rem);
        background: var(--registration-canvas);
        font-family: "Segoe UI", Tahoma, sans-serif;
    }

    .registration-shell { max-width: 1040px; margin: 0 auto; }
    .registration-heading { margin-bottom: 1.5rem; }
    .registration-eyebrow {
        color: var(--registration-green);
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
    }
    .registration-heading h1 {
        margin: .35rem 0 .45rem;
        color: var(--registration-ink);
        font-family: Georgia, "Times New Roman", serif;
        font-size: clamp(1.8rem, 3vw, 2.45rem);
        font-weight: 600;
        letter-spacing: -.035em;
    }
    .registration-heading p { max-width: 620px; margin: 0; color: var(--registration-muted); }
    .registration-draft-status {
        margin-top: .9rem;
        padding: .7rem .9rem;
        border: 1px solid #d6e5dc;
        border-radius: 8px;
        background: #f5faf6;
        color: #355d4c;
        font-size: .82rem;
    }
    .registration-card {
        overflow: hidden;
        border: 1px solid var(--registration-line);
        border-radius: 16px;
        background: var(--registration-paper);
        box-shadow: 0 16px 44px rgba(24, 43, 40, .07);
    }
    .registration-progress {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        border-bottom: 1px solid var(--registration-line);
        background: #fbfcfb;
    }
    .registration-progress-step {
        position: relative;
        display: flex;
        align-items: center;
        gap: .75rem;
        min-width: 0;
        padding: 1rem clamp(.75rem, 2vw, 1.35rem);
        color: #788580;
    }
    .registration-progress-step + .registration-progress-step { border-left: 1px solid #e8eeeb; }
    .registration-step-number {
        display: grid;
        width: 30px;
        height: 30px;
        flex: 0 0 30px;
        place-items: center;
        border: 1px solid #cbd6d1;
        border-radius: 50%;
        background: #fff;
        color: #65756f;
        font-size: .8rem;
        font-weight: 700;
    }
    .registration-step-copy { min-width: 0; }
    .registration-step-copy strong, .registration-step-copy small { display: block; }
    .registration-step-copy strong { overflow: hidden; font-size: .82rem; text-overflow: ellipsis; white-space: nowrap; }
    .registration-step-copy small { margin-top: .1rem; color: #81908a; font-size: .7rem; }
    .registration-progress-step.is-current { color: var(--registration-green-dark); }
    .registration-progress-step.is-current .registration-step-number,
    .registration-progress-step.is-complete .registration-step-number {
        border-color: var(--registration-green);
        background: var(--registration-green);
        color: #fff;
    }
    .registration-progress-step.is-current .registration-step-copy strong { color: var(--registration-green-dark); }
    .registration-body { padding: clamp(1.25rem, 4vw, 2.5rem); }
    .registration-section-heading { margin-bottom: 1.5rem; }
    .registration-section-heading h2 {
        margin: 0 0 .4rem;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 1.55rem;
        font-weight: 600;
        letter-spacing: -.025em;
    }
    .registration-section-heading p { margin: 0; color: var(--registration-muted); font-size: .92rem; }
    .registration-label { margin-bottom: .45rem; color: #2f403c; font-size: .84rem; font-weight: 700; }
    .registration-label .required { color: #a34a3a; }
    .student-registration .form-control,
    .student-registration .form-select {
        min-height: 47px;
        border-color: #d5dfda;
        border-radius: 8px;
        background-color: #fff;
        color: var(--registration-ink);
        font-size: .92rem;
    }
    .student-registration .form-control:focus,
    .student-registration .form-select:focus {
        border-color: var(--registration-green);
        box-shadow: 0 0 0 3px rgba(23, 107, 85, .12);
    }
    .field-hint { display: block; margin-top: .4rem; color: var(--registration-muted); font-size: .78rem; }
    .email-status { min-height: 1.25rem; margin-top: .45rem; font-size: .8rem; }
    .email-status.is-available { color: #176b55; }
    .email-status.is-taken, .email-status.is-error { color: #a23d32; }
    .email-status.is-checking { color: #6a7370; }
    .registration-callout {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        margin: 1.25rem 0 0;
        padding: .9rem 1rem;
        border: 1px solid #e9dfc9;
        border-radius: 8px;
        background: #fcf8ef;
        color: #685630;
        font-size: .84rem;
    }
    .registration-callout i { margin-top: .15rem; color: var(--registration-gold); }
    .registration-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.25rem;
        border-top: 1px solid #e8eeeb;
    }
    .registration-actions .btn { min-height: 44px; border-radius: 8px; padding: .65rem 1.15rem; font-weight: 700; }
    .btn-registration-primary { border-color: var(--registration-green); background: var(--registration-green); color: #fff; }
    .btn-registration-primary:hover, .btn-registration-primary:focus { border-color: var(--registration-green-dark); background: var(--registration-green-dark); color: #fff; }
    .btn-registration-secondary { border-color: #d5dfda; background: #fff; color: #33423e; }
    .btn-registration-secondary:hover { border-color: #afbeb7; background: #f8faf8; color: var(--registration-ink); }
    .registration-status { min-height: 1.2rem; color: var(--registration-muted); font-size: .8rem; }
    .fee-summary {
        overflow: hidden;
        border: 1px solid var(--registration-line);
        border-radius: 10px;
    }
    .fee-summary-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .9rem 1rem;
        border-bottom: 1px solid var(--registration-line);
        background: #f7faf8;
    }
    .fee-summary-header strong { font-size: .9rem; }
    .fee-summary-header span { color: var(--registration-muted); font-size: .77rem; }
    .fee-table { width: 100%; margin: 0; }
    .fee-table th, .fee-table td { padding: .8rem 1rem; border-color: #e8eeeb; }
    .fee-table th { color: #718078; font-size: .72rem; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; }
    .fee-table td { color: #31413d; font-size: .88rem; }
    .fee-table tfoot td { background: #f7faf8; font-weight: 800; }
    .payment-panel {
        margin-top: 1.25rem;
        padding: 1.15rem;
        border: 1px solid #dce7e1;
        border-radius: 10px;
        background: #f9fbf9;
    }
    .payment-panel h3 { margin: 0 0 1rem; font-size: 1rem; font-weight: 800; }
    .payment-category-list { display: grid; gap: .65rem; }
    .payment-category-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(170px, .7fr);
        align-items: center;
        gap: 1rem;
        padding: .75rem .85rem;
        border: 1px solid #dce5e1;
        border-radius: 8px;
        background: #fff;
    }
    .payment-category-choice { display: flex; align-items: flex-start; gap: .65rem; min-width: 0; cursor: pointer; }
    .payment-category-choice input { width: 1.05rem; height: 1.05rem; flex: 0 0 auto; margin-top: .15rem; accent-color: var(--registration-green); }
    .payment-category-choice strong, .payment-category-choice small { display: block; }
    .payment-category-choice strong { color: #2f403c; font-size: .88rem; }
    .payment-category-choice small { margin-top: .15rem; color: var(--registration-muted); font-size: .76rem; }
    .payment-total {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        margin-top: .8rem;
        padding-top: .8rem;
        border-top: 1px solid #dce5e1;
        color: var(--registration-ink);
        font-size: .9rem;
        font-weight: 800;
    }
    .payment-note { margin: .75rem 0 0; color: var(--registration-muted); font-size: .8rem; }
    .review-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .8rem; }
    .review-item {
        min-width: 0;
        padding: .85rem 1rem;
        border: 1px solid var(--registration-line);
        border-radius: 8px;
        background: #fbfcfb;
    }
    .review-item span { display: block; margin-bottom: .25rem; color: #73817b; font-size: .72rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
    .review-item strong { display: block; overflow-wrap: anywhere; color: var(--registration-ink); font-size: .9rem; font-weight: 700; }
    .review-payment {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 1rem;
        padding: 1rem;
        border-left: 3px solid var(--registration-gold);
        background: #fcf8ef;
        color: #5b4b2d;
    }
    .review-payment span { min-width: 0; overflow-wrap: anywhere; }
    .review-payment strong { flex: 0 0 auto; font-size: 1.05rem; }
    @media (max-width: 700px) {
        .student-registration { margin: -1rem; padding: 1rem; }
        .registration-progress { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .registration-progress-step { justify-content: center; padding: .8rem .35rem; }
        .registration-step-copy { display: none; }
        .registration-step-number { width: 32px; height: 32px; flex-basis: 32px; }
        .registration-body { padding: 1.25rem; }
        .registration-actions { align-items: stretch; }
        .registration-actions .btn { flex: 1; }
    }
    @media (max-width: 480px) {
        .student-registration { margin: -.75rem; padding: .75rem; }
        .registration-heading { margin-bottom: 1rem; }
        .registration-card { border-radius: 12px; }
        .registration-body { padding: 1rem; }
        .registration-section-heading h2 { font-size: 1.35rem; }
        .registration-actions { flex-wrap: wrap; }
        .registration-actions .registration-status { order: 3; width: 100%; }
        .fee-summary-header { align-items: flex-start; flex-direction: column; gap: .25rem; }
        .fee-table thead { display: none; }
        .fee-table, .fee-table tbody, .fee-table tr, .fee-table td, .fee-table tfoot { display: block; width: 100%; }
        .fee-table tr { padding: .4rem .8rem; border-bottom: 1px solid #e8eeeb; }
        .fee-table td { display: flex; justify-content: space-between; gap: .8rem; padding: .35rem 0; border: 0; text-align: right; }
        .fee-table td:first-child { font-weight: 700; text-align: left; }
        .fee-table td:first-child::before { content: "Fee"; color: #718078; font-size: .72rem; font-weight: 800; letter-spacing: .07em; text-transform: uppercase; }
        .fee-table tfoot td { padding: .75rem .8rem; }
        .payment-category-row { grid-template-columns: 1fr; gap: .6rem; }
        .review-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

<main class="student-registration">
    <div class="registration-shell">
        <header class="registration-heading">
            <div class="registration-eyebrow">Student administration / New record</div>
            <h1>Register a student</h1>
            <p>Enter student details, confirm their placement, and record one or more initial payments in a guided process.</p>
            <div id="draftStatus" class="registration-draft-status" role="status" aria-live="polite" hidden></div>
        </header>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please review the highlighted registration details.</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="registration-card" aria-label="Student registration form">
            <div class="registration-progress" aria-label="Registration steps">
                <div class="registration-progress-step is-current" data-progress-step="1" aria-current="step">
                    <span class="registration-step-number">1</span>
                    <span class="registration-step-copy"><strong>Student</strong><small>Personal details</small></span>
                </div>
                <div class="registration-progress-step" data-progress-step="2">
                    <span class="registration-step-number">2</span>
                    <span class="registration-step-copy"><strong>Placement</strong><small>Year and course</small></span>
                </div>
                <div class="registration-progress-step" data-progress-step="3">
                    <span class="registration-step-number">3</span>
                    <span class="registration-step-copy"><strong>Fees</strong><small>Breakdown and payment</small></span>
                </div>
                <div class="registration-progress-step" data-progress-step="4">
                    <span class="registration-step-number">4</span>
                    <span class="registration-step-copy"><strong>Review</strong><small>Confirm registration</small></span>
                </div>
            </div>

            <div class="registration-body">
                <form id="studentRegistrationForm"
                      action="{{ route('staff.students.store') }}"
                      method="POST"
                      data-draft-key="student-registration-draft-{{ Auth::guard('staff')->id() }}"
                      data-registration-succeeded="{{ session()->has('success') ? 'true' : 'false' }}"
                      data-email-url="{{ route('staff.api.student-registration.email') }}"
                      data-terms-url="{{ route('staff.api.student-registration.terms', ['academicYear' => '__YEAR__']) }}"
                      data-courses-url="{{ route('staff.api.student-registration.courses') }}"
                      data-fees-url="{{ route('staff.api.student-registration.fees') }}"
                      data-old-course="{{ old('course_id') }}"
                      data-old-term="{{ old('enrollment_term_id') }}">
                    @csrf

                    <section class="registration-step" data-step="1">
                        <div class="registration-section-heading">
                            <h2>Student details</h2>
                            <p>Start with the student's identity and contact information. Email availability is checked before you continue.</p>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label registration-label" for="full_name">Full name <span class="required">*</span></label>
                                <input class="form-control" id="full_name" name="full_name" type="text" autocomplete="name"
                                       value="{{ old('full_name') }}" maxlength="255" required>
                                <small class="field-hint">The first letter of each name will be capitalized.</small>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label registration-label" for="sex">Sex <span class="required">*</span></label>
                                <select class="form-select" id="sex" name="sex" required>
                                    <option value="">Select sex</option>
                                    @foreach(['Male', 'Female', 'Other'] as $sex)
                                        <option value="{{ $sex }}" @selected(old('sex') === $sex)>{{ $sex }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label registration-label" for="email">Email address <span class="required">*</span></label>
                                <input class="form-control" id="email" name="email" type="email" autocomplete="email"
                                       value="{{ old('email') }}" maxlength="255" required aria-describedby="emailStatus">
                                <div id="emailStatus" class="email-status" role="status" aria-live="polite"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label registration-label" for="phone">Telephone <span class="required">*</span></label>
                                <input class="form-control" id="phone" name="phone" type="tel" autocomplete="tel"
                                       value="{{ old('phone') }}" maxlength="20" required placeholder="+250 7xx xxx xxx">
                            </div>
                        </div>
                        <div class="registration-callout">
                            <i class="fas fa-circle-info" aria-hidden="true"></i>
                            <span>A valid, unused email address is required so the student's account can be created.</span>
                        </div>
                        <div class="registration-actions">
                            <a class="btn btn-registration-secondary" href="{{ route('students.index') }}" data-clear-draft>Cancel</a>
                            <span class="registration-status" aria-live="polite"></span>
                            <button class="btn btn-registration-primary" type="button" data-next>Continue <i class="fas fa-arrow-right ms-2"></i></button>
                        </div>
                    </section>

                    <section class="registration-step" data-step="2" hidden>
                        <div class="registration-section-heading">
                            <h2>Academic placement</h2>
                            <p>Choose the academic year, branch, course, study shift, and term for this student's enrollment.</p>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label registration-label" for="academic_year_id">Academic year <span class="required">*</span></label>
                                <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                                    <option value="">Select academic year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" @selected((string) old('academic_year_id') === (string) $year->id)>{{ $year->year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label registration-label" for="branch_id">Branch <span class="required">*</span></label>
                                <select class="form-select" id="branch_id" name="branch_id" required>
                                    <option value="">Select branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" @selected((string) old('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label registration-label" for="course_id">Course <span class="required">*</span></label>
                                <select class="form-select" id="course_id" name="course_id" required disabled>
                                    <option value="">Select academic year and branch first</option>
                                </select>
                                <small class="field-hint" id="courseStatus" role="status"></small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label registration-label" for="enrollment_term_id">Enrollment term <span class="required">*</span></label>
                                <select class="form-select" id="enrollment_term_id" name="enrollment_term_id" required disabled>
                                    <option value="">Select academic year first</option>
                                </select>
                                <small class="field-hint" id="termStatus" role="status"></small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label registration-label" for="shift">Study shift <span class="required">*</span></label>
                                <select class="form-select" id="shift" name="shift" required>
                                    <option value="">Select study shift</option>
                                    <option value="day" @selected(old('shift') === 'day')>Day</option>
                                    <option value="evening" @selected(old('shift') === 'evening')>Evening</option>
                                    <option value="weekend" @selected(old('shift') === 'weekend')>Weekend</option>
                                </select>
                            </div>
                        </div>
                        <div class="registration-actions">
                            <button class="btn btn-registration-secondary" type="button" data-back><i class="fas fa-arrow-left me-2"></i>Back</button>
                            <span class="registration-status" aria-live="polite"></span>
                            <button class="btn btn-registration-primary" type="button" data-next>Continue <i class="fas fa-arrow-right ms-2"></i></button>
                        </div>
                    </section>

                    <section class="registration-step" data-step="3" hidden>
                        <div class="registration-section-heading">
                            <h2>Fee breakdown and initial payments</h2>
                            <p>Review the amounts configured for this branch, course, and term, then record today's cash payments.</p>
                        </div>
                        <div id="feeLoading" class="registration-callout" role="status">
                            <i class="fas fa-circle-info" aria-hidden="true"></i>
                            <span>Choose an academic placement first to load the fee schedule.</span>
                        </div>
                        <div id="feeError" class="alert alert-warning mt-3" role="alert" hidden></div>
                        <div id="feeBreakdown" class="fee-summary mt-3" hidden>
                            <div class="fee-summary-header">
                                <strong>Configured fee schedule</strong>
                                <span id="feeScheduleSource"></span>
                            </div>
                            <table class="fee-table">
                                <thead>
                                    <tr><th scope="col">Fee category</th><th scope="col" class="text-end">Amount (RWF)</th></tr>
                                </thead>
                                <tbody id="feeRows"></tbody>
                                <tfoot>
                                    <tr><td>Total scheduled</td><td class="text-end" id="feeTotal"></td></tr>
                                </tfoot>
                            </table>
                        </div>
                        <div id="paymentFields" class="payment-panel" hidden>
                            <h3>Cash payments received</h3>
                            <p class="payment-note mt-0 mb-3">Select one or more fee categories and enter the amount received for each. Partial payments are accepted.</p>
                            <div id="paymentRows" class="payment-category-list"></div>
                            <div class="payment-total"><span>Total received</span><strong id="paymentTotal">RWF 0</strong></div>
                            <p class="payment-note">Each amount must be greater than zero and cannot exceed its fee category's scheduled total.</p>
                        </div>
                        <div class="registration-actions">
                            <button class="btn btn-registration-secondary" type="button" data-back><i class="fas fa-arrow-left me-2"></i>Back</button>
                            <span class="registration-status" aria-live="polite"></span>
                            <button class="btn btn-registration-primary" type="button" data-next>Review registration <i class="fas fa-arrow-right ms-2"></i></button>
                        </div>
                    </section>

                    <section class="registration-step" data-step="4" hidden>
                        <div class="registration-section-heading">
                            <h2>Review and confirm</h2>
                            <p>Check the student details, placement, and cash payment before saving the registration.</p>
                        </div>
                        <div class="review-grid">
                            <div class="review-item"><span>Full name</span><strong id="reviewName">—</strong></div>
                            <div class="review-item"><span>Email</span><strong id="reviewEmail">—</strong></div>
                            <div class="review-item"><span>Telephone</span><strong id="reviewPhone">—</strong></div>
                            <div class="review-item"><span>Sex</span><strong id="reviewSex">—</strong></div>
                            <div class="review-item"><span>Academic year</span><strong id="reviewYear">—</strong></div>
                            <div class="review-item"><span>Branch</span><strong id="reviewBranch">—</strong></div>
                            <div class="review-item"><span>Course</span><strong id="reviewCourse">—</strong></div>
                            <div class="review-item"><span>Term and shift</span><strong id="reviewTermShift">—</strong></div>
                        </div>
                        <div class="review-payment">
                            <span>Cash payment categories<br><small id="reviewPaymentType">—</small></span>
                            <strong id="reviewAmount">—</strong>
                        </div>
                        <div class="registration-callout">
                            <i class="fas fa-shield-halved" aria-hidden="true"></i>
                            <span>Confirming creates the student account and records each selected amount as successful cash received.</span>
                        </div>
                        <div class="registration-actions">
                            <button class="btn btn-registration-secondary" type="button" data-back><i class="fas fa-arrow-left me-2"></i>Back</button>
                            <span class="registration-status" aria-live="polite"></span>
                            <button class="btn btn-registration-primary" id="submitRegistration" type="submit">
                                <i class="fas fa-check me-2"></i>Register student
                            </button>
                        </div>
                    </section>
                </form>
            </div>
        </section>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('studentRegistrationForm');
    if (!form) return;

    const steps = Array.from(form.querySelectorAll('.registration-step'));
    const progressSteps = Array.from(document.querySelectorAll('[data-progress-step]'));
    const fullName = form.elements.full_name;
    const email = form.elements.email;
    const emailStatus = document.getElementById('emailStatus');
    const yearSelect = form.elements.academic_year_id;
    const branchSelect = form.elements.branch_id;
    const courseSelect = form.elements.course_id;
    const termSelect = form.elements.enrollment_term_id;
    const feeLoading = document.getElementById('feeLoading');
    const feeError = document.getElementById('feeError');
    const feeBreakdown = document.getElementById('feeBreakdown');
    const paymentFields = document.getElementById('paymentFields');
    const paymentRows = document.getElementById('paymentRows');
    const paymentTotal = document.getElementById('paymentTotal');
    let activeStep = 1;
    let emailState = 'idle';
    let verifiedEmail = '';
    let emailTimer = null;
    let emailRequest = null;
    let emailRequestId = 0;
    let courseRequestId = 0;
    let termRequestId = 0;
    let feeRequestId = 0;
    let feeItems = [];
    let feesLoading = false;
    let pendingOldPayments = @json(old('payments', []));
    const draftStatus = document.getElementById('draftStatus');
    const draftKey = form.dataset.draftKey;
    let restoredDraft = null;
    let restoredStep = 1;

    const formatMoney = amount => new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(Number(amount || 0));

    function setDraftStatus(message, visible) {
        draftStatus.textContent = message;
        draftStatus.hidden = !visible;
    }

    function clearStoredDraft() {
        try {
            window.sessionStorage.removeItem(draftKey);
        } catch (error) {
            console.error('Unable to clear the student registration draft.', error);
        }
    }

    if (form.dataset.registrationSucceeded === 'true') {
        clearStoredDraft();
    } else {
        try {
            const storedDraft = window.sessionStorage.getItem(draftKey);
            if (storedDraft) {
                const parsedDraft = JSON.parse(storedDraft);
                if (parsedDraft && parsedDraft.version === 1 && typeof parsedDraft === 'object') {
                    restoredDraft = parsedDraft;
                } else {
                    clearStoredDraft();
                }
            }
        } catch (error) {
            console.error('Unable to restore the student registration draft.', error);
            setDraftStatus('Automatic draft saving is unavailable in this browser. Refreshing may clear unsaved details.', true);
        }
    }

    function restoreSelectValue(select, value) {
        if (value && Array.from(select.options).some(option => option.value === String(value))) {
            select.value = String(value);
        }
    }

    if (restoredDraft) {
        if (typeof restoredDraft.full_name === 'string') fullName.value = restoredDraft.full_name;
        if (typeof restoredDraft.email === 'string') email.value = restoredDraft.email;
        if (typeof restoredDraft.phone === 'string') form.elements.phone.value = restoredDraft.phone;
        restoreSelectValue(form.elements.sex, restoredDraft.sex);
        restoreSelectValue(yearSelect, restoredDraft.academic_year_id);
        restoreSelectValue(branchSelect, restoredDraft.branch_id);
        restoreSelectValue(form.elements.shift, restoredDraft.shift);
        form.dataset.oldCourse = restoredDraft.course_id || form.dataset.oldCourse;
        form.dataset.oldTerm = restoredDraft.enrollment_term_id || form.dataset.oldTerm;
        if (Array.isArray(restoredDraft.payments)) pendingOldPayments = restoredDraft.payments;
        const savedStep = Number(restoredDraft.step);
        if (Number.isInteger(savedStep) && savedStep >= 1 && savedStep <= steps.length) {
            restoredStep = savedStep;
        }
        setDraftStatus('Your unfinished registration was restored from this browser tab.', true);
    }

    function getSelectedPayments() {
        return Array.from(paymentRows.querySelectorAll('.payment-category-row')).flatMap(row => {
            const checkbox = row.querySelector('.payment-category-toggle');
            if (!checkbox.checked) return [];
            return [{
                type: checkbox.value,
                label: checkbox.dataset.label,
                amount: row.querySelector('.payment-category-amount').value
            }];
        });
    }

    function persistDraft() {
        if (form.dataset.registrationSucceeded === 'true') {
            clearStoredDraft();
            setDraftStatus('', false);
            return;
        }

        const visiblePayments = paymentRows.querySelectorAll('.payment-category-row').length > 0
            ? getSelectedPayments().map(payment => ({ type: payment.type, amount: payment.amount }))
            : (Array.isArray(pendingOldPayments) ? pendingOldPayments : []);
        const draft = {
            version: 1,
            step: activeStep,
            full_name: fullName.value,
            sex: form.elements.sex.value,
            email: email.value,
            phone: form.elements.phone.value,
            academic_year_id: yearSelect.value,
            branch_id: branchSelect.value,
            course_id: courseSelect.value || form.dataset.oldCourse || '',
            enrollment_term_id: termSelect.value || form.dataset.oldTerm || '',
            shift: form.elements.shift.value,
            payments: visiblePayments
        };
        const hasContent = Boolean(
            draft.full_name.trim()
            || draft.sex
            || draft.email.trim()
            || draft.phone.trim()
            || draft.academic_year_id
            || draft.branch_id
            || draft.course_id
            || draft.enrollment_term_id
            || draft.payments.length
        );

        try {
            if (hasContent) {
                window.sessionStorage.setItem(draftKey, JSON.stringify(draft));
                setDraftStatus('Your unfinished registration is saved in this browser tab and will be cleared after registration or when you cancel.', true);
            } else {
                window.sessionStorage.removeItem(draftKey);
                if (form.dataset.registrationSucceeded !== 'true') setDraftStatus('', false);
            }
        } catch (error) {
            console.error('Unable to save the student registration draft.', error);
            setDraftStatus('Automatic draft saving is unavailable in this browser. Refreshing may clear unsaved details.', true);
        }
    }

    function updatePaymentSummary() {
        const total = getSelectedPayments().reduce((sum, payment) => sum + Number(payment.amount || 0), 0);
        paymentTotal.textContent = `RWF ${formatMoney(total)}`;
    }

    function renderPaymentRows(items) {
        const previousPayments = Array.isArray(pendingOldPayments)
            ? pendingOldPayments
            : Object.values(pendingOldPayments || {});
        const validPreviousPayments = previousPayments.filter(payment => payment && typeof payment === 'object');
        pendingOldPayments = [];
        paymentRows.replaceChildren();

        items.forEach((item, index) => {
            const previousPayment = validPreviousPayments.find(payment => payment.type === item.type);
            const row = document.createElement('div');
            row.className = 'payment-category-row';

            const choice = document.createElement('label');
            choice.className = 'payment-category-choice';
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = `payments[${index}][type]`;
            checkbox.value = item.type;
            checkbox.checked = Boolean(previousPayment);
            checkbox.className = 'payment-category-toggle';
            checkbox.dataset.label = item.label;

            const description = document.createElement('span');
            const label = document.createElement('strong');
            label.textContent = item.label;
            const maximum = document.createElement('small');
            maximum.textContent = `Scheduled: RWF ${formatMoney(item.amount)}`;
            description.append(label, maximum);
            choice.append(checkbox, description);

            const amount = document.createElement('input');
            amount.type = 'number';
            amount.name = `payments[${index}][amount]`;
            amount.className = 'form-control payment-category-amount';
            amount.min = '1';
            amount.step = '1';
            amount.max = String(item.amount);
            amount.required = checkbox.checked;
            amount.disabled = !checkbox.checked;
            amount.value = previousPayment ? previousPayment.amount : '';
            amount.setAttribute('aria-label', `${item.label} amount paid in RWF`);

            const validateAmount = () => {
                amount.setCustomValidity(Number(amount.value || 0) > Number(item.amount)
                    ? 'The payment cannot exceed this fee category total.'
                    : '');
                updatePaymentSummary();
            };
            checkbox.addEventListener('change', function () {
                amount.disabled = !this.checked;
                amount.required = this.checked;
                if (!this.checked) amount.value = '';
                validateAmount();
            });
            amount.addEventListener('input', validateAmount);
            validateAmount();

            row.append(choice, amount);
            paymentRows.appendChild(row);
        });

        updatePaymentSummary();
    }

    function setEmailStatus(message, state) {
        emailStatus.textContent = message;
        emailStatus.className = 'email-status' + (state ? ' is-' + state : '');
    }

    async function checkEmail(force) {
        const normalizedEmail = email.value.trim().toLowerCase();
        email.value = normalizedEmail;
        if (!email.checkValidity()) {
            emailState = 'invalid';
            verifiedEmail = '';
            setEmailStatus(normalizedEmail ? 'Enter a valid email address.' : '', '');
            return false;
        }
        if (!force && emailState === 'available' && verifiedEmail === normalizedEmail) return true;

        if (emailRequest) emailRequest.abort();
        emailRequest = new AbortController();
        const requestId = ++emailRequestId;
        emailState = 'checking';
        verifiedEmail = '';
        setEmailStatus('Checking email availability…', 'checking');

        try {
            const url = new URL(form.dataset.emailUrl, window.location.origin);
            url.searchParams.set('email', normalizedEmail);
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
                signal: emailRequest.signal
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Email availability could not be checked.');
            if (requestId !== emailRequestId) return false;
            emailState = data.available ? 'available' : 'taken';
            verifiedEmail = data.available ? normalizedEmail : '';
            setEmailStatus(data.message, data.available ? 'available' : 'taken');
            return data.available === true;
        } catch (error) {
            if (error.name === 'AbortError' || requestId !== emailRequestId) return false;
            emailState = 'error';
            setEmailStatus(error.message || 'Email availability could not be checked. Try again.', 'error');
            return false;
        } finally {
            if (requestId === emailRequestId) emailRequest = null;
        }
    }

    email.addEventListener('input', function () {
        this.value = this.value.toLowerCase();
        emailState = 'idle';
        verifiedEmail = '';
        emailRequestId++;
        if (emailRequest) emailRequest.abort();
        window.clearTimeout(emailTimer);
        if (!this.value.trim()) {
            setEmailStatus('', '');
            return;
        }
        if (!this.checkValidity()) {
            setEmailStatus('Enter a valid email address.', 'error');
            return;
        }
        emailTimer = window.setTimeout(() => checkEmail(false), 350);
    });
    email.addEventListener('blur', () => {
        email.value = email.value.trim().toLowerCase();
        if (email.value && email.checkValidity()) checkEmail(false);
        persistDraft();
    });
    fullName.addEventListener('blur', function () {
        this.value = this.value.trim().replace(/\s+/g, ' ').replace(/(^|[\s'-])([a-z])/g, (match, separator, letter) => separator + letter.toUpperCase());
        persistDraft();
    });

    async function getJson(url) {
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'The requested information could not be loaded.');
        return data;
    }

    function resetFeeState(message) {
        feeRequestId++;
        feesLoading = false;
        feeItems = [];
        feeBreakdown.hidden = true;
        paymentFields.hidden = true;
        feeError.hidden = true;
        feeLoading.hidden = false;
        feeLoading.querySelector('span').textContent = message;
        paymentRows.replaceChildren();
        paymentTotal.textContent = 'RWF 0';
    }

    async function loadCourses() {
        const requestId = ++courseRequestId;
        const yearId = yearSelect.value;
        const branchId = branchSelect.value;
        courseSelect.disabled = true;
        courseSelect.innerHTML = '<option value="">Loading courses…</option>';
        document.getElementById('courseStatus').textContent = '';
        resetFeeState('Select a course and enrollment term to load the fee schedule.');
        if (!yearId || !branchId) {
            courseSelect.innerHTML = '<option value="">Select academic year and branch first</option>';
            document.getElementById('courseStatus').textContent = 'Choose a year and branch to see available courses.';
            return;
        }

        try {
            const url = new URL(form.dataset.coursesUrl, window.location.origin);
            url.searchParams.set('academic_year_id', yearId);
            url.searchParams.set('branch_id', branchId);
            const courses = await getJson(url);
            if (requestId !== courseRequestId) return;
            courseSelect.innerHTML = '<option value="">Select course</option>';
            courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course.id;
                option.textContent = course.abbreviation ? `${course.course_name} (${course.abbreviation})` : course.course_name;
                courseSelect.appendChild(option);
            });
            courseSelect.disabled = courses.length === 0;
            document.getElementById('courseStatus').textContent = courses.length
                ? `${courses.length} course${courses.length === 1 ? '' : 's'} available for this placement.`
                : 'No courses are configured for this year and branch.';
            if (String(form.dataset.oldCourse || '') && courses.some(course => String(course.id) === form.dataset.oldCourse)) {
                courseSelect.value = form.dataset.oldCourse;
                form.dataset.oldCourse = '';
                await loadFees();
            }
        } catch (error) {
            if (requestId !== courseRequestId) return;
            courseSelect.innerHTML = '<option value="">Courses unavailable</option>';
            document.getElementById('courseStatus').textContent = error.message;
        }
    }

    async function loadTerms() {
        const requestId = ++termRequestId;
        const yearId = yearSelect.value;
        termSelect.disabled = true;
        termSelect.innerHTML = '<option value="">Loading terms…</option>';
        document.getElementById('termStatus').textContent = '';
        resetFeeState('Select a course and enrollment term to load the fee schedule.');
        if (!yearId) {
            termSelect.innerHTML = '<option value="">Select academic year first</option>';
            return;
        }

        try {
            const url = form.dataset.termsUrl.replace('__YEAR__', encodeURIComponent(yearId));
            const terms = await getJson(url);
            if (requestId !== termRequestId) return;
            termSelect.innerHTML = '<option value="">Select enrollment term</option>';
            terms.forEach(term => {
                const option = document.createElement('option');
                option.value = term.id;
                option.textContent = term.name;
                termSelect.appendChild(option);
            });
            termSelect.disabled = terms.length === 0;
            document.getElementById('termStatus').textContent = terms.length
                ? 'Select the term the student is joining.'
                : 'No terms are configured for this academic year.';
            if (String(form.dataset.oldTerm || '') && terms.some(term => String(term.id) === form.dataset.oldTerm)) {
                termSelect.value = form.dataset.oldTerm;
                form.dataset.oldTerm = '';
                await loadFees();
            }
        } catch (error) {
            if (requestId !== termRequestId) return;
            termSelect.innerHTML = '<option value="">Terms unavailable</option>';
            document.getElementById('termStatus').textContent = error.message;
        }
    }

    async function loadFees() {
        resetFeeState('Loading the fee schedule…');
        const requestId = ++feeRequestId;
        const yearId = yearSelect.value;
        const branchId = branchSelect.value;
        const courseId = courseSelect.value;
        const termId = termSelect.value;
        if (!yearId || !branchId || !courseId || !termId) return;

        const url = new URL(form.dataset.feesUrl, window.location.origin);
        url.searchParams.set('academic_year_id', yearId);
        url.searchParams.set('branch_id', branchId);
        url.searchParams.set('course_id', courseId);
        url.searchParams.set('term_id', termId);
        feesLoading = true;
        try {
            const data = await getJson(url);
            if (requestId !== feeRequestId) return;
            feeItems = data.items || [];
            document.getElementById('feeRows').replaceChildren();
            feeItems.forEach(item => {
                const row = document.createElement('tr');
                const name = document.createElement('td');
                const amount = document.createElement('td');
                name.textContent = item.label;
                amount.className = 'text-end';
                amount.textContent = formatMoney(item.amount);
                row.append(name, amount);
                document.getElementById('feeRows').appendChild(row);
            });
            document.getElementById('feeTotal').textContent = formatMoney(data.total);
            document.getElementById('feeScheduleSource').textContent =
                `${data.term} · ${data.source === 'branch-specific' ? 'Branch-specific schedule' : 'Course schedule'}`;
            feeBreakdown.hidden = false;
            feeLoading.hidden = true;
            paymentFields.hidden = false;
            const payableItems = feeItems.filter(item => Number(item.amount) > 0);
            renderPaymentRows(payableItems);
            if (payableItems.length === 0) {
                feeError.textContent = 'The fee schedule has no payable categories. Update the schedule before recording an initial payment.';
                feeError.hidden = false;
                paymentFields.hidden = true;
            }
            if (activeStep === 4) updateReview();
            persistDraft();
        } catch (error) {
            if (requestId !== feeRequestId) return;
            feeLoading.hidden = true;
            feeError.textContent = error.message;
            feeError.hidden = false;
        } finally {
            if (requestId === feeRequestId) feesLoading = false;
        }
    }

    function selectedText(select) {
        return select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : '—';
    }

    function updateReview() {
        document.getElementById('reviewName').textContent = fullName.value.trim();
        document.getElementById('reviewEmail').textContent = email.value.trim().toLowerCase();
        document.getElementById('reviewPhone').textContent = form.elements.phone.value.trim();
        document.getElementById('reviewSex').textContent = selectedText(form.elements.sex);
        document.getElementById('reviewYear').textContent = selectedText(yearSelect);
        document.getElementById('reviewBranch').textContent = selectedText(branchSelect);
        document.getElementById('reviewCourse').textContent = selectedText(courseSelect);
        document.getElementById('reviewTermShift').textContent = `${selectedText(termSelect)} · ${selectedText(form.elements.shift)}`;
        const selectedPayments = getSelectedPayments();
        const totalPaid = selectedPayments.reduce((sum, payment) => sum + Number(payment.amount || 0), 0);
        document.getElementById('reviewPaymentType').textContent = selectedPayments.length
            ? selectedPayments.map(payment => `${payment.label}: RWF ${formatMoney(payment.amount)}`).join(' · ')
            : 'No fee categories selected';
        document.getElementById('reviewAmount').textContent = `RWF ${formatMoney(totalPaid)}`;
    }

    function showStep(nextStep) {
        activeStep = nextStep;
        steps.forEach(step => { step.hidden = Number(step.dataset.step) !== activeStep; });
        progressSteps.forEach(step => {
            const number = Number(step.dataset.progressStep);
            step.classList.toggle('is-current', number === activeStep);
            step.classList.toggle('is-complete', number < activeStep);
            if (number === activeStep) step.setAttribute('aria-current', 'step');
            else step.removeAttribute('aria-current');
        });
        if (activeStep === 4) updateReview();
        document.querySelector('.registration-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
        const heading = steps[activeStep - 1].querySelector('h2');
        if (heading) heading.focus({ preventScroll: true });
        persistDraft();
    }

    async function canContinue() {
        const currentStep = steps[activeStep - 1];
        if (activeStep === 3 && feesLoading) {
            currentStep.querySelector('.registration-status').textContent = 'The fee schedule is still loading. Please wait.';
            return false;
        }
        if (activeStep === 3 && (feeItems.length === 0 || paymentFields.hidden)) {
            feeError.textContent = feeItems.length
                ? 'There is no payable fee category in this schedule.'
                : 'Load a valid fee schedule before continuing.';
            feeError.hidden = false;
            return false;
        }
        const fields = Array.from(currentStep.querySelectorAll('input, select, textarea'));
        for (const field of fields) {
            if (!field.checkValidity()) {
                field.reportValidity();
                return false;
            }
        }
        if (activeStep === 3 && getSelectedPayments().length === 0) {
            currentStep.querySelector('.registration-status').textContent = 'Select at least one fee category and enter the amount received.';
            return false;
        }
        if (activeStep === 1) {
            if (emailState !== 'available' || verifiedEmail !== email.value.trim().toLowerCase()) {
                const available = await checkEmail(true);
                if (!available) {
                    email.focus();
                    return false;
                }
            }
        }
        if (activeStep === 2 && (courseSelect.disabled || termSelect.disabled || !courseSelect.value || !termSelect.value)) {
            currentStep.querySelector('.registration-status').textContent =
                'Select an available course and enrollment term before continuing.';
            return false;
        }
        return true;
    }

    form.querySelectorAll('[data-next]').forEach(button => {
        button.addEventListener('click', async function () {
            const status = this.closest('.registration-step').querySelector('.registration-status');
            status.textContent = '';
            this.disabled = true;
            try {
                if (await canContinue()) showStep(activeStep + 1);
            } finally {
                this.disabled = false;
            }
        });
    });
    form.querySelectorAll('[data-back]').forEach(button => {
        button.addEventListener('click', () => showStep(activeStep - 1));
    });

    yearSelect.addEventListener('change', function () {
        form.dataset.oldCourse = '';
        form.dataset.oldTerm = '';
        loadTerms();
        loadCourses();
    });
    branchSelect.addEventListener('change', function () {
        form.dataset.oldCourse = '';
        loadCourses();
    });
    courseSelect.addEventListener('change', loadFees);
    termSelect.addEventListener('change', loadFees);
    form.addEventListener('input', persistDraft);
    form.addEventListener('change', persistDraft);
    form.querySelector('[data-clear-draft]').addEventListener('click', clearStoredDraft);
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            const invalidField = form.querySelector(':invalid');
            if (invalidField) {
                const owningStep = invalidField.closest('.registration-step');
                if (owningStep) showStep(Number(owningStep.dataset.step));
                invalidField.reportValidity();
            }
            return;
        }
        const submitButton = document.getElementById('submitRegistration');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Registering…';
    });

    if (yearSelect.value) {
        loadTerms();
        if (branchSelect.value) loadCourses();
    }
    if (restoredDraft) showStep(restoredStep);
    else persistDraft();
});
</script>
@endpush
