@extends('admin.layout')

@section('title', 'Academic Years')

@section('content')
<div class="container">
    <h2>Academic Years</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.academic_year.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Add New
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Year</th>
                <th>Starting Date</th>
                <th>Expiry Date</th>
                <th>Is Ongoing</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($academicYears as $year)
                <tr>
                    <td>{{ $year->year }}</td>
                    <td>{{ \Carbon\Carbon::parse($year->starting_date)->format('Y-m-d') }}</td>
                    <td>{{ \Carbon\Carbon::parse($year->expiry_date)->format('Y-m-d') }}</td>
                    <td>{{ $year->is_ongoing ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('admin.academic_year.edit', $year->id) }}" class="btn btn-sm btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('admin.academic_year.destroy', $year->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete();">
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
                    <td colspan="5" class="text-center">No academic years found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this academic year?');
    }
</script>
@endsection
