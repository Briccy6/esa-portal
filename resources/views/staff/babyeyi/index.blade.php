@extends('staff.partials.sidebar_sec')

@section('content')
<div class="page-content">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-md-8">
            <h1 class="mb-2">
                <i class="fas fa-file-contract text-primary"></i> Term Registration Documents
            </h1>
            <p class="text-muted mb-0">Manage babyeyi documents for term registration requirements</p>
        </div>
        <div class="col-md-4 text-end">
            @can('create', App\Models\Babyeyi::class)
                <a href="{{ route('babyeyi.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i> Create New Babyeyi
                </a>
            @endcan
        </div>
    </div>

    <!-- Active Babyeyi Summary -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-star text-warning"></i> Active Babyeyi
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $activeBabyeyis = \App\Models\Babyeyi::where('status', 'published')
                            ->orWhere('status', 'preview')
                            ->orderBy('updated_at', 'desc')
                            ->limit(2)
                            ->get();
                    @endphp
                    
                    @if ($activeBabyeyis->count() > 0)
                        <div class="row">
                            @foreach ($activeBabyeyis as $babyeyi)
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 border rounded-lg" style="background-color: #f8f9fa;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <strong>{{ $babyeyi->academicYear->name ?? 'N/A' }} - {{ $babyeyi->term->name ?? 'N/A' }}</strong>
                                                </h6>
                                                <p class="mb-1 text-sm text-muted">
                                                    <i class="fas fa-book"></i>
                                                    @if ($babyeyi->course)
                                                        {{ $babyeyi->course->name }}
                                                    @elseif ($babyeyi->course_type)
                                                        Type: {{ $babyeyi->course_type }}
                                                    @else
                                                        Multiple Courses
                                                    @endif
                                                </p>
                                                <p class="mb-0 text-sm text-muted">
                                                    <i class="fas fa-code"></i> Ref: <code>{{ $babyeyi->reference_number }}</code>
                                                </p>
                                            </div>
                                            <div>
                                                @if ($babyeyi->status === 'published')
                                                    <span class="badge bg-success">Published</span>
                                                @else
                                                    <span class="badge bg-info">Preview</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-3 d-flex gap-2">
                                            <a href="{{ route('babyeyi.show', $babyeyi) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('babyeyi.edit', $babyeyi) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="{{ route('babyeyi.download', $babyeyi) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> No active babyeyi documents yet. 
                            <a href="{{ route('babyeyi.create') }}" class="alert-link">Create one now</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- All Babyeyi Documents -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> All Babyeyi Documents
                    </h5>
                </div>
                <div class="card-body">
                    @if ($babyeyis->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reference</th>
                                        <th>Academic Year - Term</th>
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
                                            <td>
                                                <code class="bg-light px-2 py-1 rounded">{{ $babyeyi->reference_number }}</code>
                                            </td>
                                            <td>
                                                <strong>{{ $babyeyi->academicYear->name ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $babyeyi->term->name ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                @if ($babyeyi->course)
                                                    <span class="badge bg-primary">{{ $babyeyi->course->name }}</span>
                                                @elseif ($babyeyi->course_type)
                                                    <span class="badge bg-secondary">Type: {{ $babyeyi->course_type }}</span>
                                                @else
                                                    <span class="badge bg-info">Multiple</span>
                                                @endif
                                            </td>
                                            <td>{{ $babyeyi->creator->name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($babyeyi->status === 'draft')
                                                    <span class="badge bg-warning">Draft</span>
                                                @elseif ($babyeyi->status === 'preview')
                                                    <span class="badge bg-info">Preview</span>
                                                @else
                                                    <span class="badge bg-success">Published</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $babyeyi->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('babyeyi.show', $babyeyi) }}" class="btn btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('babyeyi.edit', $babyeyi) }}" class="btn btn-outline-secondary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('babyeyi.preview', $babyeyi) }}" class="btn btn-outline-info" title="Preview">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </a>
                                                    <a href="{{ route('babyeyi.download', $babyeyi) }}" class="btn btn-outline-success" title="Download PDF">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $babyeyis->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-3 text-muted">No babyeyi documents have been created yet.</p>
                            <a href="{{ route('babyeyi.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus"></i> Create Your First Babyeyi
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-content h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-size: 12px;
        padding: 6px 10px;
    }
</style>
@endsection
