@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Generated Report Cards</h3>
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="printed" {{ request('status') === 'printed' ? 'selected' : '' }}>Printed</option>
            </select>
            <button class="btn btn-primary">Filter</button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Academic Year</th>
                        <th>Term</th>
                        <th>Status</th>
                        <th>Generated</th>
                        <th style="width: 320px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportCards as $reportCard)
                        <tr>
                            <td>{{ $reportCard->student->full_name ?? 'N/A' }}</td>
                            <td>{{ $reportCard->course->course_name ?? 'N/A' }}</td>
                            <td>{{ $reportCard->academicYear->year ?? 'N/A' }}</td>
                            <td>{{ $reportCard->term->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $reportCard->status === 'approved' ? 'success' : ($reportCard->status === 'printed' ? 'info' : 'warning') }}">
                                    {{ ucfirst($reportCard->status ?? 'pending') }}
                                </span>
                            </td>
                            <td>{{ $reportCard->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="d-flex gap-2 flex-wrap">
                                @if($reportCard->status !== 'approved')
                                    <form method="POST" action="{{ route('dos.report-cards.approve', $reportCard) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                @endif
                                <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('dos.report-cards.print', $reportCard) }}">
                                    Print
                                </a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('dos.report-cards.download', $reportCard) }}">
                                    Download
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No generated report cards found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $reportCards->links() }}
    </div>
</div>
@endsection

