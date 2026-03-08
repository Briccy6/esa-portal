@extends('admin.layout')

@section('title', 'Upload Resource')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upload New Resource</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.resources.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Resources
                        </a>
                    </div>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-exclamation-triangle"></i> 
                            There were errors with your submission:
                        </h5>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif
                
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.resources.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Resource Assignment -->
                            <div class="col-md-6">
                                <h5>Resource Assignment</h5>
                                
                                <div class="form-group">
                                    <label for="teacher_id">Teacher <span class="text-danger">*</span></label>
                                    <select name="teacher_id" id="teacher_id" class="form-control" required>
                                        <option value="">Select Teacher</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->teacher_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('teacher_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="branch_id">Branch <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id" class="form-control" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="course_id">Course <span class="text-danger">*</span></label>
                                    <select name="course_id" id="course_id" class="form-control" required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="lesson_id">Lesson <span class="text-danger">*</span></label>
                                    <select name="lesson_id" id="lesson_id" class="form-control" required>
                                        <option value="">Select Course First</option>
                                    </select>
                                    @error('lesson_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Resource Content -->
                            <div class="col-md-6">
                                <h5>Resource Content</h5>
                                
                                <div class="form-group">
                                    <label for="youtube_link">YouTube Link</label>
                                    <input type="url" name="youtube_link" id="youtube_link" class="form-control" 
                                           placeholder="https://youtube.com/watch?v=...">
                                    @error('youtube_link')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="document">Upload Document</label>
                                    <input type="file" name="document" id="document" class="form-control" 
                                           accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt">
                                    <small class="form-text text-muted">
                                        Supported formats: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT (Max: 10MB)
                                    </small>
                                    @error('document')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="document_link">Or Document Link</label>
                                    <input type="url" name="document_link" id="document_link" class="form-control" 
                                           placeholder="https://example.com/document.pdf">
                                    <small class="form-text text-muted">
                                        Use this instead of uploading a file if the document is hosted elsewhere
                                    </small>
                                    @error('document_link')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="video">Upload Video</label>
                                    <input type="file" name="video" id="video" class="form-control" 
                                           accept=".mp4,.avi,.mov,.wmv,.flv">
                                    <small class="form-text text-muted">
                                        Supported formats: MP4, AVI, MOV, WMV, FLV (Max: 50MB)
                                    </small>
                                    @error('video')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resource Management -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Resource Management</h5>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price">Price ($)</label>
                                            <input type="number" name="price" id="price" class="form-control" 
                                                   value="0.00" step="0.01" min="0" required>
                                            <small class="form-text text-muted">Set to 0.00 for free resources</small>
                                            @error('price')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="hidden" name="is_public" value="0">
                                            <div class="custom-control custom-switch mt-4">
                                                <input type="checkbox" name="is_public" id="is_public" class="custom-control-input" value="1">
                                                <label class="custom-control-label" for="is_public">
                                                    Make Public
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">
                                                Public resources will be visible to all students. Watermark will be applied automatically.
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input type="hidden" name="is_free" value="0">
                                            <div class="custom-control custom-switch mt-4">
                                                <input type="checkbox" name="is_free" id="is_free" class="custom-control-input" value="1" checked>
                                                <label class="custom-control-label" for="is_free">
                                                    Free Resource
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">
                                                Uncheck to make this a paid resource.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="admin_notes">Admin Notes</label>
                                    <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3"></textarea>
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
                                    <i class="fas fa-upload"></i> Upload Resource
                                </button>
                                <a href="{{ route('admin.resources.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Handle course selection to load lessons
    $('#course_id').change(function() {
        var courseId = $(this).val();
        var lessonSelect = $('#lesson_id');
        
        if (courseId) {
            $.ajax({
                url: '{{ route("admin.resources.get-lessons", ":courseId") }}'.replace(':courseId', courseId),
                method: 'GET',
                success: function(data) {
                    lessonSelect.empty();
                    lessonSelect.append('<option value="">Select Lesson</option>');
                    
                    if (data.lessons && data.lessons.length > 0) {
                        $.each(data.lessons, function(index, lesson) {
                            lessonSelect.append('<option value="' + lesson.id + '">' + lesson.title + '</option>');
                        });
                    }
                },
                error: function() {
                    lessonSelect.empty();
                    lessonSelect.append('<option value="">Error loading lessons</option>');
                }
            });
        } else {
            lessonSelect.empty();
            lessonSelect.append('<option value="">Select Course First</option>');
        }
    });
    
    // Handle free/paid relationship
    $('#is_free').change(function() {
        if ($(this).is(':checked')) {
            $('#price').val('0.00');
        }
    });
    
    $('#price').change(function() {
        if ($(this).val() > 0) {
            $('#is_free').prop('checked', false);
        } else {
            $('#is_free').prop('checked', true);
        }
    });
});
</script>
@endsection
