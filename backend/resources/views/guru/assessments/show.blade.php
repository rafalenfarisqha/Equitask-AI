
@extends('layouts.app')

@section('title', 'Detail Asesmen')

@section('content')

<link rel="stylesheet" href="{{ asset('css/easy-read.css') }}">

<style>
    /* ========================================
       HALAMAN DETAIL ASESMEN GURU
       ======================================== */

    .assessment-page {
        max-width: 1000px;
    }

    .assessment-card {
        border: 0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(15, 23, 42, .055);
    }

    .assessment-heading {
        color: #0f172a;
        font-weight: 700;
    }

    .assessment-subtitle {
        color: #64748b;
        font-size: 14px;
    }

    .assessment-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .assessment-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 10px;
        padding: 11px 18px;
        font-weight: 600;
        text-decoration: none;
    }

    .assessment-panel {
        min-height: 240px;
    }

    .question-card {
        display: none;
    }

    .question-card.active {
        display: block;
    }

    .question-text {
        white-space: pre-line;
        line-height: 1.75;
        overflow-wrap: anywhere;
    }

    /* ========================================
       PILIHAN JAWABAN UMUM / REGULER
       ======================================== */

    .question-choice {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        margin-bottom: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .question-label {
        display: inline-flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: #dbeafe;
        color: #1d4ed8;
        font-weight: 700;
    }


    /* Dukungan Belajar — Soal Modifikasi */
    .scaffolding-box {
        padding: 18px 20px;
        margin-top: 20px;

        border: 1px solid #bfdbfe;
        border-left: 4px solid #2563eb;
        border-radius: 16px;
        background: #eff6ff;

        font-family: Arial, sans-serif;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        letter-spacing: normal;
        word-spacing: normal;
        color: #172238;
    }

    /* Judul Dukungan Belajar */
    .scaffolding-box h6 {
        margin: 0 0 14px;
        font-family: Arial, sans-serif;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.5;
        color: #1e40af;
    }

    /* Daftar langkah belajar */
    .scaffolding-box ol,
    .scaffolding-box ul {
        margin-top: 0;
        margin-bottom: 0;
        padding-left: 24px;
    }

    .scaffolding-box li {
        margin-bottom: 10px;
        font-family: Arial, sans-serif;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        letter-spacing: normal;
        word-spacing: normal;
        color: #172238;
    }

    .scaffolding-box li:last-child {
        margin-bottom: 0;
    }

    /* Jika langkah ditulis dalam paragraf */
    .scaffolding-box p {
        margin-bottom: 10px;
        font-family: Arial, sans-serif;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        letter-spacing: normal;
        word-spacing: normal;
        color: #172238;
    }

    .scaffolding-box p:last-child {
        margin-bottom: 0;
    }

    /* Tampilan HP */
    @media (max-width: 575px) {
        .scaffolding-box {
            padding: 16px;
        }

        .scaffolding-box h6,
        .scaffolding-box li,
        .scaffolding-box p {
            font-size: 15px;
        }
    }


    .answer-box {
        padding: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .answer-box summary {
        cursor: pointer;
        color: #1e40af;
    }

    .evaluation-item {
        padding: 18px;
        margin-bottom: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    /* ========================================
       GAMBAR DIFY
       ======================================== */

    .assessment-image-frame {
        padding: 16px;
        border: 1px solid #dbe5f3;
        border-radius: 16px;
        background: #f8fbff;
        height: 100%;
    }

    .assessment-image-frame img {
        display: block;
        width: 100%;
        height: auto;
        max-height: 340px;
        object-fit: contain;
        border-radius: 12px;
        margin: 0 auto;
        background: #ffffff;
    }

    .assessment-image-caption {
        margin-top: 12px;
        font-size: 13px;
        line-height: 1.5;
        color: #64748b;
        text-align: center;
    }


    /* ========================================
    SOAL MODIFIKASI — EASY READ
    Tipografi mengikuti mockup Equitask AI
    ======================================== */

    #panelAdaptif.easy-read-assessment {
        font-family: Arial, "Plus Jakarta Sans", sans-serif;
        color: #172238;
    }

    #panelAdaptif .easy-read-question {
        display: block;
        width: 100%;
        max-width: 100%;
        margin: 0 0 24px !important;
        padding: 0;

        font-family: Arial, "Plus Jakarta Sans", sans-serif;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.75;

        letter-spacing: normal;
        word-spacing: normal;

        text-align: justify;
        text-justify: inter-word;
        white-space: pre-line;
        overflow-wrap: anywhere;
    }

    #panelAdaptif .adaptive-illustrations {
        margin-bottom: 24px;
    }

    #panelAdaptif .easy-read-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 24px;
    }

    #panelAdaptif .easy-read-options .question-choice {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 12px !important;

        width: 100%;
        min-height: 64px;
        margin: 0 !important;
        padding: 18px !important;

        border: 1.5px solid #dce5f0;
        border-radius: 16px;
        background: #ffffff;
    }

    #panelAdaptif .easy-read-options .question-label {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;

        flex: 0 0 26px !important;
        width: 26px !important;
        min-width: 26px !important;
        height: 26px !important;

        margin: 0 !important;
        padding: 0 !important;

        border-radius: 8px;
        background: #dfe8f7;
        color: #2563eb;

        font-family: Arial, "Plus Jakarta Sans", sans-serif;
        font-size: 12px;
        font-weight: 800;
        line-height: 1;
    }

    #panelAdaptif .easy-read-options .easy-read-option {
        display: block !important;
        flex: 1 1 auto !important;
        align-self: center !important;

        min-width: 0;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;

        font-family: Arial, "Plus Jakarta Sans", sans-serif;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.5;

        letter-spacing: normal;
        word-spacing: normal;

        color: #172238;
        white-space: normal !important;
        text-align: left;
        overflow-wrap: anywhere;
    }

    #panelAdaptif .scaffolding-box {
        margin-top: 22px;
        padding: 18px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-left: 4px solid #2563eb;
        border-radius: 16px;
    }

    #panelAdaptif .scaffolding-box h6 {
        font-family: Arial, "Plus Jakarta Sans", sans-serif;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.5;
        color: #1e40af;
        margin-bottom: 12px;
    }

    #panelAdaptif .scaffolding-box ol {
        padding-left: 24px;
        margin-bottom: 0;
    }

    #panelAdaptif .scaffolding-box li {
        font-family: Arial, "Plus Jakarta Sans", sans-serif;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.75;

        letter-spacing: normal;
        word-spacing: normal;

        color: #172238;
        margin-bottom: 12px;
    }

    #panelAdaptif .scaffolding-box li:last-child {
        margin-bottom: 0;
    }

    @media (max-width: 575px) {
        #panelAdaptif .easy-read-question {
            font-size: 15px;
            line-height: 1.75;
        }

        #panelAdaptif .easy-read-options .question-choice {
            min-height: 60px;
            padding: 18px !important;
            gap: 12px !important;
        }

        #panelAdaptif .easy-read-options .easy-read-option {
            font-size: 15px;
            line-height: 1.5;
        }

        #panelAdaptif .scaffolding-box {
            padding: 16px;
        }

        #panelAdaptif .scaffolding-box h6 {
            font-size: 15px;
        }

        #panelAdaptif .scaffolding-box li {
            font-size: 15px;
            line-height: 1.75;
        }
    }

    /* ========================================
    PERAPIAN PILIHAN JAWABAN REGULER
    ======================================== */

    #panelReguler .regular-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    #panelReguler .regular-options .question-choice {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 16px;

        min-height: 68px;
        margin: 0;
        padding: 14px 16px;

        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    #panelReguler .regular-options .question-label {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 34px;

        width: 34px;
        height: 34px;
        margin: 0;
        padding: 0;

        line-height: 1;
    }

    #panelReguler .regular-options .regular-option-text {
        display: block;
        flex: 1;
        min-width: 0;
        margin: 0;
        padding: 0;

        font-size: 16px;
        font-weight: 400;
        line-height: 1.6;
        white-space: normal;
        text-align: left;
    }

    /* Perbaikan jarak Dukungan Belajar */
    #panelAdaptif .scaffolding-box h6 {
        margin-bottom: 12px !important;
        font-size: 15px !important;
        line-height: 1.5 !important;
    }

    #panelAdaptif .scaffolding-box p,
    #panelAdaptif .scaffolding-box li,
    #panelAdaptif .scaffolding-box div {
        margin-top: 0 !important;
        margin-bottom: 10px !important;
        font-size: 15px !important;
        line-height: 1.6 !important;
        letter-spacing: normal !important;
        word-spacing: normal !important;
    }

    #panelAdaptif .scaffolding-box ol,
    #panelAdaptif .scaffolding-box ul {
        margin-bottom: 0 !important;
        padding-left: 24px !important;
    }

    #panelAdaptif .scaffolding-box > *:last-child {
        margin-bottom: 0 !important;
    }

    /* Dukungan Belajar - tipografi Soal Modifikasi */
    #panelAdaptif .scaffolding-box {
        box-sizing: border-box;
        padding: 18px 20px;
        margin-top: 22px;

        font-family: Arial, sans-serif;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.6;
        letter-spacing: normal;
        word-spacing: normal;
        color: #172238;

        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-left: 4px solid #2563eb;
        border-radius: 16px;
    }

    /* Judul */
    #panelAdaptif .scaffolding-box h6 {
        margin: 0 0 14px !important;
        padding: 0;

        font-family: Arial, sans-serif;
        font-size: 15px !important;
        font-weight: 700;
        line-height: 1.5;
        letter-spacing: normal;
        word-spacing: normal;
        color: #1e40af;
    }

    /* Samakan font Dukungan Belajar dengan Soal Modifikasi */
    #panelAdaptif .scaffolding-box,
    #panelAdaptif .scaffolding-box h6,
    #panelAdaptif .scaffolding-box .scaffolding-step {
        font-family: inherit !important;
        letter-spacing: inherit !important;
        word-spacing: inherit !important;
    }


    /* Tipografi isi Dukungan Belajar */
    #panelAdaptif .scaffolding-box .scaffolding-step {
        font-family: inherit;
        font-size: 16px !important;
        font-weight: 500 !important;
        line-height: 1.5 !important;
        letter-spacing: normal !important;
        word-spacing: normal !important;
        text-align: left;
        margin: 0 0 10px !important;
        padding: 0 !important;
    }

    #panelAdaptif .scaffolding-box .scaffolding-step:last-child {
        margin-bottom: 0 !important;
    }


    @media (max-width: 575px) {
        #panelAdaptif .scaffolding-box {
            padding: 16px;
        }
    }


    /* =========================================
    FINAL FIX: DUKUNGAN BELAJAR SOAL MODIFIKASI
    Samakan font dan jarak dengan pertanyaan
    ========================================= */

    #panelAdaptif .scaffolding-box {
        box-sizing: border-box;
        width: 100%;
        margin: 22px 0 24px !important;
        padding: 18px 20px !important;

        font-family: Arial, "Plus Jakarta Sans", sans-serif !important;
        font-size: 15px !important;
        font-weight: 500 !important;
        line-height: 1.75 !important;

        letter-spacing: normal !important;
        word-spacing: normal !important;
        text-align: left !important;

        color: #172238 !important;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-left: 4px solid #2563eb;
        border-radius: 16px;
    }

    /* Judul Dukungan Belajar */
    #panelAdaptif .scaffolding-box h6 {
        margin: 0 0 14px !important;
        padding: 0 !important;

        font-family: Arial, "Plus Jakarta Sans", sans-serif !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        line-height: 1.75 !important;

        letter-spacing: normal !important;
        word-spacing: normal !important;
        text-align: left !important;
        color: #1e40af !important;
    }

    /* Hilangkan indentasi bawaan daftar */
    #panelAdaptif .scaffolding-box ol,
    #panelAdaptif .scaffolding-box ul {
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
    }

    /* Isi setiap langkah Dukungan Belajar */
    #panelAdaptif .scaffolding-box li,
    #panelAdaptif .scaffolding-box .scaffolding-step,
    #panelAdaptif .scaffolding-box p {
        margin: 0 0 12px !important;
        padding: 0 !important;

        font-family: Arial, "Plus Jakarta Sans", sans-serif !important;
        font-size: 15px !important;
        font-weight: 500 !important;
        line-height: 1.75 !important;

        letter-spacing: normal !important;
        word-spacing: normal !important;
        text-align: left !important;

        color: #172238 !important;
    }

    /* Hapus jarak setelah langkah terakhir */
    #panelAdaptif .scaffolding-box li:last-child,
    #panelAdaptif .scaffolding-box p:last-child,
    #panelAdaptif .scaffolding-box .scaffolding-step:last-child {
        margin-bottom: 0 !important;
    }

    /* Tampilan layar HP */
    @media (max-width: 575px) {
        #panelAdaptif .scaffolding-box {
            padding: 16px !important;
            margin-top: 18px !important;
        }

        #panelAdaptif .scaffolding-box h6,
        #panelAdaptif .scaffolding-box li,
        #panelAdaptif .scaffolding-box p,
        #panelAdaptif .scaffolding-box .scaffolding-step {
            font-size: 15px !important;
        }
    }


    /* Samakan font Dukungan Belajar dengan pertanyaan Soal Modifikasi */
    #panelAdaptif .scaffolding-box .scaffolding-step,
    #panelAdaptif .scaffolding-box li,
    #panelAdaptif .scaffolding-box p {
        font-family: Arial, "Plus Jakarta Sans", sans-serif !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        line-height: 1.75 !important;
        letter-spacing: normal !important;
        word-spacing: normal !important;
        text-align: justify !important;
        text-justify: inter-word;
    }

    /* Jarak antarpetunjuk */
    #panelAdaptif .scaffolding-box li,
    #panelAdaptif .scaffolding-box .scaffolding-step,
    #panelAdaptif .scaffolding-box p {
        margin-bottom: 12px !important;
        padding: 0 !important;
    }

    #panelAdaptif .scaffolding-box li:last-child,
    #panelAdaptif .scaffolding-box p:last-child {
        margin-bottom: 0 !important;
    }

    /* Judul tetap tegas dan rapi */
    #panelAdaptif .scaffolding-box h6 {
        font-family: Arial, "Plus Jakarta Sans", sans-serif !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        line-height: 1.75 !important;
    }

    </style>

<div class="container py-4 py-md-5 assessment-page">

    {{-- NOTIFIKASI --}}

    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-3 mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- HEADER ASESMEN --}}

    <div class="assessment-card p-4 mb-4">

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">

            <div class="d-flex align-items-start gap-3">

                <a href="{{ route('guru.assessments.index') }}"
                   class="btn btn-light border rounded-3"
                   title="Kembali ke daftar asesmen">

                    <span class="material-symbols-outlined">
                        arrow_back
                    </span>
                </a>

                <div>
                    <h1 class="h3 assessment-heading mb-2">
                        {{ $assessment->nama }}
                    </h1>

                    <div class="assessment-subtitle d-flex flex-wrap gap-2">
                        <span>{{ $assessment->mapel }}</span>
                        <span>•</span>
                        <span>{{ $assessment->kelas ?? 'Kelas Umum' }}</span>
                        <span>•</span>
                        <span>{{ $assessment->versi_asesmen ?? 'Asesmen' }}</span>
                    </div>
                </div>

            </div>

            <span class="badge rounded-pill px-3 py-2
                {{ $assessment->status === 'Published'
                    ? 'bg-success'
                    : 'bg-warning text-dark' }}">

                {{ $assessment->status === 'Published'
                    ? 'Dipublikasikan'
                    : 'Draft' }}
            </span>

        </div>

        <div class="border-top mt-4 pt-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div>
                    <h6 class="fw-bold mb-1">
                        Kelola Paket Asesmen
                    </h6>

                    <p class="assessment-subtitle mb-0">
                        Periksa soal, ilustrasi, dan jawaban
                        sebelum dibagikan kepada kelas.
                    </p>
                </div>

                <div class="assessment-actions">

                    {{-- DOWNLOAD PDF REGULER --}}
                    <a href="{{ route('guru.assessments.download-pdf', [
                        'assessment' => $assessment->id,
                        'format' => 'regular'
                    ]) }}"
                    class="btn btn-outline-primary assessment-action-btn">

                        <span class="material-symbols-outlined">
                            picture_as_pdf
                        </span>

                        PDF Soal Reguler
                    </a>

                    {{-- DOWNLOAD PDF EASY READ --}}
                    <a href="{{ route('guru.assessments.download-pdf', [
                        'assessment' => $assessment->id,
                        'format' => 'easy_read'
                    ]) }}"
                    class="btn btn-outline-success assessment-action-btn">

                        <span class="material-symbols-outlined">
                            picture_as_pdf
                        </span>

                        PDF Soal Modifikasi
                    </a>

                    <a href="{{ route('guru.assessments.edit', $assessment->id) }}"
                       class="btn btn-outline-primary assessment-action-btn">

                        <span class="material-symbols-outlined">
                            edit
                        </span>

                        {{ $assessment->status === 'Published'
                            ? 'Lihat Review'
                            : 'Edit Soal' }}
                    </a>

                    @if($assessment->status === 'Published')

                        <button type="button"
                                class="btn btn-success assessment-action-btn"
                                disabled>

                            <span class="material-symbols-outlined">
                                check_circle
                            </span>

                            Sudah Dipublikasikan
                        </button>

                    @else

                        <a href="{{ route('guru.assessments.edit', $assessment->id) }}#publikasi"
                           class="btn btn-success assessment-action-btn">

                            <span class="material-symbols-outlined">
                                publish
                            </span>

                            Publikasikan
                        </a>

                    @endif

                </div>

            </div>

        </div>

    </div>

    {{-- TUJUAN PEMBELAJARAN --}}

    <div class="assessment-card p-4 mb-4">

        <h5 class="assessment-heading mb-3">
            Tujuan Pembelajaran
        </h5>

        <p class="text-secondary question-text mb-0">
            {{ $assessment->tujuan_pembelajaran ?: 'Belum diisi.' }}
        </p>

    </div>

    {{-- PERINGATAN HASIL VALIDASI --}}

    @if(
        ($statusKesetaraan ?? null) === 'perlu_revisi'
        || (
            is_numeric($jumlahPerluRevisi ?? null)
            && $jumlahPerluRevisi > 0
        )
        || in_array(
            strtoupper((string) ($hasilValidasi ?? '')),
            ['TIDAK_VALID', 'TIDAK VALID'],
            true
        )
    )

        <div class="alert alert-warning rounded-3 mb-4">

            <strong>
                Asesmen memerlukan tinjauan guru.
            </strong>

            <p class="small mt-2 mb-0">
                Periksa soal, hasil validasi, kunci jawaban,
                scaffolding, dan gambar sebelum publikasi.
            </p>

        </div>

    @elseif(($statusKesetaraan ?? null) === 'setara')

        <div class="alert alert-success rounded-3 mb-4">

            <strong>
                Hasil evaluasi AI menyatakan kedua versi setara.
            </strong>

            <p class="small mt-1 mb-0">
                Guru tetap perlu memeriksa seluruh soal
                dan ilustrasi sebelum publikasi.
            </p>

        </div>

    @endif

    {{-- HASIL ASESMEN AI --}}

    <div class="assessment-card p-4 mb-4">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

            <div>
                <h5 class="assessment-heading mb-1">
                    Hasil Asesmen AI
                </h5>

                <p class="assessment-subtitle mb-0">
                    Pilih versi asesmen untuk melihat soal satu per satu.
                </p>
            </div>

            <div class="btn-group" role="group">

                <button type="button"
                        id="tabReguler"
                        class="btn btn-primary"
                        aria-pressed="true">
                    Soal Reguler
                </button>

                <button type="button"
                        id="tabAdaptif"
                        class="btn btn-outline-primary"
                        aria-pressed="false">
                    Soal Modifikasi
                </button>

            </div>

        </div>

        {{-- ======================================
             PANEL SOAL REGULER
             ====================================== --}}

        <div id="panelReguler" class="assessment-panel">

            @if(!empty($catatanReguler))

                <p class="text-muted small question-text mb-4">
                    {{ $catatanReguler }}
                </p>

            @endif

            @forelse($soalReguler as $index => $soal)

                <article
                    class="question-card {{ $index === 0 ? 'active' : '' }}"
                    data-version="reguler"
                    data-index="{{ $index }}">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

                        <span class="badge bg-primary px-3 py-2">
                            Soal {{ $index + 1 }}
                        </span>

                        <span class="text-muted small">
                            {{ ($soal['jenis'] ?? '') === 'pilihan_ganda'
                                ? 'Pilihan Ganda'
                                : 'Esai' }}

                            • {{ $soal['level_bloom'] ?? '-' }}
                        </span>

                    </div>

                    <h5 class="fw-semibold question-text mb-4">
                        {{ $soal['pertanyaan'] ?? 'Pertanyaan belum tersedia.' }}
                    </h5>

                    @if(!empty($soal['deskripsi_gambar']))

                        <div class="alert alert-light border rounded-3 small">
                            <strong>Deskripsi Ilustrasi:</strong>
                            {{ $soal['deskripsi_gambar'] }}
                        </div>

                    @endif

                    {{-- PILIHAN JAWABAN REGULER --}}
                    @if(!empty($soal['opsi']) && is_array($soal['opsi']))

                        <div class="regular-options mb-4">

                            @foreach($soal['opsi'] as $opsi)

                                <div class="question-choice">
                                    <span class="question-label">{{ $opsi['label'] ?? '-' }}</span>
                                    <span class="regular-option-text">{{ trim((string) ($opsi['teks'] ?? '')) }}</span>
                                </div>

                            @endforeach

                        </div>

                    @endif


                    {{-- KUNCI JAWABAN REGULER --}}

                    <details class="answer-box mt-4">

                        <summary class="fw-semibold">
                            Lihat Kunci Jawaban dan Pembahasan Guru
                        </summary>

                        <div class="mt-3">

                            <strong>Kunci Jawaban</strong>

                            <p class="question-text mt-2">
                                {{ $soal['kunci_jawaban'] ?? '-' }}
                            </p>

                            <strong>Pembahasan</strong>

                            <p class="question-text mt-2">
                                {{ $soal['pembahasan'] ?? '-' }}
                            </p>

                            @if(!empty($soal['pedoman_penilaian']))

                                <hr>

                                <strong>Pedoman Penilaian</strong>

                                <p class="question-text mt-2 mb-0">
                                    {{ $soal['pedoman_penilaian'] }}
                                </p>

                            @endif

                        </div>

                    </details>

                </article>

            @empty

                <div class="alert alert-secondary">
                    Belum ada soal Reguler yang tersimpan.
                </div>

            @endforelse

        </div>

        {{-- ======================================
             PANEL SOAL ADAPTIF
             ====================================== --}}

        <div id="panelAdaptif"
             class="assessment-panel easy-read-assessment"
             style="display:none;">

            @if(!empty($catatanAdaptif))

                <p class="text-muted small question-text mb-4">
                    {{ str_ireplace(
                        'inklusif',
                        'adaptif',
                        $catatanAdaptif
                    ) }}
                </p>

            @endif

            @forelse($soalAdaptif as $index => $soal)

                @php
                    /*
                     * Hubungkan gambar berdasarkan ID soal.
                     * Contoh: PG1, PG2, ES1.
                     */
                    $idSoalAdaptif = trim(
                        (string) (
                            $soal['id_soal_asal']
                            ?? $soal['id']
                            ?? ''
                        )
                    );

                    $ilustrasiSoal = $idSoalAdaptif !== ''
                        ? ($gambarPerSoal[$idSoalAdaptif] ?? [])
                        : [];

                    $rencanaVisual = is_array(
                        $soal['rencana_visual'] ?? null
                    )
                        ? $soal['rencana_visual']
                        : [];

                    $altPerVisual = [];

                    foreach ($rencanaVisual as $visual) {
                        if (!is_array($visual)) {
                            continue;
                        }

                        $idVisual = (string) (
                            $visual['id_visual'] ?? ''
                        );

                        if ($idVisual !== '') {
                            $altPerVisual[$idVisual] =
                                $visual['teks_alternatif']
                                ?? $visual['tujuan_visual']
                                ?? '';
                        }
                    }
                @endphp

                <article
                    class="question-card {{ $index === 0 ? 'active' : '' }}"
                    data-version="adaptif"
                    data-index="{{ $index }}">

                    {{-- NOMOR SOAL --}}

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">

                        <span class="badge bg-primary px-3 py-2">
                            Soal {{ $index + 1 }}
                        </span>

                        <span class="text-muted small">
                            {{ ($soal['jenis'] ?? '') === 'pilihan_ganda'
                                ? 'Pilihan Ganda'
                                : 'Esai' }}

                            • {{ $soal['level_bloom'] ?? '-' }}
                        </span>

                    </div>

                    {{-- GAMBAR DIFY PER SOAL --}}

                    @if(!empty($ilustrasiSoal))

                        <div class="adaptive-illustrations">

                            <h6 class="fw-bold mb-3">

                                <span class="material-symbols-outlined align-middle text-primary me-1">
                                    image
                                </span>

                                Ilustrasi Soal
                            </h6>

                            <div class="row g-3 justify-content-center">

                                @foreach($ilustrasiSoal as $gambar)

                                    @php
                                        $idVisualGambar = (string) (
                                            $gambar['id_visual'] ?? ''
                                        );

                                        $teksAlternatif =
                                            $gambar['teks_alternatif']
                                            ?? null;

                                        if (empty($teksAlternatif)) {
                                            $teksAlternatif =
                                                $altPerVisual[$idVisualGambar]
                                                ?? 'Ilustrasi untuk soal '
                                                    . $idSoalAdaptif;
                                        }
                                    @endphp

                                    <div class="{{ count($ilustrasiSoal) === 1
                                        ? 'col-12 col-lg-8'
                                        : 'col-12 col-md-6' }}">

                                        <div class="assessment-image-frame">

                                            <img
                                                src="{{ $gambar['url'] }}"
                                                alt="{{ $teksAlternatif }}"
                                                loading="lazy">

                                            <div class="assessment-image-caption">

                                                <strong>
                                                    Soal {{ $idSoalAdaptif }}
                                                </strong>

                                                @if($idVisualGambar !== '')

                                                    <span class="ms-1">
                                                        • {{ $idVisualGambar }}
                                                    </span>

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @elseif(!empty($rencanaVisual))

                        <div class="alert alert-light border rounded-3 mb-4">

                            <strong>
                                Ilustrasi untuk soal
                                {{ $idSoalAdaptif ?: $index + 1 }}
                                belum tersedia.
                            </strong>

                            <p class="small text-muted mt-2 mb-0">
                                Sistem memiliki rencana visual untuk soal ini,
                                tetapi belum ada gambar dengan ID soal
                                yang sesuai dalam output tersimpan.
                            </p>

                        </div>

                    @endif

                    {{-- PERTANYAAN EASY READ --}}

                    <h5 class="question-text easy-read-question">
                        {{ $soal['pertanyaan'] ?? 'Pertanyaan belum tersedia.' }}
                    </h5>

                    {{-- =====================================
                         PILIHAN JAWABAN EASY READ - DIPERBAIKI

                         HANYA SATU LOOP.
                         TIDAK ADA LOOP DI DALAM SPAN.
                         ===================================== --}}

                    @if(!empty($soal['opsi']) && is_array($soal['opsi']))

                        <div class="easy-read-options">

                            @foreach($soal['opsi'] as $opsi)

                                <div class="question-choice">
                                    <span class="question-label">{{ $opsi['label'] ?? '-' }}</span>
                                    <span class="easy-read-option">{{ trim((string) ($opsi['teks'] ?? '')) }}</span>
                                </div>

                            @endforeach

                        </div>

                    @endif

                    {{-- SCAFFOLDING ADAPTIF --}}

                    @if(
                        !empty($soal['dukungan_scaffolding'])
                        && is_array($soal['dukungan_scaffolding'])
                    )

                        <div class="scaffolding-box mb-4">

                            <h6 class="fw-bold mb-3">
                                Dukungan Belajar (Easy Read)
                            </h6>

                            <ol class="mb-0">

                                @foreach(
                                    $soal['dukungan_scaffolding']
                                    as $petunjuk
                                )

                                    <li class="scaffolding-step">
                                        {{ $petunjuk }}
                                    </li>

                                @endforeach

                            </ol>

                        </div>

                    @endif

                    {{-- DETAIL RENCANA VISUAL --}}

                    @if(!empty($rencanaVisual))

                        <details class="bg-light border rounded-3 p-3 mb-4">

                            <summary class="fw-semibold">
                                Detail Rencana Ilustrasi
                            </summary>

                            <div class="mt-3">

                                @foreach($rencanaVisual as $visual)

                                    @if(is_array($visual))

                                        <p class="small question-text mb-2">
                                            {{ $visual['teks_alternatif']
                                                ?? $visual['tujuan_visual']
                                                ?? 'Ilustrasi pendukung.' }}
                                        </p>

                                    @endif

                                @endforeach

                            </div>

                        </details>

                    @endif

                    {{-- KUNCI JAWABAN ADAPTIF --}}

                    <details class="answer-box mt-4">

                        <summary class="fw-semibold">
                            Lihat Kunci Jawaban dan Pembahasan Guru
                        </summary>

                        <div class="mt-3">

                            <strong>Kunci Jawaban</strong>

                            <p class="question-text mt-2">
                                {{ $soal['kunci_jawaban'] ?? '-' }}
                            </p>

                            <strong>Pembahasan</strong>

                            <p class="question-text mt-2">
                                {{ $soal['pembahasan'] ?? '-' }}
                            </p>

                            @if(!empty($soal['pedoman_penilaian']))

                                <hr>

                                <strong>Pedoman Penilaian</strong>

                                <p class="question-text mt-2 mb-0">
                                    {{ $soal['pedoman_penilaian'] }}
                                </p>

                            @endif

                        </div>

                    </details>

                </article>

            @empty

                <div class="alert alert-secondary">
                    Belum ada soal Modifikasi yang tersimpan.
                </div>

            @endforelse

        </div>

        {{-- NAVIGASI SOAL --}}

        <div id="questionNavigation"
             class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-top mt-4 pt-4">

            <button
                type="button"
                id="previousQuestion"
                class="btn btn-outline-secondary rounded-3">
                ← Sebelumnya
            </button>

            <span id="questionCounter"
                  class="text-secondary small fw-semibold">
                Soal 1
            </span>

            <button
                type="button"
                id="nextQuestion"
                class="btn btn-primary rounded-3">
                Berikutnya →
            </button>

        </div>

    </div>

    {{-- ======================================
         EVALUASI DAN VALIDASI AI
         ====================================== --}}

    <div class="assessment-card p-4 mb-4">

        <div class="d-flex align-items-center gap-2 mb-3">

            <span class="material-symbols-outlined text-primary">
                fact_check
            </span>

            <h5 class="assessment-heading mb-0">
                Evaluasi dan Validasi AI
            </h5>

        </div>

        <div class="row g-3 mb-3">

            <div class="col-md-6">

                <div class="bg-light rounded-3 p-3 h-100">

                    <div class="small text-muted mb-1">
                        Status Kesetaraan
                    </div>

                    <strong>
                        {{ ucfirst(str_replace(
                            '_',
                            ' ',
                            $statusKesetaraan ?? '-'
                        )) }}
                    </strong>

                </div>

            </div>

            <div class="col-md-6">

                <div class="bg-light rounded-3 p-3 h-100">

                    <div class="small text-muted mb-1">
                        Jumlah Soal Perlu Revisi
                    </div>

                    <strong>
                        {{ $jumlahPerluRevisi ?? '-' }}
                    </strong>

                </div>

            </div>

        </div>

        @if(!empty($hasilValidasi))

            <p class="small text-muted mb-3">

                Hasil validasi workflow:

                <strong>
                    {{ $hasilValidasi }}
                </strong>

            </p>

        @endif

        @if(!empty($statusValidasiGambar))

            <p class="small text-muted mb-3">

                Status validasi gambar:

                <strong>
                    {{ $statusValidasiGambar }}
                </strong>

            </p>

        @endif

        @if(!empty($evaluasi['ringkasan_evaluasi']))

            <p class="question-text mb-4">
                {{ str_ireplace(
                    'inklusif',
                    'adaptif',
                    $evaluasi['ringkasan_evaluasi']
                ) }}
            </p>

        @endif

        {{-- HASIL EVALUASI PER SOAL --}}

        @if(
            !empty($evaluasi['hasil_per_soal'])
            && is_array($evaluasi['hasil_per_soal'])
        )

            <h6 class="fw-bold mb-3">
                Hasil Evaluasi per Soal
            </h6>

            @foreach($evaluasi['hasil_per_soal'] as $hasil)

                <div class="evaluation-item">

                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">

                        <strong>
                            {{ $hasil['id_soal_asal'] ?? 'Soal' }}
                        </strong>

                        @if(($hasil['status_soal'] ?? '') === 'setara')

                            <span class="badge bg-success">
                                Setara
                            </span>

                        @elseif(
                            ($hasil['status_soal'] ?? '') === 'perlu_revisi'
                        )

                            <span class="badge bg-warning text-dark">
                                Perlu Revisi
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                {{ str_replace(
                                    '_',
                                    ' ',
                                    $hasil['status_soal'] ?? '-'
                                ) }}
                            </span>

                        @endif

                    </div>

                    @if(
                        !empty($hasil['masalah_kritis'])
                        && is_array($hasil['masalah_kritis'])
                    )

                        <div class="small mb-3">

                            <strong>
                                Masalah yang ditemukan:
                            </strong>

                            <ul class="mb-0 mt-2">

                                @foreach($hasil['masalah_kritis'] as $masalah)

                                    <li class="question-text mb-1">
                                        {{ str_ireplace(
                                            'inklusif',
                                            'adaptif',
                                            $masalah
                                        ) }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif

                    @if(
                        !empty($hasil['rekomendasi_revisi'])
                        && is_array($hasil['rekomendasi_revisi'])
                    )

                        <div class="small">

                            <strong>
                                Rekomendasi revisi:
                            </strong>

                            <ul class="mb-0 mt-2">

                                @foreach(
                                    $hasil['rekomendasi_revisi']
                                    as $rekomendasi
                                )

                                    <li class="question-text mb-1">
                                        {{ str_ireplace(
                                            'inklusif',
                                            'adaptif',
                                            $rekomendasi
                                        ) }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif

                </div>

            @endforeach

        @endif

        {{-- REKOMENDASI UMUM --}}

        @if(
            !empty($evaluasi['rekomendasi_umum'])
            && is_array($evaluasi['rekomendasi_umum'])
        )

            <h6 class="fw-bold mt-4 mb-3">
                Rekomendasi Umum
            </h6>

            <ul class="mb-0">

                @foreach(
                    $evaluasi['rekomendasi_umum']
                    as $rekomendasi
                )

                    <li class="question-text mb-2">
                        {{ str_ireplace(
                            'inklusif',
                            'adaptif',
                            $rekomendasi
                        ) }}
                    </li>

                @endforeach

            </ul>

        @endif

        <p class="small text-muted mt-4 mb-0">
            Evaluasi AI merupakan bahan tinjauan.
            Guru tetap bertanggung jawab memeriksa
            dan menyetujui soal sebelum publikasi.
        </p>

    </div>

    {{-- GAMBAR LAMA TANPA ID SOAL --}}

    @php
        $gambarTanpaId = array_values(
            array_filter(
                $gambarAsesmen ?? [],
                fn ($gambar) =>
                    is_array($gambar)
                    && empty($gambar['id_soal_asal'])
            )
        );
    @endphp

    @if(!empty($gambarTanpaId))

        <div class="assessment-card p-4 mb-4">

            <h5 class="assessment-heading mb-3">
                Gambar Lama yang Belum Terhubung ke Soal
            </h5>

            <p class="small text-muted">
                Gambar berikut tidak memiliki ID soal.
                Gambar tidak dipasangkan otomatis agar tidak tertukar.
            </p>

            <div class="row g-3">

                @foreach($gambarTanpaId as $gambar)

                    <div class="col-md-6">

                        <div class="assessment-image-frame">

                            <img
                                src="{{ $gambar['url'] }}"
                                alt="Ilustrasi asesmen tanpa ID soal"
                                loading="lazy">

                            <p class="assessment-image-caption mb-0">
                                {{ $gambar['filename']
                                    ?? 'Gambar asesmen' }}
                            </p>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    @endif

    {{-- NAVIGASI BAWAH --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">

        <a href="{{ route('guru.assessments.index') }}"
           class="btn btn-light border rounded-3 px-4 py-2">

            <span class="material-symbols-outlined align-middle me-1">
                arrow_back
            </span>

            Kembali ke Daftar
        </a>

        <a href="{{ route('guru.assessments.edit', $assessment->id) }}"
           class="btn btn-outline-primary rounded-3 px-4 py-2">

            <span class="material-symbols-outlined align-middle me-1">
                edit
            </span>

            {{ $assessment->status === 'Published'
                ? 'Lihat Review Guru'
                : 'Tinjau & Edit Soal' }}
        </a>

    </div>

</div>

{{-- ======================================
     JAVASCRIPT NAVIGASI REGULER / ADAPTIF
     ====================================== --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const tabReguler =
        document.getElementById('tabReguler');

    const tabAdaptif =
        document.getElementById('tabAdaptif');

    const panelReguler =
        document.getElementById('panelReguler');

    const panelAdaptif =
        document.getElementById('panelAdaptif');

    const previousButton =
        document.getElementById('previousQuestion');

    const nextButton =
        document.getElementById('nextQuestion');

    const counter =
        document.getElementById('questionCounter');

    const navigation =
        document.getElementById('questionNavigation');

    let activeVersion = 'reguler';
    let currentIndex = 0;

    function getQuestions() {

        const panel = activeVersion === 'reguler'
            ? panelReguler
            : panelAdaptif;

        return Array.from(
            panel.querySelectorAll('.question-card')
        );
    }

    function renderQuestions() {

        const questions = getQuestions();

        questions.forEach(function (question, index) {
            question.classList.toggle(
                'active',
                index === currentIndex
            );
        });

        if (questions.length === 0) {
            navigation.style.display = 'none';
            counter.textContent = 'Belum ada soal';
            return;
        }

        navigation.style.display = 'flex';

        counter.textContent =
            'Soal ' +
            (currentIndex + 1) +
            ' dari ' +
            questions.length;

        previousButton.disabled =
            currentIndex === 0;

        nextButton.disabled =
            currentIndex >= questions.length - 1;
    }

    function changeVersion(version) {

        activeVersion = version;
        currentIndex = 0;

        panelReguler.style.display =
            version === 'reguler'
                ? 'block'
                : 'none';

        panelAdaptif.style.display =
            version === 'adaptif'
                ? 'block'
                : 'none';

        tabReguler.className =
            version === 'reguler'
                ? 'btn btn-primary'
                : 'btn btn-outline-primary';

        tabAdaptif.className =
            version === 'adaptif'
                ? 'btn btn-primary'
                : 'btn btn-outline-primary';

        tabReguler.setAttribute(
            'aria-pressed',
            version === 'reguler'
                ? 'true'
                : 'false'
        );

        tabAdaptif.setAttribute(
            'aria-pressed',
            version === 'adaptif'
                ? 'true'
                : 'false'
        );

        renderQuestions();
    }

    tabReguler.addEventListener('click', function () {
        changeVersion('reguler');
    });

    tabAdaptif.addEventListener('click', function () {
        changeVersion('adaptif');
    });

    previousButton.addEventListener('click', function () {
        if (currentIndex > 0) {
            currentIndex--;
            renderQuestions();
        }
    });

    nextButton.addEventListener('click', function () {
        if (currentIndex < getQuestions().length - 1) {
            currentIndex++;
            renderQuestions();
        }
    });

    changeVersion('reguler');
});
</script>

@endsection
