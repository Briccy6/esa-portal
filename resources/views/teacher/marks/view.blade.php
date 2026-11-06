@extends('teacher.layout')

@section('styles')
<style>
.marks-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 15px;
}

.marks-header h4 {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.marks-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.marks-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
}

.marks-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.marks-table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.marks-table .table {
    margin-bottom: 0;
}

.marks-table .table-dark {
    background: linear-gradient(135deg, #4c51bf 0%, #667eea 100%);
}

.marks-table .table-dark th {
    border: none;
    padding: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.marks-table .table td {
    padding: 0.875rem;
    vertical-align: middle;
    border-bottom: 1px solid #e5e7eb;
}

.marks-table .table tbody tr:hover {
    background-color: #f8fafc;
}

.mark-display {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-block;
    min-width: 60px;
    text-align: center;
}

.stats-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stats-number.primary { color: #667eea; }
.stats-number.info { color: #0ea5e9; }
.stats-number.success { color: #10b981; }
.stats-number.warning { color: #f59e0b; }

.stats-label {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
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

.btn-primary-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(102, 126, 234, 0.3);
    color: white;
}

.table-responsive {
    border-radius: 10px;
    overflow: hidden;
}

@media (max-width: 768px) {
    .marks-table .table th {
        font-size: 0.75rem;
        padding: 0.5rem;
    }
    
    .marks-table .table td {
        font-size: 0.8rem;
        padding: 0.5rem;
    }
    
    .stats-number {
        font-size: 1.5rem;
    }
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="marks-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4><i class="bi bi-journal-text"></i> Recorded Marks</h4>
                    <p class="mb-0">View and manage student marks across different lessons and assessments</p>
                </div>
                <a href="{{ route('teacher.marks.index') }}" class="btn btn-light">
                    <i class="bi bi-plus-circle"></i> Record New Marks
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="marks-card">
            <div class="card-body p-4">
                <!-- Lesson Filter -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="student-search" class="form-label fw-bold">Search Student</label>
                        <input type="text" id="student-search" class="form-control" placeholder="Type student name to search...">
                    </div>
                    <div class="col-md-3">
                        <label for="course-filter" class="form-label fw-bold">Select Course</label>
                        <select id="course-filter" class="form-select">
                            <option value="">All Courses</option>
                            @php $coursesSeen = []; @endphp
                            @foreach($students as $studentId => $studentInfo)
                                @if(!isset($coursesSeen[$studentInfo['course']]))
                                    @php $coursesSeen[$studentInfo['course']] = true; @endphp
                                    <option value="{{ $studentInfo['course'] }}">{{ $studentInfo['course'] }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="lesson-filter" class="form-label fw-bold">Select Lesson</label>
                        <select id="lesson-filter" class="form-select">
                            <option value="">All Lessons</option>
                            @foreach($groupedMarks as $termId => $termData)
                                @foreach($termData['lessons'] as $lessonId => $lessonData)
                                    <option value="{{ $lessonId }}">{{ $lessonData['title'] }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="term-filter" class="form-label fw-bold">Select Term</label>
                        <select id="term-filter" class="form-select">
                            <option value="">All Terms</option>
                            @foreach($groupedMarks as $termId => $termData)
                                <option value="{{ $termId }}">{{ $termData['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Filtered Lesson Table -->
                <div id="lesson-marks-container">
                    @if(count($groupedMarks) > 0 && count($students) > 0)
                        @foreach($groupedMarks as $termId => $termData)
                            @foreach($termData['lessons'] as $lessonId => $lessonData)
                                <!-- Lesson Section -->
                                <div class="lesson-section mb-4" data-term-id="{{ $termId }}" data-lesson-id="{{ $lessonId }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="text-primary mb-0">
                                            <i class="bi bi-journal-text me-2"></i>{{ $lessonData['title'] }}
                                        </h4>
                                        <span class="badge bg-info text-white">{{ $termData['name'] }}</span>
                                    </div>
                            
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover marks-table">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th rowspan="2" class="align-middle">Student Name</th>
                                                    <th rowspan="2" class="align-middle">Course</th>
                                                    <th rowspan="2" class="align-middle">Branch</th>
                                                    @foreach($lessonData['dates'] as $date => $dateMarks)
                                                        <th colspan="1" class="text-center">
                                                            @if($date === 'no-date')
                                                                <small>No Date</small>
                                                            @else
                                                                <small>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</small>
                                                            @endif
                                                        </th>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    @foreach($lessonData['dates'] as $date => $dateMarks)
                                                        <th class="text-center" style="min-width: 120px;">
                                                            @if($date === 'no-date')
                                                                <small>No Date</small>
                                                            @else
                                                                <small>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</small>
                                                            @endif
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($students as $studentId => $studentInfo)
                                                    <tr>
                                                        <td class="align-middle">
                                                            <strong>{{ $studentInfo['name'] ?? $studentInfo['reg_number'] ?? $studentId }}</strong>
                                                        </td>
                                                        <td class="align-middle">{{ $studentInfo['course'] }}</td>
                                                        <td class="align-middle">{{ $studentInfo['branch'] }}</td>
                                                        
                                                        @foreach($lessonData['dates'] as $date => $dateMarks)
                                                            <td class="text-center align-middle">
                                                                @if(isset($lessonData['students'][$studentId][$date]))
                                                                    @php
                                                                        $mark = $lessonData['students'][$studentId][$date];
                                                                        $originalMark = $mark->value . '/' . ($mark->assessment_type === 'test' ? ($mark->marks_out_of ?? 100) : 100);
                                                                        $percentage = $mark->normalized_percentage;
                                                                        $status = $mark->status;
                                                                    @endphp
                                                                
                                                                    <div class="mark-display">
                                                                        {{ $originalMark }}
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                @else
                    <div class="empty-state m-4">
                        <i class="bi bi-journal-text display-1"></i>
                        <h4 class="mt-4">No marks recorded yet</h4>
                        <p class="text-muted mb-4">Start recording marks for your students to see them here.</p>
                        <a href="{{ route('teacher.marks.index') }}" class="btn btn-primary-custom">
                            <i class="bi bi-plus-circle"></i> Record Marks
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSearch = document.getElementById('student-search');
    const courseFilter = document.getElementById('course-filter');
    const lessonFilter = document.getElementById('lesson-filter');
    const termFilter = document.getElementById('term-filter');
    const lessonSections = document.querySelectorAll('.lesson-section');

    function filterLessons() {
        const searchTerm = studentSearch.value.toLowerCase().trim();
        const selectedCourse = courseFilter.value;
        const selectedLesson = lessonFilter.value;
        const selectedTerm = termFilter.value;

        lessonSections.forEach(section => {
            const sectionTermId = section.getAttribute('data-term-id');
            const sectionLessonId = section.getAttribute('data-lesson-id');

            const termMatch = selectedTerm === '' || sectionTermId === selectedTerm;
            const lessonMatch = selectedLesson === '' || sectionLessonId === selectedLesson;

            // Filter rows by course and search term
            const rows = section.querySelectorAll('tbody tr');
            let hasVisibleRows = false;
            
            rows.forEach(row => {
                const nameCell = row.querySelector('td:nth-child(1)');
                const courseCell = row.querySelector('td:nth-child(2)');
                
                if (nameCell && courseCell) {
                    const studentName = nameCell.textContent.trim().toLowerCase();
                    const courseName = courseCell.textContent.trim();
                    
                    const courseMatch = selectedCourse === '' || courseName === selectedCourse;
                    const searchMatch = searchTerm === '' || studentName.includes(searchTerm);
                    
                    if (courseMatch && searchMatch) {
                        row.style.display = '';
                        hasVisibleRows = true;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            // Show/hide section based on filters and whether it has visible rows
            if (termMatch && lessonMatch && hasVisibleRows) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    }

    studentSearch.addEventListener('input', filterLessons);
    courseFilter.addEventListener('change', filterLessons);
    lessonFilter.addEventListener('change', filterLessons);
    termFilter.addEventListener('change', filterLessons);
});
</script>
@endsection
