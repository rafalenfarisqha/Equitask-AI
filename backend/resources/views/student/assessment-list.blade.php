
@extends('layouts.app')

@section('title', 'Daftar Assessment')

@push('styles')
<style>
    .assessment-page {
        padding: 28px 24px 36px;
    }

    .assessment-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
    }

    .assessment-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 11px;
        border-radius: 999px;
        background: #edf4ff;
        color: #2563eb;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .assessment-heading h1 {
        font-size: clamp(24px, 4vw, 30px);
        line-height: 1.3;
        font-weight: 800;
        letter-spacing: -1px;
        margin-bottom: 8px;
        color: #0f172a;
    }

    .assessment-heading p {
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
        max-width: 520px;
        margin: 0;
    }

    .assessment-count {
        flex-shrink: 0;
        min-width: 76px;
        text-align: center;
        padding: 13px 15px;
        border: 1px solid #dbeafe;
        border-radius: 16px;
        background: #fff;
    }

    .assessment-count strong {
        display: block;
        font-size: 24px;
        line-height: 1.2;
        color: #2563eb;
    }

    .assessment-count span {
        font-size: 11px;
        color: #64748b;
        font-weight: 700;
    }

    .assessment-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .assessment-card {
        min-width: 0;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 21px;
        box-shadow: 0 8px 24px -18px rgba(15, 23, 42, .22);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .assessment-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px -18px rgba(37, 99, 235, .35);
    }

    .assessment-card-top {
        display: flex;
        align-items: flex-start;
        gap: 13px;
        margin-bottom: 18px;
    }

    .assessment-icon {
        width: 52px;
        height: 52px;
        flex-shrink: 0;
        display: grid;
        place-items: center;
        border-radius: 15px;
        color: #2563eb;
        background: #edf4ff;
    }

    .assessment-icon .material-symbols-outlined {
        font-size: 28px;
    }

    .assessment-card h2 {
        font-size: 16px;
        font-weight: 800;
        line-height: 1.5;
        color: #0f172a;
        margin: 1px 0 6px;
        overflow-wrap: anywhere;
    }

    .assessment-subject {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #64748b;
        font-size: 12px;
    }

    .assessment-divider {
        border: 0;
        border-top: 1px solid #f1f5f9;
        margin: 0 0 16px;
        opacity: 1;
    }

    .assessment-meta {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #64748b;
        font-size: 12px;
        margin-bottom: 19px;
    }

    .assessment-meta .material-symbols-outlined {
        font-size: 17px;
    }

    .assessment-btn {
        width: 100%;
        min-height: 44px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        padding: 11px 14px;
        border: none;
        border-radius: 12px;
        background: #2563eb;
        color: #fff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
        transition: background .2s ease;
    }

    .assessment-btn:hover {
        background: #1d4ed8;
        color: #fff;
    }

    .assessment-empty {
        padding: 44px 24px;
        text-align: center;
        border: 1px dashed #cbd5e1;
        border-radius: 20px;
        background: #fff;
    }

    .assessment-empty-icon {
        display: grid;
        place-items: center;
        width: 66px;
        height: 66px;
        margin: 0 auto 16px;
        background: #f1f5f9;
        border-radius: 20px;
        color: #64748b;
    }

    .assessment-empty-icon .material-symbols-outlined {
        font-size: 32px;
    }

    .assessment-empty h2 {
        font-size: 17px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .assessment-empty p {
        font-size: 13px;
        line-height: 1.7;
        color: #64748b;
        margin: 0;
    }

    @media (max-width: 640px) {
        .assessment-page {
            padding: 24px 16px 30px;
        }

        .assessment-heading {
            gap: 10px;
        }

        .assessment-heading h1 {
            font-size: 24px;
        }

        .assessment-count {
            min-width: 63px;
            padding: 11px 10px;
        }

        .assessment-count strong {
            font-size: 21px;
        }

        .assessment-list {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .assessment-card {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="topbar">
    <a href="{{ route('siswa.dashboard') }}"
       aria-label="Kembali ke dashboard"
       style="display:flex;align-items:center;color:#0f172a;text-decoration:none;">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>

    <div style="font-size:15px;font-weight:700;">Daftar Assessment</div>
</div>

<div class="assessment-page">
    <div class="assessment-heading">
        <div>
            <div class="assessment-eyebrow">
                <span class="material-symbols-outlined"
                      style="font-size:16px;">school</span>
                Ruang Belajar
            </div>

            <h1>Assessment untukmu</h1>
            <p>
                Pilih assessment yang tersedia untuk melihat petunjuk
                dan menentukan versi pengerjaan yang sesuai.
            </p>
        </div>

        <div class="assessment-count">
            <strong>{{ $assessments->count() }}</strong>
            <span>Assessment</span>
        </div>
    </div>

    @if ($assessments->isEmpty())
        <div class="assessment-empty">
            <div class="assessment-empty-icon">
                <span class="material-symbols-outlined">assignment_late</span>
            </div>

            <h2>Belum ada assessment</h2>
            <p>
                Belum ada assessment publik yang diterbitkan guru.
                Assessment akan muncul di sini setelah tersedia.
            </p>
        </div>
    @else
        <div class="assessment-list">
            @foreach ($assessments as $assessment)
                <article class="assessment-card">
                    <div class="assessment-card-top">
                        <div class="assessment-icon">
                            <span class="material-symbols-outlined">
                                {{ $assessment->icon ?: 'assignment' }}
                            </span>
                        </div>

                        <div style="min-width:0;">
                            <h2>{{ $assessment->nama }}</h2>

                            <div class="assessment-subject">
                                <span class="material-symbols-outlined"
                                      style="font-size:16px;">menu_book</span>
                                {{ $assessment->mapel ?: 'Mata pelajaran umum' }}
                            </div>
                        </div>
                    </div>

                    <hr class="assessment-divider">

                    <div class="assessment-meta">
                        <span class="material-symbols-outlined">event</span>

                        Diterbitkan
                        @if ($assessment->created_at)
                            {{ \Illuminate\Support\Carbon::parse($assessment->created_at)->format('d M Y') }}
                        @else
                            oleh guru
                        @endif
                    </div>

                    <a class="assessment-btn"
                       href="{{ route('siswa.assessment.instructions', ['id' => $assessment->id]) }}">
                        Lihat Petunjuk
                        <span class="material-symbols-outlined"
                              style="font-size:18px;">arrow_forward</span>
                    </a>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('bottom_nav')
<nav class="bottomnav" aria-label="Navigasi siswa">
    <a class="navitem" href="{{ route('siswa.dashboard') }}">
        <span class="material-symbols-outlined">home</span>
        <span>Beranda</span>
    </a>

    <a class="navitem active" href="{{ route('siswa.assessment') }}">
        <span class="material-symbols-outlined">assignment</span>
        <span>Assessment</span>
    </a>

    <a class="navitem" href="{{ route('siswa.riwayat') }}">
        <span class="material-symbols-outlined">history</span>
        <span>Riwayat</span>
    </a>

    <a class="navitem" href="{{ route('siswa.profil') }}">
        <span class="material-symbols-outlined">person</span>
        <span>Profil</span>
    </a>
</nav>
@endsection
