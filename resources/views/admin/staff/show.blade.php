@extends('admin.layout')

@section('title', 'Staff Details')

@section('content')
<div class="container mt-4">
    <h2>Staff Details</h2>

    <div class="card p-4" style="max-width: 600px;">
        <div class="mb-3 text-center">
            @if($staff->photo)
                <img src="{{ \App\Support\StaffPhoto::url($staff->photo) }}" alt="Staff Photo" class="img-thumbnail" style="max-width: 150px;">
            @else
                <div class="text-muted">No photo available</div>
            @endif
        </div>

        <p><strong>Name:</strong> {{ $staff->name }}</p>
        <p><strong>Role:</strong> {{ $staff->role }}</p>
        <p><strong>Email:</strong> {{ $staff->email }}</p>
        <p><strong>Phone:</strong> {{ $staff->phone }}</p>

       <div class="mb-3">
    <label for="password" class="form-label"><strong>Temporary Password:</strong></label>
    <div class="input-group">
<input type="password" id="password" class="form-control" value="{{ $temp_password ?? '**********' }}" readonly>
        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
            <i class="fas fa-eye"></i>
        </button>
    </div>
</div>


        <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        if (!togglePassword) return; // Only run if togglePassword exists on page

        togglePassword.addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle the eye / eye-slash icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });
</script>

@endsection
