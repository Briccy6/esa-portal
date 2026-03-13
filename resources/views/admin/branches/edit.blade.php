@extends('admin.layout')

@section('content')
<div class="container">
    <h1>Edit Branch</h1>

    <form action="{{ route('admin.branch.update', $branch->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Branch Name</label>
            <input type="text" name="name" value="{{ $branch->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" value="{{ $branch->location }}" class="form-control" required>
        </div>

        <button class="btn btn-primary">Update Branch</button>
        <a href="{{ route('admin.branch.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
