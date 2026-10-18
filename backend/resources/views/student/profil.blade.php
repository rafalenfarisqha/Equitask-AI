
@extends('layouts.app')

@section('title', 'Profil Siswa')

@push('styles')
<style>
    .profile-hero {
        text-align: center;
        padding: 28px 20px;
        background: linear-gradient(135deg, #eff6ff, #dbeafe 55%, #e0f2fe);
        border: 1px solid rgba(37, 99, 235, .12);
    }

    .profile-avatar {
        width: 88px;
        height: 88px;
        border-radius: 28px;
        margin: 0 auto 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 800;
        color: white;
        background: linear-gradient(135deg, #2563eb, #0f766e);
        box-shadow: 0 12px 24px -12px rgba(37, 99, 235, .65);
        overflow: hidden;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-role {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 10px;
        padding: 6px 11px;
        background: #dbeafe;
        color: #1d4ed8;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
    }

    .profile-section-title {
        font-size: 15px;
        font-weight: 800;
        margin: 24px 0 12px;
    }

    .profile-info {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 15px 0;
        border-bottom: 1px solid var(--border);
    }

    .profile-info:last-child {
        border-bottom: none;
    }

    .profile-info-icon {
        width: 42px;
        height: 42px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: var(--primary-soft);
        color: var(--primary);
    }

    .profile-info-label {
        font-size: 11px;
        color: var(--muted);
        font-weight: 700;
        margin-bottom: 5px;
    }

    .profile-info-value {
        font-size: 13px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .profile-action {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 0;
        color: var(--text);
        text-decoration: none;
    }

    .profile-action .action-text {
        flex: 1;
        font-size: 13px;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="topbar">
    <div class="profile-info-icon">
        <span class="material-symbols-outlined">person</span>
    </div>
    <div style="flex:1;">
        <h1>Profil Saya</h1>
        <div class="muted">Informasi akun siswa</div>
    </div>
</div>

<div class="px" style="padding-top:18px;">

    <!-- Kartu Identitas Siswa -->
    <div class="card profile-hero">
        <div class="profile-avatar">
            @if(!empty($user->avatar))
                <img src="{{ asset('storage/' . $user->avatar) }}"
                     alt="Foto profil {{ $user->name }}">
            @else
                {{ strtoupper($initials ?: 'S') }}
            @endif
        </div>

        <div style="font-size:20px;font-weight:800;line-height:1.4;">
            {{ $user->name }}
        </div>

        <div class="muted" style="margin-top:5px;">
            {{ $user->institution ?: 'Sekolah belum diisi' }}
        </div>

        <div class="profile-role">
            <span class="material-symbols-outlined" style="font-size:14px;">
                school
            </span>
            Siswa EquiTask AI
        </div>
    </div>

    <!-- Informasi Pribadi -->
    <div class="profile-section-title">Informasi Pribadi</div>

    <div class="card">
        <div class="profile-info">
            <div class="profile-info-icon">
                <span class="material-symbols-outlined">badge</span>
            </div>
            <div style="flex:1;">
                <div class="profile-info-label">Nama Lengkap</div>
                <div class="profile-info-value">{{ $user->name }}</div>
            </div>
        </div>

        <div class="profile-info">
            <div class="profile-info-icon">
                <span class="material-symbols-outlined">fingerprint</span>
            </div>
            <div style="flex:1;">
                <div class="profile-info-label">NISN</div>
                <div class="profile-info-value">
                    {{ $user->nisn ?: 'Belum diisi' }}
                </div>
            </div>
        </div>

        <div class="profile-info">
            <div class="profile-info-icon">
                <span class="material-symbols-outlined">mail</span>
            </div>
            <div style="flex:1;">
                <div class="profile-info-label">Email</div>
                <div class="profile-info-value">
                    {{ $user->email ?: 'Belum ditambahkan' }}
                </div>
            </div>
        </div>

        <div class="profile-info">
            <div class="profile-info-icon">
                <span class="material-symbols-outlined">school</span>
            </div>
            <div style="flex:1;">
                <div class="profile-info-label">Asal Sekolah</div>
                <div class="profile-info-value">
                    {{ $user->institution ?: 'Belum diisi' }}
                </div>
            </div>
        </div>

        <div class="profile-info">
            <div class="profile-info-icon">
                <span class="material-symbols-outlined">calendar_month</span>
            </div>
            <div style="flex:1;">
                <div class="profile-info-label">Tanggal Bergabung</div>
                <div class="profile-info-value">
                    {{ $user->created_at
                        ? \Carbon\Carbon::parse($user->created_at)->format('d F Y')
                        : 'Tidak tersedia' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Pengaturan Akun -->
    <div class="profile-section-title">Pengaturan Akun</div>

    <div class="card">
        <div class="profile-action">
            <div class="profile-info-icon">
                <span class="material-symbols-outlined">verified_user</span>
            </div>
            <div class="action-text">
                <div>Keamanan Akun</div>
                <div class="muted" style="margin-top:4px;font-weight:400;">
                    Jaga kerahasiaan kata sandimu.
                </div>
            </div>
            <span class="material-symbols-outlined" style="color:var(--muted);">
                lock
            </span>
        </div>

        <div style="border-top:1px solid var(--border);"></div>

        <form action="{{ route('logout') }}" method="POST"
              onsubmit="return confirm('Apakah kamu yakin ingin keluar dari akun?')">
            @csrf
            <button type="submit"
                    class="profile-action"
                    style="width:100%;border:0;background:none;text-align:left;cursor:pointer;">
                <div class="profile-info-icon"
                     style="background:#fee2e2;color:#b91c1c;">
                    <span class="material-symbols-outlined">logout</span>
                </div>

                <div class="action-text" style="color:#b91c1c;">
                    Keluar dari Akun
                </div>

                <span class="material-symbols-outlined" style="color:#b91c1c;">
                    arrow_forward
                </span>
            </button>
        </form>
    </div>

    <div style="text-align:center;padding:10px 0 24px;">
        <div class="muted">EquiTask AI</div>
        <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
            Platform Assessment Adaptif
        </div>
    </div>
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
