@extends('staff.partials.sidebar_sec')

@section('content')
    <h2 class="mb-4">Course Fees</h2>
    <div class="row g-4">
        @php
            $systemLogo = \Illuminate\Support\Facades\Cache::get('esa_system_logo');
            $logoPath = $systemLogo ? asset('storage/' . $systemLogo) : asset('images/logo.png');
        @endphp
        @forelse($courses as $course)
            @php
                $totalFee = ($course->tuition_fee ?? 0) + ($course->registration_fee ?? 0) + ($course->trip_fee ?? 0) - ($course->discount_fee ?? 0);
                $studentCount = $course->students->count();
                $totalPaid = $course->transcripts->sum('amount_paid');
                $totalDue = ($studentCount * $totalFee) - $totalPaid;
            @endphp
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <img src="{{ $logoPath }}" class="card-img-top p-4" alt="Course Image" style="height: 180px; object-fit: contain;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $course->name }}</h5>
                        <ul class="list-group list-group-flush mb-3">
                             <li class="list-group-item d-flex justify-content-between align-items-center">
                                Class:
                                <span class="fw-bold">{{ ($course->course_name) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tuition Fee
                                <span class="fw-bold">{{ number_format($course->tuition_fee, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Registration Fee
                                <span class="fw-bold">{{ number_format($course->registration_fee, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Trip Fee
                                <span class="fw-bold">{{ number_format($course->trip_fee, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Discount Fee
                                <span class="fw-bold">{{ number_format($course->discount_fee, 2) }}</span>
                            </li>
                        </ul>
                        <!-- Details Button -->
                        <button type="button" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#courseDetailsModal{{ $course->id }}">
                            Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="courseDetailsModal{{ $course->id }}" tabindex="-1" aria-labelledby="courseDetailsModalLabel{{ $course->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="courseDetailsModalLabel{{ $course->id }}">Course Details: {{ $course->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Branch Locations:</strong>
                                @if($course->branches && $course->branches->count())
                                    {{ $course->branches->pluck('name')->join(', ') }}
                                @else
                                    <span class="text-muted">No branches assigned</span>
                                @endif
                            </p>
                            <p><strong>Teachers Assigned:</strong>
                                @if($course->teachers && $course->teachers->count())
                                    {{ $course->teachers->pluck('name')->unique()->join(', ') }}
                                @else
                                    <span class="text-muted">No teachers assigned</span>
                                @endif
                            </p>
                            <p><strong>Total Students:</strong>
                                {{ $studentCount }}
                            </p>
                            <p><strong>Total Paid Amount:</strong>
                                {{ number_format($totalPaid, 2) }}
                            </p>
                            <p><strong>Rest to be Paid:</strong>
                                {{ number_format($totalDue, 2) }}
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">No courses found.</div>
            </div>
        @endforelse
    </div>
@endsection