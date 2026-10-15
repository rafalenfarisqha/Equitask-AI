@extends('layouts.app')

@section('title', 'Notifikasi Guru')

@section('content')
<div class="screen" style="position: relative; width: 100%; min-height: 100vh; background: var(--bg); padding-bottom: 90px;">

    <!-- Topbar / Header -->
    <div class="topbar" style="background: transparent; padding: 20px 24px 16px; display: flex; align-items: center; gap: 12px;">
        <a href="{{ route('guru.dashboard') }}" style="color: var(--text); text-decoration: none; display: flex; align-items: center;">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 style="font-size: 18px; font-weight: 800; color: var(--text);">Notifikasi Aktivitas</h1>
    </div>

    <div class="px" style="padding: 0 24px; display: flex; flex-direction: column; gap: 12px;">

        <!-- Item Notifikasi 1: Siswa Mengerjakan Asesmen -->
        <div class="card" style="padding: 16px; display: flex; gap: 14px; align-items: flex-start; background: var(--card-bg); border-left: 4px solid var(--primary);">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(37, 99, 235, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span class="material-symbols-outlined" style="font-size: 20px;">assignment_turned_in</span>
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                    <span style="font-size: 13.5px; font-weight: 800; color: var(--text);">Asesmen Telah Dikerjakan</span>
                    <span style="font-size: 11px; color: var(--muted);">Baru saja</span>
                </div>
                <div style="font-size: 13px; color: var(--text); line-height: 1.4;">
                    Siswa <b>Ahmad Fauzan</b> telah menyelesaikan asesmen <i>Matematika Inklusif C2</i> dengan nilai 85.
                </div>
            </div>
        </div>

        <!-- Item Notifikasi 2: Soal Disimpan di Bank Soal -->
        <div class="card" style="padding: 16px; display: flex; gap: 14px; align-items: flex-start; background: var(--card-bg); border-left: 4px solid #10B981;">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10B981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span class="material-symbols-outlined" style="font-size: 20px;">bookmark_added</span>
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                    <span style="font-size: 13.5px; font-weight: 800; color: var(--text);">Bank Soal Publik</span>
                    <span style="font-size: 11px; color: var(--muted);">2 jam lalu</span>
                </div>
                <div style="font-size: 13px; color: var(--text); line-height: 1.4;">
                    Guru <b>Siti Rahma</b> telah menyimpan soal buatan Anda ke dalam koleksi Bank Soal mereka.
                </div>
            </div>
        </div>

        <!-- Item Notifikasi 3: Asesmen AI Berhasil Dibuat -->
        <div class="card" style="padding: 16px; display: flex; gap: 14px; align-items: flex-start; background: var(--card-bg); border-left: 4px solid #8B5CF6;">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(139, 92, 246, 0.1); color: #8B5CF6; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span class="material-symbols-outlined" style="font-size: 20px;">auto_awesome</span>
            </div>
            <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                    <span style="font-size: 13.5px; font-weight: 800; color: var(--text);">Generasi AI Selesai</span>
                    <span style="font-size: 11px; color: var(--muted);">Kemarin</span>
                </div>
                <div style="font-size: 13px; color: var(--text); line-height: 1.4;">
                    Multi-agent AI berhasil menyusun 15 butir soal adaptif tingkat C3 untuk materi <i>Bahasa Indonesia</i>.
                </div>
            </div>
        </div>

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
    <a href="{{ route('guru.profil') }}" class="navitem" style="text-decoration:none;">
        <span class="material-symbols-outlined">person</span>Profil
    </a>
</div>
@endsection
