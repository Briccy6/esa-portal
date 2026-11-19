@extends('dos.layout')

@section('content')
<style>
    .unit-planner {
        min-height: calc(100vh - 80px);
        padding: 32px clamp(16px, 3vw, 40px);
        background: #f4f7fb;
        color: #172b4d;
    }

    .unit-planner-shell {
        max-width: 1100px;
        margin: 0 auto;
    }

    .unit-planner-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .unit-planner-eyebrow {
        margin: 0 0 6px;
        color: #2457a7;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .unit-planner-header h1 {
        margin: 0;
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 750;
        letter-spacing: -.03em;
    }

    .unit-planner-header p {
        margin: 8px 0 0;
        color: #64748b;
        font-size: 15px;
    }

    .unit-planner .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        border-radius: 8px;
        font-weight: 600;
    }

    .unit-overview,
    .unit-form-card {
        border: 1px solid #e1e8f0;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .04);
    }

    .unit-overview {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 24px;
        align-items: center;
        padding: 22px 24px;
        margin-bottom: 22px;
    }

    .unit-overview-label {
        margin: 0 0 5px;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .unit-overview h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
    }

    .unit-overview-course {
        margin: 5px 0 0;
        color: #64748b;
    }

    .unit-overview-stats {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;
    }

    .unit-overview-stat {
        min-width: 118px;
        padding: 11px 14px;
        border: 1px solid #e5ebf3;
        border-radius: 9px;
        background: #f8fafc;
    }

    .unit-overview-stat span,
    .unit-overview-stat strong {
        display: block;
    }

    .unit-overview-stat span {
        color: #64748b;
        font-size: 12px;
    }

    .unit-overview-stat strong {
        margin-top: 3px;
        font-size: 15px;
    }

    .unit-plan-note {
        display: flex;
        align-items: flex-start;
        gap: 11px;
        padding: 14px 16px;
        margin-bottom: 22px;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        background: #eff6ff;
        color: #1e3a8a;
        font-size: 14px;
    }

    .unit-plan-note i {
        margin-top: 2px;
        color: #2563eb;
    }

    .unit-form-card {
        overflow: hidden;
    }

    .unit-form-heading {
        padding: 22px 24px;
        border-bottom: 1px solid #e8edf3;
    }

    .unit-form-heading h2 {
        margin: 0;
        font-size: 19px;
        font-weight: 700;
    }

    .unit-form-heading p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
    }

    .unit-form-body {
        padding: 24px;
    }

    .unit-level-field {
        max-width: 420px;
        margin-bottom: 24px;
    }

    .unit-planner .form-label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 13px;
        font-weight: 650;
    }

    .unit-planner .form-control,
    .unit-planner .form-select {
        min-height: 44px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        color: #172b4d;
        box-shadow: none;
    }

    .unit-planner .form-control:focus,
    .unit-planner .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .14);
    }

    .unit-planner .form-hint {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: 12px;
    }

    .unit-list-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 12px;
    }

    .unit-list-heading h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
    }

    .unit-list-heading p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .unit-entry {
        padding: 18px;
        margin-bottom: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
        background: #fbfcfe;
    }

    .unit-entry-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 15px;
    }

    .unit-entry-title {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 14px;
        font-weight: 700;
    }

    .unit-entry-number {
        display: inline-flex;
        width: 27px;
        height: 27px;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #e8f0ff;
        color: #2457a7;
        font-size: 12px;
    }

    .unit-remove {
        display: inline-flex;
        width: 34px;
        height: 34px;
        align-items: center;
        justify-content: center;
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fff;
        color: #b91c1c;
        cursor: pointer;
    }

    .unit-remove:hover:not(:disabled) {
        background: #fef2f2;
    }

    .unit-remove:disabled {
        opacity: .4;
        cursor: not-allowed;
    }

    .unit-hours-wrap {
        position: relative;
    }

    .unit-hours-wrap .form-control {
        padding-right: 58px;
    }

    .unit-hours-suffix {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 13px;
        pointer-events: none;
    }

    .unit-add-button {
        margin-top: 3px;
    }

    .unit-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 16px;
        margin-top: 20px;
        border-radius: 9px;
        background: #f1f5f9;
        color: #475569;
    }

    .unit-total strong {
        color: #172b4d;
        font-size: 18px;
    }

    .unit-form-actions {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 24px;
        border-top: 1px solid #e8edf3;
        background: #fbfcfe;
    }

    .unit-planner .btn-primary {
        border-color: #2457a7;
        background: #2457a7;
    }

    .unit-planner .btn-primary:hover:not(:disabled) {
        border-color: #1d4ed8;
        background: #1d4ed8;
    }

    .unit-planner .btn-primary:disabled {
        opacity: .55;
        cursor: not-allowed;
    }

    @media (max-width: 700px) {
        .unit-planner {
            padding: 22px 14px;
        }

        .unit-planner-header,
        .unit-overview {
            display: block;
        }

        .unit-planner-header .btn {
            margin-top: 16px;
        }

        .unit-overview-stats {
            justify-content: flex-start;
            margin-top: 18px;
        }

        .unit-overview-stat {
            flex: 1 1 120px;
        }

        .unit-form-body,
        .unit-form-heading {
            padding: 18px;
        }

        .unit-list-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .unit-list-heading .btn {
            width: 100%;
        }

        .unit-form-actions {
            flex-direction: column-reverse;
            padding: 16px 18px;
        }

        .unit-form-actions .btn {
            width: 100%;
        }
    }
</style>

@php
    $formUnits = old('units', [[
        'name' => '',
        'term_id' => $terms->first()?->id,
        'hours' => '',
    ]]);
    $nextUnitIndex = count($formUnits) > 0 ? max(array_keys($formUnits)) + 1 : 0;
@endphp

<main class="unit-planner">
    <div class="unit-planner-shell">
        <header class="unit-planner-header">
            <div>
                <p class="unit-planner-eyebrow">Lesson setup / Unit plan</p>
                <h1>Plan lesson units</h1>
                <p>Add the units students will study, assign each one to a term, and estimate its teaching hours.</p>
            </div>
            <a href="{{ route('staff.lessons.show', $lesson->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>Back to lesson
            </a>
        </header>

        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Please review the following:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="unit-overview" aria-label="Lesson summary">
            <div>
                <p class="unit-overview-label">Lesson</p>
                <h2>{{ $lesson->title }}</h2>
                <p class="unit-overview-course">{{ $lesson->course->course_name ?? 'Course not specified' }}</p>
            </div>
            <div class="unit-overview-stats">
                <div class="unit-overview-stat">
                    <span>Academic year</span>
                    <strong>{{ $lesson->academicYear->year ?? 'Not set' }}</strong>
                </div>
                <div class="unit-overview-stat">
                    <span>Test marks</span>
                    <strong>{{ $lesson->max_test_marks ?? 0 }}</strong>
                </div>
                <div class="unit-overview-stat">
                    <span>Exam marks</span>
                    <strong>{{ $lesson->max_exam_marks ?? 0 }}</strong>
                </div>
            </div>
        </section>

        @if($lesson->branchCourses->isNotEmpty())
            <div class="unit-plan-note" role="note">
                <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                <div>
                    <strong>Shared lesson plan</strong>
                    <div>Units added here will be available across all {{ $lesson->branchCourses->count() }} selected branch-course combinations.</div>
                </div>
            </div>
        @endif

        @if($terms->isEmpty())
            <div class="alert alert-warning" role="alert">
                No terms are available for this lesson’s academic year. Add or assign a term before planning units.
            </div>
        @endif

        <form action="{{ route('staff.lesson.details.store', $lesson->id) }}" method="POST" class="unit-form-card">
            @csrf
            <div class="unit-form-heading">
                <h2>Unit details</h2>
                <p>Choose a level and enter the term, name, and planned hours for each unit.</p>
            </div>

            <div class="unit-form-body">
                <div class="unit-level-field">
                    <label for="course_level" class="form-label">Course level <span class="text-danger">*</span></label>
                    <select class="form-select" id="course_level" name="course_level" required>
                        <option value="">Select a level</option>
                        <option value="beginner" @selected(old('course_level') === 'beginner')>Beginner</option>
                        <option value="intermediate" @selected(old('course_level') === 'intermediate')>Intermediate</option>
                        <option value="advanced" @selected(old('course_level') === 'advanced')>Advanced</option>
                    </select>
                    <span class="form-hint">This level applies to all units in this lesson plan.</span>
                    @error('course_level')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="unit-list-heading">
                    <div>
                        <h3>Units</h3>
                        <p>Each unit can be assigned to a different study term.</p>
                    </div>
                    <button type="button" class="btn btn-outline-primary unit-add-button" id="addUnitButton">
                        <i class="bi bi-plus-lg" aria-hidden="true"></i>Add another unit
                    </button>
                </div>

                <div id="units-container">
                    @foreach($formUnits as $index => $unit)
                        <article class="unit-entry" data-unit-entry>
                            <div class="unit-entry-top">
                                <div class="unit-entry-title">
                                    <span class="unit-entry-number" data-unit-number>{{ $loop->iteration }}</span>
                                    <span>Unit details</span>
                                </div>
                                <button type="button" class="unit-remove" data-remove-unit aria-label="Remove unit {{ $loop->iteration }}" @disabled(count($formUnits) === 1)>
                                    <i class="bi bi-trash3" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-lg-5">
                                    <label class="form-label" for="unit-term-{{ $index }}">Study term <span class="text-danger">*</span></label>
                                    <select class="form-select" id="unit-term-{{ $index }}" name="units[{{ $index }}][term_id]" required>
                                        <option value="">Select a term</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->id }}" @selected((string) ($unit['term_id'] ?? '') === (string) $term->id)>
                                                {{ $term->name }} · {{ $term->academicYear->year ?? 'Academic year not set' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("units.$index.term_id")
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-5">
                                    <label class="form-label" for="unit-name-{{ $index }}">Unit name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="unit-name-{{ $index }}"
                                           name="units[{{ $index }}][name]" value="{{ $unit['name'] ?? '' }}"
                                           placeholder="For example: Introduction to the subject" maxlength="255" required>
                                    @error("units.$index.name")
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-2">
                                    <label class="form-label" for="unit-hours-{{ $index }}">Planned time <span class="text-danger">*</span></label>
                                    <div class="unit-hours-wrap">
                                        <input type="number" class="form-control unit-hours-input" id="unit-hours-{{ $index }}"
                                               name="units[{{ $index }}][hours]" value="{{ $unit['hours'] ?? '' }}"
                                               min="1" step="1" placeholder="0" required>
                                        <span class="unit-hours-suffix">hours</span>
                                    </div>
                                    @error("units.$index.hours")
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="unit-total" role="status" aria-live="polite">
                    <span>Total planned teaching time</span>
                    <strong id="totalHours">0 hours</strong>
                </div>
            </div>

            <div class="unit-form-actions">
                <a href="{{ route('staff.lessons.show', $lesson->id) }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary" @disabled($terms->isEmpty())>
                    <i class="bi bi-check2-circle" aria-hidden="true"></i>Save unit plan
                </button>
            </div>
        </form>

        <template id="unit-template">
            <article class="unit-entry" data-unit-entry>
                <div class="unit-entry-top">
                    <div class="unit-entry-title">
                        <span class="unit-entry-number" data-unit-number></span>
                        <span>Unit details</span>
                    </div>
                    <button type="button" class="unit-remove" data-remove-unit aria-label="Remove unit">
                        <i class="bi bi-trash3" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-lg-5">
                        <label class="form-label" for="unit-term-__INDEX__">Study term <span class="text-danger">*</span></label>
                        <select class="form-select" id="unit-term-__INDEX__" name="units[__INDEX__][term_id]" required>
                            <option value="">Select a term</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}">{{ $term->name }} · {{ $term->academicYear->year ?? 'Academic year not set' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-lg-5">
                        <label class="form-label" for="unit-name-__INDEX__">Unit name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="unit-name-__INDEX__"
                               name="units[__INDEX__][name]" placeholder="For example: Introduction to the subject" maxlength="255" required>
                    </div>
                    <div class="col-12 col-lg-2">
                        <label class="form-label" for="unit-hours-__INDEX__">Planned time <span class="text-danger">*</span></label>
                        <div class="unit-hours-wrap">
                            <input type="number" class="form-control unit-hours-input" id="unit-hours-__INDEX__"
                                   name="units[__INDEX__][hours]" min="1" step="1" placeholder="0" required>
                            <span class="unit-hours-suffix">hours</span>
                        </div>
                    </div>
                </div>
            </article>
        </template>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('units-container');
    const template = document.getElementById('unit-template');
    const totalHours = document.getElementById('totalHours');
    let nextUnitIndex = {{ $nextUnitIndex }};

    function updateUnitList() {
        const entries = Array.from(container.querySelectorAll('[data-unit-entry]'));
        entries.forEach((entry, index) => {
            entry.querySelector('[data-unit-number]').textContent = index + 1;
            const removeButton = entry.querySelector('[data-remove-unit]');
            removeButton.disabled = entries.length === 1;
            removeButton.setAttribute('aria-label', `Remove unit ${index + 1}`);
        });

        const hours = entries.reduce((sum, entry) => {
            const value = Number(entry.querySelector('.unit-hours-input').value);
            return sum + (Number.isFinite(value) && value > 0 ? value : 0);
        }, 0);
        totalHours.textContent = `${hours} ${hours === 1 ? 'hour' : 'hours'}`;
    }

    document.getElementById('addUnitButton').addEventListener('click', function () {
        const html = template.innerHTML.replaceAll('__INDEX__', nextUnitIndex++);
        container.insertAdjacentHTML('beforeend', html);
        updateUnitList();
        container.querySelector('[data-unit-entry]:last-child input[name$="[name]"]').focus();
    });

    container.addEventListener('click', function (event) {
        const button = event.target.closest('[data-remove-unit]');
        if (button && container.querySelectorAll('[data-unit-entry]').length > 1) {
            button.closest('[data-unit-entry]').remove();
            updateUnitList();
        }
    });

    container.addEventListener('input', function (event) {
        if (event.target.matches('.unit-hours-input')) {
            updateUnitList();
        }
    });

    updateUnitList();
});
</script>
@endsection
