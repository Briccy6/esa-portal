@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h1 class="h4 mb-0">Edit unit</h1>
                    <p class="text-muted small mb-0">
                        Lesson: {{ $detail->lesson->title ?? '—' }}
                        @if($detail->lesson && $detail->lesson->course)
                            · {{ $detail->lesson->course->course_name }}
                        @endif
                    </p>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.lesson-units.update', $detail->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="unit_name" class="form-label">Unit name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('unit_name') is-invalid @enderror" id="unit_name" name="unit_name" value="{{ old('unit_name', $detail->unit_name) }}" required maxlength="255">
                            @error('unit_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="hours" class="form-label">Hours <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('hours') is-invalid @enderror" id="hours" name="hours" value="{{ old('hours', $detail->hours) }}" required min="0" step="1">
                            @error('hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="term_id" class="form-label">Term <span class="text-danger">*</span></label>
                            <select class="form-select @error('term_id') is-invalid @enderror" id="term_id" name="term_id" required>
                                <option value="">Select term</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" @selected(old('term_id', $detail->term_id) == $term->id)>
                                        {{ $term->name }}
                                        @if($term->academicYear)
                                            ({{ $term->academicYear->year ?? $term->academicYear->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('term_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('staff.lesson-units.index') }}" class="btn btn-outline-secondary">Back to units</a>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
