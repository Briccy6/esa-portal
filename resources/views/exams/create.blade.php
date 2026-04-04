@extends('admin.layout')

@section('title', 'Create Exam')

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

/* ============================================
   CARD SHELL
============================================ */
.lc-card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid var(--border);
}

/* ============================================
   PAGE HEADER
============================================ */
.lc-header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6366f1 100%);
    padding: 32px 36px;
    position: relative;
    overflow: hidden;
}

.lc-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3Ccircle cx='0' cy='0' r='20'/%3E%3Ccircle cx='60' cy='60' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.lc-header-inner {
    position: relative;
    display: flex;
    align-items: center;
    gap: 16px;
}

.lc-header-icon {
    width: 56px;
    height: 56px;
    background: rgba(255,255,255,0.15);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #fff;
    flex-shrink: 0;
    backdrop-filter: blur(4px);
}

.lc-header-text h1 {
    color: #fff;
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 4px 0;
    line-height: 1.2;
}

.lc-header-text p {
    color: rgba(255,255,255,0.85);
    font-size: 15px;
    margin: 0;
    line-height: 1.4;
}

/* ============================================
   FORM BODY
============================================ */
.lc-body {
    padding: 36px;
}

.lc-section {
    margin-bottom: 40px;
}

.lc-section:last-child {
    margin-bottom: 0;
}

.lc-section-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.lc-section-title i {
    color: var(--primary);
    font-size: 16px;
}

.lc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.lc-field {
    display: flex;
    flex-direction: column;
}

.lc-label {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-label);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.lc-label .required {
    color: var(--danger);
    font-weight: 600;
}

.lc-input-wrap {
    position: relative;
}

.lc-input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 14px;
    z-index: 10;
    pointer-events: none;
}

.lc-input,
.lc-select,
.lc-textarea {
    width: 100%;
    padding: 12px 14px 12px 40px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-family: inherit;
    background: var(--surface-2);
    color: var(--text-primary);
    transition: var(--transition);
    outline: none;
}

.lc-input:focus,
.lc-select:focus,
.lc-textarea:focus {
    border-color: var(--border-focus);
    background: var(--surface);
    box-shadow: 0 0 0 3px var(--border-focus-glow);
}

.lc-input::placeholder,
.lc-select::placeholder {
    color: var(--text-muted);
}

.lc-textarea {
    resize: vertical;
    min-height: 100px;
    padding-top: 14px;
    padding-bottom: 14px;
}

/* Select2 overrides */
.lc-select2-single,
.lc-select2-multi {
    width: 100% !important;
}

.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {
    border: 1px solid var(--border) !important;
    border-radius: var(--radius-sm) !important;
    background: var(--surface-2) !important;
    color: var(--text-primary) !important;
    padding: 8px 12px !important;
    min-height: 44px !important;
    display: flex;
    align-items: center;
    font-size: 14px !important;
}

.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: var(--border-focus) !important;
    background: var(--surface) !important;
    box-shadow: 0 0 0 3px var(--border-focus-glow) !important;
}

.select2-container--default .select2-selection__choice {
    background: var(--primary-light) !important;
    color: var(--primary-dark) !important;
    border: 1px solid var(--primary) !important;
    border-radius: var(--radius-sm) !important;
    font-size: 13px !important;
    margin: 2px !important;
    padding: 4px 8px !important;
}

.select2-container--default .select2-selection__choice__remove {
    color: var(--primary-dark) !important;
    margin-right: 4px !important;
}

.select2-dropdown {
    border: 1px solid var(--border) !important;
    border-radius: var(--radius-sm) !important;
    box-shadow: var(--shadow-lg) !important;
    background: var(--surface) !important;
    font-size: 14px !important;
}

.select2-results__option {
    padding: 10px 14px !important;
    color: var(--text-primary) !important;
}

.select2-results__option--highlighted {
    background: var(--primary-light) !important;
    color: var(--primary-dark) !important;
}

.lc-hint {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 6px;
    line-height: 1.4;
}

.lc-error {
    font-size: 12px;
    color: var(--danger);
    margin-top: 6px;
    line-height: 1.4;
}

/* ============================================
   BUTTONS
============================================ */
.lc-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 24px;
    border-top: 1px solid var(--border);
    margin-top: 32px;
}

.lc-btn {
    padding: 10px 20px;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 500;
    font-family: inherit;
    border: none;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    outline: none;
}

.lc-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.lc-btn-primary {
    background: var(--primary);
    color: white;
}

.lc-btn-primary:hover:not(:disabled) {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.lc-btn-secondary {
    background: var(--surface-2);
    color: var(--text-secondary);
    border: 1px solid var(--border);
}

.lc-btn-secondary:hover:not(:disabled) {
    background: var(--surface);
    color: var(--text-primary);
    border-color: var(--border-focus);
}

/* ============================================
   RESPONSIVE
============================================ */
@media (max-width: 768px) {
    .lc-page {
        padding: 16px 12px;
    }
    
    .lc-header {
        padding: 24px 20px;
    }
    
    .lc-body {
        padding: 24px 20px;
    }
    
    .lc-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .lc-actions {
        flex-direction: column;
    }
    
    .lc-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection

@section('content')
<div class="lc-page">
    <div class="lc-card">
        <!-- Header -->
        <div class="lc-header">
            <div class="lc-header-inner">
                <div class="lc-header-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="lc-header-text">
                    <h1>Create New Exam</h1>
                    <p>Schedule and configure exam settings</p>
                </div>
            </div>
        </div>

        <!-- Form Body -->
        <div class="lc-body">
            <form method="POST" action="{{ route('admin.exams.store') }}" id="examForm">
                @csrf

                <!-- Basic Information -->
                <div class="lc-section">
                    <h2 class="lc-section-title">
                        <i class="fas fa-info-circle"></i>
                        Basic Information
                    </h2>
                    
                    <div class="lc-grid">
                        <div class="lc-field">
                            <label class="lc-label" for="title">
                                Exam Title <span class="required">*</span>
                            </label>
                            <div class="lc-input-wrap">
                                <i class="fas fa-heading lc-input-icon"></i>
                                <input type="text" class="lc-input" id="title" name="title" 
                                       placeholder="Enter exam title" value="{{ old('title') }}" required>
                            </div>
                            @error('title')
                                <div class="lc-error">{{ $errors->first('title') }}</div>
                            @enderror
                        </div>

                        <div class="lc-field">
                            <label class="lc-label" for="exam_type">
                                Exam Type <span class="required">*</span>
                            </label>
                            <div class="lc-input-wrap">
                                <i class="fas fa-list lc-input-icon"></i>
                                <select class="lc-select" id="exam_type" name="exam_type" required>
                                    <option value="">Select exam type</option>
                                    <option value="quiz" {{ old('exam_type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    <option value="test" {{ old('exam_type') == 'test' ? 'selected' : '' }}>Test</option>
                                    <option value="midterm" {{ old('exam_type') == 'midterm' ? 'selected' : '' }}>Midterm</option>
                                    <option value="final" {{ old('exam_type') == 'final' ? 'selected' : '' }}>Final Exam</option>
                                    <option value="assignment" {{ old('exam_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                </select>
                            </div>
                            @error('exam_type')
                                <div class="lc-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="lc-field">
                            <label class="lc-label" for="class_date">
                                Exam Date <span class="required">*</span>
                            </label>
                            <div class="lc-input-wrap">
                                <i class="fas fa-calendar lc-input-icon"></i>
                                <input type="date" class="lc-input" id="class_date" name="class_date" 
                                       value="{{ old('class_date') }}" required>
                            </div>
                            @error('class_date')
                                <div class="lc-error">{{ $errors->first('class_date') }}</div>
                            @enderror
                        </div>

                        <div class="lc-field">
                            <label class="lc-label" for="exam_weight">
                                Exam Weight (%) <span class="required">*</span>
                            </label>
                            <div class="lc-input-wrap">
                                <i class="fas fa-percentage lc-input-icon"></i>
                                <input type="number" class="lc-input" id="exam_weight" name="exam_weight" 
                                       placeholder="e.g., 20" min="1" max="100" value="{{ old('exam_weight') }}" required>
                            </div>
                            @error('exam_weight')
                                <div class="lc-error">{{ $errors->first('exam_weight') }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="lc-field" style="margin-top: 24px;">
                        <label class="lc-label" for="description">Description</label>
                        <div class="lc-input-wrap">
                            <i class="fas fa-align-left lc-input-icon" style="top: 14px;"></i>
                            <textarea class="lc-textarea" id="description" name="description" 
                                      placeholder="Enter exam description and instructions">{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <div class="lc-error">{{ $errors->first('description') }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Academic Assignment -->
                <div class="lc-section">
                    <h2 class="lc-section-title">
                        <i class="fas fa-graduation-cap"></i>
                        Academic Assignment
                    </h2>
                    
                    <div class="lc-grid">
                        <div class="lc-field">
                            <label class="lc-label" for="branch_id">
                                Branch <span class="required">*</span>
                            </label>
                            <div class="lc-input-wrap">
                                <i class="fas fa-building lc-input-icon"></i>
                                <select class="lc-select lc-select2-single" id="branch_id" name="branch_id" required
                                        data-placeholder="Select branch">
                                    <option value=""></option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('branch_id')
                                <div class="lc-error">{{ $message }}</div>
                            @enderror
                            <p class="lc-hint">Select the branch for this exam</p>
                        </div>

                        <div class="lc-field">
                            <label class="lc-label" for="course_ids">
                                Courses <span class="required">*</span>
                            </label>
                            <div class="lc-input-wrap">
                                <i class="fas fa-book lc-input-icon"></i>
                                <select class="lc-select lc-select2-multi" id="course_ids" name="course_ids[]" multiple required
                                        data-placeholder="Select courses">
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_ids') && in_array($course->id, old('course_ids')) ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('course_ids')
                                <div class="lc-error">{{ $message }}</div>
                            @enderror
                            <p class="lc-hint">Select one or more courses. Filtered by selected branch.</p>
                        </div>

                        <div class="lc-field">
                            <label class="lc-label" for="lesson_ids">
                                Lessons <span class="required">*</span>
                            </label>
                            <div class="lc-input-wrap">
                                <i class="fas fa-chalkboard lc-input-icon"></i>
                                <select class="lc-select lc-select2-multi" id="lesson_ids" name="lesson_ids[]" multiple required
                                        data-placeholder="Select lessons">
                                    @foreach($lessons as $lesson)
                                        <option value="{{ $lesson->id }}" {{ old('lesson_ids') && in_array($lesson->id, old('lesson_ids')) ? 'selected' : '' }}>
                                            {{ $lesson->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('lesson_ids')
                                <div class="lc-error">{{ $message }}</div>
                            @enderror
                            <p class="lc-hint">Select one or more lessons. Filtered by selected courses.</p>
                        </div>

                        <div class="lc-field">
                            <label class="lc-label" for="teacher_id">
                                Teacher <span class="required">*</span>
                            </label>
                            <div class="lc-input-wrap">
                                <i class="fas fa-chalkboard-teacher lc-input-icon"></i>
                                <select class="lc-select lc-select2-single" id="teacher_id" name="teacher_id" required
                                        data-placeholder="Select teacher">
                                    <option value=""></option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->teacher_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('teacher_id')
                                <div class="lc-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="lc-field">
                            <label class="lc-label" for="academic_year_id">
                                Academic Year <span class="required">*</span>
                            </label>
                            <div class="lc-input-wrap">
                                <i class="fas fa-calendar-alt lc-input-icon"></i>
                                <select class="lc-select lc-select2-single" id="academic_year_id" name="academic_year_id" required
                                        data-placeholder="Select academic year">
                                    <option value=""></option>
                                    @foreach($academicYears as $academicYear)
                                        <option value="{{ $academicYear->id }}" {{ old('academic_year_id') == $academicYear->id ? 'selected' : '' }}>
                                            {{ $academicYear->year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('academic_year_id')
                                <div class="lc-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="lc-field">
                            <label class="lc-label" for="term_id">
                                Term <span class="required">*</span>
                            </label>
                            <div class="lc-input-wrap">
                                <i class="fas fa-layer-group lc-input-icon"></i>
                                <select class="lc-select lc-select2-single" id="term_id" name="term_id" required
                                        data-placeholder="Select term">
                                    <option value=""></option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                            {{ $term->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('term_id')
                                <div class="lc-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Exam Settings -->
                <div class="lc-section">
                    <h2 class="lc-section-title">
                        <i class="fas fa-cog"></i>
                        Exam Settings
                    </h2>
                    
                    <div class="lc-grid">
                        <div class="lc-field">
                            <label class="lc-label" for="total_marks">
                                Total Marks <span class="required">*</span>
                            </label>
                            <div class="lc-input-wrap">
                                <i class="fas fa-star lc-input-icon"></i>
                                <input type="number" class="lc-input" id="total_marks" name="total_marks" 
                                       placeholder="e.g., 100" min="1" value="{{ old('total_marks') }}" required>
                            </div>
                            @error('total_marks')
                                <div class="lc-error">{{ $errors->first('total_marks') }}</div>
                            @enderror
                        </div>

                        <div class="lc-field">
                            <label class="lc-label" for="exam_password">Exam Password</label>
                            <div class="lc-input-wrap">
                                <i class="fas fa-key lc-input-icon"></i>
                                <input type="text" class="lc-input" id="exam_password" name="exam_password" 
                                       placeholder="Auto-generated" value="{{ old('exam_password', $examPassword) }}">
                            </div>
                            <p class="lc-hint">Auto-generated password for exam access</p>
                            @error('exam_password')
                                <div class="lc-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="lc-field" style="margin-top: 24px;">
                        <label class="lc-label" for="notes">Additional Notes</label>
                        <div class="lc-input-wrap">
                            <i class="fas fa-sticky-note lc-input-icon" style="top: 14px;"></i>
                            <textarea class="lc-textarea" id="notes" name="notes" 
                                      placeholder="Any additional notes or instructions">{{ old('notes') }}</textarea>
                        </div>
                        @error('notes')
                            <div class="lc-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Question Bank Selection -->
                <div class="lc-section">
                    <h2 class="lc-section-title">
                        <i class="fas fa-question-circle"></i>
                        Questions from Question Banks
                    </h2>
                    
                    @if($questionBanks->count() > 0)
                        <div class="lc-grid">
                            <div class="lc-field">
                                <label class="lc-label">Select Question Banks</label>
                                <div class="lc-input-wrap">
                                    <i class="fas fa-question-circle lc-input-icon"></i>
                                    <select name="question_bank_ids[]" id="question_bank_ids" class="lc-select lc-select2-multi" multiple
                                            data-placeholder="Select question banks">
                                        @foreach($questionBanks as $questionBank)
                                            <option value="{{ $questionBank->id }}">
                                                {{ $questionBank->name }} ({{ $questionBank->questions->count() }} questions)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="lc-hint">Select one or more question banks to import questions from</p>
                            </div>
                            
                            <div class="lc-field">
                                <label class="lc-label">Number of Questions per Bank</label>
                                <input type="number" name="questions_per_bank" id="questions_per_bank" class="lc-input" 
                                       min="1" max="50" value="10" placeholder="e.g., 10">
                                <small class="lc-help">How many questions to randomly select from each bank</small>
                            </div>
                        </div>
                        
                        <div class="lc-field">
                            <button type="button" id="previewQuestions" class="lc-btn lc-btn-secondary">
                                <i class="fas fa-eye"></i>
                                Preview Selected Questions
                            </button>
                            <div id="questionsPreview" class="mt-3" style="display: none;">
                                <h4>Questions Preview:</h4>
                                <div id="questionsList"></div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>No Question Banks Available</strong>
                            <p class="mb-0">There are no question banks in the system. You need to create question banks first before you can import questions from them.</p>
                            <hr>
                            <a href="#" class="btn btn-primary" onclick="alert('Question bank creation not yet implemented')">
                                <i class="fas fa-plus"></i> Create Question Bank
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="lc-actions">
                    <a href="{{ route('admin.exams.index') }}" class="lc-btn lc-btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="lc-btn lc-btn-primary">
                        <i class="fas fa-save"></i>
                        Create Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Common Select2 configuration
    const s2common = {
        theme: 'bootstrap-5',
        width: '100%',
        allowClear: true,
        dropdownParent: $('.lc-body')
    };

    // Initialize all Select2 dropdowns
    $('.lc-select2-single').select2({
        ...s2common,
        placeholder: function() {
            return $(this).data('placeholder');
        }
    });

    $('.lc-select2-multi').select2({
        ...s2common,
        placeholder: function() {
            return $(this).data('placeholder');
        }
    });

    // Store lesson-teacher mapping
    let lessonTeacherMap = {};

    // Branch selection changes courses and lessons using lesson_branch_course table
    $('#branch_id').on('change', function() {
        const branchId = $(this).val();
        const courseSelect = $('#course_ids');
        const lessonSelect = $('#lesson_ids');
        
        if (branchId) {
            // Fetch lessons and courses for the selected branch from lesson_branch_course table
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
                        courseSelect.empty().append('<option value="">Error loading courses</option>');
                        lessonSelect.empty().append('<option value="">Error loading lessons</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Status:', status);
                    courseSelect.empty().append('<option value="">Error loading courses</option>');
                    lessonSelect.empty().append('<option value="">Error loading lessons</option>');
                }
            });
        } else {
            // If no branch selected, clear both dropdowns
            courseSelect.empty().append('<option value="">Select a branch first</option>');
            lessonSelect.empty().append('<option value="">Select a branch first</option>');
        }
    });

    // Course selection changes lessons (kept for backward compatibility)
    $('#course_ids').on('change', function() {
        // Lessons are now loaded directly from branch selection when using lesson_branch_course table
        // This is kept for potential future enhancements
    });

    // Lesson selection auto-populates teacher
    $('#lesson_ids').on('change', function() {
        const selectedLessonIds = $(this).val();
        
        if (selectedLessonIds && selectedLessonIds.length > 0) {
            const firstLessonId = selectedLessonIds[0];
            if (lessonTeacherMap[firstLessonId]) {
                // Auto-populate the teacher field
                $('#teacher_id').val(lessonTeacherMap[firstLessonId].teacher_id).trigger('change.select2');
                console.log('Teacher auto-selected:', lessonTeacherMap[firstLessonId].teacher_name);
            }
        }
    });
    });

    // Question Bank Preview Functionality
    $('#previewQuestions').on('click', function() {
        const selectedBanks = $('#question_bank_ids').val();
        const questionsPerBank = $('#questions_per_bank').val();
        
        if (!selectedBanks || selectedBanks.length === 0) {
            alert('Please select at least one question bank');
            return;
        }
        
        if (!questionsPerBank || questionsPerBank < 1) {
            alert('Please specify a valid number of questions per bank');
            return;
        }
        
        // Show loading state
        $('#questionsList').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading questions...</div>');
        $('#questionsPreview').show();
        
        // Fetch questions from selected banks
        $.ajax({
            url: '/admin/question-banks/preview-questions',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                question_bank_ids: selectedBanks,
                questions_per_bank: questionsPerBank
            },
            success: function(response) {
                displayQuestionsPreview(response.questions);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                $('#questionsList').html('<div class="alert alert-danger">Error loading questions. Please try again. (Check console for details)</div>');
            }
        });
    });
    
    function displayQuestionsPreview(questions) {
        let html = '<div class="row">';
        
        questions.forEach(function(question, index) {
            html += `
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <strong>Q${index + 1}:</strong> ${question.question_type} (${question.marks} marks)
                        </div>
                        <div class="card-body">
                            <p>${question.text}</p>
                            ${question.options ? '<small><strong>Options:</strong> ' + question.options + '</small>' : ''}
                            <br><small><strong>Bank:</strong> ${question.question_bank_name}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        html += `<div class="alert alert-info mt-3">Total: ${questions.length} questions will be added to this exam.</div>`;
        
        $('#questionsList').html(html);
    }

    // Form validation
    $('#examForm').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        const requiredFields = ['title', 'exam_type', 'class_date', 'exam_weight', 'total_marks', 'branch_id', 'teacher_id', 'academic_year_id', 'term_id'];
        
        requiredFields.forEach(function(fieldId) {
            const field = $('#' + fieldId);
            if (!field.val()) {
                isValid = false;
                field.addClass('is-invalid');
            } else {
                field.removeClass('is-invalid');
            }
        });
        
        // Check multi-select fields
        if ($('#course_ids').val().length === 0) {
            isValid = false;
            $('#course_ids').next().addClass('is-invalid');
        } else {
            $('#course_ids').next().removeClass('is-invalid');
        }
        
        if ($('#lesson_ids').val().length === 0) {
            isValid = false;
            $('#lesson_ids').next().addClass('is-invalid');
        } else {
            $('#lesson_ids').next().removeClass('is-invalid');
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});
</script>
@endsection
