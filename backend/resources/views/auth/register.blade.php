@extends('layouts.app')

@section('title', 'Daftar Akun — EquiTask AI')

@push('styles')
<style>
    .register-hero {
        background: linear-gradient(145deg, #2c63e6 0%, #3b82f6 48%, #0f766e 100%);
        padding: 40px 24px 28px;
        color: #fff;
        border-radius: 0 0 32px 32px;
        margin: -16px -20px 0;
    }
    .role-switch {
        display: flex;
        background: var(--card);
        border-radius: 14px;
        padding: 4px;
        margin: 22px 0 16px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
    }
    .role-switch button {
        flex: 1;
        border: none;
        background: none;
        padding: 10px;
        border-radius: 11px;
        font-weight: 700;
        font-size: 13px;
        color: var(--muted);
        font-family: inherit;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }
    .role-switch button.active {
        background: var(--primary);
        color: #fff;
    }
    .auth-footer {
        text-align: center;
        font-size: 13px;
        margin-top: 20px;
        color: var(--muted);
    }
    .auth-footer a {
        color: var(--primary);
        font-weight: 700;
        text-decoration: none;
    }
</style>
@endpush

@section('content')
<div class="px">
    <div class="register-hero">
        <h2 style="margin:0; font-size: 22px;">Buat Akun Baru</h2>
        <p style="margin:4px 0 0; font-size: 13px; opacity: 0.9;">Bergabung dengan EquiTask AI untuk evaluasi inklusif</p>
    </div>

    <!-- Pilihan Role Pendaftaran -->
    <div class="role-switch">
        <button id="rs-guru" class="active" onclick="setRegisterRole('guru')">Guru</button>
        <button id="rs-siswa" onclick="setRegisterRole('siswa')">Siswa</button>
    </div>

    <form action="#" method="POST">
        @csrf

        <!-- Input Nama Lengkap -->
        <span class="field-label">Nama Lengkap</span>
        <input class="input" type="text" placeholder="Masukkan nama lengkap..." required>

        <!-- Input Dinamis Berdasarkan Role -->
        <div id="dynamic-field">
            <span class="field-label">Email Institusi / Pribadi</span>
            <input class="input" type="email" placeholder="nama@sekolah.sch.id" required>
        </div>

        <!-- Input Password -->
        <span class="field-label">Password</span>
        <input class="input" type="password" placeholder="••••••••" required>

        <!-- Konfirmasi Password -->
        <span class="field-label">Konfirmasi Password</span>
        <input class="input" type="password" placeholder="••••••••" required>

        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 6px;">
            <span class="material-symbols-outlined" style="font-size:18px;">person_add</span>Daftar Sekarang
        </button>
    </form>

    <div class="auth-footer">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </div>

    <div style="height: 30px;"></div>
</div>
@endsection

@push('scripts')
<script>
    function setRegisterRole(role) {
        document.getElementById('rs-guru').className = (role === 'guru') ? 'active' : '';
        document.getElementById('rs-siswa').className = (role === 'siswa') ? 'active' : '';

        const dynamicField = document.getElementById('dynamic-field');

        if(role === 'guru') {
            dynamicField.innerHTML = `
                <span class="field-label">Email Institusi</span>
                <input class="input" type="email" placeholder="nama@sekolah.sch.id" required>
            `;
        } else {
            dynamicField.innerHTML = `
                <span class="field-label">NIS (Nomor Induk Siswa)</span>
                <input class="input" type="text" placeholder="Contoh: 2025010" required>
            `;
        }
    }
</script>
@endpush
