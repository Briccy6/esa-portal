{{-- filepath: resources/views/emails/exam_password.blade.php --}}
<p>Dear {{ $student->name }},</p>
<p>Your exam <strong>{{ $exam->title }}</strong> is scheduled for today.</p>
<p><strong>Password:</strong> {{ $exam->exam_password }}</p>
<p>
    <a href="{{ route('exams.show', $exam->id) }}" style="background:#007bff;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;">
        Do Exam
    </a>
</p>