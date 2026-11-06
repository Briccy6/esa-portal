@extends('dos.layout')

@section('content')
<style>
    .index-container {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
        padding: 30px 0;
    }
    .index-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        margin: 0 auto;
        overflow: hidden;
    }
    .index-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }
    .index-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
    .index-body { padding: 40px; }
    
    .teachers-table {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .teachers-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .teachers-table thead th {
        border: none;
        padding: 15px 12px;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .teachers-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f0f0f0;
    }
    .teachers-table tbody tr:hover {
        background-color: #f8f9ff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    }
    .teachers-table tbody td {
        padding: 15px 12px;
        vertical-align: middle;
        border-color: #f0f0f0;
    }
    
    .course-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        margin: 2px;
    }
    
    .branch-badge {
        display: inline-block;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        margin: 2px;
    }
    
    .lesson-badge {
        display: inline-block;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
        margin: 2px;
    }
    
    .assignment-item {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        margin-bottom: 8px;
    }
</style>

<div class="index-container">
    <div class="container">
        <div class="index-card">
            <div class="index-header">
                <h2><i class="fas fa-chalkboard-teacher me-2"></i>Teachers Management</h2>
                <p class="mb-0 mt-1 opacity-75">View teachers and their course/lesson assignments</p>
            </div>

            <div class="index-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">All Teachers</h4>
                    <div>
                        <a href="{{ route('dos.lessons.index') }}" class="btn btn-primary">
                            <i class="fas fa-book me-2"></i>Back to Lessons
                        </a>
                    </div>
                </div>

                <!-- Live Search -->
                <div class="search-container mb-3" style="margin-bottom: 20px;">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="teacherSearch" placeholder="Search teachers by name, course, branch, lesson...">
                        <span class="input-group-text text-muted small"><span id="teacherCount">{{ $teachers->count() }}</span> teachers</span>
                    </div>
                </div>

                @if($teachers->count() > 0)
                    <div class="table-responsive" id="tableContainer">
                        <table class="table teachers-table">
                            <thead>
                                <tr>
                                    <th>Teacher Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Assigned Courses & Branches</th>
                                    <th>Lesson Assignments</th>
                                </tr>
                            </thead>
                            <tbody id="teacherTableBody">
                                @foreach($teachers as $teacher)
                                    @php
                                        $searchText = strtolower($teacher->teacher_name . ' ' . 
                                            $teacher->email . ' ' . 
                                            ($teacher->phone ?? '') . ' ' .
                                            $teacher->courses->pluck('course_name')->implode(' ') . ' ' .
                                            $teacher->branches->pluck('name')->implode(' '));
                                        
                                        foreach($teacher->lesson_assignments as $lessonGroup) {
                                            foreach($lessonGroup as $assignment) {
                                                $searchText .= ' ' . ($assignment->lesson->title ?? '') . ' ' .
                                                    ($assignment->course->course_name ?? '') . ' ' .
                                                    ($assignment->branch->name ?? '');
                                            }
                                        }
                                    @endphp
                                    <tr class="teacher-row" data-search-text="{{ $searchText }}">
                                        <td>
                                            <strong>{{ $teacher->teacher_name }}</strong>
                                        </td>
                                        <td>{{ $teacher->email }}</td>
                                        <td>{{ $teacher->phone ?? 'N/A' }}</td>
                                        <td>
                                            @if($teacher->courses->count() > 0)
                                                @foreach($teacher->courses as $course)
                                                    <span class="course-badge">{{ $course->course_code ?? $course->course_name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No courses assigned</span>
                                            @endif
                                            
                                            @if($teacher->branches->count() > 0)
                                                <div class="mt-2">
                                                    @foreach($teacher->branches as $branch)
                                                        <span class="branch-badge">{{ $branch->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($teacher->lesson_assignments->count() > 0)
                                                @foreach($teacher->lesson_assignments as $lessonGroup)
                                                    @foreach($lessonGroup as $assignment)
                                                        <div class="assignment-item">
                                                            <span class="lesson-badge">{{ $assignment->lesson->title ?? 'N/A' }}</span>
                                                            <span class="course-badge" style="font-size: 0.75rem;">{{ $assignment->course->course_code ?? $assignment->course->course_name }}</span>
                                                            <span class="branch-badge" style="font-size: 0.75rem;">{{ $assignment->branch->name ?? 'N/A' }}</span>
                                                        </div>
                                                    @endforeach
                                                @endforeach
                                            @else
                                                <span class="text-muted">No lesson assignments</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <h4>No Teachers Found</h4>
                        <p>There are currently no teachers in the system.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('teacherSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.teacher-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const searchText = row.getAttribute('data-search-text');
        if (searchText.includes(searchTerm)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('teacherCount').textContent = visibleCount;
});
</script>
@endsection
