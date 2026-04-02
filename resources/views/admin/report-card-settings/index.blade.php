@extends('admin.layout')

@section('title', 'Report Card Settings')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title">Report Card Settings</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Report Card Settings</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.report-card-settings.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Setting
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-calendar-alt"></i>
            Report Card Settings
        </h5>
    </div>
    <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($settings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Academic Year</th>
                                        <th>Term</th>
                                        <th>Available From</th>
                                        <th>Status</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings as $setting)
                                        <tr>
                                            <td>{{ $setting->academic_year }}</td>
                                            <td>{{ $setting->term }}</td>
                                            <td>
                                                {{ $setting->report_card_available_at->format('M j, Y g:i A') }}
                                                @if(!$setting->isAvailable())
                                                    <small class="text-muted d-block">
                                                        {{ $setting->getTimeRemaining() }} remaining
                                                    </small>
                                                @else
                                                    <small class="text-success d-block">
                                                        Available now
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($setting->is_active)
                                                    @if($setting->isAvailable())
                                                        <span class="badge bg-success">Available</span>
                                                    @else
                                                        <span class="badge bg-warning">Scheduled</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($setting->description, 50) ?? 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.report-card-settings.edit', $setting) }}" 
                                                       class="btn btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.report-card-settings.toggle-status', $setting) }}" 
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-outline-{{ $setting->is_active ? 'warning' : 'success' }}" 
                                                                title="{{ $setting->is_active ? 'Deactivate' : 'Activate' }}">
                                                            <i class="fas fa-{{ $setting->is_active ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.report-card-settings.destroy', $setting) }}" 
                                                          method="POST" style="display: inline;"
                                                          onsubmit="return confirm('Are you sure you want to delete this report card setting?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            {{ $settings->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Report Card Settings Found</h5>
                            <p class="text-muted">Create your first report card setting to manage when students can access their report cards.</p>
                            <a href="{{ route('admin.report-card-settings.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Setting
                            </a>
                        </div>
                    @endif
                </div>
            </div>
@endsection
