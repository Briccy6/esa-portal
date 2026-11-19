@extends('dos.layout')

@section('title', 'Edit Resource')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-file-earmark-text me-2"></i>Edit Resource
        </h2>
        <a href="{{ route('dos.resources.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Resources
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <h5 class="alert-heading">
                <i class="bi bi-exclamation-triangle me-2"></i>
                There were errors with your submission:
            </h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Resource - {{ $resource->document_name ?? 'Untitled' }}</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('dos.resources.update', $resource->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Resource Assignment -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Resource Assignment</h6>

                        <div class="mb-3">
                            <label for="teacher_id" class="form-label">Teacher <span class="text-danger">*</span></label>
                            <select name="teacher_id" id="teacher_id" class="form-select" required>
                                <option value="">Select Teacher</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ $resource->teacher_id == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->teacher_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" id="branch_id" class="form-select" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $resource->branch_id == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                            <select name="course_id" id="course_id" class="form-select" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ $resource->course_id == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="lesson_id" class="form-label">Lesson <span class="text-danger">*</span></label>
                            <select name="lesson_id" id="lesson_id" class="form-select" required>
                                @foreach($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" {{ $resource->lesson_id == $lesson->id ? 'selected' : '' }}>
                                        {{ $lesson->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lesson_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Resource Content -->
                    <div class="col-md-6">
                        <h6 class="mb-3">Resource Content</h6>

                        <div class="mb-3">
                            <label for="youtube_link" class="form-label">YouTube Link</label>
                            <input type="url" name="youtube_link" id="youtube_link" class="form-control"
                                   value="{{ $resource->youtube_link }}" placeholder="https://youtube.com/watch?v=...">
                            @error('youtube_link')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="document" class="form-label">Upload New Document</label>
                            <input type="file" name="document" id="document" class="form-control"
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt">
                            <small class="form-text text-muted">
                                Supported formats: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT (Max: 10MB)
                            </small>
                            @if($resource->document_name)
                                <br><small class="text-info">
                                    Current: {{ $resource->document_name }}
                                </small>
                            @endif
                            @error('document')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="document_link" class="form-label">Or Document Link</label>
                            <input type="url" name="document_link" id="document_link" class="form-control"
                                   value="{{ str_starts_with($resource->document_mime ?? '', 'text/url') ? $resource->document_name : '' }}"
                                   placeholder="https://example.com/document.pdf">
                            <small class="form-text text-muted">
                                Use this instead of uploading a file if the document is hosted elsewhere
                            </small>
                            @error('document_link')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="video" class="form-label">Upload New Video</label>
                            <input type="file" name="video" id="video" class="form-control"
                                   accept=".mp4,.avi,.mov,.wmv,.flv">
                            <small class="form-text text-muted">
                                Supported formats: MP4, AVI, MOV, WMV, FLV (Max: 50MB)
                            </small>
                            @if($resource->video_name)
                                <br><small class="text-info">
                                    Current: {{ $resource->video_name }}
                                </small>
                            @endif
                            @error('video')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Resource Management -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="mb-3">Resource Management</h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price ($)</label>
                                    <input type="number" name="price" id="price" class="form-control"
                                           value="{{ number_format($resource->price, 2) }}" step="0.01" min="0" required>
                                    <small class="form-text text-muted">Set to 0.00 for free resources</small>
                                    @error('price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" name="is_public" id="is_public" class="form-check-input" value="1"
                                               {{ $resource->is_public ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_public">
                                            Make Public
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Public resources will be visible to all students.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check form-switch mt-4">
                                        <input type="checkbox" name="is_free" id="is_free" class="form-check-input" value="1"
                                               {{ $resource->is_free ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_free">
                                            Free Resource
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Uncheck to make this a paid resource.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Admin Notes</label>
                            <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3">{{ $resource->admin_notes ?? '' }}</textarea>
                            <small class="form-text text-muted">Internal notes for administrative purposes</small>
                            @error('admin_notes')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Update Resource
                        </button>
                        <a href="{{ route('dos.resources.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle course selection to load lessons
    document.getElementById('course_id').addEventListener('change', function() {
        const courseId = this.value;
        const lessonSelect = document.getElementById('lesson_id');

        if (courseId) {
            fetch(`/api/courses/${courseId}/lessons`)
                .then(response => response.json())
                .then(data => {
                    lessonSelect.innerHTML = '<option value="">Select Lesson</option>';
                    if (data.lessons && data.lessons.length > 0) {
                        data.lessons.forEach(lesson => {
                            lessonSelect.innerHTML += `<option value="${lesson.id}">${lesson.title}</option>`;
                        });
                    }
                })
                .catch(error => {
                    lessonSelect.innerHTML = '<option value="">Error loading lessons</option>';
                });
        }
    });
});
</script>
@endsection
