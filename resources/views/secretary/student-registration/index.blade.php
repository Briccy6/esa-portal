@extends('secretary.layout')

@section('title', 'Student Registration Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Complete Student Registration</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('secretary.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
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

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Reg Number</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>ID Number</th>
                                    <th>ID Photo</th>
                                    <th>Passport Photo</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>{{ $student->reg_number }}</td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->phone ?? 'N/A' }}</td>
                                        <td>{{ $student->branch ? $student->branch->branch_name : 'N/A' }}</td>
                                        <td>{{ $student->course ? $student->course->course_name : 'N/A' }}</td>
                                        <td>
                                            @if($student->id_number)
                                                <span class="badge bg-success">{{ $student->id_number }}</span>
                                            @else
                                                <span class="badge bg-danger">Missing</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($student->id_photo)
                                                <a href="{{ asset('storage/' . $student->id_photo) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-image"></i> View
                                                </a>
                                            @else
                                                <span class="badge bg-danger">Missing</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($student->passport_photo)
                                                <a href="{{ asset('storage/' . $student->passport_photo) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-image"></i> View
                                                </a>
                                            @else
                                                <span class="badge bg-danger">Missing</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($student->id_number && $student->id_photo && $student->passport_photo)
                                                <span class="badge bg-success">Complete</span>
                                            @else
                                                <span class="badge bg-warning">Incomplete</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('secretary.student-registration.show', $student) }}" class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('secretary.student-registration.edit', $student) }}" class="btn btn-sm btn-warning" title="Complete Registration">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('secretary.student-registration.complete', $student) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Mark Complete" onclick="try { return confirm('Are you sure this student\'s registration is complete?'); } catch(e) { console.error('Confirmation error:', e); return false; }">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-user-check fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No Incomplete Registrations</h5>
                                                <p class="text-muted">All students have completed their registration requirements.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($students->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $students->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
