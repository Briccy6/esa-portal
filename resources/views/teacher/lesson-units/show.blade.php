@extends('teacher.layout')

@section('title', 'Manage Lesson Units')

@section('content')
<style>
    .lesson-units-manage {
        --primary: #1e40af;
        --primary-light: #3b82f6;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --bg-card: #ffffff;
        --bg-hover: #f8fafc;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        color: var(--text-secondary);
        margin: 0;
    }

    .action-bar {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
    }

    .action-bar-form {
        flex: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .form-group {
        flex: 1;
        min-width: 200px;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.875rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-light);
        color: white;
    }

    .btn-outline {
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary);
        color: white;
    }

    .btn-danger {
        background: #dc2626;
        color: white;
    }

    .btn-danger:hover {
        background: #b91c1c;
        color: white;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
    }

    .units-table {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        background: var(--bg-hover);
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-primary);
        border-bottom: 1px solid var(--border-color);
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: var(--bg-hover);
    }

    .file-link {
        color: var(--primary);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .file-link:hover {
        text-decoration: underline;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .csv-section {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .csv-section h4 {
        margin-bottom: 1rem;
        color: var(--text-primary);
    }
</style>

<div class="container py-4 lesson-units-manage">
    <div class="page-header">
        <a href="{{ route('teacher.lesson-units.index') }}" class="btn btn-outline btn-sm mb-2">
            <i class="bi bi-arrow-left"></i> Back to Lessons
        </a>
        <h1 class="page-title">{{ $lesson->title }}</h1>
        <p class="page-subtitle">Manage units for this lesson</p>
    </div>

    <!-- Add Unit Form -->
    <div class="action-bar">
        <form method="POST" action="{{ route('teacher.lesson-units.store') }}" class="action-bar-form">
            @csrf
            <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
            
            <div class="form-group">
                <label class="form-label">Unit Name</label>
                <input type="text" name="unit_name" class="form-control" required placeholder="e.g., Introduction to Physics">
            </div>
            
            <div class="form-group">
                <label class="form-label">Hours</label>
                <input type="number" name="hours" class="form-control" required min="1" placeholder="e.g., 2">
            </div>
            
            <div class="form-group">
                <label class="form-label">File (optional)</label>
                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.csv">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Unit
            </button>
        </form>
    </div>

    <!-- Units Table -->
    @if($lesson->units->isEmpty())
        <div class="empty-state">
            <i class="bi bi-folder"></i>
            <h3>No Units Yet</h3>
            <p>Add your first unit using the form above or import from CSV.</p>
        </div>
    @else
        <div class="units-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Unit Name</th>
                        <th>Hours</th>
                        <th>File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lesson->units as $unit)
                        <tr>
                            <td>{{ $unit->unit_name }}</td>
                            <td>{{ $unit->hours }} hour{{ $unit->hours !== 1 ? 's' : '' }}</td>
                            <td>
                                @if($unit->file_path)
                                    <a href="{{ route('teacher.lesson-units.download', $unit->id) }}" class="file-link" target="_blank">
                                        <i class="bi bi-file-earmark"></i> Download
                                    </a>
                                @else
                                    <span class="text-muted">No file</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline btn-sm" onclick="editUnit({{ $unit->id }}, '{{ $unit->unit_name }}', {{ $unit->hours }})">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('teacher.lesson-units.destroy', $unit->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this unit?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- CSV Import/Export -->
    <div class="csv-section">
        <h4>CSV Import/Export</h4>
        <div class="d-flex flex-wrap gap-3">
            <form method="POST" action="{{ route('teacher.lesson-units.import') }}" enctype="multipart/form-data" style="display: inline;">
                @csrf
                <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                <input type="file" name="csv_file" accept=".csv" required>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Import CSV
                </button>
            </form>
            <a href="{{ route('teacher.lesson-units.export', $lesson->id) }}" class="btn btn-outline">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
        <small class="text-muted d-block mt-2">
            CSV format: unit_name, hours, file_path (optional). Example: "Introduction to Physics", 2, "path/to/file.pdf"
        </small>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="editUnitForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="unit_id" id="editUnitId">
                    <div class="mb-3">
                        <label class="form-label">Unit Name</label>
                        <input type="text" name="unit_name" id="editUnitName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hours</label>
                        <input type="number" name="hours" id="editUnitHours" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File (optional)</label>
                        <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.csv">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUnit(id, name, hours) {
    document.getElementById('editUnitId').value = id;
    document.getElementById('editUnitName').value = name;
    document.getElementById('editUnitHours').value = hours;
    document.getElementById('editUnitForm').action = '{{ route('teacher.lesson-units.update', '') }}/' + id;
    new bootstrap.Modal(document.getElementById('editUnitModal')).show();
}
</script>
@endsection
