@extends('admin.layout')

@section('title', 'View Timetable')

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $timetable->title }}</h1>
    <div class="header-actions">
        <a href="{{ route('admin.timetables.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Timetables
        </a>
        <a href="{{ route('dos.timetable.edit', $timetable) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Timetable
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Branch:</strong><br>
                {{ $timetable->branch->name ?? 'N/A' }}
            </div>
            <div class="col-md-3">
                <strong>Academic Year:</strong><br>
                {{ $timetable->academicYear->year ?? 'N/A' }}
            </div>
            <div class="col-md-3">
                <strong>Term:</strong><br>
                {{ $timetable->term->name ?? 'N/A' }}
            </div>
            <div class="col-md-3">
                <strong>Status:</strong><br>
                @if($timetable->status === 'published')
                    <span class="badge bg-success">Published</span>
                @else
                    <span class="badge bg-warning">Draft</span>
                @endif
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <strong>Valid From:</strong><br>
                {{ $timetable->valid_from ? $timetable->valid_from->format('M d, Y H:i') : '' }}
            </div>
            <div class="col-md-6">
                <strong>Valid Until:</strong><br>
                {{ $timetable->valid_until ? $timetable->valid_until->format('M d, Y H:i') : '' }}
            </div>
        </div>
    </div>
</div>

@if($timetable->sessions->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
            <h5>No Sessions Found</h5>
            <p class="text-muted">This timetable has no sessions scheduled yet.</p>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">TEACHING TIMETABLE FOR {{ strtoupper($timetable->term->name ?? 'SEMESTER') }} {{ $timetable->academicYear->year ?? '' }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; overflow: hidden;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Time</th>
                            <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Day</th>
                            <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Course</th>
                            <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Lesson</th>
                            <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Room</th>
                            <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Teacher</th>
                        </tr>
                    </thead>
                    <tbody style="background: white;">
                        @php
                            $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'];
                        @endphp
                        @foreach($days as $dayNum => $dayName)
                            @php
                                $daySessions = $timetable->sessions->where('day_of_week', $dayNum)->sortBy('start_time');
                            @endphp
                            @if(!$daySessions->isEmpty())
                                @foreach($daySessions as $session)
                                <tr style="transition: all 0.3s ease;">
                                    <td style="font-weight: 500;">
                                        @forelse($session->shifts as $shift)
                                            <div>{{ ucfirst($shift->shift) }}:
                                                {{ $shift->start_time ? \Carbon\Carbon::parse($shift->start_time)->format('H:i') : ($session->start_time ?? '') }}
                                                -
                                                {{ $shift->end_time ? \Carbon\Carbon::parse($shift->end_time)->format('H:i') : ($session->end_time ?? '') }}
                                            </div>
                                        @empty
                                            {{ $session->start_time ?? '' }} - {{ $session->end_time ?? '' }}
                                        @endforelse
                                    </td>
                                    <td style="font-weight: 500;">{{ $dayName }}</td>
                                    <td>
                                        @foreach($session->courses as $course)
                                            <span class="badge bg-info text-white">{{ $course->course_name }}</span>{{ !$loop->last ? ' ' : '' }}
                                        @endforeach
                                    </td>
                                    <td>{{ $session->lesson->title ?? 'N/A' }}</td>
                                    <td>{{ $session->classroom ?? '' }}</td>
                                    <td>
                                        @if($session->lesson->teacher)
                                            <span class="badge bg-success text-white">{{ $session->lesson->teacher->teacher_name }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">TBA</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <button onclick="window.print()" class="btn btn-success">
                    <i class="fas fa-print"></i> Print Timetable
                </button>
            </div>
        </div>
    </div>
@endif
@endsection
