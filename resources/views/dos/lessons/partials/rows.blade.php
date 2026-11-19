@forelse($lessonDetails as $lesson)
    @php
        $branchNames = $lesson->branchCourses->pluck('branch.name')->filter()->unique()->values();

        if ($branchNames->isEmpty()) {
            $branchNames = $lesson->branches->pluck('name')->filter()->unique()->values();
        }
    @endphp
    <article class="lesson-row">
        <div class="lesson-row-main">
            <div class="lesson-title-block">
                <span class="lesson-field-label">Lesson</span>
                <h3>{{ $lesson->title }}</h3>
            </div>
            <div class="lesson-branches-block">
                <span class="lesson-field-label">Branches</span>
                <div class="lesson-branch-list">
                    @forelse($branchNames as $branchName)
                        <span class="lesson-branch-badge">{{ $branchName }}</span>
                    @empty
                        <span class="text-body-secondary small">No branch assigned</span>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="lesson-row-actions">
            <button
                type="button"
                class="btn btn-primary lesson-details-button"
                data-lesson-id="{{ $lesson->id }}"
                data-lesson-title="{{ $lesson->title }}"
            >
                <i class="bi bi-card-list me-1" aria-hidden="true"></i>Details
            </button>
        </div>
    </article>
@empty
    <div class="lesson-empty">
        <i class="bi bi-journal-x" aria-hidden="true"></i>
        <h3 class="h6 text-dark">No lessons found</h3>
        <p class="mb-0">Try another search or create a lesson to get started.</p>
    </div>
@endforelse
