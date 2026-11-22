@extends('teacher.layout')

@section('content')
<main class="container py-4" style="max-width:760px">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <a href="{{ route('teacher.assignments.show', $assignment) }}" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Back to assignment</a>
            <h1 class="h3 mt-4">Edit submission deadline</h1>
            <p class="text-muted">{{ $assignment->title }} &middot; {{ $assignment->lesson->title }}</p>
            @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('teacher.assignments.deadline.update', $assignment) }}">
                @csrf
                @method('PUT')
                <label for="due_at" class="form-label fw-semibold">New deadline</label>
                <input id="due_at" name="due_at" type="datetime-local" class="form-control form-control-lg" value="{{ old('due_at', $assignment->due_at->format('Y-m-d\TH:i')) }}" required>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('teacher.assignments.show', $assignment) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button class="btn btn-success">Save deadline</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection
