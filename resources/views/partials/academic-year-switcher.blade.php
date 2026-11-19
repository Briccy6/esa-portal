@if(isset($adminAcademicYears) && $adminAcademicYears->count())
<form action="{{ route('academic_year.switch') }}" method="POST" class="d-flex align-items-center academic-year-switcher {{ $switcherClass ?? '' }}" style="gap: 0.5rem;">
    @csrf
    <label for="academicYearSelectGlobal" class="mb-0 small d-none d-lg-inline {{ $labelClass ?? 'text-white-50' }}" style="white-space: nowrap;">Academic Year</label>
    <select name="academic_year_id" id="academicYearSelectGlobal" class="form-select form-select-sm {{ $selectClass ?? '' }}" style="min-width: 140px; {{ $selectStyle ?? '' }}" onchange="this.form.submit()">
        @foreach($adminAcademicYears as $year)
            <option value="{{ $year->id }}" @selected(($adminSelectedAcademicYear?->id ?? null) === $year->id)>
                {{ $year->year }} ({{ ucfirst($year->status) }})
            </option>
        @endforeach
    </select>
</form>
@endif
