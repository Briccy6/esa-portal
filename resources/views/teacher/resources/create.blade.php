@extends('teacher.layout')

@section('styles')
<style>
.upload-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 15px;
}

.upload-header h2 {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.upload-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.upload-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
}

.upload-card .card-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.form-label i {
    margin-right: 0.5rem;
    color: #10b981;
}

.form-control, .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.2s ease;
    font-size: 0.95rem;
}

.form-control:focus, .form-select:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-text {
    color: #6b7280;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.error-message {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: none;
}

.error-message.show {
    display: block;
}

.submit-btn {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    color: white;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.2s ease;
    cursor: pointer;
    min-width: 200px;
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(16, 185, 129, 0.3);
}

.submit-btn:disabled {
    background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
    cursor: not-allowed;
    transform: none;
}

.alert {
    border-radius: 10px;
    border: none;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border-left: 4px solid #10b981;
}

.alert-danger {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    color: #991b1b;
    border-left: 4px solid #ef4444;
}

.alert i {
    margin-right: 0.5rem;
}

.upload-area {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.upload-area:hover {
    border-color: #10b981;
    background: #f0fdf4;
}

.upload-icon {
    font-size: 3rem;
    color: #10b981;
    margin-bottom: 1rem;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

@media (max-width: 768px) {
    .upload-header {
        padding: 1.5rem 0;
    }
    
    .upload-header h2 {
        font-size: 1.5rem;
    }
    
    .submit-btn {
        width: 100%;
    }
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="upload-header">
        <div class="container">
            <h2><i class="bi bi-cloud-upload"></i> Upload Study Resource</h2>
            <p class="mb-0">Share educational materials with your students including documents, videos, and YouTube links</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="upload-card">
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('teacher.resources.store') }}" id="resourceForm" enctype="multipart/form-data" novalidate>
                    @csrf

                    <div class="row g-4">
                        <!-- Basic Information Section -->
                        <div class="col-12">
                            <h5 class="section-title"><i class="bi bi-info-circle"></i> Basic Information</h5>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="course_id" class="form-label">
                                    <i class="bi bi-mortarboard"></i> Course
                                </label>
                                <select name="course_id" id="course_id" class="form-select" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lesson_id" class="form-label">
                                    <i class="bi bi-book"></i> Lesson
                                </label>
                                <select name="lesson_id" id="lesson_id" class="form-select" required disabled>
                                    <option value="">Select Lesson</option>
                                </select>
                                @error('lesson_id')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="branch_id" class="form-label">
                                    <i class="bi bi-diagram-3"></i> Branch
                                </label>
                                <select name="branch_id" id="branch_id" class="form-select" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Video Resources Section -->
                        <div class="col-12">
                            <h5 class="section-title"><i class="bi bi-camera-video"></i> Video Resources (Optional)</h5>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="youtube_link" class="form-label">
                                    <i class="bi bi-youtube"></i> YouTube Video Link
                                </label>
                                <input type="url" name="youtube_link" id="youtube_link" class="form-control" placeholder="https://youtube.com/..." pattern="https?://.*">
                                @error('youtube_link')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Paste the YouTube link here. Make sure the video is set to Unlisted or Private.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="video_upload" class="form-label">
                                    <i class="bi bi-film"></i> Upload Video File
                                </label>
                                <div class="upload-area">
                                    <i class="bi bi-cloud-upload upload-icon"></i>
                                    <p class="mb-2">Choose video file or drag and drop</p>
                                    <input type="file" name="video_upload" id="video_upload" class="form-control" accept="video/*" style="border: none; background: transparent; padding: 0;">
                                </div>
                                @error('video_upload')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Upload a video file (MP4, AVI, MOV, etc.). Max size: 100MB.</div>
                            </div>
                        </div>

                        <!-- Document Resources Section -->
                        <div class="col-12">
                            <h5 class="section-title"><i class="bi bi-file-text"></i> Document Resources (Required)</h5>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="document_upload" class="form-label">
                                    <i class="bi bi-file-earmark-arrow-up"></i> Upload Document File
                                </label>
                                <div class="upload-area">
                                    <i class="bi bi-cloud-upload upload-icon"></i>
                                    <p class="mb-2">Choose document file or drag and drop</p>
                                    <input type="file" name="document_upload" id="document_upload" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.xls,.xlsx" required style="border: none; background: transparent; padding: 0;">
                                </div>
                                @error('document_upload')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Upload your document file (PDF, Word, PowerPoint, Excel, etc.).</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="document_name" class="form-label">
                                    <i class="bi bi-link-45deg"></i> Document File Link (Alternative)
                                </label>
                                <input type="url" name="document_name" id="document_name" class="form-control" placeholder="https://drive.google.com/...">
                                @error('document_name')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> 
                                    Paste the link to your document (Google Drive, Dropbox, etc.) - Use this OR upload file above.
                                </div>
                            </div>
                        </div>

                        <!-- Submit Section -->
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="submit-btn" id="submitBtn">
                                <i class="bi bi-cloud-upload"></i> Upload Resource
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Courses and lessons data
    const coursesData = @json($courses);
    const courseSelect = document.getElementById('course_id');
    const lessonSelect = document.getElementById('lesson_id');
    const submitBtn = document.getElementById('submitBtn');

    let validationState = {
        course: false,
        lesson: false,
        docFileOrLink: false
    };

    // Populate lessons when course changes
    courseSelect.addEventListener('change', function() {
        const selectedCourseId = parseInt(this.value);
        lessonSelect.innerHTML = '<option value="">Select Lesson</option>';
        lessonSelect.disabled = true;
        validationState.lesson = false;

        if (!selectedCourseId) {
            validationState.course = false;
            updateSubmitButton();
            return;
        }

        const course = coursesData.find(c => c.id === selectedCourseId);
        if (course && course.lessons && course.lessons.length > 0) {
            course.lessons.forEach(lesson => {
                lessonSelect.innerHTML += `<option value="${lesson.id}">${lesson.title}</option>`;
            });
            lessonSelect.disabled = false;
            validationState.course = true;
        } else {
            validationState.course = false;
        }
        updateSubmitButton();
    });

    lessonSelect.addEventListener('change', function() {
        validationState.lesson = !!this.value;
        updateSubmitButton();
    });

    document.getElementById('document_upload').addEventListener('change', function() {
        validateDocFileOrLink();
    });

    document.getElementById('document_name').addEventListener('input', function() {
        validateDocFileOrLink();
    });

    function validateDocFileOrLink() {
        const docFile = document.getElementById('document_upload').files.length > 0;
        const docLink = document.getElementById('document_name').value.trim();
        validationState.docFileOrLink = docFile || docLink;
        updateSubmitButton();
    }

    function updateSubmitButton() {
        const isValid = validationState.course && validationState.lesson && validationState.docFileOrLink;
        submitBtn.disabled = !isValid;
        if (!isValid) {
            submitBtn.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Please complete the form and provide either a document file or link';
        } else {
            submitBtn.innerHTML = '<i class="bi bi-cloud-upload"></i> Upload Resource';
        }
    }

    // Initial button state
    updateSubmitButton();
</script>
@endsection