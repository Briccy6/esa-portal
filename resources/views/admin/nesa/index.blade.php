@extends('admin.layout')

@section('title', 'NESA Registration Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap mr-2"></i>
                        NESA Registration Management
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ App\Models\Student::count() }}</h4>
                                            <p class="mb-0">Total Students</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
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
                                            <h4 class="mb-0">{{ App\Models\Student::nesaRegistered()->count() }}</h4>
                                            <p class="mb-0">NESA Registered</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ App\Models\Student::notNesaRegistered()->count() }}</h4>
                                            <p class="mb-0">Pending Registration</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <a href="{{ route('admin.nesa.registered') }}" class="btn btn-success">
                                <i class="fas fa-check-circle mr-2"></i>View Registered Students
                            </a>
                            <a href="{{ route('admin.nesa.pending') }}" class="btn btn-warning ml-2">
                                <i class="fas fa-clock mr-2"></i>View Pending Students
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.nesa.export') }}" class="btn btn-info">
                                <i class="fas fa-download mr-2"></i>Export Registered
                            </a>
                        </div>
                    </div>

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('admin.nesa.search') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="q" class="form-control" placeholder="Search by name, reg number, or email..." value="{{ request('q') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="filter" class="form-control">
                                    <option value="all">All Students</option>
                                    <option value="registered">Registered</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search mr-2"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Students Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Reg Number</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>NESA Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>{{ $student->reg_number }}</td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                        <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($student->nesa_registered)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle mr-1"></i>Registered
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.nesa.edit', $student) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($student->nesa_document)
                                                <a href="{{ route('admin.nesa.download', $student) }}" class="btn btn-sm btn-info" title="Download NESA Document">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No students found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
