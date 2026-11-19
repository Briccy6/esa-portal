@extends('teacher.layout')

@section('styles')
<style>
.upload-header {
    background: #1e40af;
    color: white;
    padding: 1.5rem 0;
    margin-bottom: 1.5rem;
    border-radius: 12px;
}

.upload-header h2 {
    font-weight: 700;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.upload-header p {
    opacity: 0.9;
    margin-bottom: 0;
    font-size: 0.95rem;
}

.upload-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.upload-card .card-header {
    background: #1e40af;
    color: white;
    border: none;
    padding: 1.25rem;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-label {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.form-label i {
    margin-right: 0.5rem;
    color: #1e40af;
}

.form-control, .form-select {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.625rem 0.875rem;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}

.form-control:focus, .form-select:focus {
    border-color: #1e40af;
    box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
}

.form-text {
    color: #64748b;
    font-size: 0.8125rem;
    margin-top: 0.25rem;
}

.error-message {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8125rem;
    margin-top: 0.25rem;
    display: none;
}

.error-message.show {
    display: block;
}

.submit-btn {
    background: #1e40af;
    border: none;
    color: white;
    padding: 0.875rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
    cursor: pointer;
    min-width: 180px;
}

.submit-btn:hover:not(:disabled) {
    background: #1e3a8a;
    transform: translateY(-1px);
}

.submit-btn:disabled {
    background: #94a3b8;
    cursor: not-allowed;
    transform: none;
}

.alert {
    border-radius: 8px;
    border: none;
    padding: 1rem 1.25rem;
    margin-bottom: 1.25rem;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
}

.alert i {
    margin-right: 0.5rem;
}

.upload-area {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 8px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.2s ease;
    margin-bottom: 0.75rem;
}

.upload-area:hover {
    border-color: #1e40af;
    background: #eff6ff;
}

.upload-icon {
    font-size: 2.5rem;
    color: #1e40af;
    margin-bottom: 0.75rem;
}

.section-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e2e8f0;
}

@media (max-width: 768px) {
    .upload-header {
        padding: 1.25rem 0;
    }

    .upload-header h2 {
        font-size: 1.25rem;
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
                                <label for="branch_id" class="form-label">
                                    <i class="bi bi-diagram-3"></i> Branch *
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
                                <div class="form-text">Select the branch first to see available courses</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="course_id" class="form-label">
                                    <i class="bi bi-mortarboard"></i> Course *
                                </label>
                                <select name="course_id" id="course_id" class="form-select" required disabled>
                                    <option value="">Select Course</option>
                                </select>
                                @error('course_id')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Select a course in the chosen branch</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lesson_id" class="form-label">
                                    <i class="bi bi-book"></i> Lesson *
                                </label>
                                <select name="lesson_id" id="lesson_id" class="form-select" required disabled>
                                    <option value="">Select Lesson</option>
                                </select>
                                @error('lesson_id')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Select a lesson in the chosen course</div>
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
    // Data from controller
    const branchCourseData = @json($branchCourseData);
    const branchLessonData = @json($branchLessonData);
    const coursesData = @json($courses);
    const lessonsData = @json($lessons);

    const branchSelect = document.getElementById('branch_id');
    const courseSelect = document.getElementById('course_id');
    const lessonSelect = document.getElementById('lesson_id');
    const submitBtn = document.getElementById('submitBtn');

    let validationState = {
        branch: false,
        course: false,
        lesson: false,
        docFileOrLink: false
    };

    // Helper function to find course by ID
    function getCourseById(courseId) {
        return coursesData.find(c => c.id === courseId);
    }

    // Helper function to find lesson by ID
    function getLessonById(lessonId) {
        return lessonsData.find(l => l.id === lessonId);
    }

    // When branch changes, update courses
    branchSelect.addEventListener('change', function() {
        const selectedBranchId = parseInt(this.value);
        
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        courseSelect.disabled = true;
        lessonSelect.innerHTML = '<option value="">Select Lesson</option>';
        lessonSelect.disabled = true;
        
        validationState.branch = false;
        validationState.course = false;
        validationState.lesson = false;

        if (!selectedBranchId) {
            updateSubmitButton();
            return;
        }

        // Get courses for this branch
        const courseIds = branchCourseData[selectedBranchId];
        if (courseIds && courseIds.length > 0) {
            courseIds.forEach(courseId => {
                const course = getCourseById(courseId);
                if (course) {
                    courseSelect.innerHTML += `<option value="${course.id}">${course.course_name}</option>`;
                }
            });
            courseSelect.disabled = false;
            validationState.branch = true;
        }
        updateSubmitButton();
    });

    // When course changes, update lessons
    courseSelect.addEventListener('change', function() {
        const selectedBranchId = parseInt(branchSelect.value);
        const selectedCourseId = parseInt(this.value);

        lessonSelect.innerHTML = '<option value="">Select Lesson</option>';
        lessonSelect.disabled = true;
        validationState.course = false;
        validationState.lesson = false;

        if (!selectedCourseId) {
            updateSubmitButton();
            return;
        }

        // Get lessons for this branch and course
        const key = `${selectedBranchId}_${selectedCourseId}`;
        const lessons = branchLessonData[key];
        
        if (lessons && lessons.length > 0) {
            lessons.forEach(lesson => {
                lessonSelect.innerHTML += `<option value="${lesson.id}">${lesson.title}</option>`;
            });
            lessonSelect.disabled = false;
            validationState.course = true;
        }
        updateSubmitButton();
    });

    // When lesson changes
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
        const isValid = validationState.branch && validationState.course && validationState.lesson && validationState.docFileOrLink;
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