@extends('layouts.app')

@section('title', 'Beranda Siswa')

@push('styles')
<style>
    .avatar-siswa {
        width: 46px; height: 46px; border-radius: 14px;
        background: var(--secondary-soft); color: var(--secondary);
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 16px; flex-shrink: 0;
    }
    .student-dashboard-hero {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 45%, #e0f2fe 100%);
        color: var(--text); border: 1px solid rgba(37, 99, 235, 0.12);
        box-shadow: 0 24px 40px -24px rgba(15, 23, 42, 0.22);
    }
    .student-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(37, 99, 235, 0.1); border: 1px solid rgba(37, 99, 235, 0.16);
        color: var(--primary); border-radius: 999px; padding: 6px 10px;
        font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;
    }
    .student-quick-grid {
        display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 12px;
    }
    .student-quick-grid > div {
        background: rgba(255, 255, 255, 0.74); border: 1px solid rgba(37, 99, 235, 0.12);
        border-radius: 12px; padding: 10px; text-align: center;
    }
    .student-quick-grid strong { display: block; font-size: 15px; font-weight: 800; }
    .student-quick-grid small { display: block; font-size: 10px; color: var(--muted); margin-top: 2px; }
    .student-focus-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid rgba(37, 99, 235, 0.08); box-shadow: 0 16px 32px -24px rgba(15, 23, 42, 0.22);
        text-decoration: none; display: block; color: inherit;
    }
    .student-focus-pill {
        display: inline-flex; align-items: center; gap: 4px; padding: 7px 10px;
        border-radius: 999px; background: var(--primary-soft); color: var(--primary);
        font-weight: 800; font-size: 11px;
    }
    .thumb { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.97) 0%, rgba(241, 247, 255, 0.95) 100%);
        border-radius: var(--radius-sm); padding: 16px;
        box-shadow: 0 14px 28px -20px rgba(15, 23, 42, 0.26); border: 1px solid rgba(255, 255, 255, 0.72);
    }
    .section-title { font-size: 15px; font-weight: 800; margin: 22px 0 12px; }
</style>
@endpush

@section('content')
<!-- Topbar Siswa -->
<div class="topbar">
    <div class="row" style="flex:1;">
        <div class="avatar-siswa">DA</div>
        <div>
            <div style="font-size:15px;font-weight:800;">Halo, Dinda 👋</div>
            <div class="muted">Kelas 7A · Adaptif</div>
        </div>
    </div>
    <button class="iconbtn"><span class="material-symbols-outlined">notifications</span></button>
</div>

<div class="px" style="padding-top: 16px;">

    <!-- Hero Banner Siswa -->
    <div class="card student-dashboard-hero">
        <div class="row" style="justify-content:space-between; align-items:flex-start;">
            <div style="flex:1;">
                <div class="student-badge"><span class="material-symbols-outlined" style="font-size:11px;">spark</span> Fokus hari ini</div>
                <div style="font-size:18px; font-weight:800; margin-top:8px; line-height:1.35;">Semangat, Dinda — kamu hampir sampai garis finish</div>
                <div class="muted" style="margin-top:8px; line-height:1.5;">Satu assessment siap dikerjakan dan dua materi review menunggu.</div>
            </div>
            <div class="thumb" style="background:linear-gradient(135deg,var(--primary-soft),#dbeafe); color:var(--primary); width:48px; height:48px; border-radius:14px;">
                <span class="material-symbols-outlined">auto_stories</span>
            </div>
        </div>
        <div class="student-quick-grid">
            <div><strong>4 hari</strong><small>streak belajar</small></div>
            <div><strong>180 pts</strong><small>poin aktif</small></div>
        </div>
    </div>

    <!-- Fokus Assessment Hari Ini -->
    <a href="#" class="card card-tap student-focus-card" style="margin-top:14px; padding:14px 16px;">
        <div class="row" style="justify-content:space-between; gap:10px;">
            <div class="row" style="flex:1;">
                <div class="thumb" style="background:linear-gradient(135deg,var(--primary-soft),#dbeafe);">
                    <span class="material-symbols-outlined" style="color:var(--primary);">play_circle</span>
                </div>
                <div style="flex:1;">
                    <div style="font-weight:800; font-size:13.5px;">Assessment Hari Ini</div>
                    <div class="muted">IPA — Ekosistem & Rantai Makanan · 10 soal</div>
                </div>
            </div>
            <div class="student-focus-pill">
                <span class="material-symbols-outlined" style="font-size:13px;">rocket_launch</span>Mulai
            </div>
        </div>
    </a>

    <!-- Kartu Statistik Singkat -->
    <div class="stat-grid" style="margin-top:14px;">
        <div class="stat-card">
            <span class="material-symbols-outlined" style="color:var(--primary); background:var(--primary-soft); padding:7px; border-radius:10px;">trending_up</span>
            <div class="num" style="font-size:22px; font-weight:800; margin-top:10px;">76%</div>
            <div class="lbl" style="font-size:11.5px; color:var(--muted); font-weight:600;">Progress Belajar</div>
        </div>
        <div class="stat-card">
            <span class="material-symbols-outlined" style="color:var(--secondary); background:var(--secondary-soft); padding:7px; border-radius:10px;">military_tech</span>
            <div class="num" style="font-size:22px; font-weight:800; margin-top:10px;">88</div>
            <div class="lbl" style="font-size:11.5px; color:var(--muted); font-weight:600;">Nilai Terakhir</div>
        </div>
    </div>

    <!-- Rencana Belajar -->
    <div class="section-title">Rencana Belajarmu</div>
    <div class="card" style="padding:12px 14px;">
        <div class="row" style="margin-bottom:10px; align-items:flex-start;">
            <span class="material-symbols-outlined" style="color:var(--secondary);">task_alt</span>
            <div style="flex:1;">
                <div style="font-weight:700; font-size:13px;">Kerjakan assessment IPA</div>
                <div class="muted" style="margin-top:3px;">Selesai sebelum Jumat agar nilai tetap aman.</div>
            </div>
        </div>
        <div class="row" style="align-items:flex-start;">
            <span class="material-symbols-outlined" style="color:var(--primary);">menu_book</span>
            <div style="flex:1;">
                <div style="font-weight:700; font-size:13px;">Review materi rantai makanan</div>
                <div class="muted" style="margin-top:3px;">Cukup 10 menit sebelum istirahat siang.</div>
            </div>
        </div>
    </div>

    <div style="height:24px;"></div>
</div>
@endsection

@section('bottom_nav')
<div class="bottomnav">
    <a href="{{ route('siswa.dashboard') }}" class="navitem {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">home</span>Beranda
    </a>
    <a href="{{ route('siswa.assessment') }}" class="navitem {{ request()->routeIs('siswa.assessment') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">assignment</span>Assessment
    </a>
    <a href="{{ route('siswa.riwayat') }}" class="navitem {{ request()->routeIs('siswa.riwayat') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">history</span>Riwayat
    </a>
    <a href="{{ route('siswa.profil') }}" class="navitem {{ request()->routeIs('siswa.profil') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">person</span>Profil
    </a>
</div>
@endsection
