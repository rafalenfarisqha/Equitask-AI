
@extends('layouts.app')

@section('title', 'Mengerjakan Assessment')

@section('content')
<div class="assessment-page">
    <header class="assessment-header">
        <a href="{{ route('siswa.assessment') }}" class="back-link">
            ← Kembali
        </a>

        <div class="assessment-heading">
            <div>
                <span class="eyebrow">LATIHAN SIMULASI</span>
                <h1>Assessment Siswa</h1>
                <p>Ekosistem dan Rantai Makanan</p>
            </div>

            <div class="timer-card">
                <span>Sisa waktu</span>
                <strong id="timer">25:00</strong>
            </div>
        </div>
    </header>

    <main class="assessment-main">
        <section class="progress-card">
            <div class="progress-info">
                <span id="question-counter">Soal 1 dari 10</span>
                <span id="answered-counter">0 dari 10 dijawab</span>
            </div>

            <div class="progress-track">
                <div id="progress-bar" class="progress-bar"></div>
            </div>
        </section>

        <div class="assessment-layout">
            <section class="question-card">
                <div class="question-meta">
                    <span id="question-number" class="question-badge">
                        SOAL 01
                    </span>
                    <span class="question-type">Pilihan Ganda</span>
                </div>

                <h2 id="question-text"></h2>

                <div id="options-container" class="options-container"></div>

                <div class="question-navigation">
                    <button type="button" id="previous-btn"
                        class="btn btn-secondary">
                        ← Sebelumnya
                    </button>

                    <button type="button" id="next-btn"
                        class="btn btn-primary">
                        Selanjutnya →
                    </button>
                </div>
            </section>

            <aside class="question-sidebar">
                <section class="number-card">
                    <h3>Navigasi Soal</h3>
                    <p>Pilih nomor untuk berpindah soal.</p>

                    <div id="question-numbers" class="question-numbers"></div>

                    <div class="legend">
                        <span><i class="legend-dot answered"></i> Dijawab</span>
                        <span><i class="legend-dot current"></i> Aktif</span>
                        <span><i class="legend-dot unanswered"></i> Belum</span>
                    </div>
                </section>

                <section class="submit-card">
                    <h3>Sudah selesai?</h3>
                    <p>Periksa kembali jawabanmu sebelum mengakhiri simulasi.</p>

                    <button type="button" id="submit-btn"
                        class="btn btn-submit">
                        Selesaikan Simulasi
                    </button>
                </section>
            </aside>
        </div>

        <section id="result-card" class="result-card" hidden>
            <div class="result-icon">✓</div>
            <h2>Simulasi selesai!</h2>
            <p id="result-message"></p>
            <p id="result-score" class="result-score"></p>

            <button type="button" id="review-btn" class="btn btn-primary">
                Tinjau Jawaban
            </button>

            <button type="button" id="restart-btn" class="btn btn-secondary">
                Ulangi Simulasi
            </button>
        </section>
    </main>
</div>
@endsection

@push('styles')
<style>
.assessment-page {
    --assessment-primary: #2563eb;
    --assessment-text: #172033;
    --assessment-muted: #64748b;
    --assessment-border: #e2e8f0;
    max-width: 1180px;
    margin: 0 auto;
    padding: 28px 22px 60px;
    color: var(--assessment-text);
}
.assessment-header { margin-bottom: 24px; }
.back-link {
    display: inline-block;
    margin-bottom: 24px;
    color: var(--assessment-primary);
    text-decoration: none;
    font-weight: 600;
}
.assessment-heading, .progress-info, .question-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}
.eyebrow {
    color: var(--assessment-primary);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1.3px;
}
.assessment-heading h1 {
    margin: 6px 0;
    font-size: clamp(24px, 3vw, 32px);
}
.assessment-heading p, .number-card > p, .submit-card p {
    color: var(--assessment-muted);
    margin: 0;
}
.timer-card, .progress-card, .question-card, .number-card,
.submit-card, .result-card {
    background: #fff;
    border: 1px solid var(--assessment-border);
    border-radius: 16px;
}
.timer-card {
    min-width: 110px;
    padding: 13px 18px;
    text-align: center;
}
.timer-card span {
    display: block;
    color: var(--assessment-muted);
    font-size: 12px;
}
.timer-card strong {
    display: block;
    margin-top: 4px;
    font-size: 23px;
    font-variant-numeric: tabular-nums;
}
.progress-card { padding: 18px; margin-bottom: 20px; }
.progress-info { font-size: 13px; font-weight: 700; }
.progress-track {
    height: 8px;
    background: #e9eef7;
    border-radius: 99px;
    overflow: hidden;
    margin-top: 13px;
}
.progress-bar {
    width: 0;
    height: 100%;
    background: var(--assessment-primary);
    transition: width .2s ease;
}
.assessment-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 290px;
    gap: 20px;
    align-items: start;
}
.question-card { padding: clamp(20px, 4vw, 32px); }
.question-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 24px;
}
.question-badge {
    background: #eff6ff;
    color: var(--assessment-primary);
    border-radius: 7px;
    padding: 7px 10px;
    font-size: 12px;
    font-weight: 800;
}
.question-type { color: var(--assessment-muted); font-size: 13px; }
.question-card h2 {
    font-size: 21px;
    line-height: 1.6;
    margin: 0 0 24px;
}
.options-container { display: grid; gap: 12px; }
.option-item {
    display: flex;
    align-items: flex-start;
    gap: 13px;
    padding: 15px;
    border: 1px solid var(--assessment-border);
    border-radius: 12px;
    cursor: pointer;
    transition: background .15s, border-color .15s;
}
.option-item:hover { border-color: #93b4fb; background: #f8faff; }
.option-item.selected {
    border-color: var(--assessment-primary);
    background: #eff6ff;
}
.option-item input { margin-top: 5px; accent-color: var(--assessment-primary); }
.option-letter {
    display: inline-grid;
    place-items: center;
    width: 29px;
    height: 29px;
    flex-shrink: 0;
    border: 1px solid var(--assessment-border);
    border-radius: 8px;
    font-weight: 700;
    font-size: 13px;
}
.option-item.selected .option-letter {
    background: var(--assessment-primary);
    color: #fff;
    border-color: var(--assessment-primary);
}
.option-text { line-height: 1.5; padding-top: 4px; }
.question-navigation { margin-top: 28px; }
.btn {
    border: 0;
    border-radius: 10px;
    padding: 12px 16px;
    cursor: pointer;
    font: inherit;
    font-size: 14px;
    font-weight: 700;
}
.btn:disabled { opacity: .45; cursor: not-allowed; }
.btn-primary { background: var(--assessment-primary); color: #fff; }
.btn-secondary { background: #f1f5f9; color: #334155; }
.question-sidebar { display: grid; gap: 16px; }
.number-card, .submit-card { padding: 20px; }
.number-card h3, .submit-card h3 { margin: 0 0 8px; font-size: 17px; }
.number-card > p, .submit-card p { font-size: 13px; line-height: 1.6; }
.question-numbers {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 8px;
    margin: 18px 0;
}
.question-number {
    min-width: 0;
    aspect-ratio: 1;
    border: 1px solid var(--assessment-border);
    border-radius: 8px;
    background: #fff;
    color: #475569;
    font-weight: 700;
    cursor: pointer;
}
.question-number.answered { background: #dcfce7; border-color: #86efac; color: #166534; }
.question-number.current { background: var(--assessment-primary); border-color: var(--assessment-primary); color: #fff; }
.legend { display: grid; gap: 9px; color: var(--assessment-muted); font-size: 12px; }
.legend span { display: flex; align-items: center; gap: 8px; }
.legend-dot { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }
.legend-dot.answered { background: #86efac; }
.legend-dot.current { background: var(--assessment-primary); }
.legend-dot.unanswered { background: #e2e8f0; }
.btn-submit { width: 100%; margin-top: 16px; background: #0f766e; color: #fff; }
.result-card { margin-top: 22px; padding: 28px; text-align: center; }
.result-icon {
    display: grid; place-items: center; margin: 0 auto 14px;
    width: 48px; height: 48px; border-radius: 50%;
    background: #dcfce7; color: #166534; font-size: 24px;
}
.result-card h2 { margin: 8px 0; }
.result-card p { color: var(--assessment-muted); }
.result-card .btn { margin: 5px; }
.result-score { font-size: 28px; font-weight: 800; color: var(--assessment-text) !important; }
@media (max-width: 820px) {
    .assessment-layout { grid-template-columns: 1fr; }
    .question-sidebar { grid-template-columns: 1fr; }
}
@media (max-width: 520px) {
    .assessment-page { padding: 18px 12px 40px; }
    .assessment-heading { align-items: flex-start; }
    .timer-card { min-width: 85px; padding: 10px; }
    .question-navigation { align-items: stretch; }
    .question-navigation .btn { flex: 1; padding: 12px 8px; }
    .progress-info { align-items: flex-start; }
}
</style>
@endpush

<!-- @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // DATA SIMULASI: nanti diganti dengan data dari database.
    const questions = [
        {
            text: 'Dalam rantai makanan, tumbuhan hijau berperan sebagai apa?',
            options: ['Konsumen primer', 'Produsen', 'Konsumen sekunder', 'Pengurai'],
            answer: 1
        },
        {
            text: 'Perhatikan rantai makanan: rumput → belalang → katak → ular. Organisme yang menjadi konsumen primer adalah ...',
            options: ['Rumput', 'Belalang', 'Katak', 'Ular'],
            answer: 1
        },
        {
            text: 'Apa sumber energi utama bagi sebagian besar ekosistem?',
            options: ['Tanah', 'Angin', 'Matahari', 'Air hujan'],
            answer: 2
        },
        {
            text: 'Organisme yang menguraikan sisa makhluk hidup disebut ...',
            options: ['Produsen', 'Predator', 'Herbivor', 'Pengurai'],
            answer: 3
        },
        {
            text: 'Jika populasi belalang menurun drastis, organisme yang mungkin kekurangan makanan secara langsung adalah ...',
            options: ['Rumput', 'Katak', 'Matahari', 'Jamur'],
            answer: 1
        },
        {
            text: 'Hewan pemakan tumbuhan disebut ...',
            options: ['Herbivor', 'Karnivor', 'Omnivor', 'Dekomposer'],
            answer: 0
        },
        {
            text: 'Pada rantai makanan padi → tikus → ular, ular berperan sebagai ...',
            options: ['Produsen', 'Konsumen primer', 'Konsumen sekunder', 'Pengurai'],
            answer: 2
        },
        {
            text: 'Apa yang mungkin terjadi jika predator utama hilang dari suatu ekosistem?',
            options: [
                'Semua populasi pasti tetap sama',
                'Populasi mangsa dapat meningkat',
                'Produsen langsung punah',
                'Energi matahari berhenti'
            ],
            answer: 1
        },
        {
            text: 'Manakah contoh hubungan makan dan dimakan yang benar?',
            options: [
                'Katak → rumput → belalang',
                'Ular → padi → tikus',
                'Rumput → belalang → katak',
                'Belalang → matahari → rumput'
            ],
            answer: 2
        },
        {
            text: 'Mengapa keseimbangan rantai makanan penting bagi ekosistem?',
            options: [
                'Agar hanya satu spesies yang hidup',
                'Agar aliran energi dan populasi tetap seimbang',
                'Agar semua hewan menjadi herbivor',
                'Agar pengurai tidak diperlukan'
            ],
            answer: 1
        }
    ];

    let currentQuestion = 0;
    let remainingSeconds = 25 * 60;
    let finished = false;
    let reviewMode = false;
    let timerInterval = null;
    let answers = Array(questions.length).fill(null);

    const questionText = document.getElementById('question-text');
    const questionNumber = document.getElementById('question-number');
    const questionCounter = document.getElementById('question-counter');
    const answeredCounter = document.getElementById('answered-counter');
    const progressBar = document.getElementById('progress-bar');
    const optionsContainer = document.getElementById('options-container');
    const numbersContainer = document.getElementById('question-numbers');
    const previousBtn = document.getElementById('previous-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    const timerElement = document.getElementById('timer');
    const resultCard = document.getElementById('result-card');
    const resultMessage = document.getElementById('result-message');
    const resultScore = document.getElementById('result-score');

    function renderQuestionNumbers() {
        numbersContainer.innerHTML = '';

        questions.forEach(function (_, index) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'question-number';
            button.textContent = index + 1;

            if (answers[index] !== null) button.classList.add('answered');
            if (index === currentQuestion) button.classList.add('current');

            button.setAttribute('aria-label', 'Buka soal ' + (index + 1));

            button.addEventListener('click', function () {
                currentQuestion = index;
                renderQuestion();
            });

            numbersContainer.appendChild(button);
        });
    }

    function renderQuestion() {
        const question = questions[currentQuestion];

        questionText.textContent = question.text;
        questionNumber.textContent =
            'SOAL ' + String(currentQuestion + 1).padStart(2, '0');
        questionCounter.textContent =
            'Soal ' + (currentQuestion + 1) + ' dari ' + questions.length;

        const answered = answers.filter(function (answer) {
            return answer !== null;
        }).length;

        answeredCounter.textContent =
            answered + ' dari ' + questions.length + ' dijawab';

        progressBar.style.width =
            ((currentQuestion + 1) / questions.length * 100) + '%';

        optionsContainer.innerHTML = '';

        question.options.forEach(function (option, index) {
            const label = document.createElement('label');
            label.className = 'option-item';

            if (answers[currentQuestion] === index) {
                label.classList.add('selected');
            }

            const input = document.createElement('input');
            input.type = 'radio';
            input.name = 'answer';
            input.value = index;
            input.checked = answers[currentQuestion] === index;
            input.disabled = finished || reviewMode;

            const letter = document.createElement('span');
            letter.className = 'option-letter';
            letter.textContent = String.fromCharCode(65 + index);

            const text = document.createElement('span');
            text.className = 'option-text';
            text.textContent = option;

            label.append(input, letter, text);

            input.addEventListener('change', function () {
                if (finished || reviewMode) return;

                answers[currentQuestion] = index;
                renderQuestion();
            });

            optionsContainer.appendChild(label);
        });

        previousBtn.disabled = currentQuestion === 0;
        nextBtn.disabled = currentQuestion === questions.length - 1 ||
            finished || reviewMode;
        previousBtn.disabled = currentQuestion === 0 || reviewMode;

        renderQuestionNumbers();
    }

    function updateTimer() {
        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = remainingSeconds % 60;

        timerElement.textContent =
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');

        if (remainingSeconds <= 60) {
            timerElement.style.color = '#dc2626';
        }
    }

    function finishAssessment(isTimeout) {
        if (finished) return;

        const unanswered = answers.filter(function (answer) {
            return answer === null;
        }).length;

        if (!isTimeout && unanswered > 0) {
            const proceed = confirm(
                'Masih ada ' + unanswered +
                ' soal yang belum dijawab. Tetap kumpulkan simulasi?'
            );

            if (!proceed) return;
        } else if (!isTimeout) {
            if (!confirm('Yakin ingin menyelesaikan simulasi?')) return;
        }

        finished = true;
        clearInterval(timerInterval);

        const correctCount = answers.filter(function (answer, index) {
            return answer === questions[index].answer;
        }).length;

        const score = Math.round(correctCount / questions.length * 100);

        resultMessage.textContent = isTimeout
            ? 'Waktu habis. Simulasi telah diselesaikan.'
            : 'Kamu telah menyelesaikan latihan simulasi ini.';

        resultScore.textContent =
            correctCount + ' dari ' + questions.length +
            ' benar — skor simulasi ' + score + '/100';

        resultCard.hidden = false;
        submitBtn.disabled = true;
        nextBtn.disabled = true;

        renderQuestionNumbers();
        renderQuestion();

        resultCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    previousBtn.addEventListener('click', function () {
        if (currentQuestion > 0 && !reviewMode) {
            currentQuestion--;
            renderQuestion();
        }
    });

    nextBtn.addEventListener('click', function () {
        if (currentQuestion < questions.length - 1 && !finished) {
            currentQuestion++;
            renderQuestion();
        }
    });

    submitBtn.addEventListener('click', function () {
        finishAssessment(false);
    });

    document.getElementById('review-btn').addEventListener('click', function () {
        reviewMode = true;
        resultCard.hidden = true;
        submitBtn.disabled = true;
        currentQuestion = 0;
        renderQuestion();
        document.querySelector('.question-card').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    });

    document.getElementById('restart-btn').addEventListener('click', function () {
        if (!confirm('Ulangi simulasi dari awal?')) return;

        answers = Array(questions.length).fill(null);
        currentQuestion = 0;
        remainingSeconds = 25 * 60;
        finished = false;
        reviewMode = false;

        resultCard.hidden = true;
        submitBtn.disabled = false;
        timerElement.style.color = '';
        updateTimer();
        renderQuestion();

        clearInterval(timerInterval);
        timerInterval = setInterval(tick, 1000);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    function tick() {
        if (finished) return;

        remainingSeconds--;
        updateTimer();

        if (remainingSeconds <= 0) {
            remainingSeconds = 0;
            updateTimer();
            finishAssessment(true);
        }
    }

    updateTimer();
    renderQuestion();

    timerInterval = setInterval(tick, 1000);
});
</script>
@endpush -->
//end simulasi



@extends('layouts.app')

@section('title', 'Pengerjaan Assessment — EquiTask AI')

@push('styles')
<style>
    .qcard {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(246, 250, 255, 0.96) 100%);
        border-radius: var(--radius); padding: 20px;
        box-shadow: 0 18px 32px -22px rgba(15, 23, 42, 0.26);
        border: 1px solid rgba(255, 255, 255, 0.72);
        margin-bottom: 16px;
    }
    .qcard.adaptif { padding: 24px; border-radius: 24px; }
    .qtext {
        font-weight: 700; font-size: 14.5px; line-height: 1.55; color: var(--text);
    }
    .illust-box {
        background: linear-gradient(135deg, var(--secondary-soft), var(--primary-soft));
        border-radius: 16px; padding: 12px; display: flex; align-items: center; justify-content: center;
        margin-bottom: 14px; min-height: 180px;
    }
    .option {
        display: flex; align-items: center; gap: 12px; padding: 14px;
        border: 1.5px solid rgba(148, 163, 184, 0.2); border-radius: 14px;
        margin-top: 10px; cursor: pointer; font-size: 13.5px; font-weight: 600;
        background: rgba(255, 255, 255, 0.84); transition: all 0.2s ease;
    }
    .option.sel {
        border-color: rgba(37, 99, 235, 0.35);
        background: linear-gradient(135deg, var(--primary-soft) 0%, #dfeeff 100%);
    }
    .optkey {
        width: 26px; height: 26px; border-radius: 8px; background: var(--border);
        display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; flex-shrink: 0;
    }
    .option.sel .optkey { background: var(--primary); color: #fff; }
    .progress-bar { height: 8px; background: var(--border); border-radius: 99px; overflow: hidden; flex: 1; }
    .progress-bar > div { height: 100%; background: var(--secondary); border-radius: 99px; transition: width 0.3s; }
</style>
@endpush

@section('content')
/* Topbar Ujian dengan Timer */
<div class="topbar">
    <a href="{{ route('siswa.dashboard') }}" class="iconbtn" onclick="return confirm('Keluar dari assessment?')">
        <span class="material-symbols-outlined">close</span>
    </a>
    <div style="flex:1; padding: 0 12px;">
        <div class="progress-bar"><div style="width: 40%;"></div></div>
    </div>
    <div class="row" style="gap:4px; font-weight:800; font-size:13px; color:var(--primary);">
        <span class="material-symbols-outlined" style="font-size:17px;">timer</span>24:12
    </div>
</div>

<div class="px" style="padding-top: 14px;">

    /* Informasi Nomor Soal & Tombol Bookmark */
    <div class="row" style="justify-content:space-between; margin-bottom:10px;">
        <span class="muted">Soal 2 dari 10</span>
        <button class="iconbtn" style="width:32px;height:32px;">
            <span class="material-symbols-outlined" style="font-size:18px; color:var(--muted);">bookmark_border</span>
        </button>
    </div>

    /* Kartu Soal (Mode Adaptif / Ilustrasi) */
    <div class="qcard adaptif">
        <div class="illust-box">
            /* Ilustrasi / Gambar Pendukung Soal Adaptif */
            <span class="material-symbols-outlined" style="font-size: 64px; color: var(--primary);">eco</span>
        </div>

        <div class="qtext">
            Di sebuah padang rumput, penggunaan pestisida menyebabkan populasi belalang menurun secara drastis. Perubahan tersebut memengaruhi makhluk hidup lain. Apa yang terjadi pada katak?
        </div>

        <div style="margin-top: 16px;">
            /* Opsi Jawaban A */
            <div class="option sel" onclick="selectOption(this)">
                <div class="optkey">A</div>
                <div>Populasi katak menurun karena sumber makanannya berkurang.</div>
            </div>
            /* Opsi Jawaban B */
            <div class="option" onclick="selectOption(this)">
                <div class="optkey">B</div>
                <div>Populasi ular meningkat karena lebih mudah mendapatkan makanan.</div>
            </div>
            /* Opsi Jawaban C */
            <div class="option" onclick="selectOption(this)">
                <div class="optkey">C</div>
                <div>Populasi rumput menurun karena tidak dimakan belalang.</div>
            </div>
        </div>
    </div>

    <div style="height: 80px;"></div>
</div>

/* Tombol Navigasi Bawah Ujian */
<div style="position: fixed; bottom: 0; left: 0; right: 0; background: rgba(255,255,255,0.9); padding: 14px 20px; border-top: 1px solid var(--border); display: flex; gap: 10px; backdrop-filter: blur(10px);">
    <button class="btn btn-outline" style="flex:1;">Sebelumnya</button>
    <button class="btn btn-primary" style="flex:1;">Selanjutnya</button>
</div>
@endsection

@push('scripts')
<script>
    function selectOption(element) {
        // Menghapus kelas 'sel' dari semua opsi di soal ini
        const options = element.parentElement.querySelectorAll('.option');
        options.forEach(opt => opt.classList.remove('sel'));

        // Menambahkan kelas 'sel' ke opsi yang diklik
        element.classList.add('sel');
    }
</script>
@endpush