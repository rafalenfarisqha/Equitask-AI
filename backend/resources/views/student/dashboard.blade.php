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
        <div class="avatar-siswa">
            {{ strtoupper($initials ?: 'S') }}
        </div>

        <div>
            <div style="font-size:15px;font-weight:800;">
                Halo, {{ $user->name }} 👋
            </div>

            <div class="muted">
                @if($classes->isNotEmpty())
                    {{ $classes->pluck('name')->join(', ') }}
                @else
                    Belum bergabung ke kelas
                @endif
            </div>
        </div>
    </div>

    <a href="{{ route('siswa.notifikasi') }}"
       class="iconbtn"
       aria-label="Buka notifikasi"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">notifications</span>
    </a>
</div>

<div class="px" style="padding-top: 16px;">

    <!-- Hero Banner Siswa -->
    <div class="card student-dashboard-hero">
        <div class="row" style="justify-content:space-between; align-items:flex-start;">
            <div style="flex:1;">
        <div class="student-badge">
            <span class="material-symbols-outlined" style="font-size:11px;">
                auto_awesome
            </span>
            Fokus hari ini
        </div>

        <div style="font-size:18px;font-weight:800;margin-top:8px;line-height:1.35;">
            Selamat belajar, {{ $user->name }}!
        </div>

        <div class="muted" style="margin-top:8px;line-height:1.5;">
            Yuk, cek assessment yang tersedia dan lanjutkan proses belajarmu.
        </div>
    </div>

    <div class="thumb"
         style="background:linear-gradient(135deg,var(--primary-soft),#dbeafe);
                color:var(--primary);width:48px;height:48px;border-radius:14px;">
        <span class="material-symbols-outlined">auto_stories</span>
    </div>
</div>

<div class="student-quick-grid">
    <div>
        <strong>{{ $totalAssessment }}</strong>
        <small>assessment tersedia</small>
    </div>

    <div>
        <strong>{{ $totalKelas }}</strong>
        <small>kelas diikuti</small>
    </div>
</div>
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
    @if($assessments->isNotEmpty())
    @foreach($assessments->take(1) as $assessment)
        <a href="{{ route('siswa.assessment') }}"
           class="card card-tap student-focus-card"
           style="margin-top:14px;padding:14px 16px;text-decoration:none;">

            <div class="row" style="justify-content:space-between;gap:10px;">
                <div class="row" style="flex:1;min-width:0;">
                    <div class="thumb"
                         style="background:linear-gradient(135deg,var(--primary-soft),#dbeafe);">
                        <span class="material-symbols-outlined"
                              style="color:var(--primary);">
                            {{ $assessment->icon ?: 'assignment' }}
                        </span>
                    </div>

                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:800;font-size:13.5px;">
                            {{ $assessment->nama }}
                        </div>

                        <div class="muted" style="margin-top:4px;">
                            {{ $assessment->mapel }}
                        </div>
                    </div>
                </div>

                <div class="student-focus-pill">
                    Lihat
                    <span class="material-symbols-outlined" style="font-size:13px;">
                        arrow_forward
                    </span>
                </div>
            </div>
        </a>
    @endforeach
@else
    <div class="card" style="margin-top:14px;text-align:center;padding:24px;">
        <span class="material-symbols-outlined"
              style="font-size:36px;color:var(--muted);">
            assignment
        </span>

        <div style="font-weight:800;margin-top:8px;">
            Belum ada assessment tersedia
        </div>

        <div class="muted" style="margin-top:5px;">
            Assessment publik yang sudah diterbitkan guru akan muncul di sini.
        </div>

        <a href="{{ route('siswa.assessment') }}"
           class="btn btn-primary"
           style="margin-top:14px;text-decoration:none;">
            Lihat daftar assessment
        </a>
    </div>
@endif

    <!-- Statistik Dashboard Siswa -->
<div class="stat-grid" style="margin-top:14px;">

    <div class="stat-card">
        <span class="material-symbols-outlined"
              style="color:var(--primary);background:var(--primary-soft);
                     padding:7px;border-radius:10px;">
            assignment
        </span>

        <div class="num" style="font-size:22px;font-weight:800;margin-top:10px;">
            {{ $totalAssessment }}
        </div>

        <div class="lbl" style="font-size:11.5px;color:var(--muted);font-weight:600;">
            Assessment tersedia
        </div>
    </div>

    <div class="stat-card">
        <span class="material-symbols-outlined"
              style="color:var(--secondary);background:var(--secondary-soft);
                     padding:7px;border-radius:10px;">
            groups
        </span>

        <div class="num" style="font-size:22px;font-weight:800;margin-top:10px;">
            {{ $totalKelas }}
        </div>

        <div class="lbl" style="font-size:11.5px;color:var(--muted);font-weight:600;">
            Kelas yang diikuti
        </div>
    </div>

</div>

    <!-- Rencana Belajar -->
    <div class="section-title">Kelas Saya</div>

<div class="card" style="padding:12px 14px;">

    @forelse($classes as $class)
        <div class="row"
             style="margin-bottom:12px;align-items:flex-start;">

            <div class="thumb"
                 style="background:var(--primary-soft);">
                <span class="material-symbols-outlined"
                      style="color:var(--primary);">
                    school
                </span>
            </div>

            <div style="flex:1;">
                <div style="font-weight:800;font-size:13px;">
                    {{ $class->name }}
                </div>

                <div class="muted" style="margin-top:4px;">
                    {{ $class->description ?: 'Kelas belajar EquiTask AI' }}
                </div>
            </div>
        </div>
    @empty
        <div style="text-align:center;padding:20px 8px;">
            <span class="material-symbols-outlined"
                  style="font-size:36px;color:var(--muted);">
                school
            </span>

            <div style="font-weight:800;margin-top:8px;">
                Kamu belum bergabung ke kelas
            </div>

            <div class="muted" style="margin-top:5px;">
                Setelah guru menambahkanmu ke kelas, informasi kelas akan muncul di sini.
            </div>
        </div>
    @endforelse

</div>

<div style="height:24px;"></div>

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
