@extends('admin.layout')

@section('title', 'Staff Members')

@section('content')
<div class="container">
    <h2>Staff Members <a href="{{ route('admin.staff.create') }}" class="btn btn-primary float-end">Add Staff</a></h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Branch</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staffs as $staff)
            <tr>
                <td>{{ $staff->name }}</td>
                <td>{{ $staff->role }}</td>
                <td>
                    @if($staff->branches && $staff->branches->count())
                        {{ $staff->branches->pluck('name')->join(', ') }}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if($staff->photo)
                        <img src="data:image/jpeg;base64,{{ $staff->photo }}" alt="Photo" width="50" height="50">
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.staff.show', $staff->id) }}" class="btn btn-info btn-sm">View</a>
                    <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this staff?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                    </form>
                    <form action="{{ route('admin.staff.reset_password', $staff->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Reset password for this staff?');">
                        @csrf
                        <button class="btn btn-secondary btn-sm" type="submit">Reset Password</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
