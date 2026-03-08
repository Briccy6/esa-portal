{{-- filepath: d:\website\esa\resources\views\exams\take.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $exam->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        html, body {
            background: #fff !important;
            color: #222;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            user-select: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
        }
        .exam-container { max-width: 600px; margin: 2rem auto; background: #f8f9fa; padding: 2rem; border-radius: 8px; box-shadow: 0 0 10px #ddd; }
        .form-check { margin-bottom: 0.5rem; }
        .btn { padding: 0.5rem 1.5rem; font-size: 1rem; }
        .badge { font-size: 0.9rem; }
        /* Hide scrollbars */
        ::-webkit-scrollbar { display: none; }
        body { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body>
<div class="exam-container">
    <h2>{{ $exam->title }}</h2>
    <p><strong>Instructions:</strong> {{ $exam->instructions ?? 'Read each question carefully.' }}</p>
    
    @if($exam->requiresTokens())
        <div class="alert alert-info mb-3">
            <i class="fas fa-coins"></i> 
            <strong>Token Cost:</strong> {{ $exam->token_cost }} tokens 
            @if(isset($student) && $student->token)
                | <strong>Your Balance:</strong> {{ $student->token->balance }} tokens
            @endif
        </div>
    @endif
    
    <form id="examForm" method="POST" action="{{ route('student.exams.submit', $exam->id) }}">
        @csrf
        <div id="questionArea"></div>
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" id="prevBtn" style="display:none;">Previous</button>
            <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
            <button type="submit" class="btn btn-success" id="submitBtn" style="display:none;">Submit Exam</button>
        </div>
    </form>
</div>
<script>
const questions = @json($questions->values());
let current = 0;
let answers = {};
const total = questions.length;

// Debug: Log questions to console
console.log('Questions loaded:', questions);
console.log('First question:', questions[0]);

// Render a single question
function renderQuestion(idx) {
    const q = questions[idx];
    console.log('Rendering question:', q);
    
    let html = `<div class="mb-4">
        <label>
            <strong>Q${idx+1}:</strong> ${q.text || 'No question text available'}
            <span class="badge bg-info text-dark ms-2">Marks: ${q.marks ?? 1}</span>
        </label>`;
    const options = [q.option_a, q.option_b, q.option_c, q.option_d].filter(Boolean);
    if (options.length) {
        options.forEach((value, optIdx) => {
            const checked = answers[q.id] === value ? 'checked' : '';
            html += `<div class="form-check">
                <input class="form-check-input" type="radio" name="answer" id="q${q.id}_opt${optIdx}" value="${value}" ${checked} required>
                <label class="form-check-label" for="q${q.id}_opt${optIdx}">${value}</label>
            </div>`;
        });
    } else {
        html += `<div class="alert alert-warning">No options available for this question.</div>`;
    }
    html += `</div>`;
    document.getElementById('questionArea').innerHTML = html;
    document.getElementById('prevBtn').style.display = idx > 0 ? '' : 'none';
    document.getElementById('nextBtn').style.display = idx < total - 1 ? '' : 'none';
    document.getElementById('submitBtn').style.display = idx === total - 1 ? '' : 'none';
}

// Save answer on change
document.addEventListener('change', function(e) {
    if (e.target.name === 'answer') {
        answers[questions[current].id] = e.target.value;
    }
});

// Navigation
document.getElementById('prevBtn').onclick = function() {
    if (current > 0) {
        current--;
        renderQuestion(current);
    }
};
document.getElementById('nextBtn').onclick = function() {
    const selected = document.querySelector('input[name="answer"]:checked');
    if (!selected) {
        alert('Please select an answer before proceeding.');
        return;
    }
    answers[questions[current].id] = selected.value;
    if (current < total - 1) {
        current++;
        renderQuestion(current);
    }
};
document.getElementById('examForm').onsubmit = function() {
    Object.entries(answers).forEach(([qid, val]) => {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = `answers[${qid}]`;
        input.value = val;
        this.appendChild(input);
    });
    return true;
};
renderQuestion(current);

// --- Security: Prevent copy, print, screenshot, right-click ---
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('cut', e => e.preventDefault());
document.addEventListener('paste', e => e.preventDefault());
document.addEventListener('keydown', function(e) {
    // Block Ctrl+C, Ctrl+P, Ctrl+S, Ctrl+U, F12, PrintScreen, etc.
    if (
        (e.ctrlKey && ['c', 'p', 's', 'u', 'a'].includes(e.key.toLowerCase())) ||
        e.key === 'PrintScreen' ||
        e.key === 'F12' ||
        (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'i')
    ) {
        e.preventDefault();
        return false;
    }
});

// --- Cheating detection: Tab switch or blur = immediate disqualify ---
function disqualify() {
    alert('You have been disqualified for leaving the exam window.');
    window.location.href = "{{ route('student.exams.disqualified', $exam->id) }}";
}
window.onblur = disqualify;
document.addEventListener('visibilitychange', function() {
    if (document.hidden) disqualify();
});
</script>
</body>
</html>
