@extends('staff.partials.sidebar_dos')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Add Marks</h4>
                </div>
                <div class="card-body">

                    <div id="alert-container"></div>

                    {{-- Step 1: Select Course & Branch --}}
                    <form id="marks-form">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Course</label>
                                <select name="course_id" id="course_id" class="form-select" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Branch</label>
                                <select name="branch_id" id="branch_id" class="form-select" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Lesson</label>
                            <select name="lesson_id" id="lesson_id" class="form-select" required>
                                <option value="">Select Lesson</option>
                                @if(auth('teacher')->check() && isset($teacherLessons))
                                    @foreach($teacherLessons as $lesson)
                                        <option value="{{ $lesson->id }}" data-course-id="{{ $lesson->course_id }}">{{ $lesson->title }}</option>
                                    @endforeach
                                @else
                                    @isset($lessons)
                                        @foreach($lessons as $lesson)
                                            <option value="{{ $lesson->id }}" data-course-id="{{ $lesson->course_id }}">{{ $lesson->title }}</option>
                                        @endforeach
                                    @endisset
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Assessment Type</label>
                            <select name="assessment_type" id="assessment_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="exam">Exam</option>
                                <option value="test">Test</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Exam / Test</label>
                            <select name="exam_id" id="exam_id" class="form-select" required>
                                <option value="">Select Exam / Test</option>
                                {{-- Populated dynamically --}}
                            </select>
                        </div>

                        {{-- (Optional) Status --}}
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="recorded" selected>Recorded</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                            </select>
                        </div>

                        <button type="button" id="load-students" class="btn btn-primary mb-3">Show Students</button>

                        {{-- Students Table --}}
                        <div id="students-table-container"></div>

                        <button type="submit" id="save-marks" class="btn btn-success d-none">Save Marks</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const examsByCourse = @json($examsByCourse);
    const testsByCourse = @json($testsByCourse ?? []); // pass tests from controller similarly
    const courseSelect = document.getElementById('course_id');
    const examSelect = document.getElementById('exam_id');
    const assessmentType = document.getElementById('assessment_type');
    const lessonSelect = document.getElementById('lesson_id');
    const studentsTableContainer = document.getElementById('students-table-container');
    const saveButton = document.getElementById('save-marks');
    const alertContainer = document.getElementById('alert-container');

    // Auto-select course when lesson is selected (for teachers)
    lessonSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const courseId = selectedOption.getAttribute('data-course-id');
        if (courseId) {
            courseSelect.value = courseId;
            populateExamDropdown();
        }
    });

    // Populate Exam/Test Dropdown based on type
    function populateExamDropdown() {
        const courseId = courseSelect.value;
        const type = assessmentType.value;
        examSelect.innerHTML = '<option value="">Select Exam / Test</option>';
        if (!courseId || !type) return;

        const list = type === 'exam' ? examsByCourse[courseId] ?? [] : testsByCourse[courseId] ?? [];
        list.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.title;
            examSelect.appendChild(opt);
        });
    }

    courseSelect.addEventListener('change', populateExamDropdown);
    assessmentType.addEventListener('change', populateExamDropdown);

    // Load students & existing marks
    document.getElementById('load-students').addEventListener('click', function() {
        const courseId = courseSelect.value;
        const branchId = document.getElementById('branch_id').value;
        const type = assessmentType.value;
        const examId = examSelect.value;

        if (!courseId || !branchId || !type || !examId || !lessonSelect.value) {
            alert('Select course, branch, lesson, type, and exam/test.');
            return;
        }

        fetch(`/marks/ajax-students?course_id=${courseId}&branch_id=${branchId}&type=${type}&exam_id=${examId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    let html = '<table class="table table-bordered"><thead><tr><th>Student</th><th>Mark</th></tr></thead><tbody>';
                    data.students.forEach(student => {
                        const markValue = data.marks?.[student.id]?.value ?? '';
                        html += `<tr>
                                    <td>${student.full_name}<input type="hidden" name="students[]" value="${student.id}"></td>
                                    <td><input type="number" name="marks[${student.id}][value]" class="form-control" min="0" max="100" value="${markValue}" required></td>
                                 </tr>`;
                    });
                    html += '</tbody></table>';
                    studentsTableContainer.innerHTML = html;
                    saveButton.classList.remove('d-none');
                } else {
                    studentsTableContainer.innerHTML = '<div class="alert alert-warning">' + data.message + '</div>';
                    saveButton.classList.add('d-none');
                }
            });
    });

    // AJAX form submission
    document.getElementById('marks-form').addEventListener('submit', function(e) {
        e.preventDefault();
        alertContainer.innerHTML = '';

        const formData = new FormData(this);

        fetch("{{ route('teacher.marks.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alertContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                studentsTableContainer.innerHTML = '';
                saveButton.classList.add('d-none');
            } else {
                let errorsHtml = '<ul>';
                for (let key in data.errors) {
                    data.errors[key].forEach(msg => { errorsHtml += `<li>${msg}</li>`; });
                }
                errorsHtml += '</ul>';
                alertContainer.innerHTML = `<div class="alert alert-danger">${errorsHtml}</div>`;
            }
        })
        .catch(err => {
            alertContainer.innerHTML = `<div class="alert alert-danger">Server error. Try again.</div>`;
            console.error(err);
        });
    });
});
</script>
@endsection
