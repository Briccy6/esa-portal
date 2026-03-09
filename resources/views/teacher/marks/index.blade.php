@extends('teacher.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Enter Student Marks</h4>
                </div>
                <div class="card-body">
                    <form id="marksForm" method="POST" action="{{ route('teacher.marks.store') }}">
                        @csrf
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="branch_id" class="form-label fw-bold">Branch</label>
                                <select name="branch_id" id="branch_id" class="form-select" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="course_id" class="form-label fw-bold">Course</label>
                                <select name="course_id" id="course_id" class="form-select" required>
                                    <option value="">Select Course</option>
                                    @foreach($branches as $branch)
                                        @foreach($branch->courses as $course)
                                            <option value="{{ $course->id }}" data-branch="{{ $branch->id }}">
                                                {{ $course->title ?? $course->course_name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="lesson_id" class="form-label fw-bold">Lesson</label>
                                <select name="lesson_id" id="lesson_id" class="form-select" required>
                                    <option value="">Select Lesson</option>
                                    @foreach($lessons as $lesson)
                                        <option value="{{ $lesson->id }}" data-course="{{ $lesson->course_id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>
                                            {{ $lesson->title ?? $lesson->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="assessment_type" class="form-label fw-bold">Assessment Type</label>
                                <select name="assessment_type" id="assessment_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="test">Test</option>
                                    <option value="exam">Exam</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4" id="testMarksOutOfRow" style="display:none;">
                            <div class="col-md-3">
                                <label for="marks_out_of" class="form-label fw-bold">Marks Out Of</label>
                                <input type="number" name="marks_out_of" id="marks_out_of" class="form-control" min="1" placeholder="e.g. 20">
                            </div>
                        </div>

                        <div class="row g-3 mb-4" id="examSelectRow" style="display:none;">
                            <div class="col-md-3">
                                <label for="exam_id" class="form-label fw-bold">Select Exam</label>
                                <select name="exam_id" id="exam_id" class="form-select">
                                    <option value="">Select Exam</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end align-items-center mb-3">
                            <button type="button" class="btn btn-info px-4" id="showStudentsBtn" data-bs-toggle="modal" data-bs-target="#studentsModal">
                                <i class="bi bi-people"></i> Show Students
                            </button>
                        </div>
                        <div id="marks-message" class="mt-2"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Students Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="studentsModalLabel"><i class="bi bi-person-lines-fill"></i> Students List</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="studentsMarksForm">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Mark</th>
                        </tr>
                    </thead>
                    <tbody id="students-modal-list">
                        <!-- Students will be loaded here -->
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success px-4 mt-3">
                    <i class="bi bi-save"></i> Save Marks
                </button>
            </div>
        </form>
        <div id="modal-message" class="mt-2"></div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Icons CDN (for icons) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
    // Filter courses by branch
    document.getElementById('branch_id').addEventListener('change', function() {
        var branchId = this.value;
        var courseSelect = document.getElementById('course_id');
        var anyVisible = false;
        Array.from(courseSelect.options).forEach(function(option) {
            if (!option.value) return; // skip "Select Course"
            if (option.getAttribute('data-branch') == branchId) {
                option.style.display = 'block';
                anyVisible = true;
            } else {
                option.style.display = 'none';
            }
        });
        // Reset course selection if not in branch
        if (!anyVisible) {
            courseSelect.value = '';
        } else if (courseSelect.selectedOptions.length && courseSelect.selectedOptions[0].style.display === 'none') {
            courseSelect.value = '';
        }
        filterLessons();
        updateShowStudentsBtn();
    });

    // Filter lessons by course
    function filterLessons() {
        var courseId = document.getElementById('course_id').value;
        var lessonSelect = document.getElementById('lesson_id');
        Array.from(lessonSelect.options).forEach(function(option) {
            if (!option.value) return;
            option.style.display = option.getAttribute('data-course') == courseId ? 'block' : 'none';
        });
        if (lessonSelect.selectedOptions.length && lessonSelect.selectedOptions[0].getAttribute('data-course') != courseId) {
            lessonSelect.value = '';
        }
        updateShowStudentsBtn();
    }
    document.getElementById('course_id').addEventListener('change', filterLessons);

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
        var assessmentType = document.getElementById('assessment_type').value;
        var examId = document.getElementById('exam_id').value;
        var marksOutOf = document.getElementById('marks_out_of').value;
        var modalTbody = document.getElementById('students-modal-list');
        var modalMsg = document.getElementById('modal-message');
        modalTbody.innerHTML = '';
        modalMsg.innerHTML = '';

        if (!branchId || !courseId || !lessonId || !assessmentType || (assessmentType === 'exam' && !examId) || (assessmentType === 'test' && !marksOutOf)) {
            modalMsg.innerHTML = '<div class="text-warning">Please select all required fields.</div>';
            return;
        }

        fetch(`/api/teacher/branch/${branchId}/course/${courseId}/students`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function(student) {
                        modalTbody.innerHTML += `
                            <tr>
                                <td>${student.full_name}</td>
                                <td>
                                    <input type="number" name="marks[${student.id}][value]" class="form-control mark-input" min="0" max="${assessmentType === 'test' ? marksOutOf : 100}" required>
                                    <input type="hidden" name="students[]" value="${student.id}">
                                </td>
                            </tr>
                        `;
                    });

                    // Live validation for mark inputs
                    setTimeout(function() {
                        var markInputs = document.querySelectorAll('.mark-input');
                        markInputs.forEach(function(input) {
                            var errorSpan = document.createElement('span');
                            errorSpan.className = 'text-danger small d-block mt-1';
                            errorSpan.style.display = 'none';
                            input.parentNode.appendChild(errorSpan);

                            input.addEventListener('input', function() {
                                var max = parseInt(input.getAttribute('max'), 10);
                                var value = parseInt(input.value, 10);
                                if (value > max) {
                                    input.classList.add('is-invalid');
                                    errorSpan.textContent = `Entered marks (${value}) cannot be greater than total (${max}).`;
                                    errorSpan.style.display = 'block';
                                    input.value = max;
                                } else {
                                    input.classList.remove('is-invalid');
                                    errorSpan.textContent = '';
                                    errorSpan.style.display = 'none';
                                }
                            });
                        });
                    }, 100);
                } else {
                    modalTbody.innerHTML = '<tr><td colspan="2" class="text-danger">No students found.</td></tr>';
                }
            })
            .catch(error => {
                modalTbody.innerHTML = `<tr><td colspan="2" class="text-danger">Error loading students: ${error.message}</td></tr>`;
            });
    }

    document.getElementById('studentsMarksForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = e.target;
        var formData = new FormData(form);
        formData.append('branch_id', document.getElementById('branch_id').value);
        formData.append('course_id', document.getElementById('course_id').value);
        formData.append('lesson_id', document.getElementById('lesson_id').value);
        formData.append('assessment_type', document.getElementById('assessment_type').value);
        if (document.getElementById('assessment_type').value === 'exam') {
            formData.append('exam_id', document.getElementById('exam_id').value);
        }
        if (document.getElementById('assessment_type').value === 'test') {
            formData.append('marks_out_of', document.getElementById('marks_out_of').value);
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
            var modalMsg = document.getElementById('modal-message');
            if (data.success) {
                modalMsg.innerHTML = `<div class="text-success">${data.message}</div>`;
            } else {
                modalMsg.innerHTML = `<div class="text-danger">${data.message || 'Failed to save marks.'}</div>`;
            }
        })
        .catch(error => {
            document.getElementById('modal-message').innerHTML = `<div class="text-danger">Error: ${error.message}</div>`;
        });
    });

    function updateShowStudentsBtn() {
        var branchId = document.getElementById('branch_id').value;
        var courseId = document.getElementById('course_id').value;
        var lessonId = document.getElementById('lesson_id').value;
        var assessmentType = document.getElementById('assessment_type').value;
        var examId = document.getElementById('exam_id').value;
        var marksOutOf = document.getElementById('marks_out_of').value;
        var btn = document.getElementById('showStudentsBtn');
        if (!branchId || !courseId || !lessonId || !assessmentType ||
            (assessmentType === 'exam' && !examId) ||
            (assessmentType === 'test' && !marksOutOf)) {
            btn.disabled = true;
        } else {
            btn.disabled = false;
        }
    }
    ['branch_id', 'course_id', 'lesson_id', 'assessment_type', 'exam_id', 'marks_out_of'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', updateShowStudentsBtn);
    });
    window.addEventListener('DOMContentLoaded', updateShowStudentsBtn);
</script>
@endsection