@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-dark fw-bold mb-1">
                        <i class="fas fa-id-card me-2 text-success"></i>
                        TVET Report Cards
                    </h2>
                    <p class="text-muted mb-0">Generate TVET assessment report cards for students</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dos.marks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Marks
                    </a>
                </div>
            </div>

            <!-- Report Card Generation Form -->
            <div class="card shadow-sm border-2 border-success">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>Generate TVET Report Card
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dos.marks.report-card') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label fw-semibold">
                                        <i class="fas fa-user me-1"></i> Select Student
                                    </label>
                                    <select class="form-select" id="student_id" name="student_id" required>
                                        <option value="">Choose a student...</option>
                                        @if(isset($students))
                                            @foreach($students as $student)
                                                <option value="{{ $student->id }}">{{ $student->full_name }} - {{ $student->reg_number ?? 'No Reg No' }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="term" class="form-label fw-semibold">
                                        <i class="fas fa-calendar-alt me-1"></i> Term
                                    </label>
                                    <input type="text" class="form-control" id="term" name="term" placeholder="e.g., Term 1" value="{{ request('term') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="academic_year" class="form-label fw-semibold">
                                        <i class="fas fa-graduation-cap me-1"></i> Academic Year
                                    </label>
                                    <input type="text" class="form-control" id="academic_year" name="academic_year" placeholder="e.g., 2025-2026" value="{{ request('academic_year') ?? date('Y') . '-' . (date('Y') + 1) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-file-pdf me-1"></i> Generate Report Card
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo me-1"></i> Reset Form
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Instructions -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Instructions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-info mb-3">How to Generate TVET Report Cards:</h6>
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item">Select a student from the dropdown list</li>
                                <li class="list-group-item">Enter the term (optional)</li>
                                <li class="list-group-item">Enter the academic year (optional)</li>
                                <li class="list-group-item">Click "Generate Report Card" to create the TVET assessment report</li>
                                <li class="list-group-item">Use the print function to save or print the report card</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-warning mb-3">TVET Report Card Features:</h6>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Professional TVET Certificate III format
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    RQF Level III compliance
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Competency-based assessment
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Formative and summative evaluations
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Professional deliberation section
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #1e7e34);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8, #117a8b);
    }
    
    .border-2 {
        border-width: 2px !important;
    }
    
    .list-group-item {
        border: none;
        padding: 0.5rem 0;
    }
    
    .list-group-numbered {
        counter-reset: section;
    }
    
    .list-group-numbered > li {
        counter-increment: section;
        display: flex;
        align-items: center;
    }
    
    .list-group-numbered > li::before {
        content: counter(section);
        background: #28a745;
        color: white;
        font-weight: bold;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 12px;
    }
</style>
@endsection
