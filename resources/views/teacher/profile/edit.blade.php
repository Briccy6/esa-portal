@extends('teacher.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('teacher.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center mb-4">
                                    @if($teacher->photo)
                                        <img src="{{ asset('storage/teachers/' . $teacher->photo) }}" alt="Profile Photo" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                            <i class="fas fa-user fa-4x text-white"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Profile Photo</label>
                                    <input type="file" class="form-control" name="photo" accept="image/jpeg,image/png,image/jpg,image/gif">
                                    <small class="text-muted">Max size: 2MB (JPEG, PNG, JPG, GIF)</small>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Teacher Name</label>
                                    <input type="text" class="form-control" name="teacher_name" value="{{ old('teacher_name', $teacher->teacher_name) }}" required>
                                    @error('teacher_name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" class="form-control" value="{{ $teacher->email }}" disabled>
                                    <small class="text-muted">Email cannot be changed</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Phone</label>
                                    <input type="text" class="form-control" name="telephone" value="{{ old('telephone', $teacher->telephone) }}" placeholder="Enter phone number">
                                    @error('telephone')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">New Password (Optional)</label>
                                    <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                                    @error('password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Password must be at least 8 characters with mixed case, letters, numbers, and symbols</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm new password">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                                <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
