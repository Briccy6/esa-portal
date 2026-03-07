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
    margin-bottom: 20px;
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
            <form action="{{ route('exams.update', $exam->id) }}" method="POST">
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
                        <input type="date" name="date" class="lc-input" value="{{ old('date', $exam->class_date->format('Y-m-d')) }}" required>
                        @error('date')
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
                                    {{ $course->name }}
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

                <!-- Form Actions -->
                <div class="lc-btn-group">
                    <button type="submit" class="lc-btn lc-btn-primary">
                        💾 Update Exam
                    </button>
                    <a href="{{ route('exams.index') }}" class="lc-btn lc-btn-secondary">
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
