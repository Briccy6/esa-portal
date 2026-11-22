@php
    $dayNames = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'];
    $orderedDays = [1,2,3,4,5,6,0];
    $sessionsByDay = collect($sessions ?? collect())->groupBy('day_of_week');
@endphp

<style>
.weekly-schedule {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 1rem;
}
.weekly-day {
    background: #ffffff;
    border: 1px solid #e6e8eb;
    border-radius: 14px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    min-height: 260px;
}
.weekly-day h5 {
    margin: 0 0 0.75rem;
    font-size: 1rem;
    font-weight: 700;
    color: #252f3f;
}
.session-card {
    border: 1px solid #dfe3e8;
    border-radius: 12px;
    padding: 0.75rem;
    background: #f8fafc;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.session-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
}
.session-card strong {
    display: block;
    margin-bottom: 0.4rem;
}
.session-meta {
    font-size: 0.9rem;
    color: #4b5563;
    margin-bottom: 0.4rem;
}
.session-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    font-size: 0.75rem;
    background: #eef2ff;
    color: #4338ca;
    margin-top: 0.4rem;
}
.session-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-top: 0.5rem;
}
</style>

<div class="weekly-schedule">
    @foreach($orderedDays as $dayIndex)
        <div class="weekly-day">
            <h5>{{ $dayNames[$dayIndex] }}</h5>
            @forelse($sessionsByDay->get($dayIndex, collect())->sortBy('start_time') as $session)
                <div class="session-card">
                    <strong>{{ $session->lesson->title ?? 'Untitled Lesson' }}</strong>
                    <div class="session-meta">
                        <div><strong>Time:</strong>
                            @forelse($session->shifts as $shift)
                                <span>{{ ucfirst($shift->shift) }} {{ $shift->start_time ? \Carbon\Carbon::parse($shift->start_time)->format('H:i') : \Carbon\Carbon::parse($session->start_time)->format('H:i') }}-{{ $shift->end_time ? \Carbon\Carbon::parse($shift->end_time)->format('H:i') : \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</span>{{ !$loop->last ? ', ' : '' }}
                            @empty
                                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                            @endforelse
                        </div>
                        <div><strong>Room:</strong> {{ $session->classroom ?: 'TBA' }}</div>
                        <div><strong>Teacher:</strong> {{ $session->lesson->teacher->teacher_name ?? 'TBA' }}</div>
                        <div><strong>Courses:</strong>
                            @if($session->courses->count() > 0)
                                {{ $session->courses->pluck('course_name')->join(', ') }}
                            @else
                                {{ $session->lesson->course->course_name ?? 'N/A' }}
                            @endif
                        </div>
                    </div>
                    @if($session->mark_as_test)
                        <span class="session-badge">Test / Exam</span>
                    @endif
                    <div class="session-actions">
                        @if($session->mark_as_test && $session->lesson && $session->lesson->exams->count())
                            @foreach($session->lesson->exams as $exam)
                                <a href="{{ route('student.exams.take', $exam->id) }}" class="btn btn-sm btn-outline-primary">Prepare for {{ Str::limit($exam->title, 20) }}</a>
                            @endforeach
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-muted">No sessions for this day.</div>
            @endforelse
        </div>
    @endforeach
</div>
