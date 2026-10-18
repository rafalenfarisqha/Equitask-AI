
@extends('layouts.app')

@section('title', 'Riwayat Belajar')

@push('styles')
<style>
    .history-hero {
        background: linear-gradient(135deg, #173f8f, #2563eb, #0f766e);
        color: white;
        border: none;
        padding: 22px;
    }

    .history-hero .muted {
        color: rgba(255,255,255,.8);
    }

    .history-stat {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 18px;
    }

    .history-stat > div {
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 14px;
        padding: 12px;
    }

    .history-stat strong {
        display: block;
        font-size: 22px;
    }

    .history-stat small {
        font-size: 11px;
        opacity: .85;
    }

    .history-item {
        padding: 16px;
        margin-bottom: 12px;
    }

    .history-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: var(--primary);
        background: var(--primary-soft);
    }

    .history-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 800;
        padding: 6px 9px;
        border-radius: 999px;
        background: var(--primary-soft);
        color: var(--primary);
    }

    .history-empty {
        text-align: center;
        padding: 34px 20px;
    }

    .history-empty .material-symbols-outlined {
        font-size: 48px;
        color: #94a3b8;
    }
</style>
@endpush

@section('content')
<div class="topbar">
    <div class="history-icon">
        <span class="material-symbols-outlined">history</span>
    </div>
    <div style="flex:1;">
        <h1>Riwayat Belajar</h1>
        <div class="muted">Perjalanan belajarmu di EquiTask AI</div>
    </div>
</div>

<div class="px" style="padding-top:18px;">

    <div class="card history-hero">
        <div style="font-size:12px;font-weight:800;opacity:.85;">
            RINGKASAN BELAJAR
        </div>

        <div style="font-size:21px;font-weight:800;margin-top:8px;">
            Tetap semangat, {{ $user->name }}!
        </div>

        <div class="muted" style="margin-top:6px;line-height:1.5;">
            Lihat kembali assessment yang pernah kamu kerjakan.
        </div>

        <div class="history-stat">
            <div>
                <strong>{{ $totalRiwayat }}</strong>
                <small>Total aktivitas</small>
            </div>
            <div>
                <strong>{{ $selesai }}</strong>
                <small>Assessment selesai</small>
            </div>
        </div>
    </div>

    <div style="font-size:16px;font-weight:800;margin:24px 0 14px;">
        Riwayat Assessment
    </div>

    @forelse($riwayat as $item)
        @php
            $status = strtolower($item->status ?? 'unknown');

            if (in_array($status, ['submitted', 'completed'])) {
                $statusLabel = 'Selesai';
                $statusIcon = 'check_circle';
                $statusColor = '#047857';
                $statusBg = '#d1fae5';
            } elseif (in_array($status, ['in_progress', 'started'])) {
                $statusLabel = 'Sedang dikerjakan';
                $statusIcon = 'pending';
                $statusColor = '#b45309';
                $statusBg = '#fef3c7';
            } else {
                $statusLabel = ucfirst(str_replace('_', ' ', $status));
                $statusIcon = 'info';
                $statusColor = '#475569';
                $statusBg = '#f1f5f9';
            }
        @endphp

        <div class="card history-item">
            <div class="row" style="align-items:flex-start;">
                <div class="history-icon">
                    <span class="material-symbols-outlined">
                        assignment
                    </span>
                </div>

                <div style="flex:1;min-width:0;">
                    <div style="font-weight:800;font-size:14px;line-height:1.5;">
                        {{ $item->nama ?: 'Assessment tanpa judul' }}
                    </div>

                    <div class="muted" style="margin-top:4px;">
                        {{ $item->mapel ?: 'Mata pelajaran belum tersedia' }}
                    </div>

                    <div class="muted" style="margin-top:8px;">
                        @if($item->submitted_at)
                            Dikumpulkan:
                            {{ \Carbon\Carbon::parse($item->submitted_at)->format('d M Y, H:i') }}
                        @elseif($item->started_at)
                            Dimulai:
                            {{ \Carbon\Carbon::parse($item->started_at)->format('d M Y, H:i') }}
                        @else
                            Tanggal belum tersedia
                        @endif
                    </div>
                </div>
            </div>

            <div class="row"
                 style="justify-content:space-between;flex-wrap:wrap;margin-top:14px;">

                <span class="history-status"
                      style="color:{{ $statusColor }};background:{{ $statusBg }};">
                    <span class="material-symbols-outlined" style="font-size:14px;">
                        {{ $statusIcon }}
                    </span>
                    {{ $statusLabel }}
                </span>

                <div style="font-size:13px;font-weight:800;">
                    @if($item->score !== null)
                        Nilai: {{ rtrim(rtrim(number_format((float) $item->score, 2, ',', '.'), '0'), ',') }}
                    @else
                        <span class="muted">Nilai belum tersedia</span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="card history-empty">
            <span class="material-symbols-outlined">history_toggle_off</span>

            <div style="font-size:16px;font-weight:800;margin-top:12px;">
                Belum ada riwayat belajar
            </div>

            <div class="muted"
                 style="margin-top:7px;line-height:1.6;">
                Assessment yang sudah kamu mulai atau kumpulkan akan muncul di halaman ini.
            </div>

            <a href="{{ route('siswa.assessment') }}"
               class="btn btn-primary"
               style="text-decoration:none;margin-top:18px;">
                <span class="material-symbols-outlined">assignment</span>
                Lihat Assessment
            </a>
        </div>
    @endforelse

    <div style="height:24px;"></div>
</div>
@endsection

@section('bottom_nav')
<div class="bottomnav">
    <a href="{{ route('siswa.dashboard') }}"
       class="navitem {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">home</span>
        Beranda
    </a>

    <a href="{{ route('siswa.assessment') }}"
       class="navitem {{ request()->routeIs('siswa.assessment') ? 'active' : '' }}"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">assignment</span>
        Assessment
    </a>

    <a href="{{ route('siswa.riwayat') }}"
       class="navitem {{ request()->routeIs('siswa.riwayat') ? 'active' : '' }}"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">history</span>
        Riwayat
    </a>

    <a href="{{ route('siswa.profil') }}"
       class="navitem {{ request()->routeIs('siswa.profil') ? 'active' : '' }}"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">person</span>
        Profil
    </a>
</div>
@endsection
