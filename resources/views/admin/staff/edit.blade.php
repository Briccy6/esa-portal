@extends('admin.layout')

@section('title', 'Edit Staff')

@section('content')
<div class="container mt-4">
    <h2>Edit Staff</h2>

    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input 
                type="text" 
                class="form-control @error('name') is-invalid @enderror" 
                id="name" 
                name="name" 
                value="{{ old('name', $staff->name) }}" 
                required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select 
                id="role" 
                name="role" 
                class="form-select @error('role') is-invalid @enderror" 
                required>
                @foreach($roles as $role)
                    <option value="{{ $role }}" {{ old('role', $staff->role) == $role ? 'selected' : '' }}>
                        {{ $role }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="branches" class="form-label">Branches</label>
            <select 
                id="branches" 
                name="branches[]" 
                class="form-select @error('branches') is-invalid @enderror" 
                multiple
                required>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}"
                        {{ (collect(old('branches', $staff->branches->pluck('id')->toArray()))->contains($branch->id)) ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
            @error('branches')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple branches.</small>
        </div>

        <div class="mb-3">
            <label for="photo" class="form-label">Photo (Leave empty to keep current)</label>
            <input 
                type="file" 
                id="photo" 
                name="photo" 
                class="form-control @error('photo') is-invalid @enderror" 
                accept="image/*">
            @error('photo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if($staff->photo)
                <div class="mt-2">
                    <img src="data:image/jpeg;base64,{{ $staff->photo }}" alt="Current Photo" style="max-width: 150px;">
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Update Staff</button>
        <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
