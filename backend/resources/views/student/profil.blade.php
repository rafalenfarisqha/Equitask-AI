
@extends('layouts.app')

@section('title', 'Profil Siswa')

@push('styles')
<style>
    .profile-page {
        --pf-primary: #2563eb;
        --pf-primary-light: #eff6ff;
        --pf-text: #172033;
        --pf-muted: #718096;
        --pf-border: #e5eaf2;
        padding: 22px 20px 28px;
        color: var(--pf-text);
    }

    /* Judul halaman */
    .pf-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 22px;
    }

    .pf-heading-icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 15px;
        background: var(--pf-primary-light);
        color: var(--pf-primary);
    }

    .pf-heading-icon .material-symbols-outlined {
        font-size: 25px;
    }

    .pf-heading h2 {
        margin: 0;
        font-size: 21px;
        font-weight: 800;
        letter-spacing: -.5px;
    }

    .pf-heading p {
        margin: 4px 0 0;
        font-size: 12px;
        color: var(--pf-muted);
    }

    /* Kartu profil */
    .pf-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 17px;
        padding: 22px;
        margin-bottom: 26px;
        border: 1px solid #dbeafe;
        border-radius: 20px;
        background: linear-gradient(115deg, #eff6ff 0%, #f8fbff 60%, #ecfeff 100%);
    }

    .pf-hero::after {
        content: "";
        position: absolute;
        width: 150px;
        height: 150px;
        right: -65px;
        top: -85px;
        border-radius: 50%;
        background: rgba(59, 130, 246, .08);
        pointer-events: none;
    }

    .pf-avatar {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 76px;
        height: 76px;
        overflow: hidden;
        border-radius: 23px;
        background: linear-gradient(135deg, #2563eb, #0f766e);
        box-shadow: 0 8px 18px rgba(37, 99, 235, .2);
        color: #fff;
        font-size: 25px;
        font-weight: 800;
    }

    .pf-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pf-hero-info {
        min-width: 0;
        position: relative;
        z-index: 1;
    }

    .pf-hero-info h2 {
        font-size: 19px;
        line-height: 1.35;
        font-weight: 800;
        margin: 0;
        overflow-wrap: anywhere;
    }

    .pf-hero-info p {
        margin: 5px 0 10px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .pf-role {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 11px;
        font-weight: 700;
    }

    .pf-role .material-symbols-outlined {
        font-size: 15px;
    }

    /* Judul bagian */
    .pf-section {
        margin-bottom: 26px;
    }

    .pf-section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .pf-section-heading h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
    }

    .pf-section-heading span {
        font-size: 11px;
        color: var(--pf-muted);
    }

    /* Daftar informasi */
    .pf-list {
        overflow: hidden;
        border: 1px solid var(--pf-border);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .025);
    }

    .pf-info {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 15px 16px;
        min-height: 72px;
    }

    .pf-info + .pf-info {
        border-top: 1px solid var(--pf-border);
    }

    .pf-info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        border-radius: 13px;
        background: var(--pf-primary-light);
        color: var(--pf-primary);
    }

    .pf-info-icon .material-symbols-outlined {
        font-size: 22px;
    }

    .pf-info-content {
        flex: 1;
        min-width: 0;
    }

    .pf-info-label {
        margin-bottom: 5px;
        color: var(--pf-muted);
        font-size: 11px;
        font-weight: 600;
    }

    .pf-info-value {
        color: var(--pf-text);
        font-size: 13px;
        font-weight: 700;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    /* Pengaturan akun */
    .pf-action {
        display: flex;
        align-items: center;
        gap: 13px;
        width: 100%;
        padding: 15px 16px;
        color: var(--pf-text);
        text-align: left;
        text-decoration: none;
        background: #fff;
        border: 0;
        cursor: pointer;
        font: inherit;
    }

    .pf-action + .pf-action,
    .pf-list form {
        border-top: 1px solid var(--pf-border);
    }

    .pf-action .pf-info-content {
        flex: 1;
    }

    .pf-action-title {
        font-size: 13px;
        font-weight: 750;
    }

    .pf-action-description {
        margin-top: 4px;
        color: var(--pf-muted);
        font-size: 11px;
        line-height: 1.5;
    }

    .pf-action-arrow {
        flex-shrink: 0;
        color: #94a3b8;
        font-size: 21px;
    }

    .pf-logout {
        color: #dc2626;
    }

    .pf-logout .pf-info-icon {
        color: #dc2626;
        background: #fef2f2;
    }

    .pf-logout .pf-action-arrow {
        color: #dc2626;
    }

    .pf-action:focus-visible {
        outline: 3px solid #93c5fd;
        outline-offset: -3px;
    }

    .pf-action:active {
        background: #f8fafc;
    }

    .pf-footer {
        text-align: center;
        padding: 2px 0 12px;
    }

    .pf-footer strong {
        display: block;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
    }

    .pf-footer span {
        display: block;
        margin-top: 5px;
        color: #94a3b8;
        font-size: 11px;
    }

    @media (max-width: 420px) {
        .profile-page {
            padding: 18px 14px 24px;
        }

        .pf-hero {
            gap: 13px;
            padding: 17px 15px;
        }

        .pf-avatar {
            width: 64px;
            height: 64px;
            border-radius: 19px;
            font-size: 22px;
        }

        .pf-hero-info h2 {
            font-size: 17px;
        }

        .pf-info,
        .pf-action {
            padding: 13px 12px;
            gap: 11px;
        }

        .pf-info-icon {
            width: 39px;
            height: 39px;
        }
    }

    
/* Memberi jarak yang seimbang di sisi kiri dan kanan */
.profile-page {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    padding-left: 24px !important;
    padding-right: 24px !important;
    margin-left: auto;
    margin-right: auto;
}

/* Supaya tidak terlalu mepet di layar kecil */
@media (max-width: 576px) {
    .profile-page {
        padding-left: 16px !important;
        padding-right: 16px !important;
    }
}

</style>
@endpush

@section('content')
@php
    $studentName = $user->name ?? 'Siswa';
    $studentInitials = $initials ?? 'S';
    $studentSchool = $user->institution ?? null;
    $studentNisn = $user->nisn ?? null;
    $studentEmail = $user->email ?? null;
    $joinedDate = $user->created_at ?? null;
@endphp

<div class="profile-page">

    <!-- Header halaman -->
    <header class="pf-heading">
        <div class="pf-heading-icon">
            <span class="material-symbols-outlined">person</span>
        </div>
        <div>
            <h2>Profil Saya</h2>
            <p>Informasi dan pengaturan akunmu</p>
        </div>
    </header>

    <!-- Identitas siswa -->
    <section class="pf-hero" aria-label="Identitas siswa">
        <div class="pf-avatar">
            @if(!empty($user->avatar))
                <img
                    src="{{ asset('storage/' . $user->avatar) }}"
                    alt="Foto profil {{ $studentName }}"
                >
            @else
                {{ strtoupper($studentInitials ?: 'S') }}
            @endif
        </div>

        <div class="pf-hero-info">
            <h2>{{ $studentName }}</h2>
            <p>{{ $studentSchool ?: 'Sekolah belum diisi' }}</p>

            <span class="pf-role">
                <span class="material-symbols-outlined">school</span>
                Siswa EquiTask AI
            </span>
        </div>
    </section>

    <!-- Informasi pribadi -->
    <section class="pf-section">
        <div class="pf-section-heading">
            <h3>Informasi Pribadi</h3>
            <span>Data akunmu</span>
        </div>

        <div class="pf-list">
            <div class="pf-info">
                <div class="pf-info-icon">
                    <span class="material-symbols-outlined">badge</span>
                </div>
                <div class="pf-info-content">
                    <div class="pf-info-label">Nama Lengkap</div>
                    <div class="pf-info-value">{{ $studentName }}</div>
                </div>
            </div>

            <div class="pf-info">
                <div class="pf-info-icon">
                    <span class="material-symbols-outlined">fingerprint</span>
                </div>
                <div class="pf-info-content">
                    <div class="pf-info-label">NISN</div>
                    <div class="pf-info-value">
                        {{ $studentNisn ?: 'Belum diisi' }}
                    </div>
                </div>
            </div>

            <div class="pf-info">
                <div class="pf-info-icon">
                    <span class="material-symbols-outlined">mail</span>
                </div>
                <div class="pf-info-content">
                    <div class="pf-info-label">Email</div>
                    <div class="pf-info-value">
                        {{ $studentEmail ?: 'Belum ditambahkan' }}
                    </div>
                </div>
            </div>

            <div class="pf-info">
                <div class="pf-info-icon">
                    <span class="material-symbols-outlined">school</span>
                </div>
                <div class="pf-info-content">
                    <div class="pf-info-label">Asal Sekolah</div>
                    <div class="pf-info-value">
                        {{ $studentSchool ?: 'Belum diisi' }}
                    </div>
                </div>
            </div>

            <div class="pf-info">
                <div class="pf-info-icon">
                    <span class="material-symbols-outlined">calendar_month</span>
                </div>
                <div class="pf-info-content">
                    <div class="pf-info-label">Tanggal Bergabung</div>
                    <div class="pf-info-value">
                        @if($joinedDate)
                            {{ \Carbon\Carbon::parse($joinedDate)->locale('id')->translatedFormat('d F Y') }}
                        @else
                            Tidak tersedia
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pengaturan akun -->
    <section class="pf-section">
        <div class="pf-section-heading">
            <h3>Pengaturan Akun</h3>
        </div>

        <div class="pf-list">
            <div class="pf-action">
                <div class="pf-info-icon">
                    <span class="material-symbols-outlined">shield_lock</span>
                </div>
                <div class="pf-info-content">
                    <div class="pf-action-title">Keamanan Akun</div>
                    <div class="pf-action-description">
                        Jaga kerahasiaan kata sandimu.
                    </div>
                </div>
                <span class="material-symbols-outlined pf-action-arrow">
                    lock
                </span>
            </div>

            <form
                action="{{ route('logout') }}"
                method="POST"
                onsubmit="return confirm('Apakah kamu yakin ingin keluar dari akun?')"
            >
                @csrf

                <button type="submit" class="pf-action pf-logout">
                    <div class="pf-info-icon">
                        <span class="material-symbols-outlined">logout</span>
                    </div>

                    <div class="pf-info-content">
                        <div class="pf-action-title">Keluar dari Akun</div>
                        <div class="pf-action-description">
                            Akhiri sesi akunmu dengan aman.
                        </div>
                    </div>

                    <span class="material-symbols-outlined pf-action-arrow">
                        arrow_forward
                    </span>
                </button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="pf-footer">
        <strong>EquiTask AI</strong>
        <span>Platform Asesmen Adaptif</span>
    </footer>

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
