@extends('admin.layout')

@section('content')
<div class="container">
    <h1>School Branches</h1>

    <a href="{{ route('admin.branch.create') }}" class="btn btn-primary mb-3">+ Add Branch</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Branch Name</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($branches as $index => $branch)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $branch->name }}</td>
                    <td>{{ $branch->location }}</td>
                    <td>
                        <a href="{{ route('admin.branch.edit', $branch->id) }}" class="btn btn-sm btn-warning">Edit</a>

                        <form action="{{ route('admin.branch.destroy', $branch->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete this branch?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No branches found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
