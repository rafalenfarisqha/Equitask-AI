@extends('layouts.app')

@section('title', 'Masuk — EquiTask AI')

@push('styles')
<style>
    /* Mencegah munculnya scrollbar horizontal akibat elemen full-width */
    body {
        overflow-x: hidden;
    }

    .login-hero {
        background: linear-gradient(145deg, #2c63e6 0%, #3b82f6 48%, #0f766e 100%);
        /* Membuat background membentang penuh dari kiri ke kanan layar (100vw) */
        width: 100vw;
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;

        /* Padding dinamis: otomatis menyesuaikan agar teks di dalam kop tetap sejajar dengan form di bawahnya */
        padding: 52px max(24px, calc((100vw - 1024px) / 2 + 24px)) 34px;
        margin-top: -16px; /* Menutup jarak kosong paling atas */

        color: #fff;
        border-radius: 0 0 32px 32px;
        box-sizing: border-box;
    }
    .role-switch {
        display: flex;
        background: var(--card);
        border-radius: 14px;
        padding: 4px;
        margin: 22px 0 4px;
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
    .forgot {
        text-align: right;
        font-size: 12.5px;
        color: var(--primary);
        font-weight: 700;
        margin: -6px 0 20px;
        cursor: pointer;
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
    <div class="login-hero">
        <h2 style="margin:0; font-size: 22px;">EquiTask AI</h2>
        <p style="margin:4px 0 0; font-size: 13px; opacity: 0.9;">Platform Assessment Adaptif</p>
    </div>

    <div class="role-switch">
        <button id="rs-guru" class="active" onclick="setLoginRole('guru')">Guru</button>
        <button id="rs-siswa" onclick="setLoginRole('siswa')">Siswa</button>
    </div>

    <div class="login-form" style="padding: 22px 0;">
        <form action="#" method="POST">
            @csrf
            <span class="field-label" id="label-identitas">Email</span>
            <input class="input" type="text" id="input-identitas" placeholder="Masukkan email Anda..." required>

            <span class="field-label">Password</span>
            <input class="input" type="password" placeholder="••••••••" required>

            <div class="forgot">Lupa Password?</div>

            <button type="submit" class="btn btn-primary btn-block">
                <span class="material-symbols-outlined" style="font-size:18px;">login</span>Masuk
            </button>
        </form>
    </div>

    <div class="auth-footer">
        Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
    </div>

    <div style="height: 30px;"></div>
</div>
@endsection

@push('scripts')
<script>
    function setLoginRole(role) {
        document.getElementById('rs-guru').className = (role === 'guru') ? 'active' : '';
        document.getElementById('rs-siswa').className = (role === 'siswa') ? 'active' : '';

        const labelIdentitas = document.getElementById('label-identitas');
        const inputIdentitas = document.getElementById('input-identitas');

        if(role === 'guru') {
            labelIdentitas.innerText = 'Email';
            inputIdentitas.placeholder = 'Masukkan email Anda...';
            inputIdentitas.type = 'email';
        } else {
            labelIdentitas.innerText = 'NIS (Nomor Induk Siswa)';
            inputIdentitas.placeholder = 'Masukkan NIS Anda...';
            inputIdentitas.type = 'text';
        }
    }
</script>
@endpush
