@extends('dos.layout')

@section('title', 'Marks Management')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-journal-check me-2"></i>Marks Management
        </h2>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Marks</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dos.marks.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Term</label>
                    <select name="term_id" class="form-select">
                        <option value="">All Terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Lesson</label>
                    <select name="lesson_id" class="form-select">
                        <option value="">All Lessons</option>
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}" {{ request('lesson_id') == $lesson->id ? 'selected' : '' }}>
                                {{ $lesson->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Assessment Type</label>
                    <select name="assessment_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="test" {{ request('assessment_type') == 'test' ? 'selected' : '' }}>Test</option>
                        <option value="exam" {{ request('assessment_type') == 'exam' ? 'selected' : '' }}>Exam</option>
                        <option value="assignment" {{ request('assessment_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Student Search</label>
                    <input type="text" name="student_search" class="form-control" value="{{ request('student_search') }}" placeholder="Search by name or ID">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('dos.marks.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg me-1"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['total_marks'] ?? 0 }}</h5>
                    <p class="card-text">Total Marks</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['exam_marks'] ?? 0 }}</h5>
                    <p class="card-text">Exam Marks</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['test_marks'] ?? 0 }}</h5>
                    <p class="card-text">Test Marks</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body">
                    <h5 class="card-title">{{ $stats['assignment_marks'] ?? 0 }}</h5>
                    <p class="card-text">Assignment Marks</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Marks Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Marks Records</h5>
        </div>
        <div class="card-body">
            @if(empty($groupedMarks))
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-3">No marks found matching your criteria.</p>
                </div>
            @else
                @foreach($groupedMarks as $courseId => $courseData)
                    <div class="mb-4">
                        <h5 class="text-primary">{{ $courseData['name'] }}</h5>
                        @foreach($courseData['terms'] as $termId => $termData)
                            <div class="mb-3">
                                <h6 class="text-secondary">{{ $termData['name'] }}</h6>
                                @foreach($termData['lessons'] as $lessonId => $lessonData)
                                    <div class="card mb-3">
                                        <div class="card-header py-2">
                                            <h6 class="mb-0">{{ $lessonData['title'] }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Student</th>
                                                            <th>Reg Number</th>
                                                            @foreach($lessonData['dates'] as $date => $dateMarks)
                                                                <th>{{ $date === 'no-date' ? 'No Date' : \Carbon\Carbon::parse($date)->format('M d, Y') }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($students as $studentId => $student)
                                                            @if(isset($lessonData['students'][$studentId]))
                                                                <tr>
                                                                    <td>{{ $student['name'] }}</td>
                                                                    <td>{{ $student['reg_number'] }}</td>
                                                                    @foreach($lessonData['dates'] as $date => $dateMarks)
                                                                        <td>
                                                                            @if(isset($lessonData['students'][$studentId][$date]))
                                                                                {{ $lessonData['students'][$studentId][$date]->value ?? '-' }} / {{ $lessonData['students'][$studentId][$date]->marks_out_of ?? '-' }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
