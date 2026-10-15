@extends('layouts.app')

@section('title', 'Dashboard Guru')

@push('styles')
<style>
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
    .hero-pill {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px;
        border-radius: 999px; background: rgba(255, 255, 255, 0.16);
        font-size: 10.5px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 8px;
    }
    .hero-title { font-size: 18px; font-weight: 800; line-height: 1.35; }
    .hero-sub { font-size: 12px; margin-top: 8px; opacity: 0.9; line-height: 1.55; }

    .hero-metrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 14px; }
    .hero-metrics a {
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 12px;
        padding: 10px 8px;
        text-align: center;
        color: inherit;
        text-decoration: none;
        display: block;
        transition: background 0.2s;
    }
    .hero-metrics a:hover {
        background: rgba(255, 255, 255, 0.22);
    }
    .hero-metrics span { display: block; font-size: 15px; font-weight: 800; text-align: center; }
    .hero-metrics small { display: block; font-size: 10px; margin-top: 2px; opacity: 0.84; text-align: center; }

    .stat-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.97) 0%, rgba(241, 247, 255, 0.95) 100%);
        border-radius: var(--radius-sm); padding: 16px;
        box-shadow: 0 14px 28px -20px rgba(15, 23, 42, 0.26); border: 1px solid rgba(255, 255, 255, 0.72);
        position: relative; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;
        text-decoration: none; display: block; color: inherit;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 18px 34px -20px rgba(15, 23, 42, 0.28); }
    .stat-card .material-symbols-outlined { font-size: 20px; color: var(--primary); background: var(--primary-soft); padding: 7px; border-radius: 10px; }
    .stat-card .num { font-size: 22px; font-weight: 800; margin-top: 10px; }
    .stat-card .lbl { font-size: 11.5px; color: var(--muted); font-weight: 600; }

    .section-title { font-size: 15px; font-weight: 800; margin: 22px 0 12px; display: flex; align-items: center; justify-content: space-between; }
    .section-title a.link { font-size: 12.5px; font-weight: 700; color: var(--primary); cursor: pointer; text-decoration: none; }

    .card-tap { cursor: pointer; transition: transform 0.15s ease; text-decoration: none; display: block; color: inherit; }
    .card-tap:active { transform: scale(0.98); }
    .thumb { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

    .fab {
        position: fixed; right: 20px; bottom: 92px; z-index: 35;
        background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%); color: #fff;
        border: none; border-radius: 99px; padding: 15px 20px; font-weight: 800; font-size: 13.5px;
        display: flex; align-items: center; gap: 8px; box-shadow: 0 16px 30px -14px rgba(37, 99, 235, 0.55);
        cursor: pointer; transition: transform 0.2s; text-decoration: none;
    }
    .fab:active { transform: scale(0.96); }
    @media (min-width: 1024px) { .fab { right: calc(50% - 512px + 20px); } }
</style>
@endpush

@section('content')
<!-- Topbar: Profil & Notifikasi -->
<div class="topbar">
    <a href="{{ route('guru.profil') }}" class="row" style="flex:1; text-decoration:none; color:inherit;">
        <div class="avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div>
            <div style="font-size:15px;font-weight:800;">
                Halo, {{ auth()->user()->name }} 👋
            </div>
            <div class="muted">Guru EquiTask AI</div>
        </div>
    </a>
    <a href="{{ route('guru.notifikasi') }}" class="iconbtn" style="text-decoration:none; display:flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; border: 1px solid var(--border); background: var(--card-bg); color: var(--primary);">
        <span class="material-symbols-outlined" style="font-size: 20px;">notifications</span>
    </a>
</div>

<div class="px">
    <!-- Hero Section: Kelas Aktif, Asesmen, & Progress -->
    <div class="card dashboard-hero" style="margin-top:16px;">
        <div class="row" style="justify-content:space-between; align-items:flex-start;">
            <div style="flex:1;">
                <div class="hero-pill"><span class="material-symbols-outlined" style="font-size:12px;">monitoring</span>Live insights</div>
                <div class="hero-title">Kinerja kelas Anda terasa solid minggu ini</div>
                <div class="hero-sub">{{ $totalKelas ?? 3 }} kelas aktif, {{ $totalAssessments ?? 0 }} assessment siap dipantau, dan {{ $publishedCount ?? 0 }} assessment sudah dipublikasikan.</div>
            </div>
            <div class="thumb" style="background:rgba(255,255,255,0.16); width:48px; height:48px; border-radius:14px;">
                <span class="material-symbols-outlined" style="color:#fff;">auto_awesome</span>
            </div>
        </div>
        <div class="hero-metrics">
            <!-- Kelas Aktif -->
            <a href="{{ route('guru.kelas') }}">
                <span>{{ $totalKelas ?? 3 }}</span>
                <small>Kelas aktif</small>
            </a>
            <!-- Asesmen -->
            <a href="{{ route('guru.assessments.index') }}">
                <span>{{ $totalAssessments ?? 0 }}</span>
                <small>Asesmen</small>
            </a>
            <!-- Progress -->
            <a href="{{ route('guru.kelas') }}">
                <span>92%</span>
                <small>Progress</small>
            </a>
        </div>
    </div>

    <!-- Statistik Grid: 2 Kolom Seimbang & Rapi -->
    <div class="stat-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; margin-top: 16px;">

        <!-- Jumlah Kelas -->
        <a href="{{ route('guru.kelas') }}" class="stat-card" style="display: flex; align-items: center; gap: 14px; padding: 16px;">
            <span class="material-symbols-outlined" style="font-size: 24px; padding: 10px; border-radius: 12px;">groups</span>
            <div>
                <div class="num" style="font-size: 20px; font-weight: 800; margin-top: 0;">{{ $totalKelas ?? 3 }}</div>
                <div class="lbl" style="font-size: 12px; color: var(--muted); font-weight: 600;">Jumlah Kelas</div>
            </div>
        </a>

        <!-- Jumlah Asesmen -->
        <a href="{{ route('guru.assessments.index') }}" class="stat-card" style="display: flex; align-items: center; gap: 14px; padding: 16px;">
            <span class="material-symbols-outlined" style="font-size: 24px; padding: 10px; border-radius: 12px;">assignment</span>
            <div>
                <div class="num" style="font-size: 20px; font-weight: 800; margin-top: 0;">{{ $totalAssessments ?? 0 }}</div>
                <div class="lbl" style="font-size: 12px; color: var(--muted); font-weight: 600;">Jumlah Asesmen</div>
            </div>
        </a>

        <!-- Dipublikasikan -->
        <a href="{{ route('guru.bank-soal') }}" class="stat-card" style="display: flex; align-items: center; gap: 14px; padding: 16px;">
            <span class="material-symbols-outlined" style="font-size: 24px; padding: 10px; border-radius: 12px;">publish</span>
            <div>
                <div class="num" style="font-size: 20px; font-weight: 800; margin-top: 0;">{{ $publishedCount ?? 0 }}</div>
                <div class="lbl" style="font-size: 12px; color: var(--muted); font-weight: 600;">Dipublikasikan</div>
            </div>
        </a>

        <!-- Total Siswa -->
        <a href="{{ route('guru.kelas') }}" class="stat-card" style="display: flex; align-items: center; gap: 14px; padding: 16px;">
            <span class="material-symbols-outlined" style="font-size: 24px; padding: 10px; border-radius: 12px;">school</span>
            <div>
                <div class="num" style="font-size: 20px; font-weight: 800; margin-top: 0;">{{ $totalSiswa ?? 84 }}</div>
                <div class="lbl" style="font-size: 12px; color: var(--muted); font-weight: 600;">Total Siswa</div>
            </div>
        </a>
    </div>

    <!-- Link ke Analytics Kelas (Tampilan Lebih Kompak & Rapi) -->
    <a href="{{ route('guru.kelas') }}" class="card card-tap row" style="padding: 16px; margin-top: 14px; display: flex; align-items: center; gap: 14px;">
        <div class="thumb" style="background: linear-gradient(135deg, var(--secondary-soft), #daf7f1); width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <span class="material-symbols-outlined" style="color: var(--secondary); font-size: 22px;">insights</span>
        </div>
        <div style="flex: 1;">
            <div style="font-weight: 800; font-size: 13.5px;">Lihat Analytics Kelas</div>
            <div class="muted" style="font-size: 12px; margin-top: 2px;">Rata-rata nilai & tingkat penyelesaian</div>
        </div>
        <span class="material-symbols-outlined" style="color: var(--muted); font-size: 20px;">chevron_right</span>
    </a>

    <!-- Daftar Assessment Terbaru & Tombol "Lihat Semua" -->
    <div class="section-title">
        Assessment Terbaru
        <a href="{{ route('guru.assessments.index') }}" class="link">Lihat Semua</a>
    </div>

    @forelse($recentAssessments ?? [] as $a)
    <a href="{{ route('guru.assessments.index') }}" class="card card-tap row" style="margin-bottom:12px; padding:14px 16px;">
        <div class="thumb" style="background: {{ $a->color ?? '#2563EB' }}1A; width:46px; height:46px; border-radius:14px;">
            <span class="material-symbols-outlined" style="color: {{ $a->color ?? '#2563EB' }};">{{ $a->icon ?? 'assignment' }}</span>
        </div>
        <div style="flex:1;">
            <div style="font-weight:700; font-size:13.5px;">{{ $a->nama }}</div>
            <div class="muted">{{ $a->mapel }} · {{ $a->created_at->format('d M Y') }}</div>
        </div>
        @if($a->status === 'Published')
            <span class="badge badge-green"><span class="material-symbols-outlined" style="font-size:12px;">check_circle</span>Published</span>
        @else
            <span class="badge badge-amber"><span class="material-symbols-outlined" style="font-size:12px;">edit</span>Draft</span>
        @endif
    </a>
    @empty
    <div class="card" style="text-align: center; padding: 24px; margin-bottom: 12px;">
        <div class="muted" style="font-size: 13px; margin-bottom: 10px;">Belum ada asesmen yang dibuat.</div>
        <a href="{{ route('guru.assessments.create') }}" class="btn btn-primary btn-sm" style="display:inline-block; text-decoration:none; padding:8px 16px; border-radius:8px; background:var(--primary); color:#fff; font-weight:700; font-size:12px;">Buat Asesmen Sekarang</a>
    </div>
    @endforelse

</div>

<!-- Tombol Mengambang "Buat Assessment" -->
<a href="{{ route('guru.assessments.create') }}" class="fab">
    <span class="material-symbols-outlined" style="font-size:20px;">add</span>Buat Assessment
</a>
@endsection

@section('bottom_nav')
<div class="bottomnav">
    <a href="{{ route('guru.dashboard') }}" class="navitem {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">space_dashboard</span>Dashboard
    </a>
    <a href="{{ route('guru.kelas') }}" class="navitem {{ request()->routeIs('guru.kelas') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">groups</span>Kelas
    </a>
    <a href="{{ route('guru.assessments.index') }}" class="navitem {{ request()->routeIs('assessments*') || request()->routeIs('guru.assessments*') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">library_books</span>Asesmen
    </a>
    <a href="{{ route('guru.bank-soal') }}" class="navitem {{ request()->routeIs('guru.bank-soal') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">folder_open</span>Bank Soal
    </a>
    <a href="{{ route('guru.profil') }}" class="navitem {{ request()->routeIs('guru.profil') ? 'active' : '' }}" style="text-decoration:none;">
        <span class="material-symbols-outlined">person</span>Profil
    </a>
</div>
@endsection
