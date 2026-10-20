
@extends('layouts.app')

@section('title', 'Beranda Siswa')

@push('styles')
<style>
    .student-home {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px 22px 36px;
        color: #1f2937;
    }

    .student-home,
    .student-home * {
        box-sizing: border-box;
    }

    .student-home a {
        text-decoration: none;
    }

    /* Header */
    .sh-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 28px;
    }

    .sh-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .sh-avatar {
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #e8f0fe;
        color: #2459a9;
        font-size: 15px;
        font-weight: 700;
    }

    .sh-greeting {
        margin: 0 0 4px;
        font-size: 15px;
        line-height: 1.4;
        font-weight: 700;
        color: #1f2937;
    }

    .sh-subtitle {
        margin: 0;
        font-size: 13px;
        color: #6b7280;
        line-height: 1.5;
    }

    .sh-notification {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #4b5563;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        transition: background .2s, color .2s;
    }

    .sh-notification:hover {
        background: #f3f6fb;
        color: #2459a9;
    }

    /* Judul halaman */
    .sh-page-title {
        margin: 0 0 5px;
        font-size: 24px;
        line-height: 1.35;
        font-weight: 750;
        letter-spacing: -.4px;
    }

    .sh-page-description {
        margin: 0 0 22px;
        font-size: 14px;
        line-height: 1.65;
        color: #6b7280;
    }

    /* Ringkasan */
    .sh-summary {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 30px;
    }

    .sh-summary-card {
        display: flex;
        align-items: center;
        gap: 13px;
        min-width: 0;
        padding: 18px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        transition: border-color .2s;
    }

    .sh-summary-card:hover {
        border-color: #bfdbfe;
    }

    .sh-summary-icon {
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
    }

    .sh-summary-icon .material-symbols-outlined {
        font-size: 23px;
    }

    .sh-summary-label {
        margin-bottom: 3px;
        font-size: 12px;
        line-height: 1.5;
        color: #6b7280;
    }

    .sh-summary-number {
        font-size: 23px;
        line-height: 1.2;
        font-weight: 750;
        color: #111827;
    }

    /* Bagian konten */
    .sh-section {
        margin-top: 28px;
    }

    .sh-section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 13px;
    }

    .sh-section-title {
        margin: 0;
        font-size: 17px;
        line-height: 1.4;
        font-weight: 750;
        color: #1f2937;
    }

    .sh-section-link {
        flex-shrink: 0;
        font-size: 13px;
        font-weight: 600;
        color: #2563eb;
    }

    .sh-section-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    /* Assessment */
    .sh-assessment-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .sh-assessment-item {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 16px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        color: inherit;
        transition: border-color .2s, background .2s;
    }

    .sh-assessment-item:hover {
        border-color: #bfdbfe;
        background: #fbfdff;
    }

    .sh-item-icon {
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
    }

    .sh-item-icon .material-symbols-outlined {
        font-size: 23px;
    }

    .sh-item-content {
        flex: 1;
        min-width: 0;
    }

    .sh-item-title {
        margin: 0 0 5px;
        font-size: 14px;
        line-height: 1.5;
        font-weight: 700;
        color: #1f2937;
        overflow-wrap: anywhere;
    }

    .sh-item-meta {
        margin: 0;
        font-size: 12px;
        line-height: 1.5;
        color: #6b7280;
    }

    .sh-item-arrow {
        flex: 0 0 auto;
        color: #9ca3af;
    }

    /* Kondisi kosong */
    .sh-empty {
        padding: 32px 20px;
        text-align: center;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
    }

    .sh-empty-icon {
        width: 52px;
        height: 52px;
        margin: 0 auto 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #f3f4f6;
        color: #6b7280;
    }

    .sh-empty-icon .material-symbols-outlined {
        font-size: 27px;
    }

    .sh-empty-title {
        margin: 0 0 7px;
        font-size: 15px;
        font-weight: 700;
        color: #1f2937;
    }

    .sh-empty-description {
        max-width: 420px;
        margin: 0 auto 18px;
        font-size: 13px;
        line-height: 1.7;
        color: #6b7280;
    }

    .sh-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 10px 17px;
        border: 1px solid #2563eb;
        border-radius: 8px;
        background: #2563eb;
        color: #fff !important;
        font-size: 13px;
        font-weight: 650;
        transition: background .2s;
    }

    .sh-button:hover {
        background: #1d4ed8;
    }

    /* Kelas */
    .sh-class-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .sh-class-item {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 15px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
    }

    .sh-class-icon {
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #ecfdf5;
        color: #047857;
    }

    .sh-class-icon .material-symbols-outlined {
        font-size: 23px;
    }

    .sh-class-content {
        flex: 1;
        min-width: 0;
    }

    .sh-class-name {
        margin: 0 0 4px;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
        color: #1f2937;
    }

    .sh-class-description {
        margin: 0;
        font-size: 12px;
        line-height: 1.6;
        color: #6b7280;
    }

    /* Responsif */
    @media (max-width: 576px) {
        .student-home {
            padding: 17px 15px 30px;
        }

        .sh-page-title {
            font-size: 21px;
        }

        .sh-summary {
            gap: 10px;
        }

        .sh-summary-card {
            align-items: flex-start;
            flex-direction: column;
            gap: 10px;
            padding: 13px;
        }

        .sh-summary-number {
            font-size: 21px;
        }

        .sh-section {
            margin-top: 25px;
        }

        .sh-empty {
            padding: 26px 15px;
        }

        .sh-button {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
@php
    $studentName = $user->name ?? 'Siswa';
    $studentInitials = $initials ?? 'S';
@endphp

<div class="student-home">

    {{-- Header profil --}}
    <header class="sh-header">
        <div class="sh-profile">
            <div class="sh-avatar">
                {{ strtoupper($studentInitials ?: 'S') }}
            </div>

            <div>
                <p class="sh-greeting">
                    Halo, {{ $studentName }}
                </p>

                <p class="sh-subtitle">
                    @if($classes->isNotEmpty())
                        {{ $classes->pluck('name')->join(', ') }}
                    @else
                        Belum bergabung ke kelas
                    @endif
                </p>
            </div>
        </div>

        <a href="{{ route('siswa.notifikasi') }}"
           class="sh-notification"
           aria-label="Buka notifikasi">
            <span class="material-symbols-outlined">notifications</span>
        </a>
    </header>

    {{-- Judul halaman --}}
    <section>
        <h1 class="sh-page-title">Beranda</h1>
        <p class="sh-page-description">
            Lihat assessment yang tersedia dan kelas tempat kamu belajar.
        </p>
    </section>

    {{-- Ringkasan: informasi hanya ditampilkan satu kali --}}
    <section class="sh-summary" aria-label="Ringkasan aktivitas siswa">

        <div class="sh-summary-card">
            <div class="sh-summary-icon">
                <span class="material-symbols-outlined">assignment</span>
            </div>

            <div>
                <div class="sh-summary-label">Assessment tersedia</div>
                <div class="sh-summary-number">{{ $totalAssessment }}</div>
            </div>
        </div>

        <div class="sh-summary-card">
            <div class="sh-summary-icon">
                <span class="material-symbols-outlined">school</span>
            </div>

            <div>
                <div class="sh-summary-label">Kelas diikuti</div>
                <div class="sh-summary-number">{{ $totalKelas }}</div>
            </div>
        </div>

    </section>

    {{-- Daftar assessment --}}
    <section class="sh-section">
        <div class="sh-section-heading">
            <h2 class="sh-section-title">Assessment terbaru</h2>

            <a href="{{ route('siswa.assessment') }}"
               class="sh-section-link">
                Lihat semua
            </a>
        </div>

        @if($assessments->isNotEmpty())
            <div class="sh-assessment-list">
                @foreach($assessments->take(3) as $assessment)
                    <a href="{{ route('siswa.assessment') }}"
                       class="sh-assessment-item">

                        <div class="sh-item-icon">
                            <span class="material-symbols-outlined">
                                {{ $assessment->icon ?: 'assignment' }}
                            </span>
                        </div>

                        <div class="sh-item-content">
                            <p class="sh-item-title">
                                {{ $assessment->nama }}
                            </p>

                            <p class="sh-item-meta">
                                {{ $assessment->mapel ?: 'Mata pelajaran belum dicantumkan' }}
                            </p>
                        </div>

                        <span class="material-symbols-outlined sh-item-arrow">
                            chevron_right
                        </span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="sh-empty">
                <div class="sh-empty-icon">
                    <span class="material-symbols-outlined">assignment</span>
                </div>

                <h3 class="sh-empty-title">
                    Belum ada assessment
                </h3>

                <p class="sh-empty-description">
                    Assessment yang sudah diterbitkan oleh guru akan muncul di sini.
                    Silakan periksa kembali nanti.
                </p>

                <a href="{{ route('siswa.assessment') }}"
                   class="sh-button">
                    <span class="material-symbols-outlined" style="font-size:18px;">
                        visibility
                    </span>
                    Lihat daftar assessment
                </a>
            </div>
        @endif
    </section>

    {{-- Daftar kelas: tidak ada statistik kelas yang diulang --}}
    <section class="sh-section">
        <div class="sh-section-heading">
            <h2 class="sh-section-title">Kelas saya</h2>
        </div>

        @if($classes->isNotEmpty())
            <div class="sh-class-list">
                @foreach($classes as $class)
                    <div class="sh-class-item">
                        <div class="sh-class-icon">
                            <span class="material-symbols-outlined">menu_book</span>
                        </div>

                        <div class="sh-class-content">
                            <p class="sh-class-name">
                                {{ $class->name }}
                            </p>

                            <p class="sh-class-description">
                                {{ $class->description ?: 'Kelas belajar EquiTask AI' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="sh-empty">
                <div class="sh-empty-icon">
                    <span class="material-symbols-outlined">school</span>
                </div>

                <h3 class="sh-empty-title">
                    Kamu belum bergabung ke kelas
                </h3>

                <p class="sh-empty-description">
                    Kelas akan ditampilkan di sini setelah guru menambahkanmu.
                </p>
            </div>
        @endif
    </section>

</div>
@endsection

@section('bottom_nav')
<div class="bottomnav">
    <a href="{{ route('siswa.dashboard') }}"
       class="navitem {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">home</span>
        Beranda
    </a>

    <a href="{{ route('siswa.assessment') }}"
       class="navitem {{ request()->routeIs('siswa.assessment') ? 'active' : '' }}"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">assignment</span>
        Assessment
    </a>

    <a href="{{ route('siswa.riwayat') }}"
       class="navitem {{ request()->routeIs('siswa.riwayat') ? 'active' : '' }}"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">history</span>
        Riwayat
    </a>

    <a href="{{ route('siswa.profil') }}"
       class="navitem {{ request()->routeIs('siswa.profil') ? 'active' : '' }}"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">person</span>
        Profil
    </a>
</div>
@endsection
