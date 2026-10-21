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

    /* ==========================================
       TAMBAHAN: TOMBOL LIHAT HASIL
       ========================================== */

    .history-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-top: 15px;
        padding-top: 14px;
        border-top: 1px solid #e5eaf2;
    }

    .history-result-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 10px 16px;
        border-radius: 11px;
        background: #2563eb;
        color: white;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        transition: background .2s ease, transform .2s ease;
    }

    .history-result-btn:hover {
        background: #1d4ed8;
        color: white;
        transform: translateY(-1px);
    }

    .history-result-btn:focus-visible {
        outline: 3px solid #93c5fd;
        outline-offset: 3px;
    }

    .history-result-btn .material-symbols-outlined {
        font-size: 19px;
    }

    .history-pending-note {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .history-pending-note .material-symbols-outlined {
        font-size: 17px;
    }

    .history-score-note {
        margin-top: 6px;
        font-size: 11px;
        font-weight: 500;
        line-height: 1.5;
        color: #64748b;
        text-align: right;
    }

    @media (max-width: 480px) {
        .history-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .history-result-btn {
            width: 100%;
        }

        .history-score-note {
            text-align: left;
        }
    }
</style>
@endpush

@section('content')

<div class="topbar">

    <div class="history-icon">
        <span class="material-symbols-outlined">
            history
        </span>
    </div>

    <div style="flex:1;">
        <h1>Riwayat Belajar</h1>

        <div class="muted">
            Perjalanan belajarmu di EquiTask AI
        </div>
    </div>

</div>

<div class="px" style="padding-top:18px;">

    {{-- ==========================================
         RINGKASAN BELAJAR
         ========================================== --}}

    <div class="card history-hero">

        <div style="font-size:12px;font-weight:800;opacity:.85;">
            RINGKASAN BELAJAR
        </div>

        <div style="font-size:21px;font-weight:800;margin-top:8px;">
            Tetap semangat, {{ $user->name }}!
        </div>

        <div class="muted"
             style="margin-top:6px;line-height:1.5;">
            Lihat kembali assessment yang pernah kamu kerjakan.
        </div>

        <div class="history-stat">

            <div>
                <strong>
                    {{ $totalRiwayat }}
                </strong>

                <small>
                    Total aktivitas
                </small>
            </div>

            <div>
                <strong>
                    {{ $selesai }}
                </strong>

                <small>
                    Assessment selesai
                </small>
            </div>

        </div>

    </div>

    {{-- ==========================================
         DAFTAR RIWAYAT ASSESSMENT
         ========================================== --}}

    <div style="font-size:16px;font-weight:800;margin:24px 0 14px;">
        Riwayat Assessment
    </div>

    @forelse($riwayat as $item)

        @php
            /*
             * Normalisasi status pengerjaan.
             */
            $status = strtolower(
                trim((string) ($item->status ?? 'unknown'))
            );

            /*
             * Selesai = jawaban sudah dikumpulkan.
             */
            $isCompleted = in_array(
                $status,
                ['submitted', 'completed'],
                true
            );

            if ($isCompleted) {

                $statusLabel = 'Selesai';
                $statusIcon = 'check_circle';
                $statusColor = '#047857';
                $statusBg = '#d1fae5';

            } elseif (in_array(
                $status,
                ['in_progress', 'started'],
                true
            )) {

                $statusLabel = 'Sedang dikerjakan';
                $statusIcon = 'pending';
                $statusColor = '#b45309';
                $statusBg = '#fef3c7';

            } else {

                $statusLabel = ucfirst(
                    str_replace('_', ' ', $status)
                );

                $statusIcon = 'info';
                $statusColor = '#475569';
                $statusBg = '#f1f5f9';
            }

            /*
             * ID pengerjaan siswa.
             *
             * Pada query riwayat yang umum,
             * $item->id berasal dari student_assessments.id.
             *
             * Jika StudentPageController memakai alias
             * attempt_id, alias tersebut juga didukung.
             */
            $attemptId = $item->attempt_id
                ?? $item->id
                ?? null;

            /*
             * Riwayat tidak otomatis tahu apakah
             * esai sudah dikoreksi guru.
             *
             * Karena itu, skor tersimpan ditampilkan
             * sebagai nilai tercatat, bukan dijamin final.
             */
            $storedScore = $item->score ?? null;

        @endphp

        <div class="card history-item">

            {{-- INFORMASI ASSESSMENT --}}

            <div class="row"
                 style="align-items:flex-start;">

                <div class="history-icon">

                    <span class="material-symbols-outlined">
                        assignment
                    </span>

                </div>

                <div style="flex:1;min-width:0;">

                    <div
                        style="
                            font-weight:800;
                            font-size:14px;
                            line-height:1.5;
                        ">

                        {{ $item->nama ?: 'Assessment tanpa judul' }}

                    </div>

                    <div class="muted"
                         style="margin-top:4px;">

                        {{ $item->mapel ?: 'Mata pelajaran belum tersedia' }}

                    </div>

                    <div class="muted"
                         style="margin-top:8px;">

                        @if(!empty($item->submitted_at))

                            Dikumpulkan:
                            {{ \Carbon\Carbon::parse(
                                $item->submitted_at
                            )->format('d M Y, H:i') }}

                        @elseif(!empty($item->started_at))

                            Dimulai:
                            {{ \Carbon\Carbon::parse(
                                $item->started_at
                            )->format('d M Y, H:i') }}

                        @else

                            Tanggal belum tersedia

                        @endif

                    </div>

                </div>

            </div>

            {{-- ======================================
                 STATUS DAN NILAI
                 ====================================== --}}

            <div
                class="row"
                style="
                    justify-content:space-between;
                    align-items:flex-start;
                    gap:12px;
                    flex-wrap:wrap;
                    margin-top:14px;
                ">

                {{-- STATUS PENGERJAAN --}}

                <span
                    class="history-status"
                    style="
                        color:{{ $statusColor }};
                        background:{{ $statusBg }};
                    ">

                    <span
                        class="material-symbols-outlined"
                        style="font-size:14px;">

                        {{ $statusIcon }}

                    </span>

                    {{ $statusLabel }}

                </span>

                {{-- NILAI --}}

                <div style="font-size:13px;font-weight:800;">

                    @if($isCompleted && $storedScore !== null)

                        <div style="text-align:right;">

                            Nilai tercatat:
                            {{ rtrim(
                                rtrim(
                                    number_format(
                                        (float) $storedScore,
                                        2,
                                        ',',
                                        '.'
                                    ),
                                    '0'
                                ),
                                ','
                            ) }}

                        </div>

                        <div class="history-score-note">
                            Nilai bisa berubah setelah koreksi esai.
                        </div>

                    @elseif($isCompleted)

                        <span class="muted">
                            Nilai belum tersedia
                        </span>

                    @else

                        <span class="muted">
                            Belum dikumpulkan
                        </span>

                    @endif

                </div>

            </div>

            {{-- ======================================
                 TOMBOL LIHAT HASIL
                 ====================================== --}}

            @if($isCompleted && $attemptId !== null)

                <div class="history-actions">

                    <a
                        href="{{ route(
                            'siswa.assessment.result',
                            ['attemptId' => $attemptId]
                        ) }}"
                        class="history-result-btn">

                        <span
                            class="material-symbols-outlined"
                            aria-hidden="true">

                            analytics

                        </span>

                        Lihat Hasil

                    </a>

                    <span class="history-pending-note">

                        <span
                            class="material-symbols-outlined"
                            aria-hidden="true">

                            info

                        </span>

                        Buka rincian jawaban dan catatan belajarmu.

                    </span>

                </div>

            @endif

        </div>

    @empty

        {{-- ======================================
             RIWAYAT KOSONG
             ====================================== --}}

        <div class="card history-empty">

            <span class="material-symbols-outlined">
                history_toggle_off
            </span>

            <div
                style="
                    font-size:16px;
                    font-weight:800;
                    margin-top:12px;
                ">

                Belum ada riwayat belajar

            </div>

            <div
                class="muted"
                style="
                    margin-top:7px;
                    line-height:1.6;
                ">

                Assessment yang sudah kamu mulai atau
                kumpulkan akan muncul di halaman ini.

            </div>

            <a
                href="{{ route('siswa.assessment') }}"
                class="btn btn-primary"
                style="
                    text-decoration:none;
                    margin-top:18px;
                ">

                <span class="material-symbols-outlined">
                    assignment
                </span>

                Lihat Assessment

            </a>

        </div>

    @endforelse

    <div style="height:24px;"></div>

</div>

@endsection

{{-- ==========================================
     NAVIGASI BAWAH
     ========================================== --}}

@section('bottom_nav')

<div class="bottomnav">

    <a
        href="{{ route('siswa.dashboard') }}"
        class="navitem {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}"
        style="text-decoration:none;">

        <span class="material-symbols-outlined">
            home
        </span>

        Beranda

    </a>

    <a
        href="{{ route('siswa.assessment') }}"
        class="navitem {{ request()->routeIs('siswa.assessment') ? 'active' : '' }}"
        style="text-decoration:none;">

        <span class="material-symbols-outlined">
            assignment
        </span>

        Assessment

    </a>

    <a
        href="{{ route('siswa.riwayat') }}"
        class="navitem {{ request()->routeIs('siswa.riwayat') ? 'active' : '' }}"
        style="text-decoration:none;">

        <span class="material-symbols-outlined">
            history
        </span>

        Riwayat

    </a>

    <a
        href="{{ route('siswa.profil') }}"
        class="navitem {{ request()->routeIs('siswa.profil') ? 'active' : '' }}"
        style="text-decoration:none;">

        <span class="material-symbols-outlined">
            person
        </span>

        Profil

    </a>

</div>

@endsection
