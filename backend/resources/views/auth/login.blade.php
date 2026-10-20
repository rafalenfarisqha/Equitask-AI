@extends('layouts.app')

@section('title', 'Masuk — EquiTask AI')

@push('styles')
<style>
    /* Mencegah scrollbar horizontal */
    body {
        overflow-x: hidden;
    }

    .login-hero {
        background: linear-gradient(145deg, #2c63e6 0%, #3b82f6 48%, #0f766e 100%);
        width: 100vw;
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: -50vw;
        margin-right: -50vw;
        padding: 52px max(24px, calc((100vw - 1024px) / 2 + 24px)) 34px;
        margin-top: -16px;
        color: #fff;
        border-radius: 0 0 32px 32px;
        box-sizing: border-box;
    }

    .role-switch {
        display: flex;
        background: var(--card, #fff);
        border-radius: 14px;
        padding: 4px;
        margin: 22px auto 16px auto;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid var(--border, #e2e8f0);
    }

    .role-switch button {
        flex: 1;
        border: none;
        background: none;
        padding: 10px;
        border-radius: 11px;
        font-weight: 700;
        font-size: 13px;
        color: var(--muted, #64748b);
        font-family: inherit;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
        text-align: center;
    }

    .role-switch button.active {
        background: var(--primary, #2563eb);
        color: #fff;
    }

    /* Perbaikan styling form agar tertata rapi vertikal */
    .login-form form {
        max-width: 400px;
        margin: 0 auto;
    }

    .field-label {
        display: block;
        font-weight: 700;
        font-size: 13px;
        color: #1e293b;
        margin-top: 16px;
        margin-bottom: 6px;
    }

    .input {
        display: block;
        width: 100%;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid var(--border, #cbd5e1);
        background: var(--card, #fff);
        font-size: 14px;
        font-family: inherit;
        box-sizing: border-box;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .input:focus {
        border-color: var(--primary, #2563eb);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .forgot {
        text-align: right;
        font-size: 12.5px;
        color: var(--primary, #2563eb);
        font-weight: 700;
        margin: 8px 0 20px;
        cursor: pointer;
    }

    .btn-block {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px 20px;
        border-radius: 12px;
        background: var(--primary, #2563eb);
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        transition: background 0.2s, transform 0.1s;
    }

    .btn-block:active {
        transform: scale(0.98);
    }

    .auth-footer {
        text-align: center;
        font-size: 13px;
        margin-top: 20px;
        color: var(--muted, #64748b);
    }

    .auth-footer a {
        color: var(--primary, #2563eb);
        font-weight: 700;
        text-decoration: none;
    }

    .login-error {
        color: #dc2626;
        background: #fef2f2;
        border: 1px solid #fecaca;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 16px;
        font-size: 13.5px;
    }
</style>
@endpush

@section('content')
<div class="px">

    {{-- Header EquiTask AI --}}
    <div class="login-hero">
        <div style="display: flex; align-items: center; gap: 16px; max-width: 980px; margin: 0 auto;">
            <img src="{{ asset('images/logoequitask.png') }}"
                 alt="Logo EquiTask AI"
                 style="width: 60px; height: 60px; border-radius: 16px; object-fit: cover; background: white; padding: 4px; box-shadow: 0 6px 18px rgba(0,0,0,0.15);">

            <div>
                <h2 style="margin:0; font-size: 30px; font-weight: 800;">
                    EquiTask AI
                </h2>
                <p style="margin:4px 0 0; font-size: 16px; opacity: 0.9;">
                    Platform Asesmen Adaptif
                </p>
            </div>
        </div>
    </div>

    {{-- Pilihan jenis akun --}}
    <div class="role-switch">
        <button type="button"
                id="rs-guru"
                class="active"
                onclick="setLoginRole('guru')">
            Guru
        </button>

        <button type="button"
                id="rs-siswa"
                onclick="setLoginRole('siswa')">
            Siswa
        </button>
    </div>

    {{-- Form login --}}
    <div class="login-form" style="padding: 12px 0 24px 0;">
        <form action="{{ route('login') }}" method="POST">
            @csrf

            {{-- Menyimpan jenis akun yang dipilih --}}
            <input type="hidden" name="role" id="input-role" value="guru">

            {{-- Pesan kesalahan login --}}
            @if ($errors->any())
                <div class="login-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Email guru atau NISN siswa --}}
            <label class="field-label"
                   id="label-identitas"
                   for="input-identitas">
                Email
            </label>

            <input
                class="input"
                type="email"
                id="input-identitas"
                name="email"
                placeholder="Masukkan email Anda..."
                value="{{ old('email') }}"
                autocomplete="username"
                required
            >

            {{-- Password --}}
            <label class="field-label" for="input-password">
                Password
            </label>

            <input
                class="input"
                type="password"
                id="input-password"
                name="password"
                placeholder="Masukkan password..."
                autocomplete="current-password"
                required
            >

            <div class="forgot">
                Lupa Password?
            </div>

            <button type="submit" class="btn-block">
                <span class="material-symbols-outlined" style="font-size:20px;">
                    login
                </span>
                Masuk
            </button>
        </form>
    </div>

    {{-- Link pendaftaran --}}
    <div class="auth-footer">
        Belum punya akun?
        <a href="{{ route('register') }}">Daftar sekarang</a>
    </div>

    <div style="height: 30px;"></div>
</div>
@endsection

@push('scripts')
<script>
    function setLoginRole(role) {
        const tombolGuru = document.getElementById('rs-guru');
        const tombolSiswa = document.getElementById('rs-siswa');
        const labelIdentitas = document.getElementById('label-identitas');
        const inputIdentitas = document.getElementById('input-identitas');
        const inputRole = document.getElementById('input-role');

        // Kirim role yang dipilih ke AuthController
        inputRole.value = role;

        if (role === 'guru') {
            tombolGuru.classList.add('active');
            tombolSiswa.classList.remove('active');

            labelIdentitas.innerText = 'Email';
            inputIdentitas.name = 'email';
            inputIdentitas.type = 'email';
            inputIdentitas.placeholder = 'Masukkan email Anda...';
            inputIdentitas.value = @json(old('email', ''));
        } else {
            tombolSiswa.classList.add('active');
            tombolGuru.classList.remove('active');

            labelIdentitas.innerText = 'NISN (Nomor Induk Siswa Nasional)';
            inputIdentitas.name = 'nisn';
            inputIdentitas.type = 'text';
            inputIdentitas.placeholder = 'Masukkan NISN Anda...';
            inputIdentitas.value = @json(old('nisn', ''));
        }
    }
</script>
@endpush
