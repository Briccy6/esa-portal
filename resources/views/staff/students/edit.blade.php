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
    
    .edit-header h2 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
    }
    
    .edit-body {
        padding: 40px;
    }
    
    .section-title {
        color: #667eea;
        font-size: 1.3rem;
        font-weight: 600;
        margin: 30px 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }
    
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    
    .form-control, .form-select {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .file-upload-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }
    
    .file-upload-wrapper input[type=file] {
        font-size: 100px;
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        cursor: pointer;
    }
    
    .file-upload-label {
        display: block;
        padding: 15px;
        background: #f8f9fa;
        border: 2px dashed #667eea;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .file-upload-label:hover {
        background: #e9ecef;
        border-color: #764ba2;
    }
    
    .preview-container {
        margin-top: 10px;
        text-align: center;
    }
    
    .preview-image {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .alert-custom {
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .required::after {
        content: ' *';
        color: #dc3545;
    }
    
    .info-badge {
        display: inline-block;
        background: #e3f2fd;
        color: #1976d2;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-top: 5px;
    }
</style>

<div class="edit-container">
    <div class="container">
        <div class="edit-card">
            <div class="edit-header">
                <h2><i class="fas fa-user-edit me-2"></i>Edit Student Information</h2>
                <p class="mb-0 mt-2">{{ $student->full_name }} - {{ $student->reg_number }}</p>
            </div>
            
            <div class="edit-body">
                @if(session('success'))
                    <div class="alert alert-success alert-custom alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Success!</strong> {{ session('success') }}
                        @if(session('updated_fields'))
                            <div class="mt-2">
                                <small>Updated fields: {{ implode(', ', session('updated_fields')) }}</small>
                            </div>
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-custom alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error!</strong> Please fix the following issues:
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <form action="{{ route('staff.students.update', $student) }}" method="POST" enctype="multipart/form-data" id="studentEditForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <h5 class="section-title"><i class="fas fa-user me-2"></i>Basic Information</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Full Name</label>
                            <input type="text" name="full_name" class="form-control" 
                                   value="{{ old('full_name', $student->full_name) }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="{{ old('email', $student->email) }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Phone</label>
                            <input type="text" name="phone" class="form-control" 
                                   value="{{ old('phone', $student->phone) }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Discount (%)</label>
                            <input type="number" name="discount" class="form-control" 
                                   value="{{ old('discount', $student->discount) }}" 
                                   min="0" max="100" step="0.01">
                            <small class="text-muted">Leave blank for no discount</small>
                        </div>
                    </div>
                    
                    <!-- Academic Information -->
                    <h5 class="section-title"><i class="fas fa-graduation-cap me-2"></i>Academic Information</h5>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
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
                        
                        <div class="col-md-4 mb-3">
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
                        
                        <div class="col-md-4 mb-3">
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
                    </div>
                    
                    <!-- Identification -->
                    <h5 class="section-title"><i class="fas fa-id-card me-2"></i>Identification</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Identification Type</label>
                            <select name="identification_type" class="form-select">
                                <option value="">Select Type</option>
                                <option value="ID" {{ old('identification_type', $student->identification_type) == 'ID' ? 'selected' : '' }}>
                                    National ID
                                </option>
                                <option value="Passport" {{ old('identification_type', $student->identification_type) == 'Passport' ? 'selected' : '' }}>
                                    Passport
                                </option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ID/Passport Number</label>
                            <input type="text" name="id_number" class="form-control" 
                                   value="{{ old('id_number', $student->id_number) }}">
                        </div>
                    </div>
                    
                    <!-- Photo Uploads -->
                    <h5 class="section-title"><i class="fas fa-camera me-2"></i>Photos</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ID Photo</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-label" for="id_photo">
                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                    <div>Click to upload ID photo</div>
                                    <small class="text-muted">JPG, PNG (Max: 2MB)</small>
                                </label>
                                <input type="file" name="id_photo" id="id_photo" accept="image/*">
                            </div>
                            <div class="preview-container" id="id_photo_preview">
                                @if($student->id_photo)
                                    <img src="{{ asset('storage/' . $student->id_photo) }}" 
                                         class="preview-image" alt="Current ID Photo">
                                    <div class="info-badge mt-2">Current Photo</div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Passport Photo</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-label" for="passport_photo">
                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                    <div>Click to upload passport photo</div>
                                    <small class="text-muted">JPG, PNG (Max: 2MB)</small>
                                </label>
                                <input type="file" name="passport_photo" id="passport_photo" accept="image/*">
                            </div>
                            <div class="preview-container" id="passport_photo_preview">
                                @if($student->passport_photo)
                                    <img src="{{ asset('storage/' . $student->passport_photo) }}" 
                                         class="preview-image" alt="Current Passport Photo">
                                    <div class="info-badge mt-2">Current Photo</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Document Uploads -->
                    <h5 class="section-title"><i class="fas fa-file-alt me-2"></i>Academic Documents</h5>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Senior 6 Certificate</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-label" for="senior6_document">
                                    <i class="fas fa-file-upload fa-2x mb-2"></i>
                                    <div>Upload S6 Certificate</div>
                                    <small class="text-muted">PDF, JPG, PNG (Max: 2MB)</small>
                                </label>
                                <input type="file" name="senior6_document" id="senior6_document" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            @if($student->senior6_document)
                                <div class="info-badge mt-2">
                                    <i class="fas fa-check-circle"></i> Document uploaded
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Senior 3 Certificate</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-label" for="senior3_document">
                                    <i class="fas fa-file-upload fa-2x mb-2"></i>
                                    <div>Upload S3 Certificate</div>
                                    <small class="text-muted">PDF, JPG, PNG (Max: 2MB)</small>
                                </label>
                                <input type="file" name="senior3_document" id="senior3_document" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            @if($student->senior3_document)
                                <div class="info-badge mt-2">
                                    <i class="fas fa-check-circle"></i> Document uploaded
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Other Supporting Document</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-label" for="other_supporting_document">
                                    <i class="fas fa-file-upload fa-2x mb-2"></i>
                                    <div>Upload Document</div>
                                    <small class="text-muted">PDF, JPG, PNG (Max: 2MB)</small>
                                </label>
                                <input type="file" name="other_supporting_document" id="other_supporting_document" 
                                       accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            @if($student->other_supporting_document)
                                <div class="info-badge mt-2">
                                    <i class="fas fa-check-circle"></i> Document uploaded
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="text-center mt-4">
                        <a href="{{ route('students.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview functionality
document.addEventListener('DOMContentLoaded', function() {
    function setupImagePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        
        if (input) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <img src="${e.target.result}" class="preview-image" alt="Preview">
                            <div class="info-badge mt-2">New Upload Preview</div>
                        `;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }
    
    setupImagePreview('id_photo', 'id_photo_preview');
    setupImagePreview('passport_photo', 'passport_photo_preview');
    
    // Form validation
    const form = document.getElementById('studentEditForm');
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    });
});
</script>
@endsection