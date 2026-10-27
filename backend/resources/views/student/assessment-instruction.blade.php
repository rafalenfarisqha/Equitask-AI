@extends('layouts.app')

@section('title', 'Petunjuk Asesmen — EquiTask AI')

@section('content')

<div class="eq-page">
    <div class="eq-container">

        <!-- KEMBALI -->
        <a href="{{ route('siswa.assessment') }}" class="eq-back">
            <span aria-hidden="true">←</span>
            Kembali ke daftar asesmen
        </a>

        <!-- JUDUL -->
        <header class="eq-heading">
            <div class="eq-label">
                PERSIAPAN ASESMEN
            </div>

            <h1>Siap untuk belajar? ✨</h1>

            <p>
                Bacalah petunjuk berikut sebelum
                memulai asesmenmu.
            </p>
        </header>

        <!-- PESAN ERROR -->
        @if(session('error'))
            <div class="eq-alert" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="eq-alert" role="alert">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- INFORMASI ASESMEN -->
        <section class="eq-summary">

            <div class="eq-subject-icon">
                📘
            </div>

            <div class="eq-summary-main">
                <span class="eq-muted">
                    {{ $assessment->mapel ?? 'Mata Pelajaran' }}
                </span>

                <h2>
                    {{ $assessment->nama }}
                </h2>

                <p>
                    Pengajar:
                    {{ $assessment->teacher_name ?? 'Guru' }}
                </p>
            </div>

            <span class="eq-status">
                Dipublikasikan
            </span>

            <!-- STATISTIK -->
            <div class="eq-stats">

                <div>
                    <strong>
                        {{ $questionCount ?? 0 }}
                    </strong>
                    <span>Jumlah soal</span>
                </div>

                <div>
                    <strong>30 menit</strong>
                    <span>Estimasi waktu</span>
                </div>

            </div>

            <p class="eq-note">
                Soal telah disiapkan sesuai pengaturan
                pembelajaran yang ditentukan oleh guru.
                Kamu tidak perlu memilih versi soal.
            </p>
        </section>

        <!-- FITUR -->
        <section class="eq-feature">
            <h3>Fitur yang tersedia</h3>

            <div class="eq-feature-grid">

                <div>
                    <span>✓</span>
                    Dukungan teks besar
                </div>

                <div>
                    <span>✓</span>
                    Mode fokus
                </div>

                <div>
                    <span>✓</span>
                    Navigasi soal
                </div>

                <div>
                    <span>✓</span>
                    Jawaban disimpan saat dikumpulkan
                </div>

            </div>
        </section>

        <!-- SIAP MENGERJAKAN -->
        <section class="eq-ready">

            <div class="eq-ready-icon">
                📝
            </div>

            <h3>
                Asesmenmu sudah siap!
            </h3>

            <p>
                Kamu akan mengerjakan soal yang telah
                disiapkan sesuai kebutuhan belajarmu.
            </p>

            <div class="eq-ready-info">
                <span>✓</span>
                Jenis soal ditentukan otomatis oleh sistem.
            </div>

        </section>

        <!-- TIPS -->
        <div class="eq-reminder">

            <span aria-hidden="true">💡</span>

            <div>
                <strong>
                    Tips sebelum mulai
                </strong>

                <p>
                    Bacalah setiap soal dengan teliti.
                    Kerjakan sesuai kemampuanmu dan
                    periksa kembali jawaban sebelum
                    dikumpulkan.
                </p>
            </div>

        </div>

        <!--
            SATU TOMBOL MULAI ASESMEN

            Tidak ada radio Reguler / Adaptif.
            Controller memilih soal otomatis
            berdasarkan profil siswa di kelas.
        -->
        <form
            method="GET"
            action="{{ route('siswa.assessment.start', $assessment->id) }}"
            id="startAssessmentForm"
        >

            <button
                type="submit"
                id="startButton"
                class="eq-start"
            >
                Mulai Asesmen
                <span aria-hidden="true">→</span>
            </button>

        </form>

    </div>
</div>

<style>
    .eq-page {
        min-height: 100vh;
        padding: 34px 18px 60px;
        background: #f4f7ff;
        color: #17233e;
        font-family: Inter, ui-sans-serif, system-ui, sans-serif;
    }

    .eq-container {
        max-width: 850px;
        margin: auto;
    }

    .eq-back {
        display: inline-flex;
        gap: 10px;
        align-items: center;
        color: #52617d;
        text-decoration: none;
        font-size: 14px;
        margin-bottom: 26px;
    }

    .eq-back:hover {
        color: #2563eb;
    }

    .eq-heading {
        margin-bottom: 24px;
    }

    .eq-label {
        color: #2563eb;
        font-weight: 800;
        font-size: 11px;
        letter-spacing: 1.6px;
    }

    .eq-heading h1 {
        margin: 9px 0;
        font-size: clamp(25px, 4vw, 34px);
        letter-spacing: -1px;
        line-height: 1.3;
    }

    .eq-heading p {
        color: #73809a;
        margin: 0;
        font-size: 14px;
        line-height: 1.7;
    }

    .eq-summary,
    .eq-feature,
    .eq-ready,
    .eq-reminder {
        background: #fff;
        border: 1px solid #e6ebf5;
        border-radius: 20px;
        box-shadow: 0 8px 28px rgba(29, 53, 87, 0.04);
    }

    .eq-summary {
        padding: 24px;
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 14px;
    }

    .eq-subject-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 15px;
        background: #edf3ff;
        font-size: 23px;
    }

    .eq-summary-main {
        min-width: 0;
    }

    .eq-muted {
        font-size: 12px;
        color: #71809a;
    }

    .eq-summary h2 {
        margin: 4px 0;
        font-size: 20px;
        overflow-wrap: anywhere;
    }

    .eq-summary-main p {
        color: #71809a;
        margin: 5px 0 0;
        font-size: 13px;
    }

    .eq-status {
        align-self: start;
        padding: 7px 10px;
        border-radius: 30px;
        color: #087f65;
        background: #e7f8f2;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .eq-stats {
        grid-column: 1 / -1;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        border-top: 1px solid #edf0f7;
        padding-top: 18px;
        margin-top: 6px;
    }

    .eq-stats div {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .eq-stats strong {
        font-size: 17px;
    }

    .eq-stats span {
        color: #75829a;
        font-size: 12px;
    }

    .eq-note {
        grid-column: 1 / -1;
        color: #8792a7;
        font-size: 12px;
        line-height: 1.7;
        margin: 0;
    }

    .eq-feature {
        padding: 22px;
        margin: 18px 0 24px;
    }

    .eq-feature h3 {
        font-size: 16px;
        margin: 0 0 16px;
    }

    .eq-feature-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        font-size: 13px;
    }

    .eq-feature-grid > div {
        line-height: 1.6;
    }

    .eq-feature-grid span {
        color: #0d9488;
        font-weight: 900;
        margin-right: 8px;
    }

    .eq-ready {
        padding: 28px 22px;
        text-align: center;
        margin-bottom: 20px;
    }

    .eq-ready-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 14px;
        border-radius: 18px;
        background: #edf3ff;
        display: grid;
        place-items: center;
        font-size: 29px;
    }

    .eq-ready h3 {
        font-size: 19px;
        margin: 0 0 10px;
    }

    .eq-ready p {
        color: #71809a;
        font-size: 14px;
        line-height: 1.8;
        max-width: 500px;
        margin: 0 auto 18px;
    }

    .eq-ready-info {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 11px 16px;
        border-radius: 12px;
        background: #edf8f4;
        color: #087f65;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.6;
    }

    .eq-ready-info span {
        font-weight: 900;
    }

    .eq-reminder {
        display: flex;
        gap: 12px;
        padding: 16px;
        margin: 20px 0;
    }

    .eq-reminder > span {
        font-size: 22px;
        flex-shrink: 0;
    }

    .eq-reminder strong {
        font-size: 13px;
    }

    .eq-reminder p {
        color: #71809a;
        margin: 5px 0 0;
        font-size: 12px;
        line-height: 1.6;
    }

    .eq-start {
        border: 0;
        width: 100%;
        padding: 17px;
        border-radius: 14px;
        background: #2563eb;
        color: #fff;
        font-size: 15px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 7px 16px rgba(37, 99, 235, 0.15);
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .eq-start:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .eq-start:focus-visible {
        outline: 3px solid #93c5fd;
        outline-offset: 3px;
    }

    .eq-start:disabled {
        background: #aebbd3;
        box-shadow: none;
        cursor: not-allowed;
    }

    .eq-start span {
        margin-left: 8px;
    }

    .eq-alert {
        padding: 14px;
        border-radius: 12px;
        background: #fff4e5;
        color: #8b4b00;
        margin-bottom: 18px;
        font-size: 13px;
    }

    @media (max-width: 560px) {
        .eq-page {
            padding: 22px 14px 40px;
        }

        .eq-summary {
            padding: 18px;
            grid-template-columns: auto 1fr;
        }

        .eq-status {
            grid-column: 1 / -1;
            justify-self: start;
        }

        .eq-feature-grid {
            grid-template-columns: 1fr;
        }

        .eq-stats {
            gap: 8px;
        }

        .eq-stats strong {
            font-size: 15px;
        }

        .eq-stats span {
            font-size: 11px;
        }

        .eq-ready {
            padding: 22px 16px;
        }

        .eq-ready-info {
            text-align: left;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('startAssessmentForm');
        const button = document.getElementById('startButton');

        if (!form || !button) {
            return;
        }

        form.addEventListener('submit', function () {
            button.disabled = true;
            button.textContent = 'Membuka asesmen...';
        });
    });
</script>

@endsection
