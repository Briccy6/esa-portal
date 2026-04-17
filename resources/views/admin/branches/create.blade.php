@extends('admin.layout')

@section('content')
<div class="container">
    <h1>Add New Branch</h1>

    <form action="{{ route('admin.branch.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Branch Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>

        <button class="btn btn-success">Save Branch</button>
        <a href="{{ route('admin.branch.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
