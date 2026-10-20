@extends('layouts.app')

@section('content')
@php
    $total = $assessments->count();
    $draft = $assessments->where('status', 'Draft')->count();
    $published = $assessments->where('status', 'Published')->count();
@endphp

<style>
    .assessment-page {
        --as-primary: #2864f0;
        --as-ink: #17233d;
        --as-muted: #71809c;
        --as-border: #e0e8f8;
        min-height: 100vh;
        background: #f4f7ff;
        padding: 28px 30px 110px;
        color: var(--as-ink);
        font-family: inherit;
    }

    .as-container {
        max-width: 1120px;
        margin: 0 auto;
    }

    .as-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 26px;
    }

    .as-eyebrow {
        color: var(--as-primary);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 7px;
    }

    .as-title {
        font-size: clamp(25px, 3vw, 32px);
        line-height: 1.2;
        font-weight: 800;
        letter-spacing: -0.8px;
        margin: 0 0 8px;
        color: var(--as-ink);
    }

    .as-subtitle {
        color: var(--as-muted);
        font-size: 14px;
        line-height: 1.6;
        margin: 0;
    }

    .as-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 18px;
        border: 1px solid transparent;
        border-radius: 13px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: .2s ease;
        white-space: nowrap;
        font-family: inherit;
    }

    .as-btn-primary {
        background: var(--as-primary);
        color: #fff;
        box-shadow: 0 6px 16px #2864f026;
    }

    .as-btn-primary:hover {
        background: #174ed1;
        color: #fff;
        transform: translateY(-1px);
    }

    .as-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 25px;
    }

    .as-stat {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 21px;
        background: #fff;
        border: 1px solid var(--as-border);
        border-radius: 18px;
        box-shadow: 0 5px 18px #1e3a6b05;
    }

    .as-stat-icon {
        width: 47px;
        height: 47px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .as-stat-icon .material-symbols-outlined {
        font-size: 24px;
    }

    .as-stat-number {
        font-size: 25px;
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -.5px;
    }

    .as-stat-label {
        margin-top: 5px;
        color: var(--as-muted);
        font-size: 12px;
    }

    .as-panel {
        background: #fff;
        border: 1px solid var(--as-border);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px #1e3a6b05;
    }

    .as-panel-heading {
        padding: 22px 24px 17px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .as-panel-title {
        margin: 0;
        font-size: 17px;
        font-weight: 800;
    }

    .as-panel-description {
        color: var(--as-muted);
        font-size: 12px;
        margin-top: 5px;
    }

    .as-toolbar {
        padding: 0 24px 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .as-search {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 220px;
        flex: 1;
        height: 45px;
        padding: 0 14px;
        background: #f7f9fe;
        border: 1px solid var(--as-border);
        border-radius: 12px;
    }

    .as-search > .material-symbols-outlined {
        color: #8a98b0;
        font-size: 21px;
    }

    .as-search input {
        width: 100%;
        min-width: 0;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--as-ink);
        font: inherit;
        font-size: 13px;
    }

    .as-search input::placeholder {
        color: #98a4b8;
    }

    .as-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .as-chip {
        min-height: 36px;
        padding: 0 13px;
        border-radius: 10px;
        border: 1px solid var(--as-border);
        background: #fff;
        color: #65738e;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: .2s;
    }

    .as-chip:hover {
        border-color: #a9c3ff;
        color: var(--as-primary);
    }

    .as-chip.active {
        background: #eaf0ff;
        color: var(--as-primary);
        border-color: #cbd9ff;
    }

    .as-list {
        padding: 0 24px 24px;
        display: grid;
        gap: 13px;
    }

    .as-item {
        padding: 19px;
        border: 1px solid #e7edf8;
        border-radius: 16px;
        transition: border-color .2s, box-shadow .2s;
        display: block;
        text-decoration: none;
        color: inherit;
        background: #fff;
    }

    .as-item:hover {
        border-color: #c9d8ff;
        box-shadow: 0 6px 20px #244a8a09;
    }

    .as-item-main {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .as-subject-icon {
        width: 53px;
        height: 53px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 15px;
    }

    .as-subject-icon .material-symbols-outlined {
        font-size: 26px;
    }

    .as-item-info {
        min-width: 0;
        flex: 1;
    }

    .as-item-name {
        margin: 0;
        color: var(--as-ink);
        font-size: 15px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .as-item-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 7px;
        margin-top: 8px;
        color: var(--as-muted);
        font-size: 12px;
    }

    .as-meta-dot {
        width: 3px;
        height: 3px;
        border-radius: 50%;
        background: #aab5c8;
    }

    .as-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .as-badge .material-symbols-outlined {
        font-size: 14px;
    }

    .as-badge-draft {
        color: #a66a08;
        background: #fff5df;
    }

    .as-badge-published {
        color: #11835a;
        background: #e5f8ef;
    }

    .as-badge-other {
        color: #64748b;
        background: #f0f3f8;
    }

    .as-item-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 17px;
        padding-top: 15px;
        border-top: 1px solid #eef2f9;
    }

    .as-visibility {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: var(--as-muted);
    }

    .as-visibility .material-symbols-outlined {
        font-size: 16px;
    }

    .as-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .as-btn-outline {
        min-height: 35px;
        padding: 0 12px;
        border: 1px solid #dce5f4;
        background: #fff;
        color: #53627d;
        border-radius: 9px;
        font-size: 11px;
    }

    .as-btn-outline:hover {
        border-color: #a9c3ff;
        color: var(--as-primary);
    }

    .as-empty {
        padding: 58px 20px;
        text-align: center;
    }

    .as-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70px;
        height: 70px;
        margin: 0 auto 18px;
        border-radius: 22px;
        background: #edf2ff;
        color: var(--as-primary);
    }

    .as-empty-icon .material-symbols-outlined {
        font-size: 34px;
    }

    .as-empty h3 {
        margin: 0 0 8px;
        font-size: 18px;
        font-weight: 800;
    }

    .as-empty p {
        margin: 0 auto 20px;
        max-width: 380px;
        color: var(--as-muted);
        font-size: 13px;
        line-height: 1.7;
    }

    .as-alert {
        padding: 13px 16px;
        margin-bottom: 18px;
        border-radius: 12px;
        font-size: 13px;
    }

    .as-alert-success {
        background: #e7f8ef;
        color: #16734d;
        border: 1px solid #c5ecd8;
    }

    .as-alert-error {
        background: #fff0f0;
        color: #b42318;
        border: 1px solid #fbd0d0;
    }

    .as-item[hidden] {
        display: none !important;
    }

    @media (max-width: 700px) {
        .assessment-page {
            padding: 22px 15px 100px;
        }

        .as-header {
            align-items: flex-start;
        }

        .as-header .as-btn {
            padding: 0 12px;
            font-size: 12px;
        }

        .as-summary {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .as-stat {
            padding: 15px 17px;
        }

        .as-stat-icon {
            width: 42px;
            height: 42px;
        }

        .as-stat-number {
            font-size: 22px;
        }

        .as-panel-heading {
            padding: 19px 16px 15px;
        }

        .as-toolbar {
            padding: 0 16px 17px;
        }

        .as-filters {
            width: 100%;
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 3px;
        }

        .as-chip {
            flex-shrink: 0;
        }

        .as-list {
            padding: 0 12px 15px;
        }

        .as-item {
            padding: 15px;
        }

        .as-item-main {
            align-items: flex-start;
            gap: 10px;
        }

        .as-subject-icon {
            width: 43px;
            height: 43px;
            border-radius: 12px;
        }

        .as-subject-icon .material-symbols-outlined {
            font-size: 22px;
        }

        .as-item-name {
            font-size: 13px;
        }

        .as-item-main > .as-badge {
            padding: 6px 7px;
            font-size: 10px;
        }

        .as-item-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .as-actions {
            width: 100%;
        }
    }

    @media (max-width: 400px) {
        .as-header {
            flex-direction: column;
        }

        .as-header .as-btn {
            width: 100%;
        }

        .as-item-main {
            flex-wrap: wrap;
        }

        .as-item-main > .as-badge {
            margin-left: 53px;
        }
    }
</style>

<div class="assessment-page">
    <div class="as-container">

        <header class="as-header">
            <div>
                <h1 class="as-title">Asesmen Saya</h1>
                <p class="as-subtitle">
                    Kelola asesmen dan soal yang telah dibuat dalam satu tempat.
                </p>
            </div>

            <a href="{{ route('guru.assessments.create') }}"
               class="as-btn as-btn-primary">
                <span class="material-symbols-outlined">add</span>
                Buat Asesmen
            </a>
        </header>

        @if (session('success'))
            <div class="as-alert as-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="as-alert as-alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="as-summary">
            <div class="as-stat">
                <div class="as-stat-icon"
                     style="background:#eaf0ff;color:#2864f0;">
                    <span class="material-symbols-outlined">library_books</span>
                </div>
                <div>
                    <div class="as-stat-number">{{ $total }}</div>
                    <div class="as-stat-label">Total Asesmen</div>
                </div>
            </div>

            <div class="as-stat">
                <div class="as-stat-icon"
                     style="background:#fff4df;color:#d99016;">
                    <span class="material-symbols-outlined">edit_note</span>
                </div>
                <div>
                    <div class="as-stat-number">{{ $draft }}</div>
                    <div class="as-stat-label">Masih Draft</div>
                </div>
            </div>

            <div class="as-stat">
                <div class="as-stat-icon"
                     style="background:#e4f8ef;color:#15966a;">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
                <div>
                    <div class="as-stat-number">{{ $published }}</div>
                    <div class="as-stat-label">Telah Dipublikasikan</div>
                </div>
            </div>
        </section>

        <section class="as-panel">
            <div class="as-panel-heading">
                <div>
                    <h2 class="as-panel-title">Daftar Asesmen</h2>
                    <div class="as-panel-description">
                        Semua asesmen yang dibuat.
                    </div>
                </div>
            </div>

            <form method="GET"
                  action="{{ route('guru.assessments.index') }}"
                  id="assessmentFilterForm">

                <div class="as-toolbar">
                    <label class="as-search">
                        <span class="material-symbols-outlined">search</span>
                        <input
                            type="search"
                            name="search"
                            id="searchAssessment"
                            value="{{ request('search') }}"
                            placeholder="Cari nama asesmen atau mapel..."
                        >
                    </label>

                    <div class="as-filters">
                        @foreach (['Semua', 'Draft', 'Published'] as $status)
                            <button
                                type="button"
                                class="as-chip {{ request('status', 'Semua') === $status ? 'active' : '' }}"
                                data-status="{{ $status }}"
                            >
                                {{ $status === 'Semua' ? 'Semua Status' : ($status === 'Published' ? 'Dipublikasikan' : $status) }}
                            </button>
                        @endforeach
                    </div>

                    <input type="hidden" name="status"
                           id="assessmentStatus"
                           value="{{ request('status', 'Semua') }}">
                    <input type="hidden" name="mapel"
                           id="assessmentMapel"
                           value="{{ request('mapel', 'Semua') }}">
                </div>
            </form>

            <div class="as-list" id="assessmentList">
                @forelse ($assessments as $a)
                    @php
                        $status = $a->status ?? 'Draft';
                        $mapel = $a->mapel ?? 'Umum';
                        $icon = $a->icon ?: 'description';
                        $color = $a->color ?: '#2864F0';
                        $isPublished = $status === 'Published';
                        $isPublic = $a->visibility === 'public';
                    @endphp

                    <!-- Kartu dibungkus tag <a> agar bisa diklik menuju halaman detail -->
                    <a href="{{ route('guru.assessments.show', $a->id) }}"
                        class="as-item"
                        data-assessment-item
                        data-name="{{ strtolower($a->nama) }}"
                        data-mapel="{{ strtolower($mapel) }}"
                        data-status="{{ $status }}"
                    >
                        <div class="as-item-main">
                            <div class="as-subject-icon"
                                 style="background:{{ $color }}18;color:{{ $color }};">
                                <span class="material-symbols-outlined">
                                    {{ $icon }}
                                </span>
                            </div>

                            <div class="as-item-info">
                                <h3 class="as-item-name">{{ $a->nama }}</h3>
                                <div class="as-item-meta">
                                    <span>{{ $mapel }}</span>
                                    <span class="as-meta-dot"></span>
                                    <span>
                                        Dibuat {{ $a->created_at?->format('d M Y') ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <span class="as-badge {{ $isPublished ? 'as-badge-published' : ($status === 'Draft' ? 'as-badge-draft' : 'as-badge-other') }}">
                                <span class="material-symbols-outlined">
                                    {{ $isPublished ? 'check_circle' : ($status === 'Draft' ? 'edit_note' : 'info') }}
                                </span>
                                {{ $status }}
                            </span>
                        </div>

                        <div class="as-item-footer" onclick="event.preventDefault();">
                            <div class="as-visibility">
                                <span class="material-symbols-outlined">
                                    {{ $isPublic ? 'public' : 'lock' }}
                                </span>
                                {{ $isPublic ? 'Dibagikan ke Bank Soal Publik' : 'Hanya terlihat olehmu' }}
                            </div>

                            <div class="as-actions">
                                @if ($isPublic)
                                    <span class="as-badge as-badge-published">
                                        <span class="material-symbols-outlined">public</span>
                                        Sudah dibagikan
                                    </span>
                                @else
                                    <form
                                        action="{{ route('guru.assessments.share', $a->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Bagikan asesmen ini ke Bank Soal Publik?')"
                                    >
                                        @csrf
                                        <button type="submit" class="as-btn as-btn-outline">
                                            <span class="material-symbols-outlined"
                                                  style="font-size:15px;vertical-align:middle;">
                                                share
                                            </span>
                                            Bagikan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="as-empty">
                        <div class="as-empty-icon">
                            <span class="material-symbols-outlined">assignment</span>
                        </div>
                        <h3>Belum ada asesmen</h3>
                        <p>
                            Asesmen yang kamu buat akan tersimpan di sini.
                            Mulai buat asesmen untuk mengelola soal pembelajaranmu.
                        </p>
                        <a href="{{ route('guru.assessments.create') }}"
                           class="as-btn as-btn-primary">
                            <span class="material-symbols-outlined">add</span>
                            Buat Asesmen Pertama
                        </a>
                    </div>
                @endforelse

                <div class="as-empty" id="assessmentNoResults" hidden>
                    <div class="as-empty-icon">
                        <span class="material-symbols-outlined">search_off</span>
                    </div>
                    <h3>Asesmen tidak ditemukan</h3>
                    <p>Coba gunakan kata kunci atau filter yang berbeda.</p>
                    <button type="button" id="resetAssessmentFilters"
                            class="as-btn as-btn-primary">
                        Reset Filter
                    </button>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('assessmentFilterForm');
    const search = document.getElementById('searchAssessment');
    const statusInput = document.getElementById('assessmentStatus');
    const mapelInput = document.getElementById('assessmentMapel');
    const items = [...document.querySelectorAll('[data-assessment-item]')];
    const noResults = document.getElementById('assessmentNoResults');
    const resetButton = document.getElementById('resetAssessmentFilters');
    const statusButtons = document.querySelectorAll('[data-status]');

    if (!form || !search) return;

    function applyFilters() {
        const keyword = search.value.trim().toLowerCase();
        const status = statusInput.value;
        const mapel = mapelInput.value.toLowerCase();
        let visible = 0;

        items.forEach(function (item) {
            const matchesKeyword =
                item.dataset.name.includes(keyword) ||
                item.dataset.mapel.includes(keyword);

            const matchesStatus =
                status === 'Semua' || item.dataset.status === status;

            const matchesMapel =
                mapel === 'semua' || item.dataset.mapel === mapel;

            const show = matchesKeyword && matchesStatus && matchesMapel;

            item.hidden = !show;
            if (show) visible++;
        });

        if (noResults) {
            noResults.hidden = items.length === 0 || visible > 0;
        }
    }

    function updateStatusButtons() {
        statusButtons.forEach(function (button) {
            button.classList.toggle(
                'active',
                button.dataset.status === statusInput.value
            );
        });
    }

    search.addEventListener('input', applyFilters);

    statusButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            statusInput.value = button.dataset.status;
            updateStatusButtons();
            applyFilters();
        });
    });

    if (resetButton) {
        resetButton.addEventListener('click', function () {
            search.value = '';
            statusInput.value = 'Semua';
            mapelInput.value = 'Semua';
            updateStatusButtons();
            applyFilters();
        });
    }

    updateStatusButtons();
    applyFilters();
});
</script>
@endsection


@section('bottom_nav')
    <nav class="bottomnav">
        <a href="{{ route('guru.dashboard') }}"
           class="navitem"
           style="text-decoration: none;">
            <span class="material-symbols-outlined">dashboard</span>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('guru.kelas') }}"
           class="navitem"
           style="text-decoration: none;">
            <span class="material-symbols-outlined">groups</span>
            <span>Kelas</span>
        </a>

        <a href="{{ route('guru.assessments.index') }}"
           class="navitem active"
           style="text-decoration: none;">
            <span class="material-symbols-outlined">library_books</span>
            <span>Asesmen</span>
        </a>

        <a href="{{ route('guru.bank-soal') }}"
           class="navitem"
           style="text-decoration: none;">
            <span class="material-symbols-outlined">folder_open</span>
            <span>Bank Soal</span>
        </a>

        <a href="{{ route('guru.profil') }}"
           class="navitem"
           style="text-decoration: none;">
            <span class="material-symbols-outlined">person</span>
            <span>Profil</span>
        </a>
    </nav>
@endsection
