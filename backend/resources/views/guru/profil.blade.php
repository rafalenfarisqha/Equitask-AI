@extends('layouts.app')

@section('title', 'Profil Guru')

@section('content')
<div class="screen" style="position: relative; width: 100%; height: auto; background: var(--bg); padding-bottom: 60px;">

    <!-- Topbar / Header -->
    <div class="topbar" style="background: transparent; padding: 20px 24px 16px; display: flex; align-items: center; gap: 12px;">
        <a href="{{ route('guru.dashboard') }}" style="color: var(--text); text-decoration: none; display: flex; align-items: center;">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 style="font-size: 18px; font-weight: 800; color: var(--text);">Profil Guru</h1>
    </div>

    <div class="px" style="padding: 0 24px;">

        @if(session('success'))
            <div style="background: #10B9811A; color: #10B981; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 700; margin-bottom: 16px; border: 1px solid rgba(16, 185, 129, 0.2);">
                {{ session('success') }}
            </div>
        @endif

        <!-- Kartu Foto & Nama Singkat -->
        <div class="card" style="padding: 24px; text-align: center; margin-bottom: 16px; background: linear-gradient(135deg, #2563EB0D, #2563EB02);">
            <div style="width: 72px; height: 72px; border-radius: 20px; margin: 0 auto 12px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: var(--primary); color: #fff; font-weight: 800; font-size: 24px;">
                @if(isset($guru->avatar) && $guru->avatar)
                    <img src="{{ asset('storage/' . $guru->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    {{ strtoupper(substr($guru->name ?? 'G', 0, 2)) }}
                @endif
            </div>
            <div style="font-size: 18px; font-weight: 800; color: var(--text);">{{ $guru->name }}</div>
            <div class="muted" style="font-size: 13px; margin-top: 4px;">{{ $guru->email }}</div>
            <span class="badge badge-green" style="display: inline-block; margin-top: 10px; font-size: 11px;">Guru Pengampu Inklusif</span>
        </div>

        <!-- Detail Informasi Akun -->
        <div class="card" style="padding: 16px 20px; display: flex; flex-direction: column; gap: 14px; margin-bottom: 16px;">

            <div style="padding-bottom: 12px; border-bottom: 1px solid var(--border); display: flex; flex-direction: column; gap: 4px;">
                <span style="font-size: 12.5px; font-weight: 700; color: var(--muted);">Nama Depan</span>
                <span style="font-size: 14px; font-weight: 400; color: var(--text);">{{ $guru->first_name ?? '-' }}</span>
            </div>

            <div style="padding-bottom: 12px; border-bottom: 1px solid var(--border); display: flex; flex-direction: column; gap: 4px;">
                <span style="font-size: 12.5px; font-weight: 700; color: var(--muted);">Nama Belakang</span>
                <span style="font-size: 14px; font-weight: 400; color: var(--text);">{{ $guru->last_name ?? '-' }}</span>
            </div>

            <div style="padding-bottom: 12px; border-bottom: 1px solid var(--border); display: flex; flex-direction: column; gap: 4px;">
                <span style="font-size: 12.5px; font-weight: 700; color: var(--muted);">No. Telepon / WhatsApp</span>
                <span style="font-size: 14px; font-weight: 400; color: var(--text);">{{ $guru->phone ?? '-' }}</span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <span style="font-size: 12.5px; font-weight: 700; color: var(--muted);">Institusi</span>
                <span style="font-size: 14px; font-weight: 400; color: var(--text);">{{ $guru->institution ?? '-' }}</span>
            </div>

        </div>

        <!-- Tombol Edit Profil -->
        <a href="{{ route('guru.profil.edit') }}" class="btn btn-primary" style="display: block; text-align: center; padding: 12px; font-weight: 800; border-radius: 12px; text-decoration: none; margin-bottom: 16px; background: var(--primary); color: #fff;">
            <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px; margin-right: 4px;">edit</span>Edit Profil
        </a>

        <!-- Tombol Logout -->
        <form action="{{ route('logout') }}" method="POST" style="margin-bottom: 20px;">
            @csrf
            <button type="submit" class="btn btn-outline" style="width: 100%; padding: 14px; font-weight: 800; color: #DC2626; border-color: rgba(220, 38, 38, 0.3); background: rgba(220, 38, 38, 0.04); cursor: pointer; border-radius: 12px;">
                <span class="material-symbols-outlined" style="vertical-align: middle; font-size: 18px; margin-right: 6px;">logout</span>Keluar Akun
            </button>
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
