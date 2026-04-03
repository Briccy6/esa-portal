@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1><i class="fas fa-file-alt"></i> Babyeyi Documents</h1>
            <p class="text-muted">Term registration documents and requirements for all courses</p>
        </div>
    </div>

    @if ($babyeyis->count() > 0)
        <div class="row">
            @foreach ($babyeyis as $babyeyi)
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100 transition-transform" style="transform: translateZ(0); transition: transform 0.3s ease;">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-scroll"></i>
                                {{ $babyeyi->academicYear->year ?? 'N/A' }} - {{ $babyeyi->term->name ?? 'N/A' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-secondary">Course</h6>
                                <p class="mb-0">
                                    @if ($babyeyi->course)
                                        <strong>{{ $babyeyi->course->name }}</strong>
                                    @elseif ($babyeyi->course_type)
                                        <em>Multiple courses - Type: {{ $babyeyi->course_type }}</em>
                                    @else
                                        <em>Multiple courses</em>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-secondary">Reference</h6>
                                <p class="mb-0"><code>{{ $babyeyi->reference_number }}</code></p>
                            </div>

                            @if ($babyeyi->term)
                                <div class="mb-3">
                                    <h6 class="text-secondary">Term Duration</h6>
                                    <p class="mb-0">
                                        <small>
                                            {{ $babyeyi->term->start_date->format('M d, Y') }} - 
                                            {{ $babyeyi->term->end_date->format('M d, Y') }}
                                        </small>
                                    </p>
                                </div>
                            @endif

                            @if ($babyeyi->trip_known && $babyeyi->trip_place)
                                <div class="mb-3">
                                    <h6 class="text-secondary"><i class="fas fa-bus"></i> Trip</h6>
                                    <p class="mb-1">Destination: <strong>{{ $babyeyi->trip_place }}</strong></p>
                                    @if ($babyeyi->trip_fees)
                                        <p class="mb-0">Fees: <strong>RWF {{ number_format($babyeyi->trip_fees, 2) }}</strong></p>
                                    @endif
                                </div>
                            @endif

                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> 
                                    Created: {{ $babyeyi->created_at->format('M d, Y') }}
                                </small>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <a href="{{ route('public.babyeyi.show', $babyeyi) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View Document
                            </a>
                            <a href="{{ route('public.babyeyi.download', $babyeyi) }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <nav aria-label="Page navigation">
            {{ $babyeyis->links() }}
        </nav>
    @else
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle"></i> 
            <strong>No documents available.</strong> Babyeyi documents will appear here once they are published.
        </div>
    @endif
</div>

<style>
.card {
    border: none;
    border-radius: 8px;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
}

.card-header {
    border-bottom: 3px solid #0056b3;
}

.text-secondary {
    font-weight: 600;
    color: #495057 !important;
    font-size: 0.85rem;
    text-transform: uppercase;
}

code {
    background-color: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}
</style>
@endsection
