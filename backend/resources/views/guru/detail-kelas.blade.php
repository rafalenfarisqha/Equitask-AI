@extends('layouts.app')

@section('title', 'Detail Kelas ' . $classroom->name)

@push('styles')
<style>
    .tabbar {
        display: flex;
        gap: 8px;
        padding: 0 20px 10px;
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(12px);
        position: sticky;
        top: 65px;
        z-index: 39;
        border-bottom: 1px solid var(--border);
        margin-bottom: 16px;
    }

    .tabbtn {
        flex: 1;
        padding: 11px 14px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        background: var(--card);
        color: var(--muted);
        border: 1px solid var(--border);
        cursor: pointer;
    }

    .tabbtn.active {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    .chip-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .avatar {
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--secondary-soft);
        color: var(--secondary);
        font-weight: 800;
        font-size: 16px;
    }

    .student-card {
        padding: 16px;
        margin-bottom: 12px;
    }

    .student-row {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .student-details {
        flex: 1;
        min-width: 130px;
    }

    .student-name {
        font-weight: 800;
        font-size: 14px;
    }

    .student-profile {
        border-top: 1px solid var(--border);
        margin-top: 14px;
        padding-top: 14px;
    }

    .profile-status {
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        padding: 6px 11px;
        border-radius: 999px;
    }

    .profile-status.reguler {
        background: #eaf1ff;
        color: #1d4ed8;
    }

    .profile-status.adaptif {
        background: #f2eaff;
        color: #6d28d9;
    }

    .adaptive-label {
        display: flex;
        gap: 10px;
        align-items: center;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
    }

    .adaptive-checkbox {
        width: 19px;
        height: 19px;
        accent-color: #2563eb;
        cursor: pointer;
        flex-shrink: 0;
    }

    .muted-help {
        color: var(--muted);
        font-size: 12px;
        line-height: 1.7;
        margin: 8px 0 14px;
    }

    .assessment-box {
        padding: 18px;
        margin-bottom: 16px;
    }

    .assessment-box h3 {
        font-size: 16px;
        font-weight: 800;
        margin: 0 0 8px;
    }

    .assessment-select {
        display: block;
        width: 100%;
        box-sizing: border-box;
        padding: 12px 13px;
        border: 1px solid var(--border);
        border-radius: 11px;
        font-size: 13px;
        background: #fff;
        color: #0f172a;
        margin: 10px 0 12px;
    }

    .assessment-item {
        padding: 17px;
        margin-bottom: 12px;
    }

    .assessment-item-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .assessment-name {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
    }

    .assessment-subject {
        color: var(--muted);
        font-size: 12px;
        margin-top: 5px;
    }

    .assignment-badge {
        display: inline-flex;
        padding: 7px 11px;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        font-size: 11px;
        font-weight: 800;
    }

    .empty-state {
        padding: 35px 18px;
        text-align: center;
        color: var(--muted);
    }

    .empty-state .material-symbols-outlined {
        font-size: 36px;
        color: #94a3b8;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 999;
        padding: 14px;
        box-sizing: border-box;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,.5);
    }

    .modal-box {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        width: 100%;
        max-width: 420px;
        max-height: 90vh;
        overflow-y: auto;
        box-sizing: border-box;
    }

    .modal-input {
        width: 100%;
        box-sizing: border-box;
        padding: 11px 12px;
        border-radius: 9px;
        border: 1px solid var(--border);
        font-size: 13px;
    }

    .notice-success,
    .notice-error {
        margin: 0 20px 14px;
        padding: 13px 15px;
        border-radius: 11px;
        font-size: 13px;
        font-weight: 600;
    }

    .notice-success {
        background: #d1fae5;
        color: #065f46;
    }

    .notice-error {
        background: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 600px) {
        .assessment-box,
        .assessment-item,
        .student-card {
            padding: 14px;
        }
    }
</style>
@endpush

@section('content')

<!-- TOPBAR -->
<div class="topbar">
    <a href="{{ route('guru.kelas') }}"
       class="iconbtn"
       aria-label="Kembali ke daftar kelas"
       style="text-decoration:none;">
        <span class="material-symbols-outlined">
            arrow_back_ios_new
        </span>
    </a>

    <h1>{{ $classroom->name }}</h1>
</div>

<!-- NOTIFIKASI -->
@if(session('success'))
    <div class="notice-success" role="status">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="notice-error" role="alert">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="notice-error" role="alert">
        <strong>Periksa kembali data yang dikirim.</strong>

        <ul style="margin:8px 0 0; padding-left:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- NAVIGASI TAB -->
<div class="tabbar">
    <button
        type="button"
        class="tabbtn active"
        id="btn-tab-siswa"
        onclick="switchTab('siswa')"
    >
        Siswa
    </button>

    <button
        type="button"
        class="tabbtn"
        id="btn-tab-assessment"
        onclick="switchTab('assessment')"
    >
        Assessment
    </button>
</div>

<div class="px" id="tab-content">

    <!-- ======================================= -->
    <!-- TAB SISWA -->
    <!-- ======================================= -->
    <div id="content-siswa">

        <div class="chip-row">

            <button
                type="button"
                class="btn btn-outline btn-sm"
                onclick="openModal('modalTambahSiswa')"
            >
                <span class="material-symbols-outlined"
                      style="font-size:16px;">
                    person_add
                </span>
                Tambah Siswa
            </button>

            <button
                type="button"
                class="btn btn-outline btn-sm"
                onclick="openModal('modalImportExcel')"
            >
                <span class="material-symbols-outlined"
                      style="font-size:16px;">
                    upload_file
                </span>
                Import CSV
            </button>

        </div>

        <!-- INFORMASI PROFIL -->
        <div class="card"
             style="padding:15px; margin-bottom:15px;
                    background:#f8faff;">

            <div style="font-weight:800;
                        font-size:13px;
                        margin-bottom:6px;">
                Pengaturan Soal Adaptif
            </div>

            <p class="muted-help"
               style="margin:0;">
                Semua siswa mendapat soal
                <strong>Reguler</strong> secara default.
                Centang hanya siswa yang perlu mendapat
                soal <strong>Adaptif</strong>.
            </p>

        </div>

        <!-- DAFTAR SISWA -->
        @forelse($classroom->students as $student)

            @php
                $track = strtolower(trim(
                    (string) (
                        $student->assessment_track
                        ?? $student->pivot?->assessment_track
                        ?? 'reguler'
                    )
                ));

                $isAdaptive = $track === 'adaptif';
            @endphp

            <div class="card student-card">

                <div class="student-row">

                    <div class="avatar">
                        {{ strtoupper(substr($student->name, 0, 2)) }}
                    </div>

                    <div class="student-details">

                        <div class="student-name">
                            {{ $student->name }}
                        </div>

                        <div class="muted"
                             style="font-size:12px;">
                            NISN:
                            {{ $student->nisn ?? 'Tidak ada NISN' }}
                        </div>

                    </div>

                    <span class="badge badge-blue">
                        Siswa Aktif
                    </span>

                </div>

                <!-- PENANDAAN ADAPTIF -->
                <div class="student-profile">

                    <div style="font-size:12px;
                                font-weight:800;
                                margin-bottom:10px;">
                        Profil Asesmen
                    </div>

                    <div style="margin-bottom:12px;">

                        @if($isAdaptive)
                            <span class="profile-status adaptif">
                                Adaptif
                            </span>
                        @else
                            <span class="profile-status reguler">
                                Reguler (Default)
                            </span>
                        @endif

                    </div>

                    <form
                        method="POST"
                        action="{{ route(
                            'guru.kelas.anggota.assessment-track',
                            [
                                'classId' => $classroom->id,
                                'studentId' => $student->id
                            ]
                        ) }}"
                    >
                        @csrf
                        @method('PATCH')

                        <!-- Satu nilai yang dikirim -->
                        <input
                            type="hidden"
                            name="assessment_track"
                            id="track-value-{{ $student->id }}"
                            value="{{ $isAdaptive ? 'adaptif' : 'reguler' }}"
                        >

                        <label
                            class="adaptive-label"
                            for="adaptive-{{ $student->id }}"
                        >
                            <input
                                type="checkbox"
                                id="adaptive-{{ $student->id }}"
                                class="adaptive-checkbox"
                                @checked($isAdaptive)
                                onchange="document.getElementById(
                                    'track-value-{{ $student->id }}'
                                ).value = this.checked
                                    ? 'adaptif'
                                    : 'reguler'"
                            >

                            <span>
                                Tandai sebagai siswa Adaptif
                            </span>
                        </label>

                        <p class="muted-help">
                            Tidak dicentang = Reguler.
                            Dicentang = Adaptif.
                        </p>

                        <button
                            type="submit"
                            class="btn btn-primary btn-sm"
                        >
                            <span class="material-symbols-outlined"
                                  style="font-size:16px;
                                         vertical-align:middle;">
                                save
                            </span>
                            Simpan Profil
                        </button>

                    </form>

                </div>
            </div>

        @empty

            <div class="card empty-state">
                <span class="material-symbols-outlined">
                    group_off
                </span>

                <div style="font-weight:700; margin-top:8px;">
                    Belum ada siswa di kelas ini.
                </div>

                <p class="muted-help">
                    Klik Tambah Siswa untuk mendaftarkan
                    anggota baru.
                </p>
            </div>

        @endforelse

    </div>

    <!-- ======================================= -->
    <!-- TAB ASSESSMENT -->
    <!-- ======================================= -->
    <div id="content-assessment"
         style="display:none;">


        <!-- DAFTAR ASESMEN KELAS -->
        <div style="font-size:15px;
                    font-weight:800;
                    margin:22px 0 13px;">
            Assessment Kelas Ini
            ({{ $classAssessments->count() }})
        </div>

        @forelse($classAssessments as $assessment)

            <div class="card assessment-item">

                <div class="assessment-item-header">

                    <div>
                        <div class="assessment-name">
                            {{ $assessment->nama }}
                        </div>

                        <div class="assessment-subject">
                            {{ $assessment->mapel ?? 'Umum' }}
                        </div>
                    </div>

                    <span class="assignment-badge">
                        Ditugaskan
                    </span>

                </div>

                <div style="margin-top:15px;">

                    <a
                        href="{{ route(
                            'guru.assessments.show',
                            $assessment->id
                        ) }}"
                        class="btn btn-outline btn-sm"
                        style="text-decoration:none;"
                    >
                        <span
                            class="material-symbols-outlined"
                            style="font-size:16px;
                                   vertical-align:middle;"
                        >
                            visibility
                        </span>
                        Lihat Detail
                    </a>

                </div>

            </div>

        @empty

            <div class="card empty-state">

                <span class="material-symbols-outlined">
                    assignment
                </span>

                <div style="font-weight:700;
                            margin-top:8px;">
                    Belum ada assessment untuk kelas ini.
                </div>

                <p class="muted-help">
                    Pilih assessment Published
                    pada formulir di atas,
                    kemudian klik Tugaskan ke Kelas.
                </p>

            </div>

        @endforelse

    </div>

    <div style="height:24px;"></div>

</div>

<!-- ======================================= -->
<!-- MODAL TAMBAH SISWA -->
<!-- ======================================= -->
<div id="modalTambahSiswa"
     class="modal-overlay">

    <div class="modal-box">

        <div style="display:flex;
                    align-items:center;
                    justify-content:space-between;
                    margin-bottom:12px;">

            <h3 style="font-size:16px;
                       font-weight:800;
                       margin:0;">
                Tambah Siswa ke Kelas
            </h3>

            <button
                type="button"
                onclick="closeModal('modalTambahSiswa')"
                style="border:0; background:none;
                       cursor:pointer; font-size:18px;"
                aria-label="Tutup"
            >
                ✕
            </button>

        </div>

        <p class="muted-help">
            Tambahkan siswa melalui link
            atau menggunakan NISN.
        </p>

        <!-- LINK KELAS -->
        <div style="padding:13px;
                    background:#f8fafc;
                    border:1px solid var(--border);
                    border-radius:10px;
                    margin-bottom:18px;">

            <div style="font-size:12px;
                        font-weight:800;
                        margin-bottom:7px;">
                Link Bergabung Siswa
            </div>

            <p class="muted-help">
                Bagikan link berikut kepada siswa.
            </p>

            <div style="display:flex; gap:7px;">

                <input
                    type="text"
                    id="joinLink"
                    readonly
                    value="{{ route(
                        'siswa.kelas.join',
                        $classroom->id
                    ) }}"
                    style="flex:1; min-width:0;
                           padding:8px;
                           border:1px solid var(--border);
                           border-radius:7px;
                           font-size:11px;"
                >

                <button
                    type="button"
                    class="btn btn-outline btn-sm"
                    onclick="copylink()"
                >
                    Salin
                </button>

            </div>
        </div>

        <!-- FORM NISN -->
        <form
            method="POST"
            action="{{ route(
                'guru.kelas.addStudent',
                $classroom->id
            ) }}"
        >
            @csrf

            <label
                for="nisn-input"
                style="font-size:12px;
                       font-weight:700;"
            >
                Nomor Induk Siswa Nasional
            </label>

            <input
                id="nisn-input"
                type="text"
                name="nisn"
                class="modal-input"
                required
                placeholder="Masukkan NISN siswa"
                style="margin:8px 0 15px;"
            >

            <div style="display:flex;
                        gap:8px;
                        justify-content:flex-end;">

                <button
                    type="button"
                    class="btn btn-outline btn-sm"
                    onclick="closeModal('modalTambahSiswa')"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn btn-primary btn-sm"
                >
                    Tambah Siswa
                </button>

            </div>
        </form>

    </div>
</div>

<!-- ======================================= -->
<!-- MODAL IMPORT CSV -->
<!-- ======================================= -->
<div id="modalImportExcel"
     class="modal-overlay">

    <div class="modal-box">

        <div style="display:flex;
                    justify-content:space-between;
                    align-items:center;
                    margin-bottom:12px;">

            <h3 style="font-size:16px;
                       font-weight:800;
                       margin:0;">
                Import Siswa via CSV
            </h3>

            <button
                type="button"
                onclick="closeModal('modalImportExcel')"
                style="border:0; background:none;
                       cursor:pointer; font-size:18px;"
                aria-label="Tutup"
            >
                ✕
            </button>

        </div>

        <p class="muted-help">
            Upload file CSV atau TXT.
            Kolom pertama berisi NISN siswa.
            Semua siswa baru otomatis Reguler.
        </p>

        <form
            method="POST"
            action="{{ route(
                'guru.kelas.importExcel',
                $classroom->id
            ) }}"
            enctype="multipart/form-data"
        >
            @csrf

            <label
                for="import-file"
                style="font-size:12px;
                       font-weight:700;"
            >
                Pilih File CSV/TXT
            </label>

            <input
                type="file"
                id="import-file"
                name="file"
                class="modal-input"
                accept=".csv,.txt"
                required
                style="margin:8px 0 15px;"
            >

            <div style="display:flex;
                        gap:8px;
                        justify-content:flex-end;">

                <button
                    type="button"
                    class="btn btn-outline btn-sm"
                    onclick="closeModal('modalImportExcel')"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn btn-primary btn-sm"
                >
                    Upload & Import
                </button>

            </div>

        </form>

    </div>
</div>

@endsection

<!-- ======================================= -->
<!-- BOTTOM NAV -->
<!-- ======================================= -->
@section('bottom_nav')

<div class="bottomnav">

    <a
        href="{{ route('guru.dashboard') }}"
        class="navitem {{
            request()->routeIs('guru.dashboard')
                ? 'active'
                : ''
        }}"
        style="text-decoration:none;"
    >
        <span class="material-symbols-outlined">
            space_dashboard
        </span>
        Dashboard
    </a>

    <a
        href="{{ route('guru.kelas') }}"
        class="navitem {{
            request()->routeIs('guru.kelas*')
                ? 'active'
                : ''
        }}"
        style="text-decoration:none;"
    >
        <span class="material-symbols-outlined">
            groups
        </span>
        Kelas
    </a>

    <a
        href="{{ route('guru.assessments.index') }}"
        class="navitem {{
            request()->routeIs('guru.assessments*')
                ? 'active'
                : ''
        }}"
        style="text-decoration:none;"
    >
        <span class="material-symbols-outlined">
            library_books
        </span>
        Asesmen
    </a>

    <a
        href="{{ route('guru.bank-soal') }}"
        class="navitem {{
            request()->routeIs('guru.bank-soal')
                ? 'active'
                : ''
        }}"
        style="text-decoration:none;"
    >
        <span class="material-symbols-outlined">
            folder_open
        </span>
        Bank Soal
    </a>

    <a
        href="{{ route('guru.profil') }}"
        class="navitem {{
            request()->routeIs('guru.profil')
                ? 'active'
                : ''
        }}"
        style="text-decoration:none;"
    >
        <span class="material-symbols-outlined">
            person
        </span>
        Profil
    </a>

</div>
@endsection

<!-- ======================================= -->
<!-- JAVASCRIPT -->
<!-- ======================================= -->
@push('scripts')
<script>
    function switchTab(tabName) {
        const tabs = ['siswa', 'assessment'];

        tabs.forEach(function(name) {
            const button = document.getElementById(
                'btn-tab-' + name
            );

            const content = document.getElementById(
                'content-' + name
            );

            if (button) {
                button.classList.toggle(
                    'active',
                    name === tabName
                );
            }

            if (content) {
                content.style.display =
                    name === tabName
                        ? 'block'
                        : 'none';
            }
        });

        // Simpan tab aktif agar tidak kembali
        // ke Siswa setiap halaman dimuat ulang.
        try {
            sessionStorage.setItem(
                'equitask-class-tab-{{ $classroom->id }}',
                tabName
            );
        } catch (error) {
            // Tetap berfungsi jika storage tidak tersedia.
        }
    }

    function openModal(modalId) {
        const modal = document.getElementById(modalId);

        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);

        if (modal) {
            modal.style.display = 'none';
        }
    }

    function copylink() {
        const input = document.getElementById('joinLink');

        if (!input) {
            return;
        }

        if (
            navigator.clipboard
            && navigator.clipboard.writeText
        ) {
            navigator.clipboard.writeText(input.value)
                .then(function() {
                    alert('Link berhasil disalin!');
                })
                .catch(function() {
                    fallbackCopy(input);
                });
        } else {
            fallbackCopy(input);
        }
    }

    function fallbackCopy(input) {
        input.select();
        input.setSelectionRange(0, 99999);

        document.execCommand('copy');

        alert('Link berhasil disalin!');
    }

    document.addEventListener('DOMContentLoaded', function() {
        let savedTab = 'siswa';

        try {
            savedTab = sessionStorage.getItem(
                'equitask-class-tab-{{ $classroom->id }}'
            ) || 'siswa';
        } catch (error) {
            // Gunakan tab Siswa.
        }

        if (
            savedTab !== 'siswa'
            && savedTab !== 'assessment'
        ) {
            savedTab = 'siswa';
        }

        switchTab(savedTab);
    });
</script>
@endpush
