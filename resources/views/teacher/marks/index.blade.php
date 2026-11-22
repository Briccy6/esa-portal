@extends('teacher.layout')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
* { font-family: var(--font); }
body { background: var(--surface-2); padding: 2.5rem 0 4rem; }

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
  top: -50px; right: -50px;
  width: 200px; height: 200px;
  background: var(--accent);
  opacity: 0.08;
  border-radius: 50%;
}
.hero-banner::after {
  content: '';
  position: absolute;
  bottom: -30px; left: -30px;
  width: 120px; height: 120px;
  background: var(--accent);
  opacity: 0.06;
  border-radius: 50%;
}
.hero-title { font-size: 1.75rem; font-weight: 800; color: #fff; letter-spacing: -0.04em; margin-bottom: 0.5rem; }
.hero-subtitle { color: rgba(255,255,255,0.5); font-size: 0.875rem; }
.hero-stats { font-family: var(--mono); font-size: 1.25rem; color: var(--accent); font-weight: 500; }

.card { background: var(--surface); border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
.card-header { background: var(--ink); color: white; padding: 1.1rem 1.5rem; border-radius: 16px 16px 0 0; border: none; }
.card-body { padding: 1.75rem; }

.btn { border-radius: 9px; font-family: var(--font); font-weight: 600; transition: background 0.15s, transform 0.12s; }
.btn:hover { transform: translateY(-1px); }
.btn-primary { background: var(--accent); border-color: var(--accent); }
.btn-primary:hover { background: var(--accent-dark); border-color: var(--accent-dark); }
.btn-success { background: var(--green-bg); color: #0d6e4e; border: 1px solid #a7f3d0; }
.btn-success:hover { background: #d4f4e8; }
.btn-outline-info { background: var(--surface); border: 1px solid var(--border); color: var(--ink-muted); }
.btn-outline-info:hover { background: var(--surface-2); }
.btn-outline-secondary { background: var(--surface); border: 1px solid var(--border); color: var(--ink-muted); }
.btn-outline-secondary:hover { background: var(--surface-2); }

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
.form-label { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--ink); margin-bottom: 0.5rem; }

.modal-content { border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 24px rgba(0,0,0,0.05); }
.modal-header { background: var(--ink); color: white; border-radius: 16px 16px 0 0; border: none; }
.modal-body { background: var(--surface-2); }

.table { background: var(--surface); }
.table thead th { background: var(--surface-2); color: var(--ink-faint); font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; border-bottom: 2px solid var(--border); }
.table tbody tr { border-bottom: 1px solid var(--surface-3); transition: background 0.15s; }
.table tbody tr:hover { background: var(--surface-2); }
.table td { padding: 0.85rem 1rem; vertical-align: middle; }

.mark-input { font-family: var(--mono); font-weight: 500; text-align: center; border: 1.5px solid var(--border); border-radius: 8px; max-width: 110px; transition: all 0.15s; }
.mark-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); outline: none; }

.percentage-badge { font-family: var(--mono); font-weight: 500; font-size: 0.8rem; padding: 0.25rem 0.5rem; border-radius: 6px; }
.badge-high { background: var(--green-bg); color: #0d6e4e; }
.badge-mid { background: #fffbeb; color: #92400e; }
.badge-low { background: #fff0f3; color: #b91c1c; }

.student-row { transition: all 0.3s ease; }
.student-row:hover { background-color: #f8f9fa; }

.alert-info { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; border-radius: 12px; padding: 0.9rem 1.25rem; display: flex; gap: 0.7rem; }

@media (max-width: 768px) {
  .hero-banner { padding: 1.75rem 1.5rem; }
  .hero-title { font-size: 1.35rem; }
  .card-body { padding: 1.25rem; }
}
@media (max-width: 480px) {
  .hero-title { font-size: 1.2rem; }
  .container-fluid { padding: 0 1rem; }
  .btn-lg { width: 100%; margin-bottom: 0.5rem; }
}
</style>
@endpush

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
                                <small id="assessmentTypeInfo" class="form-text d-block mt-2" style="display:none; color: var(--accent); font-weight: 500;">
                                    <i class="bi bi-info-circle me-1"></i><span id="assessmentTypeLabel"></span>
                                </small>
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

                        <!-- Test Fields -->
                        <div class="row g-4 mt-2" id="testMarksOutOfRow">
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
                                <input type="date" name="test_date" id="test_date" class="form-control">
                            </div>
                        </div>

                        <!-- Exam Fields -->
                        <div class="row g-4 mt-2" id="examSelectRow">
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
                                <input type="date" name="exam_date" id="exam_date" class="form-control" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                <small class="form-text mt-1" style="color: var(--accent);">
                                    <i class="bi bi-info-circle me-1"></i>Auto-populated from exam details
                                </small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('teacher.assignments.index') }}" class="btn btn-outline-warning">
                                    <i class="bi bi-journal-check me-2"></i>Assignments &amp; Marks
                                </a>
                                <a href="{{ route('teacher.marks.view') }}" class="btn btn-outline-info">
                                    <i class="bi bi-eye me-2"></i>View Recorded Marks
                                </a>
                                <button type="button" class="btn btn-success" id="downloadTemplateBtn" disabled>
                                    <i class="bi bi-download me-2"></i>Download CSV Template
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="uploadCsvBtn" disabled data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="bi bi-upload me-2"></i>Upload CSV
                                </button>
                            </div>
                            <button type="button" class="btn btn-primary px-4" id="showStudentsBtn" disabled data-bs-toggle="modal" data-bs-target="#studentsModal">
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

<!-- Students Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header">
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
      <div class="modal-body">
        <form id="studentsMarksForm">
            @csrf
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2" style="font-size: 1.2rem;"></i>
                <div>
                    <strong>Instructions:</strong> Enter the marks for each student below. Marks will be automatically validated against the total marks.
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <input type="text" id="student-search" class="form-control" placeholder="Search student by name or ID...">
                    <small class="form-text text-muted mt-1"><i class="bi bi-search me-1"></i>Type to filter students</small>
                </div>
                <div class="col-md-4">
                    <button type="button" id="sort-students-btn" class="btn btn-outline-primary w-100">
                        <i class="bi bi-sort-alpha-down me-2"></i>Sort: A-Z
                    </button>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th><i class="bi bi-person-circle me-2"></i>Student Name</th>
                                    <th style="width: 200px;"><i class="bi bi-pencil-square me-2"></i>Marks</th>
                                    <th style="width: 110px;"><i class="bi bi-percent me-2"></i>Percentage</th>
                                </tr>
                            </thead>
                            <tbody id="students-modal-list"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="filter-info" class="mt-3 text-muted small" style="display: none;"></div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Empty fields will be saved as 0</small>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success px-4" id="saveMarksBtn">
                        <i class="bi bi-save me-1"></i><span id="saveBtnText">Save Marks</span>
                    </button>
                </div>
            </div>
        </form>
        <div id="modal-message" class="mt-3"></div>
      </div>
    </div>
  </div>
</div>

<!-- Upload CSV Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-upload" style="font-size: 1.5rem; color: var(--accent);"></i>
            </div>
            <div>
                <h5 class="modal-title mb-0" id="uploadModalLabel">Upload Marks CSV</h5>
                <small style="color: rgba(255,255,255,0.7);">Upload marks from CSV file</small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="uploadCsvForm">
            @csrf
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2" style="font-size: 1.2rem;"></i>
                <div>
                    <strong>Instructions:</strong> Download the CSV template first, fill in the marks, then upload it here.
                </div>
            </div>

            <div class="mb-3">
                <label for="csv_file" class="form-label">
                    <i class="bi bi-file-earmark-csv me-1" style="color: var(--accent);"></i>CSV File
                </label>
                <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                <small class="form-text text-muted">Only CSV files are allowed</small>
            </div>

            <div id="uploadMessage"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-2"></i>Cancel
        </button>
        <button type="submit" form="uploadCsvForm" class="btn btn-primary" id="uploadCsvSubmitBtn">
            <i class="bi bi-check-circle me-2"></i>Upload Marks
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ─── Helpers ──────────────────────────────────────────────────────
    function isJsonResponse(res) {
        var ct = res.headers && res.headers.get ? (res.headers.get('content-type') || '') : '';
        return ct.toLowerCase().includes('application/json');
    }

    function fetchJson(url, options) {
        return fetch(url, options).then(function (res) {
            if (isJsonResponse(res)) {
                return res.json().then(function (data) {
                    if (!res.ok) {
                        var msg = (data && (data.message || data.error)) ? (data.message || data.error) : ('HTTP ' + res.status);
                        var err = new Error(msg);
                        err.status = res.status;
                        err.data = data;
                        throw err;
                    }
                    return data;
                });
            }

            return res.text().then(function (text) {
                var msg = 'Request failed (HTTP ' + res.status + ').';
                if (res.status === 419) msg = 'Session expired (CSRF). Please refresh and login again.';
                if (res.status === 401 || res.status === 403) msg = 'You are not authorized. Please login again.';
                if (res.status === 302) msg = 'Redirected to another page. Please login again.';
                var err = new Error(msg);
                err.status = res.status;
                err.raw = text;
                throw err;
            });
        });
    }

    // ─── Load Branches ────────────────────────────────────────────────
    function loadBranches() {
        fetchJson('/api/teacher/branches', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(branches => {
                var sel = document.getElementById('branch_id');
                sel.innerHTML = '<option value="">Select Branch</option>';
                if (Array.isArray(branches) && branches.length > 0) {
                    branches.forEach(function (b) {
                        sel.innerHTML += '<option value="' + b.id + '">' + b.name + '</option>';
                    });
                } else {
                    sel.innerHTML = '<option value="">No branches available</option>';
                }
            })
            .catch(function (err) {
                document.getElementById('branch_id').innerHTML = '<option value="">Error loading branches</option>';
                console.error('Branches error:', err);
            });
    }

    loadBranches();

    // ─── Branch → Courses ─────────────────────────────────────────────
    document.getElementById('branch_id').addEventListener('change', function () {
        var branchId = this.value;
        var courseSelect = document.getElementById('course_id');
        var lessonSelect = document.getElementById('lesson_id');

        courseSelect.innerHTML = '<option value="">Select Course</option>';
        courseSelect.disabled = true;
        lessonSelect.innerHTML = '<option value="">Select Lesson</option>';
        lessonSelect.disabled = true;
        document.getElementById('lesson-info').style.display = 'none';

        if (!branchId) { updateBtn(); return; }

        fetchJson('/api/teacher/branch/' + branchId + '/courses', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (courses) {
                if (Array.isArray(courses) && courses.length > 0) {
                    courseSelect.innerHTML = '<option value="">Select Course</option>';
                    courses.forEach(function (c) {
                        courseSelect.innerHTML += '<option value="' + c.id + '">' + c.course_name + ' (' + c.course_code + ')</option>';
                    });
                    courseSelect.disabled = false;
                } else {
                    courseSelect.innerHTML = '<option value="">No courses available</option>';
                }
            })
            .catch(function (err) {
                courseSelect.innerHTML = '<option value="">Error loading courses</option>';
                console.error('Courses error:', err);
            });

        updateBtn();
    });

    // ─── Course → Lessons ─────────────────────────────────────────────
    document.getElementById('course_id').addEventListener('change', function () {
        var branchId = document.getElementById('branch_id').value;
        var courseId = this.value;
        var lessonSelect = document.getElementById('lesson_id');
        var lessonInfo = document.getElementById('lesson-info');

        lessonSelect.innerHTML = '<option value="">Select Lesson</option>';
        lessonSelect.disabled = true;
        lessonInfo.style.display = 'none';

        if (!branchId || !courseId) { updateBtn(); return; }

        fetchJson('/api/teacher/branch/' + branchId + '/course/' + courseId + '/lessons', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (lessons) {
                if (Array.isArray(lessons) && lessons.length > 0) {
                    lessonSelect.innerHTML = '<option value="">Select Lesson</option>';
                    lessons.forEach(function (l) {
                        lessonSelect.innerHTML += '<option value="' + l.id + '">' + l.title + '</option>';
                    });
                    lessonSelect.disabled = false;
                    document.getElementById('lesson-count').textContent = lessons.length;
                    lessonInfo.style.display = 'inline-block';
                } else {
                    lessonSelect.innerHTML = '<option value="">No lessons available</option>';
                    lessonInfo.innerHTML = '<i class="bi bi-info-circle me-1"></i>No lessons for this course/branch';
                    lessonInfo.style.display = 'inline-block';
                }
            })
            .catch(function (err) {
                lessonSelect.innerHTML = '<option value="">Error loading lessons</option>';
                console.error('Lessons error:', err);
            });

        updateBtn();
    });

    // ─── Assessment Type Toggle ────────────────────────────────────────
    function updateAssessmentTypeFields() {
        var type = document.getElementById('assessment_type').value;
        var testRow = document.getElementById('testMarksOutOfRow');
        var examRow = document.getElementById('examSelectRow');
        var typeInfo = document.getElementById('assessmentTypeInfo');
        var typeLabel = document.getElementById('assessmentTypeLabel');
        
        console.log('updateAssessmentTypeFields called - Assessment Type:', type);
        
        // Show/hide test fields
        if (testRow) {
            if (type === 'test') {
                testRow.style.display = 'flex';
                testRow.style.visibility = 'visible';
                testRow.style.opacity = '1';
                // Set required attribute
                document.getElementById('marks_out_of').required = true;
                document.getElementById('test_date').required = true;
                console.log('Showing test fields');
            } else {
                testRow.style.display = 'none';
            }
        }
        
        // Show/hide exam fields
        if (examRow) {
            if (type === 'exam') {
                examRow.style.display = 'flex';
                examRow.style.visibility = 'visible';
                examRow.style.opacity = '1';
                // Set required attribute (exam_date is auto-populated, not required)
                document.getElementById('exam_id').required = true;
                document.getElementById('exam_date').required = false;
                console.log('Showing exam fields');
            } else {
                examRow.style.display = 'none';
            }
        }

        // Show assessment type feedback
        if (typeInfo && typeLabel) {
            if (type === 'test') {
                typeLabel.textContent = 'Test selected - Enter marks out of and test date';
                typeInfo.style.display = 'block';
            } else if (type === 'exam') {
                typeLabel.textContent = 'Exam selected - Select exam (date will auto-populate)';
                typeInfo.style.display = 'block';
            } else {
                typeInfo.style.display = 'none';
            }
        }

        // Load exams if exam is selected
        if (type === 'exam') {
            document.getElementById('exam_id').innerHTML = '<option value="">Select Exam</option>';
            var termId = document.getElementById('term_id').value;
            if (termId) {
                console.log('Loading exams for term:', termId);
                loadExams();
            }
        }
        
        console.log('Assessment type change completed');
    }
    
    // Attach change listener
    var assessmentTypeSelect = document.getElementById('assessment_type');
    if (assessmentTypeSelect) {
        assessmentTypeSelect.addEventListener('change', function () {
            console.log('assessment_type change event fired, new value:', this.value);
            updateAssessmentTypeFields();
            updateBtn();
        });
    } else {
        console.error('Could not find assessment_type element!');
    }
    
    // Initialize field visibility on page load
    console.log('Initializing form - calling updateAssessmentTypeFields');
    updateAssessmentTypeFields();

    // ─── Term Change → reload exams if needed ─────────────────────────
    document.getElementById('term_id').addEventListener('change', function () {
        document.getElementById('exam_id').innerHTML = '<option value="">Select Exam</option>';
        if (document.getElementById('assessment_type').value === 'exam' && this.value) {
            loadExams();
        }
        updateBtn();
    });

    // ─── Load Exams ───────────────────────────────────────────────────
    function loadExams() {
        var courseId = document.getElementById('course_id').value;
        var termId = document.getElementById('term_id').value;
        var examSelect = document.getElementById('exam_id');

        if (!courseId || !termId) {
            examSelect.innerHTML = '<option value="">' + (!courseId ? 'Select a course first' : 'Select a term first') + '</option>';
            return;
        }

        return fetchJson('/api/course/' + courseId + '/exams?term_id=' + termId, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (data) {
                examSelect.innerHTML = '<option value="">Select Exam</option>';
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function (exam) {
                        // Create option with data-date attribute to store the class_date
                        var option = document.createElement('option');
                        option.value = exam.id;
                        option.textContent = exam.title;
                        option.setAttribute('data-date', exam.class_date);
                        examSelect.appendChild(option);
                    });
                } else {
                    examSelect.innerHTML = '<option value="">No exams for this term</option>';
                }
            })
            .catch(function (err) {
                examSelect.innerHTML = '<option value="">Error loading exams</option>';
                console.error('Exams error:', err);
            });
    }

    // ─── Auto-populate exam date when exam is selected ────────────────
    document.getElementById('exam_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var examDate = selectedOption.getAttribute('data-date');
        var examTitle = selectedOption.textContent;
        
        if (examDate) {
            // Convert ISO datetime (e.g., "2026-01-05T00:00:00.000000Z") to date format (YYYY-MM-DD)
            var dateObj = new Date(examDate);
            var year = dateObj.getFullYear();
            var month = String(dateObj.getMonth() + 1).padStart(2, '0');
            var day = String(dateObj.getDate()).padStart(2, '0');
            var formattedDate = year + '-' + month + '-' + day;
            
            document.getElementById('exam_date').value = formattedDate;
            console.log('Auto-populated exam_date for "' + examTitle + '":', formattedDate);
        } else {
            document.getElementById('exam_date').value = '';
            console.log('Cleared exam_date - no exam selected');
        }
        
        updateBtn();
    });

    // ─── Show Students Button ─────────────────────────────────────────
    document.getElementById('showStudentsBtn').addEventListener('click', function () {
        loadStudentsModal();
    });

    function loadStudentsModal() {
        var branchId      = document.getElementById('branch_id').value;
        var courseId      = document.getElementById('course_id').value;
        var lessonId      = document.getElementById('lesson_id').value;
        var termId        = document.getElementById('term_id').value;
        var assessmentType = document.getElementById('assessment_type').value;
        var examId        = document.getElementById('exam_id').value;
        var examDate      = document.getElementById('exam_date').value;
        var marksOutOf    = document.getElementById('marks_out_of').value;
        var testDate      = document.getElementById('test_date').value;
        var modalTbody    = document.getElementById('students-modal-list');
        var modalMsg      = document.getElementById('modal-message');

        modalTbody.innerHTML = '';
        modalMsg.innerHTML = '';

        // Validate required fields based on assessment type
        var missingFields = [];
        
        if (!branchId) missingFields.push('Branch');
        if (!courseId) missingFields.push('Course');
        if (!lessonId) missingFields.push('Lesson');
        if (!termId) missingFields.push('Term');
        if (!assessmentType) missingFields.push('Assessment Type (Test/Exam)');
        
        if (assessmentType === 'exam') {
            if (!examId) missingFields.push('Exam');
            // Exam date is auto-populated, no need to validate
        } else if (assessmentType === 'test') {
            if (!marksOutOf) missingFields.push('Marks Out Of');
            if (!testDate) missingFields.push('Test Date');
        }

        if (missingFields.length > 0) {
            var fieldList = missingFields.map(function(f) { return '<li>' + f + '</li>'; }).join('');
            modalMsg.innerHTML = '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i><strong>Missing required fields:</strong><ul class="mb-0 mt-2">' + fieldList + '</ul></div>';
            console.warn('Missing fields:', missingFields);
            return;
        }

        modalTbody.innerHTML = '<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>';

        var existingMarksPromise = fetchJson('/api/teacher/marks/existing?' + new URLSearchParams({
            branch_id: branchId,
            course_id: courseId,
            lesson_id: lessonId,
            term_id: termId,
            assessment_type: assessmentType,
            test_date: testDate || '',
            exam_id: examId || ''
        }).toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).catch(function() { return { success: false, marks: {} }; });

        fetchJson('/api/teacher/branch/' + branchId + '/course/' + courseId + '/students', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (data) {
                var students = Array.isArray(data) ? data : (data.students || []);
                modalTbody.innerHTML = '';

                if (students.length === 0) {
                    modalTbody.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-muted"><i class="bi bi-person-x" style="font-size:3rem;"></i><div class="mt-3"><h5>No Students Found</h5><p class="mb-0">No students enrolled in this course/branch.</p></div></td></tr>';
                    return;
                }

                // Sort students by name (A-Z by default)
                students.sort(function(a, b) {
                    return a.full_name.localeCompare(b.full_name);
                });

                var maxMark = assessmentType === 'test' ? marksOutOf : 100;

                existingMarksPromise.then(function(existingResp) {
                    var existingMarks = (existingResp && existingResp.marks) ? existingResp.marks : {};

                    students.forEach(function (student, index) {
                    var initials = student.full_name.charAt(0).toUpperCase();
                    var existing = existingMarks[String(student.id)] || existingMarks[student.id];
                    var existingValue = existing && (existing.value !== undefined && existing.value !== null) ? String(existing.value) : '';
                    modalTbody.innerHTML += `
                        <tr class="student-row">
                            <td class="text-center">
                                <span class="badge bg-primary rounded-circle">${index + 1}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3 d-flex align-items-center justify-content-center bg-primary text-white rounded-circle fw-bold" style="width:40px;height:40px;">
                                        ${initials}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">${student.full_name}</div>
                                        <small class="text-muted">ID: ${student.id}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="marks[${student.id}][value]" class="form-control mark-input" min="0" max="${maxMark}" placeholder="0" value="${existingValue}">
                                    <span class="input-group-text">/ ${maxMark}</span>
                                </div>
                                <input type="hidden" name="students[]" value="${student.id}">
                                <div class="text-danger small mt-1 mark-error" style="display:none;"></div>
                            </td>
                            <td class="text-center">
                                <span class="percentage-badge" id="percentage-${student.id}">-</span>
                            </td>
                        </tr>
                    `;
                    });

                    // Trigger percentage update for prefilled values
                    document.querySelectorAll('.mark-input').forEach(function (input) {
                        if (input.value !== '') {
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });
                });

                // Attach live percentage validation
                document.querySelectorAll('.mark-input').forEach(function (input) {
                    input.addEventListener('input', function () {
                        var max = parseInt(input.getAttribute('max'), 10);
                        var raw = input.value;
                        var value = raw === '' ? null : parseInt(raw, 10);
                        var studentId = input.name.match(/marks\[(\d+)\]/)[1];
                        var badge = document.getElementById('percentage-' + studentId);
                        var errDiv = input.closest('td').querySelector('.mark-error');

                        if (value !== null && !isNaN(value)) {
                            if (value > max) {
                                input.value = max;
                                value = max;
                            }
                            if (value < 0) {
                                input.value = 0;
                                value = 0;
                            }
                            var pct = Math.round((value / max) * 100);
                            badge.textContent = pct + '%';
                            badge.className = 'percentage-badge ' + (pct >= 75 ? 'badge-high' : pct >= 50 ? 'badge-mid' : 'badge-low');
                            input.classList.remove('is-invalid');
                            errDiv.style.display = 'none';
                        } else {
                            badge.textContent = '-';
                            badge.className = 'percentage-badge';
                        }
                    });
                });

                // Live search
                setupLiveSearch(students.length);
            })
            .catch(function (err) {
                console.error('Students error:', err);
                modalTbody.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-danger"><i class="bi bi-exclamation-triangle" style="font-size:3rem;"></i><div class="mt-3"><h5>Error Loading Students</h5><p class="mb-0">' + err.message + '</p></div></td></tr>';
            });
    }

    // ─── Live Search ──────────────────────────────────────────────────
    function setupLiveSearch(total) {
        var searchInput  = document.getElementById('student-search');
        var filterInfo   = document.getElementById('filter-info');

        searchInput.value = '';
        searchInput.focus();

        searchInput.addEventListener('keyup', function () {
            var term = this.value.toLowerCase().trim();
            var rows = document.querySelectorAll('.student-row');
            var visible = 0;

            rows.forEach(function (row) {
                var name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                var show = !term || name.includes(term);
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (term) {
                filterInfo.style.display = 'block';
                filterInfo.textContent = 'Showing ' + visible + ' of ' + total + ' students';
            } else {
                filterInfo.style.display = 'none';
            }
        });
    }

    // ─── Sort Students Toggle ───────────────────────────────────────────
    var sortBtn = document.getElementById('sort-students-btn');
    var isAscending = true;

    if (sortBtn) {
        sortBtn.addEventListener('click', function() {
            var rows = Array.from(document.querySelectorAll('.student-row'));
            var tbody = document.getElementById('students-modal-list');

            rows.sort(function(a, b) {
                var nameA = a.querySelector('td:nth-child(2)').textContent.trim();
                var nameB = b.querySelector('td:nth-child(2)').textContent.trim();

                if (isAscending) {
                    return nameA.localeCompare(nameB);
                } else {
                    return nameB.localeCompare(nameA);
                }
            });

            // Re-append rows in sorted order
            rows.forEach(function(row) {
                tbody.appendChild(row);
            });

            // Update button state
            isAscending = !isAscending;
            if (isAscending) {
                sortBtn.innerHTML = '<i class="bi bi-sort-alpha-down me-2"></i>Sort: A-Z';
            } else {
                sortBtn.innerHTML = '<i class="bi bi-sort-alpha-up me-2"></i>Sort: Z-A';
            }
        });
    }

    // ─── Save Marks Form Submit ────────────────────────────────────────
    document.getElementById('studentsMarksForm').addEventListener('submit', function (e) {
        e.preventDefault();

        var saveBtn     = document.getElementById('saveMarksBtn');
        var saveBtnText = document.getElementById('saveBtnText');
        var modalMsg    = document.getElementById('modal-message');
        var assessmentType = document.getElementById('assessment_type').value;

        // Normalise empty inputs to 0
        var invalid = false;
        document.querySelectorAll('.mark-input').forEach(function (input) {
            if (input.value === '' || input.value === null) {
                input.value = '0';
            } else {
                var max = parseInt(input.getAttribute('max'), 10);
                var val = parseInt(input.value, 10);
                if (isNaN(val) || val < 0) {
                    input.classList.add('is-invalid');
                    invalid = true;
                } else if (val > max) {
                    input.value = max;
                }
                input.classList.remove('is-invalid');
            }
        });

        if (invalid) {
            modalMsg.innerHTML = '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Please fix invalid mark values.</div>';
            return;
        }

        saveBtn.disabled = true;
        saveBtnText.textContent = 'Saving…';

        // Build request payload as FormData (preserves file structure if needed)
        var formData = new FormData(this);
        
        // Explicitly set the form data to ensure assessment type is correctly transmitted
        formData.set('branch_id',       document.getElementById('branch_id').value);
        formData.set('course_id',       document.getElementById('course_id').value);
        formData.set('lesson_id',       document.getElementById('lesson_id').value);
        formData.set('term_id',         document.getElementById('term_id').value);
        formData.set('assessment_type', assessmentType);

        console.log('Form submission - Assessment Type:', assessmentType);

        if (assessmentType === 'exam') {
            var examId = document.getElementById('exam_id').value;
            var examDate = document.getElementById('exam_date').value;
            formData.set('exam_id',   examId);
            formData.set('exam_date', examDate);
            console.log('Exam submission - exam_id:', examId, 'exam_date:', examDate, '(auto-populated from exam database)');
        } else if (assessmentType === 'test') {
            var marksOutOf = document.getElementById('marks_out_of').value;
            var testDate = document.getElementById('test_date').value;
            formData.set('marks_out_of', marksOutOf);
            formData.set('test_date',    testDate);
            console.log('Test submission - marks_out_of:', marksOutOf, 'test_date:', testDate);
        } else {
            console.error('No assessment type selected!');
            modalMsg.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>Please select an assessment type (Test or Exam).</div>';
            saveBtn.disabled = false;
            saveBtnText.textContent = 'Save Marks';
            return;
        }

        // Get CSRF token from the form's hidden input (more reliable than page-load-time token)
        // Try multiple locations to find the token for maximum compatibility
        var csrfToken = null;
        
        // Try 1: Get from the form's _token field (from @csrf)
        var tokenInput = document.querySelector('#studentsMarksForm input[name="_token"]');
        if (tokenInput && tokenInput.value) {
            csrfToken = tokenInput.value;
        }
        
        // Try 2: Fallback to meta tag if available
        if (!csrfToken) {
            var metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                csrfToken = metaToken.getAttribute('content');
            }
        }
        
        // Try 3: Fallback to page-load-time token (last resort)
        if (!csrfToken) {
            csrfToken = '{{ csrf_token() }}';
        }
        
        // Ensure token is in FormData (it should be from @csrf, but let's be explicit)
        if (csrfToken && (!formData.has('_token') || !formData.get('_token'))) {
            formData.set('_token', csrfToken);
        }
        
        console.log('CSRF Token status:', csrfToken ? 'Found (' + csrfToken.substring(0, 10) + '...)' : 'MISSING - This will cause 419 error!');
        
        if (!csrfToken) {
            modalMsg.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><strong>Session Error:</strong> CSRF token not found. Please refresh the page and try again.</div>';
            saveBtn.disabled = false;
            saveBtnText.textContent = 'Save Marks';
            return;
        }

        fetchJson('{{ route("teacher.marks.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function (data) {
            saveBtn.disabled = false;
            saveBtnText.textContent = 'Save Marks';

            if (data.success) {
                modalMsg.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>' + data.message + '</div>';
                document.getElementById('students-modal-list').innerHTML = '';
                setTimeout(function () {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('studentsModal'));
                    if (modal) modal.hide();
                }, 2000);
            } else {
                var details = '';
                if (data.errors) {
                    Object.values(data.errors).forEach(function (msgs) {
                        details += '<li>' + (Array.isArray(msgs) ? msgs.join(', ') : msgs) + '</li>';
                    });
                    if (details) details = '<ul class="mt-2 mb-0">' + details + '</ul>';
                }
                modalMsg.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><strong>Error:</strong> ' + (data.message || 'Failed to save marks.') + details + '</div>';
            }
        })
        .catch(function (err) {
            saveBtn.disabled = false;
            saveBtnText.textContent = 'Save Marks';
            var extra = '';
            if (err && err.status) {
                extra = ' (HTTP ' + err.status + ')';
                if (err.status === 419) {
                    extra = ' - Session expired or CSRF token invalid. Please refresh the page and try again.';
                }
            }
            modalMsg.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>' + (err.message || 'Failed to save marks') + extra + '</div>';
        });
    });

    // ─── Update Show Students Button State ────────────────────────────
    function updateBtn() {
        var branchId       = document.getElementById('branch_id').value;
        var courseId       = document.getElementById('course_id').value;
        var lessonId       = document.getElementById('lesson_id').value;
        var termId         = document.getElementById('term_id').value;
        var assessmentType = document.getElementById('assessment_type').value;
        var examId         = document.getElementById('exam_id').value;
        var marksOutOf     = document.getElementById('marks_out_of').value;
        var testDate       = document.getElementById('test_date').value;

        // Exam date is auto-populated, so we don't check for it
        var ready = branchId && courseId && lessonId && termId && assessmentType
            && !(assessmentType === 'exam' && !examId)
            && !(assessmentType === 'test' && (!marksOutOf || !testDate));

        document.getElementById('showStudentsBtn').disabled = !ready;
        document.getElementById('downloadTemplateBtn').disabled = !ready;
        document.getElementById('uploadCsvBtn').disabled = !ready;
    }

    // ─── Download CSV Template ────────────────────────────────────────
    document.getElementById('downloadTemplateBtn').addEventListener('click', function() {
        var branchId       = document.getElementById('branch_id').value;
        var courseId       = document.getElementById('course_id').value;
        var lessonId       = document.getElementById('lesson_id').value;
        var termId         = document.getElementById('term_id').value;
        var assessmentType = document.getElementById('assessment_type').value;
        var examId         = document.getElementById('exam_id').value;
        var examDate       = document.getElementById('exam_date').value;
        var marksOutOf     = document.getElementById('marks_out_of').value;
        var testDate       = document.getElementById('test_date').value;

        var params = new URLSearchParams();
        params.append('branch_id', branchId);
        params.append('course_id', courseId);
        params.append('lesson_id', lessonId);
        params.append('term_id', termId);
        params.append('assessment_type', assessmentType);

        if (assessmentType === 'test') {
            params.append('marks_out_of', marksOutOf);
            params.append('test_date', testDate);
        } else {
            params.append('exam_id', examId);
            params.append('exam_date', examDate);
        }

        window.location.href = '{{ route("teacher.marks.download-template") }}?' + params.toString();
    });

    // ─── Upload CSV Marks ─────────────────────────────────────────────
    var previewMarksData = null;

    document.getElementById('uploadCsvForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        var assessmentType = document.getElementById('assessment_type').value;
        var branchId = document.getElementById('branch_id').value;
        var courseId = document.getElementById('course_id').value;
        var lessonId = document.getElementById('lesson_id').value;
        var termId = document.getElementById('term_id').value;

        console.log('Upload form values:', { branchId, courseId, lessonId, termId, assessmentType });

        if (!branchId || !courseId || !lessonId || !termId) {
            document.getElementById('uploadMessage').innerHTML =
                '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>Please ensure Branch, Course, Lesson, and Term are selected before uploading.</div>';
            return;
        }

        // Ensure CSRF token is included
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken && !formData.has('_token')) {
            formData.append('_token', csrfToken);
        }

        formData.append('branch_id', branchId);
        formData.append('course_id', courseId);
        formData.append('lesson_id', lessonId);
        formData.append('term_id', termId);
        formData.append('assessment_type', assessmentType);

        // Only send exam fields if assessment type is exam
        if (assessmentType === 'exam') {
            formData.append('exam_id', document.getElementById('exam_id').value);
            formData.append('exam_date', document.getElementById('exam_date').value);
        }

        // Only send test fields if assessment type is test
        if (assessmentType === 'test') {
            formData.append('marks_out_of', document.getElementById('marks_out_of').value);
            formData.append('test_date', document.getElementById('test_date').value);
        }

        var uploadBtn = document.getElementById('uploadCsvSubmitBtn');
        var uploadMsg = document.getElementById('uploadMessage');
        var originalText = uploadBtn.textContent;

        uploadBtn.disabled = true;
        uploadBtn.textContent = 'Uploading...';
        uploadMsg.innerHTML = '';

        fetch('{{ route("teacher.marks.upload") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function(res) {
            return res.json();
        })
        .then(function(data) {
            uploadBtn.disabled = false;
            uploadBtn.textContent = originalText;

            if (data.success && data.preview) {
                // Show preview table
                previewMarksData = data.marks;
                uploadMsg.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>' + data.message + '</div>';

                // Build preview table
                var tableHtml = '<div class="table-responsive mt-3"><table class="table table-bordered table-sm">';
                tableHtml += '<thead><tr><th>Student Name</th><th>Reg Number</th><th>Mark</th></tr></thead><tbody>';
                data.marks.forEach(function(item) {
                    tableHtml += '<tr><td>' + item.student_name + '</td><td>' + item.reg_number + '</td><td>' + item.mark + '</td></tr>';
                });
                tableHtml += '</tbody></table></div>';

                uploadMsg.innerHTML += tableHtml;

                // Change button to confirm
                uploadBtn.textContent = 'Confirm & Save Marks';
                uploadBtn.className = 'btn btn-success';
                uploadBtn.onclick = function(e) {
                    e.preventDefault();
                    confirmMarksUpload();
                };

                if (data.failedDetails && data.failedDetails.length > 0) {
                    uploadMsg.innerHTML += '<div class="alert alert-warning mt-2"><strong>Failed rows:</strong><ul class="mb-0 mt-1">';
                    data.failedDetails.forEach(function(detail) {
                        uploadMsg.innerHTML += '<li>' + detail + '</li>';
                    });
                    uploadMsg.innerHTML += '</ul></div>';
                }
            } else if (data.success) {
                uploadMsg.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>' + data.message + '</div>';
                setTimeout(function() {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
                    if (modal) modal.hide();
                }, 2000);
            } else {
                uploadMsg.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>' + (data.message || 'Upload failed') + '</div>';
            }
        })
        .catch(function(err) {
            uploadBtn.disabled = false;
            uploadBtn.textContent = originalText;
            uploadMsg.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>Upload failed: ' + (err.message || 'Unknown error') + '</div>';
        });
    });

    function confirmMarksUpload() {
        if (!previewMarksData || previewMarksData.length === 0) {
            return;
        }

        var confirmBtn = document.getElementById('uploadCsvSubmitBtn');
        var uploadMsg = document.getElementById('uploadMessage');
        var assessmentType = document.getElementById('assessment_type').value;
        var branchId = document.getElementById('branch_id').value;
        var courseId = document.getElementById('course_id').value;
        var lessonId = document.getElementById('lesson_id').value;
        var termId = document.getElementById('term_id').value;

        console.log('Confirm upload values:', { branchId, courseId, lessonId, termId, assessmentType });

        if (!branchId || !courseId || !lessonId || !termId) {
            uploadMsg.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>Please ensure Branch, Course, Lesson, and Term are selected.</div>';
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm & Save Marks';
            return;
        }

        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Saving...';

        var formData = new FormData();
        // Add CSRF token
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            formData.append('_token', csrfToken);
        }

        formData.append('branch_id', branchId);
        formData.append('course_id', courseId);
        formData.append('lesson_id', lessonId);
        formData.append('term_id', termId);
        formData.append('assessment_type', assessmentType);

        // Only send exam fields if assessment type is exam
        if (assessmentType === 'exam') {
            formData.append('exam_id', document.getElementById('exam_id').value);
            formData.append('exam_date', document.getElementById('exam_date').value);
        }

        // Only send test fields if assessment type is test
        if (assessmentType === 'test') {
            formData.append('marks_out_of', document.getElementById('marks_out_of').value);
            formData.append('test_date', document.getElementById('test_date').value);
        }

        previewMarksData.forEach(function(item, index) {
            formData.append('marks[' + index + '][student_id]', item.student_id);
            formData.append('marks[' + index + '][mark]', item.mark);
        });

        fetch('{{ route("teacher.marks.confirm-upload") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function(res) {
            return res.json();
        })
        .then(function(data) {
            if (data.success) {
                uploadMsg.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>' + data.message + '</div>';
                if (data.failedDetails && data.failedDetails.length > 0) {
                    uploadMsg.innerHTML += '<div class="alert alert-warning mt-2"><strong>Failed rows:</strong><ul class="mb-0 mt-1">';
                    data.failedDetails.forEach(function(detail) {
                        uploadMsg.innerHTML += '<li>' + detail + '</li>';
                    });
                    uploadMsg.innerHTML += '</ul></div>';
                }
                setTimeout(function() {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
                    if (modal) modal.hide();
                    // Reset form
                    document.getElementById('uploadCsvForm').reset();
                    previewMarksData = null;
                    confirmBtn.textContent = 'Upload Marks';
                    confirmBtn.className = 'btn btn-primary';
                    confirmBtn.onclick = null;
                }, 2000);
            } else {
                uploadMsg.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>' + (data.message || 'Save failed') + '</div>';
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Confirm & Save Marks';
            }
        })
        .catch(function(err) {
            uploadMsg.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>Save failed: ' + (err.message || 'Unknown error') + '</div>';
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm & Save Marks';
        });
    }

    // Attach change listeners for button state
    ['branch_id','course_id','lesson_id','assessment_type','term_id',
     'exam_id','exam_date','marks_out_of','test_date'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', updateBtn);
    });

    updateBtn();
});
</script>
@endsection