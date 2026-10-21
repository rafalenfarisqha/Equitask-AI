
@extends('layouts.app')

@section('title', 'Hasil Asesmen')

@section('content')

<style>
    .result-page {
        min-height: 85vh;
        padding: 36px 20px 80px;
        background: #f5f8ff;
        color: #17233d;
    }

    .result-container {
        max-width: 850px;
        margin: 0 auto;
    }

    .result-card {
        background: #ffffff;
        border: 1px solid #e1e8f5;
        border-radius: 24px;
        padding: 32px;
        box-shadow: 0 12px 36px rgba(25, 50, 90, .05);
    }

    .result-center {
        text-align: center;
    }

    .result-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 74px;
        height: 74px;
        margin: 0 auto 18px;
        background: #eaf2ff;
        border-radius: 22px;
        font-size: 35px;
    }

    .result-title {
        margin: 0 0 10px;
        font-size: 30px;
        font-weight: 800;
    }

    .result-subtitle {
        color: #64748b;
        font-size: 15px;
        line-height: 1.8;
    }

    .result-score-label {
        margin-top: 24px;
        font-size: 14px;
        font-weight: 700;
        color: #64748b;
    }

    .result-score {
        margin: 10px 0;
        font-size: clamp(45px, 8vw, 76px);
        font-weight: 800;
        line-height: 1.2;
        color: #2563eb;
    }

    .result-category {
        display: inline-block;
        margin-top: 12px;
        padding: 9px 18px;
        border-radius: 99px;
        background: #eaf2ff;
        color: #2458c8;
        font-size: 14px;
        font-weight: 800;
    }

    .result-section-title {
        margin: 28px 0 15px;
        font-size: 19px;
        font-weight: 800;
    }

    .result-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .result-stat {
        padding: 23px 12px;
        border: 1px solid #e0e8f5;
        border-radius: 18px;
        background: white;
        text-align: center;
    }

    .result-stat-number {
        font-size: 38px;
        font-weight: 800;
    }

    .result-stat-label {
        margin-top: 7px;
        color: #64748b;
        font-size: 13px;
    }

    .result-correct {
        color: #0d9488;
    }

    .result-wrong {
        color: #dc5261;
    }

    .result-empty {
        color: #b7791f;
    }

    .result-feedback {
        margin-top: 25px;
        padding: 24px;
        border: 1px solid #bfdbfe;
        border-left: 5px solid #2563eb;
        border-radius: 18px;
        background: #eff6ff;
    }

    .result-feedback h2 {
        margin: 0 0 12px;
        color: #1d4ed8;
        font-size: 19px;
        font-weight: 800;
    }

    .result-feedback p {
        margin: 0;
        font-size: 16px;
        line-height: 1.85;
    }

    .result-essay {
        margin-top: 22px;
        padding: 23px;
        background: #ffffff;
        border: 1px solid #dbe5f2;
        border-radius: 18px;
    }

    .result-essay h2 {
        margin: 0 0 13px;
        font-size: 18px;
        font-weight: 800;
    }

    .result-essay p {
        color: #64748b;
        font-size: 14px;
        line-height: 1.8;
    }

    .result-essay-details {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        margin-top: 15px;
    }

    .result-pill {
        padding: 10px 13px;
        background: #f1f5f9;
        border-radius: 10px;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    .result-actions {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 30px;
    }

    .result-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 14px 22px;
        min-height: 48px;
        border-radius: 12px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 800;
    }

    .result-button-primary {
        background: #2563eb;
        color: white;
    }

    .result-button-secondary {
        background: white;
        color: #334155;
        border: 1px solid #dbe5f2;
    }

    .result-note {
        margin-top: 22px;
        color: #718096;
        font-size: 12px;
        line-height: 1.7;
        text-align: center;
    }

    @media (max-width: 575px) {
        .result-page {
            padding: 20px 12px 60px;
        }

        .result-card {
            padding: 26px 17px;
        }

        .result-title {
            font-size: 25px;
        }

        .result-stats {
            gap: 8px;
        }

        .result-stat {
            padding: 18px 7px;
        }

        .result-stat-number {
            font-size: 29px;
        }

        .result-stat-label {
            font-size: 11px;
        }

        .result-feedback,
        .result-essay {
            padding: 19px;
        }
    }
</style>

@php
    /*
     * Data berasal dari AssessmentController siswa.
     * Tidak ada perhitungan nilai baru di Blade.
     */

    $nilaiPilihanGanda = $nilaiPG ?? null;

    $adaEsai = $hasEssay ?? false;

    $jumlahBenar = $benar ?? 0;
    $jumlahSalah = $salah ?? 0;
    $jumlahKosong = $belumDijawab ?? 0;

    $totalEsai = $jumlahEsai ?? 0;
    $totalEsaiTerisi = $esaiTerisi ?? 0;
    $totalEsaiKosong = $esaiBelumTerisi ?? 0;

    $kategori = $kategoriHasil ?? 'Hasil Pengerjaan';

    $motivasi = $kalimatHasil
        ?? 'Terima kasih telah menyelesaikan asesmen. Terus semangat belajar!';
@endphp

<div class="result-page">

    <div class="result-container">

        {{-- KARTU HASIL UTAMA --}}

        <div class="result-card result-center">

            <div class="result-icon" aria-hidden="true">
                🎓
            </div>

            <h1 class="result-title">
                Asesmen Selesai!
            </h1>

            <p class="result-subtitle">
                Terima kasih sudah menyelesaikan asesmen.
                Berikut ringkasan hasil belajarmu.
            </p>

            <div class="result-score-label">
                @if($adaEsai)
                    Nilai Sementara Pilihan Ganda
                @else
                    Nilai Pilihan Ganda
                @endif
            </div>

            @if($nilaiPilihanGanda !== null)

                <div class="result-score">
                    {{ number_format($nilaiPilihanGanda, 2, ',', '.') }}
                    <span style="font-size:20px">
                        / 100
                    </span>
                </div>

            @else

                <div
                    class="result-score"
                    style="font-size:32px">
                    Menunggu Penilaian
                </div>

            @endif

            <span class="result-category">
                {{ $kategori }}
            </span>

        </div>

        {{-- RINGKASAN PILIHAN GANDA --}}

        <h2 class="result-section-title">
            Ringkasan Pilihan Ganda
        </h2>

        <div class="result-stats">

            <div class="result-stat">
                <div class="result-stat-number result-correct">
                    {{ $jumlahBenar }}
                </div>

                <div class="result-stat-label">
                    Jawaban Benar
                </div>
            </div>

            <div class="result-stat">
                <div class="result-stat-number result-wrong">
                    {{ $jumlahSalah }}
                </div>

                <div class="result-stat-label">
                    Jawaban Salah
                </div>
            </div>

            <div class="result-stat">
                <div class="result-stat-number result-empty">
                    {{ $jumlahKosong }}
                </div>

                <div class="result-stat-label">
                    Belum Dijawab
                </div>
            </div>

        </div>

        {{-- MOTIVASI DARI BACKEND --}}

        <div class="result-feedback">

            <h2>
                💡 Catatan Belajarmu
            </h2>

            <p>
                {{ $motivasi }}
            </p>

        </div>

        {{-- ESAI MENUNGGU GURU --}}

        @if($adaEsai)

            <div class="result-essay">

                <h2>
                    📝 Esai Menunggu Koreksi Guru
                </h2>

                <p>
                    {{ $catatanEsai
                        ?? 'Jawaban esaimu akan diperiksa oleh guru.' }}

                    Nilai keseluruhan belum final.
                    Tetap semangat belajar, ya!
                </p>

                <div class="result-essay-details">

                    <span class="result-pill">
                        Total esai:
                        {{ $totalEsai }}
                    </span>

                    <span class="result-pill">
                        Sudah diisi:
                        {{ $totalEsaiTerisi }}
                    </span>

                    <span class="result-pill">
                        Belum diisi:
                        {{ $totalEsaiKosong }}
                    </span>

                </div>

            </div>

        @endif

        {{-- NAVIGASI --}}

        <div class="result-actions">

            <a
                href="{{ route('siswa.dashboard') }}"
                class="result-button result-button-primary">
                Kembali ke Dashboard
            </a>

            <a
                href="{{ route('siswa.riwayat') }}"
                class="result-button result-button-secondary">
                Lihat Riwayat
            </a>

        </div>

        <p class="result-note">
            @if($adaEsai)
                Hasil pilihan ganda diperiksa otomatis.
                Skor keseluruhan masih menunggu koreksi esai oleh guru.
            @else
                Hasil dihitung berdasarkan jawaban pilihan ganda
                yang telah kamu kumpulkan.
            @endif
        </p>

    </div>

</div>

@endsection
