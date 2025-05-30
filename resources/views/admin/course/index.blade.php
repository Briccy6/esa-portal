@extends('admin.layout')

@section('title', 'Courses')

@section('content')
<div class="container">
    <h2>Courses</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.course.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Add New Course
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Abbreviation</th>
                <th>Course Type</th>
                <th>Course Code</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
      <tbody>
    @forelse($courses as $course)
        <tr>
            <td>{{ $course->course_name }}</td>
            <td>{{ $course->abbreviation }}</td>
            <td>{{ $course->course_type }}</td>
            <td>{{ $course->course_code }}</td>
            <td>
                <a href="{{ route('admin.course.edit', $course->id) }}" class="btn btn-sm btn-warning" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>

                <form action="{{ route('admin.course.destroy', $course->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete();">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">No courses found.</td>
        </tr>
    @endforelse
</tbody>

    </table>
</div>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this course?');
    }
</script>
@endsection
