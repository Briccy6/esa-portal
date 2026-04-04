@extends('admin.layout')

@section('title', 'Edit Exam')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* ============================================
   ROOT & RESET
============================================ */
:root {
    --primary:       #4f46e5;
    --primary-light: #eef2ff;
    --primary-dark:  #3730a3;
    --success:       #10b981;
    --success-light: #ecfdf5;
    --danger:        #ef4444;
    --warning:       #f59e0b;
    --surface:       #ffffff;
    --surface-2:     #f8fafc;
    --border:        #e2e8f0;
    --border-focus:  #4f46e5;
    --text-primary:  #0f172a;
    --text-secondary:#64748b;
    --text-muted:    #94a3b8;
    --shadow-sm:     0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:     0 4px 12px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shadow-lg:     0 10px 30px rgba(0,0,0,.10), 0 4px 10px rgba(0,0,0,.06);
    --radius-sm:     8px;
    --radius-md:     12px;
    --radius-lg:     16px;
    --transition:    all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

* { box-sizing: border-box; }

body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f1f5f9;
    color: var(--text-primary);
    -webkit-font-smoothing: antialiased;
}

/* ============================================
   PAGE WRAPPER
============================================ */
.lc-page {
    padding: 24px 16px;
    max-width: 1100px;
    margin: 0 auto;
}

.lc-header {
    margin-bottom: 32px;
}

.lc-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.lc-subtitle {
    font-size: 15px;
    color: var(--text-secondary);
    line-height: 1.5;
}

/* ============================================
   CARD LAYOUT
============================================ */
.lc-card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    margin-bottom: 24px;
    overflow: hidden;
}

.lc-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
    background: var(--surface-2);
}

.lc-card-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.lc-card-body {
    padding: 24px;
}

/* ============================================
   FORM ELEMENTS
============================================ */
.lc-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.lc-form-group {
    display: flex;
    flex-direction: column;
}

.lc-form-group.full-width {
    grid-column: 1 / -1;
}

.lc-label {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.lc-label .required {
    color: var(--danger);
    font-weight: 600;
}

.lc-input,
.lc-select,
.lc-textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-family: inherit;
    background: var(--surface);
    color: var(--text-primary);
    transition: var(--transition);
}

.lc-input:focus,
.lc-select:focus,
.lc-textarea:focus {
    outline: none;
    border-color: var(--border-focus);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.lc-textarea {
    resize: vertical;
    min-height: 100px;
}

/* ============================================
   SELECT2 STYLING
============================================ */
.select2-container--default .select2-selection--single {
    height: 42px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    padding: 0 14px;
    background: var(--surface);
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--text-primary);
    font-size: 14px;
    padding: 0;
    line-height: normal;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    right: 8px;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: var(--border-focus);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* ============================================
   BUTTONS
============================================ */
.lc-btn {
    padding: 10px 20px;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.lc-btn-primary {
    background: var(--primary);
    color: white;
}

.lc-btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.lc-btn-secondary {
    background: var(--surface-2);
    color: var(--text-primary);
    border: 1px solid var(--border);
}

.lc-btn-secondary:hover {
    background: var(--border);
}

.lc-btn-group {
    display: flex;
    gap: 12px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid var(--border);
}

/* ============================================
   ALERTS
============================================ */
.lc-alert {
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    margin-bottom: 20px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.lc-alert-success {
    background: var(--success-light);
    color: var(--success);
    border: 1px solid #34d399;
}

.lc-alert-error {
    background: #fef2f2;
    color: var(--danger);
    border: 1px solid #fca5a5;
}

/* ============================================
   TOKEN SECTION
============================================ */
.lc-token-section {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border: 1px solid #cbd5e1;
    border-radius: var(--radius-md);
    padding: 20px;
    margin-bottom: 24px;
}

.lc-token-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.lc-token-description {
    font-size: 13px;
    color: var(--text-secondary);
    margin-bottom: 16px;
    line-height: 1.5;
}

.lc-token-input-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.lc-token-input {
    max-width: 200px;
}

.lc-token-badge {
    background: var(--primary);
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
}

/* ============================================
   PUBLIC SECTION
============================================ */
.lc-public-section {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #7dd3fc;
    border-radius: var(--radius-md);
    padding: 20px;
    margin-bottom: 24px;
}

.lc-public-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.lc-public-description {
    font-size: 13px;
    color: var(--text-secondary);
    margin-bottom: 16px;
    line-height: 1.5;
}

/* ============================================
   RESPONSIVE
============================================ */
@media (max-width: 768px) {
    .lc-page {
        padding: 16px 12px;
    }
    
    .lc-form-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .lc-card-body {
        padding: 20px;
    }
    
    .lc-btn-group {
        flex-direction: column;
    }
    
    .lc-token-input-group {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .lc-token-input {
        max-width: 100%;
        width: 100%;
    }
}
</style>
@endsection

@section('content')
<div class="lc-page">
    <!-- Page Header -->
    <div class="lc-header">
        <h1 class="lc-title">Edit Exam</h1>
        <p class="lc-subtitle">Modify exam details and token requirements</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="lc-alert lc-alert-success">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="lc-alert lc-alert-error">
            ⚠ {{ session('error') }}
        </div>
    @endif

    <!-- Edit Form -->
    <div class="lc-card">
        <div class="lc-card-header">
            <h2 class="lc-card-title">Exam Information</h2>
        </div>
        <div class="lc-card-body">
            <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="lc-form-grid">
                    <div class="lc-form-group">
                        <label class="lc-label">
                            Exam Title <span class="required">*</span>
                        </label>
                        <input type="text" name="title" class="lc-input" value="{{ old('title', $exam->title) }}" required>
                        @error('title')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="lc-form-group">
                        <label class="lc-label">
                            Exam Date <span class="required">*</span>
                        </label>
                        <input type="date" name="class_date" class="lc-input" value="{{ old('class_date', $exam->class_date->format('Y-m-d')) }}" required>
                        @error('class_date')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="lc-form-group">
                        <label class="lc-label">
                            Total Marks <span class="required">*</span>
                        </label>
                        <input type="number" name="total_marks" class="lc-input" value="{{ old('total_marks', $exam->total_marks) }}" min="1" required>
                        @error('total_marks')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="lc-form-group">
                        <label class="lc-label">
                            Exam Weight (%) <span class="required">*</span>
                        </label>
                        <input type="number" name="exam_weight" class="lc-input" value="{{ old('exam_weight', $exam->exam_weight) }}" min="1" max="100" required>
                        @error('exam_weight')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="lc-form-grid">
                    <div class="lc-form-group">
                        <label class="lc-label">
                            Exam Type <span class="required">*</span>
                        </label>
                        <select name="exam_type" class="lc-select" required>
                            <option value="">Select Exam Type</option>
                            <option value="quiz" {{ old('exam_type', $exam->exam_type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                            <option value="test" {{ old('exam_type', $exam->exam_type) == 'test' ? 'selected' : '' }}>Test</option>
                            <option value="midterm" {{ old('exam_type', $exam->exam_type) == 'midterm' ? 'selected' : '' }}>Midterm</option>
                            <option value="final" {{ old('exam_type', $exam->exam_type) == 'final' ? 'selected' : '' }}>Final Exam</option>
                            <option value="assignment" {{ old('exam_type', $exam->exam_type) == 'assignment' ? 'selected' : '' }}>Assignment</option>
                        </select>
                        @error('exam_type')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="lc-form-group">
                        <label class="lc-label">
                            Branch <span class="required">*</span>
                        </label>
                        <select name="branch_id" class="lc-select" required>
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $exam->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="lc-form-group">
                        <label class="lc-label">
                            Academic Year <span class="required">*</span>
                        </label>
                        <select name="academic_year_id" class="lc-select" required>
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}" {{ old('academic_year_id', $exam->academic_year_id) == $academicYear->id ? 'selected' : '' }}>
                                    {{ $academicYear->year }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="lc-form-group">
                        <label class="lc-label">
                            Teacher <span class="required">*</span>
                        </label>
                        <select name="teacher_id" class="lc-select" required>
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id', $exam->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->teacher_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="lc-form-group">
                        <label class="lc-label">
                            Term <span class="required">*</span>
                        </label>
                        <select name="term_id" class="lc-select" required>
                            <option value="">Select Term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ old('term_id', $exam->term_id) == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('term_id')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Token Configuration -->
                <div class="lc-token-section">
                    <h3 class="lc-token-title">
                        🔑 Token Configuration
                    </h3>
                    <p class="lc-token-description">
                        Set the number of tokens required for students to attempt this exam. Students must have sufficient tokens in their account to access the exam.
                    </p>
                    <div class="lc-form-grid">
                        <div class="lc-form-group">
                            <label class="lc-label">
                                Token Cost <span class="required">*</span>
                            </label>
                            <div class="lc-token-input-group">
                                <input type="number" name="token_cost" class="lc-input lc-token-input" value="{{ old('token_cost', $exam->token_cost ?? 0) }}" min="0" required>
                                <span class="lc-token-badge">tokens</span>
                            </div>
                            @error('token_cost')
                                <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    @if($exam->token_cost > 0)
                        <div style="margin-top: 12px; padding: 8px 12px; background: #fef3c7; border: 1px solid #fcd34d; border-radius: 6px; font-size: 13px; color: #92400e;">
                            <strong>Current Status:</strong> Students need {{ $exam->token_cost }} tokens to attempt this exam.
                        </div>
                    @else
                        <div style="margin-top: 12px; padding: 8px 12px; background: #ecfdf5; border: 1px solid #34d399; border-radius: 6px; font-size: 13px; color: #065f46;">
                            <strong>Current Status:</strong> This exam is free for all students.
                        </div>
                    @endif
                </div>

                <!-- Courses and Lessons -->
                <div class="lc-form-grid">
                    <div class="lc-form-group">
                        <label class="lc-label">
                            Courses <span class="required">*</span>
                        </label>
                        <select name="course_ids[]" class="lc-select" multiple="multiple" required>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ in_array($course->id, $exam->courses->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_ids')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="lc-form-group">
                        <label class="lc-label">
                            Lessons <span class="required">*</span>
                        </label>
                        <select name="lesson_ids[]" class="lc-select" multiple="multiple" required>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ in_array($lesson->id, $exam->lessons->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $lesson->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('lesson_ids')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="lc-form-grid">
                    <div class="lc-form-group full-width">
                        <label class="lc-label">Description</label>
                        <textarea name="description" class="lc-textarea" placeholder="Enter exam description...">{{ old('description', $exam->description) }}</textarea>
                        @error('description')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="lc-form-group full-width">
                        <label class="lc-label">Instructions</label>
                        <textarea name="instructions" class="lc-textarea" placeholder="Enter exam instructions...">{{ old('instructions', $exam->instructions ?? '') }}</textarea>
                        @error('instructions')
                            <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Public Access Settings -->
                <div class="lc-public-section">
                    <h3 class="lc-public-title">
                        🌐 Public Access Settings
                    </h3>
                    <p class="lc-public-description">
                        Configure public access for this exam. When enabled, external users can access and attempt this exam.
                    </p>
                    <div class="lc-form-grid">
                        <div class="lc-form-group">
                            <label class="lc-label">
                                <input type="checkbox" name="is_public" value="1" {{ old('is_public', $exam->is_public) ? 'checked' : '' }} class="mr-2">
                                Enable Public Access
                            </label>
                            <p class="text-sm text-gray-600 mt-1">Allow external users to access this exam</p>
                        </div>
                        
                        <div class="lc-form-group">
                            <label class="lc-label">
                                Public Price (RWF)
                            </label>
                            <input type="number" name="public_price" class="lc-input" value="{{ old('public_price', $exam->public_price ?? 0) }}" min="0" step="0.01">
                            <p class="text-sm text-gray-600 mt-1">Price for public access (0 = free)</p>
                            @error('public_price')
                                <small style="color: var(--danger); margin-top: 4px; display: block;">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    
                    @if($exam->is_public)
                        <div style="margin-top: 12px; padding: 8px 12px; background: #ecfdf5; border: 1px solid #34d399; border-radius: 6px; font-size: 13px; color: #065f46;">
                            <strong>Current Status:</strong> This exam is publicly accessible at {{ route('public.exams.show', $exam) }}
                        </div>
                    @else
                        <div style="margin-top: 12px; padding: 8px 12px; background: #fef3c7; border: 1px solid #fcd34d; border-radius: 6px; font-size: 13px; color: #92400e;">
                            <strong>Current Status:</strong> This exam is only accessible to registered students.
                        </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="lc-btn-group">
                    <button type="submit" class="lc-btn lc-btn-primary">
                        💾 Update Exam
                    </button>
                    <a href="{{ route('admin.exams.index') }}" class="lc-btn lc-btn-secondary">
                        ← Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.lc-select').select2({
        theme: 'default',
        width: '100%',
        placeholder: function() {
            return $(this).attr('name').includes('course') ? 'Select courses...' : 'Select lessons...';
        }
    });

    // Store lesson-teacher mapping
    let lessonTeacherMap = {};

    // Lesson selection auto-populates teacher
    $('select[name="lesson_ids[]"]').on('change', function() {
        const selectedLessonIds = $(this).val();
        
        if (selectedLessonIds && selectedLessonIds.length > 0) {
            const firstLessonId = selectedLessonIds[0];
            if (lessonTeacherMap[firstLessonId]) {
                // Auto-populate the teacher field
                $('select[name="teacher_id"]').val(lessonTeacherMap[firstLessonId].teacher_id).trigger('change.select2');
                console.log('Teacher auto-selected:', lessonTeacherMap[firstLessonId].teacher_name);
            }
        }
    });

    // Branch selection dynamically loads lessons and courses
    $('select[name="branch_id"]').on('change', function() {
        const branchId = $(this).val();
        const courseSelect = $('select[name="course_ids[]"]');
        const lessonSelect = $('select[name="lesson_ids[]"]');
        
        if (branchId) {
            $.ajax({
                url: `/admin/exams/api/lessons-courses/${branchId}`,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        // Store lesson-teacher mapping
                        lessonTeacherMap = data.lessonTeachers || {};
                        
                        // Populate courses
                        courseSelect.empty();
                        if (data.courses && data.courses.length > 0) {
                            data.courses.forEach(function(course) {
                                courseSelect.append(`<option value="${course.id}">${course.name}</option>`);
                            });
                        } else {
                            courseSelect.append('<option value="">No courses available for this branch</option>');
                        }
                        courseSelect.trigger('change.select2');
                        
                        // Populate lessons
                        lessonSelect.empty();
                        if (data.lessons && data.lessons.length > 0) {
                            data.lessons.forEach(function(lesson) {
                                lessonSelect.append(`<option value="${lesson.id}">${lesson.title}</option>`);
                            });
                        } else {
                            lessonSelect.append('<option value="">No lessons available for this branch</option>');
                        }
                        lessonSelect.trigger('change.select2');
                    } else {
                        console.error('Error from API:', data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        }
    });

    // Token cost validation
    $('input[name="token_cost"]').on('input', function() {
        var value = parseInt($(this).val());
        if (value < 0) {
            $(this).val(0);
        }
    });

    // Form submission confirmation
    $('form').on('submit', function(e) {
        var tokenCost = parseInt($('input[name="token_cost"]').val());
        if (tokenCost > 0) {
            if (!confirm('This exam will require ' + tokenCost + ' tokens for students to attempt. Continue?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>
@endsection
