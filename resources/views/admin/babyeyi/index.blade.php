@extends('admin.layout')

@section('title', 'Babyeyi Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-contract text-primary me-2"></i>
                Term Registration Documents (Babyeyi)
            </h2>
            <div class="text-muted">Create, edit, preview and publish term registration documents.</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('babyeyi.bulk.create') }}" class="btn btn-success">
                <i class="fas fa-bolt me-1"></i> Bulk Generate
            </a>
            <a href="{{ route('babyeyi.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Create New
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php
        $total = $babyeyis->total();
        $published = $babyeyis->where('status', 'published')->count();
        $draft = $babyeyis->where('status', 'draft')->count();
        $preview = $babyeyis->where('status', 'preview')->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total</div>
                    <div class="h3 mb-0">{{ $total }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Published</div>
                    <div class="h3 mb-0 text-success">{{ $published }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Draft</div>
                    <div class="h3 mb-0 text-warning">{{ $draft }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Preview</div>
                    <div class="h3 mb-0 text-info">{{ $preview }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <strong>All Babyeyi Documents</strong>
        </div>
        <div class="card-body">
            @if ($babyeyis->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Reference</th>
                                <th>Academic Year / Term</th>
                                <th>Course</th>
                                <th>Created By</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($babyeyis as $babyeyi)
                                <tr>
                                    <td><code class="bg-light px-2 py-1 rounded">{{ $babyeyi->reference_number }}</code></td>
                                    <td>
                                        <strong>{{ $babyeyi->academicYear->year ?? 'N/A' }}</strong><br>
                                        <span class="text-muted small">{{ $babyeyi->term->name ?? 'N/A' }}</span>
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
                                            <span class="badge bg-warning text-dark">Draft</span>
                                        @elseif ($babyeyi->status === 'preview')
                                            <span class="badge bg-info text-dark">Preview</span>
                                        @else
                                            <span class="badge bg-success">Published</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $babyeyi->created_at->format('M d, Y') }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('babyeyi.show', $babyeyi) }}" class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('babyeyi.edit', $babyeyi) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('babyeyi.preview', $babyeyi) }}" class="btn btn-outline-info" title="Preview">
                                                <i class="fas fa-search"></i>
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

                <div class="mt-3">
                    {{ $babyeyis->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="text-muted mb-2">
                        <i class="fas fa-inbox" style="font-size: 2.5rem; opacity: 0.25;"></i>
                    </div>
                    <div class="h5 mb-2">No babyeyi documents yet</div>
                    <div class="text-muted mb-3">Create your first term registration document.</div>
                    <a href="{{ route('babyeyi.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create Document
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
