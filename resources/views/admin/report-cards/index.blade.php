@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Report Cards</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.report-cards.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Generate Report Card
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <form method="POST" action="{{ route('admin.report-cards.generate-class') }}" class="form-inline">
                        @csrf
                            <div class="form-group mr-2">
                                <label for="course_id" class="mr-2">Course:</label>
                                <select name="course_id" id="course_id" class="form-control form-control-sm" required>
                                    <option value="">Select Course</option>
                                    @foreach(\App\Models\Course::all() as $course)
                                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <label for="academic_year_id" class="mr-2">Academic Year:</label>
                                <select name="academic_year_id" id="academic_year_id" class="form-control form-control-sm" required>
                                    <option value="">Select Year</option>
                                    @foreach(\App\Models\AcademicYear::all() as $year)
                                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <label for="term_id" class="mr-2">Term:</label>
                                <select name="term_id" id="term_id" class="form-control form-control-sm" required>
                                    <option value="">Select Term</option>
                                    @foreach(\App\Models\Term::all() as $term)
                                        <option value="{{ $term->id }}">Term {{ $term->term }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info btn-sm">
                                <i class="fas fa-users"></i> Generate for Class
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>Academic Year</th>
                                    <th>Term</th>
                                    <th>Average</th>
                                    <th>Grade</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportCards as $reportCard)
                                    <tr>
                                        <td>{{ $reportCard->student->full_name }}</td>
                                        <td>{{ $reportCard->course->course_name }}</td>
                                        <td>{{ $reportCard->academicYear->year }}</td>
                                        <td>Term {{ $reportCard->term->term }}</td>
                                        <td>{{ number_format($reportCard->average_marks, 2) }}%</td>
                                        <td>
                                            <span class="badge badge-{{ $reportCard->grade == 'A' || $reportCard->grade == 'A+' ? 'success' : ($reportCard->grade == 'B' || $reportCard->grade == 'B+' ? 'info' : ($reportCard->grade == 'C' ? 'warning' : 'danger')) }}">
                                                {{ $reportCard->grade }}
                                            </span>
                                        </td>
                                        <td>{{ $reportCard->position_display }}</td>
                                        <td>
                                            <span class="badge badge-{{ $reportCard->status == 'approved' ? 'success' : ($reportCard->status == 'printed' ? 'primary' : 'warning') }}">
                                                {{ ucfirst($reportCard->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.report-cards.show', $reportCard) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.report-cards.print', $reportCard) }}" class="btn btn-sm btn-primary" target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                @if($reportCard->status !== 'approved')
                                                    <a href="{{ route('admin.report-cards.edit', $reportCard) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                <form method="POST" action="{{ route('admin.report-cards.destroy', $reportCard) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $reportCards->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
