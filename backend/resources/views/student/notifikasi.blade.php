
@extends('layouts.app')

@section('title', 'Notifikasi Siswa')

@push('styles')
<style>
    /* ===== Halaman Notifikasi Siswa ===== */
    .notification-screen {
        width: 100%;
        min-height: 100vh;
        box-sizing: border-box;
        padding-bottom: 100px !important;
        background: var(--bg);
    }

    .notification-screen *,
    .notification-screen *::before,
    .notification-screen *::after {
        box-sizing: border-box;
    }

    /* Header */
    .notification-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 22px 24px 18px;
        border-bottom: 1px solid var(--border, #e2e8f0);
        background: var(--card-bg, #fff);
    }

    .notification-back {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        flex-shrink: 0;
        border-radius: 12px;
        color: var(--text);
        text-decoration: none;
        transition: background .2s ease;
    }

    .notification-back:hover {
        background: var(--bg, #f1f5f9);
    }

    .notification-back .material-symbols-outlined {
        font-size: 23px;
    }

    .notification-heading {
        margin: 0;
        color: var(--text);
        font-size: 19px;
        font-weight: 800;
        line-height: 1.4;
    }

    .notification-subheading {
        margin: 3px 0 0;
        color: var(--muted);
        font-size: 12px;
    }

    /* Area daftar notifikasi */
    .notification-content {
        display: flex;
        flex-direction: column;
        gap: 14px;
        width: 100%;
        max-width: 850px;
        margin: 0 auto;
        padding: 20px 24px 28px;
    }

    /* Kartu notifikasi */
    .notification-card {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        width: 100%;
        min-width: 0;
        padding: 18px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-left: 4px solid var(--notification-accent, #2563eb);
        border-radius: 14px;
        background: var(--card-bg, #fff);
        box-shadow: 0 2px 8px rgba(15, 23, 42, .035);
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .notification-card:hover {
        box-shadow: 0 5px 16px rgba(15, 23, 42, .07);
        transform: translateY(-1px);
    }

    .notification-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        border-radius: 12px;
        background: var(--notification-icon-bg, #eff6ff);
        color: var(--notification-accent, #2563eb);
    }

    .notification-icon .material-symbols-outlined {
        font-size: 21px;
    }

    .notification-body {
        flex: 1;
        min-width: 0;
    }

    .notification-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 5px 12px;
        margin: 1px 0 7px;
    }

    .notification-title {
        margin: 0;
        color: var(--text);
        font-size: 14px;
        font-weight: 800;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .notification-time {
        flex-shrink: 0;
        color: var(--muted);
        font-size: 11px;
        line-height: 1.6;
    }

    .notification-message {
        margin: 0;
        color: var(--muted, #64748b);
        font-size: 13px;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    .notification-message p {
        margin: 0 0 6px;
    }

    .notification-message p:last-child {
        margin-bottom: 0;
    }

    /* Tampilan ketika belum ada notifikasi */
    .notification-empty {
        padding: 48px 24px;
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        background: var(--card-bg, #fff);
        text-align: center;
    }

    .notification-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        margin: 0 auto 16px;
        border-radius: 20px;
        background: #eff6ff;
        color: #64748b;
    }

    .notification-empty-icon .material-symbols-outlined {
        font-size: 32px;
    }

    .notification-empty-title {
        margin: 0;
        color: var(--text);
        font-size: 16px;
        font-weight: 800;
    }

    .notification-empty-description {
        max-width: 320px;
        margin: 8px auto 0;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.7;
    }

    /* Navigasi bawah */
    .bottomnav .navitem {
        text-decoration: none;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 576px) {
        .notification-header {
            gap: 10px;
            padding: 16px;
        }

        .notification-heading {
            font-size: 17px;
        }

        .notification-content {
            gap: 12px;
            padding: 16px 16px 24px;
        }

        .notification-card {
            gap: 12px;
            padding: 15px 13px;
            border-radius: 12px;
        }

        .notification-icon {
            width: 38px;
            height: 38px;
            flex-basis: 38px;
            border-radius: 11px;
        }

        .notification-card-top {
            flex-direction: column;
            gap: 3px;
        }

        .notification-title {
            font-size: 13px;
        }

        .notification-time {
            font-size: 10px;
        }

        .notification-message {
            font-size: 12px;
        }

        .notification-empty {
            padding: 36px 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="screen notification-screen">

    <!-- Header -->
    <header class="notification-header">
        <a
            href="{{ route('siswa.dashboard') }}"
            class="notification-back"
            aria-label="Kembali ke dashboard"
        >
            <span class="material-symbols-outlined">arrow_back</span>
        </a>

        <div>
            <h1 class="notification-heading">Notifikasi Saya</h1>
            <p class="notification-subheading">
                Informasi dan aktivitas akunmu
            </p>
        </div>
    </header>

    <!-- Daftar notifikasi -->
    <main class="notification-content">

        @forelse($notifications ?? [] as $notif)
            @php
                $borderColor = '#2563eb';
                $bgColor = 'rgba(37, 99, 235, 0.10)';
                $icon = 'notifications';

                if ($notif->type == 'assessment') {
                    $borderColor = '#10B981';
                    $bgColor = 'rgba(16, 185, 129, 0.12)';
                    $icon = 'assignment';
                } elseif ($notif->type == 'security') {
                    $borderColor = '#8B5CF6';
                    $bgColor = 'rgba(139, 92, 246, 0.12)';
                    $icon = 'security';
                }
            @endphp

            <article
                class="notification-card"
                style="
                    --notification-accent: {{ $borderColor }};
                    --notification-icon-bg: {{ $bgColor }};
                "
            >
                <div class="notification-icon">
                    <span class="material-symbols-outlined">
                        {{ $icon }}
                    </span>
                </div>

                <div class="notification-body">
                    <div class="notification-card-top">
                        <h2 class="notification-title">
                            {{ $notif->title }}
                        </h2>

                        <time class="notification-time">
                            {{ $notif->created_at
                                ? $notif->created_at->diffForHumans()
                                : '' }}
                        </time>
                    </div>

                    <div class="notification-message">
                        {!! $notif->message !!}
                    </div>
                </div>
            </article>

        @empty
            <div class="notification-empty">
                <div class="notification-empty-icon">
                    <span class="material-symbols-outlined">
                        notifications_off
                    </span>
                </div>

                <h2 class="notification-empty-title">
                    Belum ada notifikasi
                </h2>

                <p class="notification-empty-description">
                    Informasi asesmen baru dari guru akan muncul
                    di sini secara otomatis.
                </p>
            </div>
        @endforelse

    </main>
</div>
@endsection

@section('bottom_nav')
<div class="bottomnav">
    <a
        href="{{ route('siswa.dashboard') }}"
        class="navitem"
        style="text-decoration: none;"
    >
        <span class="material-symbols-outlined">space_dashboard</span>
        Dashboard
    </a>

    <a
        href="{{ route('siswa.notifikasi') }}"
        class="navitem active"
        style="text-decoration: none;"
    >
        <span class="material-symbols-outlined">notifications</span>
        Notifikasi
    </a>
</div>
@endsection
