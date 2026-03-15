
@extends('teacher.layout')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="form-container">
    <div class="container">
        <div class="form-wrapper">
            <div class="form-header">
                <h2>
                    <i class="fas fa-edit"></i>
                    Edit Resource
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

                <form method="POST" action="{{ route('teacher.resources.update', $resource->id) }}" id="resourceForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

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
                                        <option value="{{ $course->id }}" {{ $resource->course_id == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
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
                                <select name="lesson_id" id="lesson_id" class="form-select" required>
                                    <option value="">Select Lesson</option>
                                    @foreach($lessons as $lesson)
                                        <option value="{{ $lesson->id }}" {{ $resource->lesson_id == $lesson->id ? 'selected' : '' }}>
                                            {{ $lesson->title }}
                                        </option>
                                    @endforeach
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
                                        <option value="{{ $branch->id }}" {{ $resource->branch_id == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
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
                                <input type="url" name="youtube_link" id="youtube_link" class="form-control"
                                    value="{{ old('youtube_link', $resource->youtube_link) }}"
                                    placeholder="https://youtube.com/..." pattern="https?://.*">
                                @error('youtube_link')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Paste the YouTube link here. Make sure the video is set to Unlisted or Private.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="video_upload" class="form-label">
                                    <i class="fas fa-video"></i>
                                    Upload Video File (Optional)
                                </label>
                                <input type="file" name="video_upload" id="video_upload" class="form-control" accept="video/*">
                                @if($resource->video_name)
                                    <div class="form-text">Current video: {{ $resource->video_name }} (Leave empty to keep current video)</div>
                                @else
                                    <div class="form-text">Upload a video file (MP4, AVI, MOV, etc.). Max size: 100MB.</div>
                                @endif
                                @error('video_upload')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="document_upload" class="form-label">
                                    <i class="fas fa-file-upload"></i>
                                    Upload Document File (Optional)
                                </label>
                                <input type="file" name="document_upload" id="document_upload" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.xls,.xlsx">
                                @if($resource->document_blob && $resource->document_name)
                                    <div class="form-text">
                                        Current document: <a href="{{ route('teacher.resources.viewDocument', $resource->id) }}" target="_blank">{{ $resource->document_name }}</a> (Leave empty to keep current document)
                                    </div>
                                @else
                                    <div class="form-text">Upload your document file (PDF, Word, PowerPoint, Excel, etc.).</div>
                                @endif
                                @error('document_upload')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="document_name" class="form-label">
                                    <i class="fas fa-link"></i>
                                    Document File Link (Alternative to upload)
                                </label>
                                <input type="url" name="document_name" id="document_name" class="form-control"
                                    value="{{ old('document_name', $resource->document_name) }}"
                                    placeholder="https://drive.google.com/...">
                                @error('document_name')
                                    <div class="error-message show">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Paste the link to your document (Google Drive, Dropbox, etc.) - Use this OR upload file above.</div>
                            </div>
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="submit-btn" id="submitBtn">
                                <i class="fas fa-save"></i>
                                Update Resource
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection