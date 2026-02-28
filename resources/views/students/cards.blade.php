@php
    $contactInfo = env('ESA_CONTACT_INFO', 'Contact us at: info@excellentservantacademy.com, Tel: +250 788 525 247');
    $logoUrl = \App\Helpers\LogoHelper::getLogoUrl();
    $schoolName = 'Excellent Servant Academy';
    $timestamp = now()->format('Y-m-d H:i');
    $generatedBy = auth()->user()->name ?? 'System';
   
    $systemName = $systemName ?? 'ESA E-Learning System';
    $systemLogo = \App\Helpers\LogoHelper::getLogoUrl();
    @endphp

<div class="row g-4 mb-4">
    @foreach($exams as $exam)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm bg-white text-dark">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-file-alt me-2"></i>{{ $exam->title }}</h5>
                    <p class="card-text">
                        <strong>Courses:</strong>
                        @forelse($exam->courses as $course)
                            {{ $course->course_name }}{{ !$loop->last ? ',' : '' }}
                        @empty
                            -
                        @endforelse
                        <br>
                        <strong>Date:</strong> {{ $exam->date }}<br>
                        <strong>Lessons:</strong>
                        @forelse($exam->lessons as $lesson)
                            {{ $lesson->title }}{{ !$loop->last ? ',' : '' }}
                        @empty
                            -
                        @endforelse
                    </p>
                    @if(isset($marks[$exam->id]))
                        <div class="alert alert-success mb-2">
                            <strong>Mark:</strong> {{ $marks[$exam->id]->value }}<br>
                            <strong>Grade:</strong> {{ $grades[$marks[$exam->id]->grade_id] ?? '-' }}
                        </div>
                    @else
                       <div class="d-flex flex-column gap-2 mt-2">
    <button 
        class="btn btn-primary" 
        onclick="openExamFullscreen({{ $exam->id }})"
    >
        <i class="fas fa-brain me-1"></i> Take Exam
    </button>

    <button 
        class="btn btn-outline-secondary"
        data-bs-toggle="modal"
        data-bs-target="#prepareExamModal"
        data-exam-id="{{ $exam->id }}"
        data-exam-subject="{{ $exam->title }}"
    >
        <i class="fas fa-robot me-1"></i> Prepare with AI
    </button>
</div>

                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>


{{-- Announcements Card --}}
<div class="col-md-4">
    <div class="card h-100 shadow-sm bg-white text-dark">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-bullhorn me-2"></i>Announcements</h5>
            <ul class="list-unstyled mb-0" style="max-height: 120px; overflow-y: auto;">
                @forelse($announcements as $announcement)
                    <li class="mb-2">
                        <strong>{{ $announcement->title }}</strong><br>
                        <small>{{ $announcement->created_at->format('M d, Y') }}</small>
                        <div>{{ $announcement->content }}</div>
                    </li>
                @empty
                    <li>No announcements at this time.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
<br>

{{-- Payment Card --}}
{{-- Prepare for Exam Modal --}}
<div class="modal fade" id="prepareExamModal" tabindex="-1" aria-labelledby="prepareExamModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content bg-white text-dark">
      <div class="modal-header">
        <h5 class="modal-title" id="prepareExamModalLabel">AI Exam Preparation Assistant</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="aiExamForm">
          <input type="hidden" id="examId" name="exam_id">
          <input type="hidden" id="examSubject" name="subject">
          <div class="mb-3">
            <label for="studentQuestion" class="form-label">Ask a question or request a summary:</label>
            <textarea class="form-control" id="studentQuestion" rows="3" placeholder="Type your question or leave blank for a summary..."></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Ask AI</button>
        </form>
        <div id="aiExamResponse" class="mt-3" style="display:none;">
          <div class="alert alert-info"></div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Payment Modal --}}
<div class="modal fade" id="payNowModal" tabindex="-1" aria-labelledby="payNowModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-white text-dark">
      <div class="modal-header">
        <h5 class="modal-title" id="payNowModalLabel">Pay Now</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" class="form-control" id="amount" placeholder="Enter amount">
          </div>
          <button type="submit" class="btn btn-primary">Pay</button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Payment Tracking Bar Graph --}}
<div class="col-md-8">
    <div class="card h-100 shadow-sm bg-white text-dark">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-chart-bar me-2"></i>Payment Tracking</h5>
            <canvas id="paymentBarChart" height="120"></canvas>
            <div class="mt-3 text-end">
                <a href="{{ route('student.payment') }}" class="btn btn-success">
                    <i class="fas fa-money-bill-wave me-1"></i> Pay Now
                </a>
            </div>
        </div>
    </div>
</div>

@php
    $fees = [
        ['type' => 'Registration', 'amount' => $registrationFee],
        ['type' => 'Tuition', 'amount' => $tuitionFee],
        ['type' => 'Trip', 'amount' => $tripFee],
        ['type' => 'Practical', 'amount' => $praticalfee],
    ];
    $paidData = [];
    $remainingData = [];
    $labels = [];
    foreach($fees as $fee) {
        $paid = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('type', strtolower($fee['type']))
            ->whereIn('status', ['paid', 'successful'])
            ->sum('amount');
        $remaining = max(0, $fee['amount'] - $paid);
        $labels[] = $fee['type'];
        $paidData[] = $paid;
        $remainingData[] = $remaining;
    }
@endphp

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('paymentBarChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [
                {
                    label: 'Paid',
                    data: {!! json_encode($paidData) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                },
                {
                    label: 'Remaining',
                    data: {!! json_encode($remainingData) !!},
                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: false },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y + ' RWF';
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1000 }
                }
            }
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    // Fill modal with exam info
    document.querySelectorAll('[data-bs-target="#prepareExamModal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('examId').value = this.getAttribute('data-exam-id');
            document.getElementById('examSubject').value = this.getAttribute('data-exam-subject');
        });
    });

    // AI form submit
    const aiForm = document.getElementById('aiExamForm');
    const aiResponseDiv = document.getElementById('aiExamResponse');
    if(aiForm) {
        aiForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            aiResponseDiv.style.display = 'block';
            aiResponseDiv.querySelector('.alert').textContent = 'Thinking...';

            const exam_id = document.getElementById('examId').value;
            const subject = document.getElementById('examSubject').value;
            const question = document.getElementById('studentQuestion').value;

            const response = await fetch('{{ route('student.ai.exam') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    exam_id: exam_id,
                    subject: subject,
                    question: question
                })
            });
            const data = await response.json();
            if(data.success) {
                aiResponseDiv.querySelector('.alert').innerHTML = data.summary;
            } else {
                aiResponseDiv.querySelector('.alert').textContent = data.message || 'No answer received.';
            }
        });
    }
});

function openExamFullscreen(examId) {
    const url = "{{ url('/exam') }}/" + examId + "/take";
    const win = window.open(url, '_blank');
    win.focus();
}
</script>