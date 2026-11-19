@extends('staff.partials.sidebar_dos')

@section('title', 'Student Cards')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-id-card me-2"></i>
            Student Cards
        </h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-success" onclick="try { generateAllCards(); } catch(e) { console.error('Generate all cards error:', e); alert('Unable to generate all cards. Please try again.'); }">
                <i class="bi bi-download me-1"></i> Generate All Cards
            </button>
            <a href="{{ route('dos.students.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Students
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $students->count() }}</h4>
                            <p class="card-text">Total Cards</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-id-card fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $students->where('payed_registration_fees', 1)->count() }}</h4>
                            <p class="card-text">Active Students</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $students->where('qr_code', '!=', null)->count() }}</h4>
                            <p class="card-text">With QR Codes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-qr-code fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dos.students.StudentCards') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="branch_filter" class="form-label">Branch</label>
                        <select name="branch" id="branch_filter" class="form-select">
                            <option value="">All Branches</option>
                            @if(isset($branches))
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="course_filter" class="form-label">Course</label>
                        <select name="course" id="course_filter" class="form-select">
                            <option value="">All Courses</option>
                            @if(isset($courses))
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label">Status</label>
                        <select name="status" id="status_filter" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active (Paid Fees)</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive (Unpaid Fees)</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('dos.students.StudentCards') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Student Cards Grid -->
    <div class="row">
        @if(isset($students) && $students->count() > 0)
            @foreach($students as $student)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card student-card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="card-title mb-0">Student ID Card</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="avatar-lg bg-primary text-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                    {{ strtoupper(substr($student->full_name, 0, 1)) }}
                                </div>
                            </div>
                            
                            <h5 class="text-center mb-3">{{ $student->full_name }}</h5>
                            
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Student ID:</strong></td>
                                    <td>{{ $student->student_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $student->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $student->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Branch:</strong></td>
                                    <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Academic Year:</strong></td>
                                    <td>{{ $student->academicYear->year ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge {{ $student->payed_registration_fees ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $student->payed_registration_fees ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            
                            @if($student->qr_code)
                                <div class="text-center mt-3">
                                    <img src="{{ $student->qr_code }}" alt="QR Code" class="img-fluid" style="max-width: 100px;">
                                    <p class="small text-muted mt-1">QR Code</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="btn-group w-100" role="group">
                                <button onclick="try { generateCard({{ $student->id }}); } catch(e) { console.error('Card generation error:', e); alert('Unable to generate card. Please try again.'); }" class="btn btn-primary">
                                    <i class="bi bi-download me-1"></i> Generate
                                </button>
                                <button onclick="try { printCard({{ $student->id }}); } catch(e) { console.error('Print error:', e); alert('Unable to print card. Please try again.'); }" class="btn btn-outline-secondary">
                                    <i class="bi bi-printer me-1"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-id-card fs-1 text-muted"></i>
                    <p class="mt-2">No students found</p>
                    <a href="{{ route('dos.students.index') }}" class="btn btn-primary">
                        <i class="bi bi-people me-1"></i> View Students
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.student-card {
    transition: transform 0.2s ease-in-out;
}
.student-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
</style>

<script>
function generateCard(studentId) {
    fetch(`/dos/students/${studentId}/generate-card`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Student card generated successfully!');
            location.reload();
        } else {
            alert('Error generating card: ' + data.message);
        }
    });
}

function printCard(studentId) {
    window.print();
}

function generateAllCards() {
    if(confirm('Generate cards for all students? This may take some time.')) {
        fetch('/dos/students/generate-cards-bulk', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error generating cards: ' + data.message);
            }
        });
    }
}
</script>

<style media="print">
.student-card {
    break-inside: avoid;
    page-break-inside: avoid;
}
.btn-group {
    display: none !important;
}
</style>
@endsection
