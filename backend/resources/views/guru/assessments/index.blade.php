
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
        letter-spacing: -.8px;
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
        gap: 7px;
        min-height: 40px;
        padding: 0 14px;
        border: 1px solid transparent;
        border-radius: 11px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        font-family: inherit;
        transition: .2s ease;
    }

    .as-btn-primary {
        background: var(--as-primary);
        color: white;
    }

    .as-btn-primary:hover {
        background: #174ed1;
        color: white;
    }

    .as-btn-outline {
        border-color: #dce5f4;
        background: white;
        color: #53627d;
    }

    .as-btn-outline:hover {
        border-color: #a9c3ff;
        color: var(--as-primary);
    }

    .as-btn-copy {
        border-color: #c7d7ff;
        background: #eff4ff;
        color: #2455ce;
    }

    .as-btn-copy:hover {
        background: #e1ebff;
        color: #1745b8;
    }

    .as-btn .material-symbols-outlined {
        font-size: 17px;
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
        background: white;
        border: 1px solid var(--as-border);
        border-radius: 18px;
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
        font-weight: 800;
    }

    .as-stat-label {
        margin-top: 5px;
        color: var(--as-muted);
        font-size: 12px;
    }

    .as-panel {
        background: white;
        border: 1px solid var(--as-border);
        border-radius: 20px;
        overflow: hidden;
    }

    .as-panel-heading {
        padding: 22px 24px 17px;
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

    .as-search input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--as-ink);
        font: inherit;
        font-size: 13px;
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
        background: white;
        color: #65738e;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
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
        background: white;
        transition: .2s ease;
    }

    .as-item:hover {
        border-color: #c9d8ff;
        box-shadow: 0 6px 20px #244a8a09;
    }

    .as-item[hidden] {
        display: none !important;
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
        gap: 8px;
        margin-top: 8px;
        color: var(--as-muted);
        font-size: 12px;
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
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: var(--as-muted);
    }

    .as-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
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

    .as-empty {
        padding: 40px 20px;
        text-align: center;
        color: var(--as-muted);
    }

    /* =====================================
       MODAL DUPLIKAT DAN EDIT JUDUL
       ===================================== */

    .as-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, .55);
    }

    .as-modal[hidden] {
        display: none !important;
    }

    .as-modal-card {
        width: 100%;
        max-width: 480px;
        padding: 25px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .18);
    }

    .as-modal-title {
        margin: 0 0 8px;
        font-size: 20px;
        font-weight: 800;
    }

    .as-modal-description {
        margin: 0 0 20px;
        font-size: 13px;
        line-height: 1.7;
        color: var(--as-muted);
    }

    .as-field-label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 700;
    }

    .as-field-input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #dce5f4;
        border-radius: 11px;
        font-family: inherit;
        font-size: 14px;
    }

    .as-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        margin-top: 22px;
    }

    @media (max-width: 700px) {
        .assessment-page {
            padding: 22px 15px 100px;
        }

        .as-summary {
            grid-template-columns: 1fr;
        }

        .as-header {
            align-items: flex-start;
        }

        .as-toolbar {
            padding: 0 16px 17px;
        }

        .as-list {
            padding: 0 12px 15px;
        }

        .as-item {
            padding: 15px;
        }

        .as-item-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (max-width: 400px) {
        .as-header {
            flex-direction: column;
        }

        .as-actions {
            width: 100%;
        }
    }
</style>

<div class="assessment-page">
    <div class="as-container">

        {{-- HEADER --}}

        <header class="as-header">
            <div>
                <h1 class="as-title">Asesmen Saya</h1>
                <p class="as-subtitle">
                    Kelola asesmen, duplikasi paket, dan publikasi ke siswa.
                </p>
            </div>

            <a
                href="{{ route('guru.assessments.create') }}"
                class="as-btn as-btn-primary">

                <span class="material-symbols-outlined">add</span>
                Buat Asesmen
            </a>
        </header>

        {{-- NOTIFIKASI --}}

        @if(session('success'))
            <div class="as-alert as-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="as-alert as-alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="as-alert as-alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- STATISTIK --}}

        <section class="as-summary">

            <div class="as-stat">
                <div class="as-stat-icon"
                     style="background:#eaf0ff;color:#2864f0">
                    <span class="material-symbols-outlined">
                        library_books
                    </span>
                </div>

                <div>
                    <div class="as-stat-number">{{ $total }}</div>
                    <div class="as-stat-label">Total Asesmen</div>
                </div>
            </div>

            <div class="as-stat">
                <div class="as-stat-icon"
                     style="background:#fff4df;color:#d99016">
                    <span class="material-symbols-outlined">
                        edit_note
                    </span>
                </div>

                <div>
                    <div class="as-stat-number">{{ $draft }}</div>
                    <div class="as-stat-label">Masih Draft</div>
                </div>
            </div>

            <div class="as-stat">
                <div class="as-stat-icon"
                     style="background:#e4f8ef;color:#15966a">
                    <span class="material-symbols-outlined">
                        check_circle
                    </span>
                </div>

                <div>
                    <div class="as-stat-number">{{ $published }}</div>
                    <div class="as-stat-label">Telah Dipublikasikan</div>
                </div>
            </div>

        </section>

        {{-- DAFTAR ASESMEN --}}

        <section class="as-panel">

            <div class="as-panel-heading">
                <h2 class="as-panel-title">Daftar Asesmen</h2>
                <div class="as-panel-description">
                    Pilih Detail, Duplikat, atau Edit Judul pada asesmen.
                </div>
            </div>

            {{-- PENCARIAN DAN FILTER --}}

            <form
                method="GET"
                action="{{ route('guru.assessments.index') }}"
                id="assessmentFilterForm">

                <div class="as-toolbar">

                    <label class="as-search">
                        <span class="material-symbols-outlined">
                            search
                        </span>

                        <input
                            type="search"
                            name="search"
                            id="searchAssessment"
                            value="{{ request('search') }}"
                            placeholder="Cari nama asesmen atau mapel...">
                    </label>

                    <div class="as-filters">

                        @foreach(['Semua', 'Draft', 'Published'] as $filterStatus)

                            <button
                                type="button"
                                class="as-chip {{ request('status', 'Semua') === $filterStatus ? 'active' : '' }}"
                                data-status="{{ $filterStatus }}">

                                {{ $filterStatus === 'Semua'
                                    ? 'Semua Status'
                                    : ($filterStatus === 'Published'
                                        ? 'Dipublikasikan'
                                        : 'Draft') }}
                            </button>

                        @endforeach

                    </div>

                    <input
                        type="hidden"
                        name="status"
                        id="assessmentStatus"
                        value="{{ request('status', 'Semua') }}">

                </div>
            </form>

            {{-- KARTU ASESMEN --}}

            <div class="as-list" id="assessmentList">

                @forelse($assessments as $a)

                    @php
                        $status = $a->status ?? 'Draft';
                        $mapel = $a->mapel ?? 'Umum';
                        $icon = $a->icon ?: 'description';
                        $color = $a->color ?: '#2864F0';
                        $isPublished = $status === 'Published';
                        $isPublic = $a->visibility === 'public';
                    @endphp

                    <div
                        class="as-item"
                        data-assessment-item
                        data-name="{{ strtolower($a->nama) }}"
                        data-mapel="{{ strtolower($mapel) }}"
                        data-status="{{ $status }}">

                        <div class="as-item-main">

                            <div
                                class="as-subject-icon"
                                style="background:#eaf0ff;color:{{ $color }}">

                                <span class="material-symbols-outlined">
                                    {{ $icon }}
                                </span>
                            </div>

                            <div class="as-item-info">

                                <h3 class="as-item-name">
                                    {{ $a->nama }}
                                </h3>

                                <div class="as-item-meta">
                                    <span>{{ $mapel }}</span>
                                    <span>•</span>
                                    <span>
                                        Dibuat
                                        {{ $a->created_at?->format('d M Y') ?? '-' }}
                                    </span>
                                </div>

                            </div>

                            <span class="as-badge {{ $isPublished ? 'as-badge-published' : ($status === 'Draft' ? 'as-badge-draft' : 'as-badge-other') }}">
                                {{ $status }}
                            </span>

                        </div>

                        {{-- FOOTER KARTU --}}

                        <div class="as-item-footer">

                            <div class="as-visibility">
                                <span class="material-symbols-outlined">
                                    {{ $isPublic ? 'public' : 'lock' }}
                                </span>

                                {{ $isPublic
                                    ? 'Dibagikan ke Bank Soal Publik'
                                    : 'Belum dibagikan ke Bank Soal Publik' }}
                            </div>

                            <div class="as-actions">

                                {{-- DETAIL ASESMEN --}}
                                <a
                                    href="{{ route('guru.assessments.show', $a->id) }}"
                                    class="as-btn as-btn-outline">

                                    <span class="material-symbols-outlined">
                                        visibility
                                    </span>
                                    Detail
                                </a>

                                {{-- DUPLIKAT --}}
                                <button
                                    type="button"
                                    class="as-btn as-btn-copy"
                                    data-duplicate
                                    data-id="{{ $a->id }}"
                                    data-title="{{ $a->nama }}">

                                    <span class="material-symbols-outlined">
                                        content_copy
                                    </span>

                                    Duplikat
                                </button>

                                {{-- EDIT JUDUL --}}
                                <button
                                    type="button"
                                    class="as-btn as-btn-outline"
                                    data-edit-title
                                    data-id="{{ $a->id }}"
                                    data-title="{{ $a->nama }}">

                                    <span class="material-symbols-outlined">
                                        edit
                                    </span>

                                    Edit Judul
                                </button>

                                {{-- BAGIKAN KE BANK SOAL --}}
                                @if(!$isPublic && $isPublished)

                                    <form
                                        action="{{ route('guru.assessments.share', $a->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Bagikan asesmen ini ke Bank Soal Publik?')">

                                        @csrf

                                        <button
                                            type="submit"
                                            class="as-btn as-btn-outline">

                                            <span class="material-symbols-outlined">
                                                share
                                            </span>

                                            Bagikan
                                        </button>
                                    </form>

                                @elseif($isPublic)

                                    <span class="as-badge as-badge-published">
                                        Sudah dibagikan
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="as-empty">
                        <h3>Belum ada asesmen</h3>
                        <p>
                            Mulai buat asesmen untuk mengelola
                            soal pembelajaranmu.
                        </p>

                        <a
                            href="{{ route('guru.assessments.create') }}"
                            class="as-btn as-btn-primary">
                            Buat Asesmen Pertama
                        </a>
                    </div>

                @endforelse

                <div
                    class="as-empty"
                    id="assessmentNoResults"
                    hidden>

                    <h3>Asesmen tidak ditemukan</h3>

                    <p>
                        Coba kata kunci atau filter yang berbeda.
                    </p>

                    <button
                        type="button"
                        id="resetAssessmentFilters"
                        class="as-btn as-btn-primary">

                        Reset Filter
                    </button>
                </div>

            </div>

        </section>

    </div>
</div>

{{-- ==========================================
     MODAL DUPLIKAT
     ========================================== --}}

<div
    class="as-modal"
    id="duplicateModal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="duplicateModalTitle"
    hidden>

    <div class="as-modal-card">

        <h2
            class="as-modal-title"
            id="duplicateModalTitle">
            Duplikat Paket Asesmen
        </h2>

        <p class="as-modal-description">
            Paket salinan akan menggunakan soal Reguler
            dan Adaptif dari paket asli, tanpa Generate
            ulang. Salinan disimpan sebagai Draft
            sampai kamu memublikasikannya.
        </p>

        <form method="POST" id="duplicateForm">

            @csrf

            <label
                for="duplicateName"
                class="as-field-label">
                Nama Paket Salinan
            </label>

            <input
                type="text"
                name="nama"
                id="duplicateName"
                class="as-field-input"
                maxlength="255"
                required>

            <div class="as-modal-actions">

                <button
                    type="button"
                    class="as-btn as-btn-outline"
                    data-close-modal>
                    Batal
                </button>

                <button
                    type="submit"
                    class="as-btn as-btn-primary">
                    Buat Salinan
                </button>

            </div>

        </form>

    </div>
</div>

{{-- ==========================================
     MODAL EDIT JUDUL
     ========================================== --}}

<div
    class="as-modal"
    id="editTitleModal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="editTitleModalTitle"
    hidden>

    <div class="as-modal-card">

        <h2
            class="as-modal-title"
            id="editTitleModalTitle">
            Edit Judul Asesmen
        </h2>

        <p class="as-modal-description">
            Perubahan judul tidak akan mengubah
            pertanyaan, pilihan jawaban, atau hasil.
        </p>

        <form method="POST" id="editTitleForm">

            @csrf
            @method('PATCH')

            <label
                for="editTitleName"
                class="as-field-label">
                Nama Asesmen
            </label>

            <input
                type="text"
                name="nama"
                id="editTitleName"
                class="as-field-input"
                maxlength="255"
                required>

            <div class="as-modal-actions">

                <button
                    type="button"
                    class="as-btn as-btn-outline"
                    data-close-modal>
                    Batal
                </button>

                <button
                    type="submit"
                    class="as-btn as-btn-primary">
                    Simpan Judul
                </button>

            </div>

        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const search =
        document.getElementById('searchAssessment');

    const statusInput =
        document.getElementById('assessmentStatus');

    const items = [
        ...document.querySelectorAll(
            '[data-assessment-item]'
        )
    ];

    const noResults =
        document.getElementById('assessmentNoResults');

    const statusButtons =
        document.querySelectorAll('[data-status]');

    function applyFilters() {

        const keyword = search.value.trim().toLowerCase();
        const status = statusInput.value;

        let visible = 0;

        items.forEach(item => {

            const matchesKeyword =
                item.dataset.name.includes(keyword) ||
                item.dataset.mapel.includes(keyword);

            const matchesStatus =
                status === 'Semua' ||
                item.dataset.status === status;

            const show =
                matchesKeyword && matchesStatus;

            item.hidden = !show;

            if (show) visible++;
        });

        if (noResults) {
            noResults.hidden =
                items.length === 0 || visible > 0;
        }
    }

    statusButtons.forEach(button => {

        button.addEventListener('click', function () {

            statusInput.value = this.dataset.status;

            statusButtons.forEach(btn => {
                btn.classList.toggle(
                    'active',
                    btn === this
                );
            });

            applyFilters();
        });
    });

    if (search) {
        search.addEventListener('input', applyFilters);
    }

    const resetButton =
        document.getElementById('resetAssessmentFilters');

    if (resetButton) {

        resetButton.addEventListener('click', () => {

            search.value = '';
            statusInput.value = 'Semua';

            statusButtons.forEach(button => {
                button.classList.toggle(
                    'active',
                    button.dataset.status === 'Semua'
                );
            });

            applyFilters();
        });
    }

    // =====================================
    // MODAL
    // =====================================

    const duplicateModal =
        document.getElementById('duplicateModal');

    const duplicateForm =
        document.getElementById('duplicateForm');

    const duplicateName =
        document.getElementById('duplicateName');

    const editTitleModal =
        document.getElementById('editTitleModal');

    const editTitleForm =
        document.getElementById('editTitleForm');

    const editTitleName =
        document.getElementById('editTitleName');

    const duplicateBaseUrl = @json(
        url('/guru/assessments')
    );

    function closeModals() {

        duplicateModal.hidden = true;
        editTitleModal.hidden = true;

        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-duplicate]')
        .forEach(button => {

            button.addEventListener('click', function () {

                const id = this.dataset.id;
                const title = this.dataset.title;

                duplicateName.value =
                    title + ' (Salinan)';

                duplicateForm.action =
                    `${duplicateBaseUrl}/${id}/duplicate`;

                duplicateModal.hidden = false;
                document.body.style.overflow = '';

                duplicateName.focus();
            });
        });

    document.querySelectorAll('[data-edit-title]')
        .forEach(button => {

            button.addEventListener('click', function () {

                const id = this.dataset.id;
                const title = this.dataset.title;

                editTitleName.value = title;

                editTitleForm.action =
                    `${duplicateBaseUrl}/${id}/title`;

                editTitleModal.hidden = false;
                document.body.style.overflow = '';

                editTitleName.focus();
            });
        });

    document.querySelectorAll('[data-close-modal]')
        .forEach(button => {

            button.addEventListener(
                'click',
                closeModals
            );
        });

    [duplicateModal, editTitleModal]
        .forEach(modal => {

            modal.addEventListener('click', function (event) {

                if (event.target === modal) {
                    closeModals();
                }
            });
        });

    document.addEventListener('keydown', event => {

        if (event.key === 'Escape') {
            closeModals();
        }
    });

    if (search) {
        applyFilters();
    }
});
</script>

@endsection

@section('bottom_nav')
    <nav class="bottomnav">

        <a
            href="{{ route('guru.dashboard') }}"
            class="navitem"
            style="text-decoration:none">

            <span class="material-symbols-outlined">
                dashboard
            </span>
            <span>Dashboard</span>
        </a>

        <a
            href="{{ route('guru.kelas') }}"
            class="navitem"
            style="text-decoration:none">

            <span class="material-symbols-outlined">
                groups
            </span>
            <span>Kelas</span>
        </a>

        <a
            href="{{ route('guru.assessments.index') }}"
            class="navitem active"
            style="text-decoration:none">

            <span class="material-symbols-outlined">
                library_books
            </span>
            <span>Asesmen</span>
        </a>

        <a
            href="{{ route('guru.bank-soal') }}"
            class="navitem"
            style="text-decoration:none">

            <span class="material-symbols-outlined">
                folder_open
            </span>
            <span>Bank Soal</span>
        </a>

        <a
            href="{{ route('guru.profil') }}"
            class="navitem"
            style="text-decoration:none">

            <span class="material-symbols-outlined">
                person
            </span>
            <span>Profil</span>
        </a>

    </nav>
@endsection
