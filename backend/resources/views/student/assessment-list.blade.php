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


@php
    $isDemo = $assessments->isEmpty();

    $demoAssessments = collect([
        (object) [
            'id' => 1,
            'nama' => 'Ekosistem dan Rantai Makanan',
            'mapel' => 'IPA',
            'icon' => 'eco',
            'color' => '#16A34A',
            'created_at' => now(),
        ],
        (object) [
            'id' => 2,
            'nama' => 'Keanekaragaman Hayati',
            'mapel' => 'IPA',
            'icon' => 'science',
            'color' => '#2563EB',
            'created_at' => now(),
        ],
        (object) [
            'id' => 3,
            'nama' => 'Perubahan Lingkungan',
            'mapel' => 'IPA',
            'icon' => 'psychology',
            'color' => '#9333EA',
            'created_at' => now(),
        ],
    ]);

    $displayAssessments = $isDemo ? $demoAssessments : $assessments;
@endphp

@if ($isDemo)
    <div style="margin-bottom: 16px; padding: 12px 16px;
                background: #FEF3C7; color: #92400E;
                border-radius: 10px; font-size: 13px;">
        Mode demo — assessment di bawah ini adalah data contoh,
        bukan assessment asli dari guru.
    </div>
@endif
<!-- 
/* SIMULASI */
<div class="assessment-list">
    @foreach ($displayAssessments as $assessment)
        <div class="assessment-item"
             style="margin-bottom: 16px; padding: 20px;
                    border: 1px solid #E2E8F0;
                    border-radius: 14px; background: #fff;">

            <div style="display: flex; gap: 14px; align-items: flex-start;">
                <div style="width: 46px; height: 46px; flex-shrink: 0;
                            border-radius: 12px;
                            background: {{ $assessment->color ?? '#2563EB' }}20;
                            display: grid; place-items: center;
                            font-size: 22px;">
                    📝
                </div>

                <div style="flex: 1; min-width: 0;">
                    <h3 style="margin: 0 0 8px; font-size: 17px;">
                        {{ $assessment->nama }}
                    </h3>

                    <p style="margin: 0 0 12px; color: #64748B;
                              font-size: 14px;">
                        Mata pelajaran: {{ $assessment->mapel ?? 'Umum' }}
                    </p>

                    @if ($isDemo)
                        <a href="{{ route('siswa.assessment-demo', $assessment->id) }}"
                           style="display: inline-block; padding: 10px 15px;
                                  border-radius: 9px; background: #2563EB;
                                  color: #fff; text-decoration: none;
                                  font-size: 14px; font-weight: 600;">
                            Coba Simulasi →
                        </a>
                    @else
                        <span style="font-size: 13px; color: #64748B;">
                            Assessment tersedia
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

 -->
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