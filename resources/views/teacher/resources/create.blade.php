@extends('teacher.layout')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="form-container">
    <div class="container">
        <div class="form-wrapper">
            <div class="form-header">
                <h2>
                    <i class="fas fa-cloud-upload-alt"></i>
                    Upload Study Resource
                </h2>
            </div>
            <div class="form-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('teacher.resources.store') }}" id="resourceForm" novalidate>
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="course_id" class="form-label">
                                    <i class="fas fa-graduation-cap"></i>
                                    Course
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
                                    <i class="fas fa-book-open"></i>
                                    Lesson
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
                                    <i class="fas fa-code-branch"></i>
                                    Branch
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

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="youtube_link" class="form-label">
                                    <i class="fab fa-youtube"></i>
                                    YouTube Video Link (Optional)
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
                                <label for="document_name" class="form-label">
                                    <i class="fas fa-link"></i>
                                    Document File Link (Required)
                                </label>
                                <input type="url" name="document_name" id="document_name" class="form-control" placeholder="https://drive.google.com/..." required>
                                @error('document_name')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Paste the link to your document (Google Drive, Dropbox, etc.).</div>
                            </div>
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="submit-btn" id="submitBtn">
                                <i class="fas fa-upload"></i>
                                Upload Resource
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
        docLink: false
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

    document.getElementById('document_name').addEventListener('input', function() {
        validationState.docLink = !!this.value.trim();
        updateSubmitButton();
    });

    function updateSubmitButton() {
        const isValid = validationState.course && validationState.lesson && validationState.docLink;
        submitBtn.disabled = !isValid;
        if (!isValid) {
            submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Please complete the form and provide a document link';
        } else {
            submitBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Resource';
        }
    }

    // Initial button state
    updateSubmitButton();
</script>
@endsection