@extends('teacher.layout')

@section('styles')
<style>
.exams-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    margin-top: 1rem;
    border-radius: 15px;
    position: relative;
    z-index: 1;
}

.exams-header h1 {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.exams-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.exams-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
    position: relative;
    z-index: 1;
}

.exams-card .card-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    padding: 1.5rem;
    z-index: 1;
    position: relative;
}

.filters-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
    position: relative;
    z-index: 1;
}

.filters-card .card-header {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border: none;
    padding: 1.5rem;
    position: relative;
    z-index: 1;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.exams-table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    position: relative;
    z-index: 1;
}

.exams-table .table {
    margin-bottom: 0;
    table-layout: auto;
}

.exams-table .table thead {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.exams-table .table thead th {
    border: none;
    padding: 1.25rem 1rem;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    white-space: nowrap;
    text-align: left;
}

.exams-table .table tbody td {
    padding: 1.25rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #e5e7eb;
    font-size: 0.95rem;
}

.exams-table .table tbody tr {
    transition: all 0.2s ease;
}

.exams-table .table tbody tr:hover {
    background-color: #f0f9ff;
}

.exams-table .table tbody tr:last-child td {
    border-bottom: none;
}

/* Column width adjustments */
.exams-table .table th:nth-child(1),
.exams-table .table td:nth-child(1) {
    min-width: 200px;
    width: 25%;
}

.exams-table .table th:nth-child(2),
.exams-table .table td:nth-child(2) {
    min-width: 150px;
    width: 20%;
}

.exams-table .table th:nth-child(3),
.exams-table .table td:nth-child(3) {
    min-width: 100px;
    width: 12%;
}

.exams-table .table th:nth-child(4),
.exams-table .table td:nth-child(4) {
    min-width: 100px;
    width: 12%;
}

.exams-table .table th:nth-child(5),
.exams-table .table td:nth-child(5) {
    min-width: 80px;
    width: 10%;
    text-align: center;
}

.exams-table .table th:nth-child(6),
.exams-table .table td:nth-child(6) {
    min-width: 90px;
    width: 12%;
    text-align: center;
}

.exams-table .table th:nth-child(7),
.exams-table .table td:nth-child(7) {
    min-width: 200px;
    width: 9%;
    text-align: right;
}

.badge-custom {
    padding: 0.5rem 0.875rem;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
    white-space: nowrap;
    margin-right: 0.5rem;
    margin-bottom: 0.375rem;
}

.badge-info {
    background: #06b6d4;
    color: white;
}

.badge-success {
    background: #10b981;
    color: white;
}

.badge-warning {
    background: #f59e0b;
    color: white;
}

.badge-secondary {
    background: #6366f1;
    color: white;
}

.btn-action {
    padding: 0.625rem 1rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s ease;
    border: none;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    margin-right: 0.5rem;
}

.btn-view {
    background: #3b82f6;
    color: white;
}

.btn-view:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    color: white;
    text-decoration: none;
}

.btn-edit {
    background: #6366f1;
    color: white;
}

.btn-edit:hover {
    background: #4f46e5;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    color: white;
    text-decoration: none;
}

.btn-questions {
    background: #06b6d4;
    color: white;
}

.btn-questions:hover {
    background: #0891b2;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
    color: white;
    text-decoration: none;
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    color: white;
    text-decoration: none;
}

.btn-primary-custom {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
    color: white;
}

.btn-outline-custom {
    border: 2px solid #3b82f6;
    color: #3b82f6;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-outline-custom:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border-color: transparent;
    transform: translateY(-1px);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 15px;
    border: 2px dashed #e5e7eb;
}

.empty-state i {
    color: #d1d5db;
    margin-bottom: 1rem;
}

.pagination-info {
    color: #6b7280;
    font-size: 0.875rem;
}

.container-fluid {
    padding-top: 1rem !important;
    position: relative;
    z-index: 1;
}

.table-responsive {
    min-height: auto;
}

.table {
    font-size: 0.95rem;
}

.table thead th {
    background-color: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    color: #1e293b;
}

.table tbody tr {
    border-bottom: 1px solid #e2e8f0;
}

.table tbody tr:last-child {
    border-bottom: none;
}

@media (max-width: 768px) {
    .exams-table .table th {
        font-size: 0.75rem;
        padding: 0.5rem;
    }
    
    .exams-table .table td {
        font-size: 0.8rem;
        padding: 0.5rem;
    }
    
    .btn-action {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>
@endsection

@section('content')
<div class="container-fluid" style="padding-top: 2rem;">
    <!-- Header -->
    <div class="exams-header" style="margin-top: 0;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-clipboard-data"></i> My Exams</h1>
                    <p class="mb-0">View and manage question banks for examinations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="container">
        <div class="filters-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Search Exams</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="liveSearch" class="form-label">Search by Exam Title, Course</label>
                        <input type="text" id="liveSearch" class="form-control" 
                               placeholder="Type to search..." autocomplete="off">
                    </div>
                    <div class="col-md-6">
                        <label for="courseFilter" class="form-label">Filter by Course</label>
                        <select id="courseFilter" class="form-select">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exams Table -->
    <div class="container">
        <div class="exams-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> All Exams</h5>
            </div>
            <div class="card-body p-0">
                @if($exams->count() > 0)
                    <div class="table-responsive exams-table">
                        <table class="table table-hover" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Course</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Marks</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exams as $exam)
                                    <tr>
                                        <td>
                                            <strong>{{ $exam->title }}</strong>
                                            @if($exam->description)
                                                <br><small class="text-muted">{{ Str::limit($exam->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($exam->courses->count() > 0)
                                                @foreach($exam->courses->take(2) as $course)
                                                    <span class="badge badge-custom badge-secondary">{{ $course->course_name }}</span>
                                                @endforeach
                                                @if($exam->courses->count() > 2)
                                                    <span class="badge badge-custom badge-secondary">+{{ $exam->courses->count() - 2 }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">No course</span>
                                            @endif
                                        </td>
                                        <td>{{ $exam->class_date->format('M d, Y') }}</td>
                                        <td><span class="badge badge-custom badge-info">{{ $exam->exam_type }}</span></td>
                                        <td><strong>{{ $exam->total_marks }}</strong></td>
                                        <td>
                                            @if($exam->is_public)
                                                <span class="badge badge-custom badge-success">Public</span>
                                            @else
                                                <span class="badge badge-custom badge-warning">Private</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-action btn-view" title="View Exam">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <a href="{{ route('teacher.exams.questions', $exam) }}" class="btn btn-action btn-questions" title="Add Questions">
                                                    <i class="bi bi-plus-circle"></i> Add Questions
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div class="pagination-info">
                            Showing <span id="resultCount">0</span> of {{ $exams->total() }} exams
                        </div>
                        <div id="paginationContainer"></div>
                    </div>
                @else
                    <div class="empty-state m-4">
                        <i class="bi bi-clipboard-x display-1"></i>
                        <h4 class="mt-4">No Exams Found</h4>
                        <p class="text-muted mb-4">You don't have any exams available yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Store all exams data
    const allExams = @json($exams->items());
    
    const searchInput = document.getElementById('liveSearch');
    const courseFilter = document.getElementById('courseFilter');
    const tbody = document.querySelector('.exams-table tbody');
    
    if (!searchInput || !tbody) return;
    
    // Debounce search
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func(...args), wait);
        };
    }
    
    // Filter and render exams
    function filterAndRender() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const courseId = courseFilter.value;
        
        // Filter exams based on search and course
        const filtered = allExams.filter(exam => {
            const matchesSearch = !searchTerm || 
                exam.title.toLowerCase().includes(searchTerm) ||
                exam.subject.toLowerCase().includes(searchTerm) ||
                (exam.courses && exam.courses.some(c => c.course_name.toLowerCase().includes(searchTerm))) ||
                exam.exam_type.toLowerCase().includes(searchTerm);
            
            const matchesCourse = !courseId || 
                (exam.courses && exam.courses.some(c => c.id == courseId));
            
            return matchesSearch && matchesCourse;
        });
        
        // Clear table
        tbody.innerHTML = '';
        
        if (filtered.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `<td colspan="7" class="text-center py-4 text-muted">
                <i class="bi bi-search"></i> No exams found matching your search
            </td>`;
            tbody.appendChild(emptyRow);
            document.getElementById('resultCount').textContent = '0';
            return;
        }
        
        // Render filtered exams
        filtered.forEach(exam => {
            const courseBadges = exam.courses && exam.courses.length > 0 ? 
                exam.courses.slice(0, 2).map(c => 
                    `<span class="badge badge-custom badge-secondary">${c.course_name}</span>`
                ).join('') : '<span class="text-muted">No course</span>';
            
            const extraCourses = exam.courses && exam.courses.length > 2 ? 
                `<span class="badge badge-custom badge-secondary">+${exam.courses.length - 2} More</span>` : '';
            
            const examType = exam.exam_type || 'Unknown';
            
            const statusBadge = exam.is_public ? 
                '<span class="badge badge-custom badge-success">Public</span>' :
                '<span class="badge badge-custom badge-warning">Private</span>';
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <strong style="color: #1f2937; font-size: 0.95rem;">${exam.title}</strong>
                    ${exam.description ? `<div style="font-size: 0.85rem; color: #6b7280; margin-top: 0.25rem;">${exam.description.substring(0, 60)}</div>` : ''}
                </td>
                <td>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.375rem;">
                        ${courseBadges} ${extraCourses}
                    </div>
                </td>
                <td>
                    <span style="color: #374151; font-weight: 500;">${new Date(exam.class_date).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</span>
                </td>
                <td>
                    <span class="badge badge-custom badge-info">${examType.toUpperCase()}</span>
                </td>
                <td style="text-align: center;">
                    <span style="font-weight: 700; color: #1f2937; font-size: 1rem;">${exam.total_marks}</span>
                </td>
                <td style="text-align: center;">
                    ${statusBadge}
                </td>
                <td style="text-align: right;">
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; flex-wrap: wrap;">
                        <a href="/teacher/exams/${exam.id}" class="btn btn-action btn-view" title="View Exam Details">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <a href="/teacher/exams/${exam.id}/questions" class="btn btn-action btn-questions" title="Add Questions to This Exam">
                            <i class="bi bi-plus-circle"></i> Add Q
                        </a>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
        
        document.getElementById('resultCount').textContent = filtered.length;
    }
    
    // Event listeners
    searchInput.addEventListener('input', debounce(filterAndRender, 300));
    courseFilter.addEventListener('change', filterAndRender);
    
    // Initial render
    filterAndRender();
});
</script>
@endsection
