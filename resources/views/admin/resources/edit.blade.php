@extends('admin.layout')

@section('title', 'Edit Resource')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Resource - {{ $resource->document_name ?? 'Untitled' }}</h3>
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
                    <form method="POST" action="{{ route('admin.resources.update', $resource->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Resource Assignment -->
                            <div class="col-md-6">
                                <h5>Resource Assignment</h5>
                                
                                <div class="form-group">
                                    <label for="teacher_id">Teacher <span class="text-danger">*</span></label>
                                    <select name="teacher_id" id="teacher_id" class="form-control" required>
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
                                
                                <div class="form-group">
                                    <label for="branch_id">Branch <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id" class="form-control" required>
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
                                
                                <div class="form-group">
                                    <label for="course_id">Course <span class="text-danger">*</span></label>
                                    <select name="course_id" id="course_id" class="form-control" required>
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
                                
                                <div class="form-group">
                                    <label for="lesson_id">Lesson <span class="text-danger">*</span></label>
                                    <select name="lesson_id" id="lesson_id" class="form-control" required>
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
                                <h5>Resource Content</h5>
                                
                                <div class="form-group">
                                    <label for="youtube_link">YouTube Link</label>
                                    <input type="url" name="youtube_link" id="youtube_link" class="form-control" 
                                           value="{{ $resource->youtube_link }}" placeholder="https://youtube.com/watch?v=...">
                                    @error('youtube_link')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="document">Upload New Document</label>
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
                                
                                <div class="form-group">
                                    <label for="document_link">Or Document Link</label>
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
                                
                                <div class="form-group">
                                    <label for="video">Upload New Video</label>
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
                                <h5>Resource Management</h5>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price">Price ($)</label>
                                            <input type="number" name="price" id="price" class="form-control" 
                                                   value="{{ number_format($resource->price, 2) }}" step="0.01" min="0" required>
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
                                                <input type="checkbox" name="is_public" id="is_public" class="custom-control-input" value="1" 
                                                       {{ $resource->is_public ? 'checked' : '' }}>
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
                                                <input type="checkbox" name="is_free" id="is_free" class="custom-control-input" value="1" 
                                                       {{ $resource->is_free ? 'checked' : '' }}>
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
                                    <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3">{{ $resource->admin_notes ?? '' }}</textarea>
                                    <small class="form-text text-muted">Internal notes for administrative purposes</small>
                                    @error('admin_notes')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Current Resource Preview -->
                        @if($resource->document_name && !str_starts_with($resource->document_mime ?? '', 'text/url'))
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Current Document Preview</h5>
                                <div class="border p-3">
                                    <p><strong>File:</strong> {{ $resource->document_name }}</p>
                                    <p><strong>Type:</strong> {{ $resource->document_mime }}</p>
                                    <p><strong>Watermark Status:</strong> 
                                        @if($resource->watermark_applied)
                                            <span class="badge badge-success">Applied</span>
                                        @else
                                            <span class="badge badge-warning">Not Applied</span>
                                        @endif
                                    </p>
                                    
                                    <div class="btn-group mt-2">
                                        <button type="button" class="btn btn-info btn-sm" onclick="previewDocument()">
                                            <i class="fas fa-eye"></i> Preview
                                        </button>
                                        <a href="{{ route('admin.resources.download', $resource->id) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Resource
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

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Preview</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="previewFrame" style="width: 100%; height: 500px; border: none;"></iframe>
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
    
    // Preview document
    window.previewDocument = function() {
        $('#previewFrame').attr('src', '{{ route('admin.resources.preview', $resource->id) }}');
        $('#previewModal').modal('show');
    };
});
</script>
@endsection
