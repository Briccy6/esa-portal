@extends('staff.layout')

@section('title', 'Account Locking Settings')

@section('content')
@include('partials.academic-year-banner')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-gray-800">Account Locking Settings</h1>
</div>

<!-- Locked Accounts Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-lock me-2"></i>Locked Accounts
        </h5>
    </div>
    <div class="card-body">
        @if($lockedAccounts->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="fas fa-unlock fa-3x mb-3"></i>
                <p>No accounts are currently locked.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User Type</th>
                            <th>User ID</th>
                            <th>Lock Reason</th>
                            <th>Locked At</th>
                            <th>Unlock At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lockedAccounts as $lock)
                            <tr>
                                <td>
                                    <span class="badge bg-{{ $lock->user_type === 'staff' ? 'primary' : ($lock->user_type === 'student' ? 'success' : 'info') }}">
                                        {{ ucfirst($lock->user_type) }}
                                    </span>
                                </td>
                                <td>{{ $lock->user_id }}</td>
                                <td>
                                    {{ $lock->lock_reason }}
                                    @if($lock->lock_details)
                                        <small class="text-muted d-block">
                                            @if(isset($lock->lock_details['current_percentage']))
                                                Current: {{ round($lock->lock_details['current_percentage'], 2) }}%, Required: {{ $lock->lock_details['required_percentage'] }}%
                                            @elseif(isset($lock->lock_details['days_without_expense']))
                                                Days without expense: {{ $lock->lock_details['days_without_expense'] }}
                                            @endif
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $lock->locked_at ? $lock->locked_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td>{{ $lock->unlock_at ? $lock->unlock_at->format('Y-m-d H:i') : 'Manual unlock required' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-success unlock-btn"
                                            data-user-type="{{ $lock->user_type }}"
                                            data-user-id="{{ $lock->user_id }}">
                                        <i class="fas fa-unlock me-1"></i>Unlock
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Settings Sections -->
@foreach(['accountant', 'student', 'dos', 'teacher', 'client'] as $role)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-cog me-2"></i>{{ ucfirst($role) }} Settings
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('staff.account-locking.store') }}" method="POST" class="mb-4">
            @csrf
            <input type="hidden" name="role" value="{{ $role }}">

            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Setting Key</label>
                    <select name="setting_key" class="form-select" required>
                        @if($role === 'accountant')
                            <option value="student_count_limit">Student Count Limit</option>
                            <option value="expense_recording_days">Expense Recording Days</option>
                            <option value="show_unpaid_reminder">Show Unpaid Reminder</option>
                        @elseif($role === 'student')
                            <option value="payment_percentage_threshold">Payment Percentage Threshold</option>
                        @else
                            <option value="custom_setting">Custom Setting</option>
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Value</label>
                    <input type="text" name="setting_value" class="form-control" required placeholder="e.g., 50">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-1"></i>Add
                    </button>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Brief description of this setting">
                </div>
            </div>
        </form>

        @if(isset($settings[$role]) && $settings[$role]->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Setting Key</th>
                        <th>Value</th>
                        <th>Branch</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($settings[$role] as $setting)
                    <tr>
                        <td>{{ $setting->setting_key }}</td>
                        <td>
                            @if(is_array($setting->setting_value))
                                {{ $setting->setting_value['value'] ?? $setting->setting_value }}
                            @else
                                {{ $setting->setting_value }}
                            @endif
                        </td>
                        <td>{{ $setting->branch ? $setting->branch->name : 'All Branches' }}</td>
                        <td>{{ $setting->description ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $setting->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $setting->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger delete-setting" data-id="{{ $setting->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted">No settings configured for {{ ucfirst($role) }}.</p>
        @endif
    </div>
</div>
@endforeach

<!-- Unlock Modal -->
<div class="modal fade" id="unlockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Unlock Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('staff.account-locking.unlock') }}" method="POST">
                @csrf
                <input type="hidden" name="user_type" id="unlock_user_type">
                <input type="hidden" name="user_id" id="unlock_user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Unlock Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Reason for unlocking this account..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-unlock me-1"></i>Unlock Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Unlock button handlers
        document.querySelectorAll('.unlock-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('unlock_user_type').value = this.dataset.userType;
                document.getElementById('unlock_user_id').value = this.dataset.userId;
                new bootstrap.Modal(document.getElementById('unlockModal')).show();
            });
        });

        // Delete setting handlers
        document.querySelectorAll('.delete-setting').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this setting?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('staff.account-locking.destroy', '__id__') }}'.replace('__id__', this.dataset.id);
                    form.innerHTML = '<input type="hidden" name="_method" value="DELETE"><input type="hidden" name="_token" value="{{ csrf_token() }}">';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
