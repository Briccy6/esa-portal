<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Date</th>
            <th>Courses</th>
            <th>Lessons</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($exams as $index => $exam)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $exam->title }}</td>
                <td>{{ \Carbon\Carbon::parse($exam->date)->format('M d, Y') }}</td>
                <td>
                    @foreach($exam->courses as $course)
                        <span class="badge bg-primary">{{ $course->course_name }}</span>
                    @endforeach
                </td>
                <td>
                    @foreach($exam->lessons as $lesson)
                        <span class="badge bg-info text-dark">{{ $lesson->title }}</span>
                    @endforeach
                </td>
                <td>
                    <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> View
                    </a>
                    @if(auth('admin')->check() || auth('staff')->check())
                        <a href="{{ route('exams.edit', $exam) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>