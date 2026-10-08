@extends('layouts.app')

@section('title', 'Dashboard Guru')

@push('styles')
<style>
    /* Styling khusus Dashboard Guru */
    .avatar {
        width: 46px; height: 46px; border-radius: 14px;
        background: linear-gradient(135deg, var(--primary-soft), #dfeeff);
        color: var(--primary);
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 16px; flex-shrink: 0;
    }

    .dashboard-hero {
        background: linear-gradient(135deg, #173f8f 0%, #2563eb 48%, #0f766e 100%);
        color: #fff; border: none;
        box-shadow: 0 24px 44px -24px rgba(15, 23, 42, 0.42);
        position: relative; overflow: hidden; padding: 18px;
    }
    .dashboard-hero::after {
        content: ""; position: absolute; inset: auto -18px -24px auto;
        width: 120px; height: 120px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.2), transparent 70%);
        pointer-events: none;
    }
    .hero-pill {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px;
        border-radius: 999px; background: rgba(255, 255, 255, 0.16);
        font-size: 10.5px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 8px;
    }
    .hero-title { font-size: 18px; font-weight: 800; line-height: 1.35; }
    .hero-sub { font-size: 12px; margin-top: 8px; opacity: 0.9; line-height: 1.55; }

    .hero-metrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 14px; }
    .hero-metrics > div { background: rgba(255, 255, 255, 0.14); border: 1px solid rgba(255, 255, 255, 0.16); border-radius: 12px; padding: 10px 8px; text-align: center; }
    .hero-metrics span { display: block; font-size: 15px; font-weight: 800; }
    .hero-metrics small { display: block; font-size: 10px; margin-top: 2px; opacity: 0.84; }

    .stat-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.97) 0%, rgba(241, 247, 255, 0.95) 100%);
        border-radius: var(--radius-sm); padding: 16px;
        box-shadow: 0 14px 28px -20px rgba(15, 23, 42, 0.26); border: 1px solid rgba(255, 255, 255, 0.72);
        position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 18px 34px -20px rgba(15, 23, 42, 0.28); }
    .stat-card::before {
        content: ""; position: absolute; inset: auto -20px -20px auto; width: 84px; height: 84px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.12), transparent 70%); pointer-events: none;
    }
    .stat-card .material-symbols-outlined { font-size: 20px; color: var(--primary); background: var(--primary-soft); padding: 7px; border-radius: 10px; }
    .stat-card .num { font-size: 22px; font-weight: 800; margin-top: 10px; }
    .stat-card .lbl { font-size: 11.5px; color: var(--muted); font-weight: 600; }

    .section-title { font-size: 15px; font-weight: 800; margin: 22px 0 12px; display: flex; align-items: center; justify-content: space-between; }
    .section-title a.link { font-size: 12.5px; font-weight: 700; color: var(--primary); cursor: pointer; text-decoration: none; }

    .card-tap { cursor: pointer; transition: transform 0.15s ease; text-decoration: none; display: block; color: inherit; }
    .card-tap:active { transform: scale(0.98); }

    .thumb { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

    /* Tombol Mengambang (Floating Action Button) */
    .fab {
        position: fixed; right: 20px; bottom: 92px; z-index: 35;
        background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%); color: #fff;
        border: none; border-radius: 99px; padding: 15px 20px; font-weight: 800; font-size: 13.5px;
        display: flex; align-items: center; gap: 8px; box-shadow: 0 16px 30px -14px rgba(37, 99, 235, 0.55);
        cursor: pointer; transition: transform 0.2s; text-decoration: none;
    }
    .fab:active { transform: scale(0.96); }

    /* Agar FAB tidak keluar jalur di layar laptop lebar */
    @media (min-width: 1024px) {
        .fab {
            right: calc(50% - 512px + 20px);
        }
    }
</style>
@endpush

@section('content')
<!-- Header Profil -->
<div class="topbar">
    <div class="row" style="flex:1;">
        <div class="avatar">AN</div>
        <div>
            <div style="font-size:15px;font-weight:800;">Halo, Bu Anita 👋</div>
            <div class="muted">SMP Inklusif Harapan Bangsa</div>
        </div>
    </div>
    <button class="iconbtn"><span class="material-symbols-outlined">notifications</span></button>
</div>

<div class="px">
    <!-- Hero Section -->
    <div class="card dashboard-hero" style="margin-top:16px;">
        <div class="row" style="justify-content:space-between; align-items:flex-start;">
            <div style="flex:1;">
                <div class="hero-pill"><span class="material-symbols-outlined" style="font-size:12px;">monitoring</span>Live insights</div>
                <div class="hero-title">Kinerja kelas Anda terasa solid minggu ini</div>
                <div class="hero-sub">3 kelas aktif, 15 assessment siap dipantau, dan 9 assessment sudah dipublikasikan.</div>
            </div>
            <div class="thumb" style="background:rgba(255,255,255,0.16); width:48px; height:48px; border-radius:14px;">
                <span class="material-symbols-outlined" style="color:#fff;">auto_awesome</span>
            </div>
        </div>
        <div class="hero-metrics">
            <div><span>3</span><small>Kelas aktif</small></div>
            <div><span>15</span><small>Assessment</small></div>
            <div><span>92%</span><small>Progress</small></div>
        </div>
    </div>

    <!-- Statistik Grid -->
    <div class="stat-grid">
        <div class="stat-card">
            <span class="material-symbols-outlined">groups</span>
            <div class="num">3</div><div class="lbl">Jumlah Kelas</div>
        </div>
        <div class="stat-card">
            <span class="material-symbols-outlined">assignment</span>
            <div class="num">15</div><div class="lbl">Jumlah Assessment</div>
        </div>
        <div class="stat-card">
            <span class="material-symbols-outlined">publish</span>
            <div class="num">9</div><div class="lbl">Dipublikasikan</div>
        </div>
        <div class="stat-card">
            <span class="material-symbols-outlined">school</span>
            <div class="num">84</div><div class="lbl">Total Siswa</div>
        </div>
    </div>

    <!-- Link ke Analytics -->
    <a href="#" class="card card-tap row" style="padding:14px 16px;">
        <div class="thumb" style="background:linear-gradient(135deg,var(--secondary-soft),#daf7f1);">
            <span class="material-symbols-outlined" style="color:var(--secondary);">insights</span>
        </div>
        <div style="flex:1;">
            <div style="font-weight:800; font-size:13.5px;">Lihat Analytics Kelas</div>
            <div class="muted">Rata-rata nilai & tingkat penyelesaian</div>
        </div>
        <span class="material-symbols-outlined" style="color:var(--muted);">chevron_right</span>
    </a>

    <!-- Daftar Assessment Terbaru -->
    <div class="section-title">
        Assessment Terbaru
        <a href="{{ route('guru.bank-soal') }}" class="link">Lihat Semua</a>
    </div>

    <!-- Item 1 -->
    <a href="#" class="card card-tap row" style="margin-bottom:12px; padding:14px 16px;">
        <div class="thumb" style="background:#10B9811A; width:46px; height:46px; border-radius:14px;">
            <span class="material-symbols-outlined" style="color:#10B981;">eco</span>
        </div>
        <div style="flex:1;">
            <div style="font-weight:700; font-size:13.5px;">Ekosistem & Rantai Makanan</div>
            <div class="muted">IPA · 10 Jul 2026</div>
        </div>
        <span class="badge badge-green"><span class="material-symbols-outlined" style="font-size:12px;">check_circle</span>Published</span>
    </a>

    <!-- Item 2 -->
    <a href="#" class="card card-tap row" style="margin-bottom:12px; padding:14px 16px;">
        <div class="thumb" style="background:#2563EB1A; width:46px; height:46px; border-radius:14px;">
            <span class="material-symbols-outlined" style="color:#2563EB;">calculate</span>
        </div>
        <div style="flex:1;">
            <div style="font-weight:700; font-size:13.5px;">Operasi Pecahan</div>
            <div class="muted">Matematika · 08 Jul 2026</div>
        </div>
        <span class="badge badge-amber"><span class="material-symbols-outlined" style="font-size:12px;">edit</span>Draft</span>
    </a>

</div>

<!-- Floating Action Button -->
<a href="#" class="fab">
    <span class="material-symbols-outlined" style="font-size:20px;">add</span>Buat Assessment
</a>
@endsection

@section('bottom_nav')
<div class="bottomnav">
    <!-- Request::routeIs digunakan untuk mengecek rute aktif dan memberi warna biru (class active) -->
    <a href="{{ route('guru.dashboard') }}" class="navitem {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">space_dashboard</span>Dashboard
    </a>
    <a href="{{ route('guru.kelas') }}" class="navitem {{ request()->routeIs('guru.kelas') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">groups</span>Kelas
    </a>
    <a href="{{ route('guru.bank-soal') }}" class="navitem {{ request()->routeIs('guru.bank-soal') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">folder_open</span>Assessment
    </a>
    <a href="{{ route('guru.profil') }}" class="navitem {{ request()->routeIs('guru.profil') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">person</span>Profil
    </a>
</div>
@endsection
