@extends($staff->role === 'DOS' ? 'dos.layout' : 'staff.layout')

@section('title', 'My Profile')
@section('page_title', 'My Profile')

@section('content')
@php
    $dashboardRoute = $staff->role === 'DOS' ? 'staff.dashboard.dos' : 'staff.dashboard.accountant';
    $photoUrl = \App\Support\StaffPhoto::url($staff->photo);
@endphp

<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">My profile</h1>
            <p class="text-muted mb-0">Manage your personal information and account password.</p>
        </div>
        <a href="{{ route($dashboardRoute) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Back to dashboard
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <section class="card h-100">
                <div class="card-body text-center p-4">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="{{ $staff->name }}'s profile photo"
                             class="rounded-circle mb-3"
                             style="width: 112px; height: 112px; object-fit: cover; border: 3px solid #e5eaf2;">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                             style="width: 112px; height: 112px; background: #eaf1fb; color: #2457a7;">
                            <i class="fas fa-user" style="font-size: 2.5rem;" aria-hidden="true"></i>
                        </div>
                    @endif
                    <h2 class="h5 mb-1">{{ $staff->name }}</h2>
                    <p class="text-muted mb-3">{{ $staff->email }}</p>
                    <span class="badge text-bg-primary">{{ $staff->role ?? 'Staff' }}</span>
                </div>
            </section>
        </div>

        <div class="col-lg-8">
            <section class="card mb-4">
                <div class="card-header">
                    <h2 class="h6 mb-0"><i class="fas fa-user-edit me-2" aria-hidden="true"></i>Profile information</h2>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('staff.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="profile-name">Full name</label>
                                <input id="profile-name" type="text" class="form-control" name="name"
                                       value="{{ old('name', $staff->name) }}" autocomplete="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="profile-email">Email address</label>
                                <input id="profile-email" type="email" class="form-control" name="email"
                                       value="{{ old('email', $staff->email) }}" autocomplete="email" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="profile-phone">Phone</label>
                                <input id="profile-phone" type="tel" class="form-control" name="phone"
                                       value="{{ old('phone', $staff->phone) }}" autocomplete="tel">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="profile-photo">Profile photo</label>
                                <input id="profile-photo" type="file" class="form-control" name="profile_photo"
                                       accept="image/*" aria-describedby="profile-photo-help">
                                <small id="profile-photo-help" class="form-text text-muted">Image file, up to 2 MB.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="profile-address">Address</label>
                            <textarea id="profile-address" class="form-control" name="address" rows="3"
                                      autocomplete="street-address">{{ old('address', $staff->address) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1" aria-hidden="true"></i>Save profile
                        </button>
                    </form>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <h2 class="h6 mb-0"><i class="fas fa-lock me-2" aria-hidden="true"></i>Change password</h2>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('staff.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label" for="current-password">Current password</label>
                            <input id="current-password" type="password" class="form-control"
                                   name="current_password" autocomplete="current-password" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="new-password">New password</label>
                                <input id="new-password" type="password" class="form-control"
                                       name="password" autocomplete="new-password" minlength="8" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="confirm-password">Confirm new password</label>
                                <input id="confirm-password" type="password" class="form-control"
                                       name="password_confirmation" autocomplete="new-password" minlength="8" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key me-1" aria-hidden="true"></i>Update password
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
