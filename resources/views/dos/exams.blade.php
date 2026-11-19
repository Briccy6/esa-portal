@extends('staff.partials.sidebar_dos')

@section('title', 'Exams Management')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-clipboard-data-fill me-2"></i>
            Exams Management
        </h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="try { exportExams(); } catch(e) { console.error('Export exams error:', e); alert('Unable to export exams. Please try again.'); }">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button class="btn btn-primary" href="{{ route('dos.exams.create') }}">
                <i class="bi bi-plus-circle me-1"></i> Create Exam
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $exams->count() ?? 0 }}</h4>
                            <p class="card-text">Total Exams</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clipboard-data fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $exams->where('is_published', 1)->count() ?? 0 }}</h4>
                            <p class="card-text">Published</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $exams->where('is_published', 0)->count() ?? 0 }}</h4>
                            <p class="card-text">Draft</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-pencil fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $exams->where('is_public', 1)->count() ?? 0 }}</h4>
                            <p class="card-text">Public Exams</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-globe fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dos.exams.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="branch_filter" class="form-label">Branch</label>
                        <select name="branch" id="branch_filter" class="form-select">
                            <option value="">All Branches</option>
                            @if(isset($branches))
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label">Status</label>
                        <select name="status" id="status_filter" class="form-select">
                            <option value="">All Status</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="type_filter" class="form-label">Type</label>
                        <select name="type" id="type_filter" class="form-select">
                            <option value="">All Types</option>
                            <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>Public</option>
                            <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Private</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('dos.exams.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Exams Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Exam Title</th>
                            <th>Course</th>
                            <th>Questions</th>
                            <th>Duration</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($exams) && $exams->count() > 0)
                            @foreach($exams as $exam)
                                <tr>
                                    <td>
                                        <strong>{{ $exam->title }}</strong>
                                        @if($exam->description)
                                            <br><small class="text-muted">{{ Str::limit($exam->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $exam->course->course_name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $exam->questions->count() ?? 0 }}</span>
                                        @if($exam->questions->count() > 0)
                                            <br><small class="text-muted">{{ $exam->total_marks ?? 0 }} marks</small>
                                        @endif
                                    </td>
                                    <td>{{ $exam->duration ?? 'N/A' }} min</td>
                                    <td>
                                        @if($exam->is_public)
                                            <span class="badge bg-primary">Public</span>
                                        @else
                                            <span class="badge bg-secondary">Private</span>
                                        @endif
                                        @if($exam->public_price > 0)
                                            <br><small class="text-muted">RWF {{ number_format($exam->public_price, 0) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($exam->is_published)
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td>{{ $exam->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary"
                                                    onclick="try { viewExam({{ $exam->id }}); } catch(e) { console.error('View exam error:', e); alert('Unable to view exam. Please try again.'); }">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info"
                                                    onclick="try { viewQuestions({{ $exam->id }}); } catch(e) { console.error('View questions error:', e); alert('Unable to view questions. Please try again.'); }">
                                                <i class="bi bi-list-ul"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning"
                                                    onclick="try { editExam({{ $exam->id }}); } catch(e) { console.error('Edit exam error:', e); alert('Unable to edit exam. Please try again.'); }">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @if($exam->is_published)
                                                <button type="button" class="btn btn-outline-success"
                                                        onclick="try { previewExam({{ $exam->id }}); } catch(e) { console.error('Preview exam error:', e); alert('Unable to preview exam. Please try again.'); }">
                                                    <i class="bi bi-play-circle"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-outline-danger"
                                                    onclick="try { deleteExam({{ $exam->id }}); } catch(e) { console.error('Delete exam error:', e); alert('Unable to delete exam. Please try again.'); }">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-clipboard-x fs-1"></i>
                                        <p class="mt-2">No exams found</p>
                                        <a href="{{ route('dos.exams.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i> Create First Exam
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function viewExam(id) {
    window.location.href = `/dos/exams/${id}`;
}

function viewQuestions(id) {
    window.location.href = `/dos/exams/${id}/questions`;
}

function editExam(id) {
    window.location.href = `/dos/exams/${id}/edit`;
}

function previewExam(id) {
    window.open(`/admin/exams/${id}/preview`, '_blank');
}

function deleteExam(id) {
    if(confirm('Are you sure you want to delete this exam? This action cannot be undone.')) {
        // Add CSRF token
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/dos/exams/${id}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

function exportExams() {
    window.location.href = `/dos/exams/export`;
}
</script>
@endsection
