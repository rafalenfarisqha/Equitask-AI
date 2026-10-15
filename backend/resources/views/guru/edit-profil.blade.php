@extends('layouts.app')

@section('title', 'Edit Profil Akun')

@section('content')
<div class="screen" style="position: relative; width: 100%; height: auto; background: var(--bg); padding-bottom: 60px;">

    <!-- Topbar / Header -->
    <div class="topbar" style="background: transparent; padding: 20px 24px 16px; display: flex; align-items: center; gap: 12px;">
        <a href="{{ route('guru.profil') }}" style="color: var(--text); text-decoration: none; display: flex; align-items: center;">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 style="font-size: 18px; font-weight: 800; color: var(--text);">Edit Profil Akun</h1>
    </div>

    <div class="px" style="padding: 0 24px;">

        @if($errors->any())
            <div style="background: #EF44441A; color: #EF4444; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 700; margin-bottom: 16px; border: 1px solid rgba(239, 68, 68, 0.2);">
                Terjadi kesalahan: {{ $errors->first() }}
            </div>
        @endif

        <!-- Form Edit Profil -->
        <form action="{{ route('guru.profil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card" style="padding: 20px; display: flex; flex-direction: column; gap: 14px; margin-bottom: 16px;">
                <div style="font-size: 14px; font-weight: 800; margin-bottom: 4px; color: var(--text);">Informasi Pribadi & Kontak</div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--muted);">Nama Depan</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $guru->first_name) }}" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border); background: var(--card-bg); font-size: 13px; color: var(--text);">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--muted);">Nama Belakang</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $guru->last_name) }}" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border); background: var(--card-bg); font-size: 13px; color: var(--text);">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--muted);">Email Akun</label>
                    <input type="email" name="email" value="{{ old('email', $guru->email) }}" required style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border); background: var(--card-bg); font-size: 13px; color: var(--text);">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--muted);">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $guru->phone) }}" placeholder="Contoh: 08123456789" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border); background: var(--card-bg); font-size: 13px; color: var(--text);">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--muted);">Institusi / Sekolah / Universitas</label>
                    <input type="text" name="institution" value="{{ old('institution', $guru->institution) }}" placeholder="Contoh: Universitas Airlangga" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border); background: var(--card-bg); font-size: 13px; color: var(--text);">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--muted);">Foto Profil (Opsional)</label>
                    <input type="file" name="avatar" accept="image/png, image/jpeg" style="width: 100%; font-size: 12px; color: var(--muted); padding: 6px 0;">
                </div>

                <hr style="border: none; border-top: 1px solid var(--border); margin: 6px 0;">

                <div style="font-size: 14px; font-weight: 800; color: var(--text);">Ubah Kata Sandi (Opsional)</div>
                <div style="font-size: 11.5px; color: var(--muted); margin-top: -8px;">Kosongkan jika tidak ingin mengganti sandi.</div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--muted);">Sandi Baru</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border); background: var(--card-bg); font-size: 13px; color: var(--text);">
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--muted);">Konfirmasi Sandi Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi sandi baru" style="width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border); background: var(--card-bg); font-size: 13px; color: var(--text);">
                </div>

                <div style="display: flex; gap: 10px; margin-top: 8px;">
                    <a href="{{ route('guru.profil') }}" class="btn btn-outline" style="flex: 1; text-align: center; padding: 12px; font-weight: 800; border-radius: 10px; text-decoration: none; border: 1px solid var(--border); color: var(--text);">Batal</a>
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px; font-weight: 800; border-radius: 10px; cursor: pointer; background: var(--primary); color: #fff;">Simpan</button>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection

@section('bottom_nav')
<div class="bottomnav">
    <a href="{{ route('guru.dashboard') }}" class="navitem" style="text-decoration:none;">
        <span class="material-symbols-outlined">space_dashboard</span>Dashboard
    </a>
    <a href="{{ route('guru.kelas') }}" class="navitem" style="text-decoration:none;">
        <span class="material-symbols-outlined">groups</span>Kelas
    </a>
    <a href="{{ route('guru.assessments.index') }}" class="navitem" style="text-decoration:none;">
        <span class="material-symbols-outlined">library_books</span>Asesmen
    </a>
    <a href="{{ route('guru.bank-soal') }}" class="navitem" style="text-decoration:none;">
        <span class="material-symbols-outlined">folder_open</span>Bank Soal
    </a>
    <a href="{{ route('guru.profil') }}" class="navitem active" style="text-decoration:none;">
        <span class="material-symbols-outlined">person</span>Profil
    </a>
</div>
@endsection
