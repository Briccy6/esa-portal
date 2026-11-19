@if($lessonDetails->hasPages())
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <p class="text-body-secondary small mb-0">
            Showing {{ $lessonDetails->firstItem() }}-{{ $lessonDetails->lastItem() }}
            of {{ $lessonDetails->total() }} lessons
        </p>
        {{ $lessonDetails->links('pagination::bootstrap-5') }}
    </div>
@endif
