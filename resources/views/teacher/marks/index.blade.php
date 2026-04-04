@extends('teacher.layout')

@section('styles')
@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
  --ink: #0d0f14;
  --ink-muted: #4a5066;
  --ink-faint: #8891a8;
  --surface: #ffffff;
  --surface-2: #f5f6fa;
  --surface-3: #eceef5;
  --border: #e2e5ef;
  --accent: #4f63ff;
  --accent-dark: #3447e8;
  --accent-glow: rgba(79,99,255,0.08);
  --green: #00c98d;
  --green-dark: #0d7a56;
  --green-bg: #e6faf4;
  --red: #ff4f6a;
  --amber: #f59e0b;
  --font: 'Sora', sans-serif;
  --mono: 'JetBrains Mono', monospace;
}

* {
  font-family: var(--font);
}

body {
  background: var(--surface-2);
  padding: 2.5rem 0 4rem;
}

.hero-banner {
  background: var(--ink);
  border-radius: 20px;
  padding: 2.25rem 3rem;
  position: relative;
  overflow: hidden;
}

.hero-banner::before {
  content: '';
  position: absolute;
  top: -50px;
  right: -50px;
  width: 200px;
  height: 200px;
  background: var(--accent);
  opacity: 0.08;
  border-radius: 50%;
}

.hero-banner::after {
  content: '';
  position: absolute;
  bottom: -30px;
  left: -30px;
  width: 120px;
  height: 120px;
  background: var(--accent);
  opacity: 0.06;
  border-radius: 50%;
}

.hero-title {
  font-size: 1.75rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.04em;
  margin-bottom: 0.5rem;
}

.hero-subtitle {
  color: rgba(255,255,255,0.5);
  font-size: 0.875rem;
}

.hero-stats {
  font-family: var(--mono);
  font-size: 1.25rem;
  color: var(--accent);
  font-weight: 500;
}

.card {
  background: var(--surface);
  border-radius: 16px;
  border: 1px solid var(--border);
  box-shadow: 0 4px 24px rgba(0,0,0,0.05);
}

.card-header {
  background: var(--ink);
  color: white;
  padding: 1.1rem 1.5rem;
  border-radius: 16px 16px 0 0;
  border: none;
}

.card-body {
  padding: 1.75rem;
}

.btn {
  border-radius: 9px;
  font-family: var(--font);
  font-weight: 600;
  transition: background 0.15s, transform 0.12s;
}

.btn:hover {
  transform: translateY(-1px);
}

.btn-primary {
  background: var(--accent);
  border-color: var(--accent);
}

.btn-primary:hover {
  background: var(--accent-dark);
  border-color: var(--accent-dark);
}

.btn-success {
  background: var(--green-bg);
  color: #0d6e4e;
  border: 1px solid #a7f3d0;
}

.btn-success:hover {
  background: #d4f4e8;
  transform: translateY(-1px);
}

.btn-outline-info {
  background: var(--surface);
  border: 1px solid var(--border);
  color: var(--ink-muted);
}

.btn-outline-info:hover {
  background: var(--surface-2);
  transform: translateY(-1px);
}

.btn-outline-secondary {
  background: var(--surface);
  border: 1px solid var(--border);
  color: var(--ink-muted);
}

.btn-outline-secondary:hover {
  background: var(--surface-2);
  transform: translateY(-1px);
}

.form-control, .form-select {
  border-radius: 10px;
  border: 1.5px solid var(--border);
  padding: 0.7rem 1rem;
  font-family: var(--font);
  font-size: 0.875rem;
  background: var(--surface);
}

.form-control:focus, .form-select:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-glow);
  outline: none;
}

.form-label {
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: var(--ink);
  margin-bottom: 0.5rem;
}

.modal-content {
  border-radius: 16px;
  border: 1px solid var(--border);
  box-shadow: 0 4px 24px rgba(0,0,0,0.05);
}

.modal-header {
  background: var(--ink);
  color: white;
  border-radius: 16px 16px 0 0;
  border: none;
}

.modal-body {
  background: var(--surface-2);
}

.table {
  background: var(--surface);
}

.table thead th {
  background: var(--surface-2);
  color: var(--ink-faint);
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  border-bottom: 2px solid var(--border);
}

.table tbody tr {
  border-bottom: 1px solid var(--surface-3);
  transition: background 0.15s;
}

.table tbody tr:hover {
  background: var(--surface-2);
}

.table td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
}

.alert-info {
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #1d4ed8;
  border-radius: 12px;
  padding: 0.9rem 1.25rem;
  display: flex;
  gap: 0.7rem;
}

.mark-input {
  font-family: var(--mono);
  font-weight: 500;
  text-align: center;
  border: 1.5px solid var(--border);
  border-radius: 8px;
  max-width: 110px;
  transition: all 0.15s;
}

.mark-input:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-glow);
  outline: none;
}

.percentage-badge {
  font-family: var(--mono);
  font-weight: 500;
  font-size: 0.8rem;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
}

.badge-high {
  background: var(--green-bg);
  color: #0d6e4e;
}

.badge-mid {
  background: #fffbeb;
  color: #92400e;
}

.badge-low {
  background: #fff0f3;
  color: #b91c1c;
}

@media (max-width: 768px) {
  .hero-banner {
    padding: 1.75rem 1.5rem;
  }
  
  .hero-title {
    font-size: 1.35rem;
  }
  
  .card-body {
    padding: 1.25rem;
  }
  
  .row.g-4 > .col-md-3 {
    margin-bottom: 1rem;
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 1.2rem;
  }
  
  .container-fluid {
    padding: 0 1rem;
  }
  
  .btn-lg {
    width: 100%;
    margin-bottom: 0.5rem;
  }
}
</style>
@endpush
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Hero Banner -->
            <div class="hero-banner mb-4">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="bi bi-award-fill" style="font-size: 3rem; color: var(--accent);"></i>
                    </div>
                    <h1 class="hero-title">Student Marks Management</h1>
                    <p class="hero-subtitle">Enter and manage student assessment marks with ease</p>
                    @if(isset($totalMarks))
                        <div class="hero-stats mt-3">Total Records: {{ $totalMarks }}</div>
                    @endif
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-pencil-square" style="font-size: 1.5rem; color: var(--accent);"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">Assessment Details</h4>
                            <small style="color: rgba(255,255,255,0.7);">Configure the assessment parameters</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="marksForm" method="POST" action="{{ route('teacher.marks.store') }}">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-3">
                                <label for="branch_id" class="form-label">
                                    <i class="bi bi-geo-alt me-1" style="color: var(--accent);"></i>Branch
                                </label>
                                <select name="branch_id" id="branch_id" class="form-select" required>
                                    <option value="">Select Branch</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="course_id" class="form-label">
                                    <i class="bi bi-book me-1" style="color: var(--accent);"></i>Course
                                </label>
                                <select name="course_id" id="course_id" class="form-select" required disabled>
                                    <option value="">Select Course</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="lesson_id" class="form-label">
                                    <i class="bi bi-journal-text me-1" style="color: var(--accent);"></i>Lesson
                                </label>
                                <select name="lesson_id" id="lesson_id" class="form-select" required disabled>
                                    <option value="">Select Lesson</option>
                                </select>
                                <small id="lesson-info" class="form-text text-muted mt-2" style="display:none;">
                                    <i class="bi bi-info-circle me-1"></i><span id="lesson-count">0</span> lesson(s) available
                                </small>
                            </div>
                            <div class="col-md-3">
                                <label for="assessment_type" class="form-label">
                                    <i class="bi bi-clipboard-check me-1" style="color: var(--accent);"></i>Assessment Type
                                </label>
                                <select name="assessment_type" id="assessment_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="test">Test</option>
                                    <option value="exam">Exam</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="term_id" class="form-label">
                                    <i class="bi bi-calendar3 me-1" style="color: var(--accent);"></i>Term
                                </label>
                                <select name="term_id" id="term_id" class="form-select" required>
                                    <option value="">Select Term</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-4 mt-2" id="testMarksOutOfRow" style="display:none;">
                            <div class="col-md-3">
                                <label for="marks_out_of" class="form-label">
                                    <i class="bi bi-calculator me-1" style="color: var(--accent);"></i>Marks Out Of
                                </label>
                                <input type="number" name="marks_out_of" id="marks_out_of" class="form-control" min="1" placeholder="e.g. 20">
                            </div>
                            <div class="col-md-3">
                                <label for="test_date" class="form-label">
                                    <i class="bi bi-calendar-event me-1" style="color: var(--accent);"></i>Test Date
                                </label>
                                <input type="date" name="test_date" id="test_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="row g-4 mt-2" id="examSelectRow" style="display:none;">
                            <div class="col-md-3">
                                <label for="exam_id" class="form-label">
                                    <i class="bi bi-file-earmark-text me-1" style="color: var(--accent);"></i>Select Exam
                                </label>
                                <select name="exam_id" id="exam_id" class="form-select">
                                    <option value="">Select Exam</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="exam_date" class="form-label">
                                    <i class="bi bi-calendar-event me-1" style="color: var(--accent);"></i>Exam Date
                                </label>
                                <input type="date" name="exam_date" id="exam_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('teacher.marks.view') }}" class="btn btn-outline-info">
                                <i class="bi bi-eye me-2"></i>View Recorded Marks
                            </a>
                            <button type="button" class="btn btn-primary px-4" id="showStudentsBtn" data-bs-toggle="modal" data-bs-target="#studentsModal">
                                <i class="bi bi-people me-2"></i>Show Students
                            </button>
                        </div>
                        <div id="marks-message" class="mt-3"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Students Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <!-- Professional Modal Header -->
      <div class="modal-header bg-gradient-primary text-white border-0">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-people-fill" style="font-size: 1.5rem; color: var(--accent);"></i>
            </div>
            <div>
                <h5 class="modal-title mb-0" id="studentsModalLabel">Students List</h5>
                <small style="color: rgba(255,255,255,0.7);">Enter marks for each student</small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <!-- Enhanced Modal Body -->
      <div class="modal-body bg-light">
        <form id="studentsMarksForm">
            <!-- Instructions Card -->
            <div class="alert alert-info border-0 bg-blue-50 text-blue-800 mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2" style="font-size: 1.2rem;"></i>
                    <div>
                        <strong>Instructions:</strong> Enter the marks for each student below. Marks will be automatically validated against the total marks.
                    </div>
                </div>
            </div>
            
            <!-- Professional Students Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="border-0" style="width: 50px;">
                                        <i class="bi bi-hash"></i>
                                    </th>
                                    <th class="border-0">
                                        <i class="bi bi-person-circle me-2"></i>Student Name
                                    </th>
                                    <th class="border-0" style="width: 150px;">
                                        <i class="bi bi-pencil-square me-2"></i>Marks
                                    </th>
                                    <th class="border-0" style="width: 100px;">
                                        <i class="bi bi-percent me-2"></i>Percentage
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="students-modal-list" class="bg-white">
                                <!-- Students will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    <small><i class="bi bi-info-circle me-1"></i>All fields are required</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success px-4" id="saveMarksBtn">
                        <i class="bi bi-save"></i> <span id="saveBtnText">Save Marks</span>
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Message Area -->
        <div id="modal-message" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>

<!-- Enhanced Styles -->
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.modal-xl {
    max-width: 90%;
}

.form-select-lg, .form-control-lg {
    font-size: 1rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.form-select-lg:focus, .form-control-lg:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.mark-input {
    font-weight: 600;
    text-align: center;
    border: 2px solid #e9ecef;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
}

.mark-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    transform: scale(1.05);
}

.percentage-badge {
    font-weight: 600;
    font-size: 0.875rem;
}

.student-row {
    transition: all 0.3s ease;
}

.student-row:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.alert-info {
    border-left: 4px solid #0dcaf0;
}

.badge-high {
    background: var(--green-bg);
    color: #0d6e4e;
}

.badge-mid {
    background: #fffbeb;
    color: #92400e;
}

.badge-low {
    background: #fff0f3;
    color: #b91c1c;
}
</style>

<!-- Bootstrap Icons CDN (for icons) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
    // Load branches on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadBranches();
    });
    
    // Load branches for this teacher
    function loadBranches() {
        fetch('/api/teacher/branches')
            .then(res => {
                if (!res.ok) throw new Error('Failed to load branches');
                return res.json();
            })
            .then(branches => {
                const branchSelect = document.getElementById('branch_id');
                branchSelect.innerHTML = '<option value="">Select Branch</option>';
                
                if (Array.isArray(branches) && branches.length > 0) {
                    branches.forEach(branch => {
                        const option = document.createElement('option');
                        option.value = branch.id;
                        option.textContent = branch.name;
                        branchSelect.appendChild(option);
                    });
                } else {
                    branchSelect.innerHTML = '<option value="">No branches available for you</option>';
                }
            })
            .catch(error => {
                console.error('Error loading branches:', error);
            });
    }
    
    // Load courses when branch is selected
    document.getElementById('branch_id').addEventListener('change', function() {
        const branchId = this.value;
        const courseSelect = document.getElementById('course_id');
        const lessonSelect = document.getElementById('lesson_id');
        
        courseSelect.innerHTML = '<option value="">Select Course</option>';
        courseSelect.disabled = true;
        lessonSelect.innerHTML = '<option value="">Select Lesson</option>';
        lessonSelect.disabled = true;
        
        if (!branchId) return;
        
        fetch(`/api/teacher/branch/${branchId}/courses`)
            .then(res => {
                if (!res.ok) throw new Error('Failed to load courses');
                return res.json();
            })
            .then(courses => {
                if (Array.isArray(courses) && courses.length > 0) {
                    courseSelect.innerHTML = '<option value="">Select Course</option>';
                    courses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = course.course_name + ' (' + course.course_code + ')';
                        courseSelect.appendChild(option);
                    });
                    courseSelect.disabled = false;
                } else {
                    courseSelect.innerHTML = '<option value="">No courses available in this branch</option>';
                    courseSelect.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error loading courses:', error);
                courseSelect.innerHTML = '<option value="">Error loading courses</option>';
            });
        
        updateShowStudentsBtn();
    });
    
    // Load lessons when course is selected
    document.getElementById('course_id').addEventListener('change', function() {
        const branchId = document.getElementById('branch_id').value;
        const courseId = this.value;
        const lessonSelect = document.getElementById('lesson_id');
        const lessonInfo = document.getElementById('lesson-info');
        
        lessonSelect.innerHTML = '<option value="">Select Lesson</option>';
        lessonSelect.disabled = true;
        lessonInfo.style.display = 'none';
        
        if (!branchId || !courseId) return;
        
        fetch(`/api/teacher/branch/${branchId}/course/${courseId}/lessons`)
            .then(res => {
                if (!res.ok) throw new Error('Failed to load lessons');
                return res.json();
            })
            .then(lessons => {
                if (Array.isArray(lessons) && lessons.length > 0) {
                    lessonSelect.innerHTML = '<option value="">Select Lesson</option>';
                    lessons.forEach(lesson => {
                        const option = document.createElement('option');
                        option.value = lesson.id;
                        option.textContent = lesson.title;
                        lessonSelect.appendChild(option);
                    });
                    lessonSelect.disabled = false;
                    lessonInfo.style.display = 'inline-block';
                    document.getElementById('lesson-count').textContent = lessons.length;
                } else {
                    lessonSelect.innerHTML = '<option value="">No lessons assigned to you in this branch-course</option>';
                    lessonSelect.disabled = true;
                    lessonInfo.innerHTML = '<i class="bi bi-info-circle me-1"></i>No lessons available for this course in this branch';
                    lessonInfo.style.display = 'inline-block';
                }
            })
            .catch(error => {
                console.error('Error loading lessons:', error);
                lessonSelect.innerHTML = '<option value="">Error loading lessons</option>';
                lessonSelect.disabled = true;
            });
        
        updateShowStudentsBtn();
    }

    document.getElementById('assessment_type').addEventListener('change', function() {
        var type = this.value;
        document.getElementById('testMarksOutOfRow').style.display = type === 'test' ? 'flex' : 'none';
        document.getElementById('examSelectRow').style.display = type === 'exam' ? 'flex' : 'none';
        if (type === 'exam') {
            loadExams();
        }
        updateShowStudentsBtn();
    });

    function loadExams() {
        var courseId = document.getElementById('course_id').value;
        fetch(`/api/course/${courseId}/exams`)
            .then(res => res.json())
            .then(data => {
                var examSelect = document.getElementById('exam_id');
                examSelect.innerHTML = '<option value="">Select Exam</option>';
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function(exam) {
                        examSelect.innerHTML += `<option value="${exam.id}">${exam.title}</option>`;
                    });
                }
            })
            .catch(error => {
                document.getElementById('marks-message').innerHTML = `<div class="text-danger">Error loading exams: ${error.message}</div>`;
            });
    }

    document.getElementById('showStudentsBtn').addEventListener('click', function() {
        loadStudentsModal();
    });

    function loadStudentsModal() {
        var branchId = document.getElementById('branch_id').value;
        var courseId = document.getElementById('course_id').value;
        var lessonId = document.getElementById('lesson_id').value;
        var termId = document.getElementById('term_id').value;
        var assessmentType = document.getElementById('assessment_type').value;
        var examId = document.getElementById('exam_id').value;
        var examDate = document.getElementById('exam_date').value;
        var marksOutOf = document.getElementById('marks_out_of').value;
        var testDate = document.getElementById('test_date').value;
        var modalTbody = document.getElementById('students-modal-list');
        var modalMsg = document.getElementById('modal-message');
        modalTbody.innerHTML = '';
        modalMsg.innerHTML = '';

        if (!branchId || !courseId || !lessonId || !termId || !assessmentType || (assessmentType === 'exam' && (!examId || !examDate)) || (assessmentType === 'test' && (!marksOutOf || !testDate))) {
            modalMsg.innerHTML = '<div class="text-warning">Please select all required fields including term.</div>';
            return;
        }

        fetch(`/api/teacher/branch/${branchId}/course/${courseId}/students`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function(student, index) {
                        modalTbody.innerHTML += `
                            <tr class="student-row">
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-circle">${index + 1}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 600;">
                                                ${student.full_name.charAt(0).toUpperCase()}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">${student.full_name}</div>
                                            <small class="text-muted">Student ID: ${student.id}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" name="marks[${student.id}][value]" class="form-control mark-input" min="0" max="${assessmentType === 'test' ? marksOutOf : 100}" required placeholder="0">
                                        <span class="input-group-text">/ ${assessmentType === 'test' ? marksOutOf : 100}</span>
                                    </div>
                                    <input type="hidden" name="students[]" value="${student.id}">
                                </td>
                                <td class="text-center">
                                    <span class="percentage-badge" id="percentage-${student.id}">-</span>
                                </td>
                            </tr>
                        `;
                    });

                    // Enhanced live validation for mark inputs - attach immediately after DOM elements exist
                    var markInputs = document.querySelectorAll('.mark-input');
                    markInputs.forEach(function(input) {
                        var errorDiv = document.createElement('div');
                        errorDiv.className = 'text-danger small mt-1';
                        errorDiv.style.display = 'none';
                        input.parentNode.parentNode.appendChild(errorDiv);

                        input.addEventListener('input', function() {
                            var max = parseInt(input.getAttribute('max'), 10);
                            var value = input.value ? parseInt(input.value, 10) : null;
                            var studentId = input.name.match(/marks\[(\d+)\]/)[1];
                            var percentageBadge = document.getElementById('percentage-' + studentId);
                            
                            // Calculate and display percentage
                            if (value !== null && !isNaN(value) && value >= 0 && value <= max) {
                                var percentage = Math.round((value / max) * 100);
                                percentageBadge.textContent = percentage + '%';
                                
                                // Update badge color based on percentage
                                percentageBadge.className = 'percentage-badge';
                                if (percentage >= 75) {
                                    percentageBadge.classList.add('badge-high');
                                } else if (percentage >= 50) {
                                    percentageBadge.classList.add('badge-mid');
                                } else {
                                    percentageBadge.classList.add('badge-low');
                                }
                                
                                input.classList.remove('is-invalid');
                                errorDiv.style.display = 'none';
                            } else if (value !== null && !isNaN(value) && value > max) {
                                input.classList.add('is-invalid');
                                errorDiv.textContent = `Marks (${value}) cannot exceed total (${max})`;
                                errorDiv.style.display = 'block';
                                input.value = max;
                                
                                // Update percentage for max value
                                var percentage = Math.round((max / max) * 100);
                                percentageBadge.textContent = percentage + '%';
                                percentageBadge.className = 'percentage-badge badge-high';
                            } else {
                                percentageBadge.textContent = '-';
                                percentageBadge.className = 'percentage-badge';
                            }
                        });
                    });
                } else {
                    modalTbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-x" style="font-size: 3rem;"></i>
                                    <div class="mt-3">
                                        <h5>No Students Found</h5>
                                        <p class="mb-0">There are no students enrolled in this course/lesson combination.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                modalTbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="text-danger">
                                <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
                                <div class="mt-3">
                                    <h5>Error Loading Students</h5>
                                    <p class="mb-0">${error.message}</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            });
    }

    // Form submission handler - properly attached to the form element
    document.addEventListener('submit', function(e) {
        if (e.target.id !== 'studentsMarksForm') return;
        
        e.preventDefault();
        
        var saveBtn = document.getElementById('saveMarksBtn');
        var saveBtnText = document.getElementById('saveBtnText');
        var modalMsg = document.getElementById('modal-message');
        var form = e.target;
        
        // Validation: Ensure all mark fields are filled
        var markInputs = form.querySelectorAll('.mark-input');
        var allFilled = true;
        var emptyCount = 0;
        
        markInputs.forEach(function(input) {
            if (!input.value || input.value === '' || isNaN(input.value)) {
                input.classList.add('is-invalid');
                allFilled = false;
                emptyCount++;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        if (!allFilled) {
            modalMsg.innerHTML = `<div class="alert alert-warning alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i> Please fill in all mark fields (${emptyCount} missing). <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            return;
        }
        
        // Disable button and show loading state
        saveBtn.disabled = true;
        saveBtnText.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
        saveBtn.classList.remove('btn-success');
        saveBtn.classList.add('btn-outline-secondary');
        
        // Clear previous messages
        modalMsg.innerHTML = '';
        
        var formData = new FormData(form);
        formData.append('branch_id', document.getElementById('branch_id').value);
        formData.append('course_id', document.getElementById('course_id').value);
        formData.append('lesson_id', document.getElementById('lesson_id').value);
        formData.append('term_id', document.getElementById('term_id').value);
        formData.append('assessment_type', document.getElementById('assessment_type').value);
        
        if (document.getElementById('assessment_type').value === 'exam') {
            formData.append('exam_id', document.getElementById('exam_id').value);
            formData.append('exam_date', document.getElementById('exam_date').value);
        }
        if (document.getElementById('assessment_type').value === 'test') {
            formData.append('marks_out_of', document.getElementById('marks_out_of').value);
            formData.append('test_date', document.getElementById('test_date').value);
        }

        fetch('{{ route('teacher.marks.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            // Restore button state
            saveBtn.disabled = false;
            saveBtnText.innerHTML = '<i class="bi bi-save"></i> Save Marks';
            saveBtn.classList.remove('btn-outline-secondary');
            saveBtn.classList.add('btn-success');
            
            if (data.success) {
                modalMsg.innerHTML = `<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>${data.message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
                // Clear form after successful save
                form.reset();
                // Clear the students list
                document.getElementById('students-modal-list').innerHTML = '';
                // Close modal after 2 seconds
                setTimeout(function() {
                    try {
                        var modalElement = document.getElementById('studentsModal');
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }
                    } catch(e) {
                        console.log('Modal close:', e);
                    }
                }, 2000);
            } else {
                var errorMsg = data.message || 'Failed to save marks.';
                var errorDetails = '';
                if (data.errors && typeof data.errors === 'object') {
                    errorDetails = '<ul class="mb-0 mt-2">' + Object.entries(data.errors).map(([field, messages]) => {
                        return '<li>' + (Array.isArray(messages) ? messages.join(', ') : messages) + '</li>';
                    }).join('') + '</ul>';
                }
                modalMsg.innerHTML = `<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>${errorMsg}${errorDetails} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            }
        })
        .catch(error => {
            // Restore button state on error
            saveBtn.disabled = false;
            saveBtnText.innerHTML = '<i class="bi bi-save"></i> Save Marks';
            saveBtn.classList.remove('btn-outline-secondary');
            saveBtn.classList.add('btn-success');
            
            document.getElementById('modal-message').innerHTML = `<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>Error: ${error.message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
        });
    });

    function updateShowStudentsBtn() {
        var branchId = document.getElementById('branch_id').value;
        var courseId = document.getElementById('course_id').value;
        var lessonId = document.getElementById('lesson_id').value;
        var termId = document.getElementById('term_id').value;
        var assessmentType = document.getElementById('assessment_type').value;
        var examId = document.getElementById('exam_id').value;
        var examDate = document.getElementById('exam_date').value;
        var marksOutOf = document.getElementById('marks_out_of').value;
        var testDate = document.getElementById('test_date').value;
        var btn = document.getElementById('showStudentsBtn');
        if (!branchId || !courseId || !lessonId || !termId || !assessmentType ||
            (assessmentType === 'exam' && (!examId || !examDate)) ||
            (assessmentType === 'test' && (!marksOutOf || !testDate))) {
            btn.disabled = true;
        } else {
            btn.disabled = false;
        }
    }
    
    // Attach event listeners to form fields for button state updates
    ['branch_id', 'course_id', 'lesson_id', 'assessment_type', 'exam_id', 'marks_out_of', 'exam_date', 'term_id', 'test_date'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', updateShowStudentsBtn);
    });

    // Check button state when DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateShowStudentsBtn);
    } else {
        updateShowStudentsBtn();
    }
</script>
@endsection