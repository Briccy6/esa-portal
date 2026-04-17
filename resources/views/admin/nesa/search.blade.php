@extends('admin.layout')

@section('title', 'NESA Registration Search Results')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-search mr-2"></i>
                        Search Results
                        @if($query)
                            <span class="badge badge-light ml-2">"{{ $query }}"</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form method="GET" action="{{ route('admin.nesa.search') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="q" class="form-control" placeholder="Search by name, reg number, or email..." value="{{ $query }}">
                            </div>
                            <div class="col-md-2">
                                <select name="filter" class="form-control">
                                    <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Students</option>
                                    <option value="registered" {{ $filter === 'registered' ? 'selected' : '' }}>Registered</option>
                                    <option value="pending" {{ $filter === 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search mr-2"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Results Summary -->
                    @if($query)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Found {{ $students->total() }} students matching "{{ $query }}" 
                            @if($filter !== 'all')
                                with status "{{ $filter }}"
                            @endif
                        </div>
                    @endif

                    <!-- Students Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Reg Number</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>NESA Status</th>
                                    <th>Registration Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td><strong>{{ $student->reg_number }}</strong></td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->phone ?? 'N/A' }}</td>
                                        <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                        <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                        <td>
                                            @if($student->nesa_registered)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle mr-1"></i>Registered
                                                </span>
                                                <br>
                                                <small class="text-muted">SDMS: {{ $student->nesa_sdms_code }}</small>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $student->nesa_registration_date ? $student->nesa_registration_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.nesa.edit', $student) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($student->nesa_document)
                                                <a href="{{ route('admin.nesa.download', $student) }}" class="btn btn-sm btn-info" title="Download Document">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-search mr-2"></i>
                                                No students found matching your search criteria.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($students->hasPages())
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} entries
                            </div>
                            <div>
                                {{ $students->links() }}
                            </div>
                        </div>
                    @endif

                    <!-- Back Button -->
                    <div class="mt-4">
                        <a href="{{ route('admin.nesa.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Back to NESA Management
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
