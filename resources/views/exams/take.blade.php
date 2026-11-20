<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $exam->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#183c32">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,500;6..72,600;6..72,700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --exam-ink: #1d2925;
            --exam-muted: #65726c;
            --exam-green: #1c4b3d;
            --exam-green-dark: #14392f;
            --exam-green-soft: #e7f0e9;
            --exam-paper: #fffefa;
            --exam-canvas: #f3f2eb;
            --exam-line: #e5e3d8;
            --exam-amber: #d89038;
            --exam-shadow: 0 18px 55px rgba(34, 49, 41, .09);
        }

        * { box-sizing: border-box; }

        html, body {
            min-height: 100%;
            background: var(--exam-canvas);
            color: var(--exam-ink);
            margin: 0;
            padding: 0;
            font-family: 'DM Sans', sans-serif;
        }
        body {
            background-image:
                radial-gradient(ellipse at 8% 0%, rgba(216, 144, 56, .12), transparent 34rem),
                linear-gradient(rgba(28, 75, 61, .025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(28, 75, 61, .025) 1px, transparent 1px);
            background-size: auto, 36px 36px, 36px 36px;
        }
        .exam-shell { min-height: 100vh; padding: 0 24px 64px; }
        .exam-masthead {
            max-width: 1120px;
            min-height: 76px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(28, 75, 61, .14);
        }
        .brand-lockup { display: flex; align-items: center; gap: 12px; }
        .brand-seal {
            width: 38px; height: 38px;
            display: grid; place-items: center;
            border-radius: 11px 11px 11px 3px;
            color: #fff; background: var(--exam-green);
            font-family: 'Newsreader', Georgia, serif;
            font-size: 16px; font-weight: 700;
            letter-spacing: -.06em;
        }
        .brand-name { color: var(--exam-ink); font-size: 12px; font-weight: 700; letter-spacing: .12em; }
        .session-label { display: flex; align-items: center; gap: 9px; color: var(--exam-muted); font-size: 12px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; }
        .session-dot { width: 8px; height: 8px; border-radius: 50%; background: #3b8c65; box-shadow: 0 0 0 4px rgba(59, 140, 101, .12); }
        .exam-container { max-width: 1120px; margin: 56px auto 0; }
        .exam-intro {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 48px;
            align-items: end;
            margin-bottom: 30px;
        }
        .eyebrow { margin: 0 0 14px; color: #8a6333; font-size: 11px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
        .exam-title {
            margin: 0;
            max-width: 760px;
            color: var(--exam-ink);
            font-family: 'Newsreader', Georgia, serif;
            font-size: clamp(36px, 5vw, 56px);
            font-weight: 600;
            letter-spacing: -.045em;
            line-height: 1.02;
        }
        .exam-instructions { max-width: 660px; margin: 15px 0 0; color: var(--exam-muted); font-size: 15px; line-height: 1.7; }
        .exam-aside {
            display: grid; grid-template-columns: 1fr 1fr;
            padding: 17px 0;
            border-top: 1px solid var(--exam-line);
            border-bottom: 1px solid var(--exam-line);
        }
        .aside-stat + .aside-stat { padding-left: 18px; border-left: 1px solid var(--exam-line); }
        .aside-label { display: block; margin-bottom: 6px; color: var(--exam-muted); font-size: 10px; font-weight: 700; letter-spacing: .11em; text-transform: uppercase; }
        .aside-value { color: var(--exam-ink); font-size: 14px; font-weight: 700; }
        .exam-layout { display: grid; grid-template-columns: minmax(0, 1fr) 278px; gap: 22px; align-items: start; }
        .question-card, .sidebar-card {
            border: 1px solid rgba(28, 75, 61, .09);
            border-radius: 18px;
            background: var(--exam-paper);
            box-shadow: var(--exam-shadow);
        }
        .question-card { padding: clamp(24px, 4vw, 42px); }
        .question-progress { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 13px; }
        .question-kicker { color: var(--exam-green); font-size: 11px; font-weight: 700; letter-spacing: .13em; text-transform: uppercase; }
        .question-count { color: var(--exam-muted); font-size: 12px; font-weight: 600; font-variant-numeric: tabular-nums; }
        .progress-track { height: 4px; margin-bottom: 30px; overflow: hidden; border-radius: 10px; background: #e9e9df; }
        .progress-fill { width: 0; height: 100%; border-radius: inherit; background: var(--exam-green); transition: width .3s ease; }
        .question-prompt { margin: 0 0 28px; color: var(--exam-ink); font-family: 'Newsreader', Georgia, serif; font-size: clamp(23px, 3vw, 31px); font-weight: 500; letter-spacing: -.025em; line-height: 1.35; }
        .question-prompt strong { color: var(--exam-green); font-family: 'DM Sans', sans-serif; font-size: 13px; letter-spacing: .08em; }
        .marks-badge { display: inline-flex; align-items: center; gap: 7px; margin-left: 10px; padding: 5px 9px; border-radius: 99px; background: #f8f0df; color: #855a23; font: 700 11px 'DM Sans', sans-serif; vertical-align: middle; white-space: nowrap; }
        .answer-list { display: grid; gap: 10px; }
        .answer-option {
            display: flex; align-items: center; gap: 14px;
            min-height: 62px; padding: 14px 16px;
            border: 1px solid var(--exam-line); border-radius: 12px;
            background: #fff; color: var(--exam-ink); cursor: pointer;
            transition: border-color .18s ease, background .18s ease, transform .18s ease, box-shadow .18s ease;
        }
        .answer-option:hover { transform: translateY(-1px); border-color: #9ab6a5; box-shadow: 0 6px 16px rgba(28, 75, 61, .07); }
        .answer-option.selected { border-color: var(--exam-green); background: #f1f6f1; box-shadow: inset 0 0 0 1px var(--exam-green); }
        .answer-radio { width: 18px; height: 18px; flex: 0 0 auto; margin: 0; accent-color: var(--exam-green); }
        .answer-option:focus-within { outline: 3px solid rgba(28, 75, 61, .2); outline-offset: 2px; }
        .option-text { font-size: 14px; line-height: 1.55; }
        .navigation { display: flex; justify-content: space-between; align-items: center; gap: 14px; margin-top: 30px; padding-top: 22px; border-top: 1px solid var(--exam-line); }
        .nav-hint { color: var(--exam-muted); font-size: 12px; }
        .nav-btn {
            min-height: 46px; padding: 0 19px;
            border: 1px solid transparent; border-radius: 9px;
            font: 700 13px 'DM Sans', sans-serif; cursor: pointer;
            transition: background .18s ease, transform .18s ease, box-shadow .18s ease;
        }
        .nav-btn:hover { transform: translateY(-1px); }
        .nav-btn.primary { display: inline-flex; align-items: center; gap: 12px; color: #fff; background: var(--exam-green); box-shadow: 0 5px 12px rgba(28, 75, 61, .18); }
        .nav-btn.primary:hover { background: var(--exam-green-dark); }
        .nav-btn.secondary { color: var(--exam-green); background: #fff; border-color: var(--exam-line); }
        .nav-btn.secondary:hover { background: var(--exam-green-soft); }
        .sidebar { display: grid; gap: 14px; }
        .sidebar-card { padding: 21px; box-shadow: 0 9px 28px rgba(34, 49, 41, .055); }
        .sidebar-label { margin: 0 0 16px; color: var(--exam-muted); font-size: 10px; font-weight: 700; letter-spacing: .13em; text-transform: uppercase; }
        .token-row { display: flex; align-items: center; gap: 12px; }
        .token-icon { display: grid; width: 38px; height: 38px; flex: 0 0 auto; place-items: center; border-radius: 12px; background: #f8f0df; color: #a56c26; font-size: 16px; }
        .token-value { display: block; color: var(--exam-ink); font-size: 17px; font-weight: 700; }
        .token-caption { display: block; margin-top: 2px; color: var(--exam-muted); font-size: 11px; }
        .token-divider { height: 1px; margin: 17px 0; background: var(--exam-line); }
        .token-balance { display: flex; justify-content: space-between; gap: 10px; color: var(--exam-muted); font-size: 12px; }
        .token-balance strong { color: var(--exam-ink); }
        .guide-card { background: var(--exam-green); color: #fff; }
        .guide-card .sidebar-label { color: #b6d0bf; }
        .guide-mark { display: grid; width: 34px; height: 34px; margin-bottom: 12px; place-items: center; border: 1px solid rgba(255,255,255,.24); border-radius: 10px; color: #f2c276; font-size: 15px; }
        .guide-title { margin: 0 0 8px; font-family: 'Newsreader', Georgia, serif; font-size: 20px; font-weight: 500; }
        .guide-copy { margin: 0; color: #d6e2d9; font-size: 12px; line-height: 1.65; }
        .security-note { display: flex; align-items: flex-start; gap: 10px; padding: 3px 4px; color: var(--exam-muted); font-size: 11px; line-height: 1.55; }
        .security-note span:first-child { color: var(--exam-green); }
        .empty-options { padding: 14px; border-radius: 10px; background: #fff5e7; color: #76521f; font-size: 13px; }
        @media (max-width: 820px) {
            .exam-container { margin-top: 38px; }
            .exam-intro { grid-template-columns: 1fr; gap: 22px; }
            .exam-aside { max-width: 420px; }
            .exam-layout { grid-template-columns: 1fr; }
            .sidebar { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .guide-card { grid-row: span 2; }
        }
        @media (max-width: 560px) {
            .exam-shell { padding: 0 16px 36px; }
            .exam-masthead { min-height: 64px; }
            .session-label { font-size: 10px; }
            .exam-container { margin-top: 31px; }
            .exam-title { font-size: 39px; }
            .exam-instructions { font-size: 14px; }
            .question-card { padding: 23px 18px; border-radius: 14px; }
            .question-prompt { font-size: 23px; }
            .answer-option { min-height: 57px; padding: 12px; }
            .navigation { align-items: stretch; }
            .nav-hint { display: none; }
            .nav-btn { flex: 1; padding: 0 12px; }
            .sidebar { grid-template-columns: 1fr; }
            .guide-card { grid-row: auto; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; }
        }
    </style>
</head>
<body>
<div class="exam-shell">
    <header class="exam-masthead">
        <div class="brand-lockup">
            <div class="brand-seal" aria-hidden="true">ESA</div>
            <span class="brand-name">EXCELLENT SERVANT ACADEMY</span>
        </div>
        <div class="session-label"><span class="session-dot" aria-hidden="true"></span> Assessment in progress</div>
    </header>

    <main class="exam-container">
        <section class="exam-intro" aria-labelledby="examTitle">
            <div>
                <p class="eyebrow">Student examination &nbsp; / &nbsp; Live session</p>
                <h1 class="exam-title" id="examTitle">{{ $exam->title }}</h1>
                <p class="exam-instructions"><strong>Instructions</strong> &nbsp; {{ $exam->instructions ?? 'Read each question carefully.' }}</p>
            </div>
            <div class="exam-aside" aria-label="Exam summary">
                <div class="aside-stat">
                    <span class="aside-label">Session</span>
                    <span class="aside-value">In progress</span>
                </div>
                <div class="aside-stat">
                    <span class="aside-label">Questions</span>
                    <span class="aside-value" id="questionTotal">{{ $questions->count() }}</span>
                </div>
            </div>
        </section>

        <div class="exam-layout">
            <section class="question-card" aria-label="Exam question">
                <div class="question-progress">
                    <span class="question-kicker" id="questionLabel">Question 01</span>
                    <span class="question-count" id="questionCount">01 / {{ sprintf('%02d', $questions->count()) }}</span>
                </div>
                <div class="progress-track" role="progressbar" aria-label="Exam progress" aria-valuemin="0" aria-valuemax="{{ max(1, $questions->count()) }}" aria-valuenow="0">
                    <div class="progress-fill" id="questionProgress"></div>
                </div>
                <form id="examForm" method="POST" action="{{ route('student.exams.submit', $exam->id) }}">
                    @csrf
                    <div id="questionArea" aria-live="polite"></div>
                    <div class="navigation" id="examNavigation">
                        <span class="nav-hint">Your selection stays on this page as you move between questions.</span>
                        <button type="button" class="nav-btn secondary" id="prevBtn" style="display:none;">Previous</button>
                        <button type="button" class="nav-btn primary" id="nextBtn">Next question <span aria-hidden="true">&rarr;</span></button>
                        <button type="submit" class="nav-btn primary" id="submitBtn" style="display:none;">Submit exam <span aria-hidden="true">&rarr;</span></button>
                    </div>
                </form>
            </section>

            <aside class="sidebar" aria-label="Exam details">
                @if($exam->requiresTokens())
                    <section class="sidebar-card">
                        <h2 class="sidebar-label">Exam access</h2>
                        <div class="token-row">
                            <span class="token-icon" aria-hidden="true">&#9672;</span>
                            <div>
                                <span class="token-value">{{ $exam->token_cost }} tokens</span>
                                <span class="token-caption">This exam's entry cost</span>
                            </div>
                        </div>
                        @if(isset($student) && $student->token)
                            <div class="token-divider"></div>
                            <div class="token-balance"><span>Available balance</span><strong>{{ $student->token->balance }} tokens</strong></div>
                        @endif
                    </section>
                @endif
                <section class="sidebar-card guide-card">
                    <p class="sidebar-label">Before you begin</p>
                    <div class="guide-mark" aria-hidden="true">&#10003;</div>
                    <h2 class="guide-title">Take your time.</h2>
                    <p class="guide-copy">Read every option carefully. You can move between questions and change your answer before submitting.</p>
                </section>
                <div class="security-note"><span aria-hidden="true">&#9679;</span><span>Keep this page open while you complete your assessment.</span></div>
            </aside>
        </div>
    </main>
</div>
<script>
const questions = @json($questions->values());
let current = 0;
let answers = {};
const total = questions.length;

// Render a single question
function renderQuestion(idx) {
    if (!total) {
        document.getElementById('questionArea').innerHTML = '<div class="empty-options">No questions are available for this exam. Please contact your instructor.</div>';
        document.getElementById('questionLabel').textContent = 'No questions';
        document.getElementById('questionCount').textContent = '00 / 00';
        document.getElementById('questionProgress').style.width = '0%';
        document.getElementById('examNavigation').style.display = 'none';
        return;
    }

    const q = questions[idx];
    
    let html = `<div class="mb-4">
        <h2 class="question-prompt"><strong>Q${String(idx+1).padStart(2, '0')}</strong> &nbsp; ${q.text || 'No question text available'}
            <span class="marks-badge">${q.marks ?? 1} ${q.marks == 1 ? 'mark' : 'marks'}</span>
        </h2>
        <div class="answer-list">`;
    const options = [q.option_a, q.option_b, q.option_c, q.option_d].filter(Boolean);
    if (options.length) {
        options.forEach((value, optIdx) => {
            const checked = answers[q.id] === value ? 'checked' : '';
            const selected = answers[q.id] === value ? 'selected' : '';
            html += `<label class="answer-option ${selected}" for="q${q.id}_opt${optIdx}">
                <input class="answer-radio" type="radio" name="answer" id="q${q.id}_opt${optIdx}" value="${value}" ${checked} required>
                <span class="option-text">${value}</span>
            </label>`;
        });
    } else {
        html += `<div class="empty-options">No options are available for this question.</div>`;
    }
    html += `</div></div>`;
    document.getElementById('questionArea').innerHTML = html;
    document.getElementById('prevBtn').style.display = idx > 0 ? '' : 'none';
    document.getElementById('nextBtn').style.display = idx < total - 1 ? '' : 'none';
    document.getElementById('submitBtn').style.display = idx === total - 1 ? '' : 'none';
    const position = String(idx + 1).padStart(2, '0');
    const totalLabel = String(total).padStart(2, '0');
    document.getElementById('questionLabel').textContent = `Question ${position}`;
    document.getElementById('questionCount').textContent = `${position} / ${totalLabel}`;
    document.getElementById('questionProgress').style.width = `${((idx + 1) / total) * 100}%`;
    document.querySelector('.progress-track').setAttribute('aria-valuenow', idx + 1);
}

// Save answer on change
document.addEventListener('change', function(e) {
    if (e.target.name === 'answer') {
        answers[questions[current].id] = e.target.value;
        e.target.closest('.answer-option').classList.add('selected');
        document.querySelectorAll('.answer-option').forEach(function(option) {
            if (option !== e.target.closest('.answer-option')) option.classList.remove('selected');
        });
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
