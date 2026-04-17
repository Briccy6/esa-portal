@extends('admin.layout')

@section('title', 'Question Banks')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Question Banks</h5>
                    <a href="{{ route('admin.question-banks.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Question Bank
                    </a>
                </div>
                <div class="card-body">
                    @if($message = Session::get('success'))
                        <div class="alert alert-success">
                            {{ $message }}
                        </div>
                    @endif

                    @if($message = Session::get('error'))
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Course</th>
                                    <th>Branch</th>
                                    <th>Academic Year</th>
                                    <th>Teacher</th>
                                    <th>Questions</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($questionBanks as $questionBank)
                                    <tr>
                                        <td>{{ $questionBank->name }}</td>
                                        <td>{{ Str::limit($questionBank->description, 50) }}</td>
                                        <td>{{ $questionBank->course?->course_name ?? 'N/A' }}</td>
                                        <td>{{ $questionBank->course?->branch?->name ?? 'N/A' }}</td>
                                        <td>{{ $questionBank->academicYear?->year ?? 'N/A' }}</td>
                                        <td>{{ $questionBank->teacher?->teacher_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $questionBank->questions->count() }} questions
                                            </span>
                                        </td>
                                        <td>
                                            @if($questionBank->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.question-banks.show', $questionBank) }}" class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.question-banks.edit', $questionBank) }}" class="btn btn-outline-secondary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.question-banks.add-question', $questionBank) }}" class="btn btn-outline-success" title="Add Question">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                                <form action="{{ route('admin.question-banks.destroy', $questionBank) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this question bank?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            No question banks found. <a href="{{ route('admin.question-banks.create') }}">Create one</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($questionBanks->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $questionBanks->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
