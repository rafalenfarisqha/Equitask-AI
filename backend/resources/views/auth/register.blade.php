@extends('layouts.app')

@section('title', 'Daftar Akun — EquiTask AI')

@push('styles')
<style>
    body {
        overflow-x: hidden;
    }

    .register-hero {
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
        background: var(--card, #ffffff);
        border-radius: 14px;
        padding: 4px;
        margin: 22px auto 16px auto;
        max-width: 400px;
        box-shadow: var(--shadow, 0 4px 12px rgba(0,0,0,0.05));
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

    .register-form-container {
        max-width: 400px;
        margin: 0 auto;
        padding: 10px 0 22px;
    }

    .field-label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        margin-bottom: 6px;
        margin-top: 14px;
        color: var(--text, #1e293b);
    }

    .input {
        width: 100%;
        padding: 11px 14px;
        border-radius: 12px;
        border: 1px solid var(--border, #cbd5e1);
        background: #f8fafc;
        font-size: 13.5px;
        font-family: inherit;
        box-sizing: border-box;
        outline: none;
        transition: border-color 0.2s, background 0.2s;
    }

    .input:focus {
        border-color: var(--primary, #2563eb);
        background: #ffffff;
    }

    .btn-block {
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        border: none;
        background: var(--primary, #2563eb);
        color: white;
    }

    .auth-footer {
        text-align: center;
        font-size: 13px;
        margin-top: 20px;
        color: var(--muted, #64748b);
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    .auth-footer a {
        color: var(--primary, #2563eb);
        font-weight: 700;
        text-decoration: none;
    }

    .register-error {
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
    <!-- Hero Banner -->
    <div class="register-hero">
        <div style="display: flex; align-items: center; gap: 16px;">
            <img src="{{ asset('images/logoequitask.png') }}"
                 alt="Logo EquiTask AI"
                 style="width: 60px; height: 60px; border-radius: 16px; object-fit: cover; background: white; padding: 4px; box-shadow: 0 6px 18px rgba(0,0,0,0.15);">
            <div>
                <h2 style="margin:0; font-size: 22px; font-weight: 800;">Buat Akun Baru</h2>
                <p style="margin:4px 0 0; font-size: 13px; opacity: 0.9;">Platform Assessment Adaptif</p>
            </div>
        </div>
    </div>

    <!-- Pilihan Role Pendaftaran -->
    <div class="role-switch">
        <button type="button" id="rs-guru" class="active" onclick="setRegisterRole('guru')">Guru</button>
        <button type="button" id="rs-siswa" onclick="setRegisterRole('siswa')">Siswa</button>
    </div>

    <div class="register-form-container">
        <form action="{{ route('register.store') }}" method="POST">
            @csrf

            <!-- Menyimpan role yang dipilih -->
            <input type="hidden" name="role" id="input-role" value="{{ old('role', 'guru') }}">

            <!-- Pesan Error Validasi Aman -->
            @if(isset($errors) &&$errors->any())
                <div class="register-error">
                    @php $allErrors =$errors->all(); @endphp
                    @for($i = 0; $i < count($allErrors);$i++)
                        <div>{{ $allErrors[$i] }}</div>
                    @endfor
                </div>
            @endif

            <!-- Input Nama Lengkap -->
            <span class="field-label">Nama Lengkap</span>
            <input class="input" type="text" name="name" placeholder="Masukkan nama lengkap..." value="{{ old('name') }}" required>

            <!-- Input Dinamis (Email untuk Guru / NISN untuk Siswa) -->
            <div id="dynamic-field">
                <span class="field-label">Email Institusi</span>
                <input class="input" type="email" name="email" placeholder="nama@sekolah.sch.id" value="{{ old('email') }}" required>
            </div>

            <!-- Input Asal Sekolah / Institusi -->
            <span class="field-label">Asal Sekolah / Institusi</span>
            <input class="input" type="text" name="institution" placeholder="Contoh: SDN Klampis Ngasem 1" value="{{ old('institution') }}" required>

            <!-- Input Password -->
            <span class="field-label">Password</span>
            <input class="input" type="password" name="password" placeholder="••••••••" required>

            <!-- Konfirmasi Password -->
            <span class="field-label">Konfirmasi Password</span>
            <input class="input" type="password" name="password_confirmation" placeholder="••••••••" required>

            <button type="submit" class="btn-block" style="margin-top: 20px;">
                <span class="material-symbols-outlined" style="font-size:18px;">person_add</span> Daftar Sekarang
            </button>
        </form>
    </div>

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
        document.getElementById('input-role').value = role;

        const dynamicField = document.getElementById('dynamic-field');

        if(role === 'guru') {
            dynamicField.innerHTML = `
                <span class="field-label">Email Institusi</span>
                <input class="input" type="email" name="email" placeholder="nama@sekolah.sch.id" required>
            `;
        } else {
            dynamicField.innerHTML = `
                <span class="field-label">NISN (Nomor Induk Siswa Nasional)</span>
                <input class="input" type="text" name="nisn" placeholder="Contoh: 2025010" required>
            `;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialRole = document.getElementById('input-role').value || 'guru';
        setRegisterRole(initialRole);
    });
</script>
@endpush
