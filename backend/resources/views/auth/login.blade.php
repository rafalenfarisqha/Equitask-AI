
@extends('layouts.app')

@section('title', 'Masuk — EquiTask AI')

@push('styles')
<style>
    /* Semua gaya dibatasi pada halaman login */
    .equi-login {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        padding-bottom: 32px;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .equi-login,
    .equi-login * {
        box-sizing: border-box;
    }

    /* Header */
    .equi-login__hero {
        width: 100%;
        background: linear-gradient(
            145deg,
            #2c63e6 0%,
            #3b82f6 48%,
            #0f766e 100%
        );
        color: #ffffff;
        border-radius: 0 0 28px 28px;
        padding: 38px 24px;
    }

    .equi-login__hero-inner {
        width: 100%;
        max-width: 980px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .equi-login__logo {
        width: 64px;
        height: 64px;
        flex: 0 0 64px;
        object-fit: contain;
        padding: 5px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .15);
    }

    .equi-login__heading {
        min-width: 0;
    }

    .equi-login__title {
        margin: 0;
        font-size: 30px;
        line-height: 1.2;
        font-weight: 800;
    }

    .equi-login__subtitle {
        margin: 5px 0 0;
        font-size: 15px;
        line-height: 1.5;
        opacity: .92;
    }

    /* Area login */
    .equi-login__content {
        width: min(100% - 40px, 420px);
        margin: 0 auto;
        padding-top: 24px;
    }

    /* Pilihan guru / siswa */
    .equi-login__roles {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px;
        width: 100%;
        padding: 4px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
    }

    .equi-login__role {
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: #64748b;
        font-family: inherit;
        font-size: 14px;
        font-weight: 700;
        padding: 11px 12px;
        cursor: pointer;
        transition: background .2s, color .2s;
    }

    .equi-login__role.active {
        background: #2563eb;
        color: #ffffff;
    }

    /* Form */
    .equi-login__form {
        margin-top: 26px;
    }

    .equi-login__field {
        margin-bottom: 18px;
    }

    .equi-login__label {
        display: block;
        margin-bottom: 8px;
        color: #1e293b;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
    }

    .equi-login__input {
        display: block;
        width: 100%;
        min-width: 0;
        height: 48px;
        padding: 11px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        background: #ffffff;
        color: #1e293b;
        font-family: inherit;
        font-size: 16px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .equi-login__input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
    }

    .equi-login__input::placeholder {
        color: #94a3b8;
    }

    .equi-login__forgot {
        margin: -2px 0 20px;
        text-align: right;
        color: #2563eb;
        font-size: 13px;
        font-weight: 700;
    }

    .equi-login__submit {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
        width: 100%;
        min-height: 48px;
        padding: 12px 18px;
        border: 0;
        border-radius: 12px;
        background: #2563eb;
        color: #ffffff;
        font-family: inherit;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(37, 99, 235, .2);
        transition: background .2s, transform .1s;
    }

    .equi-login__submit:hover {
        background: #1d4ed8;
    }

    .equi-login__submit:active {
        transform: scale(.99);
    }

    .equi-login__footer {
        margin-top: 28px;
        text-align: center;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .equi-login__footer a {
        color: #2563eb;
        font-weight: 700;
        text-decoration: none;
    }

    .equi-login__error {
        margin-bottom: 18px;
        padding: 12px;
        border: 1px solid #fecaca;
        border-radius: 10px;
        background: #fef2f2;
        color: #dc2626;
        font-size: 13px;
    }

    /* Tablet */
    @media (max-width: 767.98px) {
        .equi-login__hero {
            padding: 26px 20px;
            border-radius: 0 0 24px 24px;
        }

        .equi-login__logo {
            width: 54px;
            height: 54px;
            flex-basis: 54px;
            border-radius: 13px;
        }

        .equi-login__title {
            font-size: 24px;
        }

        .equi-login__subtitle {
            font-size: 13px;
        }

        .equi-login__content {
            width: min(100% - 32px, 420px);
            padding-top: 20px;
        }

        .equi-login__form {
            margin-top: 20px;
        }

        .equi-login__footer {
            margin-top: 24px;
        }
    }

    /* HP dengan layar kecil */
    @media (max-width: 380px) {
        .equi-login__hero {
            padding: 22px 16px;
        }

        .equi-login__hero-inner {
            gap: 12px;
        }

        .equi-login__logo {
            width: 46px;
            height: 46px;
            flex-basis: 46px;
        }

        .equi-login__title {
            font-size: 21px;
        }

        .equi-login__subtitle {
            font-size: 12px;
        }

        .equi-login__content {
            width: calc(100% - 28px);
        }
    }
</style>
@endpush

@section('content')
<div class="equi-login">

    {{-- Header EquiTask AI --}}
    <header class="equi-login__hero">
        <div class="equi-login__hero-inner">

            <img
                src="{{ asset('images/logoequitask.png') }}"
                alt="Logo EquiTask AI"
                class="equi-login__logo"
            >

            <div class="equi-login__heading">
                <h1 class="equi-login__title">
                    EquiTask AI
                </h1>
                <p class="equi-login__subtitle">
                    Platform Asesmen Adaptif
                </p>
            </div>

        </div>
    </header>

    <div class="equi-login__content">

        {{-- Pilihan jenis akun --}}
        <div class="equi-login__roles"
             role="group"
             aria-label="Jenis akun">

            <button
                type="button"
                id="rs-guru"
                class="equi-login__role"
                onclick="setLoginRole('guru')">
                Guru
            </button>

            <button
                type="button"
                id="rs-siswa"
                class="equi-login__role"
                onclick="setLoginRole('siswa')">
                Siswa
            </button>

        </div>

        {{-- Form login --}}
        <div class="equi-login__form">

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <input
                    type="hidden"
                    name="role"
                    id="input-role"
                    value="{{ old('role', 'guru') }}"
                >

                {{-- Pesan kesalahan --}}
                @if ($errors->any())
                    <div class="equi-login__error"
                         role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                {{-- Identitas --}}
                <div class="equi-login__field">

                    <label
                        class="equi-login__label"
                        id="label-identitas"
                        for="input-identitas">
                        Email
                    </label>

                    <input
                        class="equi-login__input"
                        type="email"
                        id="input-identitas"
                        name="email"
                        placeholder="Masukkan email Anda..."
                        value="{{ old('email', '') }}"
                        autocomplete="username"
                        required
                    >

                </div>

                {{-- Password --}}
                <div class="equi-login__field">

                    <label
                        class="equi-login__label"
                        for="input-password">
                        Password
                    </label>

                    <input
                        class="equi-login__input"
                        type="password"
                        id="input-password"
                        name="password"
                        placeholder="Masukkan password..."
                        autocomplete="current-password"
                        required
                    >

                </div>

                {{-- Teks lupa password --}}
                <div class="equi-login__forgot">
                    Lupa Password?
                </div>

                {{-- Tombol masuk --}}
                <button
                    type="submit"
                    class="equi-login__submit">

                    <span
                        class="material-symbols-outlined"
                        aria-hidden="true"
                        style="font-size: 20px;">
                        login
                    </span>

                    Masuk
                </button>

            </form>

        </div>

        {{-- Link pendaftaran --}}
        <div class="equi-login__footer">
            Belum punya akun?
            <a href="{{ route('register') }}">
                Daftar sekarang
            </a>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    function setLoginRole(role) {
        const tombolGuru =
            document.getElementById('rs-guru');

        const tombolSiswa =
            document.getElementById('rs-siswa');

        const labelIdentitas =
            document.getElementById('label-identitas');

        const inputIdentitas =
            document.getElementById('input-identitas');

        const inputRole =
            document.getElementById('input-role');

        const isGuru = role === 'guru';

        inputRole.value = isGuru ? 'guru' : 'siswa';

        tombolGuru.classList.toggle('active', isGuru);
        tombolSiswa.classList.toggle('active', !isGuru);

        tombolGuru.setAttribute(
            'aria-pressed',
            String(isGuru)
        );

        tombolSiswa.setAttribute(
            'aria-pressed',
            String(!isGuru)
        );

        if (isGuru) {
            labelIdentitas.textContent = 'Email';
            inputIdentitas.name = 'email';
            inputIdentitas.type = 'email';
            inputIdentitas.placeholder =
                'Masukkan email Anda...';
            inputIdentitas.value = '';
        } else {
            labelIdentitas.textContent =
                'NISN (Nomor Induk Siswa Nasional)';

            inputIdentitas.name = 'nisn';
            inputIdentitas.type = 'text';
            inputIdentitas.inputMode = 'numeric';
            inputIdentitas.placeholder =
                'Masukkan NISN Anda...';
            inputIdentitas.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const oldRole = @json(old('role', 'guru'));
        const oldEmail = @json(old('email', ''));
        const oldNisn = @json(old('nisn', ''));

        const role = oldRole === 'siswa' ? 'siswa' : 'guru';

        setLoginRole(role);

        document.getElementById('input-identitas').value =
            role === 'siswa' ? oldNisn : oldEmail;
    });
</script>
@endpush
