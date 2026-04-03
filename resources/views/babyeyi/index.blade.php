@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Babyeyi Documents</h1>
        </div>
        <div class="col-md-4 text-right">
            @can('create', App\Models\Babyeyi::class)
                <a href="{{ route('babyeyi.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Babyeyi
                </a>
            @endcan
        </div>
    </div>

    @if ($babyeyis->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Reference</th>
                        <th>Academic Year</th>
                        <th>Term</th>
                        <th>Course</th>
                        <th>Created By</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($babyeyis as $babyeyi)
                        <tr>
                            <td><strong>{{ $babyeyi->reference_number }}</strong></td>
                            <td>{{ $babyeyi->academicYear->year ?? 'N/A' }}</td>
                            <td>{{ $babyeyi->term->name ?? 'N/A' }}</td>
                            <td>{{ $babyeyi->course->name ?? ($babyeyi->course_type ? 'Type: ' . $babyeyi->course_type : 'Multiple') }}</td>
                            <td>{{ $babyeyi->creator->name ?? 'N/A' }}</td>
                            <td>
                                @if ($babyeyi->status === 'draft')
                                    <span class="badge badge-warning">Draft</span>
                                @elseif ($babyeyi->status === 'preview')
                                    <span class="badge badge-info">Preview</span>
                                @else
                                    <span class="badge badge-success">Published</span>
                                @endif
                            </td>
                            <td>{{ $babyeyi->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('babyeyi.show', $babyeyi) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if ($babyeyi->status === 'draft')
                                        <a href="{{ route('babyeyi.edit', $babyeyi) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('babyeyi.destroy', $babyeyi) }}" method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this draft?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Draft">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if ($babyeyi->status === 'published')
                                        <a href="{{ route('babyeyi.download', $babyeyi) }}" class="btn btn-sm btn-secondary" title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $babyeyis->links() }}
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No babyeyi documents found. Create one to get started!
        </div>
    @endif
</div>
@endsection
