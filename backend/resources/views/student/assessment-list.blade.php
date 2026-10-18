@extends('layouts.app')

@section('title', 'Daftar Assessment')

@section('content')
<div class="topbar">
    <a href="{{ route('siswa.dashboard') }}"
       class="iconbtn"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>

    <h1>Daftar Assessment</h1>
</div>

<div class="px" style="padding-top:20px;">

    <div style="margin-bottom:18px;">
        <div style="font-size:22px;font-weight:800;">
            Assessment untukmu
        </div>

        <div class="muted" style="margin-top:6px;">
            Pilih assessment yang tersedia untuk melihat daftar tugas belajar.
        </div>
    </div>

    @forelse($assessments as $assessment)
        <div class="card" style="padding:16px;">
            <div class="row" style="align-items:flex-start;">
                <div class="thumb"
                     style="background:var(--primary-soft);">
                    <span class="material-symbols-outlined"
                          style="color:var(--primary);">
                        {{ $assessment->icon ?: 'assignment' }}
                    </span>
                </div>

                <div style="flex:1;">
                    <div style="font-weight:800;font-size:14px;">
                        {{ $assessment->nama }}
                    </div>

                    <div class="muted" style="margin-top:5px;">
                        Mata pelajaran: {{ $assessment->mapel }}
                    </div>

                    <div class="muted" style="margin-top:5px;">
                        Dipublikasikan:
                        {{ \Carbon\Carbon::parse($assessment->created_at)->format('d M Y') }}
                    </div>
                </div>
            </div>

            <div style="margin-top:14px;">
                <span class="badge badge-green">Tersedia</span>
            </div>
        </div>
    @empty
        <div class="card" style="text-align:center;padding:32px 18px;">
            <span class="material-symbols-outlined"
                  style="font-size:44px;color:var(--muted);">
                assignment_late
            </span>

            <div style="font-weight:800;margin-top:12px;">
                Belum ada assessment
            </div>

            <div class="muted" style="margin-top:6px;">
                Belum ada assessment publik yang diterbitkan guru.
            </div>
        </div>
    @endforelse

    <div style="height:20px;"></div>
</div>
@endsection

@section('bottom_nav')
<div class="bottomnav">
    <a href="{{ route('siswa.dashboard') }}"
       class="navitem"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">home</span>
        Beranda
    </a>

    <a href="{{ route('siswa.assessment') }}"
       class="navitem active"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">assignment</span>
        Assessment
    </a>

    <a href="{{ route('siswa.riwayat') }}"
       class="navitem"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">history</span>
        Riwayat
    </a>

    <a href="{{ route('siswa.profil') }}"
       class="navitem"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">person</span>
        Profil
    </a>
</div>
@endsection