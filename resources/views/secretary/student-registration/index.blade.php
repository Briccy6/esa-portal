@extends('secretary.layout')

@section('page_title', 'Registrations')
@section('title', 'Student Registration Management')

@section('content')
<div class="page-shell">
    <div class="page-heading">
        <div>
            <h1>Complete student registration</h1>
            <p>Finish missing ID details, photos, and supporting documents.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('secretary.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to dashboard
            </a>
        </div>
    </div>

    <section class="sec-card">
        <div class="sec-card-header">
            <div>
                <h2>Incomplete registrations</h2>
                <p>{{ $students->total() }} student{{ $students->total() === 1 ? '' : 's' }} still need documents</p>
            </div>
        </div>
        <div class="sec-table-wrap">
            <table class="sec-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Contact</th>
                        <th>Branch / Course</th>
                        <th>ID Number</th>
                        <th>Photos</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php
                            $isComplete = $student->id_number && $student->id_photo && $student->passport_photo;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $student->full_name }}</div>
                                <div class="text-muted small">{{ $student->reg_number }}</div>
                            </td>
                            <td>
                                <div>{{ $student->email }}</div>
                                <div class="text-muted small">{{ $student->phone ?? 'No phone' }}</div>
                            </td>
                            <td>
                                <div>{{ $student->branch->branch_name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $student->course->course_name ?? 'N/A' }}</div>
                            </td>
                            <td>
                                @if($student->id_number)
                                    <span class="badge text-bg-success">{{ $student->id_number }}</span>
                                @else
                                    <span class="badge text-bg-danger">Missing</span>
                                @endif
                            </td>
                            <td>
                                <div class="small">ID: {{ $student->id_photo ? 'Uploaded' : 'Missing' }}</div>
                                <div class="small">Passport: {{ $student->passport_photo ? 'Uploaded' : 'Missing' }}</div>
                            </td>
                            <td>
                                @if($isComplete)
                                    <span class="badge text-bg-success">Complete</span>
                                @else
                                    <span class="badge text-bg-warning">Incomplete</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('secretary.student-registration.show', $student) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    <a href="{{ route('secretary.student-registration.edit', $student) }}" class="btn btn-sm btn-primary">Complete</a>
                                    @if($isComplete)
                                        <form action="{{ route('secretary.student-registration.complete', $student) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark this registration as complete?')">
                                                Mark done
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="sec-empty">
                                <i class="bi bi-check-circle d-block mb-2 fs-3"></i>
                                All students have completed their registration requirements.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="p-3 d-flex justify-content-center">
                {{ $students->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
