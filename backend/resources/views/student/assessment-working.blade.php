@extends('layouts.app')

@section('title', 'Pengerjaan Assessment')

@section('content')

<link rel="stylesheet" href="{{ asset('css/easy-read.css') }}">

<style>
    /* =========================================
       DASAR HALAMAN SISWA
       ========================================= */

    .aw-page {
        --aw-blue: #2563eb;
        --aw-teal: #0d9488;
        --aw-ink: #182337;
        --aw-muted: #718096;

        max-width: 1200px;
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
        color: var(--aw-muted);
        text-decoration: none;
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
        width: 0;
        height: 100%;
        background: linear-gradient(
            90deg,
            var(--aw-blue),
            var(--aw-teal)
        );
        transition: width .2s ease;
    }

    .aw-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 250px;
        gap: 20px;
        align-items: start;
    }

    .aw-card,
    .aw-sidebar {
        min-width: 0;
        padding: 24px;
        background: white;
        border: 1px solid #e6edf5;
        border-radius: 22px;
        box-shadow: 0 8px 30px rgba(25, 50, 90, .04);
    }

    .aw-number {
        margin-bottom: 18px;
        color: var(--aw-blue);
        font-size: 13px;
        font-weight: 800;
    }

    .aw-question {
        margin-bottom: 24px;
        font-size: 19px;
        font-weight: 650;
        line-height: 1.8;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    /* =========================================
       PILIHAN GANDA
       ========================================= */

    .aw-options {
        display: grid;
        gap: 12px;
    }

    .aw-option {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-items: center;
        gap: 12px;

        min-height: 68px;
        padding: 14px 16px;

        background: #fff;
        border: 1.5px solid #e0e7f0;
        border-radius: 15px;

        cursor: pointer;
        transition: .15s ease;
    }

    .aw-option:hover {
        background: #f8fbff;
        border-color: #93b4fa;
    }

    .aw-option.selected {
        background: #eff6ff;
        border-color: var(--aw-blue);
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

        background: #edf2f8;
        color: #2458cb;
        border-radius: 9px;

        font-size: 14px;
        font-weight: 800;
        line-height: 1;
    }

    .aw-option-text {
        display: block;
        flex: 1;
        min-width: 0;
        margin: 0;

        font-size: 16px;
        font-weight: 500;
        line-height: 1.6;
        white-space: normal;
        overflow-wrap: break-word;
    }

    /* =========================================
       EASY READ ADAPTIF
       ========================================= */

    .aw-page.easy-read-assessment .aw-question {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 21px;
        font-weight: 600;
        line-height: 1.75;
        letter-spacing: .2px;
        word-spacing: 1px;
    }

    .aw-page.easy-read-assessment .aw-options {
        gap: 14px;
    }

    .aw-page.easy-read-assessment .aw-option {
        min-height: 74px;
        gap: 14px;
        padding: 16px 18px;
        border-radius: 16px;
    }

    .aw-page.easy-read-assessment .aw-letter {
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        background: #e5edff;
        color: #2458cb;
        border-radius: 11px;
        font-size: 17px;
    }

    .aw-page.easy-read-assessment .aw-option-text {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 18px;
        font-weight: 600;
        line-height: 1.65;
        letter-spacing: .2px;
        word-spacing: .5px;
        color: #172238;
    }

    /* =========================================
       GAMBAR SOAL ADAPTIF
       ========================================= */

    .student-question-images {
        display: grid;
        gap: 16px;
        margin-bottom: 25px;
    }

    .student-question-figure {
        margin: 0;
        padding: 16px;
        background: #f8fbff;
        border: 1px solid #dbe5f2;
        border-radius: 16px;
        text-align: center;
    }

    .student-question-image {
        display: block;
        max-width: 100%;
        max-height: 420px;
        width: auto;
        height: auto;
        margin: 0 auto;
        object-fit: contain;
        border-radius: 10px;
    }

    .student-question-figure figcaption {
        margin-top: 12px;
        color: #475569;
        font-size: 14px;
        line-height: 1.6;
    }

    .student-image-error {
        display: none;
        padding: 12px;
        color: #64748b;
        font-size: 13px;
    }

    /* =========================================
       INPUT JAWABAN ESAI
       ========================================= */

    .student-essay-wrapper {
        display: block;
        margin-top: 8px;
    }

    .student-essay-label {
        display: block;
        margin-bottom: 12px;
        color: #172238;
        font-size: 16px;
        font-weight: 700;
    }

    .student-essay-input {
        display: block;
        box-sizing: border-box;
        width: 100%;
        min-height: 230px;
        padding: 18px 20px;

        background: #fff;
        color: #172238;
        border: 1.5px solid #d5e1f2;
        border-radius: 15px;

        font-family: Arial, Helvetica, sans-serif;
        font-size: 17px;
        line-height: 1.8;

        resize: vertical;
        white-space: pre-wrap;
        overflow-wrap: break-word;
    }

    .student-essay-input:focus {
        border-color: #2563eb;
        outline: 3px solid #bfdbfe;
    }

    .student-essay-hint {
        margin: 10px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.7;
    }

    .aw-page.easy-read-assessment .student-essay-label {
        font-size: 18px;
    }

    .aw-page.easy-read-assessment .student-essay-input {
        min-height: 270px;
        font-size: 19px;
        line-height: 1.85;
        letter-spacing: .2px;
        word-spacing: 1px;
    }

    /* =========================================
       DUKUNGAN BELAJAR ADAPTIF
       ========================================= */

    .student-scaffolding-box {
        margin-top: 26px;
        padding: 22px 24px;

        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-left: 4px solid #2563eb;
        border-radius: 16px;
    }

    .student-scaffolding-title {
        display: flex;
        align-items: center;
        gap: 10px;

        margin: 0 0 12px;
        color: #1e40af;

        font-family: Arial, Helvetica, sans-serif;
        font-size: 18px;
        font-weight: 700;
        line-height: 1.5;
    }

    .student-scaffolding-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        flex: 0 0 36px;
        width: 36px;
        height: 36px;

        background: #dbeafe;
        border-radius: 11px;
        font-size: 19px;
    }

    .student-scaffolding-description {
        margin: 0 0 14px;
        color: #475569;
        font-size: 15px;
        line-height: 1.7;
    }

    .student-scaffolding-list {
        margin: 0;
        padding-left: 26px;
    }

    .student-scaffolding-list li {
        margin-bottom: 12px;
        padding-left: 4px;
        color: #172238;

        font-family: Arial, Helvetica, sans-serif;
        font-size: 17px;
        font-weight: 500;
        line-height: 1.8;
        letter-spacing: .2px;
        word-spacing: .7px;
        overflow-wrap: break-word;
    }

    .student-scaffolding-list li:last-child {
        margin-bottom: 0;
    }

    /* =========================================
       NAVIGASI
       ========================================= */

    .aw-nav {
        display: flex;
        gap: 10px;
        margin-top: 25px;
    }

    .aw-btn {
        flex: 1;
        min-height: 48px;
        padding: 12px 17px;

        background: white;
        color: var(--aw-ink);
        border: 1px solid #dce5f0;
        border-radius: 12px;

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

    .aw-sidebar h3 {
        margin: 0 0 8px;
        font-size: 16px;
    }

    .aw-numbers {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
        margin: 18px 0;
    }

    .aw-numbtn {
        aspect-ratio: 1;
        background: white;
        border: 1px solid #dfe7f0;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
    }

    .aw-numbtn.current {
        background: var(--aw-blue);
        color: white;
        border-color: var(--aw-blue);
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

    /* =========================================
       AKSESIBILITAS
       ========================================= */

    .aw-tools {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 18px;
    }

    .aw-tool {
        padding: 9px 12px;
        background: white;
        border: 1px solid #dfe7f0;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .aw-tool.active {
        background: #e0f2fe;
        border-color: #7dd3fc;
    }

    .aw-submit {
        width: 100%;
        margin-top: 16px;
        padding: 13px;

        background: var(--aw-teal);
        color: white;
        border: none;
        border-radius: 12px;

        font-weight: 800;
        cursor: pointer;
    }

    .aw-submit:disabled {
        opacity: .65;
        cursor: not-allowed;
    }

    /* Tombol perbesar teks */

    .aw-page.large-text .aw-question {
        font-size: 23px;
    }

    .aw-page.large-text .aw-option-text {
        font-size: 20px;
    }

    .aw-page.large-text .student-essay-input {
        font-size: 21px;
    }

    .aw-page.easy-read-assessment.large-text .aw-question {
        font-size: 25px;
    }

    .aw-page.easy-read-assessment.large-text .aw-option-text {
        font-size: 22px;
    }

    .aw-page.easy-read-assessment.large-text .student-essay-input {
        font-size: 23px;
    }

    .aw-page.easy-read-assessment.large-text
    .student-scaffolding-list li {
        font-size: 21px;
    }

    /* Mode kontras */

    .aw-page.high-contrast {
        --aw-ink: #000;
        --aw-muted: #303030;
    }

    .aw-page.high-contrast .aw-card,
    .aw-page.high-contrast .aw-sidebar,
    .aw-page.high-contrast .aw-option,
    .aw-page.high-contrast .student-essay-input,
    .aw-page.high-contrast .student-scaffolding-box {
        border-color: #333;
    }

    .aw-page.high-contrast .aw-question,
    .aw-page.high-contrast .aw-option-text,
    .aw-page.high-contrast .student-essay-input,
    .aw-page.high-contrast .student-scaffolding-list li {
        color: #000;
    }

    /* Mode fokus */

    .aw-page.focus-mode .aw-layout {
        grid-template-columns: minmax(0, 1fr);
    }

    .aw-page.focus-mode .aw-card {
        width: 100%;
        max-width: 850px;
        margin: 0 auto;
    }

    /* =========================================
       RESPONSIF HP
       ========================================= */

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

        .aw-card {
            padding: 19px;
        }

        .aw-numbers {
            grid-template-columns: repeat(5, minmax(0, 1fr));
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

        .student-question-figure {
            padding: 10px;
        }

        .student-question-image {
            max-height: 320px;
        }

        .student-scaffolding-box {
            padding: 18px;
        }

        .student-scaffolding-title {
            font-size: 17px;
        }

        .student-scaffolding-list li {
            font-size: 16px;
        }

        .aw-page.easy-read-assessment .student-essay-input {
            font-size: 18px;
        }
    }
    /* NORMAL: layout asli — soal di kiri, navigasi di kanan. */
    .aw-page:not(.focus-mode) .aw-layout {
        grid-template-columns: minmax(0, 1fr) 250px;
    }
    .aw-page:not(.focus-mode) .aw-sidebar {
        display: block;
        position: static;
        width: auto;
        max-width: none;
        height: auto;
        margin: 0;
        visibility: visible;
        pointer-events: auto;
        transform: none;
        overflow: visible;
        border-radius: 22px;
    }
    .aw-page:not(.focus-mode) .aw-nav-toggle,
    .aw-page:not(.focus-mode) .aw-close-nav {
        display: none;
    }
    .aw-page:not(.focus-mode) .aw-card {
        width: auto;
        max-width: none;
        margin: 0;
    }

    /* FOKUS: area soal melebar, sidebar meluncur ke kanan. */
    .aw-page.focus-mode .aw-layout {
        grid-template-columns: minmax(0, 1fr);
    }
    .aw-page.focus-mode .aw-card {
        width: 100%;
        max-width: 850px;
        margin-inline: auto;
        box-sizing: border-box;
    }
    .aw-page.focus-mode .aw-sidebar {
        display: block !important;
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        z-index: 1101;
        box-sizing: border-box;
        width: min(355px, 90vw);
        max-width: none;
        height: 100dvh;
        margin: 0;
        overflow-y: auto;
        border-radius: 20px 0 0 20px;
        visibility: hidden;
        pointer-events: none;
        transform: translateX(100%);
        transition: transform .3s ease, visibility .3s;
    }
    .aw-page.focus-mode .aw-sidebar.nav-open {
        visibility: visible;
        pointer-events: auto;
        transform: translateX(0);
    }
    .aw-nav-toggle {
        display: none;
        position: fixed;
        top: 46%;
        right: 0;
        z-index: 1102;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        width: 44px;
        min-height: 80px;
        padding: 9px 4px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 12px 0 0 12px;
        font-size: 22px;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(0,0,0,.15);
        transition: right .3s ease;
    }
    .aw-page.focus-mode .aw-nav-toggle { display: flex; }
    .aw-page.focus-mode.nav-is-open .aw-nav-toggle {
        right: min(355px, 90vw);
    }
    .aw-nav-toggle-label {
        font-size: 11px;
        font-weight: 700;
        writing-mode: vertical-rl;
    }
    .aw-nav-toggle:focus-visible,
    .aw-close-nav:focus-visible {
        outline: 3px solid #f59e0b;
        outline-offset: 3px;
    }
    .aw-nav-backdrop { display: none !important; }
    .aw-close-nav {
        display: none;
        float: right;
        padding: 9px 13px;
        border-radius: 9px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        cursor: pointer;
    }
    .aw-page.focus-mode .aw-close-nav { display: block; }
    #unansweredCount {
        clear: both;
        padding-top: 12px;
        font-size: 14px;
        font-weight: 700;
        color: #334155;
    }
    .aw-numbtn.unanswered:not(.current) {
        background: #fff7ed;
        color: #9a3412;
        border-color: #fdba74;
    }
    .aw-btn.submit-final {
        background: #0d9488;
        border-color: #0d9488;
        color: white;
    }
    @media (max-width: 700px) {
        .aw-page:not(.focus-mode) .aw-layout { grid-template-columns: 1fr; }
        .aw-page:not(.focus-mode) .aw-sidebar { order: -1; }
    }
    @media (prefers-reduced-motion: reduce) {
        .aw-page.focus-mode .aw-sidebar,
        .aw-nav-toggle { transition: none; }
    }
</style>

<div
    id="assessmentApp"
    class="aw-page {{ $version === 'adaptif' ? 'easy-read-assessment' : '' }}">

    {{-- HEADER --}}

    <div class="aw-top">
        <a
            href="{{ route('siswa.assessment.instructions', $assessment->id) }}"
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

            <span id="elapsedTime"
                  style="font-weight:800;color:#2563eb">
                00:00
            </span>
        </div>

        <div class="aw-progress">
            <div
                id="progressBar"
                class="aw-progress-bar">
            </div>
        </div>
    </div>

    @if($errors->any())
        <div
            role="alert"
            style="padding:14px;margin-bottom:20px;border-radius:12px;background:#fff0f0;color:#9f1239;">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- FORM PENGERJAAN --}}

    <form
        id="assessmentForm"
        method="POST"
        action="{{ route('siswa.assessment.submit', ['id' => $assessment->id]) }}">

        @csrf

        <input
            type="hidden"
            name="attempt_id"
            value="{{ $attemptId }}">

        <input
            type="hidden"
            name="version_type"
            value="{{ $version }}">

        {{-- Tombol panel navigasi tetap tersedia saat Mode Fokus aktif. --}}
        <button type="button" id="openQuestionNav" class="aw-nav-toggle"
                aria-label="Buka navigasi soal" aria-controls="questionSidebar"
                aria-expanded="false"><span id="navToggleArrow" aria-hidden="true">❮</span><span class="aw-nav-toggle-label">Nomor soal</span></button>
        <div id="navBackdrop" class="aw-nav-backdrop" aria-hidden="true"></div>

        <div class="aw-layout">

            <main class="aw-card">

                {{-- LOOP SOAL --}}

                @foreach($questions as $index => $question)
                    @php
                        /*
                         * 1. Normalisasi opsi jawaban.
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

                        /*
                         * 2. Periksa jenis soal.
                         *
                         * PERBAIKAN UTAMA:
                         * Soal tanpa opsi mendapat textarea,
                         * walaupun question_type dari database
                         * belum terbaca sebagai "esai".
                         */
                        $tipeSoal = strtolower(
                            trim((string) (
                                $question->question_type ?? ''
                            ))
                        );

                        $typeSaysEssay =
                            str_contains($tipeSoal, 'esai')
                            || str_contains($tipeSoal, 'essay')
                            || str_contains($tipeSoal, 'uraian');

                        $isEssay = $typeSaysEssay || empty($options);

                        /*
                         * 3. Ambil jawaban tersimpan.
                         */
                        $jawabanTersimpan = old(
                            'answers.' . $question->id,
                            $savedAnswers[$question->id] ?? ''
                        );

                        /*
                         * 4. Ambil scaffolding Adaptif.
                         */
                        $petunjukAdaptif = $version === 'adaptif'
                            ? (
                                ($scaffoldingPerQuestion ?? [])[$question->id]
                                ?? []
                            )
                            : [];

                        $petunjukAdaptif = is_array($petunjukAdaptif)
                            ? $petunjukAdaptif
                            : [];

                        /*
                         * 5. Ambil gambar Adaptif.
                         */
                        $gambarSoal = $version === 'adaptif'
                            ? (
                                ($gambarPerQuestion ?? [])[$question->id]
                                ?? []
                            )
                            : [];

                        $gambarSoal = is_array($gambarSoal)
                            ? $gambarSoal
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

                        {{-- TEKS PERTANYAAN --}}

                        <div class="aw-question {{ $version === 'adaptif' ? 'easy-read-question' : '' }}">{{ $question->question_text }}</div>

                        {{-- GAMBAR KHUSUS ADAPTIF --}}

                        @if(
                            $version === 'adaptif'
                            && !empty($gambarSoal)
                        )
                            <div class="student-question-images">

                                @foreach($gambarSoal as $gambar)
                                    @php
                                        $imageUrl = is_array($gambar)
                                            ? ($gambar['url'] ?? null)
                                            : null;

                                        $imageAlt = is_array($gambar)
                                            ? (
                                                $gambar['alt']
                                                ?? 'Ilustrasi pendukung soal'
                                            )
                                            : 'Ilustrasi pendukung soal';
                                    @endphp

                                    @if(
                                        is_string($imageUrl)
                                        && filter_var($imageUrl, FILTER_VALIDATE_URL)
                                    )
                                        <figure class="student-question-figure">
                                            <img
                                                src="{{ $imageUrl }}"
                                                alt="{{ $imageAlt }}"
                                                class="student-question-image"
                                                loading="lazy"
                                                referrerpolicy="no-referrer"
                                                onerror="this.style.display='none';this.nextElementSibling.style.display='block';">

                                            <div class="student-image-error">
                                                Ilustrasi belum dapat dimuat.
                                            </div>

                                            <figcaption>
                                                {{ $imageAlt }}
                                            </figcaption>
                                        </figure>
                                    @endif
                                @endforeach

                            </div>
                        @endif

                        {{-- ======================================
                             JAWABAN SISWA
                             ====================================== --}}

                        @if($isEssay)

                            {{-- ESAI: SELALU TAMPILKAN TEXTAREA --}}

                            <div class="student-essay-wrapper">

                                <label
                                    for="essay-answer-{{ $question->id }}"
                                    class="student-essay-label">
                                    Tulis jawabanmu di sini
                                </label>

                                <textarea
                                    id="essay-answer-{{ $question->id }}"
                                    name="answers[{{ $question->id }}]"
                                    class="student-essay-input"
                                    rows="8"
                                    maxlength="5000"
                                    placeholder="Tuliskan jawaban dengan bahasamu sendiri..."
                                    aria-label="Jawaban esai pertanyaan {{ $index + 1 }}">{{ $jawabanTersimpan }}</textarea>

                                <p class="student-essay-hint">
                                    Kamu dapat menulis beberapa kalimat.
                                    Periksa kembali jawaban sebelum dikumpulkan.
                                </p>

                            </div>

                        @else

                            {{-- PILIHAN GANDA --}}

                            <div class="aw-options {{ $version === 'adaptif' ? 'easy-read-options' : '' }}">

                                @foreach($options as $key => $option)
                                    @php
                                        $optionData = is_array($option)
                                            ? $option
                                            : [];

                                        $optionLabel =
                                            $optionData['label']
                                            ?? (
                                                is_numeric($key)
                                                    ? chr(65 + (int) $key)
                                                    : (string) $key
                                            );

                                        $optionText =
                                            $optionData['teks']
                                            ?? $optionData['text']
                                            ?? (
                                                is_scalar($option)
                                                    ? (string) $option
                                                    : ''
                                            );

                                        /*
                                         * Format key asli dipertahankan.
                                         */
                                        $optionValue = (string) $key;

                                        $isChecked =
                                            (string) $jawabanTersimpan
                                            === $optionValue;
                                    @endphp

                                    <label class="aw-option">

                                        <input
                                            type="radio"
                                            name="answers[{{ $question->id }}]"
                                            value="{{ $optionValue }}"
                                            {{ $isChecked ? 'checked' : '' }}>

                                        <span class="aw-letter">
                                            {{ $optionLabel }}
                                        </span>

                                        <span class="aw-option-text">
                                            {{ $optionText }}
                                        </span>

                                    </label>
                                @endforeach

                            </div>

                        @endif

                        {{-- DUKUNGAN BELAJAR ADAPTIF --}}

                        @if(
                            $version === 'adaptif'
                            && !empty($petunjukAdaptif)
                        )
                            <div class="student-scaffolding-box">

                                <h3 class="student-scaffolding-title">
                                    <span
                                        class="student-scaffolding-icon"
                                        aria-hidden="true">
                                        💡
                                    </span>

                                    Dukungan Belajar Adaptif
                                </h3>

                                <p class="student-scaffolding-description">
                                    Gunakan petunjuk berikut untuk
                                    membantu memahami pertanyaan.
                                </p>

                                <ol class="student-scaffolding-list">

                                    @foreach($petunjukAdaptif as $petunjuk)
                                        @if(
                                            is_string($petunjuk)
                                            && trim($petunjuk) !== ''
                                        )
                                            <li>{{ $petunjuk }}</li>
                                        @endif
                                    @endforeach

                                </ol>

                            </div>
                        @endif

                    </section>
                @endforeach

                {{-- NAVIGASI SEBELUMNYA/SELANJUTNYA --}}

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

            {{-- SIDEBAR --}}

            <aside class="aw-sidebar" id="questionSidebar" aria-label="Navigasi dan bantuan soal"
                   >

                <button type="button" id="closeQuestionNav" class="aw-close-nav"
                        aria-label="Tutup navigasi soal">✕ Tutup</button>
                <h3>Navigasi soal</h3>
                <p id="unansweredCount" role="status" aria-live="polite">Memeriksa jawaban...</p>

                <p class="aw-muted"
                   style="font-size:12px;line-height:1.6">
                    Pilih nomor untuk berpindah soal.
                </p>

                <div class="aw-numbers" id="questionNumbers">
                    @foreach($questions as $index => $question)
                        <button
                            type="button"
                            class="aw-numbtn {{ $index === 0 ? 'current' : '' }}"
                            data-go="{{ $index }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>

                <div class="aw-muted" style="font-size:12px">
                    <span style="color:#0d9488">●</span>
                    Sudah dijawab

                    <br>

                    <span style="color:#2563eb">●</span>
                    Soal aktif
                </div>

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

{{-- ==========================================
     JAVASCRIPT HALAMAN SISWA
     ========================================== --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const app = document.getElementById('assessmentApp');
    const form = document.getElementById('assessmentForm');

    const panels = Array.from(
        document.querySelectorAll('.aw-question-panel')
    );

    const numberButtons = Array.from(
        document.querySelectorAll('.aw-numbtn')
    );

    const previousBtn = document.getElementById('previousBtn');
    const nextBtn = document.getElementById('nextBtn');
    const progressText = document.getElementById('progressText');
    const progressBar = document.getElementById('progressBar');
    const sidebar = app.querySelector('.aw-sidebar');
    const submitBtn = document.getElementById('submitBtn');

    const total = panels.length;

    let current = 0;
    let focusMode = false;

    /*
     * Status terjawab untuk PG dan esai.
     */
    function isPanelAnswered(panel) {
        const checkedRadio = panel.querySelector(
            'input[type="radio"]:checked'
        );

        const essayInput = panel.querySelector(
            '.student-essay-input'
        );

        return Boolean(
            checkedRadio ||
            (
                essayInput &&
                essayInput.value.trim() !== ''
            )
        );
    }

    /*
     * Warna nomor soal.
     */
    function updateAnswered() {
        panels.forEach((panel, index) => {
            if (numberButtons[index]) {
                numberButtons[index].classList.toggle(
                    'answered',
                    isPanelAnswered(panel)
                );
            }
        });
    }

    /*
     * Berpindah soal.
     */
    function showQuestion(index) {
        if (index < 0 || index >= total) {
            return;
        }

        current = index;

        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }

        panels.forEach((panel, i) => {
            panel.style.display = i === current
                ? ''
                : 'none';
        });

        numberButtons.forEach((button, i) => {
            button.classList.toggle(
                'current',
                i === current
            );
        });

        previousBtn.disabled = current === 0;

        const isLastQuestion = current === total - 1;
        nextBtn.disabled = total === 0;
        nextBtn.textContent = isLastQuestion
            ? 'Kumpulkan Jawaban ✓'
            : 'Selanjutnya →';
        nextBtn.classList.toggle('submit-final', isLastQuestion);

        progressText.textContent =
            `Soal ${current + 1} dari ${total}`;

        progressBar.style.width =
            `${((current + 1) / total) * 100}%`;

        updateAnswered();
    }

    numberButtons.forEach(button => {
        button.addEventListener('click', function () {
            showQuestion(Number(this.dataset.go));
        });
    });

    previousBtn.addEventListener('click', function () {
        showQuestion(current - 1);
    });

    nextBtn.addEventListener('click', function () {
        if (current === total - 1) {
            submitBtn.click(); // Tetap menggunakan konfirmasi dan validasi yang sudah ada.
            return;
        }
        showQuestion(current + 1);
    });

    /*
     * Pilihan ganda.
     */
    form.querySelectorAll('input[type="radio"]')
        .forEach(input => {

            input.addEventListener('change', function () {
                const panel = this.closest('.aw-question-panel');

                panel.querySelectorAll('.aw-option')
                    .forEach(option => {
                        const radio = option.querySelector(
                            'input[type="radio"]'
                        );

                        option.classList.toggle(
                            'selected',
                            Boolean(radio && radio.checked)
                        );
                    });

                updateAnswered();
            });
        });

    /*
     * ESAI:
     * Status terjawab berubah saat mengetik.
     */
    form.querySelectorAll('.student-essay-input')
        .forEach(textarea => {
            textarea.addEventListener(
                'input',
                updateAnswered
            );
        });

    /*
     * Tombol perbesar teks.
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

            app.classList.toggle('focus-mode', focusMode);

            // Hanya pada Mode Fokus, navigasi masuk ke panel geser.
            // Mode biasa mengembalikan sidebar asli yang selalu terlihat.
            closeQuestionNav(false);
            if (!focusMode) {
                sidebar.inert = false;
                sidebar.removeAttribute('aria-hidden');
            }

            this.classList.toggle('active', focusMode);
            this.textContent = focusMode ? '✕ Keluar Mode Fokus' : '◎ Mode fokus';
            this.setAttribute('aria-pressed', String(focusMode));
        });

    /*
     * Panel navigasi samping — tetap dapat dibuka di Mode Fokus.
     */
    const openNavBtn = document.getElementById('openQuestionNav');
    const closeNavBtn = document.getElementById('closeQuestionNav');
    const navBackdrop = document.getElementById('navBackdrop');
    const unansweredCount = document.getElementById('unansweredCount');
    let navWasOpenedFrom = null;

    function updateUnansweredCount() {
        const remaining = panels.filter(panel => !isPanelAnswered(panel)).length;
        unansweredCount.textContent = remaining === 0
            ? 'Semua soal sudah dijawab ✓'
            : `${remaining} soal belum dijawab`;
        numberButtons.forEach((button, index) => {
            const answered = isPanelAnswered(panels[index]);
            button.setAttribute('aria-label',
                `Soal ${index + 1}, ${answered ? 'sudah dijawab' : 'belum dijawab'}`);
            button.classList.toggle('unanswered', !answered);
        });
    }

    const navToggleArrow = document.getElementById('navToggleArrow');

    function openQuestionNav() {
        updateUnansweredCount();
        navWasOpenedFrom = document.activeElement;
        sidebar.inert = false;
        sidebar.removeAttribute('aria-hidden');
        sidebar.classList.add('nav-open');
        app.classList.add('nav-is-open');
        navToggleArrow.textContent = '❯';
        openNavBtn.setAttribute('aria-label', 'Sembunyikan navigasi soal ke kanan');
        openNavBtn.setAttribute('aria-expanded', 'true');
        closeNavBtn.focus();
    }

    function closeQuestionNav(restoreFocus = true) {
        sidebar.classList.remove('nav-open');
        app.classList.remove('nav-is-open');
        navToggleArrow.textContent = '❮';
        openNavBtn.setAttribute('aria-label', 'Buka navigasi soal dari kanan');
        openNavBtn.setAttribute('aria-expanded', 'false');
        if (focusMode) {
            sidebar.inert = true;
            sidebar.setAttribute('aria-hidden', 'true');
        } else {
            sidebar.inert = false;
            sidebar.removeAttribute('aria-hidden');
        }
        if (restoreFocus && focusMode) openNavBtn.focus();
    }

    // Panah yang sama membuka DAN menutup panel.
    openNavBtn.addEventListener('click', function () {
        if (!focusMode) return;
        if (sidebar.classList.contains('nav-open')) {
            closeQuestionNav();
        } else {
            openQuestionNav();
        }
    });
    closeNavBtn.addEventListener('click', () => closeQuestionNav());
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && sidebar.classList.contains('nav-open')) {
            closeQuestionNav();
        }
    });
    // Klik nomor tetap berpindah soal; setelah itu panel bergeser ke kanan.
    numberButtons.forEach(button => button.addEventListener('click', () => {
        if (focusMode) closeQuestionNav();
    }));
    form.addEventListener('change', updateUnansweredCount);
    form.addEventListener('input', updateUnansweredCount);

    /*
     * Pembaca soal dan petunjuk.
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

            const panel = panels[current];

            if (!panel) {
                return;
            }

            const questionText =
                panel.querySelector('.aw-question')
                    ?.innerText ?? '';

            const optionText = Array.from(
                panel.querySelectorAll('.aw-option')
            ).map(option => option.innerText).join('. ');

            const imageText = Array.from(
                panel.querySelectorAll('.student-question-image')
            ).map(img => img.alt).filter(Boolean).join('. ');

            const scaffoldingText =
                panel.querySelector('.student-scaffolding-box')
                    ?.innerText ?? '';

            const textToRead = [
                questionText,
                optionText,
                imageText,
                scaffoldingText
            ].filter(Boolean).join('. ');

            const utterance =
                new SpeechSynthesisUtterance(textToRead);

            utterance.lang = 'id-ID';

            window.speechSynthesis.speak(utterance);
        });

    /*
     * Kirim jawaban.
     */
    submitBtn.addEventListener('click', function () {

        const unanswered = panels.filter(
            panel => !isPanelAnswered(panel)
        ).length;

        const message = unanswered > 0
            ? `Masih ada ${unanswered} soal yang belum dijawab. Tetap kumpulkan?`
            : 'Yakin ingin mengumpulkan jawaban? Setelah dikumpulkan, asesmen tidak dapat dilanjutkan.';

        if (!confirm(message)) {
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengumpulkan...';

        form.submit();
    });

    /*
     * Timer tampilan, bukan batas waktu server.
     */
    let elapsed = 0;

    setInterval(function () {
        elapsed++;

        const minutes = String(
            Math.floor(elapsed / 60)
        ).padStart(2, '0');

        const seconds = String(
            elapsed % 60
        ).padStart(2, '0');

        document.getElementById('elapsedTime')
            .textContent = `${minutes}:${seconds}`;

    }, 1000);

    /*
     * Inisialisasi awal.
     */
    if (total > 0) {
        showQuestion(0);
    }

    panels.forEach(panel => {
        panel.querySelectorAll('.aw-option')
            .forEach(option => {
                const radio = option.querySelector(
                    'input[type="radio"]'
                );

                option.classList.toggle(
                    'selected',
                    Boolean(radio && radio.checked)
                );
            });
    });

    updateAnswered();
    updateUnansweredCount();

});
</script>

@endsection
