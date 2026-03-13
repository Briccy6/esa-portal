@extends('secretary.layout')

@section('title', 'Student Registration Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Student Registration Details - {{ $student->full_name }}</h5>
                    <div>
                        <a href="{{ route('secretary.student-registration.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('secretary.student-registration.edit', $student) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Registration
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Personal Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Registration Number:</strong></td>
                                            <td>{{ $student->reg_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Full Name:</strong></td>
                                            <td>{{ $student->full_name }}</td>
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
                                            <td><strong>Sex:</strong></td>
                                            <td>{{ $student->sex ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Academic Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Branch:</strong></td>
                                            <td>{{ $student->branch ? $student->branch->branch_name : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Course:</strong></td>
                                            <td>{{ $student->course ? $student->course->course_name : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Academic Year:</strong></td>
                                            <td>{{ $student->academic_year ? $student->academic_year->year : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Registration Fees:</strong></td>
                                            <td>
                                                @if($student->payed_registration_fees)
                                                    <span class="badge bg-success">Paid</span>
                                                @else
                                                    <span class="badge bg-danger">Not Paid</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Discount:</strong></td>
                                            <td>{{ $student->discount ?? 0 }}%</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Identification Details</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>Identification Type:</strong></td>
                                            <td>
                                                @if($student->identification_type)
                                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $student->identification_type)) }}</span>
                                                @else
                                                    <span class="badge bg-danger">Not Set</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>ID/Passport Number:</strong></td>
                                            <td>
                                                @if($student->id_number)
                                                    <span class="badge bg-success">{{ $student->id_number }}</span>
                                                @else
                                                    <span class="badge bg-danger">Missing</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Registration Status</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Overall Status:</strong>
                                        @if($student->id_number && $student->id_photo && $student->passport_photo)
                                            <span class="badge bg-success">Complete</span>
                                        @else
                                            <span class="badge bg-warning">Incomplete</span>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">ID Number:</small>
                                            <div>{{ $student->id_number ? '✅ Complete' : '❌ Missing' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">ID Photo:</small>
                                            <div>{{ $student->id_photo ? '✅ Uploaded' : '❌ Missing' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Passport Photo:</small>
                                            <div>{{ $student->passport_photo ? '✅ Uploaded' : '❌ Missing' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Supporting Docs:</small>
                                            <div>{{ ($student->senior6_document || $student->senior3_document || $student->other_supporting_document) ? '✅ Available' : '⚠️ None' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Photos</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>ID Photo</h6>
                                            @if($student->id_photo)
                                                <div class="text-center">
                                                    <img src="{{ asset('storage/' . $student->id_photo) }}" 
                                                         alt="ID Photo" class="img-fluid" style="max-height: 300px; border: 1px solid #ddd;">
                                                    <div class="mt-2">
                                                        <a href="{{ asset('storage/' . $student->id_photo) }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-expand"></i> View Full Size
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center text-muted py-4">
                                                    <i class="fas fa-image fa-3x mb-2"></i>
                                                    <p>No ID photo uploaded</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Passport Photo</h6>
                                            @if($student->passport_photo)
                                                <div class="text-center">
                                                    <img src="{{ asset('storage/' . $student->passport_photo) }}" 
                                                         alt="Passport Photo" class="img-fluid" style="max-height: 300px; border: 1px solid #ddd;">
                                                    <div class="mt-2">
                                                        <a href="{{ asset('storage/' . $student->passport_photo) }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-expand"></i> View Full Size
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center text-muted py-4">
                                                    <i class="fas fa-image fa-3x mb-2"></i>
                                                    <p>No passport photo uploaded</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Supporting Documents</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6>Senior 6 Document</h6>
                                            @if($student->senior6_document)
                                                <a href="{{ asset('storage/' . $student->senior6_document) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-file"></i> View Document
                                                </a>
                                            @else
                                                <span class="text-muted">Not uploaded</span>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <h6>Senior 3 Document</h6>
                                            @if($student->senior3_document)
                                                <a href="{{ asset('storage/' . $student->senior3_document) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-file"></i> View Document
                                                </a>
                                            @else
                                                <span class="text-muted">Not uploaded</span>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <h6>Other Supporting Document</h6>
                                            @if($student->other_supporting_document)
                                                <a href="{{ asset('storage/' . $student->other_supporting_document) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-file"></i> View Document
                                                </a>
                                            @else
                                                <span class="text-muted">Not uploaded</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('secretary.student-registration.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <div>
                            <a href="{{ route('secretary.student-registration.edit', $student) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Registration
                            </a>
                            @if($student->id_number && $student->id_photo && $student->passport_photo)
                                <form action="{{ route('secretary.student-registration.complete', $student) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success ms-2" onclick="return confirm('Are you sure this student\'s registration is complete?')">
                                        <i class="fas fa-check"></i> Mark Complete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
