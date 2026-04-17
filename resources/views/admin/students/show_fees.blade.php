@extends('admin.layout')

@section('title', 'Student School Fees')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Select Academic Year and Term</h1>

    <form action="{{ url()->current() }}" method="GET" class="mb-6">
        <div class="flex space-x-4 items-center">
            <div>
                <label for="academic_year_id" class="block font-semibold mb-1">Academic Year</label>
                <select name="academic_year_id" id="academic_year_id" class="border rounded p-2">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $year->id == $academicYearId ? 'selected' : '' }}>
                            {{ $year->year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="term_id" class="block font-semibold mb-1">Term</label>
                <select name="term_id" id="term_id" class="border rounded p-2">
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ $term->id == $termId ? 'selected' : '' }}>
                            {{ $term->term }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Get Fee</button>
            </div>
        </div>
    </form>

    @if($schoolFee)
        <div class="mt-6 p-4 bg-white rounded shadow">
            <h2 class="text-lg font-semibold mb-2">School Fee Details</h2>
            <p><strong>Academic Year:</strong> {{ $schoolFee->academicYear->year }}</p>
            <p><strong>Term:</strong> {{ $schoolFee->term->term }}</p>
            <p><strong>Amount:</strong> {{ number_format($schoolFee->amount, 2) }} RWF</p>
        </div>
    @else
        <p class="text-red-600 mt-4">No fee found for the selected academic year and term.</p>
    @endif
</div>
@endsection
