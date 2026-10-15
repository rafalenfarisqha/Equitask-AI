
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
        background: var(--card);
        border-radius: 14px;
        padding: 4px;
        margin: 22px auto 16px auto;
        max-width: 400px;
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
        tetx-align: center;
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

    .login-error {
        color: #dc2626;
        background: #fef2f2;
        border: 1px solid #fecaca;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 16px;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="px">

    {{-- Header EquiTask AI --}}
    <div class="login-hero">
        <div style="display: flex; align-items: center; gap: 16px;">
            <img src="{{ asset('images/logoequitask.png') }}"
                 alt="Logo EquiTask AI"
                 style="width: 60px; height: 60px; border-radius: 16px; object-fit: cover; background: white; padding: 4px; box-shadow: 0 6px 18px rgba(0,0,0,0.15);">

            <div>
                <h2 style="margin:0; font-size: 30px; font-weight: 800;">
                    EquiTask AI
                </h2>
                <p style="margin:4px 0 0; font-size: 18px; opacity: 0.9;">
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
    <div class="login-form" style="padding: 22px 0;">
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

            {{-- Email guru atau NIS siswa --}}
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

            <button type="submit" class="btn btn-primary btn-block">
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
            inputIdentitas.name = 'nis';
            inputIdentitas.type = 'text';
            inputIdentitas.placeholder = 'Masukkan NISN Anda...';
            inputIdentitas.value = @json(old('nisn', ''));
        }
    }
</script>
@endpush
