@extends('admin.layout')

@section('title', 'Edit Document')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Document - {{ $resource->document_name ?? 'Untitled' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.documents.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.documents.update', $resource->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Document Info -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Teacher</label>
                                    <p class="form-control-static">{{ $resource->teacher->teacher_name }}</p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Course</label>
                                    <p class="form-control-static">{{ $resource->course->course_name }}</p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Branch</label>
                                    <p class="form-control-static">{{ $resource->branch->name }}</p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Lesson</label>
                                    <p class="form-control-static">{{ $resource->lesson->title ?? 'N/A' }}</p>
                                </div>
                            </div>
                            
                            <!-- Management Settings -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price">Price ($)</label>
                                    <input type="number" name="price" id="price" class="form-control" 
                                           value="{{ $resource->price }}" step="0.01" min="0" required>
                                    <small class="form-text text-muted">Set to 0.00 for free documents</small>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="is_public" id="is_public" class="custom-control-input" 
                                               {{ $resource->is_public ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_public">
                                            Make Public
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Public documents will be visible to all students. Watermark will be applied automatically.
                                    </small>
                                </div>
                                
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="is_free" id="is_free" class="custom-control-input" 
                                               {{ $resource->is_free ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_free">
                                            Free Document
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Uncheck to make this a paid document.
                                    </small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="admin_notes">Admin Notes</label>
                                    <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3">{{ $resource->admin_notes ?? '' }}</textarea>
                                    <small class="form-text text-muted">Internal notes for administrative purposes</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Document Preview -->
                        @if($resource->document_name)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Document Preview</h5>
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
                                        <a href="{{ route('admin.documents.download', $resource->id) }}" class="btn btn-success btn-sm">
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
                                    <i class="fas fa-save"></i> Update Document
                                </button>
                                <a href="{{ route('admin.documents.index') }}" class="btn btn-secondary">
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
    // Preview document
    window.previewDocument = function() {
        $('#previewFrame').attr('src', '{{ route('admin.documents.preview', $resource->id) }}');
        $('#previewModal').modal('show');
    };
    
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
