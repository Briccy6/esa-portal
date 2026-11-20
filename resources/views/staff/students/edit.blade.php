@extends('staff.partials.sidebar_sec')

@section('content')
<style>
    .edit-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 30px 0;
    }
    .edit-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        max-width: 1200px;
        margin: 0 auto;
        overflow: hidden;
    }
    .edit-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }
    .edit-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
    .edit-body { padding: 40px; }
    .section-title {
        color: #667eea;
        font-size: 1.3rem;
        font-weight: 600;
        margin: 30px 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }
    .section-title.nesa { color: #059669; border-color: #059669; }
    .form-label { font-weight: 600; color: #333; margin-bottom: 8px; }
    .form-control, .form-select {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
    }
    .file-upload-wrapper { position: relative; overflow: hidden; display: inline-block; width: 100%; }
    .file-upload-wrapper input[type=file] { font-size: 100px; position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer; }
    .file-upload-label {
        display: block; padding: 15px;
        background: #f8f9fa;
        border: 2px dashed #667eea;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .file-upload-label:hover { background: #e9ecef; border-color: #764ba2; }
    .file-upload-label.nesa-upload { border-color: #059669; }
    .file-upload-label.nesa-upload:hover { border-color: #047857; }
    .preview-container { margin-top: 10px; text-align: center; }
    .preview-image { max-width: 200px; max-height: 200px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white; border: none;
        padding: 15px 40px; border-radius: 8px;
        font-size: 1.1rem; font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102,126,234,0.4); color: white; }
    .btn-nesa {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white; border: none;
        padding: 12px 35px; border-radius: 8px;
        font-size: 1rem; font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-nesa:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(5,150,105,0.4); color: white; }
    .required::after { content: ' *'; color: #dc3545; }
    .info-badge {
        display: inline-block; background: #e3f2fd; color: #1976d2;
        padding: 4px 12px; border-radius: 20px; font-size: 0.82rem; margin-top: 5px;
    }
    .nesa-badge {
        display: inline-block; background: #d1fae5; color: #065f46;
        padding: 4px 12px; border-radius: 20px; font-size: 0.82rem; margin-top: 5px;
    }
    .nesa-status-box {
        border-radius: 12px; padding: 15px 20px; margin-bottom: 10px;
    }
    .nesa-registered { background: #d1fae5; border: 1px solid #6ee7b7; }
    .nesa-not-registered { background: #fef3c7; border: 1px solid #fcd34d; }
    .alert-custom { border-radius: 10px; padding: 15px 20px; margin-bottom: 20px; }
</style>

<div class="edit-container">
    <div class="container">
        <div class="edit-card">
            <div class="edit-header">
                <h2><i class="fas fa-user-edit me-2"></i>Edit Student Information</h2>
                <p class="mb-0 mt-2 opacity-80">{{ $student->full_name }} — {{ $student->reg_number }}</p>
            </div>

            <div class="edit-body">

                {{-- Alerts --}}
                @if(session('success'))
                    <div class="alert alert-success alert-custom alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><strong>Success!</strong> {{ session('success') }}
                        @if(session('updated_fields'))
                            <div class="mt-1"><small>Updated: {{ implode(', ', session('updated_fields')) }}</small></div>
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-custom alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-custom alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i><strong>Please fix:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- ════════════════════════════════════════════
                     MAIN STUDENT EDIT FORM
                ════════════════════════════════════════════ --}}
                <form action="{{ route('staff.students.update', $student) }}" method="POST"
                      enctype="multipart/form-data" id="studentEditForm">
                    @csrf
                    @method('PUT')

                    {{-- Basic Information --}}
                    <h5 class="section-title"><i class="fas fa-user me-2"></i>Basic Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Full Name</label>
                            <input type="text" name="full_name" class="form-control"
                                   value="{{ old('full_name', $student->full_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Sex</label>
                            <select name="sex" class="form-select" required>
                                <option value="">Select Sex</option>
                                <option value="Male"   {{ old('sex', $student->sex) == 'Male'   ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex', $student->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other"  {{ old('sex', $student->sex) == 'Other'  ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $student->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $student->phone) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Discount (%)</label>
                            <input type="number" name="discount" class="form-control"
                                   value="{{ old('discount', $student->discount) }}" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Education Level</label>
                            <select name="education_level" class="form-select">
                                <option value="">Select Level</option>
                                <option value="Senior 3" {{ old('education_level', $student->education_level) == 'Senior 3' ? 'selected' : '' }}>Senior 3</option>
                                <option value="Senior 6" {{ old('education_level', $student->education_level) == 'Senior 6' ? 'selected' : '' }}>Senior 6</option>
                                <option value="Other"    {{ old('education_level', $student->education_level) == 'Other'    ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    {{-- Academic Information --}}
                    <h5 class="section-title"><i class="fas fa-graduation-cap me-2"></i>Academic Information</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label required">Branch</label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                            {{ old('branch_id', $student->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Course</label>
                            <select name="course_id" class="form-select" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}"
                                            {{ old('course_id', $student->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Academic Year</label>
                            <select name="academic_year_id" class="form-select" required>
                                <option value="">Select Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}"
                                            {{ old('academic_year_id', $student->academic_year_id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Shift</label>
                            <select name="shift" class="form-select" required>
                                <option value="">Select Shift</option>
                                <option value="day" {{ old('shift', $student->shift ?? 'day') == 'day' ? 'selected' : '' }}>Day</option>
                                <option value="evening" {{ old('shift', $student->shift) == 'evening' ? 'selected' : '' }}>Evening</option>
                                <option value="weekend" {{ old('shift', $student->shift) == 'weekend' ? 'selected' : '' }}>Weekend</option>
                            </select>
                        </div>
                    </div>

                    {{-- Identification --}}
                    <h5 class="section-title"><i class="fas fa-id-card me-2"></i>Identification</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Identification Type</label>
                            <select name="identification_type" class="form-select">
                                <option value="">Select Type</option>
                                <option value="ID"       {{ old('identification_type', $student->identification_type) == 'ID'       ? 'selected' : '' }}>National ID</option>
                                <option value="Passport" {{ old('identification_type', $student->identification_type) == 'Passport' ? 'selected' : '' }}>Passport</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ID / Passport Number</label>
                            <input type="text" name="id_number" class="form-control"
                                   value="{{ old('id_number', $student->id_number) }}">
                        </div>
                    </div>

                    {{-- Photos --}}
                    <h5 class="section-title"><i class="fas fa-camera me-2"></i>Photos</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ID Photo</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-label" for="id_photo">
                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                    <div>Click to upload ID photo</div>
                                    <small class="text-muted">JPG, PNG — max 2 MB</small>
                                </label>
                                <input type="file" name="id_photo" id="id_photo" accept="image/*">
                            </div>
                            <div class="preview-container" id="id_photo_preview">
                                @if($student->id_photo)
                                    <img src="{{ asset('storage/' . $student->id_photo) }}" class="preview-image" alt="ID Photo">
                                    <div class="info-badge mt-1"><i class="fas fa-check-circle"></i> Current photo</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Passport Photo</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-label" for="passport_photo">
                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                    <div>Click to upload passport photo</div>
                                    <small class="text-muted">JPG, PNG — max 2 MB</small>
                                </label>
                                <input type="file" name="passport_photo" id="passport_photo" accept="image/*">
                            </div>
                            <div class="preview-container" id="passport_photo_preview">
                                @if($student->passport_photo)
                                    <img src="{{ asset('storage/' . $student->passport_photo) }}" class="preview-image" alt="Passport Photo">
                                    <div class="info-badge mt-1"><i class="fas fa-check-circle"></i> Current photo</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Academic Documents --}}
                    <h5 class="section-title"><i class="fas fa-file-alt me-2"></i>Academic Documents</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Senior 6 Certificate</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-label" for="senior6_document">
                                    <i class="fas fa-file-upload fa-2x mb-2"></i>
                                    <div>Upload S6 Certificate</div>
                                    <small class="text-muted">PDF, JPG, PNG — max 2 MB</small>
                                </label>
                                <input type="file" name="senior6_document" id="senior6_document" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            @if($student->senior6_document)
                                <div class="info-badge mt-1"><i class="fas fa-check-circle"></i> Document uploaded</div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Senior 3 Certificate</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-label" for="senior3_document">
                                    <i class="fas fa-file-upload fa-2x mb-2"></i>
                                    <div>Upload S3 Certificate</div>
                                    <small class="text-muted">PDF, JPG, PNG — max 2 MB</small>
                                </label>
                                <input type="file" name="senior3_document" id="senior3_document" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            @if($student->senior3_document)
                                <div class="info-badge mt-1"><i class="fas fa-check-circle"></i> Document uploaded</div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Other Supporting Document</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-label" for="other_supporting_document">
                                    <i class="fas fa-file-upload fa-2x mb-2"></i>
                                    <div>Upload Document</div>
                                    <small class="text-muted">PDF, JPG, PNG — max 2 MB</small>
                                </label>
                                <input type="file" name="other_supporting_document" id="other_supporting_document" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            @if($student->other_supporting_document)
                                <div class="info-badge mt-1"><i class="fas fa-check-circle"></i> Document uploaded</div>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="text-center mt-5">
                        <a href="{{ route('students.index') }}" class="btn btn-secondary me-3 px-4">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-submit" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>

                <hr class="my-5">

                {{-- ════════════════════════════════════════════
                     NESA REGISTRATION SECTION (separate form)
                ════════════════════════════════════════════ --}}
                <h5 class="section-title nesa">
                    <i class="fas fa-university me-2"></i>NESA Registration
                </h5>

                {{-- Current NESA status display --}}
                @if($student->nesa_registered)
                    <div class="nesa-status-box nesa-registered mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                            <div>
                                <strong class="text-success d-block">Registered with NESA</strong>
                                <small class="text-muted">
                                    SDMS Code: <strong>{{ $student->nesa_sdms_code ?? '—' }}</strong> &nbsp;|&nbsp;
                                    NESA Reg #: <strong>{{ $student->nesa_reg_number ?? '—' }}</strong> &nbsp;|&nbsp;
                                    Date: <strong>{{ $student->nesa_registration_date ? \Carbon\Carbon::parse($student->nesa_registration_date)->format('d M Y') : '—' }}</strong>
                                </small>
                                @if($student->nesa_remarks)
                                    <small class="text-muted d-block">Remarks: {{ $student->nesa_remarks }}</small>
                                @endif
                                @if($student->nesa_document)
                                    <a href="{{ asset('storage/' . $student->nesa_document) }}"
                                       target="_blank" class="btn btn-sm btn-outline-success mt-2">
                                        <i class="fas fa-file-alt me-1"></i>View Proof Document
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="nesa-status-box nesa-not-registered mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                            <div>
                                <strong class="text-warning d-block">Not yet registered with NESA</strong>
                                <small class="text-muted">Fill in the form below once the student has been registered.</small>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('staff.students.nesa', $student) }}" method="POST"
                      enctype="multipart/form-data" id="nesaForm">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label required">Registered with NESA?</label>
                            <select name="nesa_registered" class="form-select" id="nesaRegisteredSelect" required>
                                <option value="0" {{ !$student->nesa_registered ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $student->nesa_registered  ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>

                        <div id="nesaFields" style="{{ $student->nesa_registered ? '' : 'display:none;' }}">
                            <div class="row g-3 mt-0">
                                <div class="col-md-3">
                                    <label class="form-label required">SDMS Code</label>
                                    <input type="text" name="nesa_sdms_code" class="form-control"
                                           value="{{ old('nesa_sdms_code', $student->nesa_sdms_code) }}"
                                           placeholder="e.g. 12345678">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">NESA Registration Number</label>
                                    <input type="text" name="nesa_reg_number" class="form-control"
                                           value="{{ old('nesa_reg_number', $student->nesa_reg_number) }}"
                                           placeholder="e.g. NESA/2024/001">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required">Registration Date</label>
                                    <input type="date" name="nesa_registration_date" class="form-control"
                                           value="{{ old('nesa_registration_date', $student->nesa_registration_date ? \Carbon\Carbon::parse($student->nesa_registration_date)->format('Y-m-d') : '') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Remarks</label>
                                    <input type="text" name="nesa_remarks" class="form-control"
                                           value="{{ old('nesa_remarks', $student->nesa_remarks) }}"
                                           placeholder="Optional notes">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Proof / Registration Document</label>
                                    <div class="file-upload-wrapper">
                                        <label class="file-upload-label nesa-upload" for="nesa_document">
                                            <i class="fas fa-file-upload fa-2x mb-2 text-success"></i>
                                            <div>Upload NESA proof document</div>
                                            <small class="text-muted">PDF, JPG, PNG — max 5 MB</small>
                                        </label>
                                        <input type="file" name="nesa_document" id="nesa_document"
                                               accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                    @if($student->nesa_document)
                                        <div class="nesa-badge mt-1">
                                            <i class="fas fa-check-circle"></i> Document already uploaded
                                            <a href="{{ asset('storage/' . $student->nesa_document) }}" target="_blank" class="ms-2 text-success">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-nesa" id="nesaSubmitBtn">
                            <i class="fas fa-university me-2"></i>Save NESA Registration
                        </button>
                    </div>
                </form>

            </div>{{-- /edit-body --}}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Image preview ────────────────────────────────────────────
    function setupPreview(inputId, previewId) {
        const input   = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;
        input.addEventListener('change', function () {
            const file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.innerHTML = `
                        <img src="${e.target.result}" class="preview-image" alt="Preview">
                        <div class="info-badge mt-1">New Upload Preview</div>`;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    setupPreview('id_photo', 'id_photo_preview');
    setupPreview('passport_photo', 'passport_photo_preview');

    // ── NESA fields toggle ───────────────────────────────────────────────
    const nesaSelect = document.getElementById('nesaRegisteredSelect');
    const nesaFields = document.getElementById('nesaFields');
    nesaSelect.addEventListener('change', function () {
        nesaFields.style.display = this.value === '1' ? '' : 'none';
    });

    // ── Loading states ───────────────────────────────────────────────────
    document.getElementById('studentEditForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…';
    });

    document.getElementById('nesaForm').addEventListener('submit', function () {
        const btn = document.getElementById('nesaSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving NESA…';
    });
});
</script>
@endsection