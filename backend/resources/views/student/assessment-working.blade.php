
@extends('layouts.app')

@section('title', 'Pengerjaan Assessment')

@section('content')

<link rel="stylesheet" href="{{ asset('css/easy-read.css') }}">

<style>
    /* ========================================
       HALAMAN PENGERJAAN ASESMEN SISWA
       ======================================== */

    .aw-page {
        --aw-blue: #2563eb;
        --aw-teal: #0d9488;
        --aw-ink: #182337;
        --aw-muted: #718096;

        max-width: 900px;
        margin: 0 auto;
        padding: 24px 18px 45px;
        color: var(--aw-ink);
    }

    .aw-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .aw-back {
        text-decoration: none;
        color: var(--aw-muted);
        font-size: 14px;
    }

    .aw-tag {
        padding: 8px 12px;
        background: #eff6ff;
        color: var(--aw-blue);
        border-radius: 99px;
        font-size: 12px;
        font-weight: 700;
    }

    .aw-heading {
        margin-bottom: 24px;
    }

    .aw-heading h1 {
        font-size: clamp(23px, 4vw, 32px);
        font-weight: 800;
        margin: 12px 0 7px;
    }

    .aw-muted {
        color: var(--aw-muted);
    }

    .aw-progress {
        height: 8px;
        background: #e8eef6;
        border-radius: 99px;
        overflow: hidden;
        margin-top: 18px;
    }

    .aw-progress-bar {
        height: 100%;
        width: 0;
        background: linear-gradient(
            90deg,
            var(--aw-blue),
            var(--aw-teal)
        );
        transition: width .2s ease;
    }

    .aw-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 210px;
        gap: 20px;
        align-items: start;
    }

    .aw-card,
    .aw-sidebar {
        background: white;
        border: 1px solid #e6edf5;
        border-radius: 22px;
        padding: 24px;
        box-shadow: 0 8px 30px rgba(25, 50, 90, .04);
    }

    .aw-number {
        font-size: 13px;
        color: var(--aw-blue);
        font-weight: 800;
        margin-bottom: 18px;
    }

    .aw-question {
        font-size: 19px;
        font-weight: 650;
        line-height: 1.8;
        margin-bottom: 24px;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    /* ========================================
       PILIHAN JAWABAN REGULER
       ======================================== */

    .aw-options {
        display: grid;
        gap: 12px;
    }

    .aw-option {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 12px;
        min-height: 68px;
        padding: 14px 16px;

        border: 1.5px solid #e0e7f0;
        border-radius: 15px;
        background: white;
        cursor: pointer;
        transition: .15s ease;
    }

    .aw-option:hover {
        border-color: #93b4fa;
        background: #f8fbff;
    }

    .aw-option.selected {
        border-color: var(--aw-blue);
        background: #eff6ff;
    }

    .aw-option input[type="radio"] {
        flex-shrink: 0;
        width: 17px;
        height: 17px;
        margin: 0;
        accent-color: var(--aw-blue);
        cursor: pointer;
    }

    .aw-letter {
        display: grid;
        place-items: center;

        flex: 0 0 34px;
        width: 34px;
        height: 34px;

        border-radius: 9px;
        background: #edf2f8;
        color: #2458cb;

        font-size: 14px;
        font-weight: 800;
        line-height: 1;
    }

    .aw-option-text {
        display: block;
        flex: 1;
        min-width: 0;
        margin: 0;
        padding: 0;

        font-size: 16px;
        font-weight: 500;
        line-height: 1.6;
        white-space: normal;
        overflow-wrap: break-word;
    }

    /* ========================================
       EASY READ KHUSUS VERSI ADAPTIF
       ======================================== */

    .aw-page.easy-read-assessment .aw-question {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 21px;
        font-weight: 600;
        line-height: 1.75;
        letter-spacing: .2px;
        word-spacing: 1px;
        white-space: pre-wrap;
        overflow-wrap: break-word;
        max-width: 65ch;
    }

    .aw-page.easy-read-assessment .aw-options {
        gap: 14px;
    }

    .aw-page.easy-read-assessment .aw-option {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-items: center;
        gap: 14px;
        min-height: 74px;
        padding: 16px 18px;
        border-radius: 16px;
        background: #ffffff;
    }

    .aw-page.easy-read-assessment .aw-option.selected {
        background: #eff6ff;
    }

    .aw-page.easy-read-assessment .aw-letter {
        display: grid;
        place-items: center;

        flex: 0 0 40px;
        width: 40px;
        height: 40px;

        border-radius: 11px;
        background: #e5edff;
        color: #2458cb;

        font-size: 17px;
        font-weight: 700;
        line-height: 1;
    }

    .aw-page.easy-read-assessment .aw-option-text {
        display: block;
        flex: 1;
        min-width: 0;
        margin: 0;
        padding: 0;

        font-family: Arial, Helvetica, sans-serif;
        font-size: 18px;
        font-weight: 600;
        line-height: 1.65;
        letter-spacing: .2px;
        word-spacing: .5px;

        color: #172238;
        white-space: normal;
        overflow-wrap: break-word;
    }

    /* ========================================
       NAVIGASI SOAL
       ======================================== */

    .aw-nav {
        display: flex;
        gap: 10px;
        margin-top: 25px;
    }

    .aw-btn {
        padding: 12px 17px;
        border-radius: 12px;
        border: 1px solid #dce5f0;
        background: white;
        color: var(--aw-ink);
        font-weight: 700;
        cursor: pointer;
    }

    .aw-btn.primary {
        background: var(--aw-blue);
        color: white;
        border-color: var(--aw-blue);
    }

    .aw-btn:disabled {
        opacity: .4;
        cursor: not-allowed;
    }

    .aw-nav .aw-btn {
        flex: 1;
    }

    .aw-sidebar h3 {
        font-size: 15px;
        margin: 0 0 8px;
    }

    .aw-numbers {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
        margin: 18px 0;
    }

    .aw-numbtn {
        aspect-ratio: 1;
        border: 1px solid #dfe7f0;
        border-radius: 10px;
        background: white;
        font-weight: 700;
        cursor: pointer;
    }

    .aw-numbtn.current {
        background: var(--aw-blue);
        border-color: var(--aw-blue);
        color: white;
    }

    .aw-numbtn.answered {
        background: #ccfbf1;
        color: #0f766e;
        border-color: #99f6e4;
    }

    .aw-numbtn.current.answered {
        background: var(--aw-blue);
        color: white;
    }

    /* ========================================
       TOMBOL AKSESIBILITAS
       ======================================== */

    .aw-tools {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 18px;
    }

    .aw-tool {
        border: 1px solid #dfe7f0;
        background: white;
        padding: 9px 12px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 700;
    }

    .aw-tool.active {
        background: #e0f2fe;
        border-color: #7dd3fc;
    }

    .aw-submit {
        width: 100%;
        margin-top: 16px;
        padding: 13px;
        border: 0;
        border-radius: 12px;
        background: var(--aw-teal);
        color: white;
        font-weight: 800;
        cursor: pointer;
    }

    .aw-submit:disabled {
        opacity: .65;
        cursor: not-allowed;
    }

    /* Tombol A+ untuk semua versi */

    .aw-page.large-text .aw-question {
        font-size: 23px;
    }

    .aw-page.large-text .aw-option-text {
        font-size: 20px;
    }

    /* Tombol A+ pada Adaptif */

    .aw-page.easy-read-assessment.large-text .aw-question {
        font-size: 25px;
    }

    .aw-page.easy-read-assessment.large-text .aw-option-text {
        font-size: 22px;
    }

    /* Mode kontras */

    .aw-page.high-contrast {
        --aw-ink: #000000;
        --aw-muted: #303030;
    }

    .aw-page.high-contrast .aw-card,
    .aw-page.high-contrast .aw-sidebar,
    .aw-page.high-contrast .aw-option {
        border-color: #333333;
    }

    .aw-page.high-contrast .aw-question,
    .aw-page.high-contrast .aw-option-text {
        color: #000000;
    }

    /* ========================================
       RESPONSIF HP
       ======================================== */

    @media (max-width: 700px) {
        .aw-page {
            padding: 17px 12px 30px;
        }

        .aw-layout {
            grid-template-columns: 1fr;
        }

        .aw-sidebar {
            order: -1;
            padding: 17px;
        }

        .aw-numbers {
            grid-template-columns: repeat(
                5,
                minmax(0, 1fr)
            );
        }

        .aw-card {
            padding: 19px;
        }

        .aw-question {
            font-size: 17px;
        }

        .aw-page.easy-read-assessment .aw-question {
            font-size: 19px;
        }

        .aw-page.easy-read-assessment .aw-option {
            min-height: 64px;
            padding: 13px;
            gap: 11px;
        }

        .aw-page.easy-read-assessment .aw-option-text {
            font-size: 17px;
        }

        .aw-page.easy-read-assessment.large-text .aw-question {
            font-size: 23px;
        }

        .aw-page.easy-read-assessment.large-text .aw-option-text {
            font-size: 21px;
        }
    }
</style>

<div
    class="aw-page {{ $version === 'adaptif' ? 'easy-read-assessment' : '' }}"
    id="assessmentApp">

    {{-- HEADER HALAMAN --}}

    <div class="aw-top">

        <a href="{{ route('siswa.assessment.instructions', $assessment->id) }}"
           class="aw-back">
            ← Petunjuk asesmen
        </a>

        <span class="aw-tag">
            {{ $version === 'adaptif'
                ? '✦ Versi Adaptif'
                : '● Versi Reguler' }}
        </span>

    </div>

    <div class="aw-heading">

        <p class="aw-muted" style="font-size:13px;margin:0">
            {{ $assessment->mapel }} · {{ $assessment->nama }}
        </p>

        <h1>Fokus pada pemahamanmu.</h1>

        <p class="aw-muted" style="font-size:14px">
            Kerjakan dengan tenang dan periksa kembali jawabanmu.
        </p>

        <div style="display:flex;justify-content:space-between;gap:10px;font-size:13px">

            <span id="progressText">
                Soal 1 dari {{ $questions->count() }}
            </span>

            <span
                id="elapsedTime"
                style="font-weight:800;color:#2563eb">
                00:00
            </span>

        </div>

        <div class="aw-progress">
            <div class="aw-progress-bar" id="progressBar"></div>
        </div>

    </div>

    {{-- FORM JAWABAN SISWA --}}

    <form
        id="assessmentForm"
        method="POST"
        action="{{ route('siswa.assessment.submit', [
            'id' => $assessment->id,
        ]) }}">

        @csrf

        <input
            type="hidden"
            name="version_type"
            value="{{ $version }}">

        <div class="aw-layout">

            <main class="aw-card">

                {{-- =====================================
                     LOOP SOAL SISWA
                     HANYA SATU LOOP UTAMA
                     ===================================== --}}

                @foreach($questions as $index => $question)

                    @php
                        /*
                         * Options dapat berupa:
                         * - array PHP
                         * - string JSON
                         */

                        $options = is_array($question->options)
                            ? $question->options
                            : json_decode(
                                $question->options ?? '[]',
                                true
                            );

                        $options = is_array($options)
                            ? $options
                            : [];
                    @endphp

                    <section
                        class="aw-question-panel"
                        data-index="{{ $index }}"
                        style="{{ $index === 0 ? '' : 'display:none' }}">

                        <div class="aw-number">
                            PERTANYAAN
                            {{ str_pad(
                                $index + 1,
                                2,
                                '0',
                                STR_PAD_LEFT
                            ) }}
                        </div>

                        {{-- PERTANYAAN REGULER / ADAPTIF --}}

                        <div class="aw-question {{ $version === 'adaptif' ? 'easy-read-question' : '' }}">{{ $question->question_text }}</div>

                        {{-- PILIHAN JAWABAN --}}

                        <div class="aw-options {{ $version === 'adaptif' ? 'easy-read-options' : '' }}">

                            {{-- =====================================
                                 LOOP PILIHAN JAWABAN
                                 HANYA SATU LOOP
                                 ===================================== --}}

                            @foreach($options as $key => $option)

                                @php
                                    /*
                                     * Data dari Dify biasanya:
                                     *
                                     * {
                                     *   "label": "A",
                                     *   "teks": "Kerongkongan"
                                     * }
                                     *
                                     * Tetapi tetap dukung
                                     * pilihan jawaban string biasa.
                                     */

                                    $optionData = is_array($option)
                                        ? $option
                                        : [];

                                    // Label yang tampil: A, B, C, D.
                                    $optionLabel =
                                        $optionData['label']
                                        ?? (
                                            is_numeric($key)
                                                ? chr(65 + (int) $key)
                                                : (string) $key
                                        );

                                    // Ambil teks jawaban saja.
                                    $optionText =
                                        $optionData['teks']
                                        ?? $optionData['text']
                                        ?? (
                                            is_scalar($option)
                                                ? (string) $option
                                                : ''
                                        );

                                    /*
                                     * Nilai input tetap menggunakan
                                     * key asli seperti kode sebelumnya.
                                     */
                                    $optionValue = $key;

                                    $isChecked =
                                        (string) (
                                            $savedAnswers[$question->id]
                                            ?? ''
                                        ) === (string) $optionValue;
                                @endphp

                                <label class="aw-option">

                                    <input
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="{{ $optionValue }}"
                                        {{ $isChecked ? 'checked' : '' }}>

                                    <span class="aw-letter">{{ $optionLabel }}</span>

                                    <span class="aw-option-text">{{ $optionText }}</span>

                                </label>

                            @endforeach

                        </div>

                    </section>

                @endforeach

                {{-- =====================================
                     AKHIR LOOP SOAL
                     ===================================== --}}

                <div class="aw-nav">

                    <button
                        type="button"
                        class="aw-btn"
                        id="previousBtn">
                        ← Sebelumnya
                    </button>

                    <button
                        type="button"
                        class="aw-btn primary"
                        id="nextBtn">
                        Selanjutnya →
                    </button>

                </div>

            </main>

            {{-- =====================================
                 SIDEBAR NAVIGASI SISWA
                 ===================================== --}}

            <aside class="aw-sidebar">

                <h3>Navigasi soal</h3>

                <p
                    class="aw-muted"
                    style="font-size:12px;line-height:1.6">
                    Pilih nomor untuk berpindah soal.
                </p>

                <div
                    class="aw-numbers"
                    id="questionNumbers">

                    @foreach($questions as $index => $question)

                        <button
                            type="button"
                            class="aw-numbtn {{ $index === 0 ? 'current' : '' }}"
                            data-go="{{ $index }}">

                            {{ $index + 1 }}

                        </button>

                    @endforeach

                </div>

                <div
                    class="aw-muted"
                    style="font-size:12px">

                    <span style="color:#0d9488">●</span>
                    Sudah dijawab

                    <br>

                    <span style="color:#2563eb">●</span>
                    Soal aktif

                </div>

                {{-- FITUR AKSESIBILITAS --}}

                <div class="aw-tools">

                    <button
                        type="button"
                        class="aw-tool"
                        id="largeTextBtn">
                        A+ Teks besar
                    </button>

                    <button
                        type="button"
                        class="aw-tool"
                        id="contrastBtn">
                        ◐ Kontras
                    </button>

                    <button
                        type="button"
                        class="aw-tool"
                        id="focusBtn">
                        ◎ Mode fokus
                    </button>

                    <button
                        type="button"
                        class="aw-tool"
                        id="readBtn">
                        🔊 Bacakan
                    </button>

                </div>

                {{-- SUBMIT JAWABAN --}}

                <button
                    type="button"
                    class="aw-submit"
                    id="submitBtn">
                    Kumpulkan jawaban
                </button>

            </aside>

        </div>

    </form>

</div>

{{-- ========================================
     JAVASCRIPT PENGERJAAN SISWA
     ======================================== --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const app = document.getElementById('assessmentApp');
    const form = document.getElementById('assessmentForm');

    const panels = [
        ...document.querySelectorAll('.aw-question-panel')
    ];

    const numberButtons = [
        ...document.querySelectorAll('.aw-numbtn')
    ];

    const previousBtn =
        document.getElementById('previousBtn');

    const nextBtn =
        document.getElementById('nextBtn');

    const progressText =
        document.getElementById('progressText');

    const progressBar =
        document.getElementById('progressBar');

    const total = panels.length;

    let current = 0;
    let focusMode = false;

    /*
     * Perbarui warna nomor soal terjawab.
     */
    function updateAnswered() {

        panels.forEach((panel, index) => {

            const answered = panel.querySelector(
                'input[type="radio"]:checked'
            );

            if (numberButtons[index]) {

                numberButtons[index].classList.toggle(
                    'answered',
                    !!answered
                );

            }
        });
    }

    /*
     * Tampilkan soal sesuai nomor.
     */
    function showQuestion(index) {

        if (index < 0 || index >= total) {
            return;
        }

        current = index;

        panels.forEach((panel, i) => {

            panel.style.display =
                i === current ? '' : 'none';

        });

        numberButtons.forEach((button, i) => {

            button.classList.toggle(
                'current',
                i === current
            );

        });

        previousBtn.disabled = current === 0;

        nextBtn.textContent =
            current === total - 1
                ? 'Soal terakhir ✓'
                : 'Selanjutnya →';

        progressText.textContent =
            `Soal ${current + 1} dari ${total}`;

        progressBar.style.width =
            `${((current + 1) / total) * 100}%`;

        updateAnswered();
    }

    /*
     * Klik nomor soal di sidebar.
     */
    numberButtons.forEach(button => {

        button.addEventListener('click', () => {

            showQuestion(
                Number(button.dataset.go)
            );

        });

    });

    /*
     * Navigasi sebelumnya.
     */
    previousBtn.addEventListener('click', () => {

        showQuestion(current - 1);

    });

    /*
     * Navigasi selanjutnya.
     */
    nextBtn.addEventListener('click', () => {

        if (current < total - 1) {

            showQuestion(current + 1);

        }

    });

    /*
     * Saat siswa memilih jawaban.
     */
    form.querySelectorAll(
        'input[type="radio"]'
    ).forEach(input => {

        input.addEventListener('change', () => {

            const panel = input.closest(
                '.aw-question-panel'
            );

            panel.querySelectorAll(
                '.aw-option'
            ).forEach(option => {

                option.classList.toggle(
                    'selected',
                    option.querySelector('input').checked
                );

            });

            updateAnswered();

        });

    });

    /*
     * Tombol A+.
     */
    document.getElementById('largeTextBtn')
        .addEventListener('click', function () {

            app.classList.toggle('large-text');
            this.classList.toggle('active');

        });

    /*
     * Tombol kontras.
     */
    document.getElementById('contrastBtn')
        .addEventListener('click', function () {

            app.classList.toggle('high-contrast');
            this.classList.toggle('active');

        });

    /*
     * Mode fokus.
     */
    document.getElementById('focusBtn')
        .addEventListener('click', function () {

            focusMode = !focusMode;

            app.querySelector('.aw-sidebar').style.display =
                focusMode ? 'none' : '';

            this.classList.toggle('active');

        });

    /*
     * Pembaca soal.
     */
    document.getElementById('readBtn')
        .addEventListener('click', function () {

            if (!('speechSynthesis' in window)) {

                alert(
                    'Fitur pembaca teks tidak didukung browser ini.'
                );

                return;
            }

            window.speechSynthesis.cancel();

            if (panels.length === 0) {
                return;
            }

            const activePanel = panels[current];

            const questionText =
                activePanel.querySelector('.aw-question')
                    ?.innerText ?? '';

            const optionTexts = [
                ...activePanel.querySelectorAll('.aw-option')
            ].map(option => option.innerText)
             .join('. ');

            const utterance =
                new SpeechSynthesisUtterance(
                    questionText + '. ' + optionTexts
                );

            utterance.lang = 'id-ID';

            window.speechSynthesis.speak(utterance);

        });

    /*
     * Konfirmasi pengumpulan jawaban.
     */
    document.getElementById('submitBtn')
        .addEventListener('click', function () {

            const unanswered = panels.filter(
                panel => !panel.querySelector(
                    'input[type="radio"]:checked'
                )
            ).length;

            const message = unanswered > 0
                ? `Masih ada ${unanswered} soal yang belum dijawab. Tetap kumpulkan?`
                : 'Yakin ingin mengumpulkan jawaban? Setelah dikumpulkan, asesmen tidak dapat dilanjutkan.';

            if (confirm(message)) {

                this.disabled = true;
                this.textContent = 'Mengumpulkan...';

                form.submit();

            }
        });

    /*
     * Timer tampilan pengerjaan.
     * Bukan batas waktu server.
     */
    let elapsed = 0;

    setInterval(() => {

        elapsed++;

        const minutes = String(
            Math.floor(elapsed / 60)
        ).padStart(2, '0');

        const seconds = String(
            elapsed % 60
        ).padStart(2, '0');

        const elapsedElement =
            document.getElementById('elapsedTime');

        if (elapsedElement) {

            elapsedElement.textContent =
                `${minutes}:${seconds}`;

        }

    }, 1000);

    /*
     * Inisialisasi halaman.
     */
    showQuestion(0);

    panels.forEach(panel => {

        panel.querySelectorAll('.aw-option')
            .forEach(option => {

                option.classList.toggle(
                    'selected',
                    option.querySelector('input').checked
                );

            });

    });

    updateAnswered();

});
</script>

@endsection
